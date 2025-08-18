<?php
namespace {

use SilverStripe\Assets\Image;
use SilverStripe\ORM\DataObject;

    class SportIdentCategory extends DataObject 
    {

        private static $db = [
            'Title' => 'Varchar(255)',
            'InternalTitle' => 'Varchar(255)',           
        ];

        private static $has_one = [

        ];

        private static $many_many = [
            'Events' => Event::class
        ];

        private static $summary_fields = [
            'Title',
            'InternalTitle',
        ];

        private static $default_sort = 'Title ASC';


        public function onBeforeWrite() {
            parent::onBeforeWrite();
            if (!$this->InternalTitle) {
                $this->InternalTitle = $this->Title;
            }
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