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
        
        $page = new Page(
            $router->getControllerPath(), 
            $router->getViewPath(), 
            $router->getLayoutPath(), 
            $router->getParams(),
            $router->getPostData(),
            $router->getMethod()
        );
        
        // Load and execute proxies
        $this->loadProxies();
        $proxies = Proxy::getForRoute($_SERVER['REQUEST_URI'], $router->getMatchedFolderPath() ?? '');
        
        // Pre-validate before rendering
        $this->preValidate($page, $router->getParams(), $router->getPostData());
        
        try {
            // Execute proxy chain, then render page
            $response = Proxy::execute($proxies, function ($request) use ($page) {
                // Pass request attributes to page if needed
                return $page->render();
            });
            
            // If middleware returned a response, output it
            if (is_string($response)) {
                echo $response;
            }
        } catch (ValidationException $e) {
            http_response_code(422);
            $this->handleValidationError($e, $router, $page);
        } catch (\Throwable $error) {
            $this->handleError($error, $router, $page);
        }
    }
    
    /**
     * Load proxy configuration from proxies/config.php if exists
     */
    private function loadProxies(): void
    {
        $configPath = __DIR__ . '/../proxies/config.php';
        if (file_exists($configPath)) {
            require $configPath;
        }
    }
    
    /**
     * Pre-validate page inputs before rendering
     */
    private function preValidate(Page $page, array $params, array $postData): void
    {
        // Allow page to setup validation in a pre-validation phase
        $controllerPath = $page->getControllerPath();
        if ($controllerPath && file_exists($controllerPath)) {
            ob_start();
            try {
                // Set up the input variables
                foreach ($params as $key => $value) {
                    $varName = '_' . $key;
                    $$varName = $value;
                }
                foreach ($postData as $key => $value) {
                    $varName = '_' . $key;
                    $$varName = $value;
                }
                
                // Include just the beginning of the controller to capture validation setup
                $lines = file($controllerPath);
                $setupCode = '';
                foreach ($lines as $line) {
                    $trimmed = trim($line);
                    // Capture validator setup lines and use statements
                    if (strpos($trimmed, 'Validator') !== false || 
                        strpos($trimmed, 'use ') === 0 ||
                        strpos($trimmed, '<?php') === 0 ||
                        empty($trimmed)) {
                        $setupCode .= $line;
                    } else {
                        break;
                    }
                }
                eval('?>' . $setupCode);
            } catch (\Throwable $e) {
                // Ignore errors in pre-validation
            }
            ob_end_clean();
        }
    }
    
    private function handleError(\Throwable $error, Router $router, Page $page): void
    {
        http_response_code(500);
        
        $errorPath = $router->findErrorPath();
        
        if ($errorPath !== null) {
            $errorMessage = $error->getMessage();
            $errorCode = $error->getCode();
            $errorFile = $error->getFile();
            $errorLine = $error->getLine();
            $errorTrace = $error->getTraceAsString();
            
            require $errorPath;
        } else {
            echo '<h1>500 Internal Server Error</h1>';
            echo '<p>An unexpected error occurred.</p>';
            if (ini_get('display_errors')) {
                echo '<pre>' . htmlspecialchars($error->getMessage()) . '</pre>';
            }
        }
    }
    
    private function handleValidationError(ValidationException $error, Router $router, Page $page): void
    {
        $errorPath = $router->findErrorPath();
        
        if ($errorPath !== null) {
            $validationErrors = $error->getErrors();
            $validationMessage = $error->getMessage();
            
            require $errorPath;
        } else {
            echo '<h1>Validation Error</h1>';
            echo '<p>The following validation errors occurred:</p>';
            echo '<ul>';
            foreach ($error->getErrors() as $field => $message) {
                echo '<li><strong>' . htmlspecialchars($field) . ':</strong> ' . htmlspecialchars($message) . '</li>';
            }
            echo '</ul>';
            echo '<p><a href="javascript:history.back()">← Go Back</a></p>';
        }
    }
}
