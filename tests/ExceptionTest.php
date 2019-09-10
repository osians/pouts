<?php

// require_once __DIR__ . '/../src/VTException/VTException.php';

use \Osians\Pouts\Pouts;

class ExceptionTest extends \PHPUnit\Framework\TestCase
{
    private $pouts;

    public function setUp()
    {
        //$this->pouts = new VTException();
    }

    public function testImplementsPsr4ExceptionInstance()
    {
        $this->pouts = new Pouts();
        $this->pouts->register();
        $this->assertInstanceOf('\Osians\Pouts\Pouts', $this->pouts);
    }

    public function tearDown() {
        #@unlink($this->logger->getLogFilePath());
        #@unlink($this->errLogger->getLogFilePath());
    }
}
