<?php
// Validation Demo - User Registration with Auto-Validation
use NextPHP\Core\Metadata;
use NextPHP\Core\Validator;
use NextPHP\Config;

// Configure page metadata
Metadata::set([
    'title' => 'Auto-Validation Demo',
    'description' => 'Demonstrates automatic input validation with enable/disable toggle',
]);

// ===========================================
// VALIDATION CONFIGURATION
// ===========================================

// Enable or disable auto-validation for this page
// Set to false to disable validation
Validator::enable(true);

// Define validation schema for form inputs
// Available rules: required, email, string, int, min:X, max:X, regex:PATTERN, in:a,b,c
Validator::schema([
    'username' => 'required|string|min:3|max:20|alphanumeric',
    'email' => 'required|email|max:100',
    'password' => 'required|string|min:8|max:50',
    'age' => 'int|min:13|max:120',
    'website' => 'url|max:200',
    'terms' => 'required',  // Must be checked
]);

// ===========================================
// FORM PROCESSING
// ===========================================

// Access form data (automatically validated if enabled)
$username = $_username ?? '';
$email = $_email ?? '';
$age = $_age ?? '';
$website = $_website ?? '';
$password = $_password ?? '';
$terms = isset($_terms) ? true : false;

$formSubmitted = false;
$formErrors = [];
$formSuccess = false;

// Check if form was submitted
if ($_isPost) {
    $formSubmitted = true;
    
    // If validation is enabled, errors would have been caught automatically
    // But we can also check for validation errors manually
    if (Validator::hasErrors()) {
        $formErrors = Validator::getErrors();
    } else {
        // Validation passed - process the form
        $formSuccess = true;
        
        // In a real app, you would:
        // - Hash the password
        // - Save to database
        // - Send confirmation email
        
        // Clear form after success
        $username = '';
        $email = '';
        $age = '';
        $website = '';
        $password = '';
        $terms = false;
    }
}

// Get validation status for display
$validationEnabled = Validator::isEnabled();
$validationSchema = [
    'username' => 'required, string, 3-20 chars, alphanumeric',
    'email' => 'required, valid email, max 100 chars',
    'password' => 'required, 8-50 chars',
    'age' => 'integer, 13-120',
    'website' => 'valid URL, max 200 chars',
    'terms' => 'required (must be checked)',
];
