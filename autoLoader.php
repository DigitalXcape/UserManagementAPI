<?php
function autoloader($className) {
    // Convert namespace to directory structure
    $className = str_replace('\\', DIRECTORY_SEPARATOR, $className);
    
    // Set base directories for different class types
    $baseDirs = [
        __DIR__ . '/model/', // For models
        __DIR__ . '/classes/', // For classes
        __DIR__ . '/logger/' // For logger classes
    ];
    
    foreach ($baseDirs as $baseDir) {
        $file = $baseDir . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return; // Exit after loading the file
        }
    }
    
    // Log error if class file not found
    error_log("Class file for $className not found!");
}

// Register the autoloader
spl_autoload_register('autoloader');
?>