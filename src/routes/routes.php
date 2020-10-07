<?php

Route::resources(
    Config::get('simplersaml.routePrefix'),
    'SimplerSaml\Http\Controllers\SamlController'
);
