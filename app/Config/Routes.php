<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Public Routes
$routes->get('/', 'Home::index');
$routes->get('login', 'Auth::login');
$routes->post('loginProcess', 'Auth::loginProcess');
$routes->get('logout', 'Auth::logout');

// Group 1: General Protected Routes (Any logged-in user)
$routes->group('', ['filter' => 'auth'], function($routes) {
    
    $routes->get('dashboard', 'Dashboard::index');
    $routes->get('dashboard/getChartData', 'Dashboard::getChartData');

    // Group 2: High-Level Access (Admin: 1 & Pharmacist: 2)
    // These users can handle Purchases and Issuances
    $routes->group('', ['filter' => 'role:1,2'], function($routes) {
        
        // Purchase Module
        $routes->get('purchase', 'PurchaseController::index');
        $routes->get('purchase/getPurchaseHistory', 'PurchaseController::getPurchaseHistory');
        $routes->get('purchase/getPurchaseItems/(:num)', 'PurchaseController::getPurchaseItems/$1');
        $routes->post('purchase/store', 'PurchaseController::store');
        $routes->get('purchase/exportDetailed', 'PurchaseController::exportDetailed');
        // Issuance Module
        $routes->get('issuance', 'IssuanceController::index');
        $routes->post('issuance/store', 'IssuanceController::store');
        $routes->get('issuance/getIssuanceLog', 'IssuanceController::getIssuanceLog');
    });

    // Group 3: Admin Only (Role: 1)
    // Only Admins can manage the Master List (Med Registry) and Delete items
    $routes->group('', ['filter' => 'role:1,2'], function($routes) {
        
        $routes->get('medlist', 'MedListController::index');
        $routes->post('medlist/getMedList', 'MedListController::getMedList');
        $routes->post('medlist/getInventory', 'MedListController::getInventory');
        $routes->post('medlist/store', 'MedListController::store');
        $routes->delete('medlist/delete/(:num)', 'MedListController::delete/$1');
        $routes->get('medlist/getHistory/(:num)', 'MedListController::getHistory/$1');
    });

    // User Management - Admin Only
$routes->group('users', ['filter' => 'role:1'], function($routes) {
    $routes->get('/', 'UserController::index');
    $routes->post('getUsers', 'UserController::getUsers');
    $routes->post('store', 'UserController::store');
    $routes->post('toggleStatus/(:num)', 'UserController::toggleStatus/$1');
});
});