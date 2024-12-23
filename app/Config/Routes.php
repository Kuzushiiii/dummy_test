<?php

use CodeIgniter\Router\RouteCollection;

// Middleware Login
$this->auth = ['filter' => 'auth'];
$this->noauth = ['filter' => 'noauth'];

/**
 * @var RouteCollection $routes
 */
$routes->add('/', 'User::viewLogin', $this->auth);


// Login
$routes->group('login', function ($routes) {
    $routes->add('', 'User::viewLogin', $this->auth);
    $routes->add('auth', 'User::loginAuth', $this->auth);
});
// Routes Master User
$routes->group('user', function ($routes) {
    $routes->add('', 'User::index', $this->noauth);
    $routes->add('table', 'User::datatable', $this->noauth);
    $routes->add('add', 'User::addData', $this->noauth);
    $routes->add('form', 'User::forms', $this->noauth);
    $routes->add('form/(:any)', 'User::forms/$1', $this->noauth);
    $routes->add('update', 'User::updateData', $this->noauth);
    $routes->add('delete', 'User::deleteData', $this->noauth);
});

//document ROUTES
$routes->group('document', function ($routes) {
    $routes->add('', 'document::index', $this->noauth);
    $routes->add('table', 'document::datatable', $this->noauth);
    $routes->add('add', 'document::addData', $this->noauth);
    $routes->add('form', 'document::forms', $this->noauth);
    $routes->add('form/(:any)', 'document::forms/$1', $this->noauth);
    $routes->add('update', 'document::updateData', $this->noauth);
    $routes->add('delete', 'document::deleteData', $this->noauth);
});


$routes->group('customer', function ($routes) { 
    $routes->add('', 'Customer::index', $this->noauth);
    $routes->add('table', 'Customer::datatable', $this->noauth);
    $routes->add('add', 'Customer::addData', $this->noauth);
    $routes->add('form', 'Customer::forms', $this->noauth); // Form tanpa parameter
    $routes->add('form/(:num)', 'Customer::forms/$1', $this->noauth); // Form dengan parameter
    $routes->add('update', 'Customer::updateData', $this->noauth);
    $routes->add('delete', 'Customer::deleteData', $this->noauth);
});

$routes->group('category', function ($routes) { 
    $routes->add('', 'Category::index', $this->noauth);
    $routes->add('table', 'Category::datatable', $this->noauth);
    $routes->add('add', 'Category::addData', $this->noauth);
    $routes->add('form', 'Category::forms', $this->noauth); // Form tanpa parameter
    $routes->add('form/(:any)', 'Category::forms/$1', $this->noauth); // Form dengan parameter
    $routes->add('update', 'Category::updateData', $this->noauth);
    $routes->add('delete', 'Category::deleteData', $this->noauth);
});


// Routes Master Supplier
$routes->group('supplier', function ($routes) {
    $routes->add('/', 'Supplier::index', $this->noauth);
    $routes->add('table', 'Supplier::dataTable', $this->noauth);
    $routes->add('forms', 'Supplier::forms', $this->noauth);
    $routes->add('form/(:any)', 'Supplier::forms/$1', $this->noauth);
    $routes->add('add', 'Supplier::add', $this->noauth);
    $routes->add('update', 'Supplier::update', $this->noauth);
    $routes->add('delete', 'Supplier::delete', $this->noauth);
});

// Routes Master Project
$routes->group('project', function ($routes) {
    $routes->add('', 'Project::index', $this->noauth);
    $routes->add('table', 'Project::datatable', $this->noauth);
    $routes->add('add', 'Project::addData', $this->noauth);
    $routes->add('form', 'Project::forms', $this->noauth);
    $routes->add('form/(:any)', 'Project::forms/$1', $this->noauth);
    $routes->add('update', 'Project::updateData', $this->noauth);
    $routes->add('delete', 'Project::deleteData', $this->noauth);
});
// Routes Master Product
$routes->group('product', function ($routes) {
    $routes->add('', 'Product::index', $this->noauth);
    $routes->add('table', 'Product::datatable', $this->noauth);
    $routes->add('add', 'Product::addData', $this->noauth);
    $routes->add('form', 'Product::forms', $this->noauth);
    $routes->add('form/(:any)', 'Product::forms/$1', $this->noauth);
    $routes->add('update', 'Product::updateData', $this->noauth);
    $routes->add('delete', 'Product::deleteData', $this->noauth);
});
// -------------------------------------------------------->
// Log Out
$routes->add('logout', 'User::logOut');
