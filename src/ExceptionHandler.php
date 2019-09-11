<?php

namespace Osians\Pouts;

use \Exception;

/**
 *    Classe responsavel por tratar Exceptions no sistema
 *    normalmente as chamada atraves do codigo
 *    thown new SysException( "Mensagem a ser apresentada" );
 */
class ExceptionHandler
{
    public function __construct()
    {
    }

    public static function handler($exception)
    {
        self::initConfig($exception);
        error_reporting(0);
    }

    public static function initConfig(Exception $exception)
    {
        global $eConfig;

        $hoje = date('yyyy-mm-dd HH:ii:ss');
        $code = $exception->getCode() . " - ". self::getTypeError($exception->getCode());

        $msgError = "\n ====== $hoje ======"
                  . "\n Erro no arquivo : " . $exception->getFile()
                  . "\n Linha :           " . $exception->getLine()
                  . "\n Mensagem :        " . $exception->getMessage()
                  . "\n Codigo :          " . $code
                  . "\n Trace(str) :      " . "\n" . $exception->getTraceAsString()
                  . "\n ";

        $tmpFileDestionation = $eConfig['log_folder']
                             . date('Ymd').'_'
                             . $eConfig['sys_errorfile_destination'];

        $destination = $eConfig['sys_error_log_type'] == 3
                     ? $eConfig['sys_errorfile_destination']
                     : $eConfig['sys_error_log_email'];

        //    se vai gravar em arquivo, chega se o mesmo existe
        if ($eConfig['sys_error_log_type'] == 3) {
            if (!file_exists($tmpFileDestionation)) {
                if ($fh = fopen($tmpFileDestionation, 'w')) {
                    fclose($fh);
                    $destination = $tmpFileDestionation;
                } else {
                    $eConfig['sys_error_log_type'] = 0;
                }
            } else {
                $destination = $tmpFileDestionation;
            }
        }

        error_log($msgError, $eConfig['sys_error_log_type'], $destination);

        # checa o que o desenvolvedor setou nas configuracoes para apresentar os erros na tela

        # apresentando pagina com saida HTML
        $error = new ErrorOutput();
        $error->show_error_log = $eConfig['show_error_log'];
        $error->message = $exception->getMessage();
        $error->line = $exception->getLine();
        $error->file = $exception->getFile();
        $error->code = $exception->getCode();
        $error->traceAsString = $exception->getTraceAsString();
        $error->showFullErrorLog = true;
        print $error;

        # ja exibimos erro, nao necessario que php faca isso novamente, desligando erros do sistema
        error_reporting(0);
    }

    public static function getTypeError($errorCode)
    {
        switch ($errorCode) {
            case E_ERROR:             return "E_ERROR"; break; // code: 1
            case E_WARNING:           return "E_WARNING"; break; // code: 2
            case E_PARSE:             return "E_PARSE"; break; // code: 4
            case E_NOTICE:            return "E_NOTICE"; break; // code: 8
            case E_CORE_ERROR:        return "E_CORE_ERROR"; break; // 1code: 6
            case E_CORE_WARNING:      return "E_CORE_WARNING"; break; // 3code: 2
            case E_COMPILE_ERROR:     return "E_COMPILE_ERROR"; break; // 6code: 4
            case E_COMPILE_WARNING:   return "E_COMPILE_WARNING"; break; // 12code: 8
            case E_USER_ERROR:        return "E_USER_ERROR"; break; // 25code: 6
            case E_USER_WARNING:      return "E_USER_WARNING"; break; // 51code: 2
            case E_USER_NOTICE:       return "E_USER_NOTICE"; break; // 102code: 4
            case E_ALL:               return "E_ALL"; break; // 614code: 3
            case E_STRICT:            return "E_STRICT"; break; // 204code: 8
            case E_RECOVERABLE_ERROR: return "E_RECOVERABLE_ERROR"; break; // 409code: 6
            default:                  return "UNDEFINED"; break;
        }
    }
}
