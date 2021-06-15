<?php

require_once("DB/DB.php");
require_once("Parser/StartParse.php");

class Application {
    function __construct() {    
        $this->db = new DB();
        $this->parser = new StartParse($this->db);
    }

    public function getFilms() {
        return $this->db->getFilms();
    }

    public function getGenres() {
        return $this->db->getGenres();
    }

    public function getCountries() {
        return $this->db->getCountries();
    }

    public function getRatingsByGenre($params) {
        if ($params["genre"])
            return $this->db->getRatingsByGenre($params["genre"]);
        return false;
    }

    public function getBestFilmsByGenre($params) {
        if ($params["genre"])
            return $this->db->getBestFilmsByGenre($params["genre"]);
        return false;
    }

    public function getRandomFilm() {
        return $this->db->getRandomFilm();
    }

    /*public function getStatInfoByGenre($params) {
        if ($params["genre"]) {
            $result;
            $tempData = $this->db->getRatingsByGenre($params["genre"]);
            $ratings = [];
            foreach ($tempData as $i) {
                array_push($ratings, $i["RATING"]);
            }
            $count = count($ratings);
            // AVG
            $avg = array_sum($ratings) / $count;
            $avg = round($avg, 2);
            $result["Ср. Арифметическое"] = $avg;
            // MODA
            $frequent = array_count_values($ratings);
            arsort($frequent);
            $result["Мода"] = array_keys($frequent)[0];
            // MEDIANA
            $sort = $ratings;
            sort($sort);
            $med = $sort[$count];
            $result["Медиана"] = $sort[intval($count / 2)];
            return $result;
        }
        return false;
    }*/

    public function getStatInfo($params) {
        if ($params["genre"]) {
            $tempData = $this->db->getRatingsByGenre($params["genre"]);
        } else if ($params["country"]) {
            $tempData = $this->db->getRatingsByCountry($params["country"]);
        } else {
            return false;
        }
        $result;
        $ratings = [];
        if ($tempData) {
            foreach ($tempData as $i) {
                array_push($ratings, $i["RATING"]);
            }
            $count = count($ratings);
            // AVG
            $avg = array_sum($ratings) / $count;
            $avg = round($avg, 2);
            $result["Ср. Арифметическое"] = $avg;
            // MODA
            $frequent = array_count_values($ratings);
            arsort($frequent);
            $result["Мода"] = array_keys($frequent)[0];
            // MEDIANA
            $sort = $ratings;
            sort($sort);
            $med = $sort[$count];
            $result["Медиана"] = $sort[intval($count / 2)];
            return $result;
        }
        return false;
    }

    public function startParse() {
        $start = 0;
        $step = 20;
        $end = $step;
        $temp = $this->parser->run($start, $end);
        $result = 0;
        // return $temp;
        while ($temp != 0) {
            $result += $temp;
            $start += $step;
            $end += $step;
            $temp = $this->parser->run($start, $end);
        }
        if ($result) {
            return "Добавлено $result фильмов";
        }
        return "Фильмов не добавлено";
    }

}