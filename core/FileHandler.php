<?php
declare(strict_types=1);

namespace NextPHP\Core;

use Exception;
use RuntimeException;
use InvalidArgumentException;

class FileHandler
{
    private string $basePath;
    private array $allowedExtensions = [];
    private int $maxFileSize;
    private bool $secureMode;

    public function __construct(string $basePath = __DIR__ . '/../storage', array $options = [])
    {
        $this->basePath = rtrim($basePath, '/\\');
        $this->allowedExtensions = $options['allowed_extensions'] ?? ['txt', 'json', 'csv', 'md', 'log'];
        $this->maxFileSize = $options['max_file_size'] ?? 10 * 1024 * 1024; // 10MB default
        $this->secureMode = $options['secure_mode'] ?? true;
        
        $this->ensureDirectory($this->basePath);
    }

    /**
     * Read file contents
     */
    public function read(string $filename): string
    {
        $filepath = $this->resolvePath($filename);
        
        if (!file_exists($filepath)) {
            throw new RuntimeException("File not found: {$filename}");
        }
        
        if (!is_readable($filepath)) {
            throw new RuntimeException("File not readable: {$filename}");
        }
        
        $content = file_get_contents($filepath);
        
        if ($content === false) {
            throw new RuntimeException("Failed to read file: {$filename}");
        }
        
        return $content;
    }

    /**
     * Write content to file
     */
    public function write(string $filename, string $content, bool $append = false): bool
    {
        $filepath = $this->resolvePath($filename);
        
        $this->ensureDirectory(dirname($filepath));
        
        if (!$this->isAllowedExtension($filepath)) {
            throw new InvalidArgumentException("File extension not allowed: {$filename}");
        }
        
        $flags = $append ? FILE_APPEND | LOCK_EX : LOCK_EX;
        $result = file_put_contents($filepath, $content, $flags);
        
        if ($result === false) {
            throw new RuntimeException("Failed to write file: {$filename}");
        }
        
        return true;
    }

    /**
     * Append content to file
     */
    public function append(string $filename, string $content): bool
    {
        return $this->write($filename, $content, true);
    }

    /**
     * Delete file
     */
    public function delete(string $filename): bool
    {
        $filepath = $this->resolvePath($filename);
        
        if (!file_exists($filepath)) {
            throw new RuntimeException("File not found: {$filename}");
        }
        
        if (!unlink($filepath)) {
            throw new RuntimeException("Failed to delete file: {$filename}");
        }
        
        return true;
    }

    /**
     * Check if file exists
     */
    public function exists(string $filename): bool
    {
        return file_exists($this->resolvePath($filename));
    }

    /**
     * Get file info
     */
    public function info(string $filename): array
    {
        $filepath = $this->resolvePath($filename);
        
        if (!file_exists($filepath)) {
            throw new RuntimeException("File not found: {$filename}");
        }
        
        return [
            'name' => basename($filepath),
            'path' => $filepath,
            'size' => filesize($filepath),
            'modified' => filemtime($filepath),
            'created' => filectime($filepath),
            'extension' => pathinfo($filepath, PATHINFO_EXTENSION),
            'mime_type' => mime_content_type($filepath) ?: 'application/octet-stream',
            'is_readable' => is_readable($filepath),
            'is_writable' => is_writable($filepath),
        ];
    }

    /**
     * Download file with proper headers
     */
    public function download(string $filename, ?string $downloadName = null): void
    {
        $filepath = $this->resolvePath($filename);
        
        if (!file_exists($filepath)) {
            throw new RuntimeException("File not found: {$filename}");
        }
        
        if (!is_readable($filepath)) {
            throw new RuntimeException("File not readable: {$filename}");
        }
        
        $downloadName = $downloadName ?? basename($filepath);
        $fileSize = filesize($filepath);
        $mimeType = mime_content_type($filepath) ?: 'application/octet-stream';
        
        // Clear any previous output
        if (ob_get_level()) {
            ob_end_clean();
        }
        
        header('Content-Description: File Transfer');
        header('Content-Type: ' . $mimeType);
        header('Content-Disposition: attachment; filename="' . $downloadName . '"');
        header('Content-Length: ' . $fileSize);
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: 0');
        
        readfile($filepath);
        exit;
    }

    /**
     * List files in directory
     */
    public function list(string $directory = '', array $filters = []): array
    {
        $path = $this->resolvePath($directory);
        
        if (!is_dir($path)) {
            throw new RuntimeException("Directory not found: {$directory}");
        }
        
        $pattern = $filters['pattern'] ?? '*';
        $extension = $filters['extension'] ?? null;
        
        if ($extension) {
            $pattern = '*.' . $extension;
        }
        
        $files = glob($path . '/' . $pattern);
        
        if ($files === false) {
            return [];
        }
        
        $result = [];
        foreach ($files as $file) {
            if (is_file($file)) {
                $result[] = [
                    'name' => basename($file),
                    'path' => str_replace($this->basePath . '/', '', $file),
                    'size' => filesize($file),
                    'modified' => filemtime($file),
                ];
            }
        }
        
        return $result;
    }

    /**
     * Copy file
     */
    public function copy(string $source, string $destination): bool
    {
        $sourcePath = $this->resolvePath($source);
        $destPath = $this->resolvePath($destination);
        
        if (!file_exists($sourcePath)) {
            throw new RuntimeException("Source file not found: {$source}");
        }
        
        $this->ensureDirectory(dirname($destPath));
        
        if (!copy($sourcePath, $destPath)) {
            throw new RuntimeException("Failed to copy file from {$source} to {$destination}");
        }
        
        return true;
    }

    /**
     * Move/Rename file
     */
    public function move(string $source, string $destination): bool
    {
        $sourcePath = $this->resolvePath($source);
        $destPath = $this->resolvePath($destination);
        
        if (!file_exists($sourcePath)) {
            throw new RuntimeException("Source file not found: {$source}");
        }
        
        $this->ensureDirectory(dirname($destPath));
        
        if (!rename($sourcePath, $destPath)) {
            throw new RuntimeException("Failed to move file from {$source} to {$destination}");
        }
        
        return true;
    }

    /**
     * Parse file content based on schema
     * 
     * Schema format:
     * [
     *   'delimiter' => '|',           // For CSV-like files
     *   'fields' => ['name', 'email', 'age'],
     *   'skip_header' => true,
     *   'validate' => [               // Optional validation
     *     'email' => 'email',
     *     'age' => 'int'
     *   ]
     * ]
     * 
     * OR for structured text:
     * [
     *   'type' => 'structured',
     *   'sections' => [
     *     'header' => ['start' => '===', 'end' => '==='],
     *     'body' => ['start' => '---', 'end' => '---']
     *   ]
     * ]
     */
    public function parse(string $filename, array $schema): array
    {
        $content = $this->read($filename);
        
        if (isset($schema['type']) && $schema['type'] === 'structured') {
            return $this->parseStructured($content, $schema);
        }
        
        return $this->parseDelimited($content, $schema);
    }

    /**
     * Save data to file based on schema
     */
    public function saveParsed(string $filename, array $data, array $schema): bool
    {
        $content = $this->formatData($data, $schema);
        return $this->write($filename, $content);
    }

    /**
     * Upload file from $_FILES
     */
    public function upload(array $file, string $destination = '', ?string $newName = null): array
    {
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            throw new InvalidArgumentException("Invalid upload file");
        }
        
        if ($file['size'] > $this->maxFileSize) {
            throw new InvalidArgumentException("File size exceeds limit of {$this->maxFileSize} bytes");
        }
        
        $originalName = $file['name'];
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        
        if (!$this->isAllowedExtension($originalName)) {
            throw new InvalidArgumentException("File extension not allowed: {$extension}");
        }
        
        $filename = $newName ?? $this->sanitizeFilename($originalName);
        $filepath = $this->resolvePath($destination . '/' . $filename);
        
        $this->ensureDirectory(dirname($filepath));
        
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            throw new RuntimeException("Failed to upload file");
        }
        
        return [
            'path' => str_replace($this->basePath . '/', '', $filepath),
            'name' => $filename,
            'size' => filesize($filepath),
            'mime_type' => mime_content_type($filepath) ?: 'application/octet-stream',
        ];
    }

    // ==================== Private Helper Methods ====================

    private function resolvePath(string $filename): string
    {
        // Prevent directory traversal
        if ($this->secureMode) {
            $filename = str_replace(['..', './', '.\\'], '', $filename);
        }
        
        return $this->basePath . '/' . ltrim($filename, '/\\');
    }

    private function ensureDirectory(string $directory): void
    {
        if (!is_dir($directory)) {
            if (!mkdir($directory, 0755, true)) {
                throw new RuntimeException("Failed to create directory: {$directory}");
            }
        }
    }

    private function isAllowedExtension(string $filename): bool
    {
        if (empty($this->allowedExtensions)) {
            return true;
        }
        
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return in_array($extension, $this->allowedExtensions, true);
    }

    private function sanitizeFilename(string $filename): string
    {
        // Remove potentially dangerous characters
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
        
        // Prevent multiple extensions
        $filename = preg_replace('/\.(?![^.]*$)/', '_', $filename);
        
        return $filename;
    }

    private function parseDelimited(string $content, array $schema): array
    {
        $lines = explode("\n", $content);
        $result = [];
        
        $delimiter = $schema['delimiter'] ?? ',';
        $fields = $schema['fields'] ?? [];
        $skipHeader = $schema['skip_header'] ?? false;
        $validation = $schema['validate'] ?? [];
        
        $startIndex = $skipHeader ? 1 : 0;
        
        for ($i = $startIndex; $i < count($lines); $i++) {
            $line = trim($lines[$i]);
            
            if (empty($line)) {
                continue;
            }
            
            $values = str_getcsv($line, $delimiter);
            
            if (empty($fields)) {
                $result[] = $values;
            } else {
                $row = [];
                foreach ($fields as $index => $field) {
                    $value = $values[$index] ?? null;
                    
                    // Apply validation if specified
                    if (isset($validation[$field])) {
                        $value = $this->validateValue($value, $validation[$field]);
                    }
                    
                    $row[$field] = $value;
                }
                $result[] = $row;
            }
        }
        
        return $result;
    }

    private function parseStructured(string $content, array $schema): array
    {
        $result = [];
        $sections = $schema['sections'] ?? [];
        
        foreach ($sections as $sectionName => $sectionConfig) {
            $startMarker = $sectionConfig['start'] ?? '';
            $endMarker = $sectionConfig['end'] ?? '';
            $extract = $sectionConfig['extract'] ?? 'content'; // 'content', 'lines', 'json'
            
            $pattern = '/' . preg_quote($startMarker, '/') . '(.*?)' . preg_quote($endMarker, '/') . '/s';
            
            if (preg_match($pattern, $content, $matches)) {
                $sectionContent = trim($matches[1]);
                
                switch ($extract) {
                    case 'lines':
                        $result[$sectionName] = array_filter(
                            array_map('trim', explode("\n", $sectionContent))
                        );
                        break;
                    case 'json':
                        $result[$sectionName] = json_decode($sectionContent, true) ?? [];
                        break;
                    case 'key_value':
                        $result[$sectionName] = $this->parseKeyValue($sectionContent);
                        break;
                    default:
                        $result[$sectionName] = $sectionContent;
                }
            }
        }
        
        return $result;
    }

    private function parseKeyValue(string $content): array
    {
        $result = [];
        $lines = explode("\n", $content);
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line) || $line[0] === '#') {
                continue;
            }
            
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $result[trim($key)] = trim($value);
            } elseif (strpos($line, ':') !== false) {
                list($key, $value) = explode(':', $line, 2);
                $result[trim($key)] = trim($value);
            }
        }
        
        return $result;
    }

    private function validateValue($value, string $type)
    {
        switch ($type) {
            case 'int':
            case 'integer':
                return (int) $value;
            case 'float':
            case 'double':
                return (float) $value;
            case 'bool':
            case 'boolean':
                return in_array(strtolower($value), ['true', '1', 'yes', 'on'], true);
            case 'email':
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    throw new InvalidArgumentException("Invalid email format: {$value}");
                }
                return $value;
            case 'url':
                if (!filter_var($value, FILTER_VALIDATE_URL)) {
                    throw new InvalidArgumentException("Invalid URL format: {$value}");
                }
                return $value;
            case 'date':
                $timestamp = strtotime($value);
                if ($timestamp === false) {
                    throw new InvalidArgumentException("Invalid date format: {$value}");
                }
                return date('Y-m-d', $timestamp);
            case 'trim':
                return trim($value);
            case 'uppercase':
                return strtoupper($value);
            case 'lowercase':
                return strtolower($value);
            default:
                return $value;
        }
    }

    private function formatData(array $data, array $schema): string
    {
        if (isset($schema['type']) && $schema['type'] === 'structured') {
            return $this->formatStructured($data, $schema);
        }
        
        return $this->formatDelimited($data, $schema);
    }

    private function formatDelimited(array $data, array $schema): string
    {
        $delimiter = $schema['delimiter'] ?? ',';
        $fields = $schema['fields'] ?? [];
        $includeHeader = $schema['include_header'] ?? false;
        
        $lines = [];
        
        if ($includeHeader && !empty($fields)) {
            $lines[] = implode($delimiter, $fields);
        }
        
        foreach ($data as $row) {
            if (empty($fields)) {
                $lines[] = implode($delimiter, $row);
            } else {
                $values = [];
                foreach ($fields as $field) {
                    $values[] = $row[$field] ?? '';
                }
                $lines[] = implode($delimiter, $values);
            }
        }
        
        return implode("\n", $lines);
    }

    private function formatStructured(array $data, array $schema): string
    {
        $sections = $schema['sections'] ?? [];
        $parts = [];
        
        foreach ($sections as $sectionName => $sectionConfig) {
            if (!isset($data[$sectionName])) {
                continue;
            }
            
            $startMarker = $sectionConfig['start'] ?? '';
            $endMarker = $sectionConfig['end'] ?? '';
            $format = $sectionConfig['format'] ?? 'raw'; // 'raw', 'json', 'key_value'
            
            $sectionContent = $data[$sectionName];
            
            switch ($format) {
                case 'json':
                    $content = json_encode($sectionContent, JSON_PRETTY_PRINT);
                    break;
                case 'key_value':
                    $lines = [];
                    foreach ($sectionContent as $key => $value) {
                        $lines[] = "{$key}={$value}";
                    }
                    $content = implode("\n", $lines);
                    break;
                case 'lines':
                    $content = is_array($sectionContent) ? implode("\n", $sectionContent) : $sectionContent;
                    break;
                default:
                    $content = is_array($sectionContent) ? implode("\n", $sectionContent) : $sectionContent;
            }
            
            $parts[] = $startMarker . "\n" . $content . "\n" . $endMarker;
        }
        
        return implode("\n\n", $parts);
    }
}
