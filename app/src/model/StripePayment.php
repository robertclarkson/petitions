<?php
namespace {

use SilverStripe\Assets\Image;
use SilverStripe\Forms\LiteralField;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBField;

    class StripePayment extends DataObject 
    {

        private static $db = [
           'payment_intent' => 'Varchar(127)',
           'charge_id' => 'Varchar(127)',
           'customer_email' => 'Varchar(255)',
           'amount_total' => 'Int',
           'payment_status' => 'Varchar(127)',
           'livemode' => 'Boolean',
        ];

        private static $has_one = [
           "EventRegistration" => EventRegistration::class
        ];

        private static $summary_fields = [
            'Created.Nice' => 'Date',
            'EventRegistration.Name' => 'Name',
            'customer_email' => 'Email',
            'AmountDollars' => 'Amount',

            
        ];

        public function getCMSFields() {
            $fields = parent::getCMSFields();
            $test = $this->livemode ? '' : 'test/';
            $fields->addFieldToTab('Root.Main', LiteralField::create('Stripe', '<p>See payment in Stripe to process refund: <a href="'.$this->StripePaymentLink().'" target="_new">'.$this->StripePaymentLink().'</a></p>'));
            return $fields;
        }

        private static $default_sort = 'Created DESC';

        public function StripePaymentLink() {
            $test = $this->livemode ? '' : 'test/';
            return "https://dashboard.stripe.com/".$test."payments/".$this->payment_intent;
        }

        public function AmountDollars() {
            return DBField::create_field('Currency', $this->amount_total / 100);
        }


        public function canCreate($member = NULL, $context = []) {
            return false;
        }

        public function canEdit($member = NULL) {
            return false;
        }

        public function canView($member = NULL) {
            return true;
        }

        public function canDelete($member = NULL) {
            return true;
        }
    }
}