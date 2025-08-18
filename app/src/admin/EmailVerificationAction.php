<?php

use Dompdf\Dompdf;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Email\Email;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridField_ActionMenuItem;
use SilverStripe\Forms\GridField\GridField_ActionProvider;
use SilverStripe\Forms\GridField\GridField_ColumnProvider;
use SilverStripe\Forms\GridField\GridField_FormAction;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\ORM\ValidationException;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\View\ArrayData;
use SilverStripe\View\Parsers\ShortcodeParser;

class EmailVerificationAction implements
    GridField_ColumnProvider,
    GridField_ActionProvider,
    GridField_ActionMenuItem
{
    /**
     * {@inheritdoc}
     */
    public function augmentColumns($gridField, &$columns)
    {
        if (!in_array('Actions', $columns)) {
            $columns[] = 'Actions';
        }
    }

    public function getTitle($gridField, $record, $columnName)
    {
        return _t(__CLASS__ . '.RESEND', 'Resend Verification Email');
    }

    public function getExtraData($gridField, $record, $columnName)
    {

        $field = $this->getApproveAction($gridField, $record, $columnName);

        if ($field) {
            return $field->getAttributes();
        }

        return null;
    }
    public function getGroup($gridField, $record, $columnName)
    {
        $field = $this->getApproveAction($gridField, $record, $columnName);

        return $field ? GridField_ActionMenuItem::DEFAULT_GROUP: null;
    }

    /**
     * {@inheritdoc}
     */
    public function getColumnAttributes($gridField, $record, $columnName)
    {
        return ['class' => 'col-buttons grid-field__col-compact'];
    }

    /**
     * {@inheritdoc}
     */
    public function getColumnMetadata($gridField, $columnName)
    {
        if ($columnName === 'Actions') {
            return ['title' => ''];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getColumnsHandled($gridField)
    {
        return ['Actions'];
    }

    /**
     * {@inheritdoc}
     */
    public function getColumnContent($gridField, $record, $columnName)
    {
        if (!$record->canEdit()) {
            return;
        }

        $field = $this->getApproveAction($gridField, $record, $columnName);

        return $field ? $field->Field() : null;
    }

    /**
     * Returns the FormAction object, used by other methods to get properties
     *
     * @return GridField_FormAction|null
     */
    public function getApproveAction($gridField, $record, $columnName)
    {
        $field = GridField_FormAction::create(
            $gridField,
            'CustomAction' . $record->ID . 'Resend Verification Email',
            _t(__CLASS__ . '.APPROVE', 'Resend Verification Email'),
            'resendverification',
            ['RecordID' => $record->ID]
        )
            ->addExtraClass(implode(' ', [
                'btn',
                'btn-secondary',
                'grid-field__icon-action',
                'action-menu--handled',
                'font-icon-block-email',
            ]))
            ->setAttribute('classNames', 'font-icon-p-mail');

        return $field;//($record->Submitted == 0) ? $field : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getActions($gridField)
    {
        return ['resendverification'];
    }

    /**
     * {@inheritdoc}
     */
    public function handleAction(GridField $gridField, $actionName, $arguments, $data)
    {
        $submission = Submission::get()->byID($arguments['RecordID']);
        if(!$submission) {
            Controller::curr()->getResponse()->setStatusCode(
                400,
                "Submission does not exist."
            );
            return Controller::curr()->redirect('admin/submissions');
        }

        $petition = $submission->PetitionPage();
        if(!$petition) {
            Controller::curr()->getResponse()->setStatusCode(
                400,
                'Submission is not attached to a petition page.'
            );
            return Controller::curr()->redirect('admin/submissions');
        }

        $subjectFileName = 'Please verify your submission by '.$submission->Name;

        $from = SiteConfig::current_site_config()->AdminEmail;
        $to = $submission->Email;

        $emailContent = $petition->ConfirmationEmail;
        $emailContent = str_replace('[name]', $submission->Name, $emailContent);
        $emailContent = str_replace('[link]', Controller::join_links($submission->PetitionPage()->AbsoluteLink(),'verifySubmissionEmail',$submission->Hash), $emailContent);
        $emailContent = ShortcodeParser::get_active()->parse($emailContent);


        $email = Email::create()
            ->setBody($emailContent)
            ->setFrom($from, 'QMTBC Submissions Portal')
            ->setTo($to)
            ->setSubject($subjectFileName);

        $email->send();
        //refresh the page
        Controller::curr()->redirect('admin/submissions');
        // output a success message to the user
        Controller::curr()->getResponse()->setStatusCode(
            200,
            'Submission Verification Resent.'
        );
    }
}
