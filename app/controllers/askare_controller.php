<?php

class AskareController extends BaseController {

    public static function listaus() {
        $askareet = Askare::kaikki();
        View::make('askare/listaus.html', array('askareet' => $askareet));
    }

    public static function askare($id) {
        $askare = Askare::find($id);
        View::make('askare/askare.html', array('askare' => $askare));
    }

    public static function muokkaus($id) {
        $askare = Askare::find($id);
        View::make('askare/muokkaus.html', array('askare' => $askare));
    }

    public static function tallenna() {
        $params = $_POST;
        $askare = new Askare(array(
            'nimi' => $params['nimi'],
            'description' => $params['description'],
            'prioriteetti' => $params['prioriteetti'],
            'luokat' => $params['luokat']
        ));
        Kint::dump($params);
        $askare->tallenna();
        Redirect::to('/askare/' . $askare->id, array('message' => 'Askare on lisätty muistilistaasi!'));
    }

    public static function uusi() {

        View::make('askare/add.html');
    }

}
