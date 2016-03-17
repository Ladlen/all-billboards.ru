<?php

/**
 * Class ErrorHandling
 *
 * Описывает функции для обработки ошибок и исключений.
 */
abstract class ErrorHandling
{
    abstract public function handleError($errno, $errstr, $errfile, $errline);

    abstract public function handleException($exception);
}