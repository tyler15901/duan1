<?php
class App {
    protected $controller = 'HomeController'; // Controller mặc định
    protected $action = 'index';              // Hàm mặc định
    protected $params = [];

    public function __construct() {
        $url = $this->parseUrl();

        // 1. Kiểm tra xem Controller có tồn tại không
        if (isset($url[0])) {
            if (file_exists('../app/Controllers/' . ucfirst($url[0]) . 'Controller.php')) {
                $this->controller = ucfirst($url[0]) . 'Controller';
                unset($url[0]);
            }
        }

        // Require Controller đó
        require_once '../app/Controllers/' . $this->controller . '.php';
        $this->controller = new $this->controller;

        // 2. Kiểm tra Action (Hàm) trong Controller
        if (isset($url[1])) {
            if (method_exists($this->controller, $url[1])) {
                $this->action = $url[1];
                unset($url[1]);
            }
        }

        // 3. Các tham số còn lại
        $this->params = $url ? array_values($url) : [];

        // Gọi hàm: controller->action(params)
        call_user_func_array([$this->controller, $this->action], $this->params);
    }

    public function parseUrl() {
        if (isset($_GET['url'])) {
            // Cắt URL thành mảng, lọc bỏ ký tự lạ
            return explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
        }
    }
}
?>