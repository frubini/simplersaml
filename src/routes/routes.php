<?php

Route::prefix(Config::get('simplersaml.routePrefix'))->group(function () {
    Route::get('login', [SimplerSaml\Http\Controllers\SamlController::class, 'login'])
        ->name('saml.login');
    Route::get('logout', [SimplerSaml\Http\Controllers\SamlController::class, 'logout'])
        ->name('saml.logout');
});
