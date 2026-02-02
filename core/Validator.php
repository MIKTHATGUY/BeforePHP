<?php
declare(strict_types=1);

namespace NextPHP\Core;

/**
 * Auto-Validation System for Route Params, Query Strings, and POST Data
 * 
 * Validates variables automatically based on schema defined in page.php
 */
class Validator
{
    private static bool $enabled = true;
    private static array $schema = [];
    private static array $errors = [];
    
    /**
     * Enable or disable auto-validation
     */
    public static function enable(bool $enabled = true): void
    {
        self::$enabled = $enabled;
    }
    
    /**
     * Check if validation is enabled
     */
    public static function isEnabled(): bool
    {
        return self::$enabled;
    }
    
    /**
     * Set validation schema
     * 
     * Example:
     * Validator::schema([
     *     'id' => 'int|min:1|max:1000',
     *     'email' => 'email|required',
     *     'age' => 'int|min:0|max:150',
     *     'name' => 'string|min:2|max:50',
     *     'url' => 'url',
     *     'status' => 'in:active,inactive,pending',
     * ]);
     */
    public static function schema(array $schema): void
    {
        self::$schema = $schema;
    }
    
    /**
     * Validate data against schema
     * Returns validated data or throws ValidationException on failure
     */
    public static function validate(array $data): array
    {
        if (!self::$enabled) {
            return $data;
        }
        
        self::$errors = [];
        $validated = [];
        
        foreach (self::$schema as $field => $rules) {
            $value = $data[$field] ?? null;
            $rulesArray = is_array($rules) ? $rules : explode('|', $rules);
            
            // Check required
            $isRequired = in_array('required', $rulesArray);
            if ($isRequired && ($value === null || $value === '')) {
                self::$errors[$field] = "{$field} is required";
                continue;
            }
            
            // Skip validation if empty and not required
            if (!$isRequired && ($value === null || $value === '')) {
                $validated[$field] = $value;
                continue;
            }
            
            // Apply rules
            foreach ($rulesArray as $rule) {
                if ($rule === 'required') continue;
                
                $result = self::applyRule($field, $value, $rule);
                if ($result !== true) {
                    self::$errors[$field] = $result;
                    break;
                }
            }
            
            if (!isset(self::$errors[$field])) {
                $validated[$field] = $value;
            }
        }
        
        if (!empty(self::$errors)) {
            throw new ValidationException(self::$errors);
        }
        
        return $validated;
    }
    
    /**
     * Apply a single validation rule
     */
    private static function applyRule(string $field, $value, string $rule): bool|string
    {
        // Parse rule with parameters (e.g., "min:5")
        $parts = explode(':', $rule, 2);
        $ruleName = $parts[0];
        $param = $parts[1] ?? null;
        
        switch ($ruleName) {
            case 'int':
            case 'integer':
                if (!is_numeric($value) || (int)$value != $value) {
                    return "{$field} must be an integer";
                }
                return true;
                
            case 'float':
            case 'double':
                if (!is_numeric($value)) {
                    return "{$field} must be a number";
                }
                return true;
                
            case 'string':
                if (!is_string($value)) {
                    return "{$field} must be a string";
                }
                return true;
                
            case 'bool':
            case 'boolean':
                $validBools = [true, false, 0, 1, '0', '1', 'true', 'false', 'on', 'off', 'yes', 'no'];
                if (!in_array(strtolower((string)$value), array_map('strtolower', array_map('strval', $validBools)), true)) {
                    return "{$field} must be a boolean";
                }
                return true;
                
            case 'email':
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    return "{$field} must be a valid email address";
                }
                return true;
                
            case 'url':
                if (!filter_var($value, FILTER_VALIDATE_URL)) {
                    return "{$field} must be a valid URL";
                }
                return true;
                
            case 'ip':
                if (!filter_var($value, FILTER_VALIDATE_IP)) {
                    return "{$field} must be a valid IP address";
                }
                return true;
                
            case 'date':
                $date = strtotime($value);
                if ($date === false) {
                    return "{$field} must be a valid date";
                }
                return true;
                
            case 'alpha':
                if (!ctype_alpha($value)) {
                    return "{$field} must contain only letters";
                }
                return true;
                
            case 'alphanumeric':
                if (!ctype_alnum($value)) {
                    return "{$field} must contain only letters and numbers";
                }
                return true;
                
            case 'numeric':
                if (!is_numeric($value)) {
                    return "{$field} must be numeric";
                }
                return true;
                
            case 'min':
                if (is_string($value) && strlen($value) < (int)$param) {
                    return "{$field} must be at least {$param} characters";
                }
                if (is_numeric($value) && $value < (int)$param) {
                    return "{$field} must be at least {$param}";
                }
                return true;
                
            case 'max':
                if (is_string($value) && strlen($value) > (int)$param) {
                    return "{$field} must be at most {$param} characters";
                }
                if (is_numeric($value) && $value > (int)$param) {
                    return "{$field} must be at most {$param}";
                }
                return true;
                
            case 'regex':
                if (!preg_match($param, $value)) {
                    return "{$field} format is invalid";
                }
                return true;
                
            case 'in':
                $allowed = explode(',', $param);
                if (!in_array($value, $allowed, true)) {
                    return "{$field} must be one of: " . implode(', ', $allowed);
                }
                return true;
                
            case 'json':
                json_decode($value);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    return "{$field} must be valid JSON";
                }
                return true;
                
            case 'uuid':
                if (!preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $value)) {
                    return "{$field} must be a valid UUID";
                }
                return true;
                
            case 'slug':
                if (!preg_match('/^[a-z0-9-]+$/', $value)) {
                    return "{$field} must be a valid slug (lowercase letters, numbers, hyphens)";
                }
                return true;
                
            default:
                return true;
        }
    }
    
    /**
     * Get validation errors
     */
    public static function getErrors(): array
    {
        return self::$errors;
    }
    
    /**
     * Check if validation has errors
     */
    public static function hasErrors(): bool
    {
        return !empty(self::$errors);
    }
    
    /**
     * Reset validation state
     */
    public static function reset(): void
    {
        self::$schema = [];
        self::$errors = [];
        self::$enabled = true;
    }
}

/**
 * Validation Exception
 */
class ValidationException extends \Exception
{
    private array $errors;
    
    public function __construct(array $errors)
    {
        $this->errors = $errors;
        parent::__construct('Validation failed: ' . implode(', ', $errors));
    }
    
    public function getErrors(): array
    {
        return $this->errors;
    }
}
