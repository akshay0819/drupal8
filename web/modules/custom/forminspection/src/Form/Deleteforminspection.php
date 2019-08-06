<?php

namespace Drupal\forminspection\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\forminspection\Controller\Forminspection_biz;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Url;

class Deleteforminspection extends ConfirmFormBase {

    protected $appinspformpk;

    public function getFormID() {
        return 'forminspection_delete';
    }

    public function getQuestion() {
        return t('Are you sure you want to delete Inspection Form %appinspformpk?', array('%appinspformpk' => $this->appinspformpk));
    }

    public function getConfirmText() {
        return t('Delete');
    }

    public function getCancelUrl() {
        return new Url('forminspection_example.list');
    }

    public function inspectionForm(array $form, FormStateInterface $form_state, $appinspformpk = NULL) {
        $this->appinspformpk = $appinspformpk;
        return parent::inspectionForm($form, $form_state);
    }

    public function submitForm(array &$form, FormStateInterface $form_state) {
        Forminspection_biz::delete_forminspection($this->appinspformpk);
//    watchdog('bd_contact', 'Deleted product  with id %id.', array('%id' => $this->id));
        drupal_set_message(t('Metadata Group %id has been deleted.', array('%id' => $this->appinspformpk)));
        return new RedirectResponse(\Drupal::url('forminspection_example.list'));
    }

}
