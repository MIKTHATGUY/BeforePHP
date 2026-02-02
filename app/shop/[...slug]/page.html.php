<?php
// Shop catch-all view
?>
<div style="max-width: 800px; margin: 0 auto; padding: 20px;">
    <h1>Shop: <?= htmlspecialchars(ucwords(str_replace('-', ' ', $categoryTitle))) ?></h1>
    
    <?php if (!empty($slugs)): ?>
        <p style="color: #666;">Path: <code><?= htmlspecialchars($categoryPath) ?></code></p>
        <div style="background: #f5f5f5; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <h3>URL Segments:</h3>
            <ul>
                <?php foreach ($slugs as $index => $segment): ?>
                    <li>Segment <?= $index ?>: <code><?= htmlspecialchars($segment) ?></code></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php else: ?>
        <p style="color: #e74c3c;">This page requires at least one segment. Try /shop/clothes</p>
    <?php endif; ?>
    
    <div style="margin-top: 30px; padding: 15px; background: #e8f4f8; border-radius: 4px;">
        <strong>Route Pattern:</strong> <code>/shop/[...slug]</code><br>
        <strong>Access via:</strong> <code>$page->getParam('slug')</code> (returns array)<br>
        <strong>Examples:</strong>
        <ul>
            <li><code>/shop/clothes</code> → slugs = ['clothes']</li>
            <li><code>/shop/clothes/tops</code> → slugs = ['clothes', 'tops']</li>
            <li><code>/shop/clothes/tops/t-shirts</code> → slugs = ['clothes', 'tops', 't-shirts']</li>
        </ul>
    </div>
</div>
