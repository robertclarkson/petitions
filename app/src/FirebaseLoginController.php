<?php


use SilverStripe\CMS\Controllers\ContentController;
use SilverStripe\Control\Controller;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\ORM\DataObject;
use SilverStripe\Security\IdentityStore;
use SilverStripe\Security\Member;
use SilverStripe\View\Requirements;
use SilverStripe\View\TemplateGlobalProvider;

class FirebaseLoginController extends PageController implements TemplateGlobalProvider
{
    /**
     * An array of actions that can be accessed via a request. Each array element should be an action name, and the
     * permissions or conditions required to allow the user to access it.
     *
     * <code>
     * [
     *     'action', // anyone can access this action
     *     'action' => true, // same as above
     *     'action' => 'ADMIN', // you must have ADMIN permissions to access this action
     *     'action' => '->checkAction' // you can only access this action if $this->checkAction() returns true
     * ];
     * </code>
     *
     * @var array
     */
    private static $allowed_actions = [
        'loginResult',
        'firebaseLogout'
    ];

    protected function init()
    {
        parent::init();

    }

    public function index() {
        return $this->renderWith(['FirebaseLogin','Page']);
    }

    public function loginResult() {
        $fbtoken = $this->request->Param('ID');
        if(!$fbtoken) {
            throw new Exception('No Firebase token specified');
        }

        // https://us-central1-qmtbc-dev.cloudfunctions.net/getMemberDataWithToken
        //tenantCode
        //idToken

        $client = new GuzzleHttp\Client();
        $res = $client->request('POST', 'https://us-central1-qmtbc-dev.cloudfunctions.net/getMemberDataWithToken', [
            'headers' => ['Content-Type' => 'application/json', 'Accept' => 'application/json'], 
            'body' => json_encode([
                'tenantCode' => 'qmtbc',
                'idToken' => $fbtoken
            ])
        ]);
        // echo '<pre>'.$res->getStatusCode();
        // "200"
        // 'application/json; charset=utf8'
        $responseArr = json_decode($res->getBody(),true);
        // print_r($responseArr);

        $session = $this->request->getSession();
        $session->set('firebase', $responseArr['member']);

        $member = Member::get()->find('firebaseid', $responseArr['member']['id']);
        if(!$member) {
            $member = new Member();
            $member->firebaseid = $responseArr['member']['id'];
        }
        $member->update($responseArr['member']);
        $member->Email = $responseArr['member']['email'];
        $member->FirstName = $responseArr['member']['firstName'];
        $member->Surname = $responseArr['member']['lastName'];
        if($responseArr['member']['postalAddress']){
            $member->addressLine1 = $responseArr['member']['postalAddress']['addressLine1'];
            $member->addressLine2 = $responseArr['member']['postalAddress']['addressLine2'];
            $member->city = $responseArr['member']['postalAddress']['city'];
            $member->country = $responseArr['member']['postalAddress']['country'];
            $member->postcode = $responseArr['member']['postalAddress']['postcode'];
        }
        $member->write();
        
        Injector::inst()->get(IdentityStore::class)->logIn($member, true, $this->request);

        $this->getResponse()->addHeader('Content-type', 'application/json');
        return json_encode($responseArr['member']);
        // return $this->redirectBack();
    }


    public function firebaseLogout() {
        $session = $this->request->getSession();
        $session->clear('firebase');
        $this->redirectBack();
    }

    /**
     * @return null|Member
     */
    public static function getCurrentFirebaseMember()
    {
        $session = Controller::curr()->request->getSession();
        $fbmember = $session->get('firebase');
        if(!$fbmember) return false;
        return (new DataObject())->update($fbmember)->setField('ID', $fbmember['id']);
    }

    /**
     * Defines global accessible templates variables.
     *
     * @return array
     */
    public static function get_template_global_variables()
    {
        return [
            "CurrentFirebaseMember" => "getCurrentFirebaseMember",
        ];
    }

}
