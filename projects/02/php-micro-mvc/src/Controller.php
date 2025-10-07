<?php

namespace App;

class Controller
{
    protected function render($view, $data = [])
    {
        extract($data, EXTR_SKIP);

        // Capture view output
        ob_start();
        include __DIR__ . "/Views/$view.php";
        $content = ob_get_clean();

        // Wrap with layout (head/body/footer patterned after Projects 00/01)
        include __DIR__ . "/Views/layout.php";
    }
}
