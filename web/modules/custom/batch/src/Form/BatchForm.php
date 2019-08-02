<?php

namespace Drupal\batch\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\customutil\CustomUtils;
use Drupal\batch\Controller\Batch_biz;
use Drupal\batch\Controller\AutocompleteController;


/**
 * Form with examples on how to use cache.
 */
class BatchForm extends FormBase {

    public $batchpk;
      

    /**
     * {@inheritdoc}
     */
    public function getFormId() {
        return 'batch_example_form';
    }


    public function buildForm(array $form, FormStateInterface $form_state, $formmode = '', $batchpk = '') {


        $this->batchpk = $batchpk;
        $this->formmode = $formmode;
        $display_mode = FALSE;
        if ($this->formmode == 'DISPLAY') {

            $this->display_mode = TRUE;
        }

        $batchdet = Batch_biz::getbatchdet($batchpk);

        $form['formtitle'] = [
            '#markup' => '<i class="fa fa-gift"></i> &nbsp;Add New Batch',
            '#prefix' => '<div class="kt-portlet__head"><div class="kt-portlet__head-label">',
            '#suffix' => '</div></div>',
        ];

        $form['formbody'] = [
            '#markup' => '<form role="form" class="kt-form">
                        <div class="form-body">
                        <div class="row">'
        ];

        $form['batchno'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Batch Number'),
            '#default_value' =>  ($form_state->getValue('batchno') != false)? $form_state->getValue('batchno') :$batchdet['batchno'],
            '#prefix' => '<div class="col-md-6">',
            '#suffix' => '</div>'
        ];

        $form['usebydate'] = [
            '#type' => 'date',
            '#title' => $this->t('Expiry Date'),
            '#default_value' =>  $batchdet['usebydate'],
            '#prefix' => '<div class="col-md-6">',
            '#suffix' => '</div>'
        ];
        $form['productcode'] = [
            '#type' => 'textfield',
            '#autocomplete_route_name' => 'batch_example.autocomplete',
            '#title' => $this->t('Product Name'),
            '#default_value' => ($form_state->getValue('productcode') != false)? $form_state->getValue('productcode') :$batchdet['productpk'],
            '#prefix' => '<div class="col-md-6">',
            '#suffix' => '</div>'
        ];


        $form['netweight'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Net Weight'),
            '#default_value' => ($form_state->getValue('netweight') != false)? $form_state->getValue('netweight') :$batchdet['netweight'],
            '#prefix' => '<div class="col-md-6">',
            '#suffix' => '</div>'
        ];

        $form['batchdate'] = [
            '#type' => 'date',
            '#title' => $this->t('Batch Date'),
            '#default_value' =>  ($form_state->getValue('batchdate') != false) ? $form_state->getValue('batchdate') : $batchdet['batchdate'],
            '#prefix' => '<div class="col-md-6">',
            '#suffix' => '</div>'
        ];

        $form['packdate'] = [
            '#type' => 'date',
            '#title' => $this->t('Manufactured Date'),
//            '#date_format' => 'd-m-Y',
            '#default_value' => ($form_state->getValue('packdate') != false) ? $form_state->getValue('packdate') : $batchdet['packdate'],
            '#prefix' => '<div class="col-md-6">',
            '#suffix' => '</div>'
        ];

        $form['packedweight'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Packed Weight'),
            '#default_value' => ($form_state->getValue('packedweight') != false) ? $form_state->getValue('packedweight') : $batchdet['packedweight'],
            '#prefix' => '<div class="col-md-6">',
            '#suffix' => '</div>'
        ];

        $form['batchtext'] = [
            '#type' => 'textarea',
            '#title' => $this->t('Batch Text'),
            '#rows' => 2,
            '#default_value' => ($form_state->getValue('batchtext') != false) ? $form_state->getValue('batchtext') : $batchdet['batchtext'],
            '#prefix' => '<div class="col-md-6">',
            '#suffix' => '</div>'
        ];

        $templates = AutocompleteController::getTemplates();
        $form['customtemplatepk'] = [
            '#type' => 'select',
            '#title' => $this->t('Template Name'),
            '#options' => $templates,
            '#default_value' =>  ($form_state->getValue('customtemplatepk') != false) ? $form_state->getValue('customtemplatepk') : $batchdet['customtemplatepk'],
//            '#ajax' => [
//                'callback' => '::textfieldsCallback',
//                'wrapper' => 'textfields-container',
//                'effect' => 'fade',
//            ],
            '#prefix' => '<div class="col-md-6">',
            '#suffix' => '</div>'
        ];


        // Wrap textfields in a container. This container will be replaced through
        // AJAX.
        $form['textfields_container'] = [
            '#type' => 'container',
            '#attributes' => ['id' => 'textfields-container'],
        ];
        $form['textfields_container']['textfields'] = [
            '#type' => 'markup',
            '#title' => $this->t("Generated text fields for first and last name"),
            '#description' => t('This is where we put automatically generated textfields'),
            '#prefix' => '<div class="container"><div class="row">',
            '#suffix' => '</div></div>'
        ];
        // This form is rebuilt on all requests, so whether or not the request comes
        // from AJAX, we should rebuild everything based on the form state.
        if(!empty($batchdet['customtemplatepk'])){
            $form_state->setValue('customtemplatepk', $batchdet['customtemplatepk']);
        }
        if ($form_state->getValue('customtemplatepk') != false) {
            $templates = AutocompleteController::getTemplateFields($form_state->getValue('customtemplatepk'));


            $form['textfields_container']['textfields']['borderline'] = [
                '#type' => 'fieldset',
                '#title' => $this->t("Generated text fields for Template"),
                '#prefix' => '<div class="col-md-12">',
                '#suffix' => '</div>'
            ];
            foreach ($templates as $key => $value) {
                $form['textfields_container']['textfields'][$value->fcode] = [  
                    '#type' => 'textfield',
                    '#title' => $value->fname,
                    '#default_value' => $batchdet[$value->fcode]['labledesc'],
                    '#prefix' => '<div class="col-md-6">',
                    '#suffix' => '</div>'
                ];
            }

//            $form['secondformbodyend'] = [
//                '#markup' => '</div><div class="row">'
//            ];
        }
        $form['actions']['next'] = [
            '#type' => 'submit',
            '#value' => $this->t('Apply'),
            '#submit' => ['::fapiExampleMultistepFormNextSubmit'],
            '#ajax' => [
                'callback' => '::textfieldsCallback',
                'wrapper' => 'textfields-container',
                'effect' => 'fade',
            ],
            '#prefix' => '<div class="col-md-6">',
            '#suffix' => '</div>'
        ];
        $form['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Submit'),
            '#prefix' => '<div class="col-md-6">',
            '#suffix' => '</div>'
        ];
        $form['formbodyend'] = [
            '#markup' => '</form></div></div>'
        ];


        return $form;
    }

    public function validateForm(array &$form, FormStateInterface $form_state) {
        if ($form_state->getValue('customtemplatepk') == false) {
            $form_state->setErrorByName('customtemplatepk', $this->t('Please Select the Batch template'));
        }

        if ($form_state->getValue('batchtext') == '') {
            $form_state->setErrorByName('batchtext', $this->t('Please enter textarea'));
        }

        if ($form_state->getValue('batchdate') == '') {
            $form_state->setErrorByName('batchdate', $this->t('Please enter Manufacture date'));
        }

        if ($form_state->getValue('packdate') == '') {
            $form_state->setErrorByName('packdate', $this->t('Please enter Packed date'));
        }

        if ($form_state->getValue('usebydate') == '') {
            $form_state->setErrorByName('usebydate', $this->t('Please enter Expiry Date'));
        }
        if(!is_numeric($form_state->getValue('netweight'))){
             $form_state->setErrorByName('netweight', $this->t('Please enter Numeric Net Weight'));
        }

                if(!is_numeric($form_state->getValue('packedweight'))){
             $form_state->setErrorByName('packedweight', $this->t('Please enter Numeric Packed Weight'));
        }
//        $form_state->setRebuild();
    }

    public function fapiExampleMultistepFormNextSubmit(array &$form, FormStateInterface $form_state) {
        $form_state->setRebuild();
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $customtemplatepk = $form_state->getValue('customtemplatepk');
        // drupal_set_message(t('An error occurred and processing did not complete.'), 'error');


        // $values = $form_state->getValues();

        // $insertid = db_insert('tragbatch')
        //         ->fields(array(
        //             'batchno' => $values['batchno'],
        //             'batchdate' => $values['batchdate'],
        //             'packdate' => $values['packdate'],
        //             'usebydate' => $values['usebydate'],
        //             'netweight' => $values['netweight'],
        //             'packedweight' => $values['packedweight'],
        //             'batchtext' => $values['batchtext'],
        //             'productdesc' => $values['productcode'],
        //         ))
        //         ->execute();

        // if ($insertid) {
        //     $query = db_select('tragcustomtemplatefields', 'tempfields');
        //     $query->fields('tempfields', ['fcode', 'source']);
        //     $query->condition('customtemplatepk', $customtemplatepk, '=');
        //     $result = $query->execute()->fetchAll();
        //     foreach ($result as $k => $val) {
        //         $fcode = $values[$val->fcode];
        //         $insertprod = db_insert('trproductinfo')
        //                 ->fields(array(
        //                     'batchpk' => $insertid,
        //                     'productpk' => 1, //$values['productcode'],
        //                     'lablecode' => $val->fcode,
        //                     'labledesc' => $fcode,
        //                         // 'updatedby' => $values['batchtext'],
        //                 ))
        //                 ->execute();
        //     }
        // }

        // //$form_state->setRebuild();
        // $form_state->setRedirect('batch_example.list');

        $vals = $form_state->getValues();

        switch ($this->formmode) {
            case 'NEW':
                $returnval = Batch_biz::save_batch($form, $form_state);
                if ($returnval == 'FAIL') {
                    
                } else {
                    drupal_set_message(t("Batch details Saved Successfully"));
                    $form_state->setRedirect('batch_example_display', array('batchpk' => $returnval));
                }
                break;
            case 'EDIT':
                $returnval = Batch_biz::edit_batch($form, $form_state, $this->batchpk);
                if ($returnval == 'FAIL') {
                    
                } else {
                    drupal_set_message(t("Batch Updated Successfully"));
                    $form_state->setRedirect('batch_example_display', array('batchpk' => $returnval));
                }
                break;
            default:
                $form_state->setRedirect('batch_example.list');
                break;
        }





    }

    /**
     * Callback for ajax_example_autotextfields.
     *
     * Selects the piece of the form we want to use as replacement markup and
     * returns it as a form (renderable array).
     */
    public function textfieldsCallback($form, FormStateInterface $form_state) {
        $form_state->setRebuild();
        return $form['textfields_container'];
    }

}
