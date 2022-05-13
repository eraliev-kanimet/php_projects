<?php
/**
 * @param string $view
 * @param string $encoder
 * @return void
 */
function view(string $view, string $encoder = '.php'): void
{
    $file = dirname(__FILE__) . '/views/' .  str_replace('.', '/', $view) . $encoder;
    if (file_exists($file)) {
        include_once $file;
    } else {
        die($file);
    }
}

foreach ($routes as $uri => $view) {
    if (preg_match("#$uri#", trim($_SERVER['REQUEST_URI'], '/'))) {
        view($view);
        die();
    }
}

view('404');

return [
    '' => 'page'
];