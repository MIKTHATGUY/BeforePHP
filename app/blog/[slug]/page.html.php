<?php
// Dynamic blog post view
?>
<article style="max-width: 800px; margin: 0 auto; padding: 20px;">
    <h1><?= htmlspecialchars($postTitle) ?></h1>
    <p style="color: #666;">Slug: <code><?= htmlspecialchars($_slug) ?></code></p>
    <div style="background: #f5f5f5; padding: 20px; border-radius: 8px; margin-top: 20px;">
        <p><?= htmlspecialchars($postContent) ?></p>
    </div>
    <div style="margin-top: 30px; padding: 15px; background: #e8f4f8; border-radius: 4px;">
        <strong>Route Pattern:</strong> <code>/blog/[slug]</code><br>
        <strong>Access via:</strong> <code>$page->getParam('slug')</code>
    </div>
</article>
