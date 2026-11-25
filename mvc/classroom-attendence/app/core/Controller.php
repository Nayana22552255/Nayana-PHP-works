<?php 

class Controller 
{
    protected function view($view, $data=[])
    {
        extract($data);
        $file=__DIR__ . '/../views/' . $view .'.php';

        if(file_exists($file))
            {
                require $file;
            }else {
                die("View Not Found: ". $view);
            }
    }

    protected function redirect($url)
    {
        header("Location: ". $url);
        exit;
    }
}
?>