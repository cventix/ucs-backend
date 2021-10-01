<?php


namespace App\Http\Controllers\API\V1;


use App\Exceptions\ApiExceptions\AuthFailureException;
use App\Exceptions\ApiExceptions\UserDeactivatedException;
use App\Exceptions\ApiExceptions\AuthInvalidVerificationCodeException;
use App\Http\Controllers\Controller;
use App\Http\Requests\AuthForgotPasswordRequest;
use App\Http\Requests\AuthLoginRequest;
use App\Http\Requests\AuthResetPasswordRequest;
use App\Http\Requests\RegisterRequest;
use App\Mail\ForgotPassword;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    /**
     * @param AuthLoginRequest $request
     * @return JsonResponse
     * @throws AuthFailureException
     * @throws UserDeactivatedException
     */
    public function login(AuthLoginRequest $request)
    {
        $validated = $request->validated();

        if (filter_var($validated['username'], FILTER_VALIDATE_EMAIL))
            $credentials['email'] = $validated['username'];
        else
            $credentials['username'] = $validated['username'];

        $credentials['password'] = $validated['password'];
        $authorized = Auth::attempt($credentials);

        if (!$authorized)
            throw new AuthFailureException();

        /** @var User $user */
        $user = Auth::user();

        if ($user->is_deactivated)
            throw new UserDeactivatedException();

        if (config('platform.prevent_multiple_login', false)) {
            $user->tokens()->delete();
        }

        $name = $validated['name'] ?? '';
        $name .= ';' . $this->getLoginLocation();

        $token = $user->createToken($name);
        $data['token'] = $token->plainTextToken;
        $data['user'] = $user->transformIt();
        return $this->successResponse($data);
    }

    /**
     * @param RegisterRequest $request Request.
     *
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        $name = ';' . $this->getLoginLocation();

        $token = $user->createToken($name);

        $data['token'] = $token->plainTextToken;
        $data['user'] = $user->transformIt();

        return $this->successResponse($data);
    }

    /**
     * @param AuthForgotPasswordRequest $request
     * @return JsonResponse
     */
    public function forgot(AuthForgotPasswordRequest $request)
    {
        $validated = $request->validated();

        $user = User::query()->where('email', $validated['email'])->first();

        if ($user) {
            $verificationCode = rand(100000, 999999);

            $user->verification_code = $verificationCode;
            $user->save();

            Mail::to($validated['email'])->send(new ForgotPassword($verificationCode));
        }

        return $this->successResponse();
    }

    /**
     * @param AuthResetPasswordRequest $request
     * @return JsonResponse
     * @throws AuthInvalidVerificationCodeException
     */
    public function reset(AuthResetPasswordRequest $request)
    {
        $validated = $request->validated();

        /** @var User $user */
        $user = User::query()->where('email', $validated['email'])->firstOrFail();

        if ($user->verification_code != $validated['verification_code'])
            throw new AuthInvalidVerificationCodeException();

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return $this->successResponse();
    }


    private function getLoginLocation()
    {
        if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]) && $_SERVER["HTTP_X_FORWARDED_FOR"]) {
            $ipAddress = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } else {
            $ipAddress = $_SERVER["REMOTE_ADDR"];
        }

        try {
            $details = json_decode(file_get_contents("http://ipinfo.io/{$ipAddress}/json"));
            return $details->country . '-' . $details->city;
        } catch (Exception $ex) {
            return '-';
        }
    }
}
