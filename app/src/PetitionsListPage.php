<?php


namespace {

use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\ORM\FieldType\DBDate;

    class PetitionsListPage extends SiteTree
    {
        private static $db = [];

        private static $has_one = [];
    }

    class PetitionsListPageController extends PageController
    {

    	public function Petitions() {
    		return PetitionPage::get()->filter('ClosingDate:GreaterThanOrEqual', date("Y-m-d"))->sort('ClosingDate', 'ASC');
    	}

    }
}
