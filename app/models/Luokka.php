<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Luokka
 *
 * @author Oskari
 */
class Luokka extends BaseModel {

    public $id, $nimi, $kayttajan_id;

    public function __construct($attributes) {
        parent::__construct($attributes);
        $this->validators = array('validate_name');
    }

    public function tallenna() {
        $query = DB::connection()->prepare('INSERT INTO Luokka (nimi, kayttajan_id) VALUES (:nimi, :kayttajan_id) RETURNING id');
        $query->execute(array('nimi' => $this->nimi, 'kayttajan_id' => $this->kayttajan_id));
        $row = $query->fetch();
        $this->id = $row['id'];
    }

    public static function find($nimi) {
        $query = DB::connection()->prepare('SELECT * FROM Luokka WHERE nimi = :nimi LIMIT 1');
        $query->execute(array('nimi' => $nimi));
        $row = $query->fetch();
        if ($row) {
            $luokka = new Luokka(array(
                'id' => $row['id'],
                'nimi' => $row['nimi'],
                'kayttajan_id' => $row['kayttajan_id']
            ));
        }
        return $luokka;
    }

    public static function findKaikkiKayttajan($kayttajan_id) {
        $query = DB::connection()->prepare('SELECT * FROM Luokka WHERE kayttajan_id = :kayttajan_id');
        $query->execute(array('kayttajan_id' => $kayttajan_id));
        $rows = $query->fetchAll();
        $luokat = array();
        foreach ($rows as $row) {
            $luokat[] = new Askare(array(
                'id' => $row['id'],
                'nimi' => $row['nimi'],
                'kayttajan_id' => $row['kayttajan_id']
            ));
        }

        return $luokat;
    }

    public function validate_name() {
        return self::validate_string_length($this->nimi, 1);
    }

}
