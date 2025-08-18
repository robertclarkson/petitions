<?php

use Colymba\BulkManager\BulkManager;
use SilverStripe\Admin\ModelAdmin;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\Forms\GridField\GridFieldFilterHeader;

class SubmissionsAdmin extends ModelAdmin 
{

    private static $managed_models = [
        'Submission'
    ];

    private static $url_segment = 'submissions';

    private static $menu_title = 'Submissions';

    private static $menu_icon_class = 'font-icon-clipboard-pencil';


    protected function getGridFieldConfig(): GridFieldConfig
    {
        $config = parent::getGridFieldConfig();

        $config->addComponent(new EmailVerificationAction());
        $config->addComponent(new EmailSubmissionAction());
        

        $BulkManager = new \Colymba\BulkManager\BulkManager();
        $BulkManager->addBulkAction('SendSubmissionHandler');
        $config->addComponent($BulkManager);

        return $config;

    }

    public function getExportFields() {
        return [
            'Created.Nice' => 'Created',
            'Name' => 'Name',
            'Phone' => 'Phone',
            'Email' => 'Email',
            'AddressLine1' => 'AddressLine1',
            'AddressLine2' => 'AddressLine2',
            'Suburb' => 'Suburb',
            'City' => 'City',
            'Postcode' => 'Postcode',

            'Submission' => 'Submission',
            'Heard' => 'Heard',

            'MySubmissionIs' => 'MySubmissionIs',
            'Reasons' => 'Reasons',
            'Decision' => 'Decision',

            'Verified' => 'Verified',
            'Submitted' => 'Submitted',
        ];
    }

}