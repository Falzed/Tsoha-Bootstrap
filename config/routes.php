<?php

  $routes->get('/', function() {
    HelloWorldController::index();
  });

  $routes->get('/hiekkalaatikko', function() {
    HelloWorldController::sandbox();
  });
  
  $routes->get('/askareet', function() {
    HelloWorldController::listaus();
  });
  
  $routes->get('/askareet/1', function() {
    HelloWorldController::askare();
  });
  $routes->get('/login', function() {
    HelloWorldController::kirjautuminen();
  });
  $routes->get('/register', function() {
    HelloWorldController::rekisteroityminen();
  });
  $routes->get('/askareet/1/modify', function() {
    HelloWorldController::muokkaus();
  });
