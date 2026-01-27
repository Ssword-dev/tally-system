<?php

class ComponentRenderFunctionStore
{
    private static array $renderFunctionCache = [];

    public static function hash(callable $fn): string
    {
        if (is_string($fn)) {
            return "static_function_construct_$fn";
        }

        $cls = $fn::class;
        return "non_native_construct_$cls";
    }

    public static function createRenderFunctionAsClosure(callable $fn): Closure
    {
        return self::$renderFunctionCache[self::hash($fn)] ??= self::_createRenderFunctionAsClosure($fn);
    }

    private static function _createRenderFunctionAsClosure(callable $fn): Closure
    {
        if ($fn instanceof Closure) {
            return $fn;
        }

        return Closure::fromCallable($fn);
    }
}

class ComponentContext
{
    public array $props;
    public Closure $render;

    public function __construct(callable $render, array $props = [])
    {
        $this->render = ComponentRenderFunctionStore::createRenderFunctionAsClosure($render);
        $this->props = $props;
    }
}

class TemplatingEngine
{
    /** @var callable */
    public $componentResolver;

    /** @var ComponentContext[] */
    public array $componentStack = [];

    /** @var array<string, string> */
    public array $componentFileCache = [];

    public function __construct()
    {
        $this->componentResolver = fn(string $name): string => __DIR__ . '/' . $name;
    }

    public function compileTemplate($code){
        
    }

    public function renderTemplateInContext(string $templateFile, array $variables)
    {
        extract($variables);
        ob_start();
        include($templateFile);
        return ob_get_clean();
    }

    public function resolveComponentFile(string $name): string
    {
        return ($this->componentResolver)($name);
    }

    public function comp(string $comp, array $props = [])
    {
        $this->componentStack[] = new ComponentContext($comp, $props);
    }

    public function endComp(string $comp)
    {
        $context = array_pop($this->componentStack);

        assert($context->render === ComponentRenderFunctionStore::createRenderFunctionAsClosure($comp), 'Mismatch of opening and closing tags.');

        return $context->render();
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <?= comp('myComp') ?>
    <?= comp('myButton') ?>
    <p>Hello world!</p>
    <?= endcomp('myButton') ?>
    <?= endcomp('myComp') ?>
    <?= 'endcomp' ?>
</body>

</html>