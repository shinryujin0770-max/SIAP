<?php
header('Content-Type: text/plain');
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
echo "before http bootstrap: " . ini_get('max_execution_time') . "\n";
$kernel->bootstrap();
echo "after http bootstrap: " . ini_get('max_execution_time') . "\n";
echo "memory: " . ini_get('memory_limit') . "\n";
