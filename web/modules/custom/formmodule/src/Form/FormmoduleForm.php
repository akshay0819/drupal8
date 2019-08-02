<?php

namespace Drupal\formmodule\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\customutil\CustomUtils;
use Drupal\formmodule\Controller\Formmodule_biz;
use Drupal\formmodule\Controller\AutocompleteController;


/**
 * Form with examples on how to use cache.
 */
class FormmoduleForm extends FormBase {

    public $apmdgpk;
      

    /**
     * {@inheritdoc}
     */
    public function getFormId() {
        return 'formmodule_example_form';
    }


    public function buildForm(array $form, FormStateInterface $form_state, $formmode = '', $apmdgpk = '', $appformpk = '') {

        $this->apmdgpk = $apmdgpk;
        $this->appformpk = $appformpk;
        $this->formmode = $formmode;
        $display_mode = FALSE;
        if ($this->formmode == 'DISPLAY') {

            $this->display_mode = TRUE;
        }

        $formmoduledet = Formmodule_biz::getformmoduledet($apmdgpk, $appformpk);

        $query = db_select('appmdgroup', 'a');
        $query->fields('a');
        $query->condition('apmdgpk', $apmdgpk, '=');
        $result = $query->execute()->fetchAssoc();
	$form['formtitle'] = [
            '#markup' => '<i class="fa fa-gift"></i> &nbsp;'. $result['apmdgroupname'] .' Info',
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
	$edit_formfields = CustomUtils::addButton('formmodule_example_edit', array('apmdgpk' => $apmdgpk, 'appformpk' => $appformpk), 'medium', 'Edit '. $result['apmdgroupname'] . ' Form');

        $form['buttons']['submitedit'] = [
            '#markup' => $edit_formfields,
            '#prefix' => '<div class="col-md-6">&nbsp;</div><div class="col-md-6">&nbsp;</div><div class="kt-portlet__head-toolbar">
                                        <div class="kt-portlet__head-wrapper">
                                            <div class="kt-portlet__head-actions">',
            '#suffix' => '</div></div></div>&nbsp;&nbsp;',
        ];
	$add_formfields = CustomUtils::addButton('formmodule_example.form', array('apmdgpk' => $apmdgpk), 'medium', 'Add '. $result['apmdgroupname'] . ' Form');

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
        $url = Url::fromRoute('formmodule_example.list', array('apmdgpk' => $apmdgpk));
        $url->setOptions($link_options);
        $cancel_formmodulelink = \Drupal::l(t('Cancel'), $url);

        $form['buttons']['cancel'] = [
            '#markup' => $cancel_formmodulelink,
            '#prefix' => '<div class="kt-portlet__head-toolbar">
                                        <div class="kt-portlet__head-wrapper">
                                            <div class="kt-portlet__head-actions">',
            '#suffix' => isset($this->display_mode) ? '</div></div></div><div class="col-md-6"></div>' : '</div></div></div>',
        ];
        
        $getfields = json_decode($result['apmdfields'], true);
	$values = json_decode($formmoduledet['appgroupfields'],true);
	$ftype = ['DATE' => 'date', 'CHAR' => 'textfield', 'AUTO' => 'textfield', 'SELECT' => 'select', 'FLOAT' => 'textfield', 'CHECK' => 'checkboxes', 'INT' => 'textfield', 'RADIO' => 'textfield', 'TEXT' => 'textarea'];
	$i = 0;$j = 0;
	foreach ($getfields as $fld)  {
	$desc = db_query("SELECT apmddesc from {appmetadata} WHERE apmdname = :apmdname AND apmdtype <> 'HEAD' LIMIT 1", array(":apmdname" => $fld))->fetchField();
	$type = db_query("SELECT apmdtype from {appmetadata} WHERE apmdname = :apmdname AND apmdtype <> 'HEAD' LIMIT 1", array(":apmdname" => $fld))->fetchField();
	$options = json_decode(db_query("SELECT apmdoptions from {appmetadata} WHERE apmdname = :apmdname AND apmdtype <> 'HEAD' LIMIT 1", array(":apmdname" => $fld))->fetchField(), true);
	$hdesc = db_query("SELECT apmddesc from {appmetadata} WHERE apmdname = :apmdname AND apmdtype = 'HEAD' LIMIT 1", array(":apmdname" => $fld))->fetchField();
	if (!empty($hdesc)) {
	   $j++;
	   $form['h'. $j] = ['#type' => 'details', '#title' => $this->t($hdesc), '#open' => ($j == 1) ? TRUE : FALSE];
	}
	else {
	$form['h'. $j][$fld] = [
            '#type' => $ftype[$type],
            '#title' => $this->t($desc),
            '#default_value' => ($form_state->getValue($fld) != false) ? $form_state->getValue($fld) : $values[$fld],
	    '#attributes' =>  isset($this->display_mode) ? ['readonly' => 'readonly'] : [], 
            '#prefix' => '<div class="col-md-6">',
            '#suffix' => '</div>'
        ];
	if ($type == 'DATE') {
	  //$form['h'. $j][$fld]['#date_date_format'] = 'm / d / Y'; 
	  $form['h'. $j][$fld]['#default_value'] = !empty($values[$fld]) ? date('Y-m-d', strtotime($values[$fld])) : date('Y-m-d');
	}
	if ($type == 'SELECT' || $type == 'RADIO' || $type == 'CHECK') {
	  if (isset($this->display_mode)) $form['h'. $j][$fld]['#disabled'] = TRUE;
	  $form['h'. $j][$fld]['#options'] = $options;
	}
	}
	if (empty($hdesc)) $i++;
	}
	if (!isset($this->display_mode)) {
	$form['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Submit'),
            '#prefix' => ($i % 2 == 0) ? '<div class="col-md-6">&nbsp;</div><div class="col-md-6">&nbsp;</div><div class="col-md-6">' : '<div class="col-md-6"></div><div class="col-md-6">&nbsp;</div><div class="col-md-6">&nbsp;</div><div class="col-md-6">',
            '#suffix' => '</div>'
        ];
	}
        else {
	$edit_formfields = CustomUtils::addButton('formmodule_example_edit', array('apmdgpk' => $apmdgpk, 'appformpk' => $appformpk), 'medium', 'Edit '. $result['apmdgroupname'] . ' Form');

        $form['actions']['submitedit'] = [
            '#markup' => $edit_formfields,
            '#prefix' => ($i % 2 == 0) ? '<div class="col-md-6">&nbsp;</div><div class="col-md-6">&nbsp;</div><div class="kt-portlet__head-toolbar"><div class="kt-portlet__head-wrapper"><div class="kt-portlet__head-actions">' : '<div class="col-md-6">&nbsp;</div><div class="col-md-6">&nbsp;</div><div class="col-md-6"></div><div class="kt-portlet__head-toolbar"><div class="kt-portlet__head-wrapper"><div class="kt-portlet__head-actions">',
            '#suffix' => '</div></div></div>&nbsp;&nbsp;',
        ];
	$add_formfields = CustomUtils::addButton('formmodule_example.form', array('apmdgpk' => $apmdgpk), 'medium', 'Add '. $result['apmdgroupname'] . ' Form');

        $form['actions']['submitadd'] = [
            '#markup' => $add_formfields,
            '#prefix' => '<div class="kt-portlet__head-toolbar">
                                        <div class="kt-portlet__head-wrapper">
                                            <div class="kt-portlet__head-actions">',
            '#suffix' => '</div></div></div>&nbsp;&nbsp;',
        ];
	}
        $url = Url::fromRoute('formmodule_example.list', array('apmdgpk' => $apmdgpk));
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


        $form['#attached']['library'][] = 'formmodule/formmodulelib';
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
 	$apmdgroupid = db_query("SELECT apmdgpk from {appmdgroup} WHERE apmdgroupid = :apmdgroupid AND apmdgpk <> :apmdgpk LIMIT 1", array(":apmdgroupid" => $form_state->getValue('apmdgroupid'), ":apmdgpk" => $this->apmdgpk))->fetchField();

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
       // $apmdgpk = $form_state->getValue('apmdgpk');
        
        $vals = $form_state->getValues();

        switch ($this->formmode) {
            case 'NEW':
                $returnval = Formmodule_biz::save_formmodule($form, $form_state, $this->apmdgpk);
                if ($returnval == 'FAIL') {                    
                } else {
                    drupal_set_message(t("Formmodule Saved Successfully"));
                    $form_state->setRedirect('formmodule_example_display', array('apmdgpk' => $this->apmdgpk, 'appformpk' => $returnval));
                }
                break;
            case 'EDIT':
                $returnval = Formmodule_biz::edit_formmodule($form, $form_state, $this->apmdgpk, $this->appformpk);
                if ($returnval == 'FAIL') {
                    
                } else {
                    drupal_set_message(t("Formmodule Updated Successfully"));
                    $form_state->setRedirect('formmodule_example_display', array('apmdgpk' => $this->apmdgpk, 'appformpk' => $this->appformpk));
                }
                break;
            default:
                $form_state->setRedirect('formmodule_example.list', array('apmdgpk' => $this->apmdgpk));
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
