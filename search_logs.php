<?php
$log_file = "C:\\Users\\admin\\.gemini\\antigravity\\brain\\d492be2e-f3b2-4bf3-82e1-721fe27865ca\\.system_generated\\logs\\transcript.jsonl";
if (file_exists($log_file)) {
    $lines = file($log_file);
    foreach ($lines as $line) {
        if (stripos($line, 'DashboardV2Controller.php') !== false) {
            $data = json_decode($line, true);
            $step = $data['step_index'] ?? 0;
            if ($step < 390) {
                echo "STEP: $step | TYPE: " . ($data['type'] ?? '') . "\n";
                if (isset($data['tool_calls'])) {
                    foreach ($data['tool_calls'] as $tc) {
                        echo "TOOL: " . $tc['name'] . "\n";
                        echo "ARGS: " . json_encode($tc['args'], JSON_PRETTY_PRINT) . "\n\n";
                    }
                }
            }
        }
    }
}
