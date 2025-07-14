<?php

abstract class Controller {
    protected $view;
    protected $request;
    protected $response;
    
    public function __construct() {
        $this->view = new View();
        $this->request = $_REQUEST;
        $this->response = [];
    }
    
    protected function render($view, $data = []) {
        return $this->view->render($view, $data);
    }
    
    protected function redirect($url) {
        header("Location: $url");
        exit();
    }
    
    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }
    
    protected function get($key, $default = null) {
        return $_GET[$key] ?? $default;
    }
    
    protected function post($key, $default = null) {
        return $_POST[$key] ?? $default;
    }
    
    protected function input($key, $default = null) {
        return $_REQUEST[$key] ?? $default;
    }
    
    protected function validate($data, $rules) {
        $errors = [];
        
        foreach ($rules as $field => $rule) {
            $value = $data[$field] ?? null;
            
            if (strpos($rule, 'required') !== false && empty($value)) {
                $errors[$field] = "Field {$field} is required";
                continue;
            }
            
            if (!empty($value)) {
                if (strpos($rule, 'email') !== false && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field] = "Field {$field} must be a valid email";
                }
                
                if (strpos($rule, 'min:') !== false) {
                    preg_match('/min:(\d+)/', $rule, $matches);
                    $min = $matches[1];
                    if (strlen($value) < $min) {
                        $errors[$field] = "Field {$field} must be at least {$min} characters";
                    }
                }
                
                if (strpos($rule, 'max:') !== false) {
                    preg_match('/max:(\d+)/', $rule, $matches);
                    $max = $matches[1];
                    if (strlen($value) > $max) {
                        $errors[$field] = "Field {$field} must not exceed {$max} characters";
                    }
                }
            }
        }
        
        return $errors;
    }
    
    protected function setFlash($key, $message) {
        $_SESSION['flash'][$key] = $message;
    }
    
    protected function getFlash($key) {
        $message = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $message;
    }
    
    protected function hasFlash($key) {
        return isset($_SESSION['flash'][$key]);
    }
    
    protected function old($key, $default = '') {
        return $_SESSION['old'][$key] ?? $default;
    }
    
    protected function setOld($data) {
        $_SESSION['old'] = $data;
    }
    
    protected function clearOld() {
        unset($_SESSION['old']);
    }
    
    protected function isPost() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
    
    protected function isGet() {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }
    
    protected function isAjax() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
    
    protected function csrf() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    protected function verifyCsrf() {
        $token = $this->post('csrf_token');
        if (!$token || $token !== $_SESSION['csrf_token']) {
            $this->setFlash('error', 'CSRF token validation failed');
            $this->redirect($_SERVER['HTTP_REFERER'] ?? '/');
        }
    }
} 