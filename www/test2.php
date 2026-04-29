<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

$request = Illuminate\Http\Request::create('/admin/units', 'GET', ['draw' => 1, 'start' => 0, 'length' => 10]);
$request->headers->set('X-Requested-With', 'XMLHttpRequest');

$controller = new App\Interfaces\Http\Controllers\Admin\UnitController();
$response = $controller->index($request);

echo $response->getContent();
