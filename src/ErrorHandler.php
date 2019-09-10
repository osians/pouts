<?php

namespace Osians\Pouts;

/**
 *    Classe responsavel por lidar e apresentar informacoes
 *    acerca de erros que venham a acontecer no sistema
 * 
 *    @source https://www.php.net/manual/pt_BR/function.set-error-handler.php
 */
class ErrorHandler
{
    public static function handler($errno, $errstr, $errfile, $errline, $errcontext)
    {
        //    Verifica se Codigo de erro esta incluido em error_reporting
        if (!(error_reporting() & $errno)) {
            return;
        }

        switch ($errno) {
            case E_USER_ERROR:
            case E_USER_WARNING:
            case E_USER_NOTICE:
            case E_NOTICE:
            case E_WARNING:
            case E_ERROR:
                throw new \Exception($errstr, $errno);
                exit(1);
                break;

            default:
                echo "Erro desconhecido: [{$errno}] {$errstr} " . PHP_EOL;
                break;
        }

        /* evita executar o handler interno de erros do PHP */
        return true;
    }
}
