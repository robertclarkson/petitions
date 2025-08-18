<?php


namespace {

use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\ORM\FieldType\DBDate;
use SilverStripe\ORM\GroupedList;

    class EventsListPage extends SiteTree
    {
        private static $db = [];

        private static $has_one = [];
    }

    class EventsListPageController extends PageController
    {

    	public function Events() {
    		return Event::get()->filter('RegistrationClose:GreaterThanOrEqual', date("Y-m-d"))->sort('RegistrationClose', 'ASC');
    	}
        
        public function GroupedEvents() {
            return GroupedList::create(Event::get()->filter('EventDateTime:GreaterThanOrEqual', date("Y-m-d"))->sort('EventDateTime', 'ASC'));
        }

        public function EventsCategories() {
            return EventCategory::get();
        }

    }
}
