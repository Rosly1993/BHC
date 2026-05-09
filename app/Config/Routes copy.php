<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->get('login', 'Auth::login');
$routes->post('loginProcess', 'Auth::loginProcess');
$routes->get('logout', 'Auth::logout');


// Protected routes
// $routes->group('', ['filter' => ['auth','jwt']], function($routes) {
$routes->group('', ['filter' => ['auth']], function($routes) {

    $routes->get('dashboard', 'Dashboard::index', ['as' => 'dashboard']);
    

    //Roles
    $routes->group('roles', function($routes) {
        $routes->get('/', 'RolesController::index', ['as' => 'roles']);
        $routes->get('create', 'RolesController::create', ['as' => 'roles.create']);
        $routes->post('store', 'RolesController::store', ['as' => 'roles.store']);
        $routes->get('edit/(:num)', 'RolesController::edit/$1', ['as' => 'roles.edit']);
        $routes->post('update/(:num)', 'RolesController::update/$1', ['as' => 'roles.update']);
        $routes->post('delete/(:num)', 'RolesController::delete/$1', ['as' => 'roles.delete']);
        $routes->post('toggle-status/(:num)', 'RolesController::toggleStatus/$1', ['as' => 'roles.toggle']);
    });


     //Factories
    $routes->group('factories', function($routes) {
        $routes->get('/', 'FactoriesController::index', ['as' => 'factories']);
        $routes->get('create', 'FactoriesController::create', ['as' => 'factories.create']);
        $routes->post('store', 'FactoriesController::store', ['as' => 'factories.store']);
        $routes->get('edit/(:num)', 'FactoriesController::edit/$1', ['as' => 'factories.edit']);
        $routes->post('update/(:num)', 'FactoriesController::update/$1', ['as' => 'factories.update']);
        $routes->post('delete/(:num)', 'FactoriesController::delete/$1', ['as' => 'factories.delete']);
        $routes->post('toggle-status/(:num)', 'FactoriesController::toggleStatus/$1', ['as' => 'factories.toggle']);
    });


     //Components
    $routes->group('components', function($routes) {
        $routes->get('/', 'ComponentsController::index', ['as' => 'components']);
        $routes->get('create', 'ComponentsController::create', ['as' => 'components.create']);
        $routes->post('store', 'ComponentsController::store', ['as' => 'components.store']);
        $routes->get('edit/(:num)', 'ComponentsController::edit/$1', ['as' => 'components.edit']);
        $routes->post('update/(:num)', 'ComponentsController::update/$1', ['as' => 'components.update']);
        $routes->post('delete/(:num)', 'ComponentsController::delete/$1', ['as' => 'components.delete']);
        $routes->post('toggle-status/(:num)', 'ComponentsController::toggleStatus/$1', ['as' => 'components.toggle']);
    });

    //Areas
    $routes->group('areas', function($routes) {
        $routes->get('/', 'AreasController::index', ['as' => 'areas']);

        // ADD THIS LINE:
         $routes->get('get-components/(:num)', 'AreasController::getComponentsByFactory/$1');

        $routes->get('create', 'AreasController::create', ['as' => 'areas.create']);
        $routes->post('store', 'AreasController::store', ['as' => 'areas.store']);
        $routes->get('edit/(:num)', 'AreasController::edit/$1', ['as' => 'areas.edit']);
        $routes->post('update/(:num)', 'AreasController::update/$1', ['as' => 'areas.update']);
        $routes->post('delete/(:num)', 'AreasController::delete/$1', ['as' => 'areas.delete']);
        $routes->post('toggle-status/(:num)', 'AreasController::toggleStatus/$1', ['as' => 'areas.toggle']);
    });

    //Processes
    $routes->group('processes', function($routes) {
        $routes->get('/', 'ProcessesController::index', ['as' => 'processes']);

        // ADD THIS LINE:
         $routes->get('get-components/(:num)', 'ProcessesController::getComponentsByFactory/$1');
         $routes->get('get-areas/(:num)', 'ProcessesController::getAreasByComponent/$1');
         

        $routes->get('create', 'ProcessesController::create', ['as' => 'processes.create']);
        $routes->post('store', 'ProcessesController::store', ['as' => 'processes.store']);
        $routes->get('edit/(:num)', 'ProcessesController::edit/$1', ['as' => 'processes.edit']);
        $routes->post('update/(:num)', 'ProcessesController::update/$1', ['as' => 'processes.update']);
        $routes->post('delete/(:num)', 'ProcessesController::delete/$1', ['as' => 'processes.delete']);
        $routes->post('toggle-status/(:num)', 'ProcessesController::toggleStatus/$1', ['as' => 'processes.toggle']);
    });


    // Users
    $routes->group('users', function($routes) {
        $routes->get('/', 'UsersController::index');
        $routes->get('create', 'UsersController::create');
        $routes->post('store', 'UsersController::store');
        $routes->get('edit/(:num)', 'UsersController::edit/$1');
        $routes->post('update/(:num)', 'UsersController::update/$1');
        $routes->post('delete/(:num)', 'UsersController::delete/$1');
        $routes->post('toggle-status/(:num)', 'UsersController::toggleStatus/$1');
        $routes->get('check-username', 'UsersController::checkUsername');

        $routes->post('toggle-lock/(:num)', 'UsersController::toggleLock/$1');

         // ADD THIS LINE:
         $routes->get('get-components/(:num)', 'UsersController::getComponentsByFactory/$1');
         $routes->get('get-areas/(:num)', 'UsersController::getAreasByComponent/$1');
    });

    //Transactions
    $routes->group('transactions', function($routes) {
        $routes->get('/', 'TransactionsController::index', ['as' => 'transactions']);
        $routes->get('create', 'TransactionsController::create', ['as' => 'transactions.create']);
        $routes->post('store', 'TransactionsController::store', ['as' => 'transactions.store']);
        $routes->get('edit/(:num)', 'TransactionsController::edit/$1', ['as' => 'transactions.edit']);
        $routes->post('update/(:num)', 'TransactionsController::update/$1', ['as' => 'transactions.update']);
        $routes->post('delete/(:num)', 'TransactionsController::delete/$1', ['as' => 'transactions.delete']);
        $routes->post('toggle-status/(:num)', 'TransactionsController::toggleStatus/$1', ['as' => 'transactions.toggle']);
    });

    //Process Transactions
    $routes->group('process_transactions', function($routes) {
        $routes->get('/', 'ProcessTransactionsController::index', ['as' => 'process_transactions']);

        // ADD THIS LINE:
         $routes->get('get-components/(:num)', 'ProcessTransactionsController::getComponentsByFactory/$1');
         $routes->get('get-areas/(:num)', 'ProcessTransactionsController::getAreasByComponent/$1');
         $routes->get('get-processes/(:num)', 'ProcessTransactionsController::getProcessByArea/$1');
         

        $routes->get('create', 'ProcessTransactionsController::create', ['as' => 'process_transactions.create']);
        $routes->post('store', 'ProcessTransactionsController::store', ['as' => 'process_transactions.store']);
        $routes->get('edit/(:num)', 'ProcessTransactionsController::edit/$1', ['as' => 'process_transactions.edit']);
        $routes->post('update/(:num)', 'ProcessTransactionsController::update/$1', ['as' => 'process_transactions.update']);
        $routes->post('delete/(:num)', 'ProcessTransactionsController::delete/$1', ['as' => 'process_transactions.delete']);
        $routes->post('toggle-status/(:num)', 'ProcessTransactionsController::toggleStatus/$1', ['as' => 'process_transactions.toggle']);
    });

 //Models
    $routes->group('models', function($routes) {
        $routes->get('/', 'ModelsController::index', ['as' => 'models']);
        $routes->get('create', 'ModelsController::create', ['as' => 'models.create']);
        $routes->post('store', 'ModelsController::store', ['as' => 'models.store']);
        $routes->get('edit/(:num)', 'ModelsController::edit/$1', ['as' => 'models.edit']);
        $routes->post('update/(:num)', 'ModelsController::update/$1', ['as' => 'models.update']);
        $routes->post('delete/(:num)', 'ModelsController::delete/$1', ['as' => 'components.delete']);
        $routes->post('toggle-status/(:num)', 'ModelsController::toggleStatus/$1', ['as' => 'components.toggle']);
    });


    // app/Config/Routes.php
$routes->group('processsequences', function($routes) {
    $routes->get('/', 'ProcesssequencesController::index', ['as' => 'processsequences']);
    
    // Dropdown Data Routes
    $routes->get('get-components/(:num)', 'ProcesssequencesController::getComponentsByFactory/$1');
    $routes->get('get-models/(:num)', 'ProcesssequencesController::get_models/$1');
    $routes->get('get-processes-by-component/(:num)', 'ProcesssequencesController::get_processes_by_component/$1');

    // Standard CRUD
    $routes->get('create', 'ProcesssequencesController::create', ['as' => 'processsequences.create']);
    $routes->post('store', 'ProcesssequencesController::store', ['as' => 'processsequences.store']);
    $routes->get('edit/(:num)', 'ProcesssequencesController::edit/$1', ['as' => 'processsequences.edit']);
    $routes->post('update/(:num)', 'ProcesssequencesController::update/$1', ['as' => 'processsequences.update']);
    $routes->post('delete/(:num)', 'ProcesssequencesController::delete/$1', ['as' => 'processsequences.delete']);
    $routes->post('toggle-status/(:num)', 'ProcesssequencesController::toggleStatus/$1', ['as' => 'processsequences.toggle']);
});
//Model Type Name
    $routes->group('modeltypenames', function($routes) {
        $routes->get('/', 'ModelTypeNamesController::index', ['as' => 'modeltypenames']);

        // ADD THIS LINE:
         $routes->get('get-models/(:num)', 'ModelTypeNamesController::getModelByFactory/$1');

        $routes->get('create', 'ModelTypeNamesController::create', ['as' => 'modeltypenames.create']);
        $routes->post('store', 'ModelTypeNamesController::store', ['as' => 'modeltypenames.store']);
        $routes->get('edit/(:num)', 'ModelTypeNamesController::edit/$1', ['as' => 'modeltypenames.edit']);
        $routes->post('update/(:num)', 'ModelTypeNamesController::update/$1', ['as' => 'modeltypenames.update']);
        $routes->post('delete/(:num)', 'ModelTypeNamesController::delete/$1', ['as' => 'modeltypenames.delete']);
        $routes->post('toggle-status/(:num)', 'ModelTypeNamesController::toggleStatus/$1', ['as' => 'modeltypenames.toggle']);
    });


        //Permissions
    $routes->group('permissions', function($routes) {
        $routes->get('/', 'PermissionsController::index', ['as' => 'permissions']);

        // ADD THIS LINE:
         $routes->get('get-components/(:num)', 'PermissionsController::getComponentsByFactory/$1');
         $routes->get('get-processes-by-component/(:num)', 'PermissionsController::getProcessesByComponent/$1');

        $routes->get('create', 'PermissionsController::create', ['as' => 'permissions.create']);
        $routes->post('store', 'PermissionsController::store', ['as' => 'permissions.store']);
        $routes->get('edit/(:num)', 'PermissionsController::edit/$1', ['as' => 'permissions.edit']);
        $routes->post('update/(:num)', 'PermissionsController::update/$1', ['as' => 'permissions.update']);
        $routes->post('delete/(:num)', 'PermissionsController::delete/$1', ['as' => 'permissions.delete']);
        $routes->post('toggle-status/(:num)', 'PermissionsController::toggleStatus/$1', ['as' => 'permissions.toggle']);
    });

    //Modules
    $routes->group('modules', function($routes) {
        $routes->get('/', 'ModulesController::index', ['as' => 'modules']);
        $routes->get('create', 'ModulesController::create', ['as' => 'modules.create']);
        $routes->post('store', 'ModulesController::store', ['as' => 'modules.store']);
        $routes->get('edit/(:num)', 'ModulesController::edit/$1', ['as' => 'modules.edit']);
        $routes->post('update/(:num)', 'ModulesController::update/$1', ['as' => 'modules.update']);
        $routes->post('delete/(:num)', 'ModulesController::delete/$1', ['as' => 'modules.delete']);
        $routes->post('toggle-status/(:num)', 'ModulesController::toggleStatus/$1', ['as' => 'modules.toggle']);
    });


      //Module Permissions
    $routes->group('modulepermissions', function($routes) {
        $routes->get('/', 'ModulepermissionsController::index', ['as' => 'modulepermissions']);

        // ADD THIS LINE:
         $routes->get('get-components/(:num)', 'ModulepermissionsController::getComponentsByFactory/$1');
         $routes->get('get-modules/(:num)', 'ModulepermissionsController::getModules/$1');

        $routes->get('create', 'ModulepermissionsController::create', ['as' => 'modulepermissions.create']);
        $routes->post('store', 'ModulepermissionsController::store', ['as' => 'modulepermissions.store']);
        $routes->get('edit/(:num)', 'ModulepermissionsController::edit/$1', ['as' => 'modulepermissions.edit']);
        $routes->post('update/(:num)', 'ModulepermissionsController::update/$1', ['as' => 'modulepermissions.update']);
        $routes->post('delete/(:num)', 'ModulepermissionsController::delete/$1', ['as' => 'modulepermissions.delete']);
        $routes->post('toggle-status/(:num)', 'ModulepermissionsController::toggleStatus/$1', ['as' => 'modulepermissions.toggle']);
    });


    //MainTransaction
    $routes->group('main_transaction', function($routes) {
    $routes->get('/', 'MainTransaction::index');
    $routes->get('process/(:num)', 'MainTransaction::process_transactions/$1', ['as' => 'process_view']);
    $routes->get('get_models_by_factory', 'MainTransaction::get_models_by_factory'); 
    $routes->get('get_unique_models', 'MainTransaction::get_unique_models'); 
    // FIX: Added (:num) so it can accept the ID from your JS fetch
    $routes->get('get_types_by_model/(:num)', 'MainTransaction::get_types_by_model/$1'); 
    $routes->get('get_machines_by_process/(:num)', 'MainTransaction::get_machines_by_process/$1');
    $routes->get('get_cut_offs_by_process/(:num)', 'MainTransaction::get_cut_offs_by_process/$1');
    $routes->get('get_model_sequence_limits/(:num)/(:num)', 'MainTransaction::get_model_sequence_limits/$1/$2');
    $routes->get('get_workweek_info', 'MainTransaction::get_workweek_info');
    $routes->get('check_serial_status', 'MainTransaction::check_serial_status');
    $routes->get('get_all_transactions', 'MainTransaction::get_all_transactions');
    $routes->get('get_serial_full_info', 'MainTransaction::get_serial_full_info');
    $routes->get('get_defects_by_process/(:num)', 'MainTransaction::get_defects_by_process/$1');
    // 5. Form Submission  
    $routes->post('save_entry', 'MainTransaction::save_entry');
});


     //Machines
    $routes->group('machines', function($routes) {
        $routes->get('/', 'MachinesController::index', ['as' => 'machines']);

        // ADD THIS LINE:
         $routes->get('get-components/(:num)', 'MachinesController::getComponentsByFactory/$1');
         $routes->get('get-areas/(:num)', 'MachinesController::getAreasByComponent/$1');
         $routes->get('get-processes/(:num)', 'MachinesController::get_processes/$1');
         

        $routes->get('create', 'MachinesController::create', ['as' => 'machines.create']);
        $routes->post('store', 'MachinesController::store', ['as' => 'machines.store']);
        $routes->get('edit/(:num)', 'MachinesController::edit/$1', ['as' => 'machines.edit']);
        $routes->post('update/(:num)', 'MachinesController::update/$1', ['as' => 'machines.update']);
        $routes->post('delete/(:num)', 'MachinesController::delete/$1', ['as' => 'machines.delete']);
        $routes->post('toggle-status/(:num)', 'MachinesController::toggleStatus/$1', ['as' => 'machines.toggle']);
    });

 //Cut Off
    $routes->group('cutoffs', function($routes) {
        $routes->get('/', 'CutoffsController::index', ['as' => 'cutoffs']);

        // ADD THIS LINE:
         $routes->get('get-components/(:num)', 'CutoffsController::getComponentsByFactory/$1');
         $routes->get('get-areas/(:num)', 'CutoffsController::getAreasByComponent/$1');
         

        $routes->get('create', 'CutoffsController::create', ['as' => 'cutoffs.create']);
        $routes->post('store', 'CutoffsController::store', ['as' => 'cutoffs.store']);
        $routes->get('edit/(:num)', 'CutoffsController::edit/$1', ['as' => 'cutoffs.edit']);
        $routes->post('update/(:num)', 'CutoffsController::update/$1', ['as' => 'cutoffs.update']);
        $routes->post('delete/(:num)', 'CutoffsController::delete/$1', ['as' => 'cutoffs.delete']);
        $routes->post('toggle-status/(:num)', 'CutoffsController::toggleStatus/$1', ['as' => 'cutoffs.toggle']);
    });


      //Defects
    $routes->group('defects', function($routes) {
        $routes->get('/', 'DefectsController::index', ['as' => 'defects']);

        // ADD THIS LINE:
         $routes->get('get-components/(:num)', 'DefectsController::getComponentsByFactory/$1');
         $routes->get('get-areas/(:num)', 'DefectsController::getAreasByComponent/$1');
         $routes->get('get-processes/(:num)', 'DefectsController::get_processes/$1');
         

        $routes->get('create', 'DefectsController::create', ['as' => 'defects.create']);
        $routes->post('store', 'DefectsController::store', ['as' => 'defects.store']);
        $routes->get('edit/(:num)', 'DefectsController::edit/$1', ['as' => 'defects.edit']);
        $routes->post('update/(:num)', 'DefectsController::update/$1', ['as' => 'defects.update']);
        $routes->post('delete/(:num)', 'DefectsController::delete/$1', ['as' => 'defects.delete']);
        $routes->post('toggle-status/(:num)', 'DefectsController::toggleStatus/$1', ['as' => 'defects.toggle']);
    });

      //BspController
  $routes->group('bsp_data', function($routes) {
    $routes->get('/', 'BspController::index');
    $routes->get('(:num)', 'BspController::index/$1'); // Year
    $routes->get('(:num)/(:num)', 'BspController::index/$1/$2'); // Month
    $routes->get('(:num)/(:num)/(:any)', 'BspController::index/$1/$2/$3'); // Date
});

    });
