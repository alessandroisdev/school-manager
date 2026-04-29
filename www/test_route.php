<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

$request = \Illuminate\Http\Request::create('/finance/boleto/2365', 'GET');

// Simulate auth
$user = \App\Domains\Auth\Models\User::find(1);
$app['auth']->guard()->setUser($user);

// Start session
$request->setLaravelSession($app['session']->driver('array'));
$request->session()->start();
$request->session()->put('active_unit_id', 3);

$response = $kernel->handle($request);

echo "STATUS: " . $response->getStatusCode() . "\n";
echo "HEADERS:\n" . $response->headers . "\n";
echo "CONTENT: " . substr($response->getContent(), 0, 500) . "\n";
