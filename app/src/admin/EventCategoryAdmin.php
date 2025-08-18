<?php

use SilverStripe\Admin\ModelAdmin;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\Forms\GridField\GridFieldFilterHeader;

class EventCategoryAdmin extends ModelAdmin 
{

    private static $managed_models = [
        'EventCategory'
    ];

    private static $url_segment = 'event-categories';

    private static $menu_title = 'Event Categories';

    private static $menu_icon_class = 'font-icon-tree';
}