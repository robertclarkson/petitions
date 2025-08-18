<?php

namespace {

use SilverStripe\Control\Controller;
use SilverStripe\Control\Email\Email;
use SilverStripe\Core\Environment;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\CompositeField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\HiddenField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\Forms\SelectionGroup;
use SilverStripe\Forms\SelectionGroup_Item;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\Security\Security;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\View\Parsers\ShortcodeParser;
use SilverStripe\View\Requirements;

    class StripeController extends PageController
    {

        private static $allowed_actions = [
            'webhook',
        ];

        protected function init()
        {
            parent::init();
            
        }

    	public function index() {
    		return 'error';
    	}

		public function webhook() {
	        \Stripe\Stripe::setApiKey(Environment::getEnv('STRIPE_SECKEY'));

	        // Retrieve the request's body and parse it as JSON
	        $payload         = @file_get_contents("php://input");
	        $endpoint_secret = Environment::getEnv('STRIPE_WEBHOOK_SECRET');

	        if (!isset($_SERVER["HTTP_STRIPE_SIGNATURE"])) {
	            return $this->httpError(404);
	        }
	        $sig_header = $_SERVER["HTTP_STRIPE_SIGNATURE"];
	        $event      = null;
	        
	        try {
	            $event = \Stripe\Webhook::constructEvent(
	                $payload, $sig_header, $endpoint_secret
	            );
	        } catch (\UnexpectedValueException $e) {
	            // Invalid payload
	            http_response_code(400); // PHP 5.4 or greater
	            exit();
	        } catch (\Stripe\Error\SignatureVerification $e) {
	            // Invalid signature
	            http_response_code(400); // PHP 5.4 or greater
	            exit();
	        }

	        try {

		        if (isset($event)) {
				    $data = $event->data->object;
				    $livemode = $event->data->livemode;
				    $stripe = new \Stripe\StripeClient(
					  	Environment::getEnv('STRIPE_SECKEY')
					);

		        	switch($event->type) {
		        		case 'checkout.session.completed':
		        			if($data->currency == 'nzd') {
					        	
					        	$registrationHash = $data->metadata->registration;
					        	if(!$registrationHash) {
					        		// throw new Exception('No registration hash supplied');
					        		Security::permissionFailure();
					        	}
					        	$paymentintent = $stripe->paymentIntents->update(
								  	$data->payment_intent,
								  	['metadata' => ['registration' => $registrationHash]]
								);
					        	$reg = EventRegistration::get()->find('Hash', $registrationHash);
					        	if(!$reg) {
					        		throw new Exception('No such registration');
					        	}

					        	if($data->amount_total == $reg->TotalCost*100) {
					        		$existingPayment = StripePayment::get()->find('payment_intent', $data->payment_intent);
					        		if($existingPayment) die('This payment has already been processed');
					        		$payment = StripePayment::create();
					        		// print_r($data);
					        		$payment->Update($data->toArray());
					        		$payment->EventRegistrationID = $reg->ID;
					        		$payment->livemode = $livemode;
					        		$payment->write(); 
					        		
					        		$reg->Payments()->add($payment);
					        		$reg->PaymentStatus = 'Paid';
					        		$reg->write();

					        		//email to cutomer
					        		$from = SiteConfig::current_site_config()->AdminEmail;
							        $to = $reg->Email;

							        $emailContent = $reg->Event()->EventConfirmationEmail ?: SiteConfig::current_site_config()->EventConfirmationEmail;
							        $emailContent = str_replace('[name]', $reg->Name, $emailContent);
							        $emailContent = ShortcodeParser::get_active()->parse($emailContent);


							        $email = Email::create()
							            ->setHTMLTemplate('Email\\EventConfirmationEmail') 
									    ->setData([
									        'Content' => LiteralField::create('content', $emailContent),
									        'Event'=> $reg->Event(),
									        'Registration' => $reg
									    ])
							            ->setFrom($from)
							            ->setTo($to)
							            ->setSubject('[QMTBC] Your registration is now complete');


							        if($email->send()) {
							        	echo 'Email SENT<br/>';
							        }
					        		echo "Success";

					        	}
					        	else {
					        		throw new Exception('Incorrect payment amount');

					        	}

				        	}
				        	else {
					        	throw new Exception('Wrong currency');
				        	}
				        	break;
				        case 'charge.refunded':
				        	if($data->currency == 'nzd') {
								$paymentintent = $stripe->paymentIntents->retrieve(
								  	$data->payment_intent,
								  	[]
								);
				        		if($paymentintent) {
				        			$registrationHash = $paymentintent->metadata->registration;
						        	if(!$registrationHash) {
						        		// throw new Exception('No registration hash supplied');
						        		Security::permissionFailure();
						        	}
						        	$reg = EventRegistration::get()->find('Hash', $registrationHash);
						        	if(!$reg) {
						        		throw new Exception('No such registration');
						        	}
						        	$existingPayment = StripePayment::get()->find('charge_id', $data->id);
					        		if($existingPayment) die('This payment has already been processed');
					        		$payment = StripePayment::create();
					        		$payment->Update($data->toArray());
					        		$payment->charge_id = $data->id;
					        		$payment->customer_email = $data->billing_details->email;
					        		$payment->payment_status = 'refund';
					        		$payment->amount_total = $data->amount_refunded * -1;
					        		$payment->EventRegistrationID = $reg->ID;
					        		$payment->livemode = $livemode;
					        		$payment->write(); 
					        		
					        		$reg->Payments()->add($payment);
					        		$reg->PaymentStatus = 'Refunded';
					        		$reg->write();


				        		} else {
				        			throw new Exception('No such payment intent');
				        		}
				        	}
				        	break;
		        		default:
		        			die('default');
		        	}
		        }
		    }
		    catch (\Exception $e) {
		    	print_r($e);
		    }
        }

    }
}

