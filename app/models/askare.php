<?php

class Askare extends BaseModel {

    public $id, $kayttaja_id, $nimi, $description, $prioriteetti, $added, $luokat_string;

    public function __construct($attributes) {
        parent::__construct($attributes);
        $this->validators = array('validate_name', 'validate_priority');
    }

    private static function classesToString($askareId) {
        $query = DB::connection()->prepare('SELECT * FROM AskareittenLuokat WHERE id = :askareId');
        $query->execute();
        $rows = $query->fetchAll();
        $luokat_string = '';
        if (count($rows) > 1) {
            for ($i = 0; $i < count($rows) - 1; $i++) {
                $luokat_string . $row['luokka_nimi'] . ', ';
            }
            $luokat_string . $rows[count($rows) - 1]['luokka_nimi'];
        }
        if (count($rows) == 1) {
            $luokat_string . $rows[count($rows) - 1]['luokka_nimi'];
        }

        return $luokat_string;
    }

    private static function stringToClasses($string) {
        $luokat_array = array();
        $uusiLuokka = '';
        for ($i = 0; $i < strlen($string); $i++) {
            if (substr($string, (-1) * strlen($string) + $i, 1) != ',' && substr($string, (-1) * strlen($string) + $i, 1) != ' ') {
                $uusiLuokka . substr($string, (-1) * strlen($string) + $i, 1);
            } else if (substr($string, (-1) * strlen($string) + $i, 1) != ',') {
                $luokat_array[] = $uusiLuokka;
                $uusiLuokka = '';
            }
        }
        return $luokat_array;
    }

    public static function kaikki($id) {
        $query = DB::connection()->prepare('SELECT * FROM Askare WHERE kayttaja_id = :id');
        $query->execute();
        $rows = $query->fetchAll();
        $askareet = array();

        foreach ($rows as $row) {

            $askareet[] = new Askare(array(
                'id' => $row['id'],
                'kayttaja_id' => $row['kayttaja_id'],
                'nimi' => $row['nimi'],
                'description' => $row['description'],
                'prioriteetti' => $row['prioriteetti'],
                'added' => $row['added'],
                'luokat_string' => Askare::classesToString($row['luokat'])
            ));
        }

        return $askareet;
    }

    public static function find($id, $kayttaja_id) {
        $query = DB::connection()->prepare('SELECT * FROM Askare WHERE id = :id AND kayttaja_id = :kayttaja_id LIMIT 1');
        $query->execute(array('id' => $id));
        $row = $query->fetch();

        if ($row) {
            $askare = new Askare(array(
                'id' => $row['id'],
                'kayttaja_id' => $row['kayttaja_id'],
                'nimi' => $row['nimi'],
                'description' => $row['description'],
                'prioriteetti' => $row['prioriteetti'],
                'added' => $row['added'],
                'luokat_string' => Askare::classesToString($row['luokat'])
            ));
        }
        return $askare;
    }

    public function tallenna() {

        $query = DB::connection()->prepare('INSERT INTO Askare (nimi, description, prioriteetti, added) VALUES (:nimi, :description, :prioriteetti, NOW()) RETURNING id');
        $luokat_temp = Askare::stringToClasses($this->luokat_string);
        $query->execute(array('nimi' => $this->nimi, 'description' => $this->description, 'prioriteetti' => $this->prioriteetti));
        $row = $query->fetch();
        $this->id = $row['id'];


        Kint::trace();
        Kint::dump($row);
    }
    
    public function update() {
        $query = DB::connection()->prepare('UPDATE Askare (nimi, description, prioriteetti, added, kayttaja_id) VALUES (:nimi, :description, :prioriteetti, :kayttaja_id, NOW()) RETURNING id');
        $luokat_temp = Askare::stringToClasses($this->luokat_string);
        $query->execute(array('nimi' => $this->nimi, 'description' => $this->description, 'prioriteetti' => $this->prioriteetti));
        $row = $query->fetch();
        $this->id = $row['id'];


        Kint::trace();
        Kint::dump($row);
    }
    
    public function destroy() {
        $query = DB::connection()->prepare('DELETE Askare (nimi, description, prioriteetti, added) VALUES (:nimi, :description, :prioriteetti, NOW()) RETURNING id');
        $luokat_temp = Askare::stringToClasses($this->luokat_string);
        $query->execute(array('nimi' => $this->nimi, 'description' => $this->description, 'prioriteetti' => $this->prioriteetti));
        $row = $query->fetch();
        $this->id = $row['id'];


        Kint::trace();
        Kint::dump($row);
    }

    public function validate_name() {
        return validate_string_length($this->nimi, 1);
    }

    public function validate_priority() {
        return validatenumeric($this->prioriteetti);
    }

    public function validate_description() {
//        kuvaus voi olla tyhjä
        return array();
    }

    public function validate_added() {
        //ei tarkista karkauspäiviä
        
        $regex = '[0-9]{4}-'
                . '((01 | 03 | 05 | 07 | 08 | 10 | 12) - ([0-2][0-9] | 3[0-1])'
                . '| (04 | 06 | 09 | 11) - ([0-2][0-9] | 30)'
                . '| 02-([0-1][0-9] | 2[0-9]))';
        
        return validate_regex($regex, $this->added);
    }
    
        public function validate_luokat() {
//        askareella ei välttämättä tarvitse olla yhtään luokkaa
        return array();
    }

}
