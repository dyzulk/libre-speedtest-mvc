<?php

namespace App\Traits;

trait ViewRenderer
{
    /**
     * Render a view file inside the main layout.
     *
     * @param string $view
     * @param array $data
     * @return void
     */
    protected function render(string $view, array $data = []): void
    {
        $viewFile = __DIR__ . '/../Views/' . $view . '.php';
        if (!file_exists($viewFile)) {
            http_response_code(500);
            echo "View file {$view} does not exist.";
            exit();
        }

        // Extract data array to local variables
        extract($data);

        // Buffer the content of the view file
        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        // Include the master layout or output directly if no_layout is specified
        if (isset($no_layout) && $no_layout) {
            echo $content;
        } else {
            $layoutFile = __DIR__ . '/../Views/layout/main.php';
            if (file_exists($layoutFile)) {
                require $layoutFile;
            } else {
                echo $content;
            }
        }
    }

    /**
     * Return a JSON response.
     *
     * @param mixed $data
     * @param int $status
     * @return void
     */
    protected function json($data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit();
    }
}
