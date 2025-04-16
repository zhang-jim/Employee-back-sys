<?php
function view($view)
{
    $path = str_replace('/', DIRECTORY_SEPARATOR, '/View/' . $view);
    $path = BASE_PATH . $path . '.php';
    if (file_exists($path)) {
        require $path;
    }
}
