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
        if(isset($url[0])) {
            $cleanSeg = str_replace('.php', '', $url[0]);
            $controllerName = ucwords($cleanSeg) . 'Controller';

            $controllerPaths = [
                '../admin/backend/controllers/' . $controllerName . '.php',
                '../company/backend/controllers/' . $controllerName . '.php',
                '../user/backend/controllers/' . $controllerName . '.php'
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

        // Require the controller
        require_once $this->currentControllerPath;

        // Instantiate controller class
        $this->currentController = new $this->currentController;

        // Check for second part of url
        if(isset($url[1])) {
            $cleanMethod = str_replace('.php', '', $url[1]);
            // Check to see if method exists in controller
            if(method_exists($this->currentController, $cleanMethod)) {
                $this->currentMethod = $cleanMethod;
                unset($url[1]);
            }
        }

        // Get params
        $this->params = $url ? array_values($url) : [];

        // Call a callback with array of params
        call_user_func_array([$this->currentController, $this->currentMethod], $this->params);
    }

    public function getUrl() {
        if(isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);
            return $url;
        }
        return [];
    }
}
