<?php

namespace Drupal\formfields\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\formfields\Controller\Formfields_biz;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Url;

class Deleteformfields extends ConfirmFormBase {

    protected $apmdgpk;

    public function getFormID() {
        return 'formfields_delete';
    }

    public function getQuestion() {
        return t('Are you sure you want to delete Field %apmdpk?', array('%apmdpk' => $this->apmdpk));
    }

    public function getConfirmText() {
        return t('Delete');
    }

    public function getCancelUrl() {
        return new Url('formfields_example.list');
    }

    public function buildForm(array $form, FormStateInterface $form_state, $apmdpk = NULL) {
        $this->apmdpk = $apmdpk;
        return parent::buildForm($form, $form_state);
    }

    public function submitForm(array &$form, FormStateInterface $form_state) {
        Formfields_biz::delete_formfields($this->apmdpk);
//    watchdog('bd_contact', 'Deleted product  with id %id.', array('%id' => $this->id));
        drupal_set_message(t('Field %id has been deleted.', array('%id' => $this->apmdpk)));
        return new RedirectResponse(\Drupal::url('formfields_example.list'));
    }

}
