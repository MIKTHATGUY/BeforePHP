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
        .copy-btn {
            background: #3498db;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            margin-left: 10px;
        }
        .copy-btn:hover {
            background: #2980b9;
        }
        .copy-btn.copied {
            background: #27ae60;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <h1>⚠️ Something went wrong!</h1>
        
        <div class="error-message" id="errorMessage">
            <strong>Error:</strong> <?= htmlspecialchars($errorMessage) ?>
            <button class="copy-btn" onclick="copyError()" id="copyBtn">Copy</button>
        </div>
        
        <p>An error occurred while loading this page. Please try again or contact support if the problem persists.</p>
        
        <button class="reset-btn" onclick="window.location.reload()">Try Again</button>
        
        <?php if (ini_get('display_errors')): ?>
        <details class="error-details" id="errorDetails">
            <summary>Debug Information (visible in development mode)</summary>
            <p><strong>File:</strong> <?= htmlspecialchars($errorFile) ?></p>
            <p><strong>Line:</strong> <?= $errorLine ?></p>
            <pre><?= htmlspecialchars($errorTrace) ?></pre>
        </details>
        <?php endif; ?>
    </div>
    <script>
        function copyError() {
            let fullError = '';
            
            // Add main error message
            const errorMessage = document.getElementById('errorMessage').innerText.replace('Copy', '').trim();
            fullError += errorMessage + '\n\n';
            
            // Add debug information if available
            const errorDetails = document.getElementById('errorDetails');
            if (errorDetails) {
                const paragraphs = errorDetails.querySelectorAll('p');
                const trace = errorDetails.querySelector('pre');
                
                paragraphs.forEach(p => {
                    fullError += p.innerText + '\n';
                });
                
                if (trace) {
                    fullError += '\nStack Trace:\n' + trace.innerText;
                }
            }
            
            navigator.clipboard.writeText(fullError).then(() => {
                const btn = document.getElementById('copyBtn');
                btn.textContent = 'Copied!';
                btn.classList.add('copied');
                setTimeout(() => {
                    btn.textContent = 'Copy';
                    btn.classList.remove('copied');
                }, 2000);
            });
        }
    </script>
</body>
</html>
