<?php
// Error boundary - catches errors in this segment and all nested segments
// Variables available: $errorMessage, $errorCode, $errorFile, $errorLine, $errorTrace
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error - Something went wrong</title>
    <style>
        body {
            font-family: system-ui, -apple-system, sans-serif;
            background: #fafafa;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        .error-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 40px;
            max-width: 600px;
            width: 100%;
        }
        h1 {
            color: #e74c3c;
            margin: 0 0 20px 0;
        }
        .error-message {
            background: #fee;
            border-left: 4px solid #e74c3c;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .error-details {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        .error-details summary {
            cursor: pointer;
            color: #666;
        }
        .error-details pre {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
            font-size: 12px;
            line-height: 1.5;
        }
        .reset-btn {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
        }
        .reset-btn:hover {
            background: #c0392b;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <h1>⚠️ Something went wrong!</h1>
        
        <div class="error-message">
            <strong>Error:</strong> <?= htmlspecialchars($errorMessage) ?>
        </div>
        
        <p>An error occurred while loading this page. Please try again or contact support if the problem persists.</p>
        
        <button class="reset-btn" onclick="window.location.reload()">Try Again</button>
        
        <?php if (ini_get('display_errors')): ?>
        <details class="error-details">
            <summary>Debug Information (visible in development mode)</summary>
            <p><strong>File:</strong> <?= htmlspecialchars($errorFile) ?></p>
            <p><strong>Line:</strong> <?= $errorLine ?></p>
            <pre><?= htmlspecialchars($errorTrace) ?></pre>
        </details>
        <?php endif; ?>
    </div>
</body>
</html>
