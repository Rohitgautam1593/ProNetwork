<?php
/**
 * App Core Class
 * Creates URL & loads core controller
 * URL FORMAT - /controller/method/params
 */
class App {
    protected $currentController = 'AuthController'; // Default controller
    protected $currentControllerPath = '../user/backend/controllers/AuthController.php';
    protected $currentMethod = 'index'; // Default method
    protected $params = [];

    public function __construct() {
        $url = $this->getUrl();

        // Check if URL is set and controller exists
        if (isset($url[0])) {
            $cleanSeg = str_replace('.php', '', $url[0]);
            $controllerName = ucwords($cleanSeg) . 'Controller';

            $controllerPaths = [
                '../admin/backend/controllers/' . $controllerName . '.php',
                '../company/backend/controllers/' . $controllerName . '.php',
                '../user/backend/controllers/' . $controllerName . '.php',
            ];

            foreach ($controllerPaths as $controllerPath) {
                if (!file_exists($controllerPath)) {
                    continue;
                }
                $this->currentController = $controllerName;
                $this->currentControllerPath = $controllerPath;
                unset($url[0]);
                break;
            }
        }

        $this->registerModuleFromPath($this->currentControllerPath);

        // Require the controller
        require_once $this->currentControllerPath;

        // Instantiate controller class
        $this->currentController = new $this->currentController();

        // Check for second part of url
        if (isset($url[1])) {
            $cleanMethod = str_replace('.php', '', $url[1]);
            // Check to see if method exists in controller
            if (method_exists($this->currentController, $cleanMethod)) {
                $this->currentMethod = $cleanMethod;
                unset($url[1]);
            }
        }

        // Get params
        $this->params = $url ? array_values($url) : [];

        // Call a callback with array of params
        call_user_func_array([$this->currentController, $this->currentMethod], $this->params);
    }

    /**
     * Tag which module owns the active controller so models resolve from that module first.
     */
    private function registerModuleFromPath($controllerPath) {
        if (defined('APP_MODULE')) {
            return;
        }
        $normalized = str_replace('\\', '/', $controllerPath);
        if (strpos($normalized, '/admin/backend/controllers/') !== false) {
            define('APP_MODULE', 'admin');
        } elseif (strpos($normalized, '/company/backend/controllers/') !== false) {
            define('APP_MODULE', 'company');
        } else {
            define('APP_MODULE', 'user');
        }
    }

    public function getUrl() {
        if (!isset($_GET['url'])) {
            return [];
        }
        $url = rtrim((string) $_GET['url'], '/');
        // Avoid deprecated FILTER_SANITIZE_URL; keep safe path segments only
        $url = preg_replace('/[^a-zA-Z0-9\/_\-\.]/', '', $url);
        $url = str_replace(['..', '//'], '', $url);
        if ($url === '') {
            return [];
        }
        return explode('/', $url);
    }
}
