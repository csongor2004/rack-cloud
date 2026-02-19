<?php
spl_autoload_register(function ($class) {
    // Kicseréljük az 'App\' prefixet a 'src/' mappára
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/src/';

    // Megnézzük, hogy az osztályunk ezzel a névvel kezdődik-e
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    // A maradék részt átalakítjuk fájl elérési úttá
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // Ha létezik a fájl, betöltjük
    if (file_exists($file)) {
        require $file;
    }
});