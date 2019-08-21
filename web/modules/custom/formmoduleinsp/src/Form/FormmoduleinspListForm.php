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
class FormmoduleinspListForm extends FormBase {

    public $appinspformpk;
      

    /**
     * {@inheritdoc}
     */
    public function getFormId() {
        return 'formmodulelistinsp_example_form';
    }


    public function buildForm(array $form, FormStateInterface $form_state, $formmode = '', $appinspformpk = '') {

        $this->appinspformpk = $appinspformpk;
       // $this->appinspdtlpk = $appinspdtlpk;
        $this->formmode = $formmode;
        $display_mode = FALSE;
        if ($this->formmode == 'DISPLAY') {

            $this->display_mode = TRUE;
        }

        $forminspectiondet = Formmoduleinsp_biz::getforminspectionlistdet($appinspformpk);
	$details = Formmoduleinsp_biz::getforminspectionlistdtl($appinspformpk);

        $query = db_select('appinspectionform', 'a');
        $query->fields('a');
        $query->condition('appinspformpk', $appinspformpk, '=');
        $result = $query->execute()->fetchAssoc();
	$this->appinspformname = $result['appinspformname'];
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
	$edit_formfields = CustomUtils::addButton('forminspection_example_edit', array('appinspformpk' => $appinspformpk), 'medium', 'Edit '. $result['appinspformname'] . ' Form');
        
        $form['buttons']['submitedit'] = [
            '#markup' => $edit_formfields,
            '#prefix' => '<div class="col-md-6">&nbsp;</div><div class="col-md-6">&nbsp;</div><div class="kt-portlet__head-toolbar">
                                        <div class="kt-portlet__head-wrapper">
                                            <div class="kt-portlet__head-actions">',
            '#suffix' => '</div></div></div>&nbsp;&nbsp;',
        ];
	/*$add_formfields = CustomUtils::addButton('formmoduleinsp_example.form', array('appinspformpk' => $appinspformpk), 'medium', 'Add '. $result['appinspformname'] . ' Form');

        $form['buttons']['submitadd'] = [
            '#markup' => $add_formfields,
            '#prefix' => '<div class="kt-portlet__head-toolbar">
                                        <div class="kt-portlet__head-wrapper">
                                            <div class="kt-portlet__head-actions">',
            '#suffix' => '</div></div></div>&nbsp;&nbsp;',
        ];*/
	}
        $link_options = array(
            'attributes' => array(
                'class' => array(
                    'btn',
                    'btn-danger',
                ),
            ),
        );
        $link_options1 = array(
            'attributes' => array(
                'class' => array(
                    'btn',
                    'btn-primary',
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
        $form['appinspformid'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Inspection Form Id'),
            '#default_value' =>  $forminspectiondet['appinspformid'],
	    '#attributes' =>  isset($this->display_mode) ? ['readonly' => 'readonly'] : [], 
            '#prefix' => '<div class="row"><div class="col-md-6">',
            '#suffix' => '</div>'
        ];

        $form['appinspformname'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Form Name'),
            '#default_value' => $forminspectiondet['appinspformname'],
            '#attributes' =>  isset($this->display_mode) ? ['readonly' => 'readonly'] : [], 
            '#prefix' => '<div class="col-md-6">',
            '#suffix' => '</div>'
        ];

	$form['appinspauditor'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Auditor'),
            '#default_value' => $forminspectiondet['appinspauditor'],
            '#attributes' =>  isset($this->display_mode) ? ['readonly' => 'readonly'] : [], 
            '#prefix' => '<div class="col-md-6">',
            '#suffix' => '</div>'
        ];

	$form['appinspauditee'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Auditee'),
            '#default_value' => $forminspectiondet['appinspauditee'],
            '#attributes' =>  isset($this->display_mode) ? ['readonly' => 'readonly'] : [], 
            '#prefix' => '<div class="col-md-6">',
            '#suffix' => '</div>'
        ];

	$form['appauditdate'] = [
            '#type' => 'date',
            '#title' => $this->t('Audit Date'),
            '#default_value' => (!empty($forminspectiondet['appauditdate']) ? date('Y-m-d', strtotime($forminspectiondet['appauditdate'])) : date('Y-m-d')),
            '#attributes' =>  isset($this->display_mode) ? ['readonly' => 'readonly'] : [], 
            '#prefix' => '<div class="col-md-6">',
            '#suffix' => '</div>'
        ];
	$options = ['Create' => 'Create', 'Audit' => 'Audit', 'Feedback' => 'Feedback', 'Completed' => 'Completed'];
	$form['appinspstatus'] = [
            '#type' => isset($this->display_mode) ? 'textfield' : 'select',
            '#title' => $this->t('Inspection Status'),
	    '#options' => $options,
            '#default_value' =>$forminspectiondet['appinspstatus'],
            '#attributes' =>  isset($this->display_mode) ? ['readonly' => 'readonly'] : [], 
            '#prefix' => '<div class="col-md-6">',
            '#suffix' => '</div>'
        ];
	$form['appinspcomments'] = [
            '#type' => 'textarea',
            '#title' => $this->t('Auditee Comments'),
            '#default_value' => $forminspectiondet['appinspcomments'],
            '#attributes' =>  isset($this->display_mode) ? ['readonly' => 'readonly'] : [], 
            '#prefix' => '<div class="col-md-6">',
            '#suffix' => '</div>'
        ];
	$form['appinspfeedback'] = [
            '#type' => 'textarea',
            '#title' => $this->t('Auditor Feedback'),
            '#default_value' => $forminspectiondet['appinspfeedback'],
            '#attributes' =>  isset($this->display_mode) ? ['readonly' => 'readonly'] : [], 
            '#prefix' => '<div class="col-md-6">',
            '#suffix' => '</div></div>'
        ];
	
        $form['field_container'] = array(
            '#type' => 'container',
            '#tree' => TRUE,
            '#prefix' => '<div id="js-ajax-elements-wrapper">',
            '#suffix' => '</div>',
        );
        $count = count($details);
        if ($form_state->get('field_deltas') == '') {
	    if ($count > 0)
	      $form_state->set('field_deltas', range(0, $count - 1));
            else $form_state->set('field_deltas', range(0, 0));
        }

        $field_count = $form_state->get('field_deltas');
        // if ($form_state->get('arraycount') == '') {
        $form_state->set('arraycount', $count);
//        }

	if (!isset($this->display_mode)) {
        $form['field_container']['add_name'] = array(
            '#type' => 'submit',
            '#value' => t('Add More'),
            '#submit' => array('::inspectionAjaxExampleAddMoreAddOne'),
            '#ajax' => array(
                'callback' => '::inspectionAjaxExampleAddMoreAddOneCallback',
                'wrapper' => 'js-ajax-elements-wrapper',
            ),
            '#prefix' => '<div class="row"><br/>',
            '#suffix' => '</div>',
        );
	}
        foreach ($field_count as $delta) {
            $form['field_container'][$delta] = array(
                '#prefix' => '<div class="row">',
                '#suffix' => '</div>',
                '#tree' => TRUE,
            );

            $form['field_container'][$delta]['appinspdtlpk'] = array(
                '#type' => 'hidden',
                '#default_value' => $details[$delta]->appinspdtlpk,
            );
	
            $form['field_container'][$delta]['slno'] = array(
                '#type' => 'textfield',
              //  '#title' => t('Sl No'),
                '#default_value' => isset($details[$delta]->slno) ? $details[$delta]->slno : $delta+1,
		'#attributes' => array('readonly' => 'readonly'),
                '#prefix' => '<div class="col-md-1 col-sm-10">',
                '#suffix' => '</div>',
            );

            $form['field_container'][$delta]['chapter'] = array(
                '#type' => 'textarea',
                '#title' => t('ChapterNo'),
                '#default_value' => $details[$delta]->chapter,
		'#attributes' =>  isset($this->display_mode) ? ['readonly' => 'readonly'] : [], 
            	'#prefix' => '<div class="col-md-2 col-sm-10">',
                '#suffix' => '</div>',
            );

            $form['field_container'][$delta]['requirements'] = array(
                '#type' => 'textarea',
                '#title' => t('Requirements'),
                '#default_value' => $details[$delta]->requirements,
                '#attributes' =>  isset($this->display_mode) ? ['readonly' => 'readonly'] : [], 
            	'#prefix' => '<div class="col-md-2 col-sm-10">',
                '#suffix' => '</div>',
            );

	    $form['field_container'][$delta]['checklist'] = array(
                '#type' => 'textarea',
                '#title' => t('Checklist'),
                '#default_value' => $details[$delta]->checklist,
                '#attributes' =>  isset($this->display_mode) ? ['readonly' => 'readonly'] : [], 
            	'#prefix' => '<div class="col-md-2 col-sm-10">',
                '#suffix' => '</div>',
            );

	    $form['field_container'][$delta]['evidence'] = array(
                '#type' => 'textarea',
                '#title' => t('Evidence'),
                '#default_value' => $details[$delta]->evidence,
                '#attributes' =>  isset($this->display_mode) ? ['readonly' => 'readonly'] : [], 
            	'#prefix' => '<div class="col-md-2 col-sm-10">',
                '#suffix' => '</div>',
            );

	    $form['field_container'][$delta]['comments'] = array(
                '#type' => 'textarea',
                '#title' => t('Comments'),
                '#default_value' => $details[$delta]->comments,
                '#attributes' =>  isset($this->display_mode) ? ['readonly' => 'readonly'] : [], 
            	'#prefix' => '<div class="col-md-2 col-sm-10">',
                '#suffix' => '</div>',
            );

	    $form['field_container'][$delta]['feedback'] = array(
                '#type' => 'textarea',
                '#title' => t('Feedback'),
                '#default_value' => $details[$delta]->feedback,
                '#attributes' =>  isset($this->display_mode) ? ['readonly' => 'readonly'] : [], 
            	'#prefix' => '<div class="col-md-2 col-sm-10">',
                '#suffix' => '</div>',
            );
	$curl = Url::fromRoute('formmoduleinsp_example_display', array('appinspformpk' => $appinspformpk, 'appinspdtlpk' => $details[$delta]->appinspdtlpk));
        $curl->setOptions($link_options1);
        $add_formmoduleinsp = \Drupal::l(t('-'), $curl);

        $form['field_container'][$delta]['submit'] = [
            '#markup' => $add_formmoduleinsp,
            '#prefix' => '<div class="col-md-2 col-sm-10">',
            '#suffix' => '</div>',
        ];
	    if (!isset($this->display_mode)) {
            $form['field_container'][$delta]['remove_name'] = array(
                '#type' => 'submit',
                '#value' => t('-'),
                '#submit' => array('::inspectionAjaxExampleAddMoreRemove'),
                '#ajax' => array(
                    'callback' => '::inspectionAjaxExampleAddMoreRemoveCallback',
                    'wrapper' => 'js-ajax-elements-wrapper',
                ),
                '#attributes' => array(
                    'class' => array('button-small'),
                ),
                '#name' => 'remove_name_' . $delta,
                '#prefix' => '<div class="col-md-1 col-sm-10">',
                '#suffix' => '</div>',
            );
	    }
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
	$edit_formfields = CustomUtils::addButton('forminspection_example_edit', array('appinspformpk' => $appinspformpk), 'medium', 'Edit '. $result['appinspformname'] . ' Form');

        $form['actions']['submitedit'] = [
            '#markup' => $edit_formfields,
            '#prefix' => ($i % 2 == 0) ? '<div class="col-md-6">&nbsp;</div><div class="col-md-6">&nbsp;</div><div class="kt-portlet__head-toolbar"><div class="kt-portlet__head-wrapper"><div class="kt-portlet__head-actions">' : '<div class="col-md-6">&nbsp;</div><div class="col-md-6">&nbsp;</div><div class="col-md-6"></div><div class="kt-portlet__head-toolbar"><div class="kt-portlet__head-wrapper"><div class="kt-portlet__head-actions">',
            '#suffix' => '</div></div></div>&nbsp;&nbsp;',
        ];
	/*$add_formfields = CustomUtils::addButton('formmoduleinsp_example.form', array('appinspformpk' => $appinspformpk), 'medium', 'Add '. $result['appinspformname'] . ' Form');

        $form['actions']['submitadd'] = [
            '#markup' => $add_formfields,
            '#prefix' => '<div class="kt-portlet__head-toolbar">
                                        <div class="kt-portlet__head-wrapper">
                                            <div class="kt-portlet__head-actions">',
            '#suffix' => '</div></div></div>&nbsp;&nbsp;',
        ];*/
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

	//$form['#attributes']['enctype'] = 'multipart/form-data';
       // $form['#attached']['library'][] = 'formmoduleinsp/formmoduleinsplib';
	$form['#attached']['library'][] = 'forminspection/forminspection';
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
                //    $form_state->setRedirect('formmoduleinsp_example_display', array('appinspformpk' => $this->appinspformpk, 'appinspdtlpk' => $returnval));
                }
                break;
            case 'EDIT':
                $returnval = Formmoduleinsp_biz::edit_formmoduleinsp($form, $form_state, $this->appinspformpk, $this->appinspdtlpk);
                if ($returnval == 'FAIL') {
                    
                } else {
                    drupal_set_message(t($this->appinspformname. " Form Updated Successfully"));
                //    $form_state->setRedirect('formmoduleinsp_example_display', array('appinspformpk' => $this->appinspformpk, 'appinspdtlpk' => $this->appinspdtlpk));
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
