<?php
$log_dir = "C:\\Users\\admin\\.gemini\\antigravity\\brain\\d492be2e-f3b2-4bf3-82e1-721fe27865ca\\.system_generated\\logs\\";
if (is_dir($log_dir)) {
    print_r(scandir($log_dir));
} else {
    echo "Directory not found: $log_dir\n";
}
