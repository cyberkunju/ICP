<?php
require_once __DIR__ . '/ApiTestCase.php';

class MarksApiTest extends ApiTestCase
{
    public function testGetMarksHistoryAsTeacher()
    {
        // Log in as the test teacher
        $loginResponse = $this->simulateRequest('POST', '/auth/login.php', [
            'username' => 'testteacher',
            'password' => 'password',
        ]);
        $token = $loginResponse['data']['token'];

        // Get marks history with required filters
        $uri = '/marks/get_marks_history.php?batch_year=2023&semester=1&subject_id=1&exam_type=internal_1';
        $response = $this->simulateRequest('GET', $uri, [], $token);

        $this->assertEquals(200, http_response_code());
        $this->assertTrue($response['success']);
        $this->assertCount(1, $response['data']['marks']);
        $this->assertEquals(40, $response['data']['marks'][0]['marks_obtained']);
    }

    public function testGetMarksHistoryUnauthenticated()
    {
        $uri = '/marks/get_marks_history.php?batch_year=2023&semester=1&subject_id=1&exam_type=internal_1';
        $response = $this->simulateRequest('GET', $uri);

        $this->assertEquals(401, http_response_code());
        $this->assertFalse($response['success']);
    }
}
