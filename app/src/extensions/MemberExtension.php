<?php

use SilverStripe\Security\Permission;
use SilverStripe\ORM\DataExtension;

class MemberExtension extends DataExtension 
{
    /**
    * Modify the field set to be displayed in the CMS detail pop-up
    */
    // public function updateCMSFields(FieldList $currentFields) 
    // {
    //     // Only show the additional fields on an appropriate kind of use 
    //     if(Permission::checkMember($this->owner->ID, "VIEW_FORUM")) {
    //         // Edit the FieldList passed, adding or removing fields as necessary
    //     }
    // }

    // define additional properties
    private static $db = [
        'firebaseid' => 'Varchar(32)',
        'code' => 'Varchar(32)',
        'ageGroupId' => 'Int',
        'ageGroupName' => 'Varchar(32)',
        'genderId' => 'Int',
        'genderName' => 'Varchar(32)',
        'isActive' => 'Boolean',
        'membershipPackageId' => 'Int',
        'membershipPackageName' => 'Varchar(32)',
        'addressLine1' => 'Varchar(64)',
        'addressLine2' => 'Varchar(64)',
        'city' => 'Varchar(64)',
        'country' => 'Varchar(64)',
        'postcode' => 'Varchar(16)',
    ]; 

    private static $has_one = []; 
    private static $has_many = [
        'Registrations' => 'EventRegistration'
    ]; 
    private static $many_many = []; 
    private static $belongs_many_many = []; 

}
