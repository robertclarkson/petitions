<?php

use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\EmailField;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\ORM\DataExtension;


class SiteConfigExtension extends DataExtension{

	private static $db = array(
		'AdminEmail' => 'Varchar(255)',
		'SubmissionThankyou' => 'HTMLText',
		'VerificationThankyou' => 'HTMLText',
		'EventWaiver' => 'HTMLText',
		'EventParentalWaiver' => 'HTMLText',
		'EventConfirmationEmail' => 'HTMLText'
	);

	private static $has_one = array(
		'Logo' => Image::class,
		'LogoBlack' => Image::class,
		'LogoGold' => Image::class
	);

	private static $owns = [
        'Logo',
        'LogoBlack',
        'LogoGold'
    ];

    public function UpdateCMSFields(SilverStripe\Forms\FieldList $fields){
		$fields->addFieldToTab('Root.Main', EmailField::create('AdminEmail'));
		$fields->addFieldToTab('Root.Main', UploadField::create('Logo'));
		$fields->addFieldToTab('Root.Main', UploadField::create('LogoBlack'));
		$fields->addFieldToTab('Root.Main', UploadField::create('LogoGold'));
		
		$fields->addFieldToTab('Root.ThankyouMessages', 
			HTMLEditorField::create('SubmissionThankyou')
				->setRightTitle('Use [name] to insert the person\'s name, use [email] to insert the person\'s email')
		);
		$fields->addFieldToTab('Root.ThankyouMessages', 
			HTMLEditorField::create('VerificationThankyou')
				->setRightTitle('Use [name] to insert the submitter\'s name')
		);

		$fields->addFieldsToTab('Root.EventSettings', 
			[
				HTMLEditorField::create('EventWaiver')
					->setRightTitle('This is the default waiver text for an event'),
				HTMLEditorField::create('EventParentalWaiver')
					->setRightTitle('This is the default parental waiver text for an event'),
				HTMLEditorField::create('EventConfirmationEmail')
					->setDescription('Use [name] to insert the submitter\'s name. This is the default confirmation email. This can be overridden with event\'s confirmation email if set.')
			]
		);

	}
}