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
 * Plugin administration pages are defined here.
 *
 * @package     mod_vivavoce
 * @category    admin
 * @copyright   2024 Saadh Zahid<saadhzahidwork@.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$hassiteconfig = has_capability('moodle/site:config', context_system::instance());

if ($hassiteconfig) {
  $settings = new admin_settingpage('mod_vivavoce_settings', new lang_string('pluginname', 'mod_vivavoce'));



  $options = array(
      'value1' => 'gpt-3.5-turbo-0613',
      'value2' => 'gpt-4-0613',
  );

  $settings->add(new admin_setting_configselect(
      'mod_vivavoce/dropdown_setting', // Setting name
      'Choose a GPT Model', // Setting display name
      'Choose a Model from the dropdown', // Setting description
      'value1', // Default value
      $options // Options array
  ));

  $ADMIN->add('localplugins', $settings);
}
