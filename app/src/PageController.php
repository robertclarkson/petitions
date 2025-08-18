<?php


namespace {
    
use SilverStripe\CMS\Controllers\ContentController;
use SilverStripe\View\Requirements;
use SilverStripe\View\ThemeResourceLoader;

    class PageController extends ContentController
    {
        /**
         * An array of actions that can be accessed via a request. Each array element should be an action name, and the
         * permissions or conditions required to allow the user to access it.
         *
         * <code>
         * [
         *     'action', // anyone can access this action
         *     'action' => true, // same as above
         *     'action' => 'ADMIN', // you must have ADMIN permissions to access this action
         *     'action' => '->checkAction' // you can only access this action if $this->checkAction() returns true
         * ];
         * </code>
         *
         * @var array
         */
        private static $allowed_actions = [

        ];

        protected function init()
        {
            parent::init();

            Requirements::css('https://cdnjs.cloudflare.com/ajax/libs/tiny-slider/2.9.3/tiny-slider.css');
            Requirements::css("https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css");
            Requirements::javascript(ThemeResourceLoader::themedResourceURL('/javascript/bundle.min.js'));
            Requirements::css(ThemeResourceLoader::themedResourceURL('/css/styles.min.css'));
        }

    }
}
