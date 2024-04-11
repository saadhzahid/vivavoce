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

namespace mod_vivavoce\event;

defined('MOODLE_INTERNAL') || die();

/**
 * The course_module_viewed event class.
 *
 * @package     mod_vivavoce
 * @category    event
 * @copyright   2024 Saadh Zahid<saadhzahidwork@.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_module_viewed extends \core\event\base {

    /**
     * Init method.
     */
    protected function init() {
        $this->data['objecttable'] = 'mdl_vivavoce'; // Replace 'vivavoce' with your database table name
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
    }

    /**
    //  * Returns localised description of event.
    //  *
    //  * @return string
    //  */
    public function get_description() {
        return 'The user viewed a viva voce assignment';
    }

    // /**
    //  * Returns legacy event log data.
    //  *
    //  * @return array
    //  */
    public function get_legacy_logdata() {
        return array($this->courseid, 'mdl_vivavoce', 'view', '', $this->objectid, $this->contextinstanceid);
    }

    // /**
    //  * Custom validation.
    //  */
    protected function get_custom_validation() {
        return true;
    }

    // /**
    //  * Returns relevant URL.
    //  *
    //  * @return \moodle_url
    //  */
    public function get_url() {
        return new \moodle_url('/mod/vivavoce/view.php', array('id' => $this->objectid));
    }

    // /**
    //  * Returns affected user ids.
    //  *
    //  * @return array
    //  */
    public function get_userids() {
        return array($this->userid);
    }
}
