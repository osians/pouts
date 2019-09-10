<?php

namespace Osians\Pouts;

// use \Osians\Pouts\ErrorHandler;

class Pouts
{
    /**
     *    Indica se erros ja foram iniciados
     *    @var bool
     */
    private $isRegistered = false;

    public function __construct()
    {
    }

    /**
     *    Registra essa instancia como o Gerenciador de Erros
     *    @return Pouts
     */
    public function register($types = 'use-php-defaults')
    {
        if (!$this->isRegistered) {
            class_exists("\\Osians\\Pouts\\ErrorHandler");
            class_exists("\\Osians\\Pouts\\ExceptionHandler");
            class_exists("\\Osians\\Pouts\\ShutdownHandler");

            if ($types === 'use-php-defaults') {
                $types = E_ALL;
            }

            set_error_handler(array("\\Osians\\Pouts\\ErrorHandler", 'handler'), $types);
            set_exception_handler(array('\\Osians\\Pouts\\ExceptionHandler', 'handler'));
            register_shutdown_function(['\\Osians\\Pouts\\ShutdownHandler', 'handler']);

            $this->isRegistered = true;
        }

        return $this;
    }
}
