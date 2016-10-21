<?php

/**
 * Hoitaa sisään- ja uloskirjautumisen.
 *
 * @author Oskari
 */
class UserController extends BaseController {

    public function login() {
        View::make('kayttaja/kirjautuminen.html');
    }

    public function handle_login() {
        $params = $_POST;

        $user = Kayttaja::authenticate($params['username'], $params['password']);

        if (!$user) {
            View::make('kayttaja/kirjautuminen.html', array('error' => 'Väärä käyttäjätunnus tai salasana!', 'username' => $params['username']));
        } else {
            $_SESSION['user'] = $user->id;

            Redirect::to('/askareet', array('message' => 'Tervetuloa takaisin ' . $user->nimi . '!'));
        }
    }
    public static function logout() {
        parent::logout();
    }
    
    public static function rekisteroityminen(){
      View::make('kayttaja/rekisteroityminen.html');
    }
    
    public static function tallenna() {
        $params = $_POST;
        $attributes = array(
            'nimi' => $params['nimi'],
            'email' => $params['email'],
            'password' => $params['password'],
            'password_confirm' => $params['password_confirm']
        );
        $kayttaja = new Kayttaja($params);
        Kint::dump($params);

        $errors = $kayttaja->errors();
        if (count($errors) == 0) {
            $kayttaja->tallenna();
            Redirect::to('/askareet/');
        } else {
            View::make('kayttaja/rekisteroityminen.html', array('errors' => $errors, 'attributes' => $attributes));
        }
    }
}
