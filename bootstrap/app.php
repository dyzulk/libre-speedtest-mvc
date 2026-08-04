<?php

require_once __DIR__ . '/../app/Core/Core.php';

// Bootstrap MVC core configurations
\App\Core\Core::bootstrap();

use App\Core\Router;

$router = new Router();

// Load application route definitions
require_once __DIR__ . '/../routes/web.php';

return $router;
