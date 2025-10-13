<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;

$controller = app()->make(App\Http\Controllers\DepartmentController::class);
$req = Request::create('/departments', 'POST', ['dept_code' => 'BSPSY', 'dept_name' => 'Psychology Test']);
$res = $controller->store($req);

// Print response
if ($res instanceof Illuminate\Http\JsonResponse || method_exists($res, 'getContent')) {
    echo $res->getContent();
} else {
    var_dump($res);
}

// Print last 20 lines of log
$log = __DIR__ . '/../storage/logs/laravel.log';
if (file_exists($log)) {
    $lines = array_slice(file($log), -20);
    echo "\n\n---- last log lines ----\n" . implode('', $lines);
}
