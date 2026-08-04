<?php

// Define application routes

$router->add('GET', '/', 'SpeedtestController@index');
$router->add('GET', '/empty', 'EngineController@empty');
$router->add('POST', '/empty', 'EngineController@empty');
$router->add('GET', '/garbage', 'EngineController@garbage');
$router->add('GET', '/getIP', 'EngineController@getIP');
$router->add('POST', '/telemetry', 'TelemetryController@store');
$router->add('GET', '/results/{id}', 'TelemetryController@show');
$router->add('GET', '/results', 'TelemetryController@show');
$router->add('GET', '/results/', 'TelemetryController@show');
$router->add('GET', '/stats', 'TelemetryController@stats', ['AuthMiddleware']);
$router->add('GET', '/login', 'AuthController@showLogin');
$router->add('POST', '/login', 'AuthController@login');
$router->add('GET', '/logout', 'AuthController@logout');
