<?php
declare(strict_types=1);

namespace NextPHP\Core;

use NextPHP\Config;

class Router
{
    private string $pageUriPath;
    private ?string $controllerPath = null;
    private ?string $viewPath = null;
    private ?string $layoutPath = null;
    private ?string $matchedFolderPath = null;
    private array $params = [];
    private bool $isNotFound = false;

    public function __construct(string $request_uri)
    {
        $this->parseUri($request_uri);
        $this->resolveRoute();
    }

    public function getControllerPath(): ?string
    {
        return $this->controllerPath;
    }

    public function getViewPath(): ?string
    {
        return $this->viewPath;
    }

    public function getLayoutPath(): ?string
    {
        return $this->layoutPath;
    }
    

    public function getParams(): array
    {
        return $this->params;
    }

    public function getParam(string $key, $default = null)
    {
        return $this->params[$key] ?? $default;
    }

    /**
     * Get POST data from form submissions
     */
    public function getPostData(): array
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $_POST;
        }
        return [];
    }

    /**
     * Check if current request is POST
     */
    public function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Get request method (GET, POST, PUT, DELETE, etc.)
     */
    public function getMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    public function isNotFound(): bool
    {
        return $this->isNotFound;
    }

    public function getMatchedFolderPath(): ?string
    {
        return $this->matchedFolderPath;
    }

    public function findErrorPath(): ?string
    {
        if ($this->matchedFolderPath === null) {
            return null;
        }

        $currentPath = $this->matchedFolderPath;
        $pagesRoot = Config::get('paths.app');

        // Search upward from matched folder to root
        while (strlen($currentPath) >= strlen($pagesRoot)) {
            $errorPath = $currentPath . "/error.php";
            if (file_exists($errorPath)) {
                return $errorPath;
            }
            $parentPath = dirname($currentPath);
            if ($parentPath === $currentPath || $parentPath === false) {
                break;
            }
            $currentPath = $parentPath;
        }

        return null;
    }

    private function parseUri(string $request_uri): void
    {
        $this->pageUriPath = trim(parse_url($request_uri, PHP_URL_PATH), "/");

        $rootName = basename(Config::get('paths.root'));
        if (str_starts_with($this->pageUriPath, $rootName)) {
            $this->pageUriPath = substr($this->pageUriPath, strlen($rootName) + 1);
        }

        $query = parse_url($request_uri, PHP_URL_QUERY);
        if ($query) {
            parse_str($query, $this->params);
        }
    }

    private function resolveRoute(): void
    {
        $uriSegments = $this->pageUriPath !== "" ? explode("/", $this->pageUriPath) : [];
        // Start with existing params (query string params already parsed in parseUri)
        $params = $this->params;
        
        if (!$this->dfs(Config::get('paths.app'), $uriSegments, 0, null, $params)) {
            // Route not found, try to find 404 page
            $this->isNotFound = true;
            $this->pageUriPath = "404";
            $uriSegments = ["404"];
            $params = [];
            $this->controllerPath = null;
            $this->viewPath = null;
            $this->layoutPath = null;
            $this->dfs(Config::get('paths.app'), $uriSegments, 0, null, $params);
        }
    }

    /**
     * DFS with URI segment tracking
     * 
     * @param string $currentFolderPath Current folder being checked
     * @param array $uriSegments Full URI segments array
     * @param int $uriIndex Current position in URI segments
     * @param string|null $currentLayoutPath Current layout path (for inheritance)
     * @param array $params Route parameters collected so far
     * @return bool True if route matched
     */
    private function dfs(string $currentFolderPath, array $uriSegments, int $uriIndex, ?string $currentLayoutPath, array &$params): bool
    {
        // Check for layout in current folder
        if (file_exists($currentFolderPath . "/layout.php")) {
            $currentLayoutPath = $currentFolderPath . "/layout.php";
        }

        // Check if this folder has page files and matches the URI at current position
        $hasController = file_exists($currentFolderPath . "/page.php");
        $hasView = file_exists($currentFolderPath . "/page.html.php");
        
        // This folder matches if we're at the end of URI and have a page
        if ($hasController || $hasView) {
            if ($uriIndex === count($uriSegments)) {
                // Exact match - all URI segments consumed
                $this->matchedFolderPath = $currentFolderPath;
                $this->params = $params;
                if ($hasController) {
                    $this->controllerPath = $currentFolderPath . "/page.php";
                }
                if ($hasView) {
                    $this->viewPath = $currentFolderPath . "/page.html.php";
                }
                $this->layoutPath = $currentLayoutPath;
                return true;
            }
        }

        // If we've consumed all URI segments but no page found here, return false
        if ($uriIndex >= count($uriSegments)) {
            return false;
        }

        // Get current URI segment to match
        $currentSegment = $uriSegments[$uriIndex];

        // Look for matching subfolders
        $folderPaths = glob($currentFolderPath . "/*", GLOB_ONLYDIR);
        if ($folderPaths === false) {
            return false;
        }

        foreach ($folderPaths as $folderPath) {
            $folderName = basename($folderPath);
            
            // Check if folder matches the current URI segment
            if ($this->folderMatchesSegment($folderName, $currentSegment, $params)) {
                // Static or single dynamic match - advance URI index by 1
                if ($this->dfs($folderPath, $uriSegments, $uriIndex + 1, $currentLayoutPath, $params)) {
                    return true;
                }
                
                // If match failed and we set a param, remove it
                if (preg_match('/^\[([^\]]+)\]$/', $folderName, $matches)) {
                    unset($params[$matches[1]]);
                }
            }
            elseif ($this->isCatchAllFolder($folderName)) {
                // Catch-all folder - consume all remaining segments
                $newParams = $params;
                $this->extractCatchAllParams($folderName, $uriSegments, $uriIndex, $newParams);
                
                if ($this->dfs($folderPath, $uriSegments, count($uriSegments), $currentLayoutPath, $newParams)) {
                    $params = $newParams;
                    return true;
                }
            }
            elseif ($this->isRouteGroup($folderName)) {
                // Route group folder - enter it without consuming URI segment
                // This allows finding routes inside groups like (codes)/404
                if ($this->dfs($folderPath, $uriSegments, $uriIndex, $currentLayoutPath, $params)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check if folder name matches URI segment
     */
    private function folderMatchesSegment(string $folderName, string $uriSegment, array &$params): bool
    {
        // Strip route groups (auth) → auth
        $folderName = preg_replace('/^\(([^)]+)\)$/', '$1', $folderName);
        
        // Static match
        if ($folderName === $uriSegment) {
            return true;
        }
        
        // Dynamic segment [slug]
        if (preg_match('/^\[([^\].]+)\]$/', $folderName, $matches)) {
            $params[$matches[1]] = $uriSegment;
            return true;
        }
        
        return false;
    }

    /**
     * Check if folder is a catch-all pattern
     */
    private function isCatchAllFolder(string $folderName): bool
    {
        return preg_match('/^\[\.\.\.[^\]]+\]$/', $folderName) || 
               preg_match('/^\[\[\.\.\.[^\]]+\]\]$/', $folderName);
    }

    /**
     * Extract parameters from catch-all folder
     */
    private function extractCatchAllParams(string $folderName, array $uriSegments, int $uriIndex, array &$params): void
    {
        // Required catch-all [...slug]
        if (preg_match('/^\[\.\.\.([^\]]+)\]$/', $folderName, $matches)) {
            $paramName = $matches[1];
            $remaining = array_slice($uriSegments, $uriIndex);
            $params[$paramName] = $remaining;
        }
        // Optional catch-all [[...slug]]
        elseif (preg_match('/^\[\[\.\.\.([^\]]+)\]\]$/', $folderName, $matches)) {
            $paramName = $matches[1];
            $remaining = array_slice($uriSegments, $uriIndex);
            if (!empty($remaining)) {
                $params[$paramName] = $remaining;
            }
            // If empty, param stays unset (undefined like Next.js)
        }
    }

    /**
     * Check if folder is a route group (parentheses notation)
     */
    private function isRouteGroup(string $folderName): bool
    {
        return preg_match('/^\([^)]+\)$/', $folderName) === 1;
    }

    private function stripGroupsFromPath(string $currentFolderPath): string
    {
        return preg_replace("/\([^)]*\)\//", "", $currentFolderPath);
    }
}
