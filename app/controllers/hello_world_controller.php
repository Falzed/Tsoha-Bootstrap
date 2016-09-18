<?php

  class HelloWorldController extends BaseController{

    public static function index(){
      // make-metodi renderöi app/views-kansiossa sijaitsevia tiedostoja
//   	  View::make('home.html');
        echo 'Tämä on etusivusi!';
    }

    public static function sandbox(){
      // Testaa koodiasi täällä
      View::make('helloworld.html');
    }
    
    public static function kirjautuminen(){
      View::make('suunnitelmat/kirjautuminen.html');
    }
    public static function rekisteroityminen(){
      View::make('suunnitelmat/rekisteroityminen.html');
    }
    public static function muokkaus(){
      View::make('suunnitelmat/muokkaus.html');
    }
    public static function listaus(){
      View::make('suunnitelmat/listaus.html');
    }
    public static function askare(){
      View::make('suunnitelmat/askare.html');
    }
  }
