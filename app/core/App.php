<?php
/**
 * App Core Class
 * Creates URL & loads core controller
 * URL FORMAT - /controller/method/params
 */
class App {
    protected $currentController = 'AuthController'; // Default controller
    protected $currentMethod = 'index'; // Default method
    protected $params = [];
    protected $isAdminRoute = false;

    public function __construct() {
        $url = $this->getUrl();

        // Check if URL is set and controller exists
        if(isset($url[0])) {
            $controllerName = ucwords($url[0]) . 'Controller';
            
            // Check main controllers folder
            if(file_exists('../app/controllers/' . $controllerName . '.php')) {
                $this->currentController = $controllerName;
                unset($url[0]);
            } 
            // Check admin subfolder
            elseif(file_exists('../app/controllers/admin/' . $controllerName . '.php')) {
                $this->currentController = $controllerName;
                $this->isAdminRoute = true; // Flag if we need it
                unset($url[0]);
            }
        }

        // Require the controller
        $path = $this->isAdminRoute ? '../app/controllers/admin/' : '../app/controllers/';
        require_once $path . $this->currentController . '.php';

        // Instantiate controller class
        $this->currentController = new $this->currentController;

        // Check for second part of url
        if(isset($url[1])) {
            // Check to see if method exists in controller
            if(method_exists($this->currentController, $url[1])) {
                $this->currentMethod = $url[1];
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
