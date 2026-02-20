<?php
namespace App\Services;

use ZipArchive;

class BackupService
{
    private $storageDir;

    public function __construct($storageDir)
    {
        $this->storageDir = $storageDir;
    }

    public function generateFullBackup()
    {
        $zip = new ZipArchive();
        $backupName = 'backup_' . date('Y-m-d_H-i-s') . '.zip';
        $backupPath = sys_get_temp_dir() . '/' . $backupName;

        if ($zip->open($backupPath, ZipArchive::CREATE) !== TRUE) {
            return false;
        }

        // Végigmegyünk a storage mappán
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->storageDir),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $name => $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($this->storageDir));
                $zip->addFile($filePath, $relativePath);
            }
        }

        $zip->close();
        return $backupPath;
    }
}