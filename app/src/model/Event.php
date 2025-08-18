<?php
namespace {

use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\Image;
use SilverStripe\Control\Controller;
use SilverStripe\Forms\CheckboxSetField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use SilverStripe\Forms\GridField\GridFieldExportButton;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBDatetime;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\UserForms\Model\EditableFormField;

    class Event extends DataObject 
    {

        private static $db = [
            'Hash' => 'Varchar(32)',
            'Title' => 'Varchar(255)',
            'Summary' => 'Text',
            'Location' => 'Enum("Queenstown,Wanaka,Dunedin")',
            'EventDateTime' => "DBDatetime",
            'RegistrationOpen' => "DBDatetime",
            'RegistrationClose' => "DBDatetime",
            
            'MemberPrice' => "Currency",
            'NonMemberPrice' => "Currency",
            'PayOnTheDayPrice' => "Currency",

            'Content' => 'HTMLText',

            'EventWaiver' => 'HTMLText',
            'EventParentalWaiver' => 'HTMLText',

            'EventConfirmationEmail' => 'HTMLText'

        ];

        private static $has_many = [
            'AddOns' => AddOn::class,
            'Registrations' => EventRegistration::class,
            'EditableFormFields' => EditableFormField::class,
        ];

        private static $has_one = [
            'Image' => Image::class
        ];
        
        private static $many_many = [
            'Slideshow' => Image::class
        ];

        private static $belongs_many_many = [
            'SportIdentCategories' => SportIdentCategory::class,
            'EventCategory' => EventCategory::class
        ];

        private static $owns = [
            'Image',
            'Slideshow'
        ];

        private static $summary_fields = [
            'Title' => 'Title',
            'EventCategoryList' => 'Category',
            'EventDateTime.Nice' => 'Event Date',
            'RegistrationOpen.Nice' => 'Reg Open',
            'RegistrationClose.Nice' => 'Reg Close',
            'MemberPrice' => 'Member',
            'NonMemberPrice' => 'NonMember',
            'PayOnTheDayPrice' => 'OnTheDay',
            
        ];

        private static $default_sort = 'EventDateTime ASC';

        public function EventCategoryList() {
            return implode(',', $this->EventCategory()->column('Title'));
        }

        public function getMonthYear() 
        {

            $datetime = DBField::create_field('DBDatetime', $this->EventDateTime)->format('MMM YYYY');
            return $datetime;
        }

        public function onBeforeWrite() {
            parent::onBeforeWrite();

            if(!$this->Hash) {
                $this->Hash = substr(bin2hex(openssl_random_pseudo_bytes(32)), 0, 32) ;
            }
        }

        public function getCMSFields() {
            $fields = parent::getCMSFields();

            $hashField = $fields->dataFieldByName('Hash')->performReadonlyTransformation();

            $fields->dataFieldByName('Summary')->setTitle('Summary - shows on the events list');

            $fields->removeByName('Hash');
            $fields->addFieldToTab('Root.Internal', 'Hash', $hashField);

            $fields->removeByName('Slideshow');
            $fields->addFieldToTab('Root.Main', UploadField::create('Slideshow'));

            $fields->removeByName('EventCategory');
            $eventCategory = CheckboxSetField::create('EventCategory', 'Event Category', EventCategory::get()->map('ID', 'Title'));
            $fields->addFieldToTab('Root.EventCategory', $eventCategory);
            

            $fields->removeByName('SportIdentCategories');
            $sportIdentField = CheckboxSetField::create('SportIdentCategories', 'Sport Ident', SportIdentCategory::get()->map('ID', 'Title'));
            $fields->addFieldToTab('Root.SportIdent', $sportIdentField);
            $htmlstring = <<<'EOD'
            <script>
            let toggleAll = true;
             function selectAll() {
                checkboxes = document.querySelectorAll('[name^="SportIdentCategories"]');
                  for(var i=0, n=checkboxes.length;i<n;i++) {
                    checkboxes[i].checked = toggleAll;
                  }
                  toggleAll=!toggleAll;
              }
            </script>
                <button onClick="selectAll()">Toggle All</button>
EOD;
            $fields->addFieldToTab('Root.SportIdent', LiteralField::create('SelectAll', $htmlstring));

            $waiver = $fields->dataFieldByName('EventWaiver');
            $parentalWaiver = $fields->dataFieldByName('EventParentalWaiver');

            $siteConfig = SiteConfig::current_site_config();
            if(!$this->EventWaiver){
                $this->EventWaiver = $siteConfig->EventWaiver;
            }
            if(!$this->EventParentalWaiver){
                $this->EventParentalWaiver = $siteConfig->EventParentalWaiver;
            }

            $fields->addFieldToTab('Root.Waivers', $waiver);
            $fields->addFieldToTab('Root.Waivers', $parentalWaiver);

            $editableGF = $fields->dataFieldByName('EditableFormFields');
            if($editableGF) {
                $editableGFconfig = GridFieldConfig_RecordEditor::create();
                $editableGF->setConfig($editableGFconfig);
            }

            // $editableGFconfig = $editableGF->getConfig();
            // $editableGFconfig->removeComponent(new GridFieldDeleteAction());
            // $editableGFconfig->addComponent(new GridFieldDeleteAction());

            $registrationsGF = $fields->dataFieldByName('Registrations');
            if($registrationsGF) {
                $registrationsGFconfig = $registrationsGF->getConfig();
                // $dataColumns = $config->getComponentByType(GridFieldDataColumns::class);
                $exportButton = new GridFieldExportButton();
                $exportButton->setExportColumns([
                    'Name' => 'Name',
                    'SportIdentCategory.InternalTitle' => 'Category',
                    'Email' => 'Email',
                    'Phone' => 'Phone',
                    'Age' => 'Age',
                    'ParentName' => 'Parent Name',
                    'EmergencyContactName' => 'Emergency Contact Name',
                    'EmergencyContactPhone' => 'Emergency Contact Phone',
                    'QuestionsSummaryCSV' => 'Questions',
                    'AddonsSummaryCSV' => 'Paid AddOns',
                    'TotalCost.Nice' => 'Total',
                    'PaymentStatus' => 'Payment',
                ]);
                $registrationsGFconfig->addComponent($exportButton);
            }

            $fields->addFieldToTab('Root.Email', HTMLEditorField::create('EventConfirmationEmail')
                    ->setDescription('Use [name] to insert the submitter\'s name. This will override the default confirmation email if set.'));
            if($this->ID) $fields->fieldByName('Root.AddOns')->Fields()->insertBefore('AddOns', LiteralField::create('addonsinfo', '<h1>Here you can add yes/no checkable options that add to the total cost</h1>'));
            if($this->ID) $fields->fieldByName('Root.EditableFormFields')->Fields()->insertBefore('EditableFormFields', LiteralField::create('editinfo', '<h1>Here you can add different types of fields that collect more information from the registrant</h1>'));
            if(!$this->ID) $fields->fieldByName('Root.Main')->Fields()->insertBefore('Title', LiteralField::create('info1', '<h1>Once you save this event for the first time you will be able to add custom fields and addons.</h1>'));
            $fields->fieldByName('Root.EventCategory')->Fields()->insertBefore('EventCategory', LiteralField::create('editinfo', '<h1>This is how the event is categorised on the event listings page</h1>'));
            $fields->fieldByName('Root.SportIdent')->Fields()->insertBefore('SportIdentCategories', LiteralField::create('editinfo', '<h1>Check the Sport Ident categories you want to enable for this event and hit save.</h1>'));
            return $fields;
        }

        public function PaidEntrants() {
            return $this->Registrations()->filter('PaymentStatus', 'Paid');
        }

        public function CanRegister() {
            return strtotime($this->RegistrationOpen) < time() && strtotime($this->RegistrationClose) > time();
        }

        public function RelatedEvents() {
            $events = Event::get()->filter([
                'ID:not' => $this->ID,
                'RegistrationClose:GreaterThanOrEqual' => date("Y-m-d")
            ])->sort('RegistrationClose', 'ASC');
            if($this->EventCategory()->exists()) {
                $events = $events->filter([
                    'EventCategory.ID' => $this->EventCategory()->column('ID')
                ]);
            } else {
                $events = $events->filter([
                    'EventCategory.ID' => 0
                ]);
            }
            return $events;
        }


        public function canCreate($member = NULL, $context = []) {
            return true;
        }

        public function canEdit($member = NULL) {
            return true;
        }

        public function canView($member = NULL) {
            return true;
        }

        public function canDelete($member = NULL) {
            return true;
        }

        public function Link($action = NULL) {
            return Controller::join_links('event-registration', 'signup', $this->Hash);
        }

        public function duplicate(bool $doWrite = true, ?array $relations = null): static {
            $newItem = parent::duplicate($doWrite, $relations);
            $newItem->Hash = '';
            $newItem->Title = $newItem->Title.' (copy - '.DBDatetime::now()->Date().')';

            foreach($this->SportIdentCategories() as $cat) {
                $newItem->SportIdentCategories()->add($cat);
            }

            foreach($this->EventCategory() as $cat) {
                $newItem->EventCategory()->add($cat);
            }


            foreach($this->EditableFormFields() as $editableField) {
                $newfield = $editableField->duplicate();
                $newfield->ParentID = $newItem->ID;
                $newItem->EditableFormFields()->add($newfield);
                $newfield->publish('Stage', 'Live');
            }

            foreach($this->AddOns() as $addon) {
                $newfield = $addon->duplicate();
                $newItem->AddOns()->add($newfield);
            }

            if ($doWrite) {
                $newItem->write();
            }
            return $newItem;
        }
    }
}