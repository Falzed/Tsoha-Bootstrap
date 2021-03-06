<?php

/**
 * Hoitaa askareitten näyttämisen, lisäämisen, muokkaamisen ja poistamisen.
 *
 * @author Oskari Kulmala
 */
class AskareController extends BaseController {

    public static function listaus() {
        self::check_logged_in();
        $user = self::get_user_logged_in();
        $id = $user->id;
        $params = $_GET;

        $parse = self::parse_lista_params($params);
        $options = $parse['options'];
        $url_params = $parse['url_params'];

        $askareet = Askare::kaikki($id, $options);

        foreach ($askareet as $askare) {
            $askare->luokat = AskareittenLuokat::kaikki($askare->id);
        }

        $askare_count = Askare::count($user->id);
        $pages = ceil($askare_count / $options['page_size']);
//        Kint::dump($askareittenLuokat);
        View::make('askare/listaus.html', array('askareet' => $askareet,
            'options' => $options, 'pages' => $pages, 'url_params' => $url_params));
    }

    private static function parse_lista_params($params) {
        $options = array();
        $url_params = '?';
        if (isset($params['haku'])) {
            $options['haku'] = $params['haku'];
            $url_params .= 'haku=' . $params['haku'];
        }
        if (array_key_exists('sort', $params)) {
            $options['sort'] = $params['sort'];
            $url_params .= '&sort=' . $params['sort'];
        }
        if (array_key_exists('asc_desc', $params)) {
            $options['asc_desc'] = $params['asc_desc'];
            $url_params .= '&asc_desc=' . $params['asc_desc'];
        }
        if (isset($params['page'])) {
            $options['page'] = $params['page'];
        } else {
            $options['page'] = 1;
        }
        if (isset($params['page_size'])) {
            $options['page_size'] = $params['page_size'];
        } else {
            $options['page_size'] = 10;
        }
        if (array_key_exists('sort', $params)) {
            $options['sort'] = $params['sort'];
        }
        if (array_key_exists('asc_desc', $params)) {
            $options['asc_desc'] = $params['asc_desc'];
        }
        return array('options' => $options, 'url_params' => $url_params);
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
        View::make('askare/muokkaus.html', array('askare' => $askare, 'luokatJoihinEiKuulu' => $luokatJoihinEiKuulu, 'luokat' => $luokat));
    }

    public static function tallenna() {
        self::check_logged_in();
        $params = $_POST;
        $attributes = array(
            'nimi' => $params['nimi'],
            'description' => $params['description'],
            'prioriteetti' => $params['prioriteetti']
        );
        $askare = new Askare($params);
        Kint::dump($params);

        $luokkien_idt = null;
        if (array_key_exists('luokat', $params)) {
            $luokkien_idt = $params['luokat'];
        }

        $errors = $askare->errors();
        if (count($errors) == 0) {
            $askare->tallenna(self::get_user_logged_in()->id);
            self::tallenna_luokat($luokkien_idt, $askare);
            Redirect::to('/askare/' . $askare->id, array('message' => 'Askare on lisätty muistilistaasi!'));
        } else {
            View::make('askare/add.html', array('errors' => $errors, 'attributes' => $attributes));
        }
    }

    public static function tallenna_luokat($luokkien_idt, $askare) {
        if (!is_null($luokkien_idt)) {
            foreach ($luokkien_idt as $luokan_id) {
                $askareittenLuokat = new AskareittenLuokat(array('askare_id' => $askare->id, 'luokka_id' => $luokan_id));
                $askareittenLuokat->tallenna(self::get_user_logged_in()->id);
            }
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
        Kint::dump($params);
        $attributes = array(
            'nimi' => $params['nimi'],
            'description' => $params['description'],
            'prioriteetti' => $params['prioriteetti'],
            'id' => $id
        );
        $askare = new Askare($attributes);
        $errors = $askare->errors();

//        $poistettavat = $params['poistettava'];
//        foreach ($poistettavat as $poistettava) {
//            $askareittenLuokat = AskareittenLuokat::find($askare->id, $poistettava);
//            $askareittenLuokat->destroy();
//        }
//        $lisattavat = $params['uudet_luokat'];
//        foreach ($lisattavat as $lisattava) {
//            $askareittenLuokat = new AskareittenLuokat(array('askare_id' => $askare->id, 'luokka_id' => $lisattava));
//            $askareittenLuokat->tallenna(self::get_user_logged_in()->id);
//        }

        self::lisaa_ja_poista_luokat($params, $askare);
        if (count($errors) == 0) {
            $askare->update();
            Redirect::to('/askare/' . $askare->id, array('message' => 'Askaretta on muokattu onnistuneesti!'));
        } else {
            View::make('askare/muokkaus.html', array('errors' => $errors, 'attributes' => $attributes));
        }
    }
    
    public static function lisaa_ja_poista_luokat($params, $askare) {
        if (array_key_exists('poistettava', $params)) {
            $poistettavat = $params['poistettava'];
            foreach ($poistettavat as $poistettava) {
                $askareittenLuokat = AskareittenLuokat::find($askare->id, $poistettava);
                Kint::dump($poistettava);
                Kint::dump($askare);
                Kint::dump($askareittenLuokat);
                $askareittenLuokat->destroy();
            }
        }
        if (array_key_exists('uudet_luokat', $params)) {
            $lisattavat = $params['uudet_luokat'];
            foreach ($lisattavat as $lisattava) {
                $askareittenLuokat = new AskareittenLuokat(array('askare_id' => $askare->id, 'luokka_id' => $lisattava));
                $askareittenLuokat->tallenna(self::get_user_logged_in()->id);
            }
        }
    }

    public static function destroy($id) {
        self::check_logged_in();
        $askare = Askare::find($id, self::get_user_logged_in()->id);
        $askare->destroy();

        Redirect::to('/askareet', array('message' => 'Askare on poistettu onnistuneesti!'));
    }

}
