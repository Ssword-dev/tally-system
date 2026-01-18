<?php
namespace Core\Server\Templating;

trait TemplatingHelpers
{
    protected function renderViewInIsolation($view, $data = array()): string
    {
        ob_start();
        extract($data, EXTR_SKIP);
        include $view;
        $html = ob_get_clean();
        return $html;
    }
}