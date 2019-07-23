<?php

namespace Drupal\commentapi\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;

class Attachments extends FormBase {

    public function getFormId() {
        return 'add_banner_form';
    }

    public function buildForm(array $form, FormStateInterface $form_state) {
        $form['container'] = [
            '#type' => 'container',
            '#attributes' => ['id' => 'box-container'],
//            '#prefix' => '<div class="col-lg-12 col-md-12 col-sm-12"><div class="kt-dropzone  m-dropzone--success" action="/attachments" id="m-dropzone-three">
//',
//            '#suffix' => '</div></div>',
        ];

//        $form['container']['image'] = array(
//            '#type' => 'managed_file',
//            '#title' => t('Choose Image File'),
//            '#upload_location' => 'public://images/',
//            '#default_value' => '',
//            '#description' => t('Specify an image(s) to display.'),
//            '#prefix' => '<div class="kt-dropzone__msg dz-message needsclick">
//                    <h3 class="kt-dropzone__msg-title">Drop files here or click to upload.</h3>
//                    <span class="kt-dropzone__msg-desc">Only image, pdf and psd files are allowed for upload</span>',
//            '#suffix' => '</div>',
//        );

        $form['images'] = array(
            '#type' => 'managed_file',
            '#upload_location' => 'public://images/',
            '#multiple' => TRUE,
            '#upload_validators' => array(
                'file_validate_extensions' => array('png gif jpg jpeg'),
                'file_validate_size' => array(25600000),
//                'file_validate_image_resolution' => array('800x600', '400x300'),
            ),
        );

        $form['submit'] = array(
            '#type' => 'submit',
            '#value' => t('Save image'),
//            '#ajax' => array(
//                'callback' => '::promptCallback',
//                'wrapper' => 'box-container',
//                'effect' => 'fade',
//            ),
            '#prefix' => '<div class="row">',
            '#suffix' => '</div>',
        );
//        $form['#attributes'] = array('class' => 'dropzone', 'enctype' => 'multipart/form-data', 'multiple' => true);
        return $form;
    }

    public function validateForm(array &$form, FormStateInterface $form_state) {
        File::load($form_state->getValue('image'));
    }

    public function submitForm(array &$form, FormStateInterface $form_state) {

        $ds  = DIRECTORY_SEPARATOR;
        $storeFolder = 'public://images/';
        print_r($form);
        exit;

        if (!empty($_FILES)) {

            $tempFile = $_FILES['file']['tmp_name'];          //3             

            $targetPath = dirname(__FILE__) . $ds . $storeFolder . $ds;  //4

            $targetFile = $targetPath . $_FILES['file']['name'];  //5

            move_uploaded_file($tempFile, $targetFile); //6
        }
    }

}
