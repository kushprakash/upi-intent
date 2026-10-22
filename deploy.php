<?php
/**
 * Auto-Deployment Script for cPanel / Live Server
 * Place this file in public_html (or your web root)
 * 
 * Usage in Browser:
 * https://your-domain.com/deploy.php?token=upi_secure_token_9835
 * 
 * Optional (for private repos with token):
 * https://your-domain.com/deploy.php?token=upi_secure_token_9835&git_token=ghp_xxxx
 */

// Secret Token to secure deployment url
$secret_token = 'upi_secure_token_9835';

// Verify token via GET or POST
$token_received = $_GET['token'] ?? $_POST['token'] ?? null;

if ($token_received !== $secret_token) {
    http_response_code(403);
    echo '<div style="font-family:sans-serif; text-align:center; padding:50px;">';
    echo '<h1 style="color:#e74c3c;">403 - Access Denied</h1>';
    echo '<p style="color:#555;">Invalid or missing secret deployment token.</p>';
    echo '</div>';
    exit;
}

// Execution time limit
set_time_limit(300);

// Project path on server (current directory where deploy.php is located)
$project_path = __DIR__;

// Optional GitHub Access Token (e.g. &git_token=ghp_...)
$git_token = $_GET['git_token'] ?? $_GET['github_token'] ?? null;
$repo_url = "https://github.com/kushprakash/upi-intent.git";

if (!empty($git_token)) {
    $repo_url = "https://{$git_token}@github.com/kushprakash/upi-intent.git";
}

// Set up environment paths and HOME for cPanel git execution
$home_dir = dirname($project_path);
putenv("HOME={$home_dir}");
putenv("PATH=" . (getenv("PATH") ?: "") . ":/usr/local/bin:/usr/bin:/bin:/usr/sbin:/sbin:/usr/local/cpanel/3rdparty/bin");

// Check if exec is enabled
$exec_enabled = function_exists('exec') && !in_array('exec', array_map('trim', explode(',', ini_get('disable_functions'))));

// Check if .git exists, if not initialize repository
$is_git_repo = is_dir($project_path . '/.git');

$commands = [];

$commands['System Diagnostic'] = 'echo "User: $(whoami)"; echo "PWD: $(pwd)"; echo "Git binary: $(which git 2>&1)"; git --version 2>&1';
$commands['Git Safe Directory'] = 'git config --global --add safe.directory "*"';
$commands['Remove Index Lock'] = 'rm -f .git/index.lock';

if (!$is_git_repo) {
    $commands['Initialize Git'] = 'git init';
    $commands['Set Remote Origin'] = "git remote add origin {$repo_url} 2>&1 || git remote set-url origin {$repo_url} 2>&1";
    $commands['Fetch Origin'] = 'git fetch origin main 2>&1';
    $commands['Checkout & Hard Reset'] = 'git reset --hard origin/main 2>&1 && git branch -M main 2>&1';
} else {
    if (!empty($git_token)) {
        $commands['Update Remote URL with Token'] = "git remote set-url origin {$repo_url} 2>&1";
    }
    $commands['Fetch Latest Changes'] = 'git fetch origin main 2>&1 || git fetch --all 2>&1';
    $commands['Force Reset to Origin Main'] = 'git reset --hard origin/main 2>&1';
}

$commands['Show Latest Commit'] = 'git log -1 --stat 2>&1';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UPI-Intent Live Deployment</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #0f172a;
            color: #e2e8f0;
            padding: 30px 20px;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: #1e293b;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            border: 1px solid #334155;
        }
        h1 {
            color: #38bdf8;
            font-size: 24px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .subtitle {
            color: #94a3b8;
            font-size: 14px;
            margin-bottom: 25px;
            border-bottom: 1px solid #334155;
            padding-bottom: 15px;
        }
        .step {
            margin-bottom: 20px;
        }
        .step-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }
        .step-title {
            font-weight: 600;
            font-size: 15px;
            color: #f1f5f9;
        }
        .badge {
            font-size: 12px;
            padding: 3px 10px;
            border-radius: 20px;
            font-weight: 600;
        }
        .badge-success { background: #065f46; color: #34d399; }
        .badge-fail { background: #7f1d1d; color: #f87171; }
        pre {
            background: #090d16;
            color: #a5f3fc;
            padding: 12px 16px;
            border-radius: 8px;
            font-family: 'Consolas', 'Courier New', monospace;
            font-size: 13px;
            overflow-x: auto;
            border: 1px solid #1e293b;
            line-height: 1.5;
            white-space: pre-wrap;
            word-break: break-all;
        }
        .summary-box {
            margin-top: 30px;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
        }
        .summary-success {
            background: #064e3b;
            color: #6ee7b7;
            border: 1px solid #059669;
        }
        .summary-fail {
            background: #7f1d1d;
            color: #fca5a5;
            border: 1px solid #dc2626;
        }
        .footer {
            margin-top: 25px;
            text-align: center;
            color: #64748b;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 UPI-Intent Live Deployment System</h1>
        <div class="subtitle">
            Target Directory: <code><?= htmlspecialchars($project_path) ?></code> | Branch: <code>main</code>
        </div>

        <?php
        $disabled_funcs = ini_get('disable_functions');
        $disabled_arr = array_map('trim', explode(',', strtolower($disabled_funcs)));
        $is_exec_disabled = in_array('exec', $disabled_arr) || !function_exists('exec');

        if ($is_exec_disabled) {
            echo "<div class='summary-box summary-fail' style='margin-bottom:25px; text-align:left;'>";
            echo "⚠️ <strong style='font-size:20px;'>PHP exec() is DISABLED on this server!</strong><br><br>";
            echo "<span style='font-size:14px; font-weight:normal; line-height:1.6;'>";
            echo "cPanel security settings me PHP se system commands (exec, shell_exec) chalana band hai.<br>";
            echo "<strong>Server Disabled Functions:</strong> <code>" . htmlspecialchars($disabled_funcs ?: 'None reported') . "</code><br><br>";
            echo "<strong>Ise fix karne ke liye:</strong><br>";
            echo "1. cPanel me <strong>Select PHP Version</strong> ya <strong>MultiPHP INI Editor</strong> me jayein.<br>";
            echo "2. <strong>disable_functions</strong> list se <code>exec</code> aur <code>shell_exec</code> hata dein (ya cPanel Git Version Control use karein).";
            echo "</span></div>";
        }

        $has_error = false;

        foreach ($commands as $name => $cmd) {
            echo "<div class='step'>";
            $output = [];
            $return_var = 0;
            
            // Execute command in project path
            exec("cd {$project_path} && ({$cmd}) 2>&1", $output, $return_var);
            
            $status_class = ($return_var === 0) ? 'badge-success' : 'badge-fail';
            $status_text = ($return_var === 0) ? 'SUCCESS' : "FAILED (code {$return_var})";

            echo "<div class='step-header'>";
            echo "<span class='step-title'>⚡ {$name}</span>";
            echo "<span class='badge {$status_class}'>{$status_text}</span>";
            echo "</div>";

            $out_text = trim(implode("\n", $output));
            if (empty($out_text)) {
                $out_text = "(Command completed with no output)";
            }
            echo "<pre>" . htmlspecialchars($out_text) . "</pre>";
            echo "</div>";

            if ($return_var !== 0) {
                $has_error = true;
                break; // Stop further commands on error
            }
        }

        if (!$has_error) {
            echo "<div class='summary-box summary-success'>";
            echo "🎉 DEPLOYMENT SUCCESSFUL — All code pulled & synced!";
            echo "</div>";
        } else {
            echo "<div class='summary-box summary-fail'>";
            echo "❌ DEPLOYMENT FAILED — Please check error logs above.";
            echo "</div>";
        }
        ?>

        <div class="footer">
            Server Time: <?= date("Y-m-d H:i:s") ?> | UPI Intent Auto-Deployer
        </div>
    </div>
</body>
</html>
