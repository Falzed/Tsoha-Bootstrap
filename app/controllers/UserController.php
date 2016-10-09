<?php

/**
 * Hoitaa sisäänkirjautumisen.
 *
 * @author Oskari
 */
class UserController {

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

}
