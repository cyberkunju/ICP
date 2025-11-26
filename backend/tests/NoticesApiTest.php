<?php
require_once __DIR__ . '/ApiTestCase.php';

class NoticesApiTest extends ApiTestCase
{
    public function testGetAllAsStudent()
    {
        // Log in as the test student
        $loginResponse = $this->simulateRequest('POST', '/auth/login.php', [
            'username' => 'teststudent',
            'password' => 'password',
        ]);
        $token = $loginResponse['data']['token'];

        // Get notices
        $response = $this->simulateRequest('GET', '/notices/get_all.php', [], $token);

        $this->assertEquals(200, http_response_code());
        $this->assertTrue($response['success']);

        // Student should see 2 notices: 'all' and 'students'
        $this->assertCount(2, $response['data']);

        $titles = array_column($response['data'], 'title');
        $this->assertContains('General Announcement', $titles);
        $this->assertContains('Student-specific Notice', $titles);
    }

    public function testGetAllUnauthenticated()
    {
        $response = $this->simulateRequest('GET', '/notices/get_all.php');

        $this->assertEquals(401, http_response_code());
        $this->assertFalse($response['success']);
    }
}
