<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
 
// page d'accueil
$routes->get('/', 'Home::index');
 
// routes operateur
$routes->group('operateur', ['namespace' => 'App\Controllers'], function ($routes) {
 
    // prefixes
    $routes->get('prefixes', 'OperateurController::prefixes');
    $routes->post('prefixes/ajouter', 'OperateurController::ajouterPrefixe');
    $routes->get('prefixes/supprimer/(:num)', 'OperateurController::supprimerPrefixe/$1');
 
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
 