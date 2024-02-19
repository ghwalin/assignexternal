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
        $this->assertNull($this->override->getId());
        $this->assertNull($this->override->getAssignexternal());
        $this->assertNull($this->override->getUserid());
        $this->assertNull($this->override->getAllowsubmissionsfromdate());
        $this->assertNull($this->override->getDuedate());
        $this->assertNull($this->override->getCutoffdate());
    }

    /**
     * @return void
     */
    public function testSettersAndGetters()
    {
        $this->override->setId(1);
        $this->override->setAssignexternal(2);
        $this->override->setUserid(3);
        $this->override->setAllowsubmissionsfromdate(1613673600); // February 18, 2021 00:00:00
        $this->override->setDuedate(1613760000); // February 19, 2021 00:00:00
        $this->override->setCutoffdate(1613846400); // February 20, 2021 00:00:00

        $this->assertEquals(1, $this->override->getId());
        $this->assertEquals(2, $this->override->getAssignexternal());
        $this->assertEquals(3, $this->override->getUserid());
        $this->assertEquals(1613673600, $this->override->getAllowsubmissionsfromdate());
        $this->assertEquals(1613760000, $this->override->getDuedate());
        $this->assertEquals(1613846400, $this->override->getCutoffdate());
    }

    /**
     * @return void
     */
    public function testToStdClass()
    {
        $this->override->setId(1);
        $this->override->setAssignexternal(2);
        $this->override->setUserid(3);
        $this->override->setAllowsubmissionsfromdate(1613673600); // February 18, 2021 00:00:00
        $this->override->setDuedate(1613760000); // February 19, 2021 00:00:00
        $this->override->setCutoffdate(1613846400); // February 20, 2021 00:00:00

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
}