<?php
require_once __DIR__ . '/ApiTestCase.php';
require_once __DIR__ . '/../config/jwt.php';

class AuthApiTest extends ApiTestCase
{
    public function testSuccessfulLogin()
    {
        $response = $this->simulateRequest('POST', '/auth/login.php', [
            'username' => 'superadmin',
            'password' => 'password',
        ]);

        $this->assertEquals(200, http_response_code());
        $this->assertArrayHasKey('data', $response);
        $this->assertArrayHasKey('token', $response['data']);
        $this->assertEquals('superadmin', $response['data']['user']['username']);
    }

    public function testFailedLogin()
    {
        $response = $this->simulateRequest('POST', '/auth/login.php', [
            'username' => 'superadmin',
            'password' => 'wrongpassword',
        ]);

        $this->assertEquals(401, http_response_code());
        $this->assertEquals('invalid_credentials', $response['error']);
    }

    public function testVerifyValidToken()
    {
        // First, log in to get a token
        $loginResponse = $this->simulateRequest('POST', '/auth/login.php', [
            'username' => 'testteacher',
            'password' => 'password',
        ]);
        $token = $loginResponse['data']['token'];

        // Now, verify the token
        $response = $this->simulateRequest('GET', '/auth/verify.php', [], $token);

        $this->assertEquals(200, http_response_code());
        $this->assertTrue($response['success']);
        $this->assertEquals('testteacher', $response['data']['username']);
    }

    public function testVerifyInvalidToken()
    {
        $response = $this->simulateRequest('GET', '/auth/verify.php', [], 'invalidtoken');

        $this->assertEquals(401, http_response_code());
        $this->assertFalse($response['success']);
    }

    public function testLogout()
    {
        // Log in to get a token
        $loginResponse = $this->simulateRequest('POST', '/auth/login.php', [
            'username' => 'teststudent',
            'password' => 'password',
        ]);
        $token = $loginResponse['data']['token'];

        // Logout
        $response = $this->simulateRequest('POST', '/auth/logout.php', [], $token);

        $this->assertEquals(200, http_response_code());
        $this->assertTrue($response['success']);

        // Verify the token is now invalid
        $verifyResponse = $this->simulateRequest('GET', '/auth/verify.php', [], $token);
        $this->assertEquals(401, http_response_code());
        $this->assertFalse($verifyResponse['success']);
    }
}
