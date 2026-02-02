<?php
declare(strict_types=1);

namespace NextPHP\Core;

/**
 * Metadata Manager - Next.js style metadata system
 * 
 * Supports:
 * - Static metadata via $metadata array
 * - Dynamic metadata generation
 * - Layout inheritance
 * - Open Graph, Twitter Cards, SEO meta tags
 */
class Metadata
{
    private static array $pageMetadata = [];
    private static array $defaultMetadata = [
        'charset' => 'utf-8',
        'viewport' => 'width=device-width, initial-scale=1',
    ];

    /**
     * Set metadata for the current page
     * Call this in page.php to define metadata
     */
    public static function set(array $metadata): void
    {
        self::$pageMetadata = array_merge(self::$pageMetadata, $metadata);
    }

    /**
     * Get all metadata including defaults
     */
    public static function get(): array
    {
        return array_merge(self::$defaultMetadata, self::$pageMetadata);
    }

    /**
     * Get specific metadata value
     */
    public static function getValue(string $key, mixed $default = null): mixed
    {
        $metadata = self::get();
        return $metadata[$key] ?? $default;
    }

    /**
     * Reset metadata (useful for testing)
     */
    public static function reset(): void
    {
        self::$pageMetadata = [];
    }

    /**
     * Render all metadata as HTML
     * Call this in your layout.php <head> section
     */
    public static function render(): string
    {
        $metadata = self::get();
        $html = [];

        // Default meta tags
        $html[] = '<meta charset="' . htmlspecialchars($metadata['charset'] ?? 'utf-8') . '">';
        $html[] = '<meta name="viewport" content="' . htmlspecialchars($metadata['viewport'] ?? 'width=device-width, initial-scale=1') . '">';

        // Title
        if (isset($metadata['title'])) {
            $title = self::formatTitle($metadata['title']);
            $html[] = '<title>' . htmlspecialchars($title) . '</title>';
            
            // Open Graph title
            $html[] = '<meta property="og:title" content="' . htmlspecialchars($title) . '">';
            // Twitter title
            $html[] = '<meta name="twitter:title" content="' . htmlspecialchars($title) . '">';
        }

        // Description
        if (isset($metadata['description'])) {
            $html[] = '<meta name="description" content="' . htmlspecialchars($metadata['description']) . '">';
            $html[] = '<meta property="og:description" content="' . htmlspecialchars($metadata['description']) . '">';
            $html[] = '<meta name="twitter:description" content="' . htmlspecialchars($metadata['description']) . '">';
        }

        // Keywords
        if (isset($metadata['keywords'])) {
            $keywords = is_array($metadata['keywords']) 
                ? implode(', ', $metadata['keywords']) 
                : $metadata['keywords'];
            $html[] = '<meta name="keywords" content="' . htmlspecialchars($keywords) . '">';
        }

        // Canonical URL
        if (isset($metadata['canonical'])) {
            $html[] = '<link rel="canonical" href="' . htmlspecialchars($metadata['canonical']) . '">';
        }

        // Robots
        if (isset($metadata['robots'])) {
            $robots = is_array($metadata['robots']) 
                ? implode(', ', $metadata['robots']) 
                : $metadata['robots'];
            $html[] = '<meta name="robots" content="' . htmlspecialchars($robots) . '">';
        }

        // Author
        if (isset($metadata['authors'])) {
            $authors = is_array($metadata['authors']) ? $metadata['authors'] : [$metadata['authors']];
            foreach ($authors as $author) {
                if (is_array($author) && isset($author['name'])) {
                    $html[] = '<meta name="author" content="' . htmlspecialchars($author['name']) . '">';
                    if (isset($author['url'])) {
                        $html[] = '<link rel="author" href="' . htmlspecialchars($author['url']) . '">';
                    }
                } else {
                    $html[] = '<meta name="author" content="' . htmlspecialchars($author) . '">';
                }
            }
        } elseif (isset($metadata['author'])) {
            $html[] = '<meta name="author" content="' . htmlspecialchars($metadata['author']) . '">';
        }

        // Open Graph
        if (isset($metadata['openGraph'])) {
            $html[] = self::renderOpenGraph($metadata['openGraph']);
        }

        // Twitter
        if (isset($metadata['twitter'])) {
            $html[] = self::renderTwitter($metadata['twitter']);
        }

        // Icons
        if (isset($metadata['icons'])) {
            $html[] = self::renderIcons($metadata['icons']);
        }

        // Theme Color
        if (isset($metadata['themeColor'])) {
            $color = is_array($metadata['themeColor']) 
                ? $metadata['themeColor']['color'] ?? $metadata['themeColor'] 
                : $metadata['themeColor'];
            $media = is_array($metadata['themeColor']) && isset($metadata['themeColor']['media'])
                ? ' media="' . htmlspecialchars($metadata['themeColor']['media']) . '"'
                : '';
            $html[] = '<meta name="theme-color" content="' . htmlspecialchars($color) . '"' . $media . '>';
        }

        // Custom CSS
        if (isset($metadata['css'])) {
            $cssFiles = is_array($metadata['css']) ? $metadata['css'] : [$metadata['css']];
            foreach ($cssFiles as $css) {
                $html[] = '<link rel="stylesheet" href="' . htmlspecialchars($css) . '">';
            }
        }

        // Custom JavaScript
        if (isset($metadata['js'])) {
            $jsFiles = is_array($metadata['js']) ? $metadata['js'] : [$metadata['js']];
            foreach ($jsFiles as $js) {
                $html[] = '<script src="' . htmlspecialchars($js) . '"></script>';
            }
        }

        // Other meta tags
        if (isset($metadata['other'])) {
            foreach ($metadata['other'] as $meta) {
                if (is_array($meta)) {
                    $attrs = [];
                    foreach ($meta as $key => $value) {
                        $attrs[] = $key . '="' . htmlspecialchars($value) . '"';
                    }
                    $html[] = '<meta ' . implode(' ', $attrs) . '>';
                }
            }
        }

        return implode("\n    ", array_filter($html));
    }

    /**
     * Render Open Graph metadata
     */
    private static function renderOpenGraph(array $og): string
    {
        $html = [];
        
        if (isset($og['title'])) {
            $html[] = '<meta property="og:title" content="' . htmlspecialchars($og['title']) . '">';
        }
        if (isset($og['description'])) {
            $html[] = '<meta property="og:description" content="' . htmlspecialchars($og['description']) . '">';
        }
        if (isset($og['url'])) {
            $html[] = '<meta property="og:url" content="' . htmlspecialchars($og['url']) . '">';
        }
        if (isset($og['siteName'])) {
            $html[] = '<meta property="og:site_name" content="' . htmlspecialchars($og['siteName']) . '">';
        }
        if (isset($og['type'])) {
            $html[] = '<meta property="og:type" content="' . htmlspecialchars($og['type']) . '">';
        }
        if (isset($og['locale'])) {
            $html[] = '<meta property="og:locale" content="' . htmlspecialchars($og['locale']) . '">';
        }
        if (isset($og['images'])) {
            $images = is_array($og['images']) && isset($og['images']['url']) 
                ? [$og['images']] 
                : $og['images'];
            foreach ($images as $image) {
                if (is_array($image)) {
                    if (isset($image['url'])) {
                        $html[] = '<meta property="og:image" content="' . htmlspecialchars($image['url']) . '">';
                    }
                    if (isset($image['width'])) {
                        $html[] = '<meta property="og:image:width" content="' . htmlspecialchars((string)$image['width']) . '">';
                    }
                    if (isset($image['height'])) {
                        $html[] = '<meta property="og:image:height" content="' . htmlspecialchars((string)$image['height']) . '">';
                    }
                    if (isset($image['alt'])) {
                        $html[] = '<meta property="og:image:alt" content="' . htmlspecialchars($image['alt']) . '">';
                    }
                } else {
                    $html[] = '<meta property="og:image" content="' . htmlspecialchars($image) . '">';
                }
            }
        }

        return implode("\n    ", $html);
    }

    /**
     * Render Twitter Card metadata
     */
    private static function renderTwitter(array $twitter): string
    {
        $html = [];
        
        if (isset($twitter['card'])) {
            $html[] = '<meta name="twitter:card" content="' . htmlspecialchars($twitter['card']) . '">';
        }
        if (isset($twitter['site'])) {
            $html[] = '<meta name="twitter:site" content="' . htmlspecialchars($twitter['site']) . '">';
        }
        if (isset($twitter['creator'])) {
            $html[] = '<meta name="twitter:creator" content="' . htmlspecialchars($twitter['creator']) . '">';
        }
        if (isset($twitter['title'])) {
            $html[] = '<meta name="twitter:title" content="' . htmlspecialchars($twitter['title']) . '">';
        }
        if (isset($twitter['description'])) {
            $html[] = '<meta name="twitter:description" content="' . htmlspecialchars($twitter['description']) . '">';
        }
        if (isset($twitter['images'])) {
            $images = is_array($twitter['images']) ? $twitter['images'] : [$twitter['images']];
            foreach ($images as $image) {
                if (is_array($image) && isset($image['url'])) {
                    $html[] = '<meta name="twitter:image" content="' . htmlspecialchars($image['url']) . '">';
                    if (isset($image['alt'])) {
                        $html[] = '<meta name="twitter:image:alt" content="' . htmlspecialchars($image['alt']) . '">';
                    }
                } else {
                    $html[] = '<meta name="twitter:image" content="' . htmlspecialchars($image) . '">';
                }
            }
        }

        return implode("\n    ", $html);
    }

    /**
     * Render icons metadata
     */
    private static function renderIcons(array $icons): string
    {
        $html = [];
        
        // Shortcut for single icon
        if (isset($icons['icon'])) {
            $html[] = '<link rel="icon" href="' . htmlspecialchars($icons['icon']) . '">';
        }
        if (isset($icons['shortcut'])) {
            $html[] = '<link rel="shortcut icon" href="' . htmlspecialchars($icons['shortcut']) . '">';
        }
        if (isset($icons['apple'])) {
            $html[] = '<link rel="apple-touch-icon" href="' . htmlspecialchars($icons['apple']) . '">';
        }
        if (isset($icons['other'])) {
            foreach ($icons['other'] as $icon) {
                if (is_array($icon)) {
                    $rel = $icon['rel'] ?? 'icon';
                    $href = $icon['url'] ?? $icon['href'] ?? '';
                    $sizes = isset($icon['sizes']) ? ' sizes="' . htmlspecialchars($icon['sizes']) . '"' : '';
                    $type = isset($icon['type']) ? ' type="' . htmlspecialchars($icon['type']) . '"' : '';
                    $html[] = '<link rel="' . htmlspecialchars($rel) . '" href="' . htmlspecialchars($href) . '"' . $sizes . $type . '>';
                }
            }
        }

        return implode("\n    ", $html);
    }

    /**
     * Format title with template
     */
    private static function formatTitle($title): string
    {
        if (is_array($title)) {
            $template = $title['template'] ?? '%s';
            $absolute = $title['absolute'] ?? null;
            $defaultTitle = $title['default'] ?? '';
            
            if ($absolute) {
                return $absolute;
            }
            
            return str_replace('%s', $defaultTitle, $template);
        }
        
        return $title;
    }

    /**
     * Get current URL
     */
    public static function getCurrentUrl(): string
    {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        return $protocol . '://' . $host . $uri;
    }

    /**
     * Auto-generate metadata for a page
     */
    public static function generate(array $options = []): array
    {
        $defaults = [
            'title' => null,
            'description' => null,
            'canonical' => self::getCurrentUrl(),
            'robots' => ['index', 'follow'],
        ];
        
        return array_merge($defaults, $options);
    }
}
