<?php
use PHPUnit\Framework\TestCase;

class GradeCalculatorTest extends TestCase
{
    public function testCalculateGrade()
    {
        // Test A+ grade
        $this->assertEquals(['grade_point' => 4.00, 'letter_grade' => 'A+'], calculateGrade(95));
        $this->assertEquals(['grade_point' => 4.00, 'letter_grade' => 'A+'], calculateGrade(90));

        // Test A grade
        $this->assertEquals(['grade_point' => 3.75, 'letter_grade' => 'A'], calculateGrade(87));
        $this->assertEquals(['grade_point' => 3.75, 'letter_grade' => 'A'], calculateGrade(85));

        // Test B grade
        $this->assertEquals(['grade_point' => 3.00, 'letter_grade' => 'B'], calculateGrade(72));
        $this->assertEquals(['grade_point' => 3.00, 'letter_grade' => 'B'], calculateGrade(70));

        // Test F grade
        $this->assertEquals(['grade_point' => 0.00, 'letter_grade' => 'F'], calculateGrade(39));
        $this->assertEquals(['grade_point' => 0.00, 'letter_grade' => 'F'], calculateGrade(0));
    }

    public function testCalculateGPA()
    {
        // Test with a set of marks
        $marks = [
            ['grade_point' => 4.00, 'credit_hours' => 3],
            ['grade_point' => 3.00, 'credit_hours' => 3],
            ['grade_point' => 2.00, 'credit_hours' => 3],
        ];
        $this->assertEquals(3.00, calculateGPA($marks));

        // Test with empty marks array
        $this->assertEquals(0.00, calculateGPA([]));
    }

    public function testCalculateCGPA()
    {
        // Test with a set of semester GPAs
        $semesterGPAs = [
            ['gpa' => 3.50, 'total_credits' => 18],
            ['gpa' => 3.80, 'total_credits' => 20],
        ];
        $this->assertEquals(3.66, calculateCGPA($semesterGPAs));

        // Test with empty semester GPAs array
        $this->assertEquals(0.00, calculateCGPA([]));
    }
}
