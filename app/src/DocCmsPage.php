<?php



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
use SilverStripe\ORM\FieldType\DBDate;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\View\Parsers\ShortcodeParser;
use SilverStripe\View\Requirements;

    class DocCmsPage extends PetitionPage {
    	private static $db = [
	        'OrganisationName' => 'Varchar(255)'
	    ];

        public function getCMSFields() {
            $fieldList = parent::getCMSFields();

            $fieldList->removeByName('Reasons');            
            $fieldList->removeByName('Decision');   

            $fieldList->removeByName('ApplicationReferenceNumber');   
            $fieldList->removeByName('ApplicantsName');   
            $fieldList->removeByName('ApplicationDetails');   
            $fieldList->removeByName('ApplicationLocation');   
            $fieldList->removeByName('DefaultSupportPosition');   

            $fieldList->addFieldToTab('Root.PetitionDetails', EmailField::create('CcTo'));

            return $fieldList;
        }

    }


    class DocCmsPageController extends PetitionPageController
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
        	$form = parent::Form();

        	$fields = FieldList::create();

            $fields->push(HiddenField::create('SubmissionType', '', 'DocSubmission'));
            $fields->push(HeaderField::create('h2', 'Your Details'));
            $fields->push(TextField::create('Name'));
            $fields->push(TextField::create('OrganisationName', 'Organisation Name, if submitting on behalf of your own organisation or buisiness (not QMTBC)'));
            $fields->push(TextField::create('Phone'));
            $fields->push(TextField::create('Email'));
            $fields->push(TextField::create('AddressLine1', 'Postal Address Line 1'));
            $fields->push(TextField::create('AddressLine2', 'Postal Address Line 2'));
            $fields->push(TextField::create('Suburb'));
            $fields->push(TextField::create('City'));
            $fields->push(TextField::create('Postcode'));


            $fields->push($heardField = DropdownField::create('Heard', 'Do you wish to be heard in support of your submission?', [
                'Heard' => 'I wish to be heard in support of my submission', 
                'Not Heard' => 'I do not wish to be heard in support of my submission'
            ])->setEmptyString('Please select...'));
            $heardField->LeftTitle = 'If you select that you would like to be heard, DOC will get in contact and ask you to come speak in person on your submission. It is definately beneficial to speak on behalf of your submission.';

            $fields->push($submissionIs = TextareaField::create('MySubmissionIs'));
            $submissionIs->LeftTitle = 'Here is where we would like you to talk about what you support or object to in relation to the CMS draft. We have included a template to help, but please, delete or adapt as you see fit.';

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

            $fields->push(LiteralField::create('loadingicon', <<<html
                <div class="cycle-loading-box" style="text-align:center; width:400px; padding: 20px 0; border: solid 1px #ccc; margin:20px 0">
                    <img class="cycle-loading"  src="{ThemeResourceLoader::themedResourceURL('/images/cycle-loading.gif')}" />
                    <p>Please wait, loading...</p>
                </div>
html
            ));

            $form->setFields($fields);
            $fields->setValues($this->dataRecord->toMap());
            if(Director::isLive()) $form->enableSpamProtection(array(
               'insertBefore' => 'FieldName'
            ));
            return $form;
        }
    }
