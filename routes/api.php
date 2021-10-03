<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::namespace('API\V1')->prefix('v1')->group(function () {
    Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        return $request->user();
    });

    Route::prefix('auth')->group(function () {
        Route::post('/login', 'AuthController@login');
        Route::post('/register', 'AuthController@register');
        Route::post('/forgot', 'AuthController@forgot');
        Route::post('/reset', 'AuthController@reset');
        Route::middleware('auth:sanctum')->post('/logout', 'AuthController@logout');
    });

    Route::group(['middleware' => ['auth:sanctum']], function () {
        Route::prefix('users')->group(function () {
            Route::get('/profile', 'UserController@profile');
            Route::get('/request-mobile-code', 'UserController@requestMobileVerificationCode');
            Route::get('/request-email-code', 'UserController@requestEmailVerificationCode');
            Route::put('/verify-email', 'UserController@verifyEmail');
            Route::put('/verify-mobile', 'UserController@verifyMobile');
            Route::post('/subscribe-push', 'UserController@subscribePushNotification');
            Route::get('/notifications', 'UserController@getNotifications');
            Route::put('/change-password', 'UserController@changePassword');
            Route::put('/update-avatar', 'UserController@updateAvatar');
            Route::put('/update-profile', 'UserController@updateProfile');
            Route::get('/menus', 'UserController@getMenus');
            Route::get('/chats', 'UserController@getChats');


            Route::middleware('role:developer|super-admin')->group(function () {
                Route::get('/meetings', 'UserController@getMeetings');
            });

            Route::middleware('permission:RESET_PASSWORD_USER')->put('{user}/reset-password', 'UserController@resetPassword');
            Route::middleware('permission:GET_ALL_ROLES_USER')->get('{user}/roles', 'UserController@getRoles');
        });

        Route::prefix('tags')->group(function () {
            Route::middleware('permission:ATTACH_TAG')->put('{tag_name}/attach/{taggable_type}/{taggable_id}', 'TagController@attach');
            Route::middleware('permission:DETACH_TAG')->put('{tag_name}/detach/{taggable_type}/{taggable_id}', 'TagController@detach');
        });

        Route::prefix('notifications')->group(function () {
            Route::middleware('permission:SEND_NOTIFICATION')->post('', 'NotificationController@sendNotification');
            Route::middleware('permission:READ_NOTIFICATION')->put('{notification}', 'NotificationController@readNotification');
        });

        Route::prefix('roles')->group(function () {
            Route::middleware('permission:GET_ALL_USERS_ROLE')->get('{role}/users', 'RoleController@getUsers');
            Route::middleware('permission:ADD_USER_ROLE')->post('{role}/users', 'RoleController@postUser');
            Route::middleware('permission:REMOVE_USER_ROLE')->delete('{role}/users/{user}', 'RoleController@deleteUser');
            Route::middleware('permission:GET_ALL_PERMISSIONS_ROLE')->get('{role}/permissions', 'RoleController@getPermissions');
            Route::middleware('permission:ADD_PERMISSION_ROLE')->post('{role}/permissions', 'RoleController@postPermission');
            Route::middleware('permission:REMOVE_PERMISSION_ROLE')->delete('{role}/permissions/{permission}', 'RoleController@deletePermission');
            Route::middleware('permission:CLONE_ROLE')->post('{role}/clone', 'RoleController@clone');
            Route::middleware('permission:ADD_MENU_ROLE')->post('{role}/menus', 'RoleController@postMenu');
            Route::middleware('permission:REMOVE_MENU_ROLE')->delete('{role}/menus/{menu}', 'RoleController@deleteMenu');
        });

        Route::softDeleteRoutes('users', 'UserController');
        Route::apiResource('notifications', 'NotificationController')->only(['index', 'show']);
        Route::apiResource('permissions', 'PermissionController')->only(['index', 'show']);
        Route::apiResource('meetings', 'MeetingController');
        Route::apiResource('tags', 'TagController');
        Route::apiResource('users', 'UserController');
        Route::apiResource('roles', 'RoleController');
    });
});
