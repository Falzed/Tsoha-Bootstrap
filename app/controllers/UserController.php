<?php

/**
 * Description of UserController
 *
 * @author Oskari
 */
class UserController {

    public function login() {
        View::make('kayttaja/login.html');
    }

    public function handle_login() {
        $params = $_POST;

        $user = Kayttaja::authenticate($params['username'], $params['password']);

        if (!$user) {
            View::make('kayttaja/login.html', array('error' => 'Väärä käyttäjätunnus tai salasana!', 'username' => $params['username']));
        } else {
            $_SESSION['user'] = $user->id;

            Redirect::to('/', array('message' => 'Tervetuloa takaisin ' . $user->nimi . '!'));
        }
    }

}
