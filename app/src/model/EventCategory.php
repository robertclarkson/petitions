<?php
namespace {

use SilverStripe\Assets\Image;
use SilverStripe\Forms\LiteralField;
use SilverStripe\ORM\DataObject;

    class EventCategory extends DataObject 
    {

        private static $db = [
            'Title' => 'Varchar(255)',

        ];

        private static $has_one = [

        ];

        private static $many_many = [
            'Events' => Event::class
        ];

        private static $summary_fields = [
            'Title',
            'ColourStyle'
        ];

        private static $default_sort = 'Title ASC';

        public function getCMSFields() {
            $fields = parent::getCMSFields();
            
            return $fields;
        }

        public function canCreate($member = NULL, $context = []) {
            return true;
        }

        public function canEdit($member = NULL) {
            return true;
        }

        public function canView($member = NULL) {
            return true;
        }

        public function canDelete($member = NULL) {
            return false;
        }
    }
}