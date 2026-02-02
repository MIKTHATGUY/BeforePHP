<?php
// Contact form controller - demonstrates POST handling

// Check if this is a POST request
$isPost = $_isPost ?? false;
$method = $_method ?? 'GET';

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
if ($isPost) {
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
    
    if (empty($subject)) {
        $formErrors[] = 'Subject is required';
    }
    
    if (empty($message)) {
        $formErrors[] = 'Message is required';
    } elseif (strlen($message) < 10) {
        $formErrors[] = 'Message must be at least 10 characters long';
    }
    
    // If no errors, process the form (in real app, send email or save to database)
    if (empty($formErrors)) {
        $formSuccess = true;
        
        // In a real application, you would:
        // - Send email
        // - Save to database
        // - Log the submission
        // etc.
        
        // Clear form after successful submission
        $name = '';
        $email = '';
        $subject = '';
        $message = '';
        $newsletter = false;
    }
}

// Get all POST data for display
$postData = $isPost ? $_POST : [];
