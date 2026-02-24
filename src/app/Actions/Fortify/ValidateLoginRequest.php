<?php

namespace App\Actions\Fortify;

use App\Http\Requests\LoginRequest;

class ValidateLoginRequest
{
    public function handle($request, $next)
    {
        dd('ここ通ってる？');
        $formRequest = LoginRequest::createFrom($request);
        $formRequest->setContainer(app())->setRedirector(app('redirect'));
        $formRequest->validateResolved();

        return $next($request);
    }
}