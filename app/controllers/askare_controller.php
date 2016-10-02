<?php

class AskareController extends BaseController {

    public static function listaus() {
        $user = self::get_user_logged_in();
        $id = $user->id;
        $askareet = Askare::kaikki($id);
        View::make('askare/listaus.html', array('askareet' => $askareet));
    }

    public static function askare($id) {
        $askare = Askare::find($id, self::get_user_logged_in()->id);
        View::make('askare/askare.html', array('askare' => $askare));
    }

    public static function muokkaus($id) {
        $askare = Askare::find($id, self::get_user_logged_in()->id);
        View::make('askare/muokkaus.html', array('attributes' => $askare));
    }

    public static function tallenna() {
        $params = $_POST;
        $attributes = array(
            'nimi' => $params['nimi'],
            'description' => $params['description'],
            'prioriteetti' => $params['prioriteetti'],
            'luokat_string' => $params['luokat_string']
        );
        $askare = new Askare(array(
            'nimi' => $params['nimi'],
            'description' => $params['description'],
            'prioriteetti' => $params['prioriteetti'],
            'luokat_string' => $params['luokat_string']
        ));
        Kint::dump($params);

        $errors = $askare->errors();
        if (count($errors) == 0) {
            $askare->tallenna(self::get_user_logged_in()->id);
            Redirect::to('/askare/' . $askare->id, array('message' => 'Askare on lisätty muistilistaasi!'));
        } else {
            View::make('askare/add.html', array('errors' => $errors, 'attributes' => $attributes));
        }
    }

    public static function uusi() {

        View::make('askare/add.html');
    }
    
    public static function update($id) {
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
        $askare = Askare::find($id, self::get_user_logged_in()->id);
        $askare->destroy();
        
        Redirect::to('/askareet', array('message' => 'Askare on poistettu onnistuneesti!'));
    }

}
