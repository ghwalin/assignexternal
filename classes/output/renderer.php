<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
namespace mod_assignexternal\output;

use plugin_renderer_base;

/**
 * Renders the HTML
 *
 * @package   mod_assignexternal
 * @copyright 2023 Marcel Suter <marcel@ghwalin.ch>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends plugin_renderer_base {

    /**
     * Defer to template for view_grading.
     * @param $page the page to render
     * @return string html for the page
     */
    public function render_view_grading($page): string {
        $data = $page->export_for_template($this);
        return parent::render_from_template('assignexternal/view_grading', $data);
    }

    /**
     * Defer to template for view_grader_navigation.
     * @param $page the page to render
     * @return string html for the page
     */
    public function render_view_grader_navigation($page): string {
        $data = $page->export_for_template($this);
        return parent::render_from_template('assignexternal/view_grader_navigation', $data);
    }

    /**
     * Defer to template for view_link.
     * @param $page the page to render
     * @return string html for the page
     */
    public function render_view_link($page): string {
        $data = $page->export_for_template($this);
        return parent::render_from_template('assignexternal/view_link', $data);
    }

    /**
     * Defer to template for view_summary.
     * @param $page the page to render
     * @return string html for the page
     */
    public function render_view_summary($page): string {
        $data = $page->export_for_template($this);
        return parent::render_from_template('assignexternal/view_summary', $data);
    }

    /**
     * Defer to template for view_student.
     * @param $page the page to render
     * @return string html for the page
     */
    public function render_view_student($page): string {
        $data = $page->export_for_template($this);
        return parent::render_from_template('assignexternal/view_student', $data);
    }
}
