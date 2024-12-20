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
// Routing Master Dibawah Sini ---------------------------

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

// -------------------------------------------------------->
// Log Out
$routes->add('logout', 'User::logOut');
