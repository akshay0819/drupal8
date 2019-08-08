<?php

namespace Drupal\formmoduleinsp\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\customutil\CustomUtils;
use Drupal\formmoduleinsp\Controller\Formmoduleinsp_biz;
use Drupal\formmoduleinsp\Controller\AutocompleteController;


/**
 * Form with examples on how to use cache.
 */
class FormmoduleinspForm extends FormBase {

    public $appinspformpk;
      

    /**
     * {@inheritdoc}
     */
    public function getFormId() {
        return 'formmoduleinsp_example_form';
    }


    public function buildForm(array $form, FormStateInterface $form_state, $formmode = '', $appinspformpk = '', $appformpk = '') {

        $this->appinspformpk = $appinspformpk;
        $this->appformpk = $appformpk;
        $this->formmode = $formmode;
        $display_mode = FALSE;
        if ($this->formmode == 'DISPLAY') {

            $this->display_mode = TRUE;
        }

        $formmoduleinspdet = Formmoduleinsp_biz::getformmoduleinspdet($appinspformpk, $appformpk);

        $query = db_select('appinspectionform', 'a');
        $query->fields('a');
        $query->condition('appinspformpk', $appinspformpk, '=');
        $result = $query->execute()->fetchAssoc();
	$form['formtitle'] = [
            '#markup' => '<i class="fa fa-gift"></i> &nbsp;'. $result['appinspformname'] .' Info',
            '#prefix' => '<div class="kt-portlet__head"><div class="kt-portlet__head-label">',
            '#suffix' => '</div></div>',
        ];
	$this->appinspformname = $result['appinspformname'];
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
	$edit_formfields = CustomUtils::addButton('formmoduleinsp_example_edit', array('appinspformpk' => $appinspformpk, 'appformpk' => $appformpk), 'medium', 'Edit '. $result['appinspformname'] . ' Form');
        
        $form['buttons']['submitedit'] = [
            '#markup' => $edit_formfields,
            '#prefix' => '<div class="col-md-6">&nbsp;</div><div class="col-md-6">&nbsp;</div><div class="kt-portlet__head-toolbar">
                                        <div class="kt-portlet__head-wrapper">
                                            <div class="kt-portlet__head-actions">',
            '#suffix' => '</div></div></div>&nbsp;&nbsp;',
        ];
	$add_formfields = CustomUtils::addButton('formmoduleinsp_example.form', array('appinspformpk' => $appinspformpk), 'medium', 'Add '. $result['appinspformname'] . ' Form');

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
        $url = Url::fromRoute('formmoduleinsp_example.list', array('appinspformpk' => $appinspformpk));
        $url->setOptions($link_options);
        $cancel_formmoduleinsplink = \Drupal::l(t('Cancel'), $url);

        $form['buttons']['cancel'] = [
            '#markup' => $cancel_formmoduleinsplink,
            '#prefix' => '<div class="kt-portlet__head-toolbar">
                                        <div class="kt-portlet__head-wrapper">
                                            <div class="kt-portlet__head-actions">',
            '#suffix' => '</div></div></div>',
        ];
        $query = db_select('appinspectiondtl', 'a');
        $query->fields('a');
        $query->condition('appinspformpk', $appinspformpk, '=');
        

        $getlist = $query->execute();
       
        $i = 0;
	foreach ($getlist as $fld) {
	   $form['h'. $i] = ['#type' => 'details', '#title' => $fld->chapter, '#open' => TRUE];
	   $form['h'. $i]['requirements'] = ['#markup' => nl2br($fld->requirements).(!empty($fld->requirements) ? '<br />' : '')];
	   $form['h'. $i]['checklist'] = ['#markup' => nl2br($fld->checklist)];
	   $form['h'. $i]['text'.$i] = ['#type' => 'textfield', '#placeholder' => 'Short answer text', '#maxlength' => 1000];
	   $i++;
	}
	if (!isset($this->display_mode)) {
	$form['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Submit'),
            '#prefix' => '<div class="col-md-6">&nbsp;</div><div class="col-md-6">&nbsp;</div><div class="col-md-6">',
            '#suffix' => '</div>'
        ];
	}
        else {
	$edit_formfields = CustomUtils::addButton('formmoduleinsp_example_edit', array('appinspformpk' => $appinspformpk, 'appformpk' => $appformpk), 'medium', 'Edit '. $result['appinspformname'] . ' Form');

        $form['actions']['submitedit'] = [
            '#markup' => $edit_formfields,
            '#prefix' => ($i % 2 == 0) ? '<div class="col-md-6">&nbsp;</div><div class="col-md-6">&nbsp;</div><div class="kt-portlet__head-toolbar"><div class="kt-portlet__head-wrapper"><div class="kt-portlet__head-actions">' : '<div class="col-md-6">&nbsp;</div><div class="col-md-6">&nbsp;</div><div class="col-md-6"></div><div class="kt-portlet__head-toolbar"><div class="kt-portlet__head-wrapper"><div class="kt-portlet__head-actions">',
            '#suffix' => '</div></div></div>&nbsp;&nbsp;',
        ];
	$add_formfields = CustomUtils::addButton('formmoduleinsp_example.form', array('appinspformpk' => $appinspformpk), 'medium', 'Add '. $result['appinspformname'] . ' Form');

        $form['actions']['submitadd'] = [
            '#markup' => $add_formfields,
            '#prefix' => '<div class="kt-portlet__head-toolbar">
                                        <div class="kt-portlet__head-wrapper">
                                            <div class="kt-portlet__head-actions">',
            '#suffix' => '</div></div></div>&nbsp;&nbsp;',
        ];
	}
        $url = Url::fromRoute('formmoduleinsp_example.list', array('appinspformpk' => $appinspformpk));
        $url->setOptions($link_options);
        $cancel_formmoduleinsplink = \Drupal::l(t('Cancel'), $url);

        $form['actions']['cancel'] = [
            '#markup' => $cancel_formmoduleinsplink,
            '#prefix' => '<div class="kt-portlet__head-toolbar">
                                        <div class="kt-portlet__head-wrapper">
                                            <div class="kt-portlet__head-actions">',
            '#suffix' => '</div></div></div>',
        ];
        $form['formbodyend'] = [
            '#markup' => '</form></div></div>'
        ];


        $form['#attached']['library'][] = 'formmoduleinsp/formmoduleinsplib';
        return $form;
    }

    public function validateForm(array &$form, FormStateInterface $form_state) {
 	/*if ($form_state->getValue('apmdgroupid') == '') {
            $form_state->setErrorByName('apmdgroupid', $this->t('Please enter proper value'));
        }

        if ($form_state->getValue('apmdgroupname') == '') {
            $form_state->setErrorByName('apmdgroupname', $this->t('Please enter group name'));
        }

        if ($form_state->getValue('apmdfields') == '') {
            $form_state->setErrorByName('apmdfields', $this->t('Please enter fields'));
        }

        if ($form_state->getValue('aptablefields') == '') {
            $form_state->setErrorByName('aptablefields', $this->t('Please enter table fields'));
        }

        if ($form_state->getValue('apkeyfields') == '') {
            $form_state->setErrorByName('apkeyfields', $this->t('Please enter appkeyfields'));
        }
 	$apmdgroupid = db_query("SELECT appinspformpk from {appinspectionform} WHERE apmdgroupid = :apmdgroupid AND appinspformpk <> :appinspformpk LIMIT 1", array(":apmdgroupid" => $form_state->getValue('apmdgroupid'), ":appinspformpk" => $this->appinspformpk))->fetchField();

        if (!empty($apmdgroupid)) {
            $form_state->setErrorByName('apmdgroupid', $this->t('There is already one Group with this id. Please enter different apmdgroupid'));
        }*/
        
//        $form_state->setRebuild();
    }

    public function fapiExampleMultistepFormNextSubmit(array &$form, FormStateInterface $form_state) {
        $form_state->setRebuild();
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
       // $appinspformpk = $form_state->getValue('appinspformpk');
        
      //  $vals = $form_state->getValues();

        switch ($this->formmode) {
            case 'NEW':
                $returnval = Formmoduleinsp_biz::save_formmoduleinsp($form, $form_state, $this->appinspformpk, $this->apmdgroupname);
                if ($returnval == 'FAIL') {                    
                } else {
                    drupal_set_message(t("Formmoduleinsp Saved Successfully"));
                    $form_state->setRedirect('formmoduleinsp_example_display', array('appinspformpk' => $this->appinspformpk, 'appformpk' => $returnval));
                }
                break;
            case 'EDIT':
                $returnval = Formmoduleinsp_biz::edit_formmoduleinsp($form, $form_state, $this->appinspformpk, $this->appformpk);
                if ($returnval == 'FAIL') {
                    
                } else {
                    drupal_set_message(t("Formmoduleinsp Updated Successfully"));
                    $form_state->setRedirect('formmoduleinsp_example_display', array('appinspformpk' => $this->appinspformpk, 'appformpk' => $this->appformpk));
                }
                break;
            default:
                $form_state->setRedirect('formmoduleinsp_example.list', array('appinspformpk' => $this->appinspformpk));
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
