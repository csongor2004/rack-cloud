<?php
namespace App\Core;

class Uploader
{
    private $uploadDir;

    public function __construct($dir)
    {
        $this->uploadDir = $dir;
        // Létrehozzuk a mappát, ha nem létezik
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }
    }

    public function upload($file)
    {
        if ($file['error'] !== UPLOAD_ERR_OK)
            return false;

        // Biztonsági név: ne az eredeti nevet használjuk, hogy ne lehessen felülírni
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $safeName = bin2hex(random_bytes(10)) . "." . $extension;

        $target = $this->uploadDir . $safeName;

        if (move_uploaded_file($file['tmp_name'], $target)) {
            return $safeName;
        }
        return false;
    }
}