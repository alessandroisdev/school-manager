<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

$user = App\Domains\Auth\Models\User::first();
auth()->login($user);

$request = Illuminate\Http\Request::create('/admin/units?draw=1&start=0&length=10', 'GET');
$request->headers->set('X-Requested-With', 'XMLHttpRequest');
$request->headers->set('Accept', 'application/json');

$response = $kernel->handle($request);
echo $response->getContent();
