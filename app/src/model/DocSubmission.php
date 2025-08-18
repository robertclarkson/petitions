<?php
namespace {

use SilverStripe\Assets\Image;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataObject;

    class DocSubmission extends Submission 
    {
    	private static $db = [
            'OrganisationName' => 'Varchar(255)',
        ];


        public function getCMSFields() {
	    	$fieldList = parent::getCMSFields();
            $fieldList->insertAfter('Name', TextField::create('OrganisationName'));

	    	$fieldList->removeByName('Submission');
	    	$fieldList->removeByName('Reasons');
	    	$fieldList->removeByName('Decision');
	    	$fieldList->removeByName('Hash');
	    	return $fieldList;
	    }
    }

}
