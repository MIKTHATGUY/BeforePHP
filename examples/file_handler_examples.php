<?php
/**
 * FileHandler Usage Examples
 * 
 * This demonstrates all capabilities of the FileHandler library
 */

use NextPHP\Core\FileHandler;

// ==================== Basic Setup ====================

// Create file handler with default settings
$fileHandler = new FileHandler();

// Or with custom options
$fileHandler = new FileHandler(__DIR__ . '/../storage', [
    'allowed_extensions' => ['txt', 'csv', 'json', 'md'],
    'max_file_size' => 5 * 1024 * 1024, // 5MB
    'secure_mode' => true
]);

// ==================== Basic File Operations ====================

// Write to file
$fileHandler->write('users/data.txt', 'Hello World!');

// Read file
$content = $fileHandler->read('users/data.txt');
echo $content; // Hello World!

// Append to file
$fileHandler->append('users/data.txt', "\nNew line added");

// Check if file exists
if ($fileHandler->exists('users/data.txt')) {
    echo "File exists!";
}

// Get file info
$info = $fileHandler->info('users/data.txt');
// Returns: ['name', 'path', 'size', 'modified', 'created', 'extension', 'mime_type', ...]

// List files
$files = $fileHandler->list('users', ['extension' => 'txt']);

// Copy file
$fileHandler->copy('users/data.txt', 'backup/data.txt');

// Move/Rename file
$fileHandler->move('backup/data.txt', 'archive/old_data.txt');

// Delete file
$fileHandler->delete('archive/old_data.txt');

// Download file (sends headers and exits)
// $fileHandler->download('users/data.txt', 'download.txt');

// ==================== Upload Handling ====================

// Handle file upload from form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['document'])) {
    try {
        $result = $fileHandler->upload($_FILES['document'], 'uploads', 'custom_name.txt');
        echo "Uploaded to: " . $result['path'];
    } catch (Exception $e) {
        echo "Upload failed: " . $e->getMessage();
    }
}

// ==================== Schema-Based Parsing Examples ====================

// Example 1: CSV-like file with custom delimiter
// File content (users.txt):
// name|email|age
// John Doe|john@example.com|30
// Jane Smith|jane@example.com|25

$data = $fileHandler->parse('users.txt', [
    'delimiter' => '|',
    'fields' => ['name', 'email', 'age'],
    'skip_header' => true,
    'validate' => [
        'age' => 'int',
        'email' => 'email'
    ]
]);

// Result:
// [
//   ['name' => 'John Doe', 'email' => 'john@example.com', 'age' => 30],
//   ['name' => 'Jane Smith', 'email' => 'jane@example.com', 'age' => 25]
// ]

// Example 2: Simple list without headers
// File content (items.txt):
// Apple
// Banana
// Cherry

$items = $fileHandler->parse('items.txt', [
    'delimiter' => "\n",
    'fields' => ['item'],
    'skip_header' => false
]);

// Example 3: Structured text file with sections
// File content (config.txt):
// ===CONFIG===
// app_name=MyApp
// version=1.0.0
// debug=true
// ===CONFIG===
//
// ===DATABASE===
// host=localhost
// port=3306
// name=mydb
// ===DATABASE===

$config = $fileHandler->parse('config.txt', [
    'type' => 'structured',
    'sections' => [
        'config' => [
            'start' => '===CONFIG===',
            'end' => '===CONFIG===',
            'extract' => 'key_value'
        ],
        'database' => [
            'start' => '===DATABASE===',
            'end' => '===DATABASE===',
            'extract' => 'key_value'
        ]
    ]
]);

// Result:
// [
//   'config' => ['app_name' => 'MyApp', 'version' => '1.0.0', 'debug' => 'true'],
//   'database' => ['host' => 'localhost', 'port' => '3306', 'name' => 'mydb']
// ]

// Example 4: JSON within structured markers
// File content (data.txt):
// [JSON]
// {"users": [{"name": "John"}, {"name": "Jane"}]}
// [JSON]

$jsonData = $fileHandler->parse('data.txt', [
    'type' => 'structured',
    'sections' => [
        'json' => [
            'start' => '[JSON]',
            'end' => '[JSON]',
            'extract' => 'json'
        ]
    ]
]);

// Example 5: Multi-line sections
// File content (notes.txt):
// ---NOTES---
// First note here
// Second note here
// Third note here
// ---NOTES---

$notes = $fileHandler->parse('notes.txt', [
    'type' => 'structured',
    'sections' => [
        'notes' => [
            'start' => '---NOTES---',
            'end' => '---NOTES---',
            'extract' => 'lines'
        ]
    ]
]);

// Result:
// ['notes' => ['First note here', 'Second note here', 'Third note here']]

// ==================== Saving Data with Schema ====================

// Save data back to file with delimited format
$users = [
    ['name' => 'Alice', 'email' => 'alice@example.com', 'age' => 28],
    ['name' => 'Bob', 'email' => 'bob@example.com', 'age' => 35]
];

$fileHandler->saveParsed('output/users.txt', $users, [
    'delimiter' => '|',
    'fields' => ['name', 'email', 'age'],
    'include_header' => true
]);

// Result file content:
// name|email|age
// Alice|alice@example.com|28
// Bob|bob@example.com|35

// Save structured data
$configData = [
    'config' => [
        'app_name' => 'MyApp',
        'version' => '2.0.0',
        'debug' => 'false'
    ],
    'database' => [
        'host' => '127.0.0.1',
        'port' => '5432',
        'name' => 'production_db'
    ]
];

$fileHandler->saveParsed('output/config.txt', $configData, [
    'type' => 'structured',
    'sections' => [
        'config' => [
            'start' => '===CONFIG===',
            'end' => '===CONFIG===',
            'format' => 'key_value'
        ],
        'database' => [
            'start' => '===DATABASE===',
            'end' => '===DATABASE===',
            'format' => 'key_value'
        ]
    ]
]);

// Result file content:
// ===CONFIG===
// app_name=MyApp
// version=2.0.0
// debug=false
// ===CONFIG===
//
// ===DATABASE===
// host=127.0.0.1
// port=5432
// name=production_db
// ===DATABASE===

// ==================== Available Validation Types ====================

$schema = [
    'fields' => ['name', 'email', 'age', 'price', 'is_active', 'website'],
    'validate' => [
        'age' => 'int',           // Converts to integer
        'price' => 'float',       // Converts to float
        'is_active' => 'bool',    // Converts to boolean
        'email' => 'email',       // Validates email format
        'website' => 'url',       // Validates URL format
        'name' => 'trim',         // Trims whitespace
        'code' => 'uppercase',    // Converts to uppercase
        'status' => 'lowercase',  // Converts to lowercase
        'birthdate' => 'date'     // Validates and formats as Y-m-d
    ]
];

// ==================== Security Features ====================

// Secure mode prevents directory traversal attacks
$fileHandler = new FileHandler(__DIR__ . '/../storage', [
    'secure_mode' => true,  // Prevents ../ and ./ in paths
    'allowed_extensions' => ['txt', 'csv', 'json']  // Whitelist extensions
]);

// These will throw exceptions in secure mode:
// $fileHandler->read('../../etc/passwd');  // Directory traversal blocked
// $fileHandler->write('shell.php', '<?php ...');  // Extension not allowed

// ==================== Error Handling ====================

try {
    $content = $fileHandler->read('nonexistent.txt');
} catch (RuntimeException $e) {
    echo "File error: " . $e->getMessage();
} catch (InvalidArgumentException $e) {
    echo "Validation error: " . $e->getMessage();
}
