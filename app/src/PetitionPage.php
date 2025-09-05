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
use Dynamic\CountryDropdownField\Fields\CountryDropdownField;

    class PetitionPage extends Page {

    	private static $db = [
	        'Title' => 'Varchar(255)',
	        'ApplicationReferenceNumber' => 'Varchar(255)',
	        'ApplicantsName' => 'Varchar(255)',
	        'ApplicationDetails' => 'Text',
	        'ApplicationLocation' => 'Varchar(255)',
	        'ClosingDate' => 'Datetime',

	        'MySubmissionIs' => 'Text',
            'Reasons' => 'Text',
            'Decision' => 'Text',

            'EmailTo' => 'Varchar(255)',
            'CcTo' => 'Varchar(255)',

            'ConfirmationEmail' => 'HTMLText',
            'SubmissionEmail' => 'HTMLText',

            
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
            $fields->push(TextField::create('Firstname'));
            $fields->push(TextField::create('Surname'));
            //19 or under, 20-29, 30-39, 40-49, 50-59, 60-69, 70-79, 80-89, 90-99, 100+
            $fields->push(DropdownField::create('AgeRange', 'Age Range')
                ->setSource(singleton('Submission')->dbObject('AgeRange')->enumValues())
                ->setEmptyString('Please select...')
            );
            $fields->push(TextField::create('Phone'));
            $fields->push(TextField::create('Email'));
            $fields->push(TextField::create('AddressLine1', 'Residential Address Line 1'));
            $fields->push(TextField::create('AddressLine2', 'Residential Address Line 2'));
            $fields->push(TextField::create('Suburb'));
            $fields->push(TextField::create('City'));
            $fields->push(TextField::create('Postcode'));
            $fields->push(CountryDropdownField::create('Country')->setValue('au'));

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
            $fields->push(CheckboxField::create('Recreate', 'I recreate on the Sunshine Coast'));
            $fields->push(TextareaField::create('Other', 'Other grounds for submission, please specify'));

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
            $fields->push($submissionIs = TextareaField::create('MySubmissionIs', 'My submission is')->setValue($this->MySubmissionIs));
            $submissionIs->LeftTitle = "We've pre-filled a submission for you to use. If you want to use it as a starting point, feel free to do so, but this is your submission, so if you want to alter or add to it, please go ahead.";

            //create an optionset with the choices in it
            $fields->push($submissionOptions = OptionsetField::create('Beerwah', 'Beerwah East SEQ Development Area', [
                'oppose' => 'I strongly oppose the proposal to develop this area and strongly insist that this area is preserved in perpetuity as undeveloped natural space, revegetated with native plants and used for the community\'s recreation and natural environment. This aligns with our Vision for the coast, and themes in the proposed planning scheme. I absolutely oppose continuing the urban sprawl consuming the coast and this would exacerbate that problem.',
                'support' => 'I strongly support the proposed development in this area.',
                'abstain' => 'I neither support nor oppose the proposal.',
            ])->setValue('oppose')->setLeftTitle('The proposed Beerwah East SEQ Development area is a 5200 hectare area (about 7,500 football fields) that stretches from Beerwah, through to Caloundra. The current proposal is that this area is zoned for development, creating enormous urban sprawl that demolishes a huge portion of our open accessible space on the foot of the Glasshouse Mountains, and will NEVER be undone - once this is done, it is lost FOREVER.'));

            $fields->push($submissionOptions = OptionsetField::create('Revegetation', 'Revegetation and reforestation', [
                'oppose' => 'I strongly oppose setting firm targets for reforestation and revegetation of our native forests and bushland across the Coast.',
                'support' => 'I strongly support setting firm targets in the order of 70% of land area being regenerated into native forest and bushland cover across the Sunshine Coast region, with this target to be reflected directly in and facilitated by the Planning Scheme, as a priority. This should form the basis of our Planning Scheme and the questions of how we manage the remaining 30% area to facilitate housing, agriculture & forestry, technology & commerce, infrastructure, educational faciltiies, cleared recreation areas and other uses can then be addressed off the back of that. The proposed plan includes some great vision statements such as "the most sustainable region in Australia" and "the region\'s outstanding biodiversity, natural assets and landscapes, including the Blackall Range and Glass House Mountains, beaches, headlands, coastal plains, waterways and wetlands are protected and enhanced and remain undiminished by development." however it monumentally fails to deliver on that promise. In fact it does the opposite, and this is completely unacceptable.',
                'abstain' => 'I neither support nor oppose the proposal.',
            ])->setValue('support')->setLeftTitle('Across SEQ we have lost between 65-70 of our bushland. This level of deforestation is on the threshold of creating a non-functional ecosystem and the proposed plan does little to lead the way as it claims. We believe our community would love to see genuine and ambitious commitment to re-green the Coast. The proposed scheme includes some great elements of "Vision" however the reality is it does not deliver on that Vision to be the national leader in sustainability and conservation.'));
            
            $fields->push($submissionOptions = OptionsetField::create('Sustainability', 'Sustainability', [
                'oppose' => "I strongly oppose setting clear definitions around sustainability and genuinely ensuring the plan delivers to the sustainability Vision it claims.",
                'support' => "I strongly support setting clear definitions of sustainability, and to ensure the plan literally delivers the Vision of being the most sustainable region in Australia. I note that ongoing 'growth', development and bulldozing of our limited land area is, by definition, not sustainable! I propose we allow further growth or development once we have demonstrated we can be fully sustainable at our current scale of development and population. Sustainability will not get easier by cramming more people in, it will be harder and I do not accept 'greenwashing' of a plan that does not deliver true sustainability.",
                'abstain' => 'I neither support nor oppose the proposal.',
            ])->setValue('support')->setLeftTitle("The Proposed Scheme includes a great Vision that we believe reflects the will of the people; 'to make this the most sustainable region in Australia'. However, the Scheme absolutely fails to deliver on that and attempts to 'greenwash' the plan with fluffy words."));

            $fields->push($submissionOptions = OptionsetField::create('Traffic', 'Traffic & Transport', [
                'oppose' => 'I strongly oppose continued development and densification across the Coast due to the already existing traffic congestion and parking issues. I strongly object to ever-widening and more congeted roads, bringing us closer and closer to what we see in other areas of SE Qld and eroding our quality of life. This is a fundamental consideration in our quality of life and is already unacceptably poor, with any further development compounding the issues or requiring ever larger transport infrastructure, which is directly at odds with the natural character of the Coast. ',
                'support' => 'I strongly support continued development, increased population density and the traffic and parking congestion and larger roads that will inevitably come with this.',
                'abstain' => 'I neither support nor oppose the proposal.',
            ])->setValue('oppose')->setLeftTitle("We've all felt the ever increasing traffic congestion, ever-widening roads and painful parking situation. "));
            
            $fields->push($submissionOptions = OptionsetField::create('Mooloolah', 'Mooloolah River Catchment Area', [
                'oppose' => "I strongly oppose any further development of the Mooloolah River catchment area and strongly request that the native vegetation is expanded substantially and protected across both the north and south banks of the river (from around Brightwater and the Sunshine Coast Hospital, through to Palmview), and south through Meridan Plains. This river is unique and special within our Council area. The National Park should be extended across publicly owned lands and zoning of the other areas should retain it's rural character. All efforts should be made to expand the natrual habitat around this fragile ecosystem which only has a small slither remaining and has seen ever encroaching development. There is also potential here for open green space and recreation, however we should ensure this retains its natural character. I do not want this area developed with the typical developer-designed 'green space'.",
                'support' => 'I strongly support development of the Mooloolah River catchment area. I want to see ongoing development encroaching the National Park and consuming the historic farmlands on both the north and south banks of the river.',
                'abstain' => 'I neither support nor oppose the proposal.',
            ])->setValue('oppose')->setLeftTitle('The Mooloolah river is an incredible natural environment, home to incredible native flora and fauna, however the past 20 years has seen enormous destruction of the natural environment along the mid to upper reaches of the Mooloolah River with development consuming more and more of this incredible area. We believe it needs to be protected.'));
            
            $fields->push($submissionOptions = OptionsetField::create('MaroochydoreHeight', 'Maroochydore - unlimited height development', [
                'oppose' => 'I strongly oppose the unlimited height development zoning the State Government has forced upon us and our community. This is at odds with the nature of and our Vision for the Coast, and I ask that the Sunshine Coast Council and our elected members pursue this with State Government to correct their poorly considered zoning. We can innovate and create a prosperous economy and healthy community without this.',
                'support' => 'I strongly support the unlimited height development in zoning Maroochydore.',
                'abstain' => 'I neither support nor oppose the proposal.',
            ])->setValue('oppose')->setLeftTitle("Our people made it clear, we don't want to be the Gold Coast. The Qld State Government has already forced through zoning of central Maroochydore to ‘UNLIMITED HEIGHT DEVELOPMENT’. That’s right, we are talking skyscrapers turning us into the next Surfers Paradise. We believe this choice was fundamentally at odds with the will of the people. This entirely changes the nature of the Coast and we believe our people want this 'undone'."));
            
            $fields->push($submissionOptions = OptionsetField::create('Caloundra', 'Densification from Caloundra to Kawana', [
                'oppose' => 'I strongly oppose rezoning of the various sections between Calounda and Kawana to increase density and drive unit development. This is absolutely out of character with this area and our ‘community of communities’ ethos on the Sunshine Coast.',
                'support' => 'I strongly support the proposed densification from Caloundra to Kawana.',
                'abstain' => 'I neither support nor oppose the proposal.',
            ])->setValue('oppose')->setLeftTitle('The scheme proposes that much of the coast between Caloundra and Kawana is rezoned in order that this section is transformed to much higher density, replacing houses with up to 4-6 story apartment blocks. This rezoning has a host of impacts for residents in the area and for others on the Coast. Massively increased density, similar to Gold Coast means all the congestion that comes along with that; not only on this part of the coast, but all the spillover congestion Long term residents may be forced out of their family homes by increased rates and rebuilding a family home when it ages will literally not be allowed - the zone only allows the higher density development such as 4-6 story unit blocks.'));
            
            $fields->push($submissionOptions = OptionsetField::create('Recreation', 'Recreation areas and open spaces', [
                'oppose' => 'I strongly oppose any further loss of outdoor recreation spaces, facilities or amenities our communities value. In many cases, open mindedness and respect for our various community groups, sub-cultures and history would result in much better community outcomes. I request that the use of public land be made available to reestablish any lost facilities or space for the various groups who call this place home and who will be the custodians of this public land into the future.',
                'support' => 'I strongly support the ongoing loss of outdoor recreation spaces, facilities and amenities in order to accommodate development.',
                'abstain' => 'I neither support nor oppose the proposal.',
            ])->setValue('oppose')->setLeftTitle('As the Coast has been consumed by land clearing and construction, Government has removed community amenities in favour of development and concreting of our natural open spaces. In some cases these are established clubs or facilities and conscious decisions have been made to get rid of them, while in other cases, they are just places the locals know and love being destroyed by a Government who may not understand the nuances of our local places. We believe we need to place a priority on retaining the open, outdoor, recreation spaces we love and from that, great economic outcomes and a healthy, thriving community will follow.'));
            
            $fields->push($submissionOptions = OptionsetField::create('MaroochydoreDevelopment', 'Maroochydore development', [
                'oppose' => 'I strongly oppose the changes to increased heights and densities in Maroochydore. This further exacerbates the already problematic traffic and parking, creates further congestion and spillover effects. Modest development may be acceptable, however this plan goes much too far, and also proposes rezoning outside of the CBD which I strongly object to.',
                'support' => 'I strongly support the proposed increases to heights and densities in Maroochydore.',
                'abstain' => 'I neither support nor oppose the proposal.',
            ])->setValue('oppose')->setLeftTitle('While Maroochydore has evolved to be one of the main centres on the Coast, the proposal pushes for substantially increased heights and densities, which also spillover outside of the CBD.'));
            
            $fields->push($submissionOptions = OptionsetField::create('Alexandra', 'Alexandra Headland Development', [
                'oppose' => 'I strongly oppose the changes to any increased heights and densities in Alexandra Headland, including the area off Mari Street. This further exacerbates the already problematic traffic and parking, creates further congestion and spillover effects and continues to erode the character of the Alexandra Headland area.',
                'support' => 'I strongly support the proposed increases to heights and densities in Alexandra Headland.',
                'abstain' => 'I neither support nor oppose the proposal.',
            ])->setValue('oppose')->setLeftTitle('Alexandra Headland has a unique character along this stretch of coast, with a balanced mix of limited highrise, residential areas, and open space. The Proposed Scheme intends to substantially increase density and heights and lose open green space from Alexandra Headland, further congesting this area.'));
            
            $fields->push(HeaderField::create('h2', 'Signature'));
            $fields->push(HiddenField::create('sig'));
            $fields->push(LiteralField::create('signaturecode', <<<html
  				<div id="signature-pad" class="signature-pad">
	                <div class="signature-pad--body">
	                    <canvas width="664" height="223" style="touch-action: none;"></canvas>
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
                'Firstname',
                'Surname',
                'AgeRange',
                'Email',
                'AddressLine1',
                'Suburb',
                'Postcode',
                'Submission',
                'Heard',
                'MySubmissionIs',
                'Reasons',
                'Decision',
                'Beerwah'
            ]);
            $form = Form::create($this, 'form', $fields, $actions, $required);
            
            // print_r($form->fields()->dataFieldByName('Name')->value());
            // if($form->fields()->dataFieldByName('Name')->value() == '') {
            $fields->setValues($this->dataRecord->toMap());
            // }
            // if(Director::isLive()) $form->enableSpamProtection(array(
            //    'insertBefore' => 'FieldName'
            // ));
            $form->enableSpamProtection()
                ->Fields()
                ->fieldByName('Captcha')
                ->setTitle('Please complete the spam protection');
            return $form;

        }

        public function submit($data, $form) {

            if(!isset($data['Business']) && 
                (!isset($data['Other']) || $data['Other'] == "") && 
                !isset($data['Recreate']) && 
                !isset($data['Work']) && 
                !isset($data['Resident'])) {
                    $form->sessionError('Please select at least one grounds for submission.', 'Grounds');
                    $form->setFieldMessage('Other','Please select at least one grounds for submission.', 'error');
                    return $this->customise([
                        'Form' => $form
                    ])->renderWith('Page');
            }

        	$submission = Submission::create();
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

            $subjectFileName = 'Please verify your submission by '.$data['Firstname'].' '.$data['Surname'];

			$from = SiteConfig::current_site_config()->AdminEmail;
			$to = $submission->Email;

            $emailContent = $this->dataRecord->ConfirmationEmail;
            $emailContent = str_replace('[name]', $submission->Firstname, $emailContent);
            $emailContent = str_replace('[link]', Controller::join_links($this->AbsoluteLink(),'verifySubmissionEmail',$submission->Hash), $emailContent);
            $emailContent = ShortcodeParser::get_active()->parse($emailContent);

			$email = Email::create()
			    ->setBody($emailContent)
			    ->setFrom($from, 'Save Our Sunny Coast Submissions Portal')
			    ->setTo($to)
			    ->setSubject($subjectFileName);

			$email->send();


            $submissionThankyou = SiteConfig::current_site_config()->SubmissionThankyou;
            $submissionThankyou = str_replace('[name]', $submission->Firstname, $submissionThankyou);
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
            $verificationThankyou = str_replace('[name]', $submission->Name(), $verificationThankyou);
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


        public function createPdfAndSend($data) {

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

            $subjectFileName = $this->dataRecord->ApplicationReferenceNumber.' Form 13 submission by '.$data['Firstname'].' '.$data['Surname'];


            $from = "rob@robertclarkson.net";
            $to = "robertclarkson@gmail.com";

            $email = Email::create()
                ->setHTMLTemplate('Email\\SubmissionEmail') 
                ->setData([
                    // 'Member' => Security::getCurrentUser(),
                    'Name'=> $data['Firstname'].' '.$data['Surname'],
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
