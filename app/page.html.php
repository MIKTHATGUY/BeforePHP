<?php
// Home page view
?>
<div style="max-width: 1000px; margin: 0 auto; padding: 20px;">
    <h1 style="color: #2c3e50; border-bottom: 3px solid #3498db; padding-bottom: 10px;">
        Welcome to NextPHP
    </h1>
    
    <p style="font-size: 1.2em; color: #555; margin-bottom: 30px;">
        A lightweight, file-based PHP framework inspired by Next.js.
        No complex routing configuration needed - just create files and folders.
    </p>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-bottom: 40px;">
        <?php foreach ($features as $feature): ?>
        <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; border-left: 4px solid #3498db;">
            <h3 style="margin-top: 0; color: #2c3e50;">
                <?php if ($feature['link']): ?>
                    <a href="<?= htmlspecialchars($feature['link']) ?>" style="text-decoration: none; color: inherit;">
                        <?= htmlspecialchars($feature['title']) ?> →
                    </a>
                <?php else: ?>
                    <?= htmlspecialchars($feature['title']) ?>
                <?php endif; ?>
            </h3>
            <p style="color: #666; margin-bottom: 0;"><?= htmlspecialchars($feature['description']) ?></p>
        </div>
        <?php endforeach; ?>
    </div>
    
    <div style="background: #e8f4f8; padding: 25px; border-radius: 8px; margin-bottom: 30px;">
        <h2 style="margin-top: 0; color: #2c3e50;">Dynamic Routes Demo</h2>
        <p style="margin-bottom: 15px;">Test the different dynamic route patterns:</p>
        <div style="display: flex; flex-wrap: wrap; gap: 10px;">
            <?php foreach ($demoLinks as $link): ?>
            <a href="<?= htmlspecialchars($link['url']) ?>" 
               style="display: inline-block; background: #3498db; color: white; padding: 10px 20px; 
                      text-decoration: none; border-radius: 4px; font-size: 0.9em;">
                <?= htmlspecialchars($link['text']) ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    
    <div style="background: #f5f5f5; padding: 20px; border-radius: 8px;">
        <h3 style="margin-top: 0;">Quick Start</h3>
        <ol style="line-height: 1.8;">
            <li>Create a folder in <code>pages/</code> (e.g., <code>pages/about/</code>)</li>
            <li>Add <code>page.php</code> for controller logic</li>
            <li>Add <code>page.html.php</code> for the view</li>
            <li>Visit <code>/about</code> - that's it!</li>
        </ol>
        <p style="margin-bottom: 0;">
            <a href="/blog" style="color: #3498db; text-decoration: none; font-weight: bold;">
                See the blog with dynamic routing →
            </a>
        </p>
    </div>
</div>
