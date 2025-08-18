<?php

namespace {

use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\Core\Environment;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\CompositeField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\HiddenField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\Forms\SelectionGroup;
use SilverStripe\Forms\SelectionGroup_Item;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\ORM\ValidationResult;
use SilverStripe\Security\Member;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\View\Requirements;

    class EventSignupController extends PageController
    {

        private static $allowed_actions = [
            'signup',
            'EventForm',
            'payment',
            'submit',
            'createCheckoutSession',
            'success',
            'cancelled',
        ];

        protected function init()
        {
            parent::init();
            
        }

    	public function index() {

    	}

        // public function MetaComponents()
        // {
        //     $tags = [];

        //     $tags['title'] = [
        //         'tag' => 'title',
        //         'content' => $this->Event()->Title
        //     ];

        //     return $tags;
        // }

        public function Title() {
            // die(print_r($this->getRequest(),true));
            if ($this->getRequest()) {
                $eventhash = $this->getRequest()->Param('ID');
                if ($eventhash) {
                    return 'Event Registration for '.Event::get()->find('hash', $eventhash)->Title;
                }
            }
        }

        public function Event() {
            $eventhash = $this->getRequest()->Param('ID');
            return Event::get()->find('hash', $eventhash);
        }

        public function signup() {
            $eventhash = $this->request->Param('ID');
            $event = Event::get()->find('hash', $eventhash);
            if($event) {
                return $this->renderWith(['EventSignup','Page']);

            }
            else {
                die('cant find this event');
            }

        }

        public function EventForm() {
            // die(print_r($this->getRequest()->postVars(),true));
            $postVars = $this->getRequest()->postVars();

            $registerFields = singleton('EventRegistration')->getFrontendFields();
            if($member = Member::currentUser()) {
                $registerFields->dataFieldByName('Name')->setValue($member->Name)->setReadonly(true);
                $registerFields->dataFieldByName('Email')->setValue($member->Email)->setReadonly(true);
            }
            $registerFields->push($hiddenEventID = HiddenField::create('EventID'));
            if(isset($postVars['EventID'])) {
                $hiddenEventID->setValue($postVars['EventID']);
            }
            else {
                $hiddenEventID->setValue($this->Event()->ID);
            }


            $registerFields->replaceField(
                'Age', 
                NumericField::create('Age')
                    ->setTitle('Age on event day')
                    ->setHTML5(true)
                    ->setAttribute('step', 1)
                    ->setAttribute('min', 0)
            );

            $registerFields->dataFieldByName('ParentName')->setTitle('Name of parent or guardian who approves this activity');

            $registerFields->removeByName('AddOns');
            $registerFields->removeByName('ActiveMember');
            $registerFields->removeByName('MemberID');
            $registerFields->removeByName('MembershipNumber');
            $registerFields->removeByName('SportIdentCategoryID');

            

            $requiredArray = [
                'Name', 'Email', 'Phone', 'Age', 'Sex', 'EmergencyContactName', 'EmergencyContactPhone', 'WaiverAgreement', 'WaiverAgreementParent'
            ];
            if($member) {
                $requiredArray = array_diff( $requiredArray, ['Name', 'Email'] );
            }

            
            $event = $this->Event();
            if ($event) {

                if($event->SportIdentCategories()->count()){   
                    $registerFields->insertAfter('Sex', $catField = DropdownField::create('SportIdentCategory', 'Category', $this->Event()->SportIdentCategories()->map('ID', 'Title')));
                    $catField->setEmptyString('Please Select...');
                    $requiredArray[] = 'SportIdentCategory';
                }


                $registerFields->insertAfter('WaiverAgreement', LiteralField::create('waivertext', '<blockquote>'.$event->EventWaiver.'</blockquote>'));
                $registerFields->insertAfter('WaiverAgreementParent', LiteralField::create('waivertext', '<blockquote>'.$event->EventParentalWaiver.'</blockquote>'));


                if($member = Member::currentUser() && $member->isActive) {
                    $registerFields->insertBefore('Name', LiteralField::create('Member', '<div class="alert alert-success">You are an active club member so your event price is $'.$event->MemberPrice.'</div>'));
                }
                else {
                    $registerFields->insertBefore('Name', LiteralField::create('Member', '<div class="alert alert-warning">You are not an active club member so your event price is $'.$event->NonMemberPrice.'. Or you could <a target="_new" href="https://www.queenstownmtb.co.nz/membership">become a member</a></div>'));

                }

                foreach($event->AddOns() as $addon) {
                    $optionalAddons = CompositeField::create();
                    $optionalAddons->push($option = CheckboxField::create('AddOns['.$addon->URLSegment.']', $addon->Title.' (Additional $'.$addon->Cost.')'));
                    $option->setRightTitle(DBField::create_field('HTMLText', $addon->Content));
                    $option->setAttribute('data-cost', $addon->Cost);
                    $registerFields->insertAfter('EmergencyContactPhone', $optionalAddons);
                    
                    

                    foreach($event->EditableFormFields() as $editableField) {
                        $registerFields->insertAfter('EmergencyContactPhone', $editableField->getFormField());
                        if(isset($editableField->Required)) {
                            $requiredArray[] = $editableField->getFormField()->Name;
                        }
                    }
                }
            }
            $required = new RequiredFields($requiredArray);

            $actionFields = FieldList::create(
                FormAction::create('submit', 'Register Now')->addExtraClass('btn btn-primary')
            );

            $form = Form::create($this, 'EventForm', $registerFields, $actionFields, $required);
            // $form->addExtraClass('row');
            return $form;
        }

        public function submit($data, $form) {
            if($data['Age'] < 18 && (!isset($data['ParentName']) || !$data['ParentName'])) {
                $validationResult = new ValidationResult();
                $validationResult->addFieldError('ParentName', 'Please enter the name of the parent');
                $form->setSessionValidationResult($validationResult);
                $form->setSessionData($form->getData());
                return $this->redirectBack();
            }

            $event = Event::get()->byID($data['EventID']);

            $registration = EventRegistration::create();
            $registration->update($data);
            
            $questionsArray = [];
            // die(print_r($event->EditableFormFields()->column(),true));
            foreach($event->EditableFormFields() as $field) {
                $questionsArray[] = [$field->Title => isset($data[$field->Name]) ? $data[$field->Name] : null];
            }
            $registration->Questions = json_encode($questionsArray);

            $cost = 0;

            if(isset($data['AddOns']) && is_array($data['AddOns'])){
                $addons = [];
                foreach($data['AddOns'] as $key => $val) {
                    $addonCost = $event->AddOns()->find('URLSegment', $key)->Cost;
                    $addons[] = [
                        'name' => $key,
                        'value' => $val,
                        'cost' => $addonCost
                    ];
                    if($val) $cost += $addonCost;
                }
                $registration->AddOns = json_encode($addons);
            } 
            $member = Member::currentUser();
            if($member && $member->isActive) {
                $cost += $event->MemberPrice;
                $registration->RegistrationCost = $event->MemberPrice;
                $registration->ActiveMember = 1;
            }
            else {
                $cost += $event->NonMemberPrice;
                $registration->RegistrationCost = $event->NonMemberPrice;
            }
            $registration->TotalCost = $cost;
            if($member) {
                $registration->MemberID = $member->ID;
            }

            $registration->write();

            return $this->redirect(Controller::join_links($this->Link(), 'payment', $registration->Hash));
            // die(print_r($data,true));

        }

        public function payment() {
            $registration = EventRegistration::get()->find('Hash', $this->getRequest()->Param('ID'));
            $event = $registration->Event();
            Requirements::Javascript('https://polyfill.io/v3/polyfill.min.js?version=3.52.1&features=fetch');
            Requirements::Javascript('https://js.stripe.com/v3/');
            
            return $this->customise([
                'Title' => 'Event Payment',
                'Event' => $event,
                'Registration' => $registration,
                'StripePubkey' => Environment::getEnv('STRIPE_PUBKEY'),
                'PaymentLink' => Controller::join_links($this->Link(), 'createCheckoutSession', $this->getRequest()->Param('ID'))
            ])->renderWith(['EventPayment', 'Page']);
        }

        public function createCheckoutSession() {

            $registration = EventRegistration::get()->find('Hash', $this->getRequest()->Param('ID'));
            if(!$registration) {
                die('no such registration');
            }
            $event = $registration->Event();

            $siteConfig = SiteConfig::current_site_config();

            \Stripe\Stripe::setApiKey(Environment::getEnv('STRIPE_SECKEY'));
            header('Content-Type: application/json');
            $checkout_session = \Stripe\Checkout\Session::create([
              'customer_email' => $registration->Email,
              "metadata" => [
                'registration' => $this->getRequest()->Param('ID'),
              ],
              'payment_method_types' => ['card'],
              'line_items' => [[
                'price_data' => [
                  'currency' => 'nzd',
                  'unit_amount' => $registration->TotalCost*100,
                  'product_data' => [
                    'name' => $event->Title.' Registration',
                    'images' => [$siteConfig->LogoBlack()->AbsoluteLink()],
                  ],
                ],
                'quantity' => 1,
              ]],
              'mode' => 'payment',
              'success_url' => Controller::join_links($this->AbsoluteLink(),'success'),
              'cancel_url' => Controller::join_links($this->AbsoluteLink(),'cancelled'),
            ]);
            echo json_encode(['id' => $checkout_session->id]);
        }

        public function success() {
            return $this->customise([
                'Title' => 'Payment Processing...',
                'Content' => DBField::create_field('HTMLText', '<h3>Please check your inbox now</h3> <p>The payment confirmation has been emailed to you (check spambox as well) If you do not get the email in the next 5 mins please get in touch with us as there might have been a problem.<br /><br /> Thank you for your registration.</p>'),
                'Form' => null,
            ])->renderWith(['Page']);
        }

        public function cancelled() {
            return $this->customise([
                'Title' => 'Payment Cancelled',
                'Content' => DBField::create_field('HTMLText', 'The payment process has been cancelled.<br /><br /> You are not registered for the event.'),
                'Form' => null,
            ])->renderWith(['Page']);
        }

        public function Link($action = NULL) {
            return Controller::join_links('event-registration');
        }

        public function AbsoluteLink($action = null)
        {
            return Director::absoluteURL($this->Link($action));
        }
    }
}
