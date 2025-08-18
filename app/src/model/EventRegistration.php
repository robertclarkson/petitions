<?php
namespace {

use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\CompositeField;
use SilverStripe\Forms\FormField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\SelectionGroup;
use SilverStripe\Forms\SelectionGroup_Item;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\ToggleCompositeField;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\Security\Member;
use SilverStripe\View\ArrayData;

    class EventRegistration extends DataObject 
    {

        private static $db = [
            'Hash' => 'Varchar(32)',
            
            'Name' => 'Varchar(255)',
            'Email' => 'Varchar(255)',
            'Phone' => 'Varchar(32)',
            'Age' => 'Int',
            'ParentName' => 'Varchar(100)',
            'Sex' => 'Enum("Female, Male")',

            'PaymentStatus' => "Enum('Pending,Paid,Refunded')", 

            'ActiveMember' => 'Boolean',
            
            'EmergencyContactName' => 'Varchar(127)',
            'EmergencyContactPhone' => 'Varchar(127)',
            
            'WaiverAgreement' => 'Boolean',
            'WaiverAgreementParent' => 'Boolean',

            'Questions' => 'Text',
            'AddOns' => 'Text',

            'RegistrationCost' => 'Currency',
            'TotalCost' => 'Currency',

        ];

        private static $has_one = [
            'SportIdentCategory' => SportIdentCategory::class,
            'Event' => Event::class,
            'Member' => Member::class
        ];

        private static $has_many = [
            'Payments' => StripePayment::class
        ];

        private static $casting = [
            'RegistrationCost' => 'Currency',
            'TotalCost' => 'Currency',
            'QuestionsSummary' => 'HTMLText',
            'AddonsSummary' => 'HTMLText',
        ];

        private static $summary_fields = [
            'Created.Nice' => 'Submitted',
            'Name' => 'Name',
            'SportIdentCategory.InternalTitle' => 'Category',
            'Email' => 'Email',
            'Phone' => 'Phone',
            'QuestionsSummaryCSV' => 'Questions',
            'AddonsSummaryCSV' => 'Paid AddOns',
            'TotalCost.Nice' => 'Total',
            'PaymentStatus' => 'Payment',
        ];

        private static $default_sort = 'Created DESC';

        public function getCMSFields() {
            $fields = parent::getCMSFields();

            $addons = $fields->fieldByName('AddOns');
            
            $addonView = $this->AddonsSummary();
            $fields->replaceField('AddOns', LiteralField::create('AddOns', '<div class="form-group field"><label class="form__field-label">Addons</label><div class="form__field-holder">'.$addonView.'</div></div>'));

            $questions = $fields->fieldByName('Questions');
            
            $questionView = $this->QuestionsSummary();
            $fields->replaceField('Questions', LiteralField::create('Questions', '<div class="form-group field"><label class="form__field-label">Questions</label><div class="form__field-holder">'.$questionView.'</div></div>'));


            return $fields;
        }

        public function AddonsSummaryArray() {
            if (!$this->AddOns || !is_array(json_decode($this->AddOns,true))) return [];
            $addonView = [];
            if ($this->AddOns) foreach(json_decode($this->AddOns,true) as $addon) {
                $eventAddon = $this->Event()->AddOns()->find('URLSegment', $addon['name']);
                $addonView[] = $eventAddon->Title.' => '.($addon['value'] ? 'Yes' : 'No').' => Cost: $'.($addon['value'] ? $addon['cost'] : 0);
            }
            return $addonView;
        }

        public function AddonsSummary() {
            return implode('<br />',$this->AddonsSummaryArray());
        }

        public function AddonsSummaryCSV() {
            return implode(", ",$this->AddonsSummaryArray());
        }

        public function QuestionsSummaryArray() {
            if (!$this->Questions || !is_array(json_decode($this->Questions,true))) return [];
            $questionView = [];
            if ($this->Questions) foreach(json_decode($this->Questions,true) as $question) {
                foreach($question as $key => $val) {
                    $questionView[] = $key.' => '.$val;
                }
            }
            return $questionView;
        }

        public function QuestionsSummary() {
            return implode('<br />',$this->QuestionsSummaryArray());
        }

        public function QuestionsSummaryCSV() {
            return implode(", ",$this->QuestionsSummaryArray());
        }

        public function getFrontendFields($params = NULL) {
            $fields = parent::getFrontendFields($params);
            $fields->removeByName('Hash');
            $fields->removeByName('Questions');
            $fields->removeByName('RegistrationCost');
            $fields->removeByName('TotalCost');
            $fields->removeByName('PaymentStatus');
            $fields->fieldByName('WaiverAgreement')->setTitle('Do you agree to the terms of the waiver?');
            $fields->fieldByName('WaiverAgreementParent')->setTitle('For under 18 entrants - As the parent or guardian of this entrant do you agree to the terms of the waiver?');
            $fields->fieldByName('Sex')->setEmptyString('Please select...');

            return $fields;
        }

        public function Update($data) {
            if (isset($data['AddOns'])) {
                $data['AddOns'] = json_encode($data['AddOns']);
            }
            parent::Update($data);
        }

        public function onBeforeWrite() {
            parent::onBeforeWrite();

            if(!$this->Hash) {
                $this->Hash = substr(bin2hex(openssl_random_pseudo_bytes(32)), 0, 32) ;
            }
        }

        public function ConfirmTableRows() {
            
            foreach($this->toMap() as $key => $val) {
                if(in_array($key, [
                    'ID', 
                    'Created', 
                    'LastEdited', 
                    'Hash', 
                    'ClassName', 
                    'EventID', 
                    'RecordClassName', 
                ])) continue;
                $row = ArrayData::create();
                $row->Name = FormField::name_to_label($key);
                if($key == 'Questions') {
                    $row->Val = DBField::create_field('HTMLText',$this->QuestionsSummary());
                }
                else if($key == 'AddOns') {
                    $row->Val = DBField::create_field('HTMLText',$this->AddonsSummary());
                }
                else if($key == 'RegistrationCost') {
                    $row->Val = DBField::create_field('Currency', $this->RegistrationCost)->Nice();
                }
                else if($key == 'TotalCost') {
                    $row->Val = DBField::create_field('Currency', $this->TotalCost)->Nice();
                }
                else if($key == 'WaiverAgreement') {
                    $row->Val = $val ? 'Yes' : 'No';
                }
                else if($key == 'WaiverAgreementParent') {
                    $row->Val = $val ? 'Yes' : 'No';
                }
                else if($key == 'SportIdentCategoryID') {
                    $row->Val = $val ? SportIdentCategory::get()->byId($val)->Title : null;
                }
                else {
                    $row->Val = $val;
                }
                $rows[] = $row;
            }
            return ArrayList::create($rows);
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
            return false;
        }
    }
}