# NextPHP Framework

<p align="center">
  <strong>A lightweight, file-based PHP framework inspired by Next.js</strong>
</p>

<p align="center">
  <a href="#features">Features</a> •
  <a href="#quick-start">Quick Start</a> •
  <a href="#documentation">Documentation</a> •
  <a href="#examples">Examples</a> •
  <a href="#api-reference">API</a>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.1+-blue.svg" alt="PHP 8.1+">
  <img src="https://img.shields.io/badge/License-MIT-green.svg" alt="License: MIT">
  <img src="https://img.shields.io/badge/Zero%20Config-Ready-brightgreen.svg" alt="Zero Config">
  <img src="https://img.shields.io/badge/Routing-File%20Based-orange.svg" alt="File Based Routing">
</p>

---

## Overview

NextPHP brings the developer experience of **Next.js** to PHP. Build modern web applications with an intuitive file-system based routing approach, zero configuration, and powerful features out of the box.

**Key Philosophy:** *Create files and folders → Get routes automatically*

```
app/
├── page.php              → /
├── about/
│   └── page.php          → /about
└── blog/
    └── [slug]/           → /blog/:slug
        └── page.php
```

---

## Table of Contents

1. [Features](#-features)
2. [Dynamic Class Loading](#-dynamic-class-loading)
3. [Quick Start](#-quick-start)
4. [Documentation](#-documentation)
   - [Directory Structure](#directory-structure)
   - [Routing](#routing)
   - [Page Files](#page-files)
   - [Layouts](#layouts)
   - [Error Boundaries](#error-boundaries)
   - [SEO & Metadata](#seo--metadata)
   - [Query Strings](#query-strings)
   - [POST Handling](#post-handling)
   - [Auto-Validation](#auto-validation)
   - [Proxies](#proxies)
   - [404 Pages](#404-pages)
5. [Examples](#-examples)
6. [FileHandler Library](#-filehandler-library)
7. [Configuration](#-configuration)
8. [API Reference](#-api-reference)
9. [How It Works](#-how-it-works)
10. [Testing](#-testing)
11. [Contributing](#-contributing)
12. [License](#-license)

---

## Features

- **File-Based Routing** - Routes mirror your folder structure
- **Dynamic Routes** - `[slug]`, `[...slug]`, `[[...slug]]` patterns like Next.js
- **Route Groups** - Organize with `(group)` notation, URL stays clean
- **Controller + View** - Separate logic (`page.php`) from presentation (`page.html.php`)
- **Nested Layouts** - Automatic layout inheritance with `$page` variable
- **Error Boundaries** - `error.php` files catch errors per segment
- **Smart 404s** - Automatic 404 resolution via DFS
- **FileHandler Library** - Schema-based file parsing and operations
- **Zero Config** - Works immediately with sensible defaults
- **Secure by Default** - Path traversal protection, extension validation
- **SEO Metadata** - Next.js style metadata API with Open Graph, Twitter Cards, and SEO optimization
- **Auto-Validation** - Automatic input validation with enable/disable toggle
- **Proxies** - Request filtering with auth, rate limiting, CORS, logging, and CSRF protection
- **POST Handling** - Built-in support for form submissions and data processing

---

## Dynamic Class Loading

NextPHP uses a dynamic ClassLoader that automatically loads classes on-demand, eliminating the need for manual `require` or `include` statements.

### How It Works

The ClassLoader follows PSR-4 autoloading standards and automatically discovers and loads classes from:
- The `core/` directory for framework classes
- The `app/` directory for application-specific classes
- Any registered custom namespaces

```php
<?php
// No need to manually require files!
// Simply use the class and it's automatically loaded

use NextPHP\Core\FileHandler;
use NextPHP\Core\Metadata;
use NextPHP\Core\Validator;

$fileHandler = new FileHandler(__DIR__ . '/../storage');
Metadata::set(['title' => 'My Page']);
Validator::schema(['id' => 'required|int']);
```

### Key Benefits

- **On-Demand Loading**: Classes are loaded only when first used, improving performance
- **No Manual Requires**: Never write `require_once` or `include` for class files
- **Automatic Discovery**: New classes in registered directories are automatically available
- **Namespace Support**: Full PSR-4 namespace support for clean code organization
- **Lazy Loading**: Framework components load only when needed, keeping memory usage minimal

### Custom Namespaces

Register additional namespaces in `config.php`:

```php
<?php
use NextPHP\Core\ClassLoader;

// Register custom namespace
ClassLoader::register('App\\Services', __DIR__ . '/services');
ClassLoader::register('App\\Helpers', __DIR__ . '/helpers');

// Now these classes are auto-loadable
use App\Services\PaymentService;
use App\Helpers\StringHelper;
```

---

## Quick Start

### Installation

```bash
# Clone the repository
git clone https://github.com/yourusername/nextphp.git

# Or download and extract
cd nextphp
```

### Server Setup

Point your web server to the `public/` directory:

**Apache (.htaccess included):**
```apache
DocumentRoot /path/to/nextphp/public
```

**Nginx:**
```nginx
server {
    root /path/to/nextphp/public;
    index index.php;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
}
```

**PHP Built-in Server:**
```bash
cd public
php -S localhost:8000
```

### Create Your First Page

```bash
# Create a new route
mkdir app/about
touch app/about/page.php
touch app/about/page.html.php
```

**app/about/page.php** (Controller):
```php
<?php
$pageTitle = 'About Us';
$team = ['Alice', 'Bob', 'Charlie'];
```

**app/about/page.html.php** (View):
```php
<h1><?= htmlspecialchars($pageTitle) ?></h1>
<ul>
  <?php foreach ($team as $member): ?>
    <li><?= htmlspecialchars($member) ?></li>
  <?php endforeach; ?>
</ul>
```

Visit `http://localhost/about`

---

## Documentation

### Directory Structure

```
nextphp/
├── config.php              # Application configuration
├── core/                   # Framework core
│   ├── App.php            # Application bootstrap
│   ├── Router.php         # File-based router
│   ├── Page.php           # Page renderer
│   ├── Config.php         # Configuration manager
│   ├── FileHandler.php    # File operations
│   ├── Metadata.php       # SEO metadata manager
│   ├── Validator.php      # Auto-validation system
│   ├── Proxy.php          # Proxy system
│   └── ClassLoader.php    # Dynamic class loading
├── app/                    # Your application routes
│   ├── page.php           # Home page (/)
│   ├── page.html.php      # Home view
│   ├── layout.php         # Root layout
│   ├── error.php          # Root error boundary
│   ├── (codes)/           # Route group (URL: /404)
│   │   └── 404/
│   │       └── page.php
│   └── blog/
│       ├── page.php       # Blog index
│       └── [slug]/        # Dynamic route
│           ├── page.php
│           └── page.html.php
├── proxies/               # Proxy configuration
│   └── config.php
├── storage/               # FileHandler storage
├── public/                # Web server root
│   └── index.php
└── examples/              # Usage examples
```

---

### Routing

#### Basic Routes

| File Structure | URL |
|---------------|-----|
| `app/page.php` | `/` |
| `app/about/page.php` | `/about` |
| `app/blog/page.php` | `/blog` |
| `app/api/users/page.php` | `/api/users` |

#### Route Groups `(group)`

Organize routes without affecting the URL:

```
app/
└── (marketing)/          → Group folder, stripped from URL
    ├── about/
    │   └── page.php      → /about (not /marketing/about)
    └── contact/
        └── page.php      → /contact (not /marketing/contact)
```

**Use cases:**
- Group related routes with shared layouts
- Organize by feature/domain
- Exclude URL segments (like `(codes)` for 404 pages)

#### Dynamic Routes

| Pattern | Example | Matches |
|---------|---------|---------|
| `[slug]` | `/blog/[slug]/` | `/blog/hello-world` |
| `[id]` | `/users/[id]/` | `/users/123` |
| `[...slug]` | `/shop/[...slug]/` | `/shop/clothes/tops` |
| `[[...slug]]` | `/docs/[[...slug]]/` | `/docs` OR `/docs/api` |

**Accessing Parameters:**

```php
<?php
// app/blog/[slug]/page.php

// The slug is automatically available as $_slug
$postTitle = ucwords(str_replace('-', ' ', $_slug));

// For catch-all routes, it's an array
// /shop/clothes/tops → $_slug = ['clothes', 'tops']
$category = implode(' / ', $_slug);
```

---

### Page Files

#### 1. page.php (Controller)

Runs first. Set up data, variables, and logic here.

```php
<?php
use NextPHP\Core\FileHandler;

// Load data
$fileHandler = new FileHandler(__DIR__ . '/../../storage');
$posts = $fileHandler->parse('posts.txt', [
    'delimiter' => '|',
    'fields' => ['id', 'title', 'author'],
    'skip_header' => true
]);

// Set variables for the view
$pageTitle = 'Blog Posts';
$postCount = count($posts);
```

#### 2. page.html.php (View)

Runs after controller. Variables from `page.php` are available.

```php
<h1><?= htmlspecialchars($pageTitle) ?></h1>
<p>Total posts: <?= $postCount ?></p>

<?php foreach ($posts as $post): ?>
  <article>
    <h2><?= htmlspecialchars($post['title']) ?></h2>
    <p>By <?= htmlspecialchars($post['author']) ?></p>
  </article>
<?php endforeach; ?>
```

#### 3. Controller-Only (API Endpoints)

```php
<?php
// app/api/users/page.php

use NextPHP\Core\FileHandler;

$fileHandler = new FileHandler(__DIR__ . '/../../../storage');
$users = $fileHandler->parse('users.txt', [
    'delimiter' => '|',
    'fields' => ['id', 'name', 'email'],
    'skip_header' => true
]);

header('Content-Type: application/json');
echo json_encode(['users' => $users]);
```

---

### Layouts

Layouts wrap pages. The rendered page content is available as `$page`.

**Root Layout** (`app/layout.php`):
```php
<?php use NextPHP\Config; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= Config::get('app.name') ?></title>
    <link rel="stylesheet" href="/styles.css">
</head>
<body>
    <header>
        <nav>
            <a href="/">Home</a>
            <a href="/about">About</a>
            <a href="/blog">Blog</a>
        </nav>
    </header>
    
    <main>
        <?= $page ?>
    </main>
    
    <footer>
        <p>&copy; 2024 <?= Config::get('app.name') ?></p>
    </footer>
</body>
</html>
```

**Nested Layouts:**

```
app/
├── layout.php           # Root layout (all pages)
├── page.php
└── admin/
    ├── layout.php       # Admin layout (admin/* only)
    ├── page.php         # Uses admin/layout.php
    └── users/
        └── page.php     # Uses admin/layout.php
```

Admin layout (`app/admin/layout.php`):
```php
<?php use NextPHP\Config; ?>
<!DOCTYPE html>
<html>
<head><title>Admin - <?= Config::get('app.name') ?></title></head>
<body>
    <nav class="admin-nav">
        <a href="/admin">Dashboard</a>
        <a href="/admin/users">Users</a>
    </nav>
    <div class="admin-content">
        <?= $page ?>
    </div>
</body>
</html>
```

---

### Error Boundaries

Create `error.php` to catch errors in a segment and all nested routes.

**Root Error** (`app/error.php`):
```php
<!DOCTYPE html>
<html>
<head>
    <title>Error</title>
    <style>
        body { font-family: system-ui; max-width: 800px; margin: 50px auto; padding: 20px; }
        .error-box { background: #fee; border-left: 4px solid #c00; padding: 20px; }
        details { margin-top: 20px; background: #f5f5f5; padding: 15px; }
        pre { overflow-x: auto; }
    </style>
</head>
<body>
    <h1>Something went wrong!</h1>
    
    <div class="error-box">
        <p><strong>Error:</strong> <?= htmlspecialchars($errorMessage) ?></p>
    </div>
    
    <?php if (ini_get('display_errors')): ?>
    <details>
        <summary>Debug Information</summary>
        <p><strong>File:</strong> <?= htmlspecialchars($errorFile) ?></p>
        <p><strong>Line:</strong> <?= $errorLine ?></p>
        <pre><?= htmlspecialchars($errorTrace) ?></pre>
    </details>
    <?php endif; ?>
    
    <p><a href="/">← Go Home</a></p>
</body>
</html>
```

**Available Variables in error.php:**
- `$errorMessage` - The error message
- `$errorCode` - Error code number
- `$errorFile` - File where error occurred
- `$errorLine` - Line number
- `$errorTrace` - Full stack trace

**Error Boundary Hierarchy:**

```
app/
├── error.php            # Catches ALL errors
├── page.php
├── api/
│   ├── error.php        # Catches /api/* errors
│   └── v1/
│       ├── error.php    # Catches /api/v1/* errors
│       └── users.php    # Error bubbles up to nearest boundary
```

---

### SEO & Metadata

NextPHP provides a powerful Metadata API inspired by Next.js for managing SEO, Open Graph, Twitter Cards, and other meta tags.

#### Basic Usage

Set metadata in your `page.php` using `Metadata::set()`:

```php
<?php
// app/about/page.php
use NextPHP\Core\Metadata;

// Static metadata (like Next.js export const metadata = {})
Metadata::set([
    'title' => 'About Us',
    'description' => 'Learn more about our company and mission',
    'keywords' => ['about', 'company', 'team']
]);

$team = ['Alice', 'Bob', 'Charlie'];
```

The layout automatically renders metadata with `Metadata::render()` in the `<head>`:

```php
<?php
// app/layout.php
use NextPHP\Core\Metadata;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <?php Metadata::render(); ?>
    <link rel="stylesheet" href="/styles.css">
</head>
<body>
    <main><?= $page ?></main>
</body>
</html>
```

#### Dynamic Metadata

Generate metadata dynamically based on route parameters (like Next.js generateMetadata):

```php
<?php
// app/blog/[slug]/page.php
use NextPHP\Core\Metadata;

// Fetch post data based on slug
$post = fetchPost($_slug);

Metadata::set([
    'title' => $post['title'],
    'description' => $post['excerpt'],
    'keywords' => $post['tags'],
    'openGraph' => [
        'title' => $post['title'],
        'description' => $post['excerpt'],
        'images' => [
            ['url' => $post['cover_image'], 'width' => 1200, 'height' => 630]
        ],
        'type' => 'article',
        'publishedTime' => $post['published_at'],
        'authors' => [$post['author']]
    ],
    'twitter' => [
        'card' => 'summary_large_image',
        'title' => $post['title'],
        'description' => $post['excerpt'],
        'images' => [$post['cover_image']]
    ]
]);
```

#### Available Metadata Fields

| Field | Type | Description |
|-------|------|-------------|
| `title` | string | Page title (supports %s template) |
| `description` | string | Meta description |
| `keywords` | array/string | Meta keywords |
| `canonical` | string | Canonical URL |
| `robots` | array/string | Robots meta directives |
| `openGraph` | object | Open Graph protocol tags |
| `twitter` | object | Twitter Card meta tags |
| `authors` | array | Page authors |
| `icons` | array | Favicon and icon definitions |
| `themeColor` | string | Theme color for browsers |

#### Title Templates

Use `%s` placeholder in titles to create consistent title patterns:

```php
<?php
// app/layout.php
use NextPHP\Core\Metadata;

// Template: "Page Title | Site Name"
Metadata::set([
    'title' => '%s | MySite'
]);
```

```php
<?php
// app/products/page.php
use NextPHP\Core\Metadata;

// Renders as: "Our Products | MySite"
Metadata::set([
    'title' => 'Our Products'
]);
```

---

### Query Strings

Query string parameters are automatically parsed and available as variables in your controller:

| URL | Variables Available |
|-----|-------------------|
| `/search?q=php` | `$_q = 'php'` |
| `/search?q=test&page=2` | `$_q = 'test'`, `$_page = '2'` |
| `/products?category=books&sort=price` | `$_category = 'books'`, `$_sort = 'price'` |

**Example Search Page:**

```php
<?php
// app/search/page.php

// Query params are automatically available as $_variables
$searchQuery = $_q ?? '';                    // ?q=...
$category = $_category ?? 'all';             // ?category=...
$page = isset($_page) ? (int)$_page : 1;      // ?page=...

// Use them in your logic
$results = searchDatabase($searchQuery, $category, $page);
```

```php
<?php
// app/search/page.html.php
?>
<form method="GET" action="/search">
    <input type="text" name="q" value="<?= htmlspecialchars($searchQuery) ?>" placeholder="Search...">
    <select name="category">
        <option value="all" <?= $category === 'all' ? 'selected' : '' ?>>All</option>
        <option value="docs" <?= $category === 'docs' ? 'selected' : '' ?>>Docs</option>
    </select>
    <button type="submit">Search</button>
</form>

<?php if (!empty($searchQuery)): ?>
    <p>Searching for: <?= htmlspecialchars($searchQuery) ?></p>
    <p>Category: <?= htmlspecialchars($category) ?></p>
<?php endif; ?>
```

---

### POST Handling

NextPHP provides built-in support for handling POST requests and form submissions. POST data is automatically available in your controllers alongside query parameters.

#### Automatic POST Variables

When a POST request is made, form fields are automatically extracted as variables:

```php
<?php
// app/contact/page.php

// POST variables are automatically available as $_fieldname
// Form fields: name, email, message
$name = $_name ?? '';
$email = $_email ?? '';
$message = $_message ?? '';

// Check if this is a POST request
if ($_isPost) {
    // Process form submission
    // Validate data, send email, save to database, etc.
    $success = processContactForm($name, $email, $message);
}
```

#### Complete Form Example

**Form View** (`app/contact/page.html.php`):
```php
<?php if (isset($success) && $success): ?>
    <div class="alert alert-success">
        Thank you! Your message has been sent.
    </div>
<?php elseif (isset($errors) && !empty($errors)): ?>
    <div class="alert alert-danger">
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="POST" action="/contact">
    <div class="form-group">
        <label for="name">Name</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($name ?? '') ?>" required>
    </div>
    
    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>" required>
    </div>
    
    <div class="form-group">
        <label for="message">Message</label>
        <textarea id="message" name="message" rows="5" required><?= htmlspecialchars($message ?? '') ?></textarea>
    </div>
    
    <button type="submit">Send Message</button>
</form>
```

**Form Controller** (`app/contact/page.php`):
```php
<?php
// Contact form controller - demonstrates POST handling

// Form data automatically available as variables from POST
$name = $_name ?? '';
$email = $_email ?? '';
$subject = $_subject ?? '';
$message = $_message ?? '';
$newsletter = isset($_newsletter) ? true : false;

// Initialize form state
$formSubmitted = false;
$formErrors = [];
$formSuccess = false;

// Process form on POST request
if ($_isPost) {
    $formSubmitted = true;
    
    // Validate form data
    if (empty($name)) {
        $formErrors[] = 'Name is required';
    }
    
    if (empty($email)) {
        $formErrors[] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $formErrors[] = 'Please enter a valid email address';
    }
    
    if (empty($message)) {
        $formErrors[] = 'Message is required';
    } elseif (strlen($message) < 10) {
        $formErrors[] = 'Message must be at least 10 characters long';
    }
    
    // If no errors, process the form
    if (empty($formErrors)) {
        $formSuccess = true;
        
        // In a real application, you would:
        // - Send email using mail() or SMTP library
        // - Save to database
        // - Log the submission
        
        // Clear form after successful submission
        $name = '';
        $email = '';
        $subject = '';
        $message = '';
        $newsletter = false;
    }
}
```

#### File Uploads

Handle file uploads with the FileHandler:

```php
<?php
// app/upload/page.php
use NextPHP\Core\FileHandler;

$uploadSuccess = false;
$uploadError = '';

if ($_isPost && isset($_files)) {
    $fileHandler = new FileHandler(__DIR__ . '/../../storage');
    
    try {
        $result = $fileHandler->upload($_files['document'], 'uploads', [
            'allowed_extensions' => ['pdf', 'doc', 'docx'],
            'max_size' => 5 * 1024 * 1024 // 5MB
        ]);
        
        $uploadSuccess = true;
        $uploadedFile = $result['name'];
    } catch (Exception $e) {
        $uploadError = $e->getMessage();
    }
}
```

---

### Auto-Validation

NextPHP provides automatic input validation for route parameters, query strings, and POST data with an easy-to-use validation system.

#### Enabling/Disabling Validation

Control validation globally using `Validator::enable()`:

```php
<?php
use NextPHP\Core\Validator;

// Enable validation (default)
Validator::enable(true);

// Disable validation entirely
Validator::enable(false);
```

#### Setting Validation Schema

Define validation rules using `Validator::schema()`:

```php
<?php
use NextPHP\Core\Validator;

// app/blog/[slug]/page.php

Validator::schema([
    'slug' => 'required|slug|min:3|max:50',
    'page' => 'int|min:1',           // Query parameter ?page=1
    'category' => 'in:tech,news,lifestyle'  // Query parameter ?category=tech
]);

// Now $_slug and query parameters are validated automatically
$post = fetchPost($_slug);
```

#### Available Validation Rules

| Rule | Description | Example |
|------|-------------|---------|
| `required` | Field must be present and not empty | `'name' => 'required'` |
| `email` | Valid email address | `'email' => 'email'` |
| `url` | Valid URL format | `'website' => 'url'` |
| `int` | Integer value | `'age' => 'int'` |
| `float` | Float/decimal value | `'price' => 'float'` |
| `string` | String value | `'name' => 'string'` |
| `bool` | Boolean value | `'active' => 'bool'` |
| `min:X` | Minimum length/value | `'password' => 'min:8'` |
| `max:X` | Maximum length/value | `'title' => 'max:100'` |
| `alphanumeric` | Letters and numbers only | `'username' => 'alphanumeric'` |
| `alpha` | Letters only | `'name' => 'alpha'` |
| `numeric` | Numbers only | `'code' => 'numeric'` |
| `in:a,b,c` | Must be one of specified values | `'status' => 'in:draft,published'` |
| `regex:PATTERN` | Must match regex pattern | `'phone' => 'regex:/^\d{3}-\d{3}-\d{4}$/'` |
| `slug` | URL-friendly slug format | `'slug' => 'slug'` |
| `uuid` | Valid UUID format | `'id' => 'uuid'` |
| `json` | Valid JSON string | `'data' => 'json'` |
| `date` | Valid date format | `'birthday' => 'date'` |
| `ip` | Valid IP address | `'ip_address' => 'ip'` |

#### Combining Multiple Rules

Use pipe `|` to chain multiple rules:

```php
<?php
use NextPHP\Core\Validator;

Validator::schema([
    'email' => 'required|email',
    'password' => 'required|string|min:8|max:100',
    'age' => 'int|min:18|max:120',
    'username' => 'required|alphanumeric|min:3|max:20',
    'role' => 'required|in:admin,editor,user'
]);
```

#### Handling Validation Errors

```php
<?php
use NextPHP\Core\Validator;

if ($_isPost) {
    try {
        // Validation runs automatically
        // If it fails, ValidationException is thrown
        
        // Access validated data
        $username = $_username;
        $email = $_email;
        
        // Process form...
        $success = true;
    } catch (ValidationException $e) {
        $errors = $e->getErrors();
        $success = false;
    }
}
```

---

### Proxies

NextPHP provides a powerful proxy system for filtering and processing HTTP requests before they reach your route handlers.

#### Built-in Proxies

##### LoggerProxy

Logs all requests for analytics and debugging:

```php
<?php
// proxies/config.php

use NextPHP\Core\Proxy\LoggerProxy;

return [
    'global' => [
        LoggerProxy::class,
    ],
    'config' => [
        'logger' => [
            'storage_path' => __DIR__ . '/../../storage/logs',
            'log_format' => 'json',
            'log_fields' => [
                'method',
                'uri',
                'ip',
                'user_agent',
                'response_time',
                'status_code',
                'user_id',
            ],
        ],
    ],
];
```

**Log Output Example:**
```json
{
  "timestamp": "2024-01-15T10:30:00+00:00",
  "method": "GET",
  "uri": "/api/users",
  "ip": "192.168.1.1",
  "user_agent": "Mozilla/5.0...",
  "response_time": 45,
  "status_code": 200
}
```

##### AuthProxy

Validates authentication tokens:

```php
<?php
// proxies/config.php

use NextPHP\Core\Proxy\AuthProxy;

return [
    'routes' => [
        '/api/*' => [AuthProxy::class],
        '/admin/*' => [AuthProxy::class],
    ],
];
```

##### RateLimitProxy

Prevents abuse by limiting request frequency:

```php
<?php
// proxies/config.php

use NextPHP\Core\Proxy\RateLimitProxy;

return [
    'routes' => [
        '/api/*' => [RateLimitProxy::class],
    ],
    'config' => [
        'rate_limit' => [
            'max_requests' => 100,
            'window_seconds' => 3600,
        ],
    ],
];
```

##### CORSProxy

Handles Cross-Origin Resource Sharing:

```php
<?php
// proxies/config.php

use NextPHP\Core\Proxy\CORSProxy;

return [
    'global' => [
        CORSProxy::class,
    ],
    'config' => [
        'cors' => [
            'allowed_origins' => ['https://app.example.com'],
            'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE'],
            'allowed_headers' => ['Content-Type', 'Authorization'],
        ],
    ],
];
```

#### Creating Custom Proxies

```php
<?php
// proxies/MaintenanceProxy.php

namespace App\Proxy;

use NextPHP\Core\Proxy\ProxyInterface;
use NextPHP\Core\Proxy\ProxyRequest;

class MaintenanceProxy implements ProxyInterface
{
    public function handle(ProxyRequest $request, callable $next): mixed
    {
        if (file_exists(__DIR__ . '/../../storage/maintenance.flag')) {
            http_response_code(503);
            return ['error' => 'Service temporarily unavailable'];
        }
        
        return $next($request);
    }
}
```

---

### 404 Pages

Create a 404 page inside a route group:

```
app/
└── (codes)/
    └── 404/
        ├── page.php
        └── page.html.php
```

**app/(codes)/404/page.php:**
```php
<?php
http_response_code(404);
$message = 'Page not found';
```

**app/(codes)/404/page.html.php:**
```php
<div style="text-align: center; padding: 50px;">
    <h1>404</h1>
    <p><?= htmlspecialchars($message) ?></p>
    <a href="/">← Back to Home</a>
</div>
```

---

## Examples

### 1. Blog with Dynamic Routes

**Structure:**
```
app/
└── blog/
    ├── page.php           # Blog index
    ├── page.html.php
    └── [slug]/            # Individual posts
        ├── page.php
        └── page.html.php
```

**app/blog/page.php:**
```php
<?php
use NextPHP\Core\FileHandler;
use NextPHP\Core\Metadata;

Metadata::set([
    'title' => 'Blog',
    'description' => 'Latest articles and news'
]);

$fileHandler = new FileHandler(__DIR__ . '/../../storage');
$posts = $fileHandler->parse('blog/posts.txt', [
    'delimiter' => '|',
    'fields' => ['slug', 'title', 'excerpt', 'date'],
    'skip_header' => true
]);
```

**app/blog/page.html.php:**
```php
<h1>Latest Posts</h1>
<?php foreach ($posts as $post): ?>
  <article>
    <h2><a href="/blog/<?= htmlspecialchars($post['slug']) ?>">
      <?= htmlspecialchars($post['title']) ?>
    </a></h2>
    <p><?= htmlspecialchars($post['excerpt']) ?></p>
    <time><?= htmlspecialchars($post['date']) ?></time>
  </article>
<?php endforeach; ?>
```

### 2. User Registration with Validation

**app/register/page.php:**
```php
<?php
use NextPHP\Core\Metadata;
use NextPHP\Core\Validator;

Metadata::set([
    'title' => 'Register',
    'description' => 'Create a new account'
]);

Validator::enable(true);
Validator::schema([
    'username' => 'required|string|min:3|max:20|alphanumeric',
    'email' => 'required|email|max:100',
    'password' => 'required|string|min:8|max:50',
    'terms' => 'required'
]);

$username = $_username ?? '';
$email = $_email ?? '';
$password = '';
$terms = isset($_terms);

$formSuccess = false;
$formErrors = [];

if ($_isPost) {
    try {
        // Validation runs automatically
        $formSuccess = true;
        
        // In production: hash password, save to DB
        // $hashedPassword = password_hash($_password, PASSWORD_BCRYPT);
        
    } catch (ValidationException $e) {
        $formErrors = $e->getErrors();
    }
}
```

**app/register/page.html.php:**
```php
<h1>Create Account</h1>

<?php if ($formSuccess): ?>
    <div class="success">Account created successfully!</div>
<?php else: ?>
    <?php if (!empty($formErrors)): ?>
        <div class="errors">
            <?php foreach ($formErrors as $field => $error): ?>
                <p><?= htmlspecialchars($field) ?>: <?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="/register">
        <input type="text" name="username" placeholder="Username" 
               value="<?= htmlspecialchars($username) ?>" required>
        <input type="email" name="email" placeholder="Email" 
               value="<?= htmlspecialchars($email) ?>" required>
        <input type="password" name="password" placeholder="Password" required>
        <label>
            <input type="checkbox" name="terms" value="1" 
                   <?= $terms ? 'checked' : '' ?> required>
            I agree to the terms
        </label>
        <button type="submit">Register</button>
    </form>
<?php endif; ?>
```

### 3. E-commerce with Catch-All Routes

**Structure:**
```
app/
└── shop/
    └── [...slug]/         # /shop/clothes/tops/t-shirts
        ├── page.php
        └── page.html.php
```

**app/shop/[...slug]/page.php:**
```php
<?php
use NextPHP\Core\Metadata;

// $_slug = ['clothes', 'tops', 't-shirts']
$breadcrumbs = $_slug ?? [];
$currentCategory = end($breadcrumbs) ?? 'Shop';
$parentCategory = count($breadcrumbs) > 1 ? $breadcrumbs[count($breadcrumbs) - 2] : null;

Metadata::set([
    'title' => ucwords(str_replace('-', ' ', $currentCategory)),
    'description' => 'Browse our ' . $currentCategory . ' collection'
]);

// Load products based on category path
$products = fetchProductsByCategory($breadcrumbs);
```

**app/shop/[...slug]/page.html.php:**
```php
<nav class="breadcrumbs">
  <a href="/">Home</a>
  <?php foreach ($breadcrumbs as $i => $crumb): ?>
    <?php $url = '/shop/' . implode('/', array_slice($breadcrumbs, 0, $i + 1)); ?>
    / <a href="<?= htmlspecialchars($url) ?>"><?= htmlspecialchars(ucwords(str_replace('-', ' ', $crumb))) ?></a>
  <?php endforeach; ?>
</nav>

<h1><?= htmlspecialchars(ucwords(str_replace('-', ' ', $currentCategory))) ?></h1>

<?php if ($parentCategory): ?>
  <p>Subcategory of: <?= htmlspecialchars(ucwords(str_replace('-', ' ', $parentCategory))) ?></p>
<?php endif; ?>

<div class="products">
  <?php foreach ($products as $product): ?>
    <div class="product">
      <h3><?= htmlspecialchars($product['name']) ?></h3>
      <p>$<?= number_format($product['price'], 2) ?></p>
    </div>
  <?php endforeach; ?>
</div>
```

### 4. REST API with JSON Responses

**app/api/users/page.php:**
```php
<?php
use NextPHP\Core\FileHandler;
use NextPHP\Core\Validator;

// Validate query parameters
Validator::schema([
    'page' => 'int|min:1',
    'limit' => 'int|min:1|max:100'
]);

$page = (int)($_page ?? 1);
$limit = (int)($_limit ?? 10);

$fileHandler = new FileHandler(__DIR__ . '/../../../storage');
$users = $fileHandler->parse('users.txt', [
    'delimiter' => '|',
    'fields' => ['id', 'name', 'email', 'role'],
    'skip_header' => true
]);

// Paginate results
$total = count($users);
$offset = ($page - 1) * $limit;
$users = array_slice($users, $offset, $limit);

header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'data' => $users,
    'pagination' => [
        'page' => $page,
        'limit' => $limit,
        'total' => $total,
        'pages' => ceil($total / $limit)
    ]
]);
```

### 5. Search with Query Parameters

**app/search/page.php:**
```php
<?php
use NextPHP\Core\Metadata;
use NextPHP\Core\Validator;

Metadata::set([
    'title' => 'Search Results',
    'description' => 'Search our site'
]);

Validator::schema([
    'q' => 'string|min:1|max:100',
    'category' => 'in:all,products,articles',
    'sort' => 'in:relevance,date,price'
]);

$searchQuery = $_q ?? '';
$category = $_category ?? 'all';
$sort = $_sort ?? 'relevance';

$results = [];
if (!empty($searchQuery)) {
    $results = performSearch($searchQuery, $category, $sort);
}
```

**app/search/page.html.php:**
```php
<h1>Search</h1>

<form method="GET" action="/search">
    <input type="text" name="q" value="<?= htmlspecialchars($searchQuery) ?>" 
           placeholder="Search..." required>
    <select name="category">
        <option value="all" <?= $category === 'all' ? 'selected' : '' ?>>All</option>
        <option value="products" <?= $category === 'products' ? 'selected' : '' ?>>Products</option>
        <option value="articles" <?= $category === 'articles' ? 'selected' : '' ?>>Articles</option>
    </select>
    <select name="sort">
        <option value="relevance" <?= $sort === 'relevance' ? 'selected' : '' ?>>Relevance</option>
        <option value="date" <?= $sort === 'date' ? 'selected' : '' ?>>Date</option>
        <option value="price" <?= $sort === 'price' ? 'selected' : '' ?>>Price</option>
    </select>
    <button type="submit">Search</button>
</form>

<?php if (!empty($searchQuery)): ?>
    <h2>Results for "<?= htmlspecialchars($searchQuery) ?>"</h2>
    <p>Category: <?= htmlspecialchars($category) ?> | Sort: <?= htmlspecialchars($sort) ?></p>
    
    <?php if (empty($results)): ?>
        <p>No results found.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($results as $result): ?>
                <li><?= htmlspecialchars($result['title']) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
<?php endif; ?>
```

---

## FileHandler Library

### Basic Operations

```php
use NextPHP\Core\FileHandler;

$fileHandler = new FileHandler(__DIR__ . '/../storage');

// Write file
$fileHandler->write('notes.txt', 'Hello World');

// Read file
$content = $fileHandler->read('notes.txt');

// Append to file
$fileHandler->append('notes.txt', "\nNew line");

// File info
$info = $fileHandler->info('notes.txt');
// ['name', 'path', 'size', 'modified', 'mime_type', ...]

// List files
$files = $fileHandler->list('uploads', ['extension' => 'pdf']);

// Copy/Move/Delete
$fileHandler->copy('old.txt', 'backup/old.txt');
$fileHandler->move('old.txt', 'archive/old.txt');
$fileHandler->delete('temp.txt');

// Upload
$result = $fileHandler->upload($_FILES['document'], 'uploads');

// Download
$fileHandler->download('reports/sales.pdf', 'download.pdf');
```

### Schema-Based Parsing

#### CSV/TSV Files

```php
$users = $fileHandler->parse('data/users.txt', [
    'delimiter' => '|',
    'fields' => ['id', 'name', 'email', 'age', 'role'],
    'skip_header' => true,
    'validate' => [
        'id' => 'int',
        'age' => 'int',
        'email' => 'email'
    ]
]);
```

#### Writing Data

```php
$users = [
    ['id' => 1, 'name' => 'Alice', 'email' => 'alice@example.com'],
    ['id' => 2, 'name' => 'Bob', 'email' => 'bob@example.com']
];

$fileHandler->saveParsed('output/users.txt', $users, [
    'delimiter' => '|',
    'fields' => ['id', 'name', 'email'],
    'include_header' => true
]);
```

---

## Configuration

**config.php:**
```php
<?php
use NextPHP\Config;

// App settings
Config::set('app.name', 'My App');
Config::set('app.url', 'https://example.com');
Config::set('app.debug', false);

// Paths
Config::set('paths.root', __DIR__);
Config::set('paths.app', __DIR__ . '/app');
Config::set('paths.storage', __DIR__ . '/storage');

// Custom settings
Config::set('mail.from', 'noreply@example.com');
Config::set('cache.ttl', 3600);
```

**Access anywhere:**
```php
use NextPHP\Config;

$appName = Config::get('app.name');
$debug = Config::get('app.debug', false);  // Default if not set
$mailFrom = Config::get('mail.from');
```

---

## API Reference

### Router Class

| Method | Description | Example |
|--------|-------------|---------|
| `getControllerPath()` | Get path to page.php | `$router->getControllerPath()` |
| `getViewPath()` | Get path to page.html.php | `$router->getViewPath()` |
| `getLayoutPath()` | Get path to layout.php | `$router->getLayoutPath()` |
| `getParams()` | Get all route/query params | `$router->getParams()` |
| `getParam($key, $default)` | Get specific param | `$router->getParam('id', 0)` |
| `getPostData()` | Get POST data array | `$router->getPostData()` |
| `isPost()` | Check if POST request | `$router->isPost()` |
| `getMethod()` | Get HTTP method | `$router->getMethod()` |
| `isNotFound()` | Check if 404 | `$router->isNotFound()` |

### Config Class

| Method | Description | Example |
|--------|-------------|---------|
| `set($key, $value)` | Set configuration value | `Config::set('app.name', 'MyApp')` |
| `get($key, $default)` | Get configuration value | `Config::get('app.name')` |
| `all()` | Get all configuration | `Config::all()` |

### Metadata Class

| Method | Description | Example |
|--------|-------------|---------|
| `set($metadata)` | Set page metadata | `Metadata::set(['title' => 'About'])` |
| `get()` | Get all metadata | `Metadata::get()` |
| `getValue($key, $default)` | Get specific value | `Metadata::getValue('title')` |
| `render()` | Render HTML meta tags | `Metadata::render()` |
| `reset()` | Reset all metadata | `Metadata::reset()` |

### Validator Class

| Method | Description | Example |
|--------|-------------|---------|
| `enable($bool)` | Enable/disable validation | `Validator::enable(true)` |
| `isEnabled()` | Check if enabled | `Validator::isEnabled()` |
| `schema($rules)` | Set validation schema | `Validator::schema(['id' => 'int'])` |
| `validate($data)` | Validate data manually | `Validator::validate($_POST)` |
| `getErrors()` | Get validation errors | `Validator::getErrors()` |
| `hasErrors()` | Check if has errors | `Validator::hasErrors()` |
| `reset()` | Reset validator state | `Validator::reset()` |

### FileHandler Class

| Method | Description | Example |
|--------|-------------|---------|
| `read($file)` | Read file contents | `$fh->read('data.txt')` |
| `write($file, $content)` | Write to file | `$fh->write('data.txt', 'Hello')` |
| `append($file, $content)` | Append to file | `$fh->append('log.txt', 'Entry')` |
| `delete($file)` | Delete file | `$fh->delete('temp.txt')` |
| `copy($from, $to)` | Copy file | `$fh->copy('a.txt', 'b.txt')` |
| `move($from, $to)` | Move file | `$fh->move('a.txt', 'b.txt')` |
| `exists($file)` | Check if exists | `$fh->exists('file.txt')` |
| `info($file)` | Get file info | `$fh->info('file.txt')` |
| `list($dir, $filters)` | List files | `$fh->list('uploads')` |
| `upload($file, $dir, $opts)` | Handle upload | `$fh->upload($_FILES['doc'], 'uploads')` |
| `download($file, $name)` | Force download | `$fh->download('report.pdf')` |
| `parse($file, $schema)` | Parse structured file | `$fh->parse('data.csv', [...])` |
| `saveParsed($file, $data, $schema)` | Save structured data | `$fh->saveParsed('out.csv', $data, [...])` |

### ClassLoader Class

| Method | Description | Example |
|--------|-------------|---------|
| `register($namespace, $path)` | Register namespace | `ClassLoader::register('App\\', 'app/')` |
| `load($class)` | Load class manually | `ClassLoader::load('SomeClass')` |

---

## How It Works

### Request Lifecycle

```
1. Request → public/index.php
2. Router parses URI
3. DFS finds matching page
4. Locate layouts (upward search)
5. Page.render():
   a. Execute page.php (controller)
   b. Capture page.html.php (view) → $page
   c. Render layout with $page
6. Return response
```

### Routing Algorithm

**Depth-First Search (DFS):**
1. Start at `app/` root
2. Compare folder names to URI segments
3. Support patterns:
   - Static: `about/` matches `about`
   - Dynamic: `[slug]/` matches any segment
   - Catch-all: `[...slug]/` matches all remaining
   - Optional: `[[...slug]]/` matches 0 or more
   - Groups: `(codes)/` skipped in URL matching
4. Find `page.php` and/or `page.html.php`
5. Track nearest `layout.php` during traversal
6. On mismatch: fallback to 404 page

### Error Handling

```
Exception Thrown
    ↓
Router.findErrorPath() - Search upward for error.php
    ↓
Found? → Render error.php with error variables
Not Found? → Show default 500 error
```

---

## Testing

Create a test page that throws an error:

```php
<?php
// app/test-error/page.php
throw new Exception('Test error for error boundary!');
```

Visit `/test-error` to see your error boundary in action.

---

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

**Guidelines:**
- Follow PSR-12 coding standards
- Add tests for new features
- Update documentation
- Keep backward compatibility when possible

---

## License

MIT License - see [LICENSE](LICENSE) file

---

## Acknowledgments

- Inspired by [Next.js](https://nextjs.org/) file-system routing
- Built for PHP developers who love simplicity

---

<p align="center">
  <strong>Happy Coding!</strong>
</p>
