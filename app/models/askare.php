<?php

/**
 * Malli askareelle.
 *
 * @author Oskari Kulmala
 */
class Askare extends BaseModel {

    public $id, $kayttaja_id, $nimi, $description, $prioriteetti, $added, $luokat;

    public function __construct($attributes) {
        parent::__construct($attributes);
        $this->validators = array('validate_name', 'validate_priority');
    }

    public static function kaikki($kayttaja_id, $options) {
        $statement = 'SELECT * FROM Askare WHERE kayttaja_id = :kayttaja_id';
        $exec_params = array('kayttaja_id' => $kayttaja_id);

        if (isset($options['haku'])) {
            $exec_params['like'] = '%' . $options['haku'] . '%';
            $statement .= ' AND nimi LIKE :like';
        }
        //jostain syystä :sort ei tunnu toimivan kyselyssä joten kovakoodatut vaihtoehdot
        if (array_key_exists('sort', $options)) {
            $sort = $options['sort'];
            $statement = $statement . ' ORDER BY :sort';
//            if ($sort == 'prioriteetti') {
//                $statement = $statement . ' ORDER BY prioriteetti';
//            } else if ($sort == 'id') {
//                $statement = $statement . ' ORDER BY id';
//            } else if ($sort == 'nimi') {
//                $statement = $statement . ' ORDER BY nimi';
//            }
//            $exec_params['sort'] = $sort;
            if (array_key_exists('asc_desc', $options)) {
                $asc_desc = $options['asc_desc'];
                if ($asc_desc == 'ASC') {
                    $statement = $statement . ' ASC';
                } else if ($asc_desc == 'DESC') {
                    $statement = $statement . ' DESC';
                }
            }
        }

        if (isset($options['page'])) {
            $page = $options['page'];
        } else {
            $page = 1;
        }
        if(isset($options['page_size'])) {
            $page_size = $options['page_size'];
        } else {
            $page_size = 10;
        }
        $statement .= ' LIMIT :limit OFFSET :offset';
        $exec_params['limit'] = $page_size;
        $exec_params['offset'] = $page_size * ($page - 1);


//        $statement = $statement . ' ORDER BY prioriteetti DESC';
        $query = DB::connection()->prepare($statement);
        $query->execute($exec_params);
        $rows = $query->fetchAll();
        $askareet = array();

        foreach ($rows as $row) {

            $askareet[] = new Askare(array(
                'id' => $row['id'],
                'kayttaja_id' => $row['kayttaja_id'],
                'nimi' => $row['nimi'],
                'description' => $row['description'],
                'prioriteetti' => $row['prioriteetti'],
                'added' => $row['added']
            ));
        }
//        Kint::dump($rows);
//        Kint::dump($statement);
//        Kint::dump($query);
//        Kint::dump($sort);
        return $askareet;
    }

    public static function count($kayttaja_id) {
        $query = DB::connection()->prepare('SELECT Count(*) FROM Askare WHERE kayttaja_id = :kayttaja_id');
        $query->execute(array('kayttaja_id' => $kayttaja_id));
        $row = $query->fetch();
        $count = $row['count'];
        return $count;
    }

    public static function find($id, $kayttaja_id) {
        $query = DB::connection()->prepare('SELECT * FROM Askare WHERE id = :id AND kayttaja_id = :kayttaja_id LIMIT 1');
        $query->execute(array('id' => $id, 'kayttaja_id' => $kayttaja_id));
        $row = $query->fetch();
        $askare = null;
        if ($row) {
            $askare = new Askare(array(
                'id' => $row['id'],
                'kayttaja_id' => $row['kayttaja_id'],
                'nimi' => $row['nimi'],
                'description' => $row['description'],
                'prioriteetti' => $row['prioriteetti'],
                'added' => $row['added']
            ));
        }
        return $askare;
    }

    public function tallenna($kayttaja_id) {
        $query = DB::connection()->prepare('INSERT INTO Askare (nimi, description, prioriteetti, added, kayttaja_id) VALUES (:nimi, :description, :prioriteetti, NOW(), :kayttaja_id) RETURNING id');
        $query->execute(array('nimi' => $this->nimi, 'description' => $this->description, 'prioriteetti' => $this->prioriteetti, 'kayttaja_id' => $kayttaja_id));
        $row = $query->fetch();
        $this->id = $row['id'];

        $query_luokka = Kint::trace();
        Kint::dump($row);
    }

    public function update() {
        $query = DB::connection()->prepare('UPDATE Askare SET nimi = :nimi, description = :description, prioriteetti = :prioriteetti WHERE id = :id');
        $query->execute(array('nimi' => $this->nimi, 'description' => $this->description, 'prioriteetti' => $this->prioriteetti, 'id' => $this->id));
        $row = $query->fetch();


        Kint::trace();
        Kint::dump($row);
    }

    public function destroy() {
        $askare_id = $this->id;
        $query_luokat = DB::connection()->prepare('DELETE FROM AskareittenLuokat WHERE askare_id = :askare_id');
        $query_luokat->execute(array('askare_id' => $askare_id));
        $query = DB::connection()->prepare('DELETE FROM Askare WHERE id=:id RETURNING id');
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
