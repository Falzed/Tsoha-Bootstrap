<?php
/**
 * Malli käyttäjälle.
 *
 * @author Oskari Kulmala
 */

class Kayttaja extends BaseModel {

    public $id, $nimi, $email, $password, $password_confirm;

    public function __construct($attributes) {
        parent::__construct($attributes);
        $this->validators = array('validate_name', 'validate_password');
    }

    public static function kaikki() {
        $query = DB::connection()->prepare('SELECT * FROM Kayttaja');
        $query->execute();
        $rows = $query->fetchAll();
        $kayttajat = array();

        foreach ($rows as $row) {
            $kayttajat[] = new Kayttaja(array(
                'id' => $row['id'],
                'nimi' => $row['nimi'],
                'email' => $row['email'],
                'password' => $row['password'],
            ));
        }

        return $kayttajat;
    }

    public static function find($id) {
        $query = DB::connection()->prepare('SELECT * FROM Kayttaja WHERE id = :id LIMIT 1');
        $query->execute(array('id' => $id));
        $row = $query->fetch();

        if ($row) {
            $kayttaja = new Kayttaja(array(
                'id' => $row['id'],
                'nimi' => $row['nimi'],
                'email' => $row['email'],
                'password' => $row['password']
            ));
        }
        return $kayttaja;
    }

    public static function authenticate($username, $password) {
        $query = DB::connection()->prepare('SELECT * FROM Kayttaja WHERE nimi = :username AND password = :password LIMIT 1');
        $query->execute(array('username' => $username, 'password' => $password));
        $row = $query->fetch();
        if ($row) {
            return new Kayttaja(array(
                'id' => $row['id'],
                'nimi' => $row['nimi'],
                'email' => $row['email'],
                'password' => $row['password']
            ));
        } else {
            return null;
        }
    }
    
    public function tallenna() {
        $query = DB::connection()->prepare('INSERT INTO Kayttaja (nimi, email, password) VALUES (:nimi, :email, :password) RETURNING id');
        $query->execute(array('nimi' => $this->nimi, 'email' => $this->email, 'password' => $this->password));
        $row = $query->fetch();
        $this->id = $row['id'];
        Kint::dump($row);
    }
    
    public function validate_name() {
        return self::validate_string_length($this->nimi, 1);
    }
    
    public function validate_password() {
        $errors = array();
        if(strcmp($this->password, $this->password_confirm)!=0) {
            $errors[] = 'Salasanat olivat eri';
            return $errors;
        }
        return self::validate_string_length($this->password, 1);
    }
    
}
