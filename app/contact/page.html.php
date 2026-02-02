<?php
// Contact form view
?>
<div style="max-width: 600px; margin: 0 auto; padding: 20px;">
    <h1 style="color: #2c3e50; margin-bottom: 10px;">Contact Us</h1>
    <p style="color: #666; margin-bottom: 30px;">
        This page demonstrates POST request handling. Form data is automatically available as variables.
    </p>
    
    <!-- Request Info -->
    <div style="background: #e8f4f8; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
        <strong>Request Method:</strong> <code><?= htmlspecialchars($method) ?></code>
        <?php if ($isPost): ?>
            <span style="color: #27ae60; margin-left: 10px;">✓ POST Request Detected</span>
        <?php endif; ?>
    </div>
    
    <!-- Success Message -->
    <?php if ($formSuccess): ?>
        <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 20px; border-radius: 4px; margin-bottom: 20px;">
            <h3 style="margin-top: 0;">✓ Message Sent Successfully!</h3>
            <p style="margin-bottom: 0;">Thank you for contacting us. We'll get back to you soon.</p>
        </div>
    <?php endif; ?>
    
    <!-- Error Messages -->
    <?php if (!empty($formErrors)): ?>
        <div style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
            <h4 style="margin-top: 0;">Please fix the following errors:</h4>
            <ul style="margin-bottom: 0;">
                <?php foreach ($formErrors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <!-- Contact Form -->
    <form method="POST" action="/contact" style="background: #f8f9fa; padding: 25px; border-radius: 8px;">
        <div style="margin-bottom: 20px;">
            <label for="name" style="display: block; font-weight: bold; margin-bottom: 5px;">
                Name <span style="color: #e74c3c;">*</span>
            </label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($name) ?>" required
                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
        </div>
        
        <div style="margin-bottom: 20px;">
            <label for="email" style="display: block; font-weight: bold; margin-bottom: 5px;">
                Email <span style="color: #e74c3c;">*</span>
            </label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required
                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
        </div>
        
        <div style="margin-bottom: 20px;">
            <label for="subject" style="display: block; font-weight: bold; margin-bottom: 5px;">
                Subject <span style="color: #e74c3c;">*</span>
            </label>
            <select id="subject" name="subject" required
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
                <option value="">Select a subject...</option>
                <option value="general" <?= $subject === 'general' ? 'selected' : '' ?>>General Inquiry</option>
                <option value="support" <?= $subject === 'support' ? 'selected' : '' ?>>Technical Support</option>
                <option value="sales" <?= $subject === 'sales' ? 'selected' : '' ?>>Sales</option>
                <option value="feedback" <?= $subject === 'feedback' ? 'selected' : '' ?>>Feedback</option>
            </select>
        </div>
        
        <div style="margin-bottom: 20px;">
            <label for="message" style="display: block; font-weight: bold; margin-bottom: 5px;">
                Message <span style="color: #e74c3c;">*</span>
            </label>
            <textarea id="message" name="message" rows="5" required
                      style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px; resize: vertical;"><?= htmlspecialchars($message) ?></textarea>
            <small style="color: #666;">Minimum 10 characters</small>
        </div>
        
        <div style="margin-bottom: 20px;">
            <label style="display: flex; align-items: center; cursor: pointer;">
                <input type="checkbox" name="newsletter" value="1" <?= $newsletter ? 'checked' : '' ?>
                       style="margin-right: 8px; width: 18px; height: 18px;">
                <span>Subscribe to our newsletter</span>
            </label>
        </div>
        
        <button type="submit" 
                style="background: #3498db; color: white; padding: 12px 30px; border: none; border-radius: 4px; font-size: 16px; cursor: pointer; width: 100%;">
            Send Message
        </button>
    </form>
    
    <!-- POST Data Display (for demonstration) -->
    <?php if ($isPost && !empty($postData)): ?>
        <div style="margin-top: 30px; background: #f0f0f0; padding: 20px; border-radius: 8px;">
            <h3 style="margin-top: 0;">POST Data Received:</h3>
            <p style="font-size: 0.9em; color: #666; margin-bottom: 15px;">
                Form fields are automatically available as variables (e.g., <code>$_name</code>, <code>$_email</code>)
            </p>
            <pre style="background: #fff; padding: 15px; border-radius: 4px; overflow-x: auto; font-size: 14px;"><?php print_r($postData); ?></pre>
        </div>
    <?php endif; ?>
    
    <!-- Documentation -->
    <div style="margin-top: 30px; padding: 20px; background: #fff3cd; border-radius: 8px;">
        <h3 style="margin-top: 0;">How POST Handling Works</h3>
        <ol style="line-height: 1.8;">
            <li>Submit a form with <code>method="POST"</code></li>
            <li>POST data is automatically parsed</li>
            <li>Each form field becomes a variable (e.g., <code>$_name</code>, <code>$_email</code>)</li>
            <li>Check <code>$_isPost</code> to detect POST requests</li>
            <li>Access raw POST data with <code>$_POST</code> if needed</li>
        </ol>
        <p style="margin-bottom: 0; font-size: 0.9em;">
            <strong>Example:</strong> A form field named "email" becomes variable <code>$_email</code> in your controller.
        </p>
    </div>
</div>
