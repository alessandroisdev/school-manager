<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

$request = \Illuminate\Http\Request::create('/finance/boleto/2365', 'GET');

// Authentication manually
$user = \App\Domains\Auth\Models\User::find(1);
$app['auth']->guard()->login($user);

// Setup session via middleware
$request->cookies->set($app['config']['session.cookie'], 'fake-session-id');

$response = $kernel->handle($request);

echo "STATUS: " . $response->getStatusCode() . "\n";
echo "HEADERS: \n";
foreach ($response->headers->all() as $name => $values) {
    echo $name . ": " . implode(", ", $values) . "\n";
}
echo "CONTENT LENGTH: " . strlen($response->getContent()) . "\n";
