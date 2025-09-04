<?php
namespace {

use Dompdf\Dompdf;
use SilverStripe\Assets\Image;
use SilverStripe\Control\Email\Email;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\View\ArrayData;
use SilverStripe\View\Parsers\ShortcodeParser;
use SilverStripe\i18n\Data\Intl\IntlLocales;

    class Submission extends DataObject 
    {

        private static $db = [
            'Firstname' => 'Varchar(255)',
            'Surname' => 'Varchar(255)',
            // 'Name' => 'Varchar(255)',
            'Phone' => 'Varchar(32)',
            'Email' => 'Varchar(255)',
            'AddressLine1' => 'Varchar(255)',
            'AddressLine2' => 'Varchar(255)',
            'Suburb' => 'Varchar(255)',
            'City' => 'Varchar(255)',
            'Postcode' => 'Varchar(10)',
            'Country' => 'Varchar(255)',
            'AgeRange' => 'Enum("19 or under, 20-29, 30-39, 40-49, 50-59, 60-69, 70-79, 80-89, 90-99, 100+")',

            // 'Submission' => "Enum('support, oppose')",
            // 'Heard' => "Enum('Heard, Not Heard')",
            // 'Reasons' => 'Text',
            // 'Decision' => 'Text',
            'Hash' => 'Varchar(32)',
            'Verified' => 'Boolean(0)',
            'Submitted' => 'Boolean(0)',
            
            "Resident" => 'Boolean',
            "Business" => 'Boolean',
            "Work" => 'Boolean',
            "Recreate" => 'Boolean',
            "Other" => 'Text',

            'Beerwah' => 'Enum("support,oppose,abstain")',
            'Revegetation' => 'Enum("support,oppose,abstain")',
            'Sustainability' => 'Enum("support,oppose,abstain")',
            'Traffic' => 'Enum("support,oppose,abstain")',
            'Mooloolah' => 'Enum("support,oppose,abstain")',
            'MaroochydoreHeight' => 'Enum("support,oppose,abstain")',
            'Caloundra' => 'Enum("support,oppose,abstain")',
            'Recreation' => 'Enum("support,oppose,abstain")',
            'MaroochydoreDevelopment' => 'Enum("support,oppose,abstain")',
            'Alexandra' => 'Enum("support,oppose,abstain")',

            'MySubmissionIs' => 'Text',
        ];

        private static $has_one = [
            'PetitionPage' => PetitionPage::class,
        	'SignatureImage' => Image::class
        ];

        private static $summary_fields = [
            'Created.Nice' => 'Created',
            'Name' => 'Name',
            'Email' => 'Email',
            'PetitionPage.Title' => 'Submission',
            'isVerified' => 'Verified',
            'isSubmitted' => 'Submitted',
            'Beerwah' => 'Beerwah',

        ];

        private static $default_sort = 'Created DESC';

        public function Name() {
            return $this->Firstname . ' ' . $this->Surname;
        }

        public function FullCountry() {
            return IntlLocales::singleton()->getCountries()[$this->Country] ?? $this->Country;
        }

        public function isVerified() {
            return $this->Verified ? 'Yes' : '';
        }

        public function isSubmitted() {
            return $this->Submitted ? 'Yes' : '';
        }


        public function onBeforeWrite() {
            parent::onBeforeWrite();

            if(!$this->Hash) {
                $this->Hash = substr(bin2hex(openssl_random_pseudo_bytes(32)), 0, 32) ;
            }
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

        public function sendSubmission() {

            $petition = $this->PetitionPage();
            if(!$petition) {
                throw new Exception('Submission is not attached to a petition page.');
            }
            
            if(!$petition->EmailTo) {
                throw new Exception('Submission petition does not have a "to" email address.');
            }

            $render = 'PetitionPDF';

            $pdf = new Dompdf();
            $pdf->output();
            $pdf->loadHTML(ArrayData::create([
                'Data' => $this,
                'SubmissionData' => $petition->toMap(),
                'Signature' => base64_encode($this->SignatureImage()->Fit(400,240)->getString())
            ])->renderWith($render));
            $pdf->render();
            $date = DBField::create_field('DBDatetime', date('d-m-Y'));

            // $pdf->stream('Petition_'.$date->format('y-M-d').'.pdf');

            $subjectFileName = 'Submission by '.$this->Name;

            $from = SiteConfig::current_site_config()->AdminEmail;
            $to = $petition->EmailTo;

            $emailContent = $petition->SubmissionEmail;
            $emailContent = str_replace('[name]', $this->Name, $emailContent);
            $emailContent = ShortcodeParser::get_active()->parse($emailContent);


            $email = Email::create()
                ->setBody($emailContent)
                ->setFrom($from, $this->Name)
                ->setReplyTo($this->Email, $this->Name)
                ->setTo($to)
                ->setSubject($subjectFileName);

            if($petition->CcTo) {
                $email->addCC($petition->CcTo);
            }

            $email->addCC($this->Email);

            $email->addAttachmentFromData($pdf->output(), $subjectFileName.'.pdf', "application/pdf");
            $email->send();

            $this->Submitted = 1;
            $this->write();

            return true;
        }
    }
}