<?php

require_once("Application/Application.php");

function router($params) {
    $method = $params["method"];
    if ($method) {
        $app = new Application();
        switch ($method) {
            case "getFilms": return $app->getFilms();
            case "getGenres": return $app->getGenres();
            case "getCountries": return $app->getCountries();
            case "getRatingsByGenre": return $app->getRatingsByGenre($params);
            case "getStatByGenre": return $app->getStatInfo($params);
            case "getStatByCountry": return $app->getStatInfo($params);
            case "getBestFilmsByGenre": return $app->getBestFilmsByGenre($params);
            case "getRandomFilm": return $app->getRandomFilm();
            case "refreshDBInfo": return $app->startParse();
            default: return false;
        }
    }

    return false;
}



function answer($data) {
    if ($data) {
        return array(
            "result" => "ok",
            "data" => $data
        );
    } 
    return array(
        "result" => "error", 
        "error" => array(
            "code" => 9000, 
            "text" => "unknown error"
        )
    );
}

echo json_encode(answer(router($_GET)));