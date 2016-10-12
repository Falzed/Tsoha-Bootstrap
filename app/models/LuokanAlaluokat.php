<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of LuokanAlaluokat
 *
 * @author Oskari
 */
class LuokanAlaluokat extends BaseModel {
    public $ylaluokan_id, $alaluokan_id;
    
    public function __construct($attributes) {
        parent::__construct($attributes);
    }
    
    public static function kaikkiAlaluokat($ylaluokan_id) {
        $query = DB::connection()->prepare('SELECT * FROM Luokka INNER JOIN LuokanAlaluokat ON alaluokan_id = id WHERE ylaluokan_id = :ylaluokan_id');
        $query->execute(array('ylaluokan_id' =>  $ylaluokan_id));
        $rows = $query->fetchAll();
        $alaluokat = array();

        foreach ($rows as $row) {

            $alaluokat[] = new Luokka(array(
                'id' => $row['alaluokan_id'],
                'nimi' => $row['nimi'],
            ));
        }

        return $alaluokat;
    }
    
    public static function findYlaluokka($alaluokan_id) {
        $query = DB::connection()->prepare('SELECT * FROM Luokka INNER JOIN LuokanAlaluokat ON alaluokan_id = id WHERE alaluokan_id = :alaluokan_id LIMIT 1');
        $query->execute(array('alaluokan_id' => $alaluokan_id));
        $row = $query->fetch();
        if($row) {
            $ylaluokka = new Luokka(array(
                'id' => $row['ylaluokan_id'],
                'nimi' => $row['nimi']
            ));
        }
        return $ylaluokka;
    }
    public function tallenna() {
        $query = DB::connection()->prepare('INSERT INTO LuokanAlaluokat (alaluokan_id, ylaluokan_id) VALUES (:alaluokan_id, :ylaluokan_id)');
        $query->execute(array('alaluokan_id' => $this->alaluokan_id, 'ylaluokan_id' => $this->ylaluokan_id));
        $query->fetch();
    }
}
