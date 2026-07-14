<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;
use Throwable;

final class View
{
    public static function render(
        string $view,
        array $data = [],
        string $layout = 'main'
    ): void {
        $viewPath = BASE_PATH
            . DIRECTORY_SEPARATOR
            . 'app'
            . DIRECTORY_SEPARATOR
            . 'Views'
            . DIRECTORY_SEPARATOR
            . str_replace('/', DIRECTORY_SEPARATOR, $view)
            . '.php';

        $layoutPath = BASE_PATH
            . DIRECTORY_SEPARATOR
            . 'app'
            . DIRECTORY_SEPARATOR
            . 'Views'
            . DIRECTORY_SEPARATOR
            . 'layouts'
            . DIRECTORY_SEPARATOR
            . $layout
            . '.php';

        if (!is_file($viewPath)) {
            throw new RuntimeException(
                "La vista {$view} no existe."
            );
        }

        if (!is_file($layoutPath)) {
            throw new RuntimeException(
                "El layout {$layout} no existe."
            );
        }

        extract($data, EXTR_SKIP);

        ob_start();

        try {
            require $viewPath;

            $content = ob_get_clean();
        } catch (Throwable $exception) {
            ob_end_clean();

            throw $exception;
        }

        require $layoutPath;
    }
}