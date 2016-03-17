<?php

require_once(APP_DIR . 'helpers/ErrorHandling.class.php');

class ErrorHandlingCommon extends ErrorHandling
{
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;

        if ($config['mode'] == 'debug')
        {
            error_reporting(E_ALL | E_STRICT);
            ini_set('display_errors', true);
            ini_set('display_startup_errors', true);
        }
        else
        {
            error_reporting(0);
        }

        set_error_handler([$this, 'handleError']);
        set_exception_handler([$this, 'handleException']);
    }

    public function handleError($errno, $errstr, $errfile, $errline)
    {
        if ($this->config['mode'] == 'debug')
        {
            echo "Произошла ошибка.\nКод: " . $errno . ".\nСообщение: " . $errstr
                . ".\nФайл: " . $errfile . ".\nСтрока: " . $errline;
        }
        else
        {
            echo "Ошибка на сервере\n";
        }
    }

    public function handleException($exception)
    {
        if ($this->config['mode'] == 'debug')
        {
            echo "Произошло исключение.\nКод: " . $exception->getCode() . ".\nСообщение: " . $exception->getMessage()
                . ".\nФайл: " . $exception->getFile() . ".\nСтрока: " . $exception->getLine()
                . ".\nTrace: " . $exception->getTraceAsString() . "\n";
        }
        else
        {
            echo "Ошибка на сервере\n";
        }
    }
}
