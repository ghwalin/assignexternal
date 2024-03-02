<?php

namespace mod_assignexternal\data;

use PHPUnit\Framework\TestCase;

/**
 * Unit tests for data class grade
 * @group mod_assignexternal
 */
class grade_test extends TestCase
{
    private $grade;

    protected function setUp(): void
    {
        $this->grade = new grade();
    }

    public function test__construct()
    {
        global $USER;
        $this->assertNull($this->grade->get_id());
        $this->assertNull($this->grade->get_assignmentexternal());
        $this->assertNull($this->grade->get_userid());
        $this->assertEquals($this->grade->get_grader(), $USER->id);
        $this->assertEquals('',$this->grade->get_externallink());
        $this->assertEquals('',$this->grade->get_externalfeedback());
        $this->assertSame(0.0, $this->grade->get_externalgrade());
        $this->assertEquals('',$this->grade->get_manualfeedback());
        $this->assertSame(0.0, $this->grade->get_manualgrade());
    }

    /**
     * @return void
     */
    public function testSettersAndGetters()
    {
        $this->grade->set_id(5);
        $this->grade->set_assignmentexternal('lu11-a11-unit');
        $this->grade->set_userid(3);
        $this->grade->set_grader(5);
        $this->grade->set_externallink('https://foo.bar');
        $this->grade->set_externalfeedback('external feedback');
        $this->grade->set_externalgrade(54.32);
        $this->grade->set_manualfeedback('manual feedback');
        $this->grade->set_manualgrade(12.87);

        $this->assertEquals(5, $this->grade->get_id());
        $this->assertEquals('lu11-a11-unit', $this->grade->get_assignmentexternal());
        $this->assertEquals(3, $this->grade->get_userid());
        $this->assertEquals(5, $this->grade->get_grader());
        $this->assertEquals('https://foo.bar', $this->grade->get_externallink());
        $this->assertEquals('external feedback', $this->grade->get_externalfeedback());
        $this->assertEquals(54.32, $this->grade->get_externalgrade());
        $this->assertEquals('manual feedback', $this->grade->get_manualfeedback());
        $this->assertEquals(12.87, $this->grade->get_manualgrade());
    }

    public function testLoad_formdata()
    {
        $this->assertTrue(true); // TODO PHPUnit
    }

    public function testLoad_data()
    {
        $this->assertTrue(true); // TODO PHPUnit
    }

    public function testLoad_db()
    {
        $this->assertTrue(true); // TODO PHPUnit
    }



    public function testTo_stdClass()
    {
        $this->assertTrue(true); // TODO PHPUnit
    }

    public function testTotal_grade()
    {
        $this->grade->set_externalgrade(75.25);
        $this->grade->set_manualgrade(5);
        $this->assertEquals(80.25, $this->grade->total_grade());
    }
}
