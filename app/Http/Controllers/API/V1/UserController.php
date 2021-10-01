<?php

namespace App\Http\Controllers\API\V1;

use App\Exceptions\ApiExceptions\EmailAlreadyVerifiedException;
use App\Exceptions\ApiExceptions\EmailVerificationCodeExpiredOrDoesNotExistsException;
use App\Exceptions\ApiExceptions\InvalidMobileVerificationCodeException;
use App\Exceptions\ApiExceptions\UserCurrentPasswordInvalidException;
use App\Http\Controllers\Controller;
use App\Http\Requests\EmailVerificationRequest;
use App\Http\Requests\DeviceTokenRequest;
use App\Http\Requests\SubscribePushNotificationRequest;
use App\Http\Requests\UserAvatarRequest;
use App\Http\Requests\UserPasswordChangeRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Mail\ApplyEmailVerification;
use App\Models\User;
use App\Notifications\Admin\AdminNotification;
use App\Traits\CRUDActions;
use App\Traits\SoftDeleteActions;
use App\Transformers\MeetingPartialTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

class UserController extends Controller
{
    use CRUDActions, SoftDeleteActions;


    protected function beforeSaveEntity(array $validated, User $user)
    {
        $user->password = Hash::make($validated['password']);
    }

    /**
     * @param User $user User.
     *
     * @return JsonResponse
     */
    public function getRoles(User $user)
    {
        $roles = $user->roles()->transformIt();
        return $this->successResponse($roles);
    }

    /**
     * @param User $user User.
     * @return JsonResponse
     */
    public function resetPassword(User $user)
    {
        /** @var User */
        $sender = Auth::user();

        $password = Str::random(8);
        $user->password = Hash::make($password);
        $user->save();

        $user->notify(new AdminNotification($sender, ['mail'], "Your Password has been reset by Admin. \nYour new password is: {$password}", "Reset Password"));

        return $this->successResponse([
            'new-password' => $password
        ]);
    }

    /**
     * @param UserPasswordChangeRequest $request
     * @return JsonResponse
     * @throws UserCurrentPasswordInvalidException
     */
    public function changePassword(UserPasswordChangeRequest $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $validated = $request->validated();

        if (!Hash::check($validated['current_password'], $user->password)) {
            throw new UserCurrentPasswordInvalidException();
        }

        $user->password = Hash::make($validated['new_password']);
        $user->save();

        return $this->successResponse();
    }

    /**
     * @return JsonResponse
     */
    public function profile()
    {
        /** @var User $user */
        $user = Auth::user();
        $data = $user->transformIt();
        return $this->successResponse($data);
    }

    /**
     * @param UserUpdateRequest $request
     * @return JsonResponse
     */
    public function updateProfile(UserUpdateRequest $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $validated = $request->validated();

        $user->update($validated);

        $data = $user->transformIt();
        return $this->successResponse($data);
    }

    /**
     * @param UserAvatarRequest $request
     * @return JsonResponse
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function updateAvatar(UserAvatarRequest $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $user->clearMediaCollection('avatar');
        $user->addMediaFromRequest('avatar')->toMediaCollection('avatar');

        return $this->successResponse();
    }

    /**
     * @param DeviceTokenRequest $request
     * @return mixed
     */
    public function subscribePushNotification(SubscribePushNotificationRequest $request)
    {
        $validated = $request->validated();

        /** @var User $user */
        $user = Auth::user();

        $endpoint = $validated['endpoint'];
        $token = $validated['keys']['auth'];
        $key = $validated['keys']['p256dh'];
        $user->updatePushSubscription($endpoint, $key, $token);

        return $this->successResponse();
    }

    /**
     * @param Request $request Request.
     * @return JsonResponse
     * @throws EmailAlreadyVerifiedException
     */
    public function requestEmailVerificationCode(Request $request): JsonResponse
    {
        $this->internalRequestEmailVerificationCode($request->user());

        return response()->json(['message' => 'Verification code has been sent to your email.']);
    }

    /**
     * @param User $user User.
     * @throws EmailAlreadyVerifiedException
     */
    private function internalRequestEmailVerificationCode(User $user): void
    {
        if ($user->getEmailVerifiedAt()) {
            throw new EmailAlreadyVerifiedException();
        }

        $verificationCode = rand(100000, 999999);
        $validTime = config('platform.apply.email_verification_code_valid_time', 5);

        Cache::put('email_verification_code_' . $user->getId(), $verificationCode, $validTime * 60);

        Mail::to($user->getEmail())->send(new ApplyEmailVerification($verificationCode));
    }


    /**
     * @param EmailVerificationRequest $request Request.
     *
     * @return JsonResponse
     * @throws EmailAlreadyVerifiedException
     * @throws EmailVerificationCodeExpiredOrDoesNotExistsException
     * @throws InvalidMobileVerificationCodeException
     */
    public function verifyEmail(EmailVerificationRequest $request): JsonResponse
    {
        $user = $request->user();
        if ($user->getEmailVerifiedAt()) {
            throw new EmailAlreadyVerifiedException();
        }
        $key = 'email_verification_code_' . $user->getId();
        if (!Cache::has($key)) {
            throw new EmailVerificationCodeExpiredOrDoesNotExistsException();
        }

        if (Cache::get($key) != $request->get('code')) {
            throw new InvalidMobileVerificationCodeException();
        }

        $user->setEmailVerifiedAt(now());
        $user->save();

        return response()->json(['message' => 'Email of user has been verified successfully.']);
    }

    /**
     * List of meeting requests
     *
     * @return JsonResponse
     */
    public function getMeetings()
    {
        /** @var User $user */
        $user = Auth::user();
        $meetingRequests = $user->meetings()->transformIt(new MeetingPartialTransformer());

        return $this->successResponse($meetingRequests);
    }

    /**
     * List of notifications
     *
     * @return JsonResponse
     */
    public function getNotifications()
    {
        /** @var User $user */
        $user = Auth::user();
        $notifications = $user->notifications()->transformIt();

        return $this->successResponse($notifications);
    }
}
