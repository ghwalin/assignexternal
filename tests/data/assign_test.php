<?php

use mod_assignexternal\data\assign;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for data class assign
 * @group mod_assignexternal
 */
class assign_test extends TestCase
{
    public function testConstructor()
    {
        $assign = new assign();
        $this->assertInstanceOf(assign::class, $assign);
        $this->assertNull($assign->get_id());
        $this->assertNull($assign->get_course());
        $this->assertNull($assign->get_coursemodule());
        $this->assertSame('', $assign->get_name());
        $this->assertSame('', $assign->get_intro());
        $this->assertSame(FORMAT_HTML, $assign->get_introformat());
        $this->assertFalse($assign->is_alwaysshowdescription());
        $this->assertSame('', $assign->get_externalname());
        $this->assertSame('', $assign->get_externallink());
        $this->assertFalse($assign->is_alwaysshowlink());
        $this->assertNull($assign->get_allowsubmissionsfromdate());
        $this->assertNull($assign->get_duedate());
        $this->assertNull($assign->get_cutoffdate());
        $this->assertNull($assign->get_timemodified());
        $this->assertNull($assign->get_externalgrademax());
        $this->assertNull($assign->get_manualgrademax());
        $this->assertNull($assign->get_passingpercentage());
        $this->assertFalse($assign->is_haspassinggrade());
    }

    /**
     * @return void
     */
    public function testSettersAndGetters()
    {
        $this->assertTrue(true); // TODO PHPUnit
    }

    public function testLoad_formdata()
    {
        $this->assertTrue(true); // TODO PHPUnit
    }

    public function test_extracted()
    {

    }

    public function testLoad_db()
    {
        $this->assertTrue(true); // TODO PHPUnit
    }

    public function test_override()
    {
        $this->assertTrue(true); // TODO PHPUnit
    }

    public function testLoad_db_external()
    {
        $this->assertTrue(true); // TODO PHPUnit
    }

    public function testTo_stdClass()
    {
        $this->assertTrue(true); // TODO PHPUnit
    }


}
