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

    public $askare_id, $luokka_id, $luokka_nimi;

    public function __construct() {
        parent::__construct();
    }
    
    public static function kaikki($askare_id) {
        $query = DB::connection()->prepare('SELECT * FROM AskareittenLuokat WHERE askare_id = :askare_id');
        $query->execute(array('askare_id' =>  $askare_id));
        $rows = $query->fetchAll();
        $luokat = array();

        foreach ($rows as $row) {

            $luokat[] = new Luokka(array(
                'id' => $row['luokka_id'],
                'nimi' => $row['luokka_nimi'],
            ));
        }

        return $luokat;
    }
    public function tallenna() {
//        $query = DB::connection()->prepare('INSERT INTO AskareittenLuokat (askare_id, luokka_id, luokka_nimi) VALUES (:askare_id, :luokka_id, :luokka_nimi RETURNING id');
        $query = DB::connection()->prepare('INSERT INTO AskareittenLuokat (askare_id, luokka_id, luokka_nimi) VALUES (:askare_id, :luokka_id, :luokka_nimi');
        $query->execute(array('askare_id' => $this->askare_id, 'luokka_id' => $this->luokka_id, 'luokka_nimi' => $this->luokka_nimi));
        $query->fetch();
//        $row = $query->fetch();
//        $this->id = $row['id'];
    }

}
