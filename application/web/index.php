<?php

use testframework\Helper;

spl_autoload_register(function($className) {
    $roots = [
        'testframework' => __DIR__ . '/../../framework',
        'testapplication' => __DIR__ . '/../../application',
    ];

    list($rootNS, $rightPart) = explode('\\', $className, 2);

    if ($rightPart && isset($roots[$rootNS])) {
        $classFile = $roots[$rootNS] . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $rightPart) . '.php';
        if (is_file($classFile)) {
            include($classFile);
            if (!class_exists($className, false) && !interface_exists($className, false) && !trait_exists($className, false)) {
                throw new Exception("Unable to find '$className' in file: $classFile.");
            }
        }
    }
}, true, true);


(function() {
    $appConfig = require(__DIR__ . '/../config/app.php');

    $app = Helper::create($appConfig);
    
    $app->run();
})();