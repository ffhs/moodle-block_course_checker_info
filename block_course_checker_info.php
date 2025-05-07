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

/**
 * Course Checker block definition.
 *
 * Provides course-related validation and checks.
 *
 * @package   block_course_checker_info
 * @copyright 2025 Simon Gisler, Fernfachhochschule Schweiz (FFHS) <simon.gisler@ffhs.ch>
 * @copyright 2025 Stefan Dani, Fernfachhochschule Schweiz (FFHS) <stefan.dani@ffhs.ch>
 * @copyright based on work by 2019 Liip SA <elearning@liip.ch>
 * @copyright based on work by 2019 Adrian Perez, Fernfachhochschule Schweiz (FFHS) <adrian.perez@ffhs.ch>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_course_checker\db\database_manager as dbm;
use local_course_checker\task\queue_check_task;
use local_course_checker\db\model\checker;
use local_course_checker\db\model\check;

/**
 * Block Course Checker.
 *
 * This block performs various checks on course content.
 */
class block_course_checker_info extends block_base {
    /**
     * Initializes the block.
     *
     * @return void
     * @throws coding_exception
     */
    public function init(): void {
        $this->title = get_string('pluginname', 'block_course_checker_info');
        $this->content_type = BLOCK_TYPE_TEXT;
    }

    /**
     * Handles instance creation.
     *
     * @return bool True if the user has permission to add the instance, false otherwise.
     * @throws coding_exception
     */
    public function instance_create(): bool {
        return has_capability('block/course_checker_info:addinstance', $this->context);
    }

    /**
     * Generates the block content.
     *
     * @return stdClass|null The block content object.
     * @throws moodle_exception
     */
    public function get_content(): ?stdClass {
        $data = new stdClass();
        $data->action_url = new moodle_url('/local/course_checker/action.php'); // The URL of the action file.
        $course = $this->page->course;
        $data->courseid = $course->id; // The current course ID.
        $data->sesskey = sesskey(); // Moodle's CSRF protection token.
        $data->returnurl = new moodle_url('/course/view.php', ['id' => $course->id]);
        $data->reporturl = new moodle_url('/local/course_checker/index.php', ['courseid' => $course->id]);

        if ($checker = checker::get_record(['course_id' => $course->id, 'version_name' => 'latest'])) {
            $data->timestamp = userdate($checker->get('timestamp'), '%A, %x %R');
            $checks = check::get_records(['checker_id' => $checker->get('id')]);
            $data = (object) array_merge((array) $data, (array) $this->calculate_status_percentages($checks));
        }

        if ($runningadhoc = dbm::planned_adhoc_tasks('\\' . queue_check_task::class, $course->id)) {
            if (array_filter($runningadhoc, fn($item) => preg_match('/"checks":{\s*.*/', $item->customdata))) {
                $data->in_progress = true;
            }
        }

        // Render the Mustache template.
        $renderer = $this->page->get_renderer('block_course_checker_info');
        $this->content = new stdClass();
        $this->content->text = $renderer->render_from_template('block_course_checker_info/block', $data);
        return $this->content;
    }

    /**
     * Determines whether the block should be empty.
     *
     * @return bool True if the block has no content or user lacks permission.
     * @throws coding_exception
     */
    public function is_empty(): bool {
        if (!has_capability('local/course_checker:view', $this->context)) {
            return true;
        }

        $this->get_content();
        return(empty($this->content->text) && empty($this->content->footer));
    }

    /**
     * Handles block specialization (configurations).
     *
     * Called after init() to set a user-defined title.
     *
     * @return void
     */
    public function specialization() {

        // Load user defined title and make sure it's never empty.
        if (empty($this->config->title)) {
            $this->title = get_string('pluginname', 'block_course_checker_info');
        } else {
            $this->title = $this->config->title;
        }
    }

    /**
     * Indicates whether the block has a configuration page.
     *
     * @return bool Always true, indicating the block has configurable settings.
     */
    public function has_config(): bool {
        return true;
    }

    /**
     * Defines applicable formats for the block.
     *
     * @return array The applicable formats.
     */
    public function applicable_formats(): array {
        return ['course-view' => true];
    }

    /**
     * Calculates status percentages for checks.
     *
     * @param array $data The check data.
     * @return array The calculated status percentages.
     */
    private function calculate_status_percentages(array $data): array {
        $statuscounts = [];
        $totalentries = count($data);

        if ($totalentries === 0) {
            return []; // Avoid division by zero.
        }

        // Count occurrences of each status.
        foreach ($data as $item) {
            $status = $item->get('status') ?? 'error'; // Handle missing statuses.
            if (!isset($statuscounts[$status])) {
                $statuscounts[$status] = 0;
            }
            $statuscounts[$status]++;
        }

        // Calculate percentages.
        $statuspercentages = [];
        foreach ($statuscounts as $status => $count) {
            $statuspercentages[$status] = (int) round(($count / $totalentries) * 100);
        }

        return $statuspercentages;
    }

}
