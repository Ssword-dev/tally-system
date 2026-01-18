<?php
namespace App;

class SimplePage
{
    private $attributes;
    private mixed $content;
    /**
     * Summary of __construct
     * @param array{
     *     attributes: array<string, mixed>,
     *     content: string|string[]
     * } $opts
     */
    public function __construct(array $opts)
    {
        $this->attributes = $opts['attributes'];
        $this->content = $opts['content'];
    }

    public function html()
    {
        $mainScriptDir = dirname($_SERVER['SCRIPT_FILENAME']);

        if (file_exists($mainScriptDir . '/_layout.php')) {
            $layoutClass = require($mainScriptDir . '/_layout.php');
            $layout = new $layoutClass($this->attributes, $this->content);
            $html = $layout->render();
            return $html;
        }


        return $this->content;
    }

    /**
     * Summary of page
     * @param array{
     *     attributes: array<string, mixed>,
     *     content: string|string[]
     * } $opts
     */
    public static function page(
        array $opts
    ) {
        return new static($opts);
    }
}