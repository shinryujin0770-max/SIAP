import json
import os

log_file = r"C:\Users\admin\.gemini\antigravity\brain\d492be2e-f3b2-4bf3-82e1-721fe27865ca\.system_generated\logs\transcript.jsonl"
if os.path.exists(log_file):
    with open(log_file, 'r', encoding='utf-8') as f:
        for line in f:
            try:
                data = json.loads(line)
                if 'tool_calls' in data:
                    for tc in data['tool_calls']:
                        if 'TargetFile' in tc.get('arguments', {}) and 'DashboardV2Controller' in tc['arguments']['TargetFile']:
                            print("STEP:", data.get('step_index'))
                            print("ARGS:", json.dumps(tc['arguments'], indent=2))
            except Exception as e:
                pass
else:
    print("Log file not found")
