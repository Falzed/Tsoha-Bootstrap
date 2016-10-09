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
$routes->post('/login', function() {
    UserController::handle_login();
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
//$routes->post('/askare/:id', function() {
//    AskareController::tallenna();
//});
$routes->get('/askare/add', function() {
    AskareController::uusi();
});
$routes->get('/askare/:id', function($id) {
    AskareController::askare($id);
});

$routes->post('/logout', function(){
  UserController::logout();
});

$routes->get('/luokat', function() {
    LuokkaController::listaus();
});
$routes->get('/luokka/add', function(){
  LuokkaController::uusi();
});
$routes->post('/luokka', function(){
  LuokkaController::tallenna();
});
$routes->get('/luokka/:id', function(){
  LuokkaController::luokka($id);
});
$routes->get('/luokka/:id/edit', function(){
  LuokkaController::muokkaus($id);
});
$routes->post('/luokka/:id/edit', function($id) {
    LuokkaController::update($id);
});
$routes->post('/luokka/:id/delete', function($id) {
    LuokkaController::destroy($id);
});
$routes->post('/luokka', function() {
    LuokkaController::tallenna();
});
