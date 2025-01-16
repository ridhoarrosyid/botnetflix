<?php

use App\Controllers\Home;
use App\Controllers\ResetPasswordController;
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', [Home::class, "index"]);



$routes->post('api/reset_password', [ResetPasswordController::class, 'reset_password']);
