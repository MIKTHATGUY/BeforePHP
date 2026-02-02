<?php
declare(strict_types=1);

namespace NextPHP;

class Config
{
    private static array $config = [];

    public static function set(string $key, mixed $value): void
    {
        $keys = explode('.', $key);
        $config = &self::$config;

        foreach ($keys as $k) {
            if (!isset($config[$k]) || !is_array($config[$k])) {
                $config[$k] = [];
            }
            if ($k === end($keys)) {
                $config[$k] = $value;
            } else {
                $config = &$config[$k];
            }
        }
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $keys = explode('.', $key);
        $value = self::$config;

        foreach ($keys as $k) {
            if (!is_array($value) || !array_key_exists($k, $value)) {
                return $default;
            }
            $value = $value[$k];
        }

        return $value;
    }

    public static function all(): array
    {
        return self::$config;
    }
}
