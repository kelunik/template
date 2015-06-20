<?php

namespace Kelunik\Tpl;

class Template {
    const LOAD_PHP = 0;
    const LOAD_RAW = 1;

    private $file;
    private $option;
    private $vars;
    private $cache;
    private $context;

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

    private function inline (string $file, int $options = self::LOAD_PHP) {
        if ($file[0] !== "/") {
            $context = "";

            foreach ($this->context as $path) {
                if (empty($path)) {
                    continue;
                }

                $context .= $path . "/";
            }
        } else {
            $context = "";
        }

        $value = $this->load($context . $file);
        $this->context[] = dirname($file);

        try {
            if ($options === self::LOAD_PHP) {
                ob_start();

                try {
                    eval("unset(\$value, \$options); extract(\$this->vars); ?>$value");
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
}