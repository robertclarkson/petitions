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

class EmailSubmissionAction implements
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
        return _t(__CLASS__ . '.APPROVE', 'Send Submission');
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
            'CustomAction' . $record->ID . 'Send Submission',
            _t(__CLASS__ . '.APPROVE', 'Send Submission'),
            'approve',
            ['RecordID' => $record->ID]
        )
            ->addExtraClass(implode(' ', [
                'btn',
                'btn-secondary',
                'grid-field__icon-action',
                'action-menu--handled',
                'font-icon-check-mark',
            ]))
            ->setAttribute('classNames', 'font-icon-check-mark');

        return $field;//($record->Submitted == 0) ? $field : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getActions($gridField)
    {
        return ['approve'];
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
            return Controller::curr()->redirect('admin/submissions', 302);
        }

        $petition = $submission->PetitionPage();
        if(!$petition) {
            Controller::curr()->getResponse()->setStatusCode(
                400,
                'Submission is not attached to a petition page.'
            );
            return Controller::curr()->redirect('admin/submissions', 302);
        }
        
        if(!$petition->EmailTo) {
            Controller::curr()->getResponse()->setStatusCode(
                400,
                'Submission petition does not have a "to" email address.'
            );
            return Controller::curr()->redirect('admin/submissions', 302);
        }

        $render = $submission->ClassName == 'Submission' ? 'PetitionPDF' : 'DocPetitionPDF';

        $pdf = new Dompdf();
        $pdf->output();
        $pdf->loadHTML(ArrayData::create([
            'Data' => $submission,
            'SubmissionData' => $petition->toMap(),
            'Signature' => base64_encode($submission->SignatureImage()->Fit(400,240)->getString())
        ])->renderWith($render));
        $pdf->render();
        $date = DBField::create_field('DBDatetime', date('d-m-Y'));

        // $pdf->stream('Petition_'.$date->format('y-M-d').'.pdf');

        $subjectFileName = $submission->ClassName == 'Submission' ? 
            $petition->ApplicationReferenceNumber.' saveoursunnycoast submission by '.$submission->Name
            :
            'Submission by '.$submission->Name;


        $from = SiteConfig::current_site_config()->AdminEmail;
        $to = $petition->EmailTo;

        $emailContent = $petition->SubmissionEmail;
        $emailContent = str_replace('[name]', $submission->Name, $emailContent);
        $emailContent = ShortcodeParser::get_active()->parse($emailContent);


        $email = Email::create()
            ->setBody($emailContent)
            ->setFrom($from, $submission->Name)
            ->setReplyTo($submission->Email, $submission->Name)
            ->setTo($to)
            ->setSubject($subjectFileName);

        if($petition->CcTo) {
            $email->addCC($petition->CcTo);
        }

        $email->addCC($submission->Email);

        $email->addAttachmentFromData($pdf->output(), $subjectFileName.'.pdf', "application/pdf");
        $email->send();

        $submission->Submitted = 1;
        $submission->write();

        //refresh the page
        Controller::curr()->redirect('admin/submissions', 302);
        // output a success message to the user
        Controller::curr()->getResponse()->setStatusCode(
            200,
            'Submission Emailed.'
        );
    }
}
