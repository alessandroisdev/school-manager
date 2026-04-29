<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$user = App\Domains\Auth\Models\User::first();
auth()->login($user);

$request = Illuminate\Http\Request::create('/admin/units', 'GET');
$request->headers->set('X-Requested-With', 'XMLHttpRequest');

$response = $kernel->handle($request);
echo $response->getContent();
