<?php

namespace Drupal\commentapi\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;

class AddBannerForm extends FormBase {

    public function getFormId() {
        return 'add_banner_form';
    }

    public function buildForm(array $form, FormStateInterface $form_state) {

        $form['container'] = [
            '#type' => 'container',
            '#attributes' => ['id' => 'box-container'],
        ];

        $form['image'] = array(
            '#type' => 'managed_file',
            '#title' => t('Choose Image File'),
            '#upload_location' => 'public://images/',
            '#default_value' => '',
            '#description' => t('Specify an image(s) to display.'),
                // '#states'        => array(
                //   'visible'      => array(
                //     ':input[name="image_type"]' => array('value' => t('Upload New Image(s)')),
                //   ),
                // ),
        );

        $form['submit'] = array(
            '#type' => 'submit',
            '#value' => t('Save image'),
            '#ajax' => array(
                'callback' => '::promptCallback',
                'wrapper' => 'box-container',
                'effect' => 'fade',
            ),
        );

        return $form;
    }

    public function validateForm(array &$form, FormStateInterface $form_state) {
        File::load($form_state->getValue('image'));
    }

    public function submitForm(array &$form, FormStateInterface $form_state) {
        
    }

    public function promptCallback(array &$form, FormStateInterface $form_state) {

        $element = $form['container'];
        $element['box']['#markup'] = $form_state->getValue('image');
        return $element;
        drupal_set_message($form_state->getValue('image'));
    }

}
