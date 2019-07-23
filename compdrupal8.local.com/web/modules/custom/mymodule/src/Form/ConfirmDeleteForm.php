<?php

namespace Drupal\mymodule\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

class ConfirmDeleteForm extends ConfirmFormBase {

    protected $id;

    public function buildForm(array $form, FormStateInterface $form_state, $companypk = '') {
        $this->id = $companypk;
        return parent::buildForm($form, $form_state);
    }

    public function submitForm(array &$form, FormStateInterface $form_state) {
        db_delete('frtcompanies')
        ->condition('companypk', $this->id)
        ->execute();
        
        $form_state->setRedirect('mymodule_company_list');

}

/**
 * {@inheritdoc}
 */
public function getFormId() {
return "confirm_delete_form";
}

/**
 * {@inheritdoc}
 */
public function getCancelUrl() {
return new Url('mymodule_company_list');
}

/**
 * {@inheritdoc}
 */
public function getQuestion() {
return t('Do you want to delete %id?', ['%id' => $this->id]);
}

}
