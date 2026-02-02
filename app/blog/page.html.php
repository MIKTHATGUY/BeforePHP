<?php
// Blog index view
?>
<div style="max-width: 800px; margin: 0 auto; padding: 20px;">
    <h1>Blog</h1>
    <p style="color: #666; margin-bottom: 30px;">Dynamic routing demo - click any post to see [slug] in action</p>
    
    <div style="display: grid; gap: 20px;">
        <?php foreach ($posts as $post): ?>
        <article style="border: 1px solid #ddd; padding: 20px; border-radius: 8px;">
            <h2 style="margin-top: 0;">
                <a href="/blog/<?= htmlspecialchars($post['slug']) ?>" style="text-decoration: none; color: #3498db;">
                    <?= htmlspecialchars($post['title']) ?>
                </a>
            </h2>
            <p style="color: #666;"><?= htmlspecialchars($post['excerpt']) ?></p>
            <code style="background: #f4f4f4; padding: 4px 8px; border-radius: 4px;">
                Route: /blog/<?= htmlspecialchars($post['slug']) ?>
            </code>
        </article>
        <?php endforeach; ?>
    </div>
    
    <div style="margin-top: 40px; padding: 20px; background: #e8f4f8; border-radius: 8px;">
        <h3>Dynamic Route Patterns Demo</h3>
        <ul>
            <li><a href="/blog/hello-world"><code>/blog/[slug]</code></a> - Single dynamic segment</li>
            <li><a href="/shop/clothes/tops"><code>/shop/[...slug]</code></a> - Catch-all (required)</li>
            <li><a href="/docs"><code>/docs/[[...slug]]</code></a> - Optional catch-all (no segments)</li>
            <li><a href="/docs/api/routing"><code>/docs/[[...slug]]</code></a> - Optional catch-all (with segments)</li>
        </ul>
    </div>
</div>
