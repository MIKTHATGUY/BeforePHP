<?php
/**
 * FileHandler Demo Page
 * Demonstrates file operations and schema-based parsing
 */

use NextPHP\Core\FileHandler;

$fileHandler = new FileHandler(__DIR__ . '/../../storage');

// Example 1: Parse users with schema
$usersSchema = [
    'delimiter' => '|',
    'fields' => ['name', 'email', 'age', 'city'],
    'skip_header' => true,
    'validate' => [
        'age' => 'int'
    ]
];

try {
    $users = $fileHandler->parse('users.txt', $usersSchema);
} catch (Exception $e) {
    $users = [];
    $error = $e->getMessage();
}

// Example 2: Parse config with structured sections
$configSchema = [
    'type' => 'structured',
    'sections' => [
        'app' => [
            'start' => '===CONFIG===',
            'end' => '===CONFIG===',
            'extract' => 'key_value'
        ],
        'database' => [
            'start' => '===DATABASE===',
            'end' => '===DATABASE===',
            'extract' => 'key_value'
        ],
        'cache' => [
            'start' => '===CACHE===',
            'end' => '===CACHE===',
            'extract' => 'key_value'
        ],
        'mail' => [
            'start' => '===MAIL===',
            'end' => '===MAIL===',
            'extract' => 'key_value'
        ]
    ]
];

try {
    $config = $fileHandler->parse('config.txt', $configSchema);
} catch (Exception $e) {
    $config = [];
}

// Get file info
$usersInfo = $fileHandler->info('users.txt');
$configInfo = $fileHandler->info('config.txt');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FileHandler Demo - NextPHP</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f5f5f5;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        h1 {
            color: #2c3e50;
            border-bottom: 3px solid #3498db;
            padding-bottom: 10px;
        }
        h2 {
            color: #34495e;
            margin-top: 30px;
        }
        .section {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            text-align: left;
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #f8f9fa;
            font-weight: 600;
            color: #555;
        }
        tr:hover {
            background: #f8f9fa;
        }
        .code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
            font-size: 0.9em;
        }
        .config-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 15px;
        }
        .config-card {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            border-left: 4px solid #3498db;
        }
        .config-card h3 {
            margin-top: 0;
            color: #2c3e50;
        }
        .config-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .config-item:last-child {
            border-bottom: none;
        }
        .label {
            font-weight: 500;
            color: #666;
        }
        .value {
            color: #2c3e50;
            font-family: 'Courier New', monospace;
        }
        .file-info {
            background: #e8f4f8;
            padding: 10px 15px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        .success {
            color: #27ae60;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>FileHandler Demo</h1>
        
        <div class="section">
            <h2>Example 1: Delimited File Parsing (users.txt)</h2>
            <div class="file-info">
                <strong>Schema:</strong> <span class="code">delimiter='|', fields=['name', 'email', 'age', 'city']</span>
                <br>
                <strong>File Size:</strong> <?= number_format($usersInfo['size']) ?> bytes | 
                <strong>Modified:</strong> <?= date('Y-m-d H:i:s', $usersInfo['modified']) ?>
            </div>
            
            <?php if (isset($error)): ?>
                <p style="color: #e74c3c;">Error: <?= htmlspecialchars($error) ?></p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Age</th>
                            <th>City</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['name']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= $user['age'] ?></td>
                            <td><?= htmlspecialchars($user['city']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <p class="success">✓ Parsed <?= count($users) ?> users with type validation (age as int)</p>
            <?php endif; ?>
        </div>
        
        <div class="section">
            <h2>Example 2: Structured Text Parsing (config.txt)</h2>
            <div class="file-info">
                <strong>Schema:</strong> <span class="code">type='structured'</span> with 4 sections
                <br>
                <strong>File Size:</strong> <?= number_format($configInfo['size']) ?> bytes | 
                <strong>Modified:</strong> <?= date('Y-m-d H:i:s', $configInfo['modified']) ?>
            </div>
            
            <div class="config-grid">
                <?php foreach ($config as $sectionName => $sectionData): ?>
                <div class="config-card">
                    <h3><?= ucfirst($sectionName) ?> Configuration</h3>
                    <?php foreach ($sectionData as $key => $value): ?>
                    <div class="config-item">
                        <span class="label"><?= htmlspecialchars($key) ?>:</span>
                        <span class="value"><?= htmlspecialchars($value) ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="section">
            <h2>File Operations Demo</h2>
            <?php
            // Demo file operations
            $demoContent = "This is a demo file created by FileHandler.\nCreated at: " . date('Y-m-d H:i:s');
            $fileHandler->write('demo/output.txt', $demoContent);
            
            // Append to it
            $fileHandler->append('demo/output.txt', "\nAppended line!");
            
            // Read it back
            $readContent = $fileHandler->read('demo/output.txt');
            
            // List files
            $demoFiles = $fileHandler->list('demo');
            ?>
            
            <h3>Written & Read Content:</h3>
            <pre class="code" style="background: #f4f4f4; padding: 15px; border-radius: 4px; overflow-x: auto;"><?= htmlspecialchars($readContent) ?></pre>
            
            <h3>Files in demo/ directory:</h3>
            <ul>
                <?php foreach ($demoFiles as $file): ?>
                <li><?= htmlspecialchars($file['name']) ?> (<?= number_format($file['size']) ?> bytes)</li>
                <?php endforeach; ?>
            </ul>
        </div>
        
        <div class="section">
            <h2>Available Features</h2>
            <ul>
                <li><strong>Read/Write/Delete:</strong> Basic file operations with security checks</li>
                <li><strong>Schema-based Parsing:</strong> Parse delimited files (CSV, TSV, etc.) with field mapping</li>
                <li><strong>Structured Text:</strong> Extract sections from marked text files</li>
                <li><strong>Validation:</strong> Type casting and validation (int, float, bool, email, url, date)</li>
                <li><strong>Upload Handling:</strong> Secure file upload with extension whitelisting</li>
                <li><strong>Download:</strong> Serve files with proper headers</li>
                <li><strong>Security:</strong> Directory traversal protection, extension validation</li>
            </ul>
        </div>
    </div>
</body>
</html>
