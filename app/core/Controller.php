<?php
/**
 * Base Controller
 * Loads the models and views
 */
class Controller {
    // Load model
    public function model($model) {
        // Extract class name (e.g., 'admin/Admin' -> 'Admin')
        $parts = explode('/', $model);
        $className = end($parts);

        $modelPaths = [
            '../admin/backend/models/' . $className . '.php',
            '../company/backend/models/' . $className . '.php',
            '../user/backend/models/' . $className . '.php'
        ];

        foreach ($modelPaths as $modelPath) {
            if (file_exists($modelPath)) {
                require_once $modelPath;
                return new $className();
            }
        }

        die('Model does not exist: ' . $model);
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

        die('View does not exist: ' . $view);
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
        } elseif (($module === 'users' || $module === 'auth') && $name !== '') {
            $paths[] = '../user/frontend/views/' . $module . '/' . $name . '.php';
        }

        return $paths;
    }
}
