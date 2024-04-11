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
 * The main mod_vivavoce configuration form.
 *
 * @package     mod_vivavoce
 * @copyright   2024 Saadh Zahid<saadhzahidwork@.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');

/**
 * Module instance settings form.
 *
 * @package     mod_vivavoce
 * @copyright   2024 Saadh Zahid<saadhzahidwork@.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_vivavoce_mod_form extends moodleform_mod {
    /**
     * Defines forms elements
     */

    public function definition() {
        global $CFG, $DB, $PAGE;

        $mform = $this->_form;



        // Adding the "general" fieldset, where all the common settings are shown.
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Adding the standard "name" field.
        $mform->addElement('text', 'name', get_string('vivavocename', 'mod_vivavoce'), array('size' => '64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('name', 'vivavocename', 'mod_vivavoce');

        // Adding the standard "intro" and "introformat" fields.
        if ($CFG->branch >= 29) {
            $this->standard_intro_elements();
        } else {
            $this->add_intro_editor();
        }

        // Goals and Objectives
        $mform->addElement('textarea', 'goals_objectives', 'Goals and Objectives:');
        $mform->setType('goals_objectives', PARAM_TEXT);

        // Deliverables
        $mform->addElement('textarea', 'deliverables', 'Deliverables:', array('rows' => 10, 'cols' => 50));
        $mform->setType('deliverables', PARAM_TEXT);

        // Constraints
        $mform->addElement('textarea', 'constraints', 'Constraints:', array('rows' => 9, 'cols' => 50));
        $mform->setType('constraints', PARAM_TEXT);

        // Other Info
        $mform->addElement('textarea', 'other_info', 'Other Info:', array('rows' => 8, 'cols' => 50));
        $mform->setType('other_info', PARAM_TEXT);



    // Assignment availability (due date)
        $mform->addElement('date_time_selector', 'datedue', 'Due Date');

        // Set default due date to the current time
        $mform->setDefault('datedue', time());

        // Add standard grading elements.
        $this->standard_grading_coursemodule_elements();


        // add generative or normal
        // Add radio buttons for choosing between set and generative questions.
        $mform->addElement('header', 'questiontypehdr', 'Choose Question Type');

        // Create a group for radio buttons.
        $group = array();
        $group[] = $mform->createElement('radio', 'questiontype', '', 'Set Questions Manually', 'manual', array('onclick' => 'toggleTextboxes(this);'));
        $group[] = $mform->createElement('radio', 'questiontype', '', 'Generative Questions', 'generative', array('onclick' => 'toggleTextboxes(this); generatequestions();'));
        $mform->addGroup($group, 'radiogroup', '', array(' '), false);
        $mform->addRule('radiogroup', null, 'required', null, 'client');



        // Add text box for manual questions (initially disabled)
        $mform->addElement('textarea', 'manualquestions', 'Enter a Question', array('rows' => 2, 'cols' => 30, 'disabled' => 'disabled'));
        $mform->setType('manualquestions', PARAM_TEXT);

        // Add a hidden text box to store the concatenated questions
        $mform->addElement('hidden', 'allquestions', '');

        // Add a button to add questions
        $mform->addElement('button', 'addquestion', 'Add', array('onclick' => 'addQuestionstobox();', 'disabled' => 'disabled'));
        $PAGE->requires->js('/mod/vivavoce/js/mod_formscript.js');

    
        // Add a readonly text box to display all questions
        $mform->addElement('textarea', 'displayquestions', 'Questions', array('rows' => 5, 'cols' => 50, 'readonly' => 'readonly', 'onclick' => 'makeTextareaEditable();'));
        // Add the button element
        $mform->addElement('button', 'toggleedit', 'Edit', array('onclick' => 'toggleTextareaEditable(this);', 'disabled' => 'disabled'));

        $mform->setType('displayquestions', PARAM_TEXT);
        $mform->setDefault('displayquestions', '');



        // Add standard elements.
        $this->standard_coursemodule_elements();

        // Add standard buttons.
        $this->add_action_buttons();
    }

    /**
     * Saves a new instance of the mod_vivavoce into the database.
     *
     * @param stdClass $data An object containing all the necessary data.
     * @return int The id of the newly inserted record.
     */
    public function save_instance($data) {
        global $DB;

        
        // Prepare data to be saved in the database
        $record = new stdClass();
        $record->name = $data->name;
        $record->intro = $data->intro;
        $record->goals_objectives = $data->goals_objectives;
        $record->deliverables = $data->deliverables;
        $record->constraints = $data->constraints;
        $record->other_info = $data->other_info;
        $record->datedue = $data->datedue;
        $record->questiontype = $data->questiontype;
        $record->manualquestions = $data->manualquestions;
        $record->displayquestions = $data->displayquestions;
        $record->allquestions = $data->allquestions;



        // Insert the record into the database
        $inserted_id = $DB->insert_record('vivavoce', $record);
        
        $PAGE->requires->js_init_call('console.log', array("Form submitted. ID: $inserted_id"));

        // Return the ID of the newly inserted record
        return $inserted_id;
    }
    

    /**
     * Updates an instance of the mod_vivavoce in the database.
     *
     * @param stdClass $data An object containing all the necessary data.
     * @return bool True if successful, false otherwise.
     */
    public function update_instance($data) {
        global $DB;
    
        // Prepare data to be updated in the database
        $record = new stdClass();
        $record->id = $data->instance; // Assuming 'instance' holds the ID of the record to be updated
        $record->name = $data->name;
        $record->intro = $data->intro;
        $record->goals_objectives = $data->goals_objectives;
        $record->deliverables = $data->deliverables;
        $record->constraints = $data->constraints;
        $record->other_info = $data->other_info;
        $record->datedue = $data->datedue;
        $record->questiontype = $data->questiontype;
        $record->manualquestions = $data->manualquestions;
        $record->displayquestions = $data->displayquestions;
        $record->allquestions = $data->allquestions;
    
        // Update the record in the database
        return $DB->update_record('vivavoce', $record);

        if ($result) {
            // Log the form update to browser console
            $PAGE->requires->js_init_call('console.log', array("Form updated. ID: $record->id"));
        }
        
    }

    /**
     * Removes an instance of the mod_vivavoce from the database.
     *
     * @param int $id Id of the module instance.
     * @return bool True if successful, false on failure.
     */
    public function delete_instance($id) {
        global $DB;

        return $DB->delete_records('mdl_vivavoce', array('id' => $id));
    }
}
