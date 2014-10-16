<?php
define('PROJECT_DIR', __DIR__);
$loader = require PROJECT_DIR . '/vendor/autoload.php';
$loader->addPsr4('PommProject\\Foundation\\Test\\', PROJECT_DIR.'/vendor/pomm-project/foundation/sources/tests/');
$file = PROJECT_DIR.'/sources/tests/config.php';

if (file_exists($file)) {
    // custom configuration
    require $file;
} else {
    // we are using travis configuration by default
    require PROJECT_DIR.'/sources/tests/config.travis.php';
}

