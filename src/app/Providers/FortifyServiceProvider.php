<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Actions\Fortify\LogoutResponse as CustomLogoutResponse;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\LogoutResponse;
use Laravel\Fortify\Contracts\RegisterResponse;
use Illuminate\Support\Facades\Auth;

// ★ LoginRequest を読み込む
use App\Http\Requests\LoginRequest;
use App\Http\Responses\RegisterResponse as CustomRegisterResponse;

class FortifyServiceProvider extends ServiceProvider
{
    public function register()
    {
        // 登録後のリダイレクトカスタム
        $this->app->singleton(RegisterResponse::class, CustomRegisterResponse::class);
    }

    public function boot()
    {
        /*
        |--------------------------------------------------------------------------
        | Register
        |--------------------------------------------------------------------------
        */
        Fortify::registerView(function () {
            return view('auth.register');
        });

        Fortify::createUsersUsing(CreateNewUser::class);

        /*
        |--------------------------------------------------------------------------
        | Login View
        |--------------------------------------------------------------------------
        */
        Fortify::loginView(function () {
            return view('auth.login');
        });

        /*
        |--------------------------------------------------------------------------
        | Login Rate Limit
        |--------------------------------------------------------------------------
        */
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(10)->by(
                ($request->email ?? '') . $request->ip()
            );
        });

        /*
        |--------------------------------------------------------------------------
        | Logout Response
        |--------------------------------------------------------------------------
        */
        $this->app->singleton(LogoutResponse::class, CustomLogoutResponse::class);

        /*
        |--------------------------------------------------------------------------
        | Login Auth Process
        | ★ LoginRequest を強制的に実行する
        |--------------------------------------------------------------------------
        */
        Fortify::authenticateUsing(function ($request) {

            // LoginRequest バリデーション強制適用
            $formRequest = app(LoginRequest::class);
            $formRequest->setContainer(app())->setRedirector(app('redirect'));
            $formRequest->validateResolved(); // ← バリデーション実行

            // バリデーション済み値
            $credentials = $formRequest->only('email', 'password');

            // 認証
            if (!Auth::attempt($credentials)) {
                return null; // → "ログイン情報が登録されていません"
            }

            return Auth::user();
        });

        /*
        |--------------------------------------------------------------------------
        | Verify Email View
        |--------------------------------------------------------------------------
        */
        Fortify::verifyEmailView(function () {
            return view('auth.verify-email');
        });
    }
}