<?php

class BaseModel {

    // "protected"-attribuutti on käytössä vain luokan ja sen perivien luokkien sisällä
    protected $validators;

    public function __construct($attributes = null) {
        // Käydään assosiaatiolistan avaimet läpi
        foreach ($attributes as $attribute => $value) {
            // Jos avaimen niminen attribuutti on olemassa...
            if (property_exists($this, $attribute)) {
                // ... lisätään avaimen nimiseen attribuuttin siihen liittyvä arvo
                $this->{$attribute} = $value;
            }
        }
    }

    public function errors() {
        // Lisätään $errors muuttujaan kaikki virheilmoitukset taulukkona
        $errors = array();

        foreach ($this->validators as $validator) {
            // Kutsu validointimetodia tässä ja lisää sen palauttamat virheet errors-taulukkoon
            $errors = array_merge($errors, $this->{$validator}());
        }

        return $errors;
    }

    public function validate_string_length($string, $minimi) {
        $errors = array();
        if (($string == '' || $string == null) && minimi>0) {
            $errors[] = 'Merkkijono ei saa olla tyhjä!';
        }
        if (strlen($string) < $minimi) {
            $errors[] = 'Merkkijonon pituuden tulee olla vähintään ' . $minimi . ' merkkiä!';
        }

        return $errors;
    }

    public function validate_numeric($num) {
        $errors = array();
        if(!is_numeric($num)) {
            $errors[] = 'Ei ole numero';
        }
        return $errors;
    }
    
    public function validate_regex($pattern, $mjono) {
        $errors = array();
        if(preg_match($pattern, $mjono)) {
            $errors[] = 'Ei ole numero';
        }
        return $errors;
    }
}
