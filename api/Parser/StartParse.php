<?php

require_once("phpQuery.php");
require_once("Parser.php");
require_once("DB/DB.php");
// require_once("Timer.php");
ini_set('max_execution_time', 1500);

class StartParse {

    function __construct($connection) {
        $this->parser = new Parser();
        $this->url = "https://sky.dostfilms.info/";
        $this->parser->set(CURLOPT_FOLLOWLOCATION, true);
        $this->connect = $connection;
    }

    public function run($start, $end) {
        $films = $this->parse($this->url, $start, $end);
        if ($films) {
            foreach ($films as $film) {
                $this->connect->add($film);
            }
            return sizeof($films);
        }
        return "Не нужно добавлять";
    }

    private function get_content($url) {
        // $timer = new Timer();
        $data = $this->parser->exec($url);
        // print($timer->result() . "<br>");
        return $data;
    }

    private function parse($url, $start, $end) {
        if ($start > 1)
            $url = $url . "?page" . $start;
        $existingFilms = 0;
        // Количество страниц за раз
        $setPage = 10;
        for (; $start < $end; $start += $setPage) {
            $arrUrls = [];
            $names = [];
            for ($j = 0; $j < $setPage; $j++) {
                $html = $this->get_content($url);
                $doc = phpQuery::newDocument($html);
                $films;
                $count = 0;
                foreach ($doc->find(".movierow__title a") as $i) {
                    $name = pq($i)->text();
                    if (!$this->connect->checkValue($name)) {
                        $names[] = $name;
                        $arrUrls[] = pq($i)->attr("href");
                        // if ($count > 0)
                            // break;
                    } else {
                        $existingFilms++;
                    }
                }
                $next = $doc->find(".pages_nav a:last")->attr("href");
                if (!empty($next)) {
                    $url = $next;
                }
            }
            
            // МУЛЬТИКУРЛИМ
            $mh = curl_multi_init();
            $arrCh = [];

            foreach ($arrUrls as $i) {
                $temp = curl_init($i);
                curl_setopt($temp, CURLOPT_RETURNTRANSFER, true);
                curl_multi_add_handle($mh, $temp);
                $arrCh[] = $temp;
            }

            $running = null;
            do {
                curl_multi_exec($mh, $running);
            } while ($running);

            foreach ($arrCh as $i) {
                curl_multi_remove_handle($mh, $i);
            }
            curl_multi_close($mh);

            $sizeNames = sizeof($names);
            for ($i = 0; $i < $sizeNames; $i++) {
                $film[0] = $names[$i];
                $temp = [];
                $temp = $this->parsePageFilm(curl_multi_getcontent($arrCh[$i]));
                $film = array_merge($film, $temp);
                $films[] = $film;
                $film = [];
            }
            // Следующая страница
            phpQuery::unloadDocuments($html);
            // print("Страница Загружена \n");
            if ($existingFilms > 180) {
                break;
            }
        }
        return $films;
    }

    private function parsePageFilm($html) {
        $doc = phpQuery::newDocument($html);
        if ($firstInfo = $doc->find(".spisok-dop-info li:first")) {
            // Производство
            $tempText = trim(preg_replace("/\s+/", ' ', $firstInfo->text()), " ");
            $film[] = substr($tempText, 26);
            $firstInfo = $firstInfo->next();
            // Длительность
            $tempText = trim(preg_replace("/\s+/", ' ', $firstInfo->text()), " ");
            $film[] = stristr(substr($tempText, 26), ' ', true);
            $firstInfo = $firstInfo->next();
            // Перевод
            $tempText = trim(preg_replace("/\s+/", ' ', $firstInfo->text()), " ");
            $film[] = substr($tempText, 16);
            $firstInfo = $firstInfo->next();
            // Дата премьеры
            $tempText = trim(preg_replace("/\s+/", ' ', $firstInfo->text()), " ");
            $film[] = substr($tempText, 27);
            $firstInfo = $firstInfo->next();
            // В оригинале
            $tempText = trim(preg_replace("/\s+/", ' ', $firstInfo->text()), " ");
            $film[] = substr($tempText, 23);
            $firstInfo = $firstInfo->next();
            // Постановщик
            $tempText = trim(preg_replace("/\s+/", ' ', $firstInfo->text()), " ");
            $film[] = substr($tempText, 24);
            $firstInfo = $firstInfo->next();
            // Главные роли
            $tempText = trim(preg_replace("/\s+/", ' ', $firstInfo->text()), " ");
            $film[] = substr($tempText, 25);
            // Рейтинг
            $rating = $doc->find(".rating-podpravka div:first")->attr("title");
            $tempText = trim(preg_replace("/\s+/", ' ', $rating), " ");
            $film[] = stristr(substr($tempText, 16), ' ', true);
            // Жанр
            $genre = $doc->find(".detaly-cat-name")->text();
            $tempText = trim(preg_replace("/\s+/", ' ', $genre), " ");
            $film[] = $tempText;
        }
        phpQuery::unloadDocuments($html);
        return $film;
    }

    // $timer = new Timer();
    
    // print("<br> Общее время загрузки:  " . $timer->result() . "<br>");
}