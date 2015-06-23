<?php

namespace Kelunik\Template;

class Template {
    const LOAD_PHP = 0;
    const LOAD_RAW = 1;

    private $file;
    private $option;
    private $vars;
    private $cache;
    private $context;
    private $closures;

    public function __construct (string $file, int $option, Cache $cache) {
        $this->file = $file;
        $this->option = $option;
        $this->cache = $cache;
        $this->vars = [];
        $this->context = [dirname($file)];
    }

    public function set ($key, $val = null) {
        if (is_array($key) && $val === null) {
            $this->vars += $key;
        } else {
            $this->vars[$key] = $val;
        }
    }

    public function render () {
        return $this->inline($this->file, $this->option);
    }

    private function inline (string $filename, int $options = self::LOAD_PHP) {
        if ($filename[0] !== "/") {
            end($this->context);
            $path = current($this->context) . "/";
        } else {
            $path = "";
        }

        $file = $path . $filename;
        $value = $this->load($file);
        $this->context[] = dirname($file);

        try {
            if ($options === self::LOAD_PHP) {
                if (!isset($this->closures[$file])) {
                    $closure = eval("return function () { extract(\$this->vars); ?>$value<?php };");
                    $this->closures[$file] = $closure;
                }

                try {
                    ob_start();
                    $closure = $this->closures[$file];
                    $closure();
                    return ob_get_contents();
                } finally {
                    ob_end_clean();
                }
            } else {
                return $value;
            }
        } finally {
            array_pop($this->context);
        }

        return "";
    }

    private function load (string $file) {
        $value = $this->cache->get($file);

        if ($value === null) {
            if (!file_exists($file)) {
                throw new FileNotFoundException("{$file} not found");
            }

            $value = file_get_contents($file);
            $this->cache->put($file, $value);
        }

        return $value;
    }

    private function escape (string $str) {
        return htmlspecialchars($str, ENT_QUOTES | ENT_HTML5);
    }
}