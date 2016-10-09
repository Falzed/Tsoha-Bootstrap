<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AskareittenLuokat
 *
 * @author oskari
 */
class AskareittenLuokat extends BaseModel {

    public $askare_id, $luokka_id;

    public function __construct($attributes) {
        parent::__construct($attributes);
        $this->validators = array('validate_ids');
    }
    
    public static function kaikki($askare_id) {
        $query = DB::connection()->prepare('SELECT * FROM AskareittenLuokat INNER JOIN Luokka ON luokka_id = id WHERE askare_id = :askare_id');
        $query->execute(array('askare_id' =>  $askare_id));
        $rows = $query->fetchAll();
        $luokat = array();

        foreach ($rows as $row) {

            $luokat[] = new Luokka(array(
                'id' => $row['luokka_id'],
                'nimi' => $row['nimi'],
            ));
        }

        return $luokat;
    }
    public function tallenna($kayttaja_id) {
        $query = DB::connection()->prepare('INSERT INTO AskareittenLuokat (askare_id, luokka_id, kayttaja_id) VALUES (:askare_id, :luokka_id, :kayttaja_id)');
        $query->execute(array('askare_id' => $this->askare_id, 'luokka_id' => $this->luokka_id, 'kayttaja_id' => $kayttaja_id));
        $row = $query->fetch();
        Kint::dump($row);
    }
    
    public function destroy() {
        $query = DB::connection()->prepare('DELETE FROM AskareittenLuokat WHERE askare_id = :askare_id AND luokka_id = :luokka_id');
        $query->execute(array('askare_id' => $this->askare_id, 'luokka_id' => $this->luokka_id));
        $row = $query->fetch();
        Kint::trace();
        Kint::dump($row);
    }
    
    public static function find($askare_id, $luokka_id) {
        $query = DB::connection()->prepare('SELECT * FROM AskareittenLuokat WHERE askare_id = :askare_id AND luokka_id = :luokka_id LIMIT 1');
        $query->execute(array('askare_id' => $askare_id, 'luokka_id' => $luokka_id));
        $row = $query->fetch();
        $askareittenLuokka = null;
        if ($row) {
            $askareittenLuokka = new AskareittenLuokat(array(
                'askare_id' => $row['askare_id'],
                'luokka_id' => $row['luokka_id']
            ));
        }
        Kint::dump($row);
        Kint::dump($askareittenLuokka);
        return $askareittenLuokka;
    }
    public function validate_ids() {
        if(!parent::validate_numeric($this->askare_id)) {
            return false;
        }
        if(!parent::validate_numeric($this->luokka_id)) {
            return false;
        }
        return true;
    }
}
