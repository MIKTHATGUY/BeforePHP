<?php
// Validation Demo View
?>
<div style="max-width: 800px; margin: 0 auto; padding: 20px;">
    <h1 style="color: #2c3e50; border-bottom: 3px solid #3498db; padding-bottom: 10px;">
        Auto-Validation Demo
    </h1>
    
    <p style="font-size: 1.1em; color: #666; margin-bottom: 30px;">
        This page demonstrates automatic input validation. Try submitting the form with invalid data!
    </p>
    
    <!-- Validation Status Panel -->
    <div style="background: <?= $validationEnabled ? '#d4edda' : '#f8d7da' ?>; padding: 20px; border-radius: 8px; margin-bottom: 30px; border-left: 4px solid <?= $validationEnabled ? '#28a745' : '#dc3545' ?>;">
        <h3 style="margin-top: 0; display: flex; align-items: center; gap: 10px;">
            <span style="font-size: 1.5em;"><?= $validationEnabled ? '✅' : '⚠️' ?></span>
            Auto-Validation is <?= $validationEnabled ? 'ENABLED' : 'DISABLED' ?>
        </h3>
        <p style="margin-bottom: 15px;">
            <?php if ($validationEnabled): ?>
                Form inputs are automatically validated against the schema. Invalid submissions will be rejected.
            <?php else: ?>
                Validation is disabled. All inputs will be accepted (not recommended for production).
            <?php endif; ?>
        </p>
        
        <!-- Validation Schema Display -->
        <div style="background: rgba(255,255,255,0.7); padding: 15px; border-radius: 4px; margin-top: 15px;">
            <h4 style="margin-top: 0;">Validation Rules:</h4>
            <ul style="margin-bottom: 0; line-height: 1.8;">
                <?php foreach ($validationSchema as $field => $rules): ?>
                    <li><code><?= htmlspecialchars($field) ?></code>: <?= htmlspecialchars($rules) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    
    <!-- Success Message -->
    <?php if ($formSuccess): ?>
        <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
            <h3 style="margin-top: 0;">✓ Registration Successful!</h3>
            <p style="margin-bottom: 0;">
                <?php if ($validationEnabled): ?>
                    All inputs passed validation and the form was processed successfully.
                <?php else: ?>
                    Form processed (validation was disabled).
                <?php endif; ?>
            </p>
        </div>
    <?php endif; ?>
    
    <!-- Validation Errors -->
    <?php if (!empty($formErrors)): ?>
        <div style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
            <h4 style="margin-top: 0;">Validation Errors:</h4>
            <ul style="margin-bottom: 0;">
                <?php foreach ($formErrors as $field => $error): ?>
                    <li><strong><?= htmlspecialchars($field) ?>:</strong> <?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <!-- Registration Form -->
    <form method="POST" action="/register" style="background: #f8f9fa; padding: 30px; border-radius: 8px;">
        <div style="margin-bottom: 20px;">
            <label for="username" style="display: block; font-weight: bold; margin-bottom: 5px;">
                Username <span style="color: #e74c3c;">*</span>
                <small style="font-weight: normal; color: #666;">(3-20 alphanumeric characters)</small>
            </label>
            <input type="text" id="username" name="username" value="<?= htmlspecialchars($username) ?>" required
                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
        </div>
        
        <div style="margin-bottom: 20px;">
            <label for="email" style="display: block; font-weight: bold; margin-bottom: 5px;">
                Email <span style="color: #e74c3c;">*</span>
                <small style="font-weight: normal; color: #666;">(valid email address)</small>
            </label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required
                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
        </div>
        
        <div style="margin-bottom: 20px;">
            <label for="password" style="display: block; font-weight: bold; margin-bottom: 5px;">
                Password <span style="color: #e74c3c;">*</span>
                <small style="font-weight: normal; color: #666;">(minimum 8 characters)</small>
            </label>
            <input type="password" id="password" name="password" required
                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label for="age" style="display: block; font-weight: bold; margin-bottom: 5px;">
                    Age
                    <small style="font-weight: normal; color: #666;">(13-120)</small>
                </label>
                <input type="number" id="age" name="age" value="<?= htmlspecialchars($age) ?>" min="13" max="120"
                       style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
            </div>
            
            <div>
                <label for="website" style="display: block; font-weight: bold; margin-bottom: 5px;">
                    Website
                    <small style="font-weight: normal; color: #666;">(valid URL)</small>
                </label>
                <input type="url" id="website" name="website" value="<?= htmlspecialchars($website) ?>" placeholder="https://example.com"
                       style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
            </div>
        </div>
        
        <div style="margin-bottom: 20px;">
            <label style="display: flex; align-items: center; cursor: pointer;">
                <input type="checkbox" name="terms" value="1" <?= $terms ? 'checked' : '' ?> required
                       style="margin-right: 10px; width: 20px; height: 20px;">
                <span>I agree to the Terms and Conditions <span style="color: #e74c3c;">*</span></span>
            </label>
        </div>
        
        <button type="submit" 
                style="background: #3498db; color: white; padding: 12px 30px; border: none; border-radius: 4px; font-size: 16px; cursor: pointer; width: 100%; margin-bottom: 10px;">
            Register
        </button>
        
        <p style="text-align: center; color: #666; font-size: 0.9em; margin-bottom: 0;">
            <span style="color: #e74c3c;">*</span> Required fields
        </p>
    </form>
    
    <!-- Code Examples -->
    <div style="margin-top: 40px; display: grid; gap: 20px;">
        
        <!-- How to Enable/Disable -->
        <div style="background: #e8f4f8; padding: 20px; border-radius: 8px;">
            <h3 style="margin-top: 0;">How to Enable/Disable Validation</h3>
            <pre style="background: #2c3e50; color: #ecf0f1; padding: 15px; border-radius: 4px; overflow-x: auto;"><code>&lt;?php
use NextPHP\Core\Validator;

// Enable validation (default)
Validator::enable(true);

// Disable validation
Validator::enable(false);

// Check if validation is enabled
if (Validator::isEnabled()) {
    // Validation is active
}</code></pre>
        </div>
        
        <!-- How to Set Schema -->
        <div style="background: #fff3cd; padding: 20px; border-radius: 8px;">
            <h3 style="margin-top: 0;">How to Define Validation Schema</h3>
            <pre style="background: #2c3e50; color: #ecf0f1; padding: 15px; border-radius: 4px; overflow-x: auto;"><code>&lt;?php
use NextPHP\Core\Validator;

Validator::schema([
    'username' => 'required|string|min:3|max:20|alphanumeric',
    'email' => 'required|email|max:100',
    'age' => 'int|min:13|max:120',
    'website' => 'url|max:200',
    'status' => 'in:active,inactive,pending',
]);

// Or use array syntax
Validator::schema([
    'password' => ['required', 'string', 'min:8', 'max:50'],
]);</code></pre>
        </div>
        
        <!-- Available Rules -->
        <div style="background: #f0f0f0; padding: 20px; border-radius: 8px;">
            <h3 style="margin-top: 0;">Available Validation Rules</h3>
            <table style="width: 100%; border-collapse: collapse; font-size: 0.9em;">
                <tr style="background: #e0e0e0;">
                    <th style="padding: 8px; text-align: left; border: 1px solid #ccc;">Rule</th>
                    <th style="padding: 8px; text-align: left; border: 1px solid #ccc;">Description</th>
                    <th style="padding: 8px; text-align: left; border: 1px solid #ccc;">Example</th>
                </tr>
                <tr>
                    <td style="padding: 8px; border: 1px solid #ccc;"><code>required</code></td>
                    <td style="padding: 8px; border: 1px solid #ccc;">Field must be present and not empty</td>
                    <td style="padding: 8px; border: 1px solid #ccc;"><code>required</code></td>
                </tr>
                <tr>
                    <td style="padding: 8px; border: 1px solid #ccc;"><code>email</code></td>
                    <td style="padding: 8px; border: 1px solid #ccc;">Must be valid email format</td>
                    <td style="padding: 8px; border: 1px solid #ccc;"><code>email</code></td>
                </tr>
                <tr>
                    <td style="padding: 8px; border: 1px solid #ccc;"><code>url</code></td>
                    <td style="padding: 8px; border: 1px solid #ccc;">Must be valid URL format</td>
                    <td style="padding: 8px; border: 1px solid #ccc;"><code>url</code></td>
                </tr>
                <tr>
                    <td style="padding: 8px; border: 1px solid #ccc;"><code>int</code></td>
                    <td style="padding: 8px; border: 1px solid #ccc;">Must be an integer</td>
                    <td style="padding: 8px; border: 1px solid #ccc;"><code>int</code></td>
                </tr>
                <tr>
                    <td style="padding: 8px; border: 1px solid #ccc;"><code>string</code></td>
                    <td style="padding: 8px; border: 1px solid #ccc;">Must be a string</td>
                    <td style="padding: 8px; border: 1px solid #ccc;"><code>string</code></td>
                </tr>
                <tr>
                    <td style="padding: 8px; border: 1px solid #ccc;"><code>min:X</code></td>
                    <td style="padding: 8px; border: 1px solid #ccc;">Minimum length (strings) or value (numbers)</td>
                    <td style="padding: 8px; border: 1px solid #ccc;"><code>min:3</code></td>
                </tr>
                <tr>
                    <td style="padding: 8px; border: 1px solid #ccc;"><code>max:X</code></td>
                    <td style="padding: 8px; border: 1px solid #ccc;">Maximum length (strings) or value (numbers)</td>
                    <td style="padding: 8px; border: 1px solid #ccc;"><code>max:100</code></td>
                </tr>
                <tr>
                    <td style="padding: 8px; border: 1px solid #ccc;"><code>alphanumeric</code></td>
                    <td style="padding: 8px; border: 1px solid #ccc;">Only letters and numbers</td>
                    <td style="padding: 8px; border: 1px solid #ccc;"><code>alphanumeric</code></td>
                </tr>
                <tr>
                    <td style="padding: 8px; border: 1px solid #ccc;"><code>in:a,b,c</code></td>
                    <td style="padding: 8px; border: 1px solid #ccc;">Must be one of the allowed values</td>
                    <td style="padding: 8px; border: 1px solid #ccc;"><code>in:active,inactive</code></td>
                </tr>
                <tr>
                    <td style="padding: 8px; border: 1px solid #ccc;"><code>regex:PATTERN</code></td>
                    <td style="padding: 8px; border: 1px solid #ccc;">Must match regex pattern</td>
                    <td style="padding: 8px; border: 1px solid #ccc;"><code>regex:/^[a-z]+$/</code></td>
                </tr>
            </table>
        </div>
        
        <!-- Manual Validation -->
        <div style="background: #d4edda; padding: 20px; border-radius: 8px;">
            <h3 style="margin-top: 0;">Manual Validation (Optional)</h3>
            <p>You can also validate data manually in your controller:</p>
            <pre style="background: #2c3e50; color: #ecf0f1; padding: 15px; border-radius: 4px; overflow-x: auto;"><code>&lt;?php
use NextPHP\Core\Validator;

// Check for validation errors
if (Validator::hasErrors()) {
    $errors = Validator::getErrors();
    // Handle errors
}

// Or validate manually
try {
    $validated = Validator::validate([
        'email' => $_email,
        'age' => $_age,
    ]);
} catch (ValidationException $e) {
    $errors = $e->getErrors();
}</code></pre>
        </div>
    </div>
</div>
