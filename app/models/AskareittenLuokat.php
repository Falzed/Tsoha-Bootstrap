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
