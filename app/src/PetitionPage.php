<?php


namespace {

use Dompdf\Dompdf;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\File;
use SilverStripe\Assets\Image;
use SilverStripe\Assets\Storage\AssetStore;
use SilverStripe\Assets\Storage\FileHashingService;
use SilverStripe\CMS\Controllers\ContentController;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\Control\Email\Email;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Forms\CompositeField;
use SilverStripe\Forms\DateField;
use SilverStripe\Forms\DatetimeField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\EmailField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\Forms\HeaderField;
use SilverStripe\Forms\HiddenField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\OptionsetField;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\ORM\FieldType\DBDate;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\View\Parsers\ShortcodeParser;
use SilverStripe\View\Requirements;
use SilverStripe\View\ThemeResourceLoader;

    class PetitionPage extends Page {

    	private static $db = [
	        'Title' => 'Varchar(255)',
	        'ApplicationReferenceNumber' => 'Varchar(255)',
	        'ApplicantsName' => 'Varchar(255)',
	        'ApplicationDetails' => 'Text',
	        'ApplicationLocation' => 'Varchar(255)',
	        'ClosingDate' => 'Datetime',

	        'DefaultSupportPosition' => 'Enum("support,oppose")',
	        'MySubmissionIs' => 'Text',
            'Reasons' => 'Text',
            'Decision' => 'Text',

            'EmailTo' => 'Varchar(255)',
            'CcTo' => 'Varchar(255)',

            "Resident" => 'Boolean',
            "Business" => 'Boolean',
            "Work" => 'Boolean',
            "Recreation" => 'Boolean',

            'ConfirmationEmail' => 'HTMLText',
            'SubmissionEmail' => 'HTMLText',

            'BeerwahOptions' => 'Enum("support,oppose,neither")'
	    ];

	    private static $owns = [
	    	'Image',
            'GalleryImage'
	    ];

	    private static $has_one = [
	    	'Image' => Image::class
	    ];

	    private static $has_many = [
	    	'Submissions' => Submission::class,
	    ];

        private static $many_many = [
            'GalleryImage' => Image::class
        ];


	    private static $summary_fields = [
	        'ApplicationReferenceNumber',
	        'Title',
	        'ApplicantsName',
	        'ApplicationLocation',
	        'ClosingDate.Nice'
	    ];

	    public function getCMSFields() {
	    	$fieldList = parent::getCMSFields();

	    	$fieldList->dataFieldByName('Content')->setTitle('Pre-amble for the submission');
            // $fieldList->insertAfter('MenuTitle', UploadField::create('Image', 'Inspiring Image'));
	    	// $fieldList->insertAfter('MenuTitle', UploadField::create('GalleryImage', 'Inspiring Images for Gallery'));

	    	// $fieldList->addFieldToTab('Root.PetitionDetails', TextField::create('ApplicationReferenceNumber'));
	    	// $fieldList->addFieldToTab('Root.PetitionDetails', TextField::create('ApplicantsName'));
	    	// $fieldList->addFieldToTab('Root.PetitionDetails', TextareaField::create('ApplicationDetails'));
	    	// $fieldList->addFieldToTab('Root.PetitionDetails', TextField::create('ApplicationLocation'));
	    	// $fieldList->addFieldToTab('Root.PetitionDetails', DatetimeField::create('ClosingDate'));
	    	// $fieldList->addFieldToTab('Root.PetitionDetails', DropdownField::create('DefaultSupportPosition', 'Set the default for the support/oppose dropdown', [
            //         'support' => 'Support the submission', 
            //         'oppose' => 'Oppose the submission'
            //     ])->setEmptyString('Please select....'));
	    	$fieldList->addFieldToTab('Root.PetitionDetails', TextareaField::create('MySubmissionIs'));
	    	// $fieldList->addFieldToTab('Root.PetitionDetails', TextareaField::create('Reasons'));
            // $fieldList->addFieldToTab('Root.PetitionDetails', TextareaField::create('Decision'));
            $fieldList->addFieldToTab('Root.PetitionDetails', EmailField::create('EmailTo'));
	    	$fieldList->addFieldToTab('Root.PetitionDetails', EmailField::create('CcTo'));

            $fieldList->addFieldToTab('Root.Emails', 
                HTMLEditorField::create('ConfirmationEmail')
                    ->setRightTitle('Use [name] to insert the person\'s name, use [link] to insert the verification link')
            );
            $fieldList->addFieldToTab('Root.Emails', 
                HTMLEditorField::create('SubmissionEmail')
                    ->setRightTitle('Use [name] to insert the submitter\'s name')
            );


	    	return $fieldList;
	    }

    }


    class PetitionPageController extends PageController
    {

    	private static $allowed_actions = [
            'Form',
            'submit',
            'verifySubmissionEmail',
            'FeedbackForm',
            'submitFeedback',
        ];

    	protected function init()
        {
            parent::init();
            // You can include any CSS or JS required by your project here.
            // See: https://docs.silverstripe.org/en/developer_guides/templates/requirements/
        }

        public function Form() {
            // echo '<pre>';
            // print_r($this->request());
            // echo '</pre>';
            $fields = FieldList::create();
            $fields->push(HiddenField::create('SubmissionType', '', 'Submission'));
            
            $fields->push(HeaderField::create('h2', 'Your Details'));
            $fields->push(TextField::create('Name'));
            $fields->push(TextField::create('Phone'));
            $fields->push(TextField::create('Email'));
            $fields->push(TextField::create('AddressLine1', 'Postal Address Line 1'));
            $fields->push(TextField::create('AddressLine2', 'Postal Address Line 2'));
            $fields->push(TextField::create('Suburb'));
            $fields->push(TextField::create('City'));
            $fields->push(TextField::create('Postcode'));

            // $applicantfields = CompositeField::create();
            // $applicantfields->push(HeaderField::create('h2', 'Applicant Details'));
            // $applicantfields->push(LiteralField::create('h2', '<p>These are the details of the entity which has submitted the Resource Consent to Council, so we have pre-populated this for you.</p>'));
            
            // $applicantfields->push(TextField::create('ApplicantsName', 'Applicant\'s Name')->performReadonlyTransformation());
            // $applicantfields->push(TextField::create('ApplicationReferenceNumber')->performReadonlyTransformation());
            // $applicantfields->push(TextareaField::create('ApplicationDetails')->performReadonlyTransformation());
            // $applicantfields->push(TextareaField::create('ApplicationLocation')->performReadonlyTransformation());
            
            // $fields->push($applicantfields);
            // // $applicantfields->getChildren()->setValues($this->dataRecord->toMap());
            // $applicantfields->getChildren()->makeReadonly();

            $fields->push(HeaderField::create('h2', 'My Grounds for submission'));
            $fields->push(CheckboxField::create('Resident', 'I am a resident of the Sunshine Coast'));
            $fields->push(CheckboxField::create('Business', 'I do business on the Sunshine Coast'));
            $fields->push(CheckboxField::create('Work', 'I work on the Sunshine Coast'));
            $fields->push(CheckboxField::create('Recreation', 'I recreate on the Sunshine Coast'));

            $fields->push(HeaderField::create('h2', 'Submission'));
            // $fields->push(DropdownField::create('Submission', 'Do you support or oppose the submission? (We hope you '.$this->dataRecord->DefaultSupportPosition.' it)', [
            //         'support' => 'I support the submission', 
            //         'oppose' => 'I oppose the submission'
            //     ])->setEmptyString('Please select....')->setValue($this->dataRecord->DefaultSupportPosition));
            
            // $fields->push($heardField = DropdownField::create('Heard', 'Do you wish to be heard in support of your submission?', [
            //     'Heard' => 'I do wish to be heard in support of my submission', 
            //     'Not Heard' => 'I do not wish to be heard in support of my submission'
            // ])->setEmptyString('Please select...'));
            // $heardField->LeftTitle = 'What this means is that if you select that you would like to be heard, Council will get in contact and ask you to come speak in person on your submission. You can select either option, up to you!';
            
            //create an optionset with the choices in it
            $fields->push($submissionOptions = OptionsetField::create('BeerwahOptions', 'Please advise your preference for the Caloundra to Beerwah area (optional)', [
                'oppose' => 'I strongly disagree with the proposal. I want this area to be regenerated into native forest and protected in perpetuity as a community recreation zone.',
                'support' => 'I agree with the proposal and want this area to be cleared for a housing development of 20,000 houses',
                'neither' => 'I neither support nor oppose the proposal.'
            ]));

            $fields->push($submissionIs = TextareaField::create('MySubmissionIs', 'My submission is'));
            $submissionIs->LeftTitle = 'Here is where we would like you to talk about the particulars of the application you support or object to.';

            // $fields->push($reasons = TextareaField::create('Reasons', 'The Reasons For My Submission Are'));
            // $reasons->LeftTitle = 'Here is where we would like you to talk about your personal view around this submission. We have included some ideas to help, but please, delete or adapt as you see fit.';

            // $fields->push($decision = TextareaField::create('Decision', 'MY SUBMISSION WOULD BE MET BY THE QUEENSTOWN LAKES DISTRICT COUNCIL MAKING THE FOLLOWING DECISION'));
            // $decision->LeftTitle = 'Again, we\'ve pre-filled some text for you, but please, change as you see fit. This is where we ask Council to make a decision and request and conditions which may be sought.';


            $fields->push(HeaderField::create('h2', 'Signature'));
            $fields->push(HiddenField::create('sig'));
            $fields->push(LiteralField::create('signaturecode', <<<html
  				<div id="signature-pad" class="signature-pad">
	                <div class="signature-pad--body">
	                    <canvas width="664" height="373" style="touch-action: none;"></canvas>
	                </div>
	                <div class="signature-pad--footer">
	                  <div class="signature-pad--actions">
	                    <div>
	                       <button type="button" class="button clear" data-action="clear">Clear Signature</button>
	                       <button type="button" class="button" data-action="undo">Undo</button>
                        </div>
	                  </div>
	                </div>
	                <div style="clear:both"></div>
                </div>
html
            ));

//             $fields->push(LiteralField::create('loadingicon', <<<html
//                 <img class="cycle-loading" src="{ThemeResourceLoader::themedResourceURL('/images/cycle-loading.gif')}" />
// html
//             ));
            $fields->push(LiteralField::create('Date', '<p>'.DBField::create_field('Date', date('Y-m-d'))->format('{o} MMMM Y').'</p>'));

            
            $actions = FieldList::create();
            $actions->push($submit = FormAction::create('submit', 'Submit'));
            $actions->push(LiteralField::create('submitinfo', '<p><br />&nbsp;&nbsp;&nbsp;Please click the link in the email we\'re about to send you</p>'));
            $submit->addExtraClass('btn btn-primary');
            $required = RequiredFields::create([
                'Name',
                'Phone',
                'Email',
                'AddressLine1',
                'City',
                'Postcode',
                'Submission',
                'Heard',
                'MySubmissionIs',
                'Reasons',
                'Decision'
            ]);
            $form = Form::create($this, 'form', $fields, $actions, $required);
            
            // print_r($form->fields()->dataFieldByName('Name')->value());
            // if($form->fields()->dataFieldByName('Name')->value() == '') {
            $fields->setValues($this->dataRecord->toMap());
            // }
            if(Director::isLive()) $form->enableSpamProtection(array(
               'insertBefore' => 'FieldName'
            ));
            return $form;

        }

        public function submit($data, $form) {
            $submissionType = $data['SubmissionType'] ? $data['SubmissionType'] : 'Submission';

        	$submission = $submissionType::create();
        	$submission->update($data);
        	$submission->PetitionPageID = $this->ID;
        	$submission->write();

            // $data_uri = $this->request->getVar('sig');
            $data_uri = $this->request->postVar('sig');
            $encoded_image = explode(",", $data_uri)[1];
            $decoded_image = base64_decode($encoded_image);
            // file_put_contents("signature.jpg", $decoded_image);

            $IRfile['name'] = "signature.jpg";
            $content = $decoded_image;
            
             /** @var FileHashingService $hasher */
            $hasher = Injector::inst()->get(FileHashingService::class);

            $stream = fopen('php://memory','r+');
            fwrite($stream, $content);
            rewind($stream);
            // When saving original filename, generate hash
            $hash = $hasher->computeFromStream($stream);

            $store = singleton(AssetStore::class);
            $return = $store->setFromString($content, 'signatures/'.bin2hex(openssl_random_pseudo_bytes(10)).'.jpg', $hash, null, [
                'visibility' => AssetStore::VISIBILITY_PROTECTED,
                'conflict' => AssetStore::CONFLICT_RENAME
            ]);

            $signatureFile = Image::create();
            $signatureFile->renameFile($return['Filename']);
            $signatureFile->FileHash = $return['Hash'];
            $signatureFile->FileFilename = $return['Filename'];
            $signatureFile->FileVariant = $return['Variant'];
            $signatureFile->write();
            $signatureFile->protectFile();

            $submission->SignatureImageID = $signatureFile->ID;
            $submission->write();


            // reference the Dompdf namespace

            // instantiate and use the dompdf class
            // $dompdf = new Dompdf();
            // $dompdf->loadHtml('hello world');

            // // (Optional) Setup the paper size and orientation
            // $dompdf->setPaper('A4', 'landscape');

            // // Render the HTML as PDF
            // $dompdf->render();

            // // Output the generated PDF to Browser
            // $dompdf->stream();

            $subjectFileName = 'Please verify your submission by '.$data['Name'];

			$from = SiteConfig::current_site_config()->AdminEmail;
			$to = $submission->Email;

            $emailContent = $this->dataRecord->ConfirmationEmail;
            $emailContent = str_replace('[name]', $submission->Name, $emailContent);
            $emailContent = str_replace('[link]', Controller::join_links($this->AbsoluteLink(),'verifySubmissionEmail',$submission->Hash), $emailContent);
            $emailContent = ShortcodeParser::get_active()->parse($emailContent);

			$email = Email::create()
			    ->setBody($emailContent)
			    ->setFrom($from, 'Save our Sunny Coast Submissions Portal')
			    ->setTo($to)
			    ->setSubject($subjectFileName);

			$email->send();


            $submissionThankyou = SiteConfig::current_site_config()->SubmissionThankyou;
            $submissionThankyou = str_replace('[name]', $submission->Name, $submissionThankyou);
            $submissionThankyou = str_replace('[email]', $submission->Email, $submissionThankyou);


            return $this->customise([
                'Title' => '',
                'Content' => DBField::create_field('HTMLText', $submissionThankyou),
                'Form' => DBField::create_field('HTMLText', '')
            ])->renderWith('Page');
            
        }

        public function verifySubmissionEmail() {

            $submittedHash = $this->request->Param('ID');
            if(!$submittedHash) {
                return $this->customise([
                    'Title' => 'Oops! Sorry we didnt recognise your verification link',
                    'Content' => DBField::create_field('HTMLText', <<<html
                        <p>Please go back to the email and click the link again. Let us know if this keeps happening</p>
html
),
                    'Form' => DBField::create_field('HTMLText', '')
                ])->renderWith('Page');

            }
            $submission = Submission::get()->find('Hash', $submittedHash);
            if(!$submission) {
                return $this->customise([
                    'Title' => 'Oops! Sorry we cant find your submission',
                    'Content' => DBField::create_field('HTMLText', <<<html
                        <p>Please go back to the email and click the link again. Let us know if this keeps happening</p>
html
),
                    'Form' => DBField::create_field('HTMLText', '')
                ])->renderWith('Page');

            }

            $submission->Verified = 1;
            $submission->write();
            
            $verificationThankyou = SiteConfig::current_site_config()->VerificationThankyou;
            $verificationThankyou = str_replace('[name]', $submission->Name, $verificationThankyou);
            $verificationThankyou = str_replace('[email]', $submission->Email, $verificationThankyou);

            return $this->customise([
                'Title' => 'Email Verified',
                'Content' => DBField::create_field('HTMLText', $verificationThankyou),
                'Form' => Form::create()
            ])->renderWith(['Page', 'Page']);

        }

        // public function FeedbackForm() {
        //     $submittedHash = $this->request->Param('ID');
        //     $submission = Submission::get()->find('Hash', $submittedHash);

        //     // die(print_r($submission,true));

        //     $fields = FieldList::create();
        //     if($submission) $fields->push(HiddenField::create('SubmissionID', '',  $submission->ID));
        //     $fields->push(OptionsetField::create(
        //         'Rating',
        //         'How do you rate our submissions portal?',
        //         [
        //             1 => "1 star - Super clunky, this thing needs a full rebuild",
        //             2 => "2 star - Quite average",
        //             3 => "3 star - A couple adjustments needed but pretty sweet",
        //             4 => "4 star - Not too shabby at all",
        //             5 => "5 star - This thing is running smooth!",
        //         ], 3
        //     ));
        //     $fields->push(TextareaField::create('Good', 'Positive feedback, please be specific so we can take your feedback on board.'));
        //     $fields->push(TextareaField::create('Bad', 'Stuff we need to improve, please be specific so we can take your feedback on board.'));

            
        //     $actions = FieldList::create();
        //     $actions->push($submit = FormAction::create('submitFeedback', 'Submit'));
        //     $submit->addExtraClass('btn btn-primary');

        //     $form = Form::create($this, 'FeedbackForm', $fields, $actions);
            
        //     return $form;
        // }


        // public function submitFeedback($data, $form) {
        //     // die(print_r($data,true));

        //     $feedback = Feedback::create();
        //     $feedback->update($data);
        //     $feedback->write();

        //     return $this->customise([
        //         'Title' => 'Thanks',
        //         'Content' => DBField::create_field('HTMLText', '<p>Thanks for your feedback</p>'),
        //         'Form' => DBField::create_field('HTMLText', '')
        //     ])->renderWith('Page');

        // }


        public function createPdfAndSend() {

            Requirements::clear();
            
            $pdf = new Dompdf();
            $pdf->output();
            $pdf->loadHTML($this->customise([
                'Data' => $data,
                'SubmissionData' => $this->dataRecord,
                'Signature' => base64_encode($signatureFile->Fit(400,240)->getString())
            ])->renderWith('PetitionPDF'));
            $pdf->render();
            $date = DBField::create_field('DBDatetime', date('d-m-Y'));

            // $pdf->stream('Petition_'.$date->format('y-M-d').'.pdf');

            $subjectFileName = $this->dataRecord->ApplicationReferenceNumber.' Form 13 submission by '.$data['Name'];


            $from = "rob@robertclarkson.net";
            $to = "robertclarkson@gmail.com";

            $email = Email::create()
                ->setHTMLTemplate('Email\\SubmissionEmail') 
                ->setData([
                    // 'Member' => Security::getCurrentUser(),
                    'Name'=> $data['Name'],
                ])
                ->setFrom($from)
                ->setTo($to)
                ->setSubject($subjectFileName);

            $email->addAttachmentFromData($pdf->output(), $subjectFileName.'.pdf', "application/pdf");
            $email->send();
            
            HTTPRequest::send_file($pdf->stream($subjectFileName.'.pdf'), $subjectFileName.'.pdf');


            // die(print_r($this->request,true));

            // die(print_r($this->request->postVar('sig'),true));
        } 
    }

}
