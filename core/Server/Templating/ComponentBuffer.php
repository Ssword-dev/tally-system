<?php
namespace Core\Server\Templating;

final class ComponentBuffer
{
    private static array $stack = [];

    public static function start(string $name): void
    {
        self::$stack[] = $name;
        ob_start();
    }

    public static function end(string $name): string
    {
        if (empty(self::$stack)) {
            throw new RuntimeException('endcomp without comp');
        }

        $open = array_pop(self::$stack);

        if ($open !== $name) {
            throw new RuntimeException("component mismatch: $open vs $name");
        }

        $slot = ob_get_clean();

        // render component here
        return self::render($name, $slot);
    }

    private static function render(string $name, string $slot): string
    {
        return "<section class=\"comp-$name\">$slot</section>";
    }
}
