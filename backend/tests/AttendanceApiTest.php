<?php
require_once __DIR__ . '/ApiTestCase.php';

class AttendanceApiTest extends ApiTestCase
{
    public function testGetStudentHistoryAsStudent()
    {
        // Log in as the test student
        $loginResponse = $this->simulateRequest('POST', '/auth/login.php', [
            'username' => 'teststudent',
            'password' => 'password',
        ]);
        $token = $loginResponse['data']['token'];

        // Get attendance history
        $response = $this->simulateRequest('GET', '/attendance/get_student_history.php', [], $token);

        $this->assertEquals(200, http_response_code());
        $this->assertTrue($response['success']);
        $this->assertCount(3, $response['data']);
        $this->assertEquals('present', $response['data'][0]['status']);
    }

    public function testGetStudentHistoryUnauthenticated()
    {
        $response = $this->simulateRequest('GET', '/attendance/get_student_history.php');

        $this->assertEquals(401, http_response_code());
        $this->assertFalse($response['success']);
    }
}
