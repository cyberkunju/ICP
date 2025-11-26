<?php
require_once __DIR__ . '/ApiTestCase.php';

class StudyMaterialsApiTest extends ApiTestCase
{
    public function testGetByDepartmentAsStudent()
    {
        // Log in as the test student
        $loginResponse = $this->simulateRequest('POST', '/auth/login.php', [
            'username' => 'teststudent',
            'password' => 'password',
        ]);
        $token = $loginResponse['data']['token'];

        // Get study materials
        $uri = '/materials/get_by_department.php?department=Computer%20Science';
        $response = $this->simulateRequest('GET', $uri, [], $token);

        $this->assertEquals(200, http_response_code());
        $this->assertTrue($response['success']);
        $this->assertCount(1, $response['data']['materials']);
        $this->assertEquals('cs101_notes.pdf', $response['data']['materials'][0]['file_name']);
    }

    public function testGetByDepartmentUnauthenticated()
    {
        $uri = '/materials/get_by_department.php?department=Computer%20Science';
        $response = $this->simulateRequest('GET', $uri);

        $this->assertEquals(401, http_response_code());
        $this->assertFalse($response['success']);
    }
}
