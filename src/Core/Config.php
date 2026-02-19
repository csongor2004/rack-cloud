<?php
namespace App\Core;

class Config
{
    private static $settings = [
        'db' => [
            'host' => 'localhost',
            'name' => 'rack_cloud',
            'user' => 'root',
            'pass' => ''
        ],
        'app' => [
            'storage_limit' => 52428800, // 50 MB bájtokban
            'allowed_extensions' => ['jpg', 'png', 'pdf', 'zip', 'docx']
        ]
    ];

    public static function get($key)
    {
        $keys = explode('.', $key);
        $result = self::$settings;
        foreach ($keys as $k) {
            if (!isset($result[$k]))
                return null;
            $result = $result[$k];
        }
        return $result;
    }
}