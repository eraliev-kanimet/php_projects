<?php

/**
 * @param string $view
 * @param string $encoder
 * @return void
 */
function view(string $view, string $encoder = '.php'): void
{
    $file = dirname(__FILE__) . '/' .  str_replace('.', '/', $view) . $encoder;
    if (file_exists($file)) {
        include $file;
    } else {
        die($file . ' does not exist!!');
    }
}

/**
 * @param array $routes
 * @return void
 */
function run(array $routes): void
{
    krsort($routes);
    foreach ($routes as $uri => $view) {
        if (preg_match("#^$uri$#", strtok(trim($_SERVER['REQUEST_URI'], '/'), '?'))) {
            view($view);
            break;
        }
    }
}

$routes = [
    '' => 'page'
];

run($routes);