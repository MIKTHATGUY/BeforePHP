<?php
declare(strict_types=1);

namespace NextPHP\Core;

class App
{
    public function run(): void
    {
        $router = new Router($_SERVER["REQUEST_URI"]);
        
        if ($router->isNotFound()) {
            http_response_code(404);
        }
        
        $page = new Page($router->getControllerPath(), $router->getViewPath(), $router->getLayoutPath(), $router->getParams());
        
        try {
            $page->render();
        } catch (\Throwable $error) {
            $this->handleError($error, $router, $page);
        }
    }
    
    private function handleError(\Throwable $error, Router $router, Page $page): void
    {
        http_response_code(500);
        
        // Find nearest error.php file
        $errorPath = $router->findErrorPath();
        
        if ($errorPath !== null) {
            // Make error available to error.php
            $errorMessage = $error->getMessage();
            $errorCode = $error->getCode();
            $errorFile = $error->getFile();
            $errorLine = $error->getLine();
            $errorTrace = $error->getTraceAsString();
            
            require $errorPath;
        } else {
            // No error boundary found, show default error
            echo '<h1>500 Internal Server Error</h1>';
            echo '<p>An unexpected error occurred.</p>';
            if (ini_get('display_errors')) {
                echo '<pre>' . htmlspecialchars($error->getMessage()) . '</pre>';
            }
        }
    }
}