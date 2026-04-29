<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

$request = \Illuminate\Http\Request::create('/finance/debug-invoice/2365', 'GET');

// Handle the request directly
$response = $kernel->handle($request);

echo "STATUS: " . $response->getStatusCode() . "\n";
echo "HEADERS: \n";
foreach ($response->headers->all() as $name => $values) {
    echo $name . ": " . implode(", ", $values) . "\n";
}
echo "CONTENT LENGTH: " . strlen($response->getContent()) . "\n";
