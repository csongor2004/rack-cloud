<?php
namespace App\Services;

use ZipArchive;

class ZipService
{
    public function createZipFromFiles($files, $storageDir)
    {
        $zip = new ZipArchive();
        $zipName = sys_get_temp_dir() . '/cloud_export_' . time() . '.zip';

        if ($zip->open($zipName, ZipArchive::CREATE) !== TRUE) {
            return false;
        }

        foreach ($files as $file) {
            $filePath = $storageDir . $file['stored_name'];
            if (file_exists($filePath)) {
                $zip->addFile($filePath, $file['original_name']);
            }
        }
        $zip->close();
        return $zipName;
    }
}