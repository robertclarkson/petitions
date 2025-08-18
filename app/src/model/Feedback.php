<?php
namespace {

use SilverStripe\Assets\Image;
use SilverStripe\ORM\DataObject;

    class Feedback extends DataObject 
    {

        private static $db = [
            'Rating' => 'Int',
            'Good' => 'Text',
            'Bad' => 'Text',
           
        ];

        private static $has_one = [
           "Submission" => Submission::class
        ];

        private static $summary_fields = [
            'Created.Nice' => 'Date',
            'Submission.Name' => 'Name',
            'Rating' => 'Rating',
            'Good' => 'Good',
            'Bad' => 'Bad'
        ];

        private static $default_sort = 'Created DESC';


        public function canCreate($member = NULL, $context = []) {
            return false;
        }

        public function canEdit($member = NULL) {
            return false;
        }

        public function canView($member = NULL) {
            return true;
        }

        public function canDelete($member = NULL) {
            return true;
        }
    }
}