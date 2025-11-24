<?php
// app/Core/App.php
class App {
    protected $controller = 'HomeController'; // Controller mặc định
    protected $action = 'index';              // Hàm mặc định
    protected $params = [];

    public function __construct() {
        $url = $this->getUrl();

        // 1. Xử lý Controller
        // Kiểm tra xem file controller có tồn tại không
        if (isset($url[0]) && file_exists('../app/Controllers/' . ucfirst($url[0]) . 'Controller.php')) {
            $this->controller = ucfirst($url[0]) . 'Controller';
            unset($url[0]);
        }
        
        // Require file controller
        require_once '../app/Controllers/' . $this->controller . '.php';
        $this->controller = new $this->controller;

        // 2. Xử lý Action (Hàm)
        if (isset($url[1])) {
            if (method_exists($this->controller, $url[1])) {
                $this->action = $url[1];
            }
            unset($url[1]);
        }

        // 3. Xử lý Params (Tham số)
        $this->params = $url ? array_values($url) : [];

        // 4. Gọi hàm
        call_user_func_array([$this->controller, $this->action], $this->params);
    }

    public function getUrl() {
        if (isset($_GET['url'])) {
            // Cắt URL, loại bỏ khoảng trắng và ký tự lạ
            return explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
        }
        return [];
    }
}