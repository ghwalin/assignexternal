<?php

namespace mod_assignexternal\controller;

use PHPUnit\Framework\TestCase;
/**
 * Unit tests for class assign_control
 * @group mod_assignexternal
 */
class assign_controlTest extends TestCase
{
    private $context;
    private $coursemodule;
    private $course;

    public function test__construct()
    {
        $assigncontrol = new assign_control($this->context, $this->coursemodule, $this->course);
        $this->assertNotNull($assigncontrol->get_context());
        $this->assertNotNull($assigncontrol->get_coursemodule());
        $this->assertNotNull($assigncontrol->get_course());
    }

    public function testAdd_instance()
    {
        $formdata = new \stdClass();
        $formdata->course = $this->course->id;
        $formdata->name = 'unittest add';

        $this->assertTrue(true); // TODO PHPUnit
    }

    public function testUpdate_instance()
    {
        $this->assertTrue(true); // TODO PHPUnit
    }

    public function testDelete_instance()
    {
        $this->assertTrue(true); // TODO PHPUnit
    }
    public function testGrade_item_update()
    {
        $this->assertTrue(true); // TODO PHPUnit
    }

    public function testGet_useridlistid()
    {
        $this->assertTrue(true); // TODO PHPUnit
    }

    public function testGetter_Setter()
    {
        $this->assertTrue(true); // TODO PHPUnit
    }

    public function setUp(): void
    {
        $this->context = null; // TODO
        $this->coursemodule = null; // TODO
        $this->course = null; // TODO
    }
    public function tearDown(): void
    {
        $this->context = null;
        $this->coursemodule = null;
        $this->course = $this->getDataGenerator()->create_course();
        $this->coursemodule = $this->getDataGenerator()->create_module('assignexternal', array('course' => $this->course->id));
    }
}
