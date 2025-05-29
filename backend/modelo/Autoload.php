<?php

session_start();
include_once 'Wconfig.php';
spl_autoload_register(function ($class_name) {
    $class_path = str_replace('\\', '/', $class_name);

    $search_directories = [
        ROOT_PATH,
        ROOT_PATH . 'backend/modelo/',
        ROOT_PATH . '/backend/controle/'
    ];

    foreach ($search_directories as $directory) {
        $file_path = $directory . $class_path . '.php';
        if (file_exists($file_path)) {
            require_once $file_path;
            return;
        }
    }
});
//echo ROOT_PATH;
