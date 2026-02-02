<?php
// Search page view
?>
<div style="max-width: 800px; margin: 0 auto; padding: 20px;">
    <h1 style="color: #2c3e50;">Search Demo</h1>
    <p style="color: #666;">This page demonstrates query string parameter handling</p>
    
    <!-- Search Form -->
    <form method="GET" action="/search" style="background: #f5f5f5; padding: 20px; border-radius: 8px; margin: 20px 0;">
        <div style="margin-bottom: 15px;">
            <label style="display: block; font-weight: bold; margin-bottom: 5px;">Search Query:</label>
            <input type="text" name="q" value="<?= htmlspecialchars($searchQuery) ?>" 
                   placeholder="Enter search term..." 
                   style="width: 100%; padding: 10px; font-size: 16px; border: 1px solid #ddd; border-radius: 4px;">
        </div>
        
        <div style="margin-bottom: 15px;">
            <label style="display: block; font-weight: bold; margin-bottom: 5px;">Category:</label>
            <select name="category" style="width: 100%; padding: 10px; font-size: 16px; border: 1px solid #ddd; border-radius: 4px;">
                <option value="all" <?= $category === 'all' ? 'selected' : '' ?>>All Categories</option>
                <option value="tutorials" <?= $category === 'tutorials' ? 'selected' : '' ?>>Tutorials</option>
                <option value="docs" <?= $category === 'docs' ? 'selected' : '' ?>>Documentation</option>
            </select>
        </div>
        
        <button type="submit" style="background: #3498db; color: white; padding: 12px 24px; border: none; border-radius: 4px; font-size: 16px; cursor: pointer;">
            Search
        </button>
        
        <?php if (!empty($searchQuery)): ?>
        <a href="/search" style="margin-left: 10px; color: #666; text-decoration: none;">Clear</a>
        <?php endif; ?>
    </form>
    
    <!-- Query String Info -->
    <div style="background: #e8f4f8; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
        <h3 style="margin-top: 0;">Query String Parameters:</h3>
        <ul style="margin: 0;">
            <li><code>q</code> = <?= $searchQuery ? '<strong>' . htmlspecialchars($searchQuery) . '</strong>' : '<em>(not set)</em>' ?></li>
            <li><code>category</code> = <strong><?= htmlspecialchars($category) ?></strong></li>
            <li><code>page</code> = <strong><?= $page ?></strong></li>
        </ul>
        <p style="margin-bottom: 0; font-size: 0.9em; color: #666;">
            Access any query param directly as a variable: <code>$_q</code>, <code>$_category</code>, <code>$_page</code>
        </p>
    </div>
    
    <!-- Search Results -->
    <?php if (!empty($searchQuery)): ?>
        <h2>Results for "<?= htmlspecialchars($searchQuery) ?>"</h2>
        <p style="color: #666;">Found <?= $totalResults ?> result(s)</p>
        
        <?php if (empty($displayResults)): ?>
            <div style="background: #fff3cd; padding: 20px; border-radius: 4px; color: #856404;">
                No results found. Try a different search term.
            </div>
        <?php else: ?>
            <div style="display: grid; gap: 15px;">
                <?php foreach ($displayResults as $result): ?>
                    <div style="background: white; padding: 15px; border: 1px solid #ddd; border-radius: 4px;">
                        <h3 style="margin-top: 0; color: #3498db;"><?= htmlspecialchars($result['title']) ?></h3>
                        <span style="display: inline-block; background: #e0e0e0; padding: 2px 8px; border-radius: 3px; font-size: 0.85em; text-transform: uppercase;">
                            <?= htmlspecialchars($result['category']) ?>
                        </span>
                        <p style="margin: 10px 0 0 0; color: #666;"><?= htmlspecialchars($result['excerpt']) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div style="margin-top: 30px; text-align: center;">
                    <p>Page <?= $page ?> of <?= $totalPages ?></p>
                    <div style="display: flex; justify-content: center; gap: 10px;">
                        <?php if ($page > 1): ?>
                            <a href="<?= htmlspecialchars($baseUrl) ?>?q=<?= urlencode($searchQuery) ?>&category=<?= urlencode($category) ?>&page=<?= $page - 1 ?>" 
                               style="padding: 8px 16px; background: #3498db; color: white; text-decoration: none; border-radius: 4px;">
                                ← Previous
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($page < $totalPages): ?>
                            <a href="<?= htmlspecialchars($baseUrl) ?>?q=<?= urlencode($searchQuery) ?>&category=<?= urlencode($category) ?>&page=<?= $page + 1 ?>" 
                               style="padding: 8px 16px; background: #3498db; color: white; text-decoration: none; border-radius: 4px;">
                                Next →
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    <?php else: ?>
        <div style="background: #f8f9fa; padding: 20px; border-radius: 4px; text-align: center; color: #666;">
            <p>Enter a search term above to see query string handling in action!</p>
            <p style="font-size: 0.9em; margin-bottom: 0;">
                Try: <code>/search?q=php&category=tutorials</code>
            </p>
        </div>
    <?php endif; ?>
    
    <!-- Example URLs -->
    <div style="margin-top: 40px; padding: 20px; background: #f0f0f0; border-radius: 8px;">
        <h3 style="margin-top: 0;">Try These URLs:</h3>
        <ul style="line-height: 2;">
            <li><a href="/search?q=php" style="color: #3498db;"><code>/search?q=php</code></a> - Search for "php"</li>
            <li><a href="/search?q=routing&category=tutorials" style="color: #3498db;"><code>/search?q=routing&category=tutorials</code></a> - Search with category filter</li>
            <li><a href="/search?q=a&page=2" style="color: #3498db;"><code>/search?q=a&page=2</code></a> - Search with pagination</li>
            <li><a href="/search?category=docs" style="color: #3498db;"><code>/search?category=docs</code></a> - Filter by category only</li>
        </ul>
    </div>
</div>
