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
        $kaikki_luokat = Luokka::kaikki(self::get_user_logged_in()->id);
        $luokat = AskareittenLuokat::kaikki($id);
        $luokatJoihinEiKuulu = array();
        foreach ($kaikki_luokat as $luokka) {
            if (!in_array($luokka, $luokat)) {
                $luokatJoihinEiKuulu[] = $luokka;
            }
        }
        View::make('askare/muokkaus.html', array('attributes' => $askare, '$luokatJoihinEiKuulu' => $luokatJoihinEiKuulu, 'luokat' => $luokat));
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

//        $luokkien_idt_apu = $params['luokat'];
        $luokkien_idt = $params['luokat'];

        $errors = $askare->errors();
        if (count($errors) == 0) {
            $askare->tallenna(self::get_user_logged_in()->id);
            foreach ($luokkien_idt as $luokan_id) {
                $askareittenLuokat = new AskareittenLuokat(array('askare_id' => $askare->id, 'luokka_id' => $luokan_id));
                $askareittenLuokat->tallenna(self::get_user_logged_in()->id);
            }
            Redirect::to('/askare/' . $askare->id, array('message' => 'Askare on lisätty muistilistaasi!'));
        } else {
            View::make('askare/add.html', array('errors' => $errors, 'attributes' => $attributes));
        }
    }

    public static function uusi() {
        self::check_logged_in();
        $kaikki_luokat = Luokka::kaikki(self::get_user_logged_in()->id);
        View::make('askare/add.html', array('kaikki_luokat' => $kaikki_luokat));
    }

    public static function update($id) {
        self::check_logged_in();
        $params = $_POST;
        $attributes = array(
            'nimi' => $params['nimi'],
            'description' => $params['description'],
            'prioriteetti' => $params['prioriteetti']
        );
        $askare = new Askare(array($attributes));
        $errors = $askare->errors();

        $poistettavat = $params['poistettava'];
        foreach ($poistettavat as $poistettava) {
            $askareittenLuokat = AskareittenLuokat::find($askare->id, $poistettava);
            $askareittenLuokat->destroy();
        }
        
        $lisattavat = $params['uudet_luokat'];
        
        foreach ($lisattavat as $lisattava) {
            $askareittenLuokat = new AskareittenLuokat(array('askare_id' => $askare->id, 'luokka_id'=>$lisattava));
            $askareittenLuokat->tallenna(self::get_user_logged_in()->id);
        }

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
