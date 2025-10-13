<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/(:any)', 'Home::index');
$routes->post('/getusage', 'Home::getusage');