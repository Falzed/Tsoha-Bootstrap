<?php

$routes->get('/', function() {
    HelloWorldController::index();
});

$routes->get('/hiekkalaatikko', function() {
    HelloWorldController::sandbox();
});

$routes->get('/askareet', function() {
    AskareController::listaus();
});
$routes->get('/login', function() {
    UserController::login();
});
$routes->post('/handle_login', function() {
    UserController::login($id);
});
$routes->get('/register', function() {
    HelloWorldController::rekisteroityminen();
});
$routes->get('/askare/:id/edit', function($id) {
    AskareController::muokkaus($id);
});
$routes->post('/askare/:id/edit', function($id) {
    AskareController::update($id);
});
$routes->post('/askare/:id/delete', function($id) {
    AskareController::destroy($id);
});
$routes->post('/askare', function() {
    AskareController::tallenna();
});
$routes->post('/askare/:id', function() {
    AskareController::tallenna();
});
$routes->get('/askare/add', function() {
    AskareController::uusi();
});
$routes->get('/askare/:id', function($id) {
    AskareController::askare($id);
});
