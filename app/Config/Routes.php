<?php

use CodeIgniter\Router\RouteCollection;

// Middleware Login
$this->auth = ['filter' => 'auth'];
$this->noauth = ['filter' => 'noauth'];

/**
 * @var RouteCollection $routes
 */
$routes->add('/', 'User::viewLogin', $this->noauth);


// Login
$routes->group('login', function ($routes) {
    $routes->add('', 'User::viewLogin', $this->noauth);
    $routes->add('auth', 'User::loginAuth', $this->noauth);
});
// Routes Master User
$routes->group('user', function ($routes) {
    $routes->add('', 'User::index', $this->auth);
    $routes->add('table', 'User::datatable', $this->auth);
    $routes->add('add', 'User::addData', $this->auth);
    $routes->add('form', 'User::forms', $this->auth);
    $routes->add('form/(:any)', 'User::forms/$1', $this->auth);
    $routes->add('update', 'User::updateData', $this->auth);
    $routes->add('delete', 'User::deleteData', $this->auth);
    $routes->add('printpdf', 'User::printPDF', $this->auth);
    $routes->add('formImport', 'User::formImport', $this->auth);
    $routes->add('importExcel', 'User::importExcel', $this->auth);
});

//document ROUTES
$routes->group('document', function ($routes) {
    $routes->add('', 'document::index', $this->auth);
    $routes->add('table', 'document::datatable', $this->auth);
    $routes->add('add', 'document::addData', $this->auth);
    $routes->add('form', 'document::forms', $this->auth);
    $routes->add('form/(:any)', 'document::forms/$1', $this->auth);
    $routes->add('update', 'document::updateData', $this->auth);
    $routes->add('delete', 'document::deleteData', $this->auth);
    $routes->add('formImport', 'document::formImport', $this->auth);
    $routes->add('importExcel', 'document::importExcel', $this->auth);
});


$routes->group('customer', function ($routes) {
    $routes->add('', 'Customer::index', $this->auth);
    $routes->add('table', 'Customer::datatable', $this->auth);
    $routes->add('add', 'Customer::addData', $this->auth);
    $routes->add('form', 'Customer::forms', $this->auth); // Form tanpa parameter
    $routes->add('form/(:num)', 'Customer::forms/$1', $this->auth); // Form dengan parameter
    $routes->add('update', 'Customer::updateData', $this->auth);
    $routes->add('exportexcel', 'Customer::exportExcel', $this->auth);
    $routes->add('printpdf', 'Customer::printPDF', $this->auth);
    $routes->add('delete', 'Customer::deleteData', $this->auth);
    $routes->add('formImport', 'Customer::formImport', $this->auth);
    $routes->add('importExcel', 'Customer::importExcel', $this->auth);
});
// Routes Master Category
$routes->group('category', function ($routes) {
        $routes->add('', 'Category::index', $this->auth);
        $routes->add('table', 'Category::datatable', $this->auth);
        $routes->add('add', 'Category::addData', $this->auth);
        $routes->add('form', 'Category::forms', $this->auth); 
        $routes->add('form/(:any)', 'Category::forms/$1', $this->auth);
        $routes->add('update', 'Category::updateData', $this->auth);
        $routes->add('delete', 'Category::deleteData', $this->auth);
        $routes->add('export', 'Category::export', $this->auth);
        $routes->add('exportPdf', 'Category::exportPdf', $this->auth); 
    });


// Routes Master Supplier
$routes->group('supplier', function ($routes) {
    $routes->add('/', 'Supplier::index', $this->auth);
    $routes->add('table', 'Supplier::dataTable', $this->auth);
    $routes->add('forms', 'Supplier::forms', $this->auth);
    $routes->add('form/(:any)', 'Supplier::forms/$1', $this->auth);
    $routes->add('add', 'Supplier::add', $this->auth);
    $routes->add('update', 'Supplier::update', $this->auth);
    $routes->add('export', 'Supplier::exportexcel', $this->auth);
    $routes->add('delete', 'Supplier::delete', $this->auth);
    $routes->add('pdf', 'Supplier::Fpdf', $this->auth);
    $routes->add('pdf/(:any)', 'Supplier::Fpdf/$1', $this->auth);
});

// Routes Master Project
$routes->group('project', function ($routes) {
    $routes->add('', 'Project::index', $this->auth);
    $routes->add('table', 'Project::datatable', $this->auth);
    $routes->add('add', 'Project::addData', $this->auth);
    $routes->add('form', 'Project::forms', $this->auth);
    $routes->add('form/(:any)', 'Project::forms/$1', $this->auth);
    $routes->add('update', 'Project::updateData', $this->auth);
    $routes->add('delete', 'Project::deleteData', $this->auth);
    $routes->add('export', 'Project::exportexcel');
    $routes->get('generatePdf', 'Project::generatePdf');
    $routes->get('generatePdf/(:any)', 'Project::generatePdf/$1', $this->auth);
});
// Routes Master Product
$routes->group('product', function ($routes) {
    $routes->add('', 'Product::index', $this->auth);
    $routes->add('table', 'Product::datatable', $this->auth);
    $routes->add('add', 'Product::addData', $this->auth);
    $routes->add('form', 'Product::forms', $this->auth);
    $routes->add('form/(:any)', 'Product::forms/$1', $this->auth);
    $routes->add('update', 'Product::updateData', $this->auth);
    $routes->add('export', 'Product::exportexcel', $this->auth);
    $routes->add('pdf', 'Product::Fpdf', $this->auth);
    $routes->add('delete', 'Product::deleteData', $this->auth);
    $routes->add('formImport', 'Product::formImport', $this->auth);
    $routes->add('importExcel', 'Product::importExcel', $this->auth);
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
    $routes->add('', 'PurchaseOrder::index', $this->auth);
    $routes->add('table', 'PurchaseOrder::datatable', $this->auth);
    $routes->add('add', 'PurchaseOrder::addData', $this->auth);
    $routes->add('form', 'PurchaseOrder::forms', $this->auth);
    $routes->add('form/(:any)', 'PurchaseOrder::forms/$1', $this->auth);
    $routes->add('update', 'PurchaseOrder::updateData', $this->auth);
    $routes->add('deleteData', 'PurchaseOrder::deleteData', $this->auth);
    $routes->post('getsuppliers', 'PurchaseOrder::getSuppliers', $this->auth);
    $routes->post('getproducts', 'PurchaseOrder::getProducts', $this->auth);
    $routes->post('getuoms', 'PurchaseOrder::getUoms', $this->auth);
    $routes->post('getdetailsajax', 'PurchaseOrder::getDetailsAjax', $this->auth);
    $routes->post('adddetail', 'PurchaseOrder::addDetail', $this->auth);
    $routes->post('saveDetail', 'PurchaseOrder::saveDetail', $this->auth);
    $routes->get('edit/(:alphanum)', 'PurchaseOrder::forms/$1', $this->auth);
    $routes->post('updatedetail', 'PurchaseOrder::updateDetail', $this->auth);
    $routes->post('deletedetail', 'PurchaseOrder::deleteDetail', $this->auth);
    $routes->get('editDetailModal/(:num)', 'PurchaseOrder::editDetailModal/$1', $this->auth);
    $routes->get('pdf/(:any)', 'PurchaseOrder::printPdf/$1', $this->auth);
    $routes->get('pdf/(:any)/(:num)', 'PurchaseOrder::printPdf/$1/$2', $this->auth);
    $routes->post('startExport', 'PurchaseOrder::startExport', $this->auth);
    $routes->post('processExportChunk', 'PurchaseOrder::processExportChunk', $this->auth);
    $routes->get('downloadExport/(:any)', 'PurchaseOrder::downloadExport/$1', $this->auth);
});
