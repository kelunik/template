<?php

namespace Kelunik\Template;

class TemplateService {
    private $cache;
    private $base;

    public function __construct (Cache $cache) {
        $this->cache = $cache;
    }

    public function load (string $file, int $option = Template::LOAD_PHP) {
        if ($file[0] !== "/") {
            $file = $this->base . $file;
        }

        return new Template($file, $option, $this->cache);
    }

    public function setBaseDirectory ($dir) {
        $this->base = rtrim($dir, "/") . "/";
    }
}