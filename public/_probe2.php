<?php
header('Content-Type: text/plain');
echo "before boot: max_execution_time=" . ini_get('max_execution_time') . "\n";
require __DIR__ . '/../vendor/autoload.php';
echo "after autoload: max_execution_time=" . ini_get('max_execution_time') . "\n";
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
echo "after bootstrap: max_execution_time=" . ini_get('max_execution_time') . "\n";
echo "time_limit_func_exists=" . (function_exists('set_time_limit') ? 'yes' : 'no') . "\n";
