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
    $routes->add('printpdf', 'User::printPDF', $this->noauth);
    $routes->add('formImport', 'User::formImport', $this->noauth);
    $routes->add('importExcel', 'User::importExcel', $this->noauth);
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
    $routes->add('formImport', 'document::formImport', $this->noauth);
    $routes->add('importExcel', 'document::importExcel', $this->noauth);
});


$routes->group('customer', function ($routes) {
    $routes->add('', 'Customer::index', $this->noauth);
    $routes->add('table', 'Customer::datatable', $this->noauth);
    $routes->add('add', 'Customer::addData', $this->noauth);
    $routes->add('form', 'Customer::forms', $this->noauth); // Form tanpa parameter
    $routes->add('form/(:num)', 'Customer::forms/$1', $this->noauth); // Form dengan parameter
    $routes->add('update', 'Customer::updateData', $this->noauth);
    $routes->add('exportexcel', 'Customer::exportExcel', $this->noauth);
    $routes->add('printpdf', 'Customer::printPDF', $this->noauth);
    $routes->add('delete', 'Customer::deleteData', $this->noauth);
    $routes->add('formImport', 'Customer::formImport', $this->noauth);
    $routes->add('importExcel', 'Customer::importExcel', $this->noauth);
});
// Routes Master Category
$routes->group('category', function ($routes) {
        $routes->add('', 'Category::index', $this->noauth);
        $routes->add('table', 'Category::datatable', $this->noauth);
        $routes->add('add', 'Category::addData', $this->noauth);
        $routes->add('form', 'Category::forms', $this->noauth); 
        $routes->add('form/(:any)', 'Category::forms/$1', $this->noauth);
        $routes->add('update', 'Category::updateData', $this->noauth);
        $routes->add('delete', 'Category::deleteData', $this->noauth);
        $routes->add('export', 'Category::export', $this->noauth);
        $routes->add('exportPdf', 'Category::exportPdf', $this->noauth); 
    });


// Routes Master Supplier
$routes->group('supplier', function ($routes) {
    $routes->add('/', 'Supplier::index', $this->noauth);
    $routes->add('table', 'Supplier::dataTable', $this->noauth);
    $routes->add('forms', 'Supplier::forms', $this->noauth);
    $routes->add('form/(:any)', 'Supplier::forms/$1', $this->noauth);
    $routes->add('add', 'Supplier::add', $this->noauth);
    $routes->add('update', 'Supplier::update', $this->noauth);
    $routes->add('export', 'Supplier::exportexcel', $this->noauth);
    $routes->add('delete', 'Supplier::delete', $this->noauth);
    $routes->add('pdf', 'Supplier::Fpdf', $this->noauth);
    $routes->add('pdf/(:any)', 'Supplier::Fpdf/$1', $this->noauth);
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
    $routes->add('export', 'Project::exportexcel');
    $routes->get('generatePdf', 'Project::generatePdf');
    $routes->get('generatePdf/(:any)', 'Project::generatePdf/$1', $this->noauth);
});
// Routes Master Product
$routes->group('product', function ($routes) {
    $routes->add('', 'Product::index', $this->noauth);
    $routes->add('table', 'Product::datatable', $this->noauth);
    $routes->add('add', 'Product::addData', $this->noauth);
    $routes->add('form', 'Product::forms', $this->noauth);
    $routes->add('form/(:any)', 'Product::forms/$1', $this->noauth);
    $routes->add('update', 'Product::updateData', $this->noauth);
    $routes->add('export', 'Product::exportexcel', $this->noauth);
    $routes->add('pdf', 'Product::Fpdf', $this->noauth);
    $routes->add('delete', 'Product::deleteData', $this->noauth);
    $routes->add('formImport', 'Product::formImport', $this->noauth);
    $routes->add('importExcel', 'Product::importExcel', $this->noauth);
});
// -------------------------------------------------------->
// Log Out
$routes->get('/logOut', 'User::logOut');

//Export to excel routes
$routes->get('Document/export', 'Document::export');
$routes->get('Document/exportpdf', 'Document::exportpdf');

// --------------------------------------------------------------------->
// Purchase Order Routes    
$routes->group('purchaseorder', function ($routes) {
    $routes->add('', 'PurchaseOrder::index', $this->noauth);
    $routes->add('table', 'PurchaseOrder::datatable', $this->noauth);
    $routes->add('add', 'PurchaseOrder::addData', $this->noauth);
    $routes->add('form', 'PurchaseOrder::forms', $this->noauth);
    $routes->add('form/(:any)', 'PurchaseOrder::forms/$1', $this->noauth);
    $routes->add('update', 'PurchaseOrder::updateData', $this->noauth);
    $routes->add('deleteData', 'PurchaseOrder::deleteData', $this->noauth);
    $routes->post('getsuppliers', 'PurchaseOrder::getSuppliers', $this->noauth);
    $routes->post('getproducts', 'PurchaseOrder::getProducts');
    $routes->post('getuoms', 'PurchaseOrder::getUoms');
    $routes->post('getdetailsajax', 'PurchaseOrder::getDetailsAjax');
    $routes->post('adddetail', 'PurchaseOrder::addDetail');
    $routes->post('saveDetail', 'PurchaseOrder::saveDetail');
    $routes->get('edit/(:alphanum)', 'PurchaseOrder::forms/$1');
    $routes->post('updatedetail', 'PurchaseOrder::updateDetail');
    $routes->post('deleteDetail', 'PurchaseOrder::deleteDetail');
    $routes->get('editDetailModal/(:num)', 'PurchaseOrder::editDetailModal/$1');
    $routes->get('pdf/(:any)', 'PurchaseOrder::generatePdf/$1');
});
