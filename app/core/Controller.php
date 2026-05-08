<?php
/**
 * Base Controller
 * Loads the models and views
 */
class Controller {
    // Load model
    public function model($model) {
        // Require model file
        require_once '../app/models/' . $model . '.php';

        // Extract class name (e.g., 'admin/Admin' -> 'Admin')
        $parts = explode('/', $model);
        $className = end($parts);

        // Instantiate model
        return new $className();
    }

    // Load view
    public function view($view, $data = []) {
        // Check for view file
        if(file_exists('../app/views/' . $view . '.php')) {
            require_once '../app/views/' . $view . '.php';
        } else {
            // View does not exist
            die('View does not exist: ' . $view);
        }
    }
}
