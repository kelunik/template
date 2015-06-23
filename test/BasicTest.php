<?php

namespace Kelunik\Template;

use PHPUnit_Framework_TestCase;

class BasicTest extends PHPUnit_Framework_TestCase {
    /** @var TemplateService */
    private $service;

    protected function setUp () {
        $this->service = new TemplateService(new Cache);
        $this->service->setBaseDirectory(__DIR__ . "/../src");
    }

    /** @test */
    public function basic () {
        $expect = <<<EXPECT
<!doctype html>
<html>
<head>
    <title>Hello World!</title>
</head>
<body>
<div class="test">
    <h1>Hello World!</h1>

    <div class="desc">
        Blah!    </div>
</div></body>
</html>
EXPECT;

        $template = $this->service->load("foo.php");
        $template->set([
            "title" => "Hello World!",
            "desc" => "Blah!",
        ]);

        $actual = $template->render();
        $this->assertEquals($expect, $actual);
    }

    /** @test */
    public function paths () {
        $expect = <<<EXPECT
1
3
5
4
2
6
end

EXPECT;

        $template = $this->service->load("1.php");
        $actual = $template->render();
        $this->assertEquals($expect, $actual);
    }
}
