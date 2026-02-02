# NextPHP Framework

A lightweight, file-based PHP framework inspired by Next.js. No complex routing configuration needed - just create files and folders.

## Overview

NextPHP is a modern PHP framework that uses a file-system based routing approach similar to Next.js. Pages are automatically routed based on their file location, with support for layouts, error boundaries, and dynamic rendering.

## Features

- **File-Based Routing** - Routes are determined by file structure
- **Dynamic Routes** - `[slug]`, `[...slug]`, and `[[...slug]]` patterns like Next.js
- **Route Groups** - Organize routes without affecting URL (e.g., `(auth)/login` → `/login`)
- **Controller + View Pattern** - Separate logic (`page.php`) from presentation (`page.html.php`)
- **Layout Inheritance** - Nested layouts with automatic inheritance
- **Error Boundaries** - `error.php` files catch errors in segments and nested routes
- **404 Handling** - Automatic 404 page resolution via DFS
- **FileHandler Library** - Comprehensive file operations with schema-based parsing
- **Zero Configuration** - Works out of the box with sensible defaults

## Installation

```bash
git clone https://github.com/yourusername/nextphp.git
cd nextphp
```

Point your web server to the `public/` directory.

## Directory Structure

```
nextphp/
├── config.php              # Configuration
├── core/
│   ├── App.php            # Application entry point
│   ├── Router.php         # File-based routing
│   ├── Page.php           # Page rendering
│   ├── Config.php         # Configuration management
│   └── FileHandler.php    # File operations library
├── app/                 # All routes go here
│   ├── page.php           # Home page controller
│   ├── page.html.php      # Home page view
│   ├── layout.php         # Root layout
│   ├── error.php          # Root error boundary
│   ├── (codes)/           # Route group (URL: /404, not /codes/404)
│   │   ├── layout.php
│   │   └── 404/
│   │       ├── page.php
│   │       └── page.html.php
│   ├── about/
│   │   ├── page.php
│   │   └── page.html.php
│   ├── blog/
│   │   ├── page.php       # Blog index
│   │   ├── page.html.php
│   │   └── [slug]/        # Dynamic route
│   │       ├── page.php
│   │       └── page.html.php
│   ├── shop/
│   │   └── [...slug]/     # Catch-all route
│   │       ├── page.php
│   │       └── page.html.php
│   └── docs/
│       └── [[...slug]]/   # Optional catch-all
│           ├── page.php
│           └── page.html.php
├── storage/               # FileHandler storage directory
├── public/                # Web server root
│   └── index.php         # Entry point
└── examples/             # Usage examples
```

## Routing

Routes are automatically created based on the file structure in the `app/` directory.

### Basic Routes

```
app/
├── page.php              → /
├── about/
│   └── page.php          → /about
└── blog/
    └── page.php          → /blog
```

### Route Groups

Wrap folder names in parentheses to create route groups that don't affect the URL:

```
app/
└── (codes)/              → Group folder (stripped from URL)
    └── 404/
        └── page.php      → /404 (not /codes/404)
```

### 404 Handling

If a route is not found, the router automatically searches for a `404` page:

```
app/
└── (codes)/
    └── 404/
        └── page.php      → Served when route not found
```

## Dynamic Routes

Dynamic routes are created using square brackets around folder names. Access route parameters directly as variables in your controller (e.g., `$slug`, `$id`).

### Single Dynamic Segment `[slug]`

```
app/
└── blog/
    └── [slug]/            → Dynamic route
        ├── page.php
        └── page.html.php
```

**URL Examples:**
| URL | `slug` value |
|-----|-------------|
| `/blog/hello-world` | `$slug = 'hello-world'` |
| `/blog/my-post` | `$slug = 'my-post'` |

**Controller (page.php):**
```php
<?php
// Access slug directly as a variable
$postTitle = ucwords(str_replace('-', ' ', $slug));
$postContent = "Content for: {$slug}";
```

**View (page.html.php):**
```php
<h1><?= htmlspecialchars($postTitle) ?></h1>
<p>Slug: <?= htmlspecialchars($slug) ?></p>
```

### Catch-all Segments `[...slug]`

Matches all subsequent segments. Requires at least one segment.

```
app/
└── shop/
    └── [...slug]/         → Catch-all route
        ├── page.php
        └── page.html.php
```

**URL Examples:**
| URL | `slug` value |
|-----|-------------|
| `/shop/clothes` | `$slug = ['clothes']` |
| `/shop/clothes/tops` | `$slug = ['clothes', 'tops']` |
| `/shop/a/b/c` | `$slug = ['a', 'b', 'c']` |

**Controller (page.php):**
```php
<?php
// $slug is an array of segments
$categoryPath = implode(' / ', $slug);
$lastCategory = end($slug);
```

### Optional Catch-all Segments `[[...slug]]`

Matches the route with or without segments.

```
app/
└── docs/
    └── [[...slug]]/       → Optional catch-all
        ├── page.php
        └── page.html.php
```

**URL Examples:**
| URL | `slug` value |
|-----|-------------|
| `/docs` | `$slug = null` (no segments) |
| `/docs/getting-started` | `$slug = ['getting-started']` |
| `/docs/api/routing` | `$slug = ['api', 'routing']` |

**Controller (page.php):**
```php
<?php
$hasSlugs = $slug !== null && !empty($slug);
$pageTitle = $hasSlugs ? implode(' / ', $slug) : 'Documentation Home';
```

## Page Structure

Each page can have up to three files:

### 1. page.php (Controller)

Contains PHP logic, data fetching, and variable setup. Runs before the view.

```php
<?php
// app/users/page.php

use NextPHP\Core\FileHandler;

$fileHandler = new FileHandler(__DIR__ . '/../../storage');

// Fetch data
$users = $fileHandler->parse('users.txt', [
    'delimiter' => '|',
    'fields' => ['name', 'email', 'age'],
    'skip_header' => true,
    'validate' => ['age' => 'int']
]);

// Set page title
$pageTitle = 'User List';
```

### 2. page.html.php (View)

Contains HTML and presentation logic. Has access to variables from `page.php`.

```php
<?php
// app/users/page.html.php
// Variables from page.php are available here: $users, $pageTitle
?>
<h1><?= htmlspecialchars($pageTitle) ?></h1>
<table>
    <?php foreach ($users as $user): ?>
    <tr>
        <td><?= htmlspecialchars($user['name']) ?></td>
        <td><?= htmlspecialchars($user['email']) ?></td>
        <td><?= $user['age'] ?></td>
    </tr>
    <?php endforeach; ?>
</table>
```

### 3. Controller-only Pages

If you only have `page.php` (no `page.html.php`), the controller's output becomes the page content:

```php
<?php
// app/api/data.php
header('Content-Type: application/json');
echo json_encode(['status' => 'ok', 'data' => []]);
```

## Layouts

Layouts wrap pages and provide common structure. The `$page` variable contains the rendered page content.

### Root Layout (app/layout.php)

```php
<?php
use NextPHP\Config;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= Config::get('app.name') ?></title>
</head>
<body>
    <nav><!-- Navigation --></nav>
    <main>
        <?= $page ?>
    </main>
    <footer><!-- Footer --></footer>
</body>
</html>
```

### Nested Layouts

Layouts are inherited automatically. A layout in a folder applies to all nested routes:

```
app/
├── layout.php           # Root layout (applies to all)
├── page.php
└── admin/
    ├── layout.php       # Admin layout (applies to admin/*)
    └── dashboard/
        └── page.php     # Uses admin/layout.php
```

## Error Boundaries (error.php)

Similar to Next.js `error.tsx`, error boundaries catch errors in their segment and all nested segments.

### Creating Error Boundaries

```php
<?php
// pages/error.php - Catches all errors
// pages/blog/error.php - Catches errors in /blog/* only
?>
<!DOCTYPE html>
<html>
<head><title>Error</title></head>
<body>
    <h1>Something went wrong!</h1>
    <p>Error: <?= htmlspecialchars($errorMessage) ?></p>
    
    <?php if (ini_get('display_errors')): ?>
    <details>
        <summary>Debug Info</summary>
        <p>File: <?= htmlspecialchars($errorFile) ?></p>
        <p>Line: <?= $errorLine ?></p>
        <pre><?= htmlspecialchars($errorTrace) ?></pre>
    </details>
    <?php endif; ?>
</body>
</html>
```

### Error Variables

The following variables are available in error.php:

- `$errorMessage` - The error message
- `$errorCode` - Error code
- `$errorFile` - File where error occurred
- `$errorLine` - Line number
- `$errorTrace` - Stack trace

### Error Boundary Hierarchy

Error boundaries bubble up the folder tree:

```
app/
├── error.php           # Root error boundary (catches all errors)
├── page.php
└── api/
    ├── error.php       # API error boundary (catches /api/* errors)
    └── users/
        └── page.php    # If this errors, api/error.php catches it
```

## FileHandler Library

A comprehensive file management library with schema-based parsing.

### Basic Usage

```php
use NextPHP\Core\FileHandler;

$fileHandler = new FileHandler(__DIR__ . '/../storage');

// Read/Write
$fileHandler->write('data.txt', 'Hello World');
$content = $fileHandler->read('data.txt');

// File operations
$fileHandler->copy('data.txt', 'backup/data.txt');
$fileHandler->move('data.txt', 'archive/data.txt');
$fileHandler->delete('archive/data.txt');

// Upload
$result = $fileHandler->upload($_FILES['document'], 'uploads');

// Download
$fileHandler->download('reports/sales.pdf', 'sales-report.pdf');
```

### Schema-Based Parsing

#### Delimited Files (CSV, TSV, etc.)

```php
// File: users.txt
// name|email|age
// John Doe|john@example.com|30
// Jane Smith|jane@example.com|25

$users = $fileHandler->parse('users.txt', [
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
```

#### Structured Text Files

```php
// File: config.txt
// ===CONFIG===
// app_name=MyApp
// version=1.0.0
// debug=true
// ===CONFIG===
//
// ===DATABASE===
// host=localhost
// port=3306
// ===DATABASE===

$config = $fileHandler->parse('config.txt', [
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
        ]
    ]
]);

// Result:
// [
//   'app' => ['app_name' => 'MyApp', 'version' => '1.0.0', 'debug' => 'true'],
//   'database' => ['host' => 'localhost', 'port' => '3306']
// ]
```

#### JSON Sections

```php
// File: data.txt
// [JSON]
// {"users": [{"name": "John"}, {"name": "Jane"}]}
// [JSON]

$data = $fileHandler->parse('data.txt', [
    'type' => 'structured',
    'sections' => [
        'json' => [
            'start' => '[JSON]',
            'end' => '[JSON]',
            'extract' => 'json'
        ]
    ]
]);

// Result: ['json' => ['users' => [...]]]
```

#### Line-Based Sections

```php
// File: notes.txt
// ---NOTES---
// First note
// Second note
// Third note
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

// Result: ['notes' => ['First note', 'Second note', 'Third note']]
```

### Validation Types

Available validation types in schema:

- `'int'` / `'integer'` - Convert to integer
- `'float'` / `'double'` - Convert to float
- `'bool'` / `'boolean'` - Convert to boolean
- `'email'` - Validate email format
- `'url'` - Validate URL format
- `'date'` - Validate and format as Y-m-d
- `'trim'` - Trim whitespace
- `'uppercase'` - Convert to uppercase
- `'lowercase'` - Convert to lowercase

### Writing Data with Schema

```php
// Save delimited data
$users = [
    ['name' => 'Alice', 'email' => 'alice@example.com', 'age' => 28],
    ['name' => 'Bob', 'email' => 'bob@example.com', 'age' => 35]
];

$fileHandler->saveParsed('output/users.txt', $users, [
    'delimiter' => '|',
    'fields' => ['name', 'email', 'age'],
    'include_header' => true
]);

// Output:
// name|email|age
// Alice|alice@example.com|28
// Bob|bob@example.com|35

// Save structured data
$configData = [
    'app' => ['name' => 'MyApp', 'version' => '2.0.0'],
    'database' => ['host' => 'localhost', 'port' => '5432']
];

$fileHandler->saveParsed('output/config.txt', $configData, [
    'type' => 'structured',
    'sections' => [
        'app' => [
            'start' => '===APP===',
            'end' => '===APP===',
            'format' => 'key_value'
        ],
        'database' => [
            'start' => '===DB===',
            'end' => '===DB===',
            'format' => 'key_value'
        ]
    ]
]);
```

### Security Features

```php
$fileHandler = new FileHandler(__DIR__ . '/../storage', [
    'secure_mode' => true,              // Prevents directory traversal (../)
    'allowed_extensions' => ['txt', 'csv', 'json', 'md'],  // Whitelist
    'max_file_size' => 5 * 1024 * 1024  // 5MB limit
]);

// These will throw exceptions:
// $fileHandler->read('../../etc/passwd');  // Blocked!
// $fileHandler->write('shell.php', '...'); // Extension not allowed!
```

## Configuration

Edit `config.php` to customize the application:

```php
<?php
use NextPHP\Config;

Config::set('app.name', 'My App');
Config::set('app.url', 'http://localhost:8080/myapp');
Config::set('app.debug', true);

Config::set('paths.root', __DIR__);
Config::set('paths.pages', __DIR__ . "/pages");
Config::set('paths.storage', __DIR__ . "/storage");
```

Access configuration anywhere:

```php
use NextPHP\Config;

$appName = Config::get('app.name');
$pagesPath = Config::get('paths.pages');
```

## How It Works

### Request Flow

1. **Request** → `public/index.php`
2. **Router** parses URI and finds matching page via DFS
3. **Router** locates nearest layout(s) and error boundaries
4. **App** creates Page instance with paths
5. **Page** renders:
   - Include `page.php` (controller logic)
   - Capture `page.html.php` output (view)
   - Include layout with `$page` variable
6. **Error Handling**: If any step throws, nearest error boundary catches it

### DFS Routing Algorithm

The router uses Depth-First Search to:

1. Traverse the `pages/` directory tree
2. Match URL path to folder path (stripping route groups)
3. Find `page.php` and/or `page.html.php`
4. Track nearest `layout.php` for inheritance
5. Fallback to 404 page if no match found

### Group Stripping

Route groups `(name)` are stripped from the URL path:

```
Folder path: pages/(auth)/login/page.php
Stripped:    pages/login/page.php
Matches URL: /login
```

## Examples

### Simple Page

```
pages/
└── about/
    ├── page.php
    └── page.html.php
```

### API Endpoint

```
pages/
└── api/
    └── users/
        └── page.php     # Returns JSON, no view needed
```

### Blog with Layout

```
pages/
├── layout.php
└── blog/
    ├── layout.php       # Blog-specific layout
    ├── page.php         # Blog index
    ├── page.html.php
    └── post/
        └── page.php     # Individual post
```

### Protected Routes with Error Handling

```
pages/
├── error.php            # Global error boundary
├── layout.php
└── admin/
    ├── error.php        # Admin-specific errors
    ├── layout.php
    ├── page.php
    └── users/
        ├── page.php     # If this errors → admin/error.php catches it
        └── error.php    # User-specific errors
```

## License

MIT License

## Contributing

Contributions welcome! Please submit pull requests or open issues for bugs/features.
