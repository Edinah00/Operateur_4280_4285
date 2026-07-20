<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
 
// page d'accueil
$routes->get('/', 'ClientController::index');
 
// authentification operateur
$routes->get('operateur/login', 'OperateurController::login');
$routes->get('operateur/logout', 'OperateurController::logout');

// routes operateur
$routes->group('operateur', ['namespace' => 'App\Controllers', 'filter' => 'role:operateur'], function ($routes) {
    $routes->get('/', 'OperateurController::index');

    // prefixes
    $routes->get('prefixes', 'OperateurController::prefixes');
    $routes->post('prefixes/ajouter', 'OperateurController::ajouterPrefixe');
    $routes->get('prefixes/supprimer/(:num)', 'OperateurController::supprimerPrefixe/$1');
    $routes->post('prefixes/supprimer/(:num)/(:num)', 'OperateurController::supprimerAssociationPrefixe/$1/$2');
 
    // types d'operation + baremes de frais
    $routes->get('baremes', 'OperateurController::baremes');
    $routes->post('baremes/ajouter', 'OperateurController::ajouterBareme');
    $routes->get('baremes/supprimer/(:num)', 'OperateurController::supprimerBareme/$1');
    $routes->post('baremes/modifier/(:num)', 'OperateurController::modifierBareme/$1');
 
    // situation des gains
    $routes->get('gains', 'OperateurController::gains');
 
    // situation des comptes clients
    $routes->get('comptes', 'OperateurController::comptes');
});
 
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
    $routes->get('historique/(:num)', 'ClientController::historiqueDetail/$1');
});
