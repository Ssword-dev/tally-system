<?php
namespace App;

use App\Traits\Singleton;
use Exception;
use mysqli;

/**
 * A database singleton,
 * 
 * @use Singleton<array{?string, ?string, ?string, ?string}, array{string, string, string, string}>
 */
final class Database
{
    use Singleton;

    private mysqli $connection;

    protected static function getInstanceParams(
        ?string $host = null,
        ?string $user = null,
        ?string $password = null,
        ?string $database = null
    ): array {
        return [
            $host ?? $_ENV['MYSQL_HOST'],
            $user ?? $_ENV['MYSQL_USER'],
            $password ?? $_ENV['MYSQL_PASSWORD'],
            $database ?? $_ENV['MYSQL_DATABASE_NAME']
        ];
    }

    protected static function initInstance(
        Database $instance,
        string $host,
        string $user,
        string $password,
        string $database
    ): void {

        $instance->connection = mysqli_connect(
            $host,
            $user,
            $password,
            $database
        );
    }

    /**
     * Generates mysqli type string for a given sequence of parameters.
     * @param array $params
     * @return string
     */
    private static function generateMySqliTypeString(array $params): string
    {
        $types = [];
        foreach ($params as $p) {
            if (is_int($p))
                $types[] = 'i'; // integer
            elseif (is_float($p) || is_double($p))
                $types[] = 'd'; // float / double
            elseif (is_string($p))
                $types[] = 's'; // string
            else
                $types[] = 'b'; // blob / unknown
        }

        return implode('', $types);
    }

    public function fetch(string $template, ...$parameters): ?array
    {
        $conn = $this->connection;
        $stmt = $conn->prepare(query: $template);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        if (count($parameters) > 0) {
            $types = self::generateMySqliTypeString($parameters);
            $stmt->bind_param($types, ...$parameters);
        }

        $stmt->execute();

        $result = $stmt->get_result();
        if ($result) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }

        return null;
    }

    public function execute(string $template, ...$parameters): int
    {
        $conn = $this->connection;

        $stmt = $conn->prepare($template);
        if (!$stmt) {
            throw new \Exception("Prepare failed: " . $conn->error);
        }

        $types = self::generateMySqliTypeString($parameters);

        $stmt->bind_param($types, ...$parameters);
        $stmt->execute();

        return $conn->insert_id;
    }
}