<?php

class View {
    private $viewPath = '../app/views/';
    private $layout = 'main';
    
    public function render($view, $data = []) {
        // Extract data to variables
        extract($data);
        
        // Start output buffering
        ob_start();
        
        // Include the view file
        $viewFile = $this->viewPath . $view . '.php';
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            throw new Exception("View file not found: {$viewFile}");
        }
        
        // Get the content
        $content = ob_get_clean();
        
        // Include layout if specified
        if ($this->layout) {
            $layoutFile = $this->viewPath . 'layouts/' . $this->layout . '.php';
            if (file_exists($layoutFile)) {
                include $layoutFile;
            } else {
                // If layout doesn't exist, just output content
                echo $content;
            }
        } else {
            echo $content;
        }
    }
    
    public function setLayout($layout) {
        $this->layout = $layout;
    }
    
    public function partial($view, $data = []) {
        extract($data);
        
        $viewFile = $this->viewPath . $view . '.php';
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            throw new Exception("Partial view file not found: {$viewFile}");
        }
    }
    
    public function component($name, $data = []) {
        $this->partial('components/' . $name, $data);
    }
    
    public function escape($value) {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
    
    public function asset($path) {
        return '/assets/' . ltrim($path, '/');
    }
    
    public function url($path = '') {
        return '/' . ltrim($path, '/');
    }
    
    public function csrf() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    public function old($key, $default = '') {
        return $_SESSION['old'][$key] ?? $default;
    }
    
    public function flash($key) {
        $message = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $message;
    }
    
    public function hasFlash($key) {
        return isset($_SESSION['flash'][$key]);
    }
} 