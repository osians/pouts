<?php

namespace Osians\Pouts;

class ShutdownHandler
{
    public static function handler()
    {
        $error = error_get_last();

        if ($error["type"] == E_ERROR) {
            ErrorHandler::handler(
                $error["type"],
                $error["message"],
                $error["file"],
                $error["line"]
            );
        }
    }
}
