
<?php

class Controller
{
    // Load a view
    protected function view($view, $data = [])
    {
        $file = BASE_PATH . '/app/views/' . $view . '.php';

        if (!file_exists($file)) {
            throw new RuntimeException("View not found: " . $file);
        }

        extract($data);

        require $file;
    }

}
