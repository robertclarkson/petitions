<?php

use SilverStripe\Admin\ModelAdmin;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\Forms\GridField\GridFieldFilterHeader;
use SwiftDevLabs\DuplicateDataObject\Forms\GridField\GridFieldDuplicateAction;

class EventAdmin extends ModelAdmin 
{

    private static $managed_models = [
        'Event'
    ];

    private static $url_segment = 'events';

    private static $menu_title = 'Events';

    private static $menu_icon_class = 'font-icon-list';


    protected function getGridFieldConfig(): GridFieldConfig
    {
        $config = parent::getGridFieldConfig();
        return $config;
    }
}