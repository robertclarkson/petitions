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

    class Submission extends DataObject 
    {

        private static $db = [
            'Name' => 'Varchar(255)',
            'Phone' => 'Varchar(32)',
            'Email' => 'Varchar(255)',
            'AddressLine1' => 'Varchar(255)',
            'AddressLine2' => 'Varchar(255)',
            'Suburb' => 'Varchar(255)',
            'City' => 'Varchar(255)',
            'Postcode' => 'Varchar(10)',

            'Submission' => "Enum('support, oppose')",
            'Heard' => "Enum('Heard, Not Heard')",

            'MySubmissionIs' => 'Text',
            'Reasons' => 'Text',
            'Decision' => 'Text',
            'Hash' => 'Varchar(32)',
            'Verified' => 'Boolean(0)',
            'Submitted' => 'Boolean(0)',
            "Resident" => 'Boolean',
            "Business" => 'Boolean',
            "Work" => 'Boolean',
            "Recreation" => 'Boolean',
            'BeerwahOptions' => 'Enum("support,oppose,neither")'
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
            'isSubmitted' => 'Submitted'

        ];

        private static $default_sort = 'Created DESC';


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

            $render = $this->ClassName == 'Submission' ? 'PetitionPDF' : 'DocPetitionPDF';

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

            $subjectFileName = $this->ClassName == 'Submission' ? 
                $petition->ApplicationReferenceNumber.' Form 13 submission by '.$this->Name
                :
                'Draft Otago CMS Submission by '.$this->Name;


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