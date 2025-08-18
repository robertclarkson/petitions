<?php


namespace {

use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\ORM\FieldType\DBDate;

    class ProfileController extends PageController
    {

    	private static $allowed_actions = [

        ];

        protected function init()
        {
            parent::init();

        }

        public function index() {
            return $this->renderWith(['ProfilePage', 'Page']);
        }


    }
}
