<?php

define('APP_DIR', dirname(__FILE__) . '/../app/');
$config = require_once(APP_DIR . 'config.php');

if ($config->mode = 'debug')
{
    error_reporting(E_ALL);
    ini_set('display_errors', true);
    ini_set('display_startup_errors', true);
}
else
{
    error_reporting(0);
}

try
{
    $app = new Application($config)->run();
}
catch (Exception $e)
{
    if ($config->mode = 'debug')
    {
        echo '��������� ������. ���: ' . $e->getCode() . "\n. ���������: " . $e->getMessage() .
            "\n. ����: " . $e->getFile() . "\n. ������: " . $e->getFile() . "\n. Trace: " . $e->getTraceAsString() . "\n";
    }
    else
    {
        echo "������ �� �������\n";
    }
}

