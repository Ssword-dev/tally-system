<?php
namespace Core\Server\Router;

use InvalidArgumentException;
use RuntimeException;

// internal route pattern caching singleton.
final class RoutePatternCache
{
    public static function has($route)
    {

    }

    public static function get($route)
    {
    }

    public static function set($route, $regexp)
    {

    }

    public static function saveCache()
    {

    }
}

final class RoutePattern
{
    public string $compiledPattern;

    /**
     * Stores the named parameter positions for easy extraction
     * @var array
     */
    private array $parameterNames = [];

    private function __construct($regexp, array $parameterNames = [])
    {
        $this->compiledPattern = $regexp;
        $this->parameterNames = $parameterNames;
    }

    /**
     * Check if the pattern matches a path and extract parameters
     *
     * @param string $path The path to match
     * @return array The matches with named parameters as keys
     * @throws RuntimeException
     */
    public function matches(string $path): array|false
    {
        $status = preg_match($this->compiledPattern, $path, $matches);

        if ($status === 0) {
            return false;
        }

        if ($status === false) {
            throw new RuntimeException(preg_last_error_msg());
        }

        if (empty($matches)) {
            return [];
        }

        // Remove the full match (index 0)
        array_shift($matches);

        // Build a result array with parameter names as keys
        $result = [];
        foreach ($this->parameterNames as $index => $name) {
            if (isset($matches[$index])) {
                $result[$name] = $matches[$index];
            }
        }

        return $result;
    }

    public function getCompiledRegexp()
    {
        return $this->compiledPattern;
    }

    public static string $REGEXP_DELIMITER = "~";

    /**
     * Tokenize a route pattern into tokens
     *
     * @param string $pattern The route pattern
     * @param array $compilerOptions Compiler options
     * @return Generator
     */
    public static function tokenize(string $pattern, $compilerOptions = [])
    {
        // literally manually compile a glob pattern to a regexp.
        $i = 0; // current position.
        $len = strlen($pattern);

        // these parses the following grammar:
        // wildcard := '*' || '**'
        // slug := '\[[a-zA-Z][A-Za-z0-9_]*\]'
        // path separator := '/'
        $grammarWithoutFallback = [
            [
                "test" => fn($char) => $char === self::$REGEXP_DELIMITER,
                "parse" => function () {
                    throw new InvalidArgumentException("Cannot use the character '" . self::$REGEXP_DELIMITER . "' in route patterns.");
                }
            ],
            // inside tokenize(), add this in the grammar
            [
                "test" => fn($char) => $char === '*',
                "parse" => function () use (&$i, $pattern, $len) {
                    // check for double asterisk
                    if ($i + 1 < $len && $pattern[$i + 1] === '*') {
                        $i += 2;
                        return [
                            "type" => "DOUBLE_WILDCARD",
                            "value" => "**"
                        ];
                    }
                    $i++;
                    return [
                        "type" => "WILDCARD",
                        "value" => "*"
                    ];
                }
            ],
            /**
             * TODO: Add a grammar rule that allows the escaping of '[' 
             * and ensures ']' HAS to be escaped before using it.
             */
            [
                "test" => fn($char) => $char === "[",
                "parse" => function () use (&$i, $pattern, $len) {
                    // parse the slug.
                    $j = $i + 1; // start after the '['
        
                    while ($j < $len && $pattern[$j] !== ']') {
                        $j++;
                    }

                    // now $j is at the closing ']'
                    if ($j >= $len) {
                        throw new InvalidArgumentException("Unclosed slug in glob pattern: " . $pattern);
                    }

                    $slug = substr($pattern, $i, $j - $i + 1); // include the closing ']'
        
                    // validate if the slug is well-formed.
                    if (!preg_match('/^\[[a-zA-Z][A-Za-z0-9_]*\]$/', $slug)) {
                        throw new InvalidArgumentException("Malformed slug in glob pattern: " . $slug);
                    }

                    // update position
                    $i = $j + 1;
                    return [
                        'type' => 'SLUG',
                        'value' => $slug,
                        'name' => substr($slug, 1, -1) // extract the name without '[/' and ']'
                    ];
                },
            ],
            [
                "test" => fn($char) => $char === '/' || $char === '\\',
                "parse" => function () use (&$i, $pattern, $compilerOptions) {
                    $char = $pattern[$i];
                    if ($char === '\\' && ($compilerOptions['strictUnixPaths'] ?? false)) {
                        throw new InvalidArgumentException("Backslashes are not allowed in strict Unix path mode: " . $pattern);
                    }

                    $i++;
                    return [
                        'type' => 'SEPARATOR',
                        'value' => $char
                    ];
                },
            ]
        ];

        $fallbackGrammarRule = [
            "test" => fn($char) => true,
            "parse" => function () use (&$i, $pattern, $len, $grammarWithoutFallback) {
                // parse literals until one of the grammar says the
                // current character is special.
                $literalBuffer = $pattern[$i]; // start with the current char in the buffer.
                $start = $i + 1; // skip the current char since it is already in the buffer.
                $j = $start;

                while ($j < $len) {
                    foreach ($grammarWithoutFallback as $rule) {
                        if ($rule['test']($pattern[$j])) {
                            // hit a special char, stop parsing literals.
                            break 2;
                        }
                    }

                    $literalBuffer .= $pattern[$j];
                    $j++;
                }

                // update position
                $i = $j;

                return [
                    'type' => 'LITERAL',
                    'value' => $literalBuffer
                ];
            }
        ];

        $grammar = [
            ...$grammarWithoutFallback,
            $fallbackGrammarRule
        ];

        while ($i < $len) {
            $char = $pattern[$i];

            foreach ($grammar as $rule) {
                if ($rule['test']($char)) {
                    $token = $rule['parse']();
                    yield $token;
                    break;
                }
            }
        }
    }

    public static function compile($pattern, $compilerOptions = [])
    {
        $token = self::tokenize($pattern);
        $regex = '';
        $parameterNames = [];
        $paramIndex = 0;

        foreach ($token as $t) {
            switch ($t['type']) {
                case 'LITERAL':
                    $regex .= preg_quote($t['value'], self::$REGEXP_DELIMITER);
                    break;
                case 'WILDCARD':
                    $regex .= '[^\/]*'; // match anything except slash
                    break;
                case 'DOUBLE_WILDCARD':
                    $regex .= '.*'; // match across slashes
                    break;
                case 'SEPARATOR':
                    $regex .= preg_quote($t['value'], self::$REGEXP_DELIMITER);
                    break;
                case 'SLUG':
                    $parameterNames[$paramIndex] = $t['name'];
                    $regex .= '([^\/]+)';
                    $paramIndex++;
                    break;
            }
        }

        $regDel = self::$REGEXP_DELIMITER;
        $compiledRegex = ($compilerOptions['strictPatterns'] ?? false) ? "$regDel^$regex\$$regDel" : "$regDel^$regex$regDel";

        return new self($compiledRegex, $parameterNames);
    }
}

register_shutdown_function(function () {
    RoutePatternCache::saveCache();
});