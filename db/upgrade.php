<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Plugin upgrade steps are defined here.
 *
 * @package     block_course_checker_info
 * @category    upgrade
 * @copyright   2025 Simn Gisler, Fernfachhochschule Schweiz (FFHS) <simon.gisler@ffhs.ch>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Execute block_course_checker_info upgrade from the given old version.
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_block_course_checker_info_upgrade($oldversion) {
    global $DB;

    if ($oldversion < 2025061000) {
        // Register new capabilities.
        update_capabilities('block_course_checker_info');

        upgrade_block_savepoint(true, 2025061000, 'course_checker_info');
    }

    return true;
}
