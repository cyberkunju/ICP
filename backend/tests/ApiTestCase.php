<?php
use PHPUnit\Framework\TestCase;

// A base class for API tests to handle common setup/teardown
class ApiTestCase extends TestCase
{
    protected $db;

    protected function setUp(): void
    {
        // Access the in-memory database created in bootstrap.php
        $this->db = $GLOBALS['test_db'];
    }

    // Helper to simulate an API request
    protected function simulateRequest(string $method, string $uri, array $body = [], string $token = null)
    {
        // Mock server variables
        $_SERVER['REQUEST_METHOD'] = $method;
        $_SERVER['REQUEST_URI'] = $uri;
        if ($token) {
            $_SERVER['HTTP_AUTHORIZATION'] = 'Bearer ' . $token;
        }

        // Mock input stream for POST requests
        if ($method === 'POST') {
            $input = json_encode($body);
            $stream = fopen('php://memory', 'r+');
            fwrite($stream, $input);
            rewind($stream);
            global $mock_input_stream;
            $mock_input_stream = $stream;
        }

        // Use output buffering to capture the API response
        ob_start();

        $path = parse_url($uri, PHP_URL_PATH);
        $file = __DIR__ . '/../api' . $path;

        if (file_exists($file)) {
            $db = $this->db;
            include $file;
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Not Found']);
        }

        $response = ob_get_clean();

        return json_decode($response, true);
    }
}
