<?php
$file = "h:\\SIAP\\resources\\views\\dashboard-v2.blade.php";
if (file_exists($file)) {
    $lines = file($file);
    foreach ($lines as $idx => $line) {
        if (stripos($line, 'updateProgramTable') !== false) {
            echo "Line " . ($idx + 1) . ": " . trim($line) . "\n";
        }
    }
}
