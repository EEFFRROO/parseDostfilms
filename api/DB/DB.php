<?php

class DB {

    private $conn = "asfsf";

    public function __construct() {
        $this->conn = oci_pconnect("system", "123", "localhost/orcl");
        if (!$this->conn) 
            print("not success");
    }

    public function checkConnect() {
        if (!$this->conn) {
            print("not success");
        } else {
            $test = oci_parse($this->conn, "SELECT * FROM FILMS");
            oci_execute($test);
            while ($row = oci_fetch_array($test, OCI_ASSOC+OCI_RETURN_NULLS)) {
                foreach ($row as $item) {
                    echo $item . "   ";
                }
            }
        }
    }

    public function __destruct() {
        oci_close($this->conn);
    }

    private function fetchAll($query) {
        $query = oci_parse($this->conn, $query);
        oci_execute($query);
        oci_fetch_all($query, $result, 0, 0, OCI_FETCHSTATEMENT_BY_ROW);
        return $result;
    }

    public function getFilms() {
        $query = "SELECT * FROM FILMS WHERE ROWNUM < 10";
        return $this->fetchAll($query);
    }

    public function getGenres() {
        $delBadsQuery = "DELETE FROM FILMS WHERE COUNTRY LIKE '%мин.%'";
        $delBads = @oci_parse($this->conn, $delBadsQuery);
        @oci_execute($check);
        $delBadsQuery = "DELETE FROM FILMS WHERE COUNTRY LIKE '%минут%'";
        $delBads = @oci_parse($this->conn, $delBadsQuery);
        @oci_execute($check);
        $query = "SELECT DISTINCT GENRE FROM FILMS WHERE NOT GENRE IS NULL ORDER BY GENRE";
        return $this->fetchAll($query);
    }

    public function getCountries() {
        $query = "SELECT DISTINCT SUBSTR(COUNTRY, 1, NVL(NULLIF(INSTR(COUNTRY, ',') - 1, -1), LENGTH(COUNTRY))) AS C FROM FILMS 
            WHERE NOT SUBSTR(COUNTRY, 1, NVL(NULLIF(INSTR(COUNTRY, ',') - 1, -1), LENGTH(COUNTRY))) IS NULL ORDER BY C";
        return $this->fetchAll($query);
    }

    public function getProducers() {
        $query = "SELECT DISTINCT SUBSTR(PRODUCER, 1, NVL(NULLIF(INSTR(PRODUCER, ',') - 1, -1), LENGTH(PRODUCER))) AS P FROM FILMS 
            WHERE NOT SUBSTR(PRODUCER, 1, NVL(NULLIF(INSTR(PRODUCER, ',') - 1, -1), LENGTH(PRODUCER))) IS NULL ORDER BY P";
        return $this->fetchAll($query);
    }
    
    public function getRatingsByGenre($genre) {
        $query = "SELECT RATING FROM FILMS WHERE GENRE = '" . $genre . "' AND NOT RATING = 0";
        return $this->fetchAll($query);
    }

    public function getRatingsByCountry($country) {
        $query = "SELECT RATING FROM FILMS WHERE COUNTRY LIKE '%" . $country . "%' AND NOT RATING = 0";
        return $this->fetchAll($query);
    }

    public function getBestFilmsByGenre($genre) {
        $query = "SELECT * FROM (SELECT * FROM FILMS WHERE GENRE = '" . $genre . "' ORDER BY RATING DESC) WHERE ROWNUM <= 10";
        return $this->fetchAll($query);
    }

    public function getRandomFilm() {
        $query = "SELECT * FROM (SELECT * FROM FILMS ORDER BY DBMS_RANDOM.VALUE) WHERE ROWNUM <= 1";
        return $this->fetchAll($query);
    }

    public function checkValue($value) {
		$value = addcslashes($value, '\'');
        $queryForCheck = "SELECT * FROM FILMS WHERE FILMS.TITLE = '" . strval($value) . "'";
        $check = oci_parse($this->conn, $queryForCheck);
        @oci_execute($check);
        if (oci_fetch_object($check))
            return true;
        return false;
    }

    public function add($arr) {
        $queryForCheck = "SELECT * FROM FILMS WHERE FILMS.TITLE = '" . strval($arr[0]) . "'";
        $check = @oci_parse($this->conn, $queryForCheck);
        @oci_execute($check);
        $row = oci_fetch_object($check);
        if (!$row) {
            self::insertToDB($this->conn, "FILMS", "FILMS.TITLE", $arr[0]);
            self::updateDB($this->conn, "FILMS", "FILMS.COUNTRY", $arr[1], $arr[0]);
            self::updateDB($this->conn, "FILMS", "FILMS.FILM_DURATION", $arr[2], $arr[0]);
            self::updateDB($this->conn, "FILMS", "FILMS.VOICE_ACTION", $arr[3], $arr[0]);
            self::updateDB($this->conn, "FILMS", "FILMS.RELEASE_DATE", $arr[4], $arr[0]);
            self::updateDB($this->conn, "FILMS", "FILMS.ORIGINAL_TITLE", $arr[5], $arr[0]);
            self::updateDB($this->conn, "FILMS", "FILMS.PRODUCER", $arr[6], $arr[0]);
            self::updateDB($this->conn, "FILMS", "FILMS.ACTORS", $arr[7], $arr[0]);
            self::updateDB($this->conn, "FILMS", "FILMS.RATING", $arr[8], $arr[0]);
            self::updateDB($this->conn, "FILMS", "FILMS.GENRE", $arr[9], $arr[0]);
            // print_r($arr);
        }
    }

    private static function insertToDB($conn ,$table, $collumn, $value) {
        $value = addcslashes($value, '\'');
        $query = "INSERT INTO " . $table . " (" . $collumn . ") VALUES ('" . $value . "')";
        // print($query . "<br>");
        $temp = @oci_parse($conn, $query);
        @oci_execute($temp);
    }

    private static function updateDB($conn, $table, $collumn, $value, $where) {
		$value = addcslashes($value, '\'');
        //$value = str_replace('\'', '`', $value);
        $query = "UPDATE " . $table . " SET " . $collumn . " = '" . $value . "' WHERE " . $table . ".TITLE = '" . $where . "'";
        // print($query . "<br>");
        $temp = @oci_parse($conn, $query);
        @oci_execute($temp);
    }

}