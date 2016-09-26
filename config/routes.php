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
    HelloWorldController::kirjautuminen();
  });
  $routes->get('/register', function() {
    HelloWorldController::rekisteroityminen();
  });
  $routes->get('/askareet/:id/modify', function($id) {
      AskareController::muokkaus($id);
  });
  $routes->post('/askare', function() {
      AskareController::tallenna();
  });
  $routes->get('/askare/add', function() {
      AskareController::tallenna();
  });
  $routes->get('/askareet/:id', function($id) {
    AskareController::askare($id);
  });
