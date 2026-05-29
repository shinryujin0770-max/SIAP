<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');
$app->instance('request', Illuminate\Http\Request::create('/dashboard-v2', 'GET'));
$kernel->bootstrap();

$user = \App\Models\User::where('username', 'nanang_as')->first();
if ($user) {
    auth()->login($user);
}

try {
    $opds = \App\Models\Opd::all();
    $html = view('dashboard-v2', ['opds' => $opds])->render();
    echo "RENDER SUCCESSFUL, HTML LENGTH: " . strlen($html) . "\n";
} catch (\Exception $e) {
    echo "RENDER FAILED: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
