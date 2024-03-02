<?php
namespace mod_assignexternal\data;
use mod_assignexternal\data\override;

/**
 * Unit tests for data class override
 * @group mod_assignexternal
 */
class overridetest extends \basic_testcase
{
    private $override;

    protected function setUp(): void
    {
        $this->override = new override();
    }

    /**
     * @return void
     */
    public function testDefaultConstructor()
    {
        $this->assertNull($this->override->get_id());
        $this->assertNull($this->override->get_assignexternal());
        $this->assertNull($this->override->get_userid());
        $this->assertNull($this->override->get_allowsubmissionsfromdate());
        $this->assertNull($this->override->get_duedate());
        $this->assertNull($this->override->get_cutoffdate());
    }

    /**
     * @return void
     */
    public function testSettersAndGetters()
    {
        $this->override->set_id(1);
        $this->override->set_assignexternal(2);
        $this->override->set_userid(3);
        $this->override->set_allowsubmissionsfromdate(1613673600); // February 18, 2021 00:00:00
        $this->override->set_duedate(1613760000); // February 19, 2021 00:00:00
        $this->override->set_cutoffdate(1613846400); // February 20, 2021 00:00:00

        $this->assertEquals(1, $this->override->get_id());
        $this->assertEquals(2, $this->override->get_assignexternal());
        $this->assertEquals(3, $this->override->get_userid());
        $this->assertEquals(1613673600, $this->override->get_allowsubmissionsfromdate());
        $this->assertEquals(1613760000, $this->override->get_duedate());
        $this->assertEquals(1613846400, $this->override->get_cutoffdate());
    }

    /**
     * @return void
     */
    public function testToStdClass()
    {
        $this->override->set_id(1);
        $this->override->set_assignexternal(2);
        $this->override->set_userid(3);
        $this->override->set_allowsubmissionsfromdate(1613673600); // February 18, 2021 00:00:00
        $this->override->set_duedate(1613760000); // February 19, 2021 00:00:00
        $this->override->set_cutoffdate(1613846400); // February 20, 2021 00:00:00

        $expectedResult = (object) [
            'id' => 1,
            'assignexternal' => 2,
            'userid' => 3,
            'allowsubmissionsfromdate' => 1613673600,
            'duedate' => 1613760000,
            'cutoffdate' => 1613846400,
        ];

        $this->assertEquals($expectedResult, $this->override->to_stdClass());
    }

    public function testLoad_formdata()
    {
        $formdata = new \stdClass();
        $formdata->id = 9;
        $formdata->assignexternal = 9;
        $formdata->userid = 2;
        $formdata->allowsubmissionsfromdate = 1613673600;
        $formdata->duedate = 1613760000;
        $formdata->cutoffdate =  1613846400;
        $override = new override();
        $override->load_formdata($formdata);

        $expected = new override();
        $expected->set_id(9);
        $expected->set_assignexternal(9);
        $expected->set_userid(2);
        $expected->set_allowsubmissionsfromdate(1613673600); // February 18, 2021 00:00:00
        $expected->set_duedate(1613760000); // February 19, 2021 00:00:00
        $expected->set_cutoffdate(1613846400); // February 20, 2021 00:00:00

        $this->assertEquals($expected, $override);
    }

    public function testLoad_db()
    {
        $this->assertTrue(true); // TODO PHPUnit
    }

}