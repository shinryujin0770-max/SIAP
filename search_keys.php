<?php
$file = "h:\\SIAP\\resources\\views\\dashboard-v2.blade.php";
if (file_exists($file)) {
    $content = file_get_contents($file);
    preg_match_all('/item\.[a-zA-Z0-9_]+/i', $content, $matches);
    print_r(array_unique($matches[0]));
} else {
    echo "File not found\n";
}
