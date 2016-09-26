<?php

class Askare extends BaseModel {

    public $id, $kayttaja_id, $nimi, $description, $prioriteetti, $luokat, $added;

    public function __construct($attributes) {
        parent::__construct($attributes);
    }

    private static function parseClasses($luokat_string) {
        $uusiLuokka = '';
        $luokat_temp = array();
        for ($i = 0; $i <= strlen($luokat_string); $i++) {
            if (substr($luokat_string, (-1) * strlen($luokat_string) + $i, 1) != ',' && substr($luokat_string, (-1) * strlen($luokat_string) + $i, 1) != ' ') {
                $uusiLuokka . substr($luokat_string, (-1) * strlen($luokat_string) + $i, 1);
            } else if (substr($luokat_string, (-1) * strlen($luokat_string) + $i, 1) == ',') {
                $luokat_temp[] = $uusiLuokka;
                $uusiLuokka = '';
            }
        }
        return $luokat_temp;
    }

    public static function kaikki() {
        $query = DB::connection()->prepare('SELECT * FROM Askare');
        $query->execute();
        $rows = $query->fetchAll();
        $askareet = array();

        foreach ($rows as $row) {
            //luokat tietokannassa stringinä
            $luokat_string = $row['luokat'];
//            $luokat_temp = parseClasses($luokat_string);
            $uusiLuokka = '';
            $luokat_temp = array();
            for ($i = 0; $i <= strlen($luokat_string); $i++) {
                if (substr($luokat_string, (-1) * strlen($luokat_string) + $i, 1) != ',' && substr($luokat_string, (-1) * strlen($luokat_string) + $i, 1) != ' ') {
                    $uusiLuokka . substr($luokat_string, (-1) * strlen($luokat_string) + $i, 1);
                } else if (substr($luokat_string, (-1) * strlen($luokat_string) + $i, 1) == ',') {
                    $luokat_temp[] = $uusiLuokka;
                    $uusiLuokka = '';
                }
            }

            $askareet[] = new Askare(array(
                'id' => $row['id'],
                'kayttaja_id' => $row['kayttaja_id'],
                'nimi' => $row['nimi'],
                'prioriteetti' => $row['prioriteetti'],
                'luokat' => $luokat_temp,
                'added' => $row['added']
            ));
        }

        return $askareet;
    }

    public static function find($id) {
        $query = DB::connection()->prepare('SELECT * FROM Askare WHERE id = :id LIMIT 1');
        $query->execute(array('id' => $id));
        $row = $query->fetch();

        if ($row) {
            $askare = new Askare(array(
                'id' => $row['id'],
                'kayttaja_id' => $row['kayttaja_id'],
                'nimi' => $row['nimi'],
                'prioriteetti' => $row['prioriteetti'],
                'luokat' => $row['luokat'],
                'added' => $row['added']
            ));
        }
        return $askare;
    }

    public function tallenna() {
        $query = DB::connection()->prepare('INSERT INTO Askare (nimi, description, prioriteetti, luokat) VALUES (:nimi, :description, :prioriteetti, :luokat) RETURNING id');
        $query->execute(array('nimi' => $this->nimi, 'description' => $this->description, 'prioriteetti' => $this->prioriteetti, 'luokat' => $this->luokat));
        $row = $query->fetch();
        $this->id = $row['id'];
        Kint::trace();
        Kint::dump($row);
    }

}
