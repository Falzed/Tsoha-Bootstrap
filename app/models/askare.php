<?php

class Askare extends BaseModel {

    public $id, $kayttaja_id, $nimi, $description, $prioriteetti, $added, $luokat_string;

    public function __construct($attributes) {
        parent::__construct($attributes);
        $this->validators = array('validate_name', 'validate_priority');
    }

    private static function classesToString($askare_id, $kayttaja_id) {
        $luokat = AskareittenLuokat::kaikki($askare_id);
        $luokat_string = '';

        foreach ($luokat as $luokka) {
            $luokat_string = $luokat_string . $luokka->nimi;
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

    public static function kaikki($kayttaja_id) {
        $query = DB::connection()->prepare('SELECT * FROM Askare WHERE kayttaja_id = :kayttaja_id');
        $query->execute(array('kayttaja_id' => $kayttaja_id));
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
//                'luokat_string' => $row['luokat']
                'luokat_string' => Askare::classesToString($row['luokat'], $row['kayttaja_id'])
            ));
        }

        return $askareet;
    }

    public static function find($id, $kayttaja_id) {
        $query = DB::connection()->prepare('SELECT * FROM Askare WHERE id = :id AND kayttaja_id = :kayttaja_id LIMIT 1');
        $query->execute(array('id' => $id, 'kayttaja_id' => $kayttaja_id));
        $row = $query->fetch();
//        $askare_id = $id;
//        $query_luokat = DB::connection()->prepare('SELECT * FROM AskareittenLuokat WHERE askare_id = :askare_id AND kayttaja_id = :kayttaja_id');
//        $query_luokat->execute(array('askare_id'=>$askare_id, 'kayttaja_id' =>$kayttaja_id));
//       $row_luokat = $query_luokat->fetch();
        $askare = null;
        if ($row) {
            $askare = new Askare(array(
                'id' => $row['id'],
                'kayttaja_id' => $row['kayttaja_id'],
                'nimi' => $row['nimi'],
                'description' => $row['description'],
                'prioriteetti' => $row['prioriteetti'],
                'added' => $row['added'],
//                'luokat_string' => $row['luokat'])
                'luokat_string' =>Askare::classesToString($row['id'], $row['kayttaja_id']))
            );
        }
        return $askare;
    }

    public function tallenna($kayttaja_id) {
        $query = DB::connection()->prepare('INSERT INTO Askare (nimi, description, prioriteetti, added, kayttaja_id) VALUES (:nimi, :description, :prioriteetti, NOW(), :kayttaja_id) RETURNING id');
        $luokat_temp = Askare::stringToClasses($this->luokat_string);
        $query->execute(array('nimi' => $this->nimi, 'description' => $this->description, 'prioriteetti' => $this->prioriteetti, 'kayttaja_id' => $kayttaja_id));
        $row = $query->fetch();
        $this->id = $row['id'];

        foreach ($luokat_temp as $luokka) {
            if (!Luokka::find($luokka->nimi)) {
                $luokka->tallenna();
            }
            $askareitten_luokat = new AskareittenLuokat(array(
                'askare_id' => $this->id,
                'luokka_id' => $luokka->id,
                'luokka_nimi' => $luokka->nimi
            ));
            $askareitten_luokat->tallenna();
        }

        $query_luokka = Kint::trace();
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
        $askare_id = $this->id;
        $query_luokat = DB::connection()->prepare('DELETE FROM AskareittenLuokat WHERE askare_id = :askare_id');
        $query_luokat->execute(array('askare_id' => $askare_id));
        $query = DB::connection()->prepare('DELETE FROM Askare WHERE id=:id RETURNING id');
        $luokat_temp = Askare::stringToClasses($this->luokat_string);
        $query->execute(array('id' => $this->id));
        $row = $query->fetch();
        $this->id = $row['id'];


        Kint::trace();
        Kint::dump($row);
    }

    public function validate_name() {
        return self::validate_string_length($this->nimi, 1);
    }

    public function validate_priority() {
        return self::validate_numeric($this->prioriteetti);
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

        return self::validate_regex($regex, $this->added);
    }

    public function validate_luokat() {
//        askareella ei välttämättä tarvitse olla yhtään luokkaa
        return array();
    }

}
