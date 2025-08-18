<?php
namespace {

use SilverStripe\Assets\Image;
use SilverStripe\ORM\DataObject;
use SilverStripe\View\Parsers\URLSegmentFilter;

    class AddOn extends DataObject 
    {

        private static $db = [
            'Title' => 'Varchar(255)',
            'URLSegment' => 'Varchar(255)',
            'Content' => 'HTMLText',
            'Cost' => 'Currency',
           
        ];

        private static $has_one = [
           "Event" => Event::class
        ];

        private static $summary_fields = [
            'Title' => 'Title',
            'Content' => 'Content',
            'Cost' => 'Cost'
        ];

        private static $default_sort = 'Created DESC';


        public function onBeforeWrite() {
            parent::onBeforeWrite();
            if (!$this->URLSegment) {

                $filter = URLSegmentFilter::create();
                $filteredTitle = $filter->filter($this->Title);

                // Fallback to generic page name if path is empty (= no valid, convertable characters)
                if (!$filteredTitle || $filteredTitle == '-' || $filteredTitle == '-1') {
                    $filteredTitle = "addon-$this->ID";
                }
                $this->URLSegment = $filteredTitle;
            }
        }

        public function getCMSFields() {
            $fields = parent::getCMSFields();
            $hashField = $fields->dataFieldByName('URLSegment')->performReadonlyTransformation();

            $fields->replaceField('URLSegment', $hashField);
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
            return true;
        }
    }
}