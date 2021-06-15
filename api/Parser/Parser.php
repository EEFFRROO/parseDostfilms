<?php

class Parser {

    private $url;
    private $ch;

    public function __construct($print = false) {
        $this->ch = curl_init();
        if (!$print)
            curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($this->ch, CURLOPT_ENCODING, '');
    }

    public function set($name, $value) {
        curl_setopt($this->ch, $name, $value);
    }

    public function exec($url) {
        curl_setopt($this->ch, CURLOPT_URL, $url);
        return curl_exec($this->ch);
    }

    public function __destruct() {
        curl_close($this->ch);
    }


}