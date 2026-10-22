<?php
header('Content-Type: text/plain');
echo "max_execution_time=" . ini_get('max_execution_time') . "\n";
flush();
$start = microtime(true);
$i = 0;
while (true) {
    $i++;
    // burn CPU
    for ($k = 0; $k < 300000; $k++) { sqrt($k * $k); }
    if ($i % 5 === 0) {
        $el = microtime(true) - $start;
        echo "busy " . (int)$el . "s\n";
        flush();
        if ($el > 140) break;
    }
}
echo "finished\n";
