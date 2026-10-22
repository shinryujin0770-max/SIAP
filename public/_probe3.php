<?php
header('Content-Type: text/plain');
echo "max_execution_time=" . ini_get('max_execution_time') . "\n";
echo "max_input_time=" . ini_get('max_input_time') . "\n";
flush();
for ($i = 1; $i <= 12; $i++) {
    sleep(10);
    echo "alive after " . ($i * 10) . "s\n";
    flush();
}
echo "done 120s\n";
