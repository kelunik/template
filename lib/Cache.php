<?php

namespace Kelunik\Template;

class Cache {
    private $data;

    public function __construct () {
        $this->data = [];
    }

    public function put (string $file, string $contents) {
        $this->data[$file] = $contents;
    }

    public function get (string $file) {
        return $this->data[$file] ?? null;
    }

    public function reset () {
        $this->data = [];
    }
}