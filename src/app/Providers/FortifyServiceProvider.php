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
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\LogoutResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\LoginRequest;
use Laravel\Fortify\Contracts\RegisterResponse;
use App\Http\Responses\RegisterResponse as CustomRegisterResponse;


class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        $this->app->singleton(RegisterResponse::class, CustomRegisterResponse::class);
    }
    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        Fortify::registerView(function () {
            return view('auth.register');
        });

        {
        Fortify::createUsersUsing(CreateNewUser::class);
            }

        Fortify::loginView(function () {
            return view('auth.login');
        });

        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->email;

            return Limit::perMinute(10)->by($email . $request->ip());
        });

        $this->app->singleton(LogoutResponse::class, CustomLogoutResponse::class);

        Fortify::authenticateUsing(function ($request) {

            app(\App\Http\Requests\LoginRequest::class)
                ->setContainer(app())
                ->setRedirector(app('redirect'))
                ->validateResolved();

            if (\Auth::attempt($request->only('email', 'password'))) {
                return \Auth::user();
            }

            return null;
        });

        Fortify::verifyEmailView(function () {
                return view('auth.verify-email');
            });

    }

}
