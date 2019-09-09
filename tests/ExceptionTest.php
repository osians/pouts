<?php

// require_once __DIR__ . '/../src/VTException/VTException.php';

use \Osians\VTException\VTException;

class ExceptionTest extends \PHPUnit\Framework\TestCase
{
    private $exception;

    public function setUp()
    {
        //$this->exception = new VTException();
    }

    public function testImplementsPsr4ExceptionInstance()
    {
        $this->exception = new VTException();
        $this->exception->register();
        $this->assertInstanceOf('\Exception', $this->exception);
    }

    public function tearDown() {
        #@unlink($this->logger->getLogFilePath());
        #@unlink($this->errLogger->getLogFilePath());
    }
}
