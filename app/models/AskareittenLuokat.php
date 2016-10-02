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

    public function _construct() {
        parent::construct();
    }

}
