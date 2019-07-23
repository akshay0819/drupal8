<?php

namespace Drupal\productmaster;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Url;

class DeleteProduct extends ConfirmFormBase {

    protected $id;

    public function getFormID() {
        return 'productmaster_delete';
    }

    public function getQuestion() {
        return t('Are you sure you want to delete Product %id?', array('%id' => $this->id));
    }

    public function getConfirmText() {
        return t('Delete');
    }

    public function getCancelUrl() {
        return new Url('productmaster_list');
    }

    public function buildForm(array $form, FormStateInterface $form_state, $id = NULL) {
        $this->id = $id;
        return parent::buildForm($form, $form_state);
    }

    public function submitForm(array &$form, FormStateInterface $form_state) {
        Product_Biz::delete_product($this->id);
//    watchdog('bd_contact', 'Deleted product  with id %id.', array('%id' => $this->id));
        drupal_set_message(t('Product %id has been deleted.', array('%id' => $this->id)));
        return new RedirectResponse(\Drupal::url('productmaster_list'));
    }

}