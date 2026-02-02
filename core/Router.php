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
        $pagesRoot = Config::get('paths.pages');

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
        if (!$this->dfs(Config::get('paths.pages'))) {
            // Route not found, try to find 404 page using same DFS
            $this->isNotFound = true;
            $this->pageUriPath = "404";
            $this->controllerPath = null;
            $this->viewPath = null;
            $this->layoutPath = null;
            $this->dfs(Config::get('paths.pages'));
        }
    }

    private function dfs(string $currentFolderPath, ?string $currentLayoutPath = null): bool
    {
        // Check for layout in the current folder only once per visit
        if (file_exists($currentFolderPath . "/layout.php")) {
            $currentLayoutPath = $currentFolderPath . "/layout.php";
        }

        $fullPath = Config::get('paths.pages');
        if ($this->pageUriPath !== "") {
            $fullPath .= "/" . $this->pageUriPath;
        }

        if ($this->stripGroupsFromPath($currentFolderPath) === $fullPath) {
            $hasController = file_exists($currentFolderPath . "/page.php");
            $hasView = file_exists($currentFolderPath . "/page.html.php");
            
            if ($hasController || $hasView) {
                $this->matchedFolderPath = $currentFolderPath;
                if ($hasController) {
                    $this->controllerPath = $currentFolderPath . "/page.php";
                }
                if ($hasView) {
                    $this->viewPath = $currentFolderPath . "/page.html.php";
                }

                if ($currentLayoutPath) {
                    $this->layoutPath = $currentLayoutPath;
                }
                return true;
            }
        }

        $folderPaths = glob($currentFolderPath . "/*", GLOB_ONLYDIR);
        if ($folderPaths === false) {
            return false;
        }

        foreach ($folderPaths as $folderPath) {
            if ($this->dfs($folderPath, $currentLayoutPath)) {
                return true;
            }
        }

        return false;
    }

    private function stripGroupsFromPath(string $currentFolderPath): string
    {
        return preg_replace("/\([^)]*\)\//", "", $currentFolderPath);
    }
}
