<?php

namespace Drupal\formmoduleinsp\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\formmoduleinsp\Controller\Formmoduleinsp_biz;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Url;

class Deleteformmoduleinsp extends ConfirmFormBase {

    protected $appinspformpk;

    public function getFormID() {
        return 'formmoduleinsp_delete';
    }

    public function getQuestion() {
        return t('Are you sure you want to delete Inspection Form %appinspformpk?', array('%appinspformpk' => $this->appinspformpk));
    }

    public function getConfirmText() {
        return t('Delete');
    }

    public function getCancelUrl() {
        return new Url('formmoduleinsp_example.list', array('appinspformpk' => $this->appinspformpk));
    }

    public function buildForm(array $form, FormStateInterface $form_state, $appinspformpk = NULL, $appformpk = NULL) {
        $this->appinspformpk = $appinspformpk;
        $this->appformpk = $appformpk;
        return parent::buildForm($form, $form_state);
    }

    public function submitForm(array &$form, FormStateInterface $form_state) {
        Formmoduleinsp_biz::delete_formmoduleinsp($this->appformpk);
//    watchdog('bd_contact', 'Deleted product  with id %id.', array('%id' => $this->id));
        drupal_set_message(t('Inspection Form %id has been deleted.', array('%id' => $this->appinspformpk)));
        return new RedirectResponse(\Drupal::url('formmoduleinsp_example.list', array('appinspformpk' => $this->appinspformpk)));
    }

}
