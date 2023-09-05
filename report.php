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

use mod_quiz\local\reports\report_base;

defined('MOODLE_INTERNAL') || die();

/**
 * Quiz report for exaquest. Based on the grading report.
 */
class quiz_exaqueststatistics_report extends report_base {
    const DEFAULT_PAGE_SIZE = 5;
    const DEFAULT_ORDER = 'random';

    /** @var string Positive integer regular expression. */
    const REGEX_POSITIVE_INT = '/^[1-9]\d*$/';

    /** @var array URL parameters for what is being displayed when grading. */
    protected $viewoptions = [];

    /** @var int the current group, 0 if none, or NO_GROUPS_ALLOWED. */
    protected $currentgroup;

    /** @var array from quiz_report_get_significant_questions. */
    protected $questions;

    /** @var stdClass the course settings. */
    protected $course;

    /** @var stdClass the course_module settings. */
    protected $cm;

    /** @var stdClass the quiz settings. */
    protected $quiz;

    /** @var context the quiz context. */
    protected $context;

    /** @var quiz_exaqueststatistics_renderer Renderer of Quiz Exaqueststatistics. */
    protected $renderer;

    /** @var string fragment of SQL code to restrict to the relevant users. */
    protected $userssql;

    /** @var array extra user fields. */
    protected $extrauserfields = [];

    public function display($quiz, $cm, $course) {
        // simply returning true / doing nothing in the display() function leads to an error
        // "Invalid state passed to moodle_page::set_state. We are in state 0 and state 3 was requested."
        $this->print_header_and_tabs($cm, $course, $quiz, 'grading');

        $whichattempts = optional_param('whichattempts', $quiz->grademethod, PARAM_INT);
        $quizstats = new \quiz_statistics\calculated($whichattempts);
        $quizinfo = $quizstats->get_formatted_quiz_info_data($course, $cm, $quiz);
        echo $this->output_quiz_info_table($quizinfo);


        return true;


    }


    /**
     * Return HTML for table of overall quiz statistics.
     *
     * @param array $quizinfo as returned by {@link get_formatted_quiz_info_data()}.
     * @return string the HTML.
     */
    protected function output_quiz_info_table($quizinfo) {

        $quizinfotable = new html_table();
        $quizinfotable->align = ['center', 'center'];
        $quizinfotable->width = '60%';
        $quizinfotable->attributes['class'] = 'generaltable titlesleft';
        $quizinfotable->data = [];

        foreach ($quizinfo as $heading => $value) {
            $quizinfotable->data[] = [$heading, $value];
        }

        return html_writer::table($quizinfotable);
    }

    /**
     * Initialise some parts of $PAGE and start output.
     *
     * @param stdClass $cm the course_module information.
     * @param stdClass $course the course settings.
     * @param stdClass $quiz the quiz settings.
     * @param string $reportmode the report name.
     */
    public function print_header_and_tabs($cm, $course, $quiz, $reportmode = 'overview') {
        global $PAGE;
        $this->renderer = $PAGE->get_renderer('quiz_exaqueststatistics');
        parent::print_header_and_tabs($cm, $course, $quiz, $reportmode);
    }

}
