<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Парсинг фотографий и схем</title>
</head>
<body>

<pre>

<?php
    define('APP_DIR', realpath(dirname(__FILE__) . '/../app') . '/');
    $config = require_once(APP_DIR . 'config/config.php');

    require_once(APP_DIR . 'config/ErrorHandlingCommon.class.php');
    new ErrorHandlingCommon($config);

    require_once(APP_DIR . 'helpers/ScrapeBillboards.class.php');
    $boards = (new ScrapeBillboards($config))->scrape();

    print_r($boards);
?>

</pre>

</body>
</html>
