<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('client/login', 'ClientController::showLoginForm');
$routes->post('client/login', 'ClientController::login');
$routes->get('client/logout', 'ClientController::logout');

$routes->group('client', ['filter' => 'role:client'], function ($routes) {
    $routes->get('/', 'ClientController::index');
    $routes->get('depot', 'ClientController::depot');
    $routes->post('depot', 'ClientController::doDepot');
    $routes->get('retrait', 'ClientController::retrait');
    $routes->post('retrait', 'ClientController::doRetrait');
    $routes->get('transfert', 'ClientController::transfert');
    $routes->post('transfert', 'ClientController::doTransfert');
    $routes->get('historique', 'ClientController::historique');
});