<?php

$ds = DIRECTORY_SEPARATOR;

require_once __DIR__ . "{$ds}..{$ds}src{$ds}ShutdownHandler.php";
require_once __DIR__ . "{$ds}..{$ds}src{$ds}ErrorHandler.php";
require_once __DIR__ . "{$ds}..{$ds}src{$ds}ExceptionHandler.php";
require_once __DIR__ . "{$ds}..{$ds}src{$ds}Pouts.php";

$pouts = new \Osians\Pouts\Pouts;
$pouts->register();

//    testando exception
// throw new Exception("Testando Handler de Exceptions", 1);

echo 10 / 0;

