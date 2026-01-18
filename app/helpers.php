<?php
use App\Auth;
use App\Router\FluentAPI as FluentRouterAPI;

function auth()
{
    return Auth::isLoggedIn() ? Auth::getInstance() : false;
}

function attributes($props)
{
    $attributeSegments = [];

    foreach ($props as $key => $value) {
        if ($value === true) {
            $attributeSegments[] = $key;
        } else if ($value === false) {
            // pass
        } else if (is_scalar($value)) {
            $attributeSegments[] = $key . "=" . (string) $value;
        } else {
            throw new UnexpectedValueException("Values passed into attributes of intrinsic elements must be scalar values only.");
        }
    }

    return implode(' ', $attributeSegments);
}

function minifyHTML($html)
{
    $document = new DOMDocument();
    $document->loadHTML($html);

    $document->preserveWhiteSpace = false;
    return $document->saveHTML();
}

function templateString(string $template, array $vars = [])
{
    $substitutions = array_combine(
        array_map(fn($key) => "{{ $key }}", array_keys($vars)),
        array_values($vars)
    );
    return minifyHTML(strtr($template, $substitutions));
}

function templateFile(string $templatePath, array $vars = []): string
{
    $template = file_get_contents($templatePath);
    return templateString($template, $vars);
}

function router()
{
    return FluentRouterAPI::getInstance();
}