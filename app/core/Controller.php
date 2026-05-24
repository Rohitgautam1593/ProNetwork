<?php
/**
 * Base Controller
 * Loads the models and views
 */
class Controller {
    /**
     * Model paths searched in module-first order (APP_MODULE set by App router).
     */
    private function modelPathsForClass($className) {
        $dirs = [
            'admin' => '../admin/backend/models/',
            'company' => '../company/backend/models/',
            'user' => '../user/backend/models/',
        ];
        $module = defined('APP_MODULE') ? APP_MODULE : 'user';
        $orderMap = [
            'admin' => ['admin', 'company', 'user'],
            'company' => ['company', 'admin', 'user'],
            'user' => ['user', 'company', 'admin'],
        ];
        $order = $orderMap[$module] ?? $orderMap['user'];
        $paths = [];
        foreach ($order as $key) {
            $paths[] = $dirs[$key] . $className . '.php';
        }
        return $paths;
    }

    // Load model
    public function model($model) {
        // Extract class name (e.g., 'admin/Admin' -> 'Admin')
        $parts = explode('/', $model);
        $className = end($parts);

        foreach ($this->modelPathsForClass($className) as $modelPath) {
            if (file_exists($modelPath)) {
                require_once $modelPath;
                return new $className();
            }
        }

        http_response_code(500);
        exit('Model does not exist: ' . htmlspecialchars($model, ENT_QUOTES, 'UTF-8'));
    }

    // Load view
    public function view($view, $data = []) {
        $viewPaths = $this->resolveViewPaths($view);

        foreach ($viewPaths as $viewPath) {
            if (file_exists($viewPath)) {
                require_once $viewPath;
                return;
            }
        }

        http_response_code(404);
        exit('View does not exist: ' . htmlspecialchars($view, ENT_QUOTES, 'UTF-8'));
    }

    private function resolveViewPaths($view) {
        $paths = [];
        $parts = explode('/', $view);
        $module = $parts[0] ?? '';
        $name = $parts[1] ?? '';

        if ($module === 'admin' && $name !== '') {
            $paths[] = '../admin/frontend/views/' . $name . '.php';
        } elseif ($module === 'company' && $name !== '') {
            $paths[] = '../company/frontend/views/' . $name . '.php';
        } elseif (($module === 'users' || $module === 'auth' || $module === 'pages') && $name !== '') {
            $paths[] = '../user/frontend/views/' . $module . '/' . $name . '.php';
        }

        return $paths;
    }
}
