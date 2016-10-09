<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of luokkaController
 *
 * @author Oskari
 */
class LuokkaController extends BaseController{

    public static function uusi() {
        self::check_logged_in();
        $luokat = Luokka::findKaikkiKayttajan(get_user_logged_in());
        View::make('luokka/add.html', array('luokat' => $luokat));
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
