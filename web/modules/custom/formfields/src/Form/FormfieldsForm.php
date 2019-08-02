<?php

namespace Drupal\formfields\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\customutil\CustomUtils;
use Drupal\formfields\Controller\Formfields_biz;
use Drupal\formfields\Controller\AutocompleteController;


/**
 * Form with examples on how to use cache.
 */
class FormfieldsForm extends FormBase {

    public $apmdpk;
      

    /**
     * {@inheritdoc}
     */
    public function getFormId() {
        return 'formfields_example_form';
    }


    public function buildForm(array $form, FormStateInterface $form_state, $formmode = '', $apmdpk = '') {

        $this->apmdpk = $apmdpk;
        $this->formmode = $formmode;
        $display_mode = FALSE;
        if ($this->formmode == 'DISPLAY') {

            $this->display_mode = TRUE;
        }

        $formfieldsdet = Formfields_biz::getformfieldsdet($apmdpk);

        $form['formtitle'] = [
            '#markup' => '<i class="fa fa-gift"></i> &nbsp;Field Info',
            '#prefix' => '<div class="kt-portlet__head"><div class="kt-portlet__head-label">',
            '#suffix' => '</div></div>',
        ];

        $form['formbody'] = [
            '#markup' => '<form role="form" class="kt-form">
                        <div class="form-body">
                        <div class="row">'
        ];

        if (!isset($this->display_mode)) {
	$form['submitup'] = [
            '#type' => 'submit',
            '#value' => $this->t('Submit'),
            '#prefix' => '<div class="col-md-6">&nbsp;</div><div class="col-md-6">&nbsp;</div><div class="col-md-6">',
            '#suffix' => '</div>'
        ];
	}
	else {
	$edit_formfields = CustomUtils::addButton('formfields_example_edit', array('apmdpk' => $apmdpk), 'medium', 'Edit Form Field');

        $form['buttons']['submitedit'] = [
            '#markup' => $edit_formfields,
            '#prefix' => '<div class="col-md-6">&nbsp;</div><div class="col-md-6">&nbsp;</div><div class="kt-portlet__head-toolbar">
                                        <div class="kt-portlet__head-wrapper">
                                            <div class="kt-portlet__head-actions">',
            '#suffix' => '</div></div></div>&nbsp;&nbsp;',
        ];
	$add_formfields = CustomUtils::addButton('formfields_example.form', '', 'medium', 'Add Form Field');

        $form['buttons']['submitadd'] = [
            '#markup' => $add_formfields,
            '#prefix' => '<div class="kt-portlet__head-toolbar">
                                        <div class="kt-portlet__head-wrapper">
                                            <div class="kt-portlet__head-actions">',
            '#suffix' => '</div></div></div>&nbsp;&nbsp;',
        ];
	}
        $link_options = array(
            'attributes' => array(
                'class' => array(
                    'btn',
                    'btn-danger',
                ),
            ),
        );
        $url = Url::fromRoute('formfields_example.list');
        $url->setOptions($link_options);
        $cancel_formmodulelink = \Drupal::l(t('Cancel'), $url);

        $form['buttons']['cancel'] = [
            '#markup' => $cancel_formmodulelink,
            '#prefix' => '<div class="kt-portlet__head-toolbar">
                                        <div class="kt-portlet__head-wrapper">
                                            <div class="kt-portlet__head-actions">',
            '#suffix' => isset($this->display_mode) ? '</div></div></div><div class="col-md-6"></div>' : '</div></div></div>',
        ];
        $form['apmdname'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Field Name'),
            '#default_value' =>  ($form_state->getValue('apmdname') != false)? $form_state->getValue('apmdname') :$formfieldsdet['apmdname'],
	    '#attributes' =>  isset($this->display_mode) ? ['readonly' => 'readonly'] : [], 
            '#prefix' => '<div class="col-md-6">',
            '#suffix' => '</div>'
        ];
	$types = CustomUtils::getCodevaluesFormCodetype('MDTY');
        $form['apmdtype'] = [
            '#type' =>  'select',
            '#title' => $this->t('Field Type'),
            '#options' => $types,
            '#default_value' => ($form_state->getValue('apmdtype') != false)? $form_state->getValue('apmdtype') :(isset($this->display_mode) ? $formfieldsdet['apmdtype'] : $formfieldsdet['apmdtype']),
            '#attributes' =>  isset($this->display_mode) ? ['readonly' => 'readonly'] : [], 
	    '#disabled' => isset($this->display_mode) ? TRUE : FALSE,
            '#prefix' => '<div class="col-md-6">',
            '#suffix' => '</div>'
        ];

	$form['apmdlength'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Field Length'),
            '#default_value' => ($form_state->getValue('apmdlength') != false) ? $form_state->getValue('apmdlength') : $formfieldsdet['apmdlength'],
            '#attributes' =>  isset($this->display_mode) ? ['readonly' => 'readonly'] : [], 
            '#prefix' => '<div class="col-md-6">',
            '#suffix' => '</div>'
        ];

        
	$form['apmddesc'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Field Desc'),
            '#default_value' => ($form_state->getValue('apmddesc') != false) ? $form_state->getValue('apmddesc') : $formfieldsdet['apmddesc'],
            '#attributes' =>  isset($this->display_mode) ? ['readonly' => 'readonly'] : [], 
            '#prefix' => '<div class="col-md-6">',
            '#suffix' => '</div>'
        ];

        if (!isset($this->display_mode)) {
	$form['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Submit'),
            '#prefix' => '<div class="col-md-6">&nbsp;</div><div class="col-md-6">&nbsp;</div><div class="col-md-6">',
            '#suffix' => '</div>'
        ];
	}
	else {
	$edit_formfields = CustomUtils::addButton('formfields_example_edit', array('apmdpk' => $apmdpk), 'medium', 'Edit Form Field');

        $form['actions']['submitedit'] = [
            '#markup' => $edit_formfields,
            '#prefix' => '<div class="col-md-6">&nbsp;</div><div class="col-md-6">&nbsp;</div><div class="kt-portlet__head-toolbar">
                                        <div class="kt-portlet__head-wrapper">
                                            <div class="kt-portlet__head-actions">',
            '#suffix' => '</div></div></div>&nbsp;&nbsp;',
        ];
	$add_formfields = CustomUtils::addButton('formfields_example.form', '', 'medium', 'Add Form Field');

        $form['actions']['submitadd'] = [
            '#markup' => $add_formfields,
            '#prefix' => '<div class="kt-portlet__head-toolbar">
                                        <div class="kt-portlet__head-wrapper">
                                            <div class="kt-portlet__head-actions">',
            '#suffix' => '</div></div></div>&nbsp;&nbsp;',
        ];
	}
        $url = Url::fromRoute('formfields_example.list');
        $url->setOptions($link_options);
        $cancel_formmodulelink = \Drupal::l(t('Cancel'), $url);

        $form['actions']['cancel'] = [
            '#markup' => $cancel_formmodulelink,
            '#prefix' => '<div class="kt-portlet__head-toolbar">
                                        <div class="kt-portlet__head-wrapper">
                                            <div class="kt-portlet__head-actions">',
            '#suffix' => '</div></div></div>',
        ];
        $form['formbodyend'] = [
            '#markup' => '</form></div></div>'
        ];


        return $form;
    }

    public function validateForm(array &$form, FormStateInterface $form_state) {
 	if ($form_state->getValue('apmdname') == '') {
            $form_state->setErrorByName('apmdname', $this->t('Please enter name'));
        }

        if ($form_state->getValue('apmdtype') == '') {
            $form_state->setErrorByName('apmdtype', $this->t('Please enter type'));
        }

        if ($form_state->getValue('apmddesc') == '') {
            $form_state->setErrorByName('apmddesc', $this->t('Please enter field description'));
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

        $apmdpk = $form_state->getValue('apmdpk');
        // drupal_set_message(t('An error occurred and processing did not complete.'), 'error');


        // $values = $form_state->getValues();

        // $insertid = db_insert('tragformfields')
        //         ->fields(array(
        //             'formfieldsno' => $values['formfieldsno'],
        //             'formfieldsdate' => $values['formfieldsdate'],
        //             'packdate' => $values['packdate'],
        //             'usebydate' => $values['usebydate'],
        //             'netweight' => $values['netweight'],
        //             'packedweight' => $values['packedweight'],
        //             'formfieldstext' => $values['formfieldstext'],
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
        //                     'apmdgpk' => $insertid,
        //                     'productpk' => 1, //$values['productcode'],
        //                     'lablecode' => $val->fcode,
        //                     'labledesc' => $fcode,
        //                         // 'updatedby' => $values['formfieldstext'],
        //                 ))
        //                 ->execute();
        //     }
        // }

        // //$form_state->setRebuild();
        // $form_state->setRedirect('formfields_example.list');

        $vals = $form_state->getValues();

        switch ($this->formmode) {
            case 'NEW':
                $returnval = Formfields_biz::save_formfields($form, $form_state);
                if ($returnval == 'FAIL') {                    
                } else {
                    drupal_set_message(t("Formfields details Saved Successfully"));
                    $form_state->setRedirect('formfields_example_display', array('apmdpk' => $returnval));
                }
                break;
            case 'EDIT':
                $returnval = Formfields_biz::edit_formfields($form, $form_state, $this->apmdpk);
                if ($returnval == 'FAIL') {
                    
                } else {
                    drupal_set_message(t("Formfields Updated Successfully"));
                    $form_state->setRedirect('formfields_example_display', array('apmdpk' => $this->apmdpk));
                }
                break;
            default:
                $form_state->setRedirect('formfields_example.list');
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
