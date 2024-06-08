<?php
$request = $_SERVER['REQUEST_URI'];

$urlComponents = parse_url($request);
$path = pathinfo($urlComponents['path'], PATHINFO_FILENAME); // Verwijder de .php extensie

if (isset($urlComponents['query'])) {
    parse_str($urlComponents['query'], $params);
    if (isset($params['category'])) {
        require __DIR__ . '/php/pages/dashboard.php';
    } else {
        switch ($path) {
            case '/' :
            case '' :
                require __DIR__ . '/php/pages/home.php';
                break;
            case 'dashboard' :
                require __DIR__ . '/php/pages/dashboard.php';
                break;
            case 'inloggen' :
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    include __DIR__ . '/php/functions/login_process.php';
                } else {
                    require __DIR__ . '/php/pages/login.php';
                }
                break;
            case 'registreren' :
                require __DIR__ . '/php/pages/register.php';
                break;
            case 'profiel' :
                require __DIR__ . '/php/pages/profile.php';
                break;
            default:
                http_response_code(404);
                require __DIR__ . '/php/pages/404.php';
                break;
        }
    }
} else {
    switch ($path) {
        case '/' :
        case '' :
            require __DIR__ . '/php/pages/home.php';
            break;
        case 'dashboard' :
            require __DIR__ . '/php/pages/dashboard.php';
            break;
        case 'inloggen' :
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                include __DIR__ . '/php/functions/login_process.php';
            } else {
                require __DIR__ . '/php/pages/login.php';
            }
            break;
        case 'registreren' :
            require __DIR__ . '/php/pages/register.php';
            break;
        case 'profiel' :
            require __DIR__ . '/php/pages/profile.php';
            break;
        default:
            http_response_code(404);
            require __DIR__ . '/php/pages/404.php';
            break;
    }
}