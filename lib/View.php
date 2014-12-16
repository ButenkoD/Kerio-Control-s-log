<?php

class View
{
    private $data = [];
    private $render;

    public function render($template, array $params = []) {
        try {
            $file = VIEWS_PATH . strtolower($template) . '.php';
            if (!file_exists($file)) {
                throw new Exception('Template ' . $template . ' not found!');
            }

            $this->render = $file;

            if (!empty($params)) {
                foreach ($params as $key => $param) {
                    $this->assign($key, $param);
                }
            }
        } catch (Exception $e) {
            echo $e->errorMessage();
        }
    }

    public function assign($variable, $value)
    {
        $this->data[$variable] = $value;
    }

    public function __destruct()
    {
        extract($this->data);
        include($this->render);
    }
}
?>