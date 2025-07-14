<?php

namespace App\Services;

class FileUploadService
{
    private $uploadDir;
    private $allowedImageTypes;
    private $allowedDocumentTypes;
    private $maxFileSize;

    public function __construct()
    {
        $this->uploadDir = dirname(__DIR__, 2) . '/uploads/';
        $this->allowedImageTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $this->allowedDocumentTypes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'application/zip',
            'application/x-zip-compressed'
        ];
        $this->maxFileSize = 10 * 1024 * 1024; // 10MB
    }

    /**
     * Upload an image file
     */
    public function uploadImage($file, $type, $userId)
    {
        return $this->uploadFile($file, $type, $userId, 'image');
    }

    /**
     * Upload a document file
     */
    public function uploadDocument($file, $type, $userId)
    {
        return $this->uploadFile($file, $type, $userId, 'document');
    }

    /**
     * Generic file upload method
     */
    private function uploadFile($file, $type, $userId, $fileType)
    {
        // Validate file
        if (!$this->validateFile($file, $fileType)) {
            return false;
        }

        // Create directory if it doesn't exist
        $uploadPath = $this->uploadDir . $type . '/';
        if (!is_dir($uploadPath)) {
            if (!mkdir($uploadPath, 0755, true)) {
                error_log("Failed to create directory: " . $uploadPath);
                return false;
            }
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = $type . '_' . $userId . '_' . time() . '.' . $extension;
        $filepath = $uploadPath . $filename;

        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            error_log("Failed to move uploaded file: " . $file['tmp_name'] . " to " . $filepath);
            return false;
        }

        // Set proper permissions
        chmod($filepath, 0644);

        return $filename;
    }

    /**
     * Validate uploaded file
     */
    private function validateFile($file, $fileType)
    {
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            error_log("File upload error: " . $file['error']);
            return false;
        }

        // Check file size
        if ($file['size'] > $this->maxFileSize) {
            error_log("File too large: " . $file['size']);
            return false;
        }

        // Check file type
        $allowedTypes = ($fileType === 'image') ? $this->allowedImageTypes : $this->allowedDocumentTypes;
        if (!in_array($file['type'], $allowedTypes)) {
            error_log("Invalid file type: " . $file['type']);
            return false;
        }

        // Additional security checks
        if (!$this->isFileSafe($file)) {
            error_log("File security check failed");
            return false;
        }

        return true;
    }

    /**
     * Check if file is safe (no malicious content)
     */
    private function isFileSafe($file)
    {
        // Check file extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $dangerousExtensions = ['php', 'php3', 'php4', 'php5', 'phtml', 'exe', 'bat', 'cmd', 'sh'];
        
        if (in_array($extension, $dangerousExtensions)) {
            return false;
        }

        // Check MIME type consistency
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if ($mimeType !== $file['type']) {
            return false;
        }

        // Check for PHP tags in file content
        $content = file_get_contents($file['tmp_name']);
        if (strpos($content, '<?php') !== false || strpos($content, '<?=') !== false) {
            return false;
        }

        return true;
    }

    /**
     * Validate file access permissions
     */
    public function validateFileAccess($type, $filename, $userId, $userRole = null)
    {
        // Admin can access all files
        if ($userRole === 'admin') {
            return true;
        }

        // Check if file exists
        $filepath = $this->uploadDir . $type . '/' . $filename;
        if (!file_exists($filepath)) {
            return false;
        }

        // Extract user ID from filename
        $filenameParts = explode('_', $filename);
        if (count($filenameParts) < 2) {
            return false;
        }

        $fileUserId = $filenameParts[1];

        // User can only access their own files
        return $fileUserId === $userId;
    }

    /**
     * Download file securely
     */
    public function downloadFile($type, $filename)
    {
        $filepath = $this->uploadDir . $type . '/' . $filename;
        
        if (!file_exists($filepath)) {
            http_response_code(404);
            echo "File not found";
            exit;
        }

        // Get file info
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $filepath);
        finfo_close($finfo);

        $fileSize = filesize($filepath);
        $extension = pathinfo($filename, PATHINFO_EXTENSION);

        // Set headers for download
        header('Content-Type: ' . $mimeType);
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . $fileSize);
        header('Cache-Control: no-cache, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        // Output file content
        readfile($filepath);
        exit;
    }

    /**
     * Delete file
     */
    public function deleteFile($type, $filename)
    {
        $filepath = $this->uploadDir . $type . '/' . $filename;
        
        if (file_exists($filepath)) {
            return unlink($filepath);
        }
        
        return false;
    }

    /**
     * Get file info
     */
    public function getFileInfo($type, $filename)
    {
        $filepath = $this->uploadDir . $type . '/' . $filename;
        
        if (!file_exists($filepath)) {
            return null;
        }

        return [
            'name' => $filename,
            'size' => filesize($filepath),
            'type' => mime_content_type($filepath),
            'modified' => filemtime($filepath)
        ];
    }

    /**
     * Clean up old files
     */
    public function cleanupOldFiles($days = 30)
    {
        $cutoffTime = time() - ($days * 24 * 60 * 60);
        
        foreach (['laporan', 'revisi', 'profil'] as $type) {
            $typeDir = $this->uploadDir . $type . '/';
            
            if (!is_dir($typeDir)) {
                continue;
            }

            $files = glob($typeDir . '*');
            foreach ($files as $file) {
                if (is_file($file) && filemtime($file) < $cutoffTime) {
                    unlink($file);
                }
            }
        }
    }

    /**
     * Generate thumbnail for image
     */
    public function generateThumbnail($filename, $type = 'profil', $width = 150, $height = 150)
    {
        $sourcePath = $this->uploadDir . $type . '/' . $filename;
        
        if (!file_exists($sourcePath)) {
            return false;
        }

        // Get image info
        $imageInfo = getimagesize($sourcePath);
        if (!$imageInfo) {
            return false;
        }

        $sourceWidth = $imageInfo[0];
        $sourceHeight = $imageInfo[1];
        $mimeType = $imageInfo['mime'];

        // Create source image
        switch ($mimeType) {
            case 'image/jpeg':
                $sourceImage = imagecreatefromjpeg($sourcePath);
                break;
            case 'image/png':
                $sourceImage = imagecreatefrompng($sourcePath);
                break;
            case 'image/gif':
                $sourceImage = imagecreatefromgif($sourcePath);
                break;
            default:
                return false;
        }

        // Create thumbnail image
        $thumbnail = imagecreatetruecolor($width, $height);

        // Preserve transparency for PNG and GIF
        if ($mimeType === 'image/png' || $mimeType === 'image/gif') {
            imagealphablending($thumbnail, false);
            imagesavealpha($thumbnail, true);
            $transparent = imagecolorallocatealpha($thumbnail, 255, 255, 255, 127);
            imagefill($thumbnail, 0, 0, $transparent);
        }

        // Resize image
        imagecopyresampled($thumbnail, $sourceImage, 0, 0, 0, 0, $width, $height, $sourceWidth, $sourceHeight);

        // Save thumbnail
        $thumbnailPath = $this->uploadDir . $type . '/thumb_' . $filename;
        
        switch ($mimeType) {
            case 'image/jpeg':
                imagejpeg($thumbnail, $thumbnailPath, 90);
                break;
            case 'image/png':
                imagepng($thumbnail, $thumbnailPath, 9);
                break;
            case 'image/gif':
                imagegif($thumbnail, $thumbnailPath);
                break;
        }

        // Clean up
        imagedestroy($sourceImage);
        imagedestroy($thumbnail);

        return 'thumb_' . $filename;
    }

    /**
     * Get upload directory path
     */
    public function getUploadDir()
    {
        return $this->uploadDir;
    }

    /**
     * Get allowed file types
     */
    public function getAllowedTypes($fileType = 'document')
    {
        return ($fileType === 'image') ? $this->allowedImageTypes : $this->allowedDocumentTypes;
    }

    /**
     * Get maximum file size
     */
    public function getMaxFileSize()
    {
        return $this->maxFileSize;
    }
} 