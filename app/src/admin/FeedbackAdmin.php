<?php

use SilverStripe\Admin\ModelAdmin;

class FeedbackAdmin extends ModelAdmin 
{

    private static $managed_models = [
        'Feedback'
    ];

    private static $url_segment = 'feedback';

    private static $menu_title = 'Feedback';
    
    private static $menu_icon_class = 'font-icon-checklist';

}