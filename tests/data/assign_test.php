<?php

use PHPUnit\Framework\TestCase;
use mod_assignexternal\data\assign;
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
        $this->assertNull($assign->getId());
        $this->assertNull($assign->getCourse());
        $this->assertNull($assign->getCoursemodule());
        $this->assertSame('', $assign->getName());
        $this->assertSame('', $assign->getIntro());
        $this->assertSame(FORMAT_HTML, $assign->getIntroformat());
        $this->assertFalse($assign->isAlwaysshowdescription());
        $this->assertSame('', $assign->getExternalname());
        $this->assertSame('', $assign->getExternallink());
        $this->assertFalse($assign->isAlwaysshowlink());
        $this->assertNull($assign->getAllowsubmissionsfromdate());
        $this->assertNull($assign->getDuedate());
        $this->assertNull($assign->getCutoffdate());
        $this->assertNull($assign->getTimemodified());
        $this->assertNull($assign->getExternalgrademax());
        $this->assertNull($assign->getManualgrademax());
        $this->assertNull($assign->getPassingpercentage());
        $this->assertFalse($assign->isHaspassinggrade());
        $this->assertFalse($assign->isHasgrade());
    }


}
