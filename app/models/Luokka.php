<?php
/**
 * Malli luokalle.
 *
 * @author Oskari Kulmala
 */
class Luokka extends BaseModel {

    public $id, $nimi, $kayttajan_id;

    public function __construct($attributes) {
        parent::__construct($attributes);
        $this->validators = array('validate_name');
    }

    public function tallenna($kayttajan_id) {
        $query = DB::connection()->prepare('INSERT INTO Luokka (nimi, kayttajan_id) VALUES (:nimi, :kayttajan_id) RETURNING id');
        $query->execute(array('nimi' => $this->nimi, 'kayttajan_id' => $kayttajan_id));
        $row = $query->fetch();
        $this->id = $row['id'];
    }
    
    public function update() {
        $query = DB::connection()->prepare('UPDATE Luokka (nimi) VALUES (:nimi) RETURNING id');
        $query->execute(array('nimi' => $this->nimi));
        $row = $query->fetch();
        $this->id = $row['id'];

        Kint::trace();
        Kint::dump($row);
    }

    public static function find($id, $kayttajan_id) {
        $query = DB::connection()->prepare('SELECT * FROM Luokka WHERE id = :id AND kayttajan_id = :kayttajan_id LIMIT 1');
        $query->execute(array('id' => $id, 'kayttajan_id' => $kayttajan_id));
        $row = $query->fetch();
        $luokka = null;
        if ($row) {
            $luokka = new Luokka(array(
                'id' => $row['id'],
                'nimi' => $row['nimi'],
                'kayttajan_id' => $row['kayttajan_id']
            ));
        }
        return $luokka;
    }

    public static function kaikki($kayttajan_id) {
        $query = DB::connection()->prepare('SELECT * FROM Luokka WHERE kayttajan_id = :kayttajan_id');
        $query->execute(array('kayttajan_id' => $kayttajan_id));
        $rows = $query->fetchAll();
        $luokat = array();
        foreach ($rows as $row) {
            $luokat[] = new Luokka(array(
                'id' => $row['id'],
                'nimi' => $row['nimi'],
                'kayttajan_id' => $row['kayttajan_id']
            ));
        }
        return $luokat;
    }
    
    public static function askareen_luokat($askareen_id) {
        $query = DB::connection()->prepare('SELECT * FROM AskareittenLuokat INNER JOIN Luokka ON luokka_id = id WHERE askareen_id = :askareen_id');
        $query->execute(array('askareen_id' => $askareen_id));
        $rows = $query->fetchAll();
        $luokat = array();
        foreach ($rows as $row) {
            $luokat[] = new Luokka(array(
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
