<?php
// Docs optional catch-all view
?>
<div style="max-width: 800px; margin: 0 auto; padding: 20px;">
    <h1>📚 <?= htmlspecialchars(ucwords(str_replace('-', ' ', $pageTitle))) ?></h1>
    
    <?php if ($hasSlugs): ?>
        <div style="background: #f5f5f5; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <h3>Documentation Path:</h3>
            <ul>
                <?php foreach ($slugs as $index => $segment): ?>
                    <li>Level <?= $index + 1 ?>: <code><?= htmlspecialchars($segment) ?></code></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php else: ?>
        <div style="background: #d4edda; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <h3>Welcome to the Documentation!</h3>
            <p>This is the root documentation page. The optional catch-all allows this route to work without any segments.</p>
        </div>
    <?php endif; ?>
    
    <div style="margin-top: 30px; padding: 15px; background: #e8f4f8; border-radius: 4px;">
        <strong>Route Pattern:</strong> <code>/docs/[[...slug]]</code> (optional catch-all)<br>
        <strong>Access via:</strong> <code>$page->getParam('slug')</code> (returns array or null)<br>
        <strong>Examples:</strong>
        <ul>
            <li><code>/docs</code> → slugs = null (no segments)</li>
            <li><code>/docs/getting-started</code> → slugs = ['getting-started']</li>
            <li><code>/docs/api/routing</code> → slugs = ['api', 'routing']</li>
        </ul>
    </div>
</div>
