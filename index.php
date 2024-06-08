<?php
$request = $_SERVER['REQUEST_URI'];

$urlComponents = parse_url($request);
$path = trim($urlComponents['path'], '/'); // Verwijder leidende en sluitende slashes

if (isset($urlComponents['query'])) {
    parse_str($urlComponents['query'], $params);
    if (isset($params['category'])) {
        require __DIR__ . '/php/pages/dashboard.php';
    } else {
        switch ($path) {
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
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    include __DIR__ . '/php/functions/register_process.php';
                } else {
                    require __DIR__ . '/php/pages/register.php';
                }
                break;
            case 'profiel' :
                require __DIR__ . '/php/pages/profile.php';
                break;
            case 'profiel/update' :
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    include __DIR__ . '/php/functions/update_profile_process.php';
                } else {
                    require __DIR__ . '/php/pages/profile_update.php';
                }
                break;
            case 'uitloggen' :
                include __DIR__ . '/php/functions/logout.php';
                break;
            default:
                http_response_code(404);
                require __DIR__ . '/php/pages/404.php';
                break;
        }
    }
} else {
    switch ($path) {
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
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                include __DIR__ . '/php/functions/register_process.php';
            } else {
                require __DIR__ . '/php/pages/register.php';
            }
            break;
        case 'profiel' :
            require __DIR__ . '/php/pages/profile.php';
            break;
        case 'profiel/update' :
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                include __DIR__ . '/php/functions/update_profile_process.php';
            } else {
                require __DIR__ . '/php/pages/profile_update.php';
            }
            break;
        case 'uitloggen' :
            include __DIR__ . '/php/functions/logout.php';
            break;
        default:
            http_response_code(404);
            require __DIR__ . '/php/pages/404.php';
            break;
    }
}
?>
