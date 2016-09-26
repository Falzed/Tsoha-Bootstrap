<?php

class Askare extends BaseModel {

    public $id, $kayttaja_id, $nimi, $description, $prioriteetti, $luokat, $added, $luokat_string;

    public function __construct($attributes) {
        parent::__construct($attributes);
    }

    private static function classesToString($luokat_arg) {
        $luokat_string = '';
        if (count($luokat_arg) == 1) {
            $luokat_string . $luokka[0];
        }
        if (count($luokat_arg) > 1) {
            $luokat_string . $luokka[0];
        } {
            for ($i = 0; $i <= count($luokat_arg) - 1; $i++) {
                $luokat_string . $luokka[i] . ', ';
            }
            $luokat_string . $luokka[count($luokat_arg) - 1];
        }        

        return $luokat_string;
    }

    public static function kaikki() {
        $query = DB::connection()->prepare('SELECT * FROM Askare');
        $query->execute();
        $rows = $query->fetchAll();
        $askareet = array();

        foreach ($rows as $row) {
            ;

            $askareet[] = new Askare(array(
                'id' => $row['id'],
                'kayttaja_id' => $row['kayttaja_id'],
                'nimi' => $row['nimi'],
                'description' => $row['description'],
                'prioriteetti' => $row['prioriteetti'],
                'luokat' => $row['luokat'],
                'added' => $row['added'],
                'luokat_string' => Askare::classesToString($row['luokat'])
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
                'description' => $row['description'],
                'prioriteetti' => $row['prioriteetti'],
                'luokat' => $row['luokat'],
                'added' => $row['added'],
                'luokat_string' => Askare::classesToString($row['luokat'])
            ));
        }
        return $askare;
    }

    public function tallenna() {
        $query = DB::connection()->prepare('INSERT INTO Askare (nimi, description, prioriteetti, luokat, added) VALUES (:nimi, :description, :prioriteetti, :luokat, NOW()) RETURNING id');
        $query->execute(array('nimi' => $this->nimi, 'description' => $this->description, 'prioriteetti' => $this->prioriteetti, 'luokat' => $this->luokat));
        $row = $query->fetch();
        $this->id = $row['id'];
        Kint::trace();
        Kint::dump($row);
    }

}
