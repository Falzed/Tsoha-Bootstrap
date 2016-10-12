<?php

/**
 * Hoitaa luokkien näyttämisen, lisäämisen, muokkauksen ja poiston.
 *
 * @author Oskari Kulmala
 */
class LuokkaController extends BaseController {

    public static function listaus() {
        self::check_logged_in();
        $user = self::get_user_logged_in();
        $id = $user->id;
        $luokat = Luokka::kaikki($id);
        View::make('luokka/lista.html', array('luokat' => $luokat));
    }

    public static function uusi() {
        self::check_logged_in();
        $kaikki_luokat = Luokka::kaikki(self::get_user_logged_in()->id);
        View::make('luokka/add.html', array('luokat' => $kaikki_luokat));
    }

    public static function tallenna() {
        self::check_logged_in();
        $kayttajan_id = self::get_user_logged_in()->id;
        $params = $_POST;
        $attributes = array(
            'nimi' => $params['nimi'],
            'kayttajan_id' => $kayttajan_id
        );
        $luokka = new Luokka($params);
        Kint::dump($params);
        if (isset($params['ylaluokka'])) {
            $ylaluokan_id = $params['ylaluokka'];
        }

        $errors = $luokka->errors();
        if (count($errors) == 0) {
            $luokka->tallenna(self::get_user_logged_in()->id);
            self::tallenna_aliluokka($ylaluokan_id, $luokka->id);
            Redirect::to('/luokka/' . $luokka->id, array('message' => 'Luokka on lisätty'));
        } else {
            View::make('luokka/add.html', array('errors' => $errors, 'attributes' => $attributes));
        }
    }

    public static function tallenna_aliluokka($ylaluokan_id, $aliluokan_id) {
        $luokan_alaluokat = new LuokanAlaluokat(array('ylaluokan_id' => $ylaluokan_id, 'alaluokan_id' => $aliluokan_id));
        $luokan_alaluokat->tallenna();
    }

    public static function luokka($id) {
        self::check_logged_in();
        $luokka = Luokka::find($id, self::get_user_logged_in()->id);
        $alaluokat = LuokanAlaluokat::kaikkiAlaluokat($id);
        View::make('luokka/luokka.html', array('luokka' => $luokka, 'alaluokat' => $alaluokat));
    }

    public static function update($id) {
        self::check_logged_in();
        $params = $_POST;
        $attributes = array(
            'nimi' => $params['nimi']
        );
        $luokka = new Luokka(array($attributes));
        $errors = $luokka->errors();

        if (count($errors) == 0) {
            $luokka->update();
            Redirect::to('/luokka/' . $luokka->id, array('message' => 'Luokkaa on muokattu onnistuneesti!'));
        } else {
            View::make('luokka/muokkaus.html', array('errors' => $errors, 'attributes' => $attributes));
        }
    }

    public static function muokkaus($id) {
        self::check_logged_in();
        $luokka = Luokka::find($id, self::get_user_logged_in()->id);
        View::make('luokka/muokkaus.html', array('attributes' => $luokka));
    }

    public static function destroy($id) {
        self::check_logged_in();
        $luokka = Luokka::find($id, self::get_user_logged_in()->id);
        $luokka->destroy();

        Redirect::to('/luokka', array('message' => 'Askare on poistettu onnistuneesti!'));
    }

}
