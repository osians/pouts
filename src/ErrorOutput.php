<?php

namespace Osians\Pouts;

// header('Content-Type: text/html; charset=utf-8');

/**
 *    Classe responsavel por apresentar uma Pagina
 *    HTML Amigavel contendo o erro encontrado.
 */
class ErrorOutput
{
    public function __set($name, $value)
    {
        $this->$name = htmlspecialchars($value);
    }

    public function gatherInformation()
    {
        global $eConfig;

        $info = new \StdClass;
        $info->dataFormatada = date('yyyy-mm-dd hh:ii:ss', $_SERVER['REQUEST_TIME']);
        $info->trace = str_replace("#", "<br>#", $this->traceAsString);
        $info->arquivo = basename($this->file);
        $info->requestedUri = isset($_SERVER['HTTP_HOST'])
            ? "http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}" : 'undefined';

        $info->server = new \StdClass;
        $info->server->httpAccept = isset($_SERVER['HTTP_ACCEPT']) ? $_SERVER['HTTP_ACCEPT'] : 'Undefined';
        $info->server->httpUserAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Undefined';
        $info->server->remoteAddr = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'Undefined';
        $info->server->serverAdmin = isset($_SERVER['SERVER_ADMIN']) ? $_SERVER['SERVER_ADMIN'] : 'Undefined';

        return $info;
    }

    public function getContent()
    {
        $info = $this->gatherInformation();

        global $eConfig;


        if (ob_get_contents()) {
            ob_end_clean();
        }

        // date_default_timezone_set($eConfig['default_time_zone']);


        $this->trechoComErro = "";

        // obtendo codigo do arquivo que deu erro
        $handle = fopen($this->file, "r");

        if ($handle) {

            $linhaAtual   = 1;
            $inicioTrecho = $this->line - 3;
            $finalTrecho  = $this->line + 2;

            while (($line = fgets($handle)) !== false){
                // obtem apenas 3 linhas de código
                if($linhaAtual >= $inicioTrecho){
                    if($linhaAtual == $this->line) $this->trechoComErro .= "<b>";
                    $this->trechoComErro .= "{$linhaAtual}: {$line}";
                    if($linhaAtual == $this->line)
                        $this->trechoComErro .= "</b>";
                    $this->trechoComErro .= "<br>";

                    if($linhaAtual == $finalTrecho) break;
                }
                $linhaAtual++;
            }
            fclose($handle);
        } else {
            // error opening the file.
        }

        // checa se a dump extra dentro de elog.
        $elog = (!empty($eConfig['elog'])) ? implode( "<br>", $eConfig['elog'] ) : null;
        $elog = (null == $elog) ? "" : '<span class="elog"><b>Extra Log:</b> <br>'. $elog . '</span><br><br>';

        // verificando se devemos apresentar o extra log
        $show_extra_log = ($eConfig['show_extra_log'] == FALSE) ? "display:none;" : "";

        // verificando se deve apresentar uma versao mais light de erros
        if($this->showFullErrorLog)
        {

        $html =<<<HTML
        <html>
            <head>
                <title>Vita: Erro Capturado</title>
                <style>
                    html,body{margin:0;padding:0;}
                    body{
                        background-color:#28282E;
                        color:#C6E863;
                    }
                    div.container{
                        margin-left:40px;
                    }
                    h1{
                       font-family:"Open Sans",Verdana, Sans-serif;
                       font-size:36px;
                       display:block;
                       color:#FF6A44;
                       font-weight:100;
                    }
                    a{
                        background-color: #C6E863;
                        color: #28282E;
                        padding: 15px 20px;
                        text-decoration: none;
                        cursor: pointer;
                        margin-bottom: 50px;
                        border-radius: 4px;
                    }
                    a:hover{
                        opacity: .8;
                    }
                    div.errwrapper{
                        font-size:16px;
                        font-family:monospace;
                    }
                    div.errmais{
                        color:#9AA2A9;
                        border-top: 1px dashed;
                        border-bottom: 1px dashed;
                        padding-top: 30px;
                        margin-bottom: 30px;
                    }
                    span.trecho_code_err{
                        background-color: #28282E;
                        display: block;
                        margin:15px 0;
                    }
                    span.elog{
                        color:#3F8AFF;
                    }
                    span.trecho_code_err b{
                        color:#FFFF00;
                    }
                    span.show_extra_log{
                        {$show_extra_log}
                    }
                    span.msg_errro{color:#DD982E;background-color:#202024;display:block;padding: 10px 25px;}
                </style>
            </head>
            <body>
            <div class="container">
                <br>
                <h1>Erro em: {$info->arquivo}</h1>

                <div class="errwrapper">
                    <div style="">
                        <span class="msg_errro">
                            {$this->message}
                        </span>
                        <span class='trecho_code_err'>
                            {$this->trechoComErro}
                        </span>
                        <br>

                        {$elog}

                        <div class="errmais">

                            <b>Mensagem:</b>   {$this->message} <br>
                            <b>Arquivo</b>:    {$this->file} (Linha: {$this->line}) <br>
                            <b>Código</b>:     {$this->code} <br>
                            <b>Trace(str):</b> {$info->trace} <br>
                            <br><br>

                            <span class='show_extra_log'>
                            <b>Dados adicionais</b> <br>
                            REQUEST_URI:  {$info->requestedUri}<br>
                            HTTP_ACCEPT:  {$info->server->httpAccept} <br>
                            USER_AGENT :  {$info->server->httpUserAgent} <br>
                            REMOTE_ADDR:  {$info->server->remoteAddr} <br>
                            REQUEST_TIME: {$info->dataFormatada}<br>
                            SERVER_ADMIN: {$info->server->serverAdmin}<br>
                            </span>
                        </div>
                        <a href="mailto:{$eConfig['sys_error_log_email']}">Informar o erro ao desenvolvedor</a>
                        <br><br>
                    </div>
                </div>
            </div>
            </body>
        </html>
HTML;

        }else{
            $_tmp = explode("-",$this->code);
            $code = isset($_tmp[0]) ? $_tmp[0] : "";

            $html =<<<HTML
            <html>
                <head>
                    <title>Vita: Erro Capturado</title>
                    <style>
                        body{margin:0;padding:0;font-family:"Open Sans",sans-serif;}
                        h1{font-weight: 100;font-size: 50px;color:#555;margin:2% 0 0 5%;}
                        .errwrapper{background-color: #ebebeb;padding: 40px 75px;color: #444;margin-top: 31px;font-family: monaco,courier,monospace;font-size: 15px;}
                    </style>
                </head>
                <body>
                    <div class="container">
                        <h1>Erro {$code}</h1>
                    </div>
                    <div class="errwrapper">
                        Arquivo: {$info->arquivo} <br>
                        Linha: {$this->line}
                    </div>
                </body>
            </html>
HTML;
        }

        return $html;
    }

    public function __toString()
    {
        return $this->getContent();
    }
}
