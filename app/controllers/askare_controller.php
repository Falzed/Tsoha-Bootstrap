<?php

class AskareController extends BaseController {

    public static function listaus() {
        self::check_logged_in();
        $user = self::get_user_logged_in();
        $id = $user->id;
        $askareet = Askare::kaikki($id);
        $askareittenLuokat = array();

        foreach ($askareet as $askare) {
            $askareenLuokat = array();
            $askareenLuokat[] = Luokka::kaikki($askare->kayttaja_id);
            $askareittenLuokat[] = $askareenLuokat;
        }
        View::make('askare/listaus.html', array('askareet' => $askareet, 'askareittenLuokat' => $askareittenLuokat));
    }

    public static function askare($id) {
        self::check_logged_in();
        $askare = Askare::find($id, self::get_user_logged_in()->id);
        $luokat = AskareittenLuokat::kaikki($id);
        View::make('askare/askare.html', array('askare' => $askare, 'luokat' => $luokat));
    }

    public static function muokkaus($id) {
        self::check_logged_in();
        $askare = Askare::find($id, self::get_user_logged_in()->id);
        View::make('askare/muokkaus.html', array('attributes' => $askare));
    }

    public static function tallenna() {
        self::check_logged_in();
        $params = $_POST;
        $attributes = array(
            'nimi' => $params['nimi'],
            'description' => $params['description'],
            'prioriteetti' => $params['prioriteetti']
        );
        $askare = new Askare(array(
            'nimi' => $params['nimi'],
            'description' => $params['description'],
            'prioriteetti' => $params['prioriteetti']
        ));
        Kint::dump($params);

        $luokkien_idt = $params['luokat[]'];
        $luokat = array();

        $errors = $askare->errors();
        if (count($errors) == 0) {
            foreach ($luokkien_id as $luokan_id) {
                $askareittenLuokat = new AskareittenLuokat(array('askare_id' => $askare->id,
                    'luokka_id' => $luokan_id));
                $askareittenLuokat->tallenna();
            }
            $askare->tallenna(self::get_user_logged_in()->id);
            Redirect::to('/askare/' . $askare->id, array('message' => 'Askare on lisätty muistilistaasi!'));
        } else {
            View::make('askare/add.html', array('errors' => $errors, 'attributes' => $attributes));
        }
    }

    public static function uusi() {
        self::check_logged_in();
        $kaikki_luokat = Luokka::kaikki(self::get_user_logged_in()->id);
        View::make('askare/add.html', array('kaikki_luokat' =>$kaikki_luokat));
    }

    public static function update($id) {
        self::check_logged_in();
        $params = $_POST;
        $attributes = array(
            'nimi' => $params['nimi'],
            'description' => $params['description'],
            'prioriteetti' => $params['prioriteetti'],
            'luokat' => $params['luokat']
        );
        $askare = new Askare(array($attributes));
        $errors = $askare->errors();

        if (count($errors) == 0) {
            $askare->update();
            Redirect::to('/askare/' . $askare->id, array('message' => 'Askaretta on muokattu onnistuneesti!'));
        } else {
            View::make('askare/muokkaus.html', array('errors' => $errors, 'attributes' => $attributes));
        }
    }

    public static function destroy($id) {
        self::check_logged_in();
        $askare = Askare::find($id, self::get_user_logged_in()->id);
        $askare->destroy();

        Redirect::to('/askareet', array('message' => 'Askare on poistettu onnistuneesti!'));
    }

}
