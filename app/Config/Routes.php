<?php

use CodeIgniter\Router\RouteCollection;

use App\Controllers\Pessoal;
use App\Controllers\ExemploGrocery;

/**
 * @var RouteCollection $routes
 */

 /**
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('site');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */


$routes->get('/pessoal', [Pessoal::class, 'index']);
$routes->match(['get', 'post'], '/crud/(:any)', [ExemploGrocery::class, 'index']);

$routes->get('/outra', [ExemploGrocery::class, 'outra']);





