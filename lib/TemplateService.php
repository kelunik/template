<?php

namespace Kelunik\Tpl;

class TemplateService {
    private $cache;

    public function __construct (Cache $cache) {
        $this->cache = $cache;
    }

    public function load (string $file, int $option = Template::LOAD_PHP) {
        return new Template($file, $option, $this->cache);
    }
}