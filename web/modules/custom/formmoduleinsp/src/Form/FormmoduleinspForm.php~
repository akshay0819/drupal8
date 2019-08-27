<?php

namespace Drupal\formmoduleinsp\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\file\Entity\File;
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


    public function buildForm(array $form, FormStateInterface $form_state, $formmode = '', $appinspformpk = '', $appinspdtlpk = '') {

        $this->appinspformpk = $appinspformpk;
        $this->appinspdtlpk = $appinspdtlpk;
        $this->formmode = $formmode;
        $display_mode = FALSE;
        if ($this->formmode == 'DISPLAY') {

            $this->display_mode = TRUE;
        }

        $formmoduleinspdet = Formmoduleinsp_biz::getformmoduleinspdet($appinspformpk, $appinspdtlpk);

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
	$edit_formfields = CustomUtils::addButton('formmoduleinsp_example_edit', array('appinspformpk' => $appinspformpk, 'appinspdtlpk' => $appinspdtlpk), 'medium', 'Edit '. $result['appinspformname'] . ' Form');
        
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
        $url = Url::fromRoute('formmodulelistinsp_example_display', array('appinspformpk' => $appinspformpk));
        $url->setOptions($link_options);
        $cancel_formmoduleinsplink = \Drupal::l(t('Cancel'), $url);

        $form['buttons']['cancel'] = [
            '#markup' => $cancel_formmoduleinsplink,
            '#prefix' => '<div class="kt-portlet__head-toolbar">
                                        <div class="kt-portlet__head-wrapper">
                                            <div class="kt-portlet__head-actions">',
            '#suffix' => '</div></div></div>',
        ];
        $form['chapter'] = [
                '#type' => 'textarea',
                '#title' => t('Chapter No'),
                '#default_value' => $formmoduleinspdet['chapter'],
		'#attributes' =>  ['readonly' => 'readonly', 'style' => 'background:#F2F3F8;'], 
            	'#prefix' => '<div class="row"><div class="col-md-6">',
                '#suffix' => '</div>',
            ];

            $form['requirements'] = [
                '#type' => 'textarea',
                '#title' => t('Requirements'),
                '#default_value' =>$formmoduleinspdet['requirements'],
                '#attributes' =>  ['readonly' => 'readonly', 'style' => 'background:#F2F3F8;'], 
            	'#prefix' => '<div class="col-md-6">',
                '#suffix' => '</div>',
            ];

	    $form['checklist'] = [
                '#type' => 'textarea',
                '#title' => t('Checklist'),
                '#default_value' => $formmoduleinspdet['checklist'],
                '#attributes' =>  ['readonly' => 'readonly', 'style' => 'background:#F2F3F8;'], 
            	'#prefix' => '<div class="col-md-6">',
                '#suffix' => '</div>',
            ];

	    $form['evidence'] = [
                '#type' => 'textarea',
                '#title' => t('Evidence'),
                '#default_value' => $formmoduleinspdet['evidence'],
                '#attributes' =>  ['readonly' => 'readonly', 'style' => 'background:#F2F3F8;'], 
            	'#prefix' => '<div class="col-md-6">',
                '#suffix' => '</div>',
            ];

	    $form['comments'] = [
                '#type' => 'textarea',
                '#title' => t('Comments'),
                '#default_value' => $formmoduleinspdet['comments'],
                '#attributes' =>  isset($this->display_mode) ? ['readonly' => 'readonly', 'style' => 'background:#F2F3F8;'] : [], 
            	'#prefix' => '<div class="col-md-6">',
                '#suffix' => '</div>',
            ];

	    $form['feedback'] = [
                '#type' => 'textarea',
                '#title' => t('Feedback'),
                '#default_value' => $formmoduleinspdet['feedback'],
                '#attributes' =>  isset($this->display_mode) ? ['readonly' => 'readonly', 'style' => 'background:#F2F3F8;'] : [], 
            	'#prefix' => '<div class="col-md-6">',
                '#suffix' => '</div>',
            ];
	    $form['docstatus'] = [
                '#type' => 'range',
                '#title' => t('Document Status'),
                '#default_value' => $formmoduleinspdet['docstatus'],
		'#attributes' => ['onmouseover' => '$(this).attr("title", this.value);'], 
            	'#disabled' => isset($this->display_mode) ? TRUE : FALSE,
                '#prefix' => '<div class="col-md-6">',
                '#suffix' => '</div>',
            ];
	    $form['compstatus'] = [
                '#type' => 'range',
                '#title' => t('Completion Status'),
                '#default_value' => $formmoduleinspdet['compstatus'],
                '#attributes' => ['onmouseover' => '$(this).attr("title", this.value);'], 
            	'#disabled' => isset($this->display_mode) ? TRUE : FALSE,
                '#prefix' => '<div class="col-md-6">',
                '#suffix' => '</div>',
            ];
	$options = ['Closed' => 'Closed', 'Open' => 'Open'];
	$form['status'] = [
            '#type' => isset($this->display_mode) ? 'textfield' : 'select',
            '#title' => $this->t('Status'),
	    '#options' => $options,
            '#default_value' =>$formmoduleinspdet['status'],
            '#attributes' =>  isset($this->display_mode) ? ['readonly' => 'readonly', 'style' => 'background:#F2F3F8;'] : [], 
            '#prefix' => '<div class="col-md-6">',
            '#suffix' => '</div></div>'
        ];
	$form['attachment'] = [
            '#type' => 'managed_file',
            '#title' => t('Attachment'),
            '#attributes' =>  isset($this->display_mode) ? ['disabled' => 'disabled'] : [], 
	    '#multiple' => TRUE,
	    '#disabled' => isset($this->display_mode) ? TRUE : FALSE,
            '#upload_location' => 'public://items',
	    '#upload_validators' => array(
	       'file_validate_extensions' => array('pdf doc docx rtf txt xls xlsx csv bmp jpg jpeg png gif tiff'),
	       'file_validate_size' => array(25600000),
	     ),
	];
	if (!isset($this->display_mode)) {
	$form['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Submit'),
            '#prefix' => '<div class="col-md-6">',
            '#suffix' => '</div>'
        ];
	}
        else {
	global $base_url;
	$query = db_select('file_managed', 'a');
	$query->join('file_usage', 'b', 'b.fid = a.fid');
        $query->fields('a');
        $query->condition('b.type', $appinspformpk.'-'.$appinspdtlpk, '=');
        $files = $query->execute();
	$i = 0;
        foreach($files as $file) {
	  $form[$i] = ['#markup' => '<div class="col-md-12"><a href="'.$base_url.'/sites/default/files/items/'.$file->filename.'" target="_blank">'.$file->filename.'</a></div>'];
	  $i++;
	}
	$edit_formfields = CustomUtils::addButton('formmoduleinsp_example_edit', array('appinspformpk' => $appinspformpk, 'appinspdtlpk' => $appinspdtlpk), 'medium', 'Edit '. $result['appinspformname'] . ' Form');

        $form['actions']['submitedit'] = [
            '#markup' => $edit_formfields,
            '#prefix' => '<div class="kt-portlet__head-toolbar"><div class="kt-portlet__head-wrapper"><div class="kt-portlet__head-actions">',
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
        $url = Url::fromRoute('formmodulelistinsp_example_display', array('appinspformpk' => $appinspformpk));
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

	$form['#attributes']['enctype'] = 'multipart/form-data';
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
                    $form_state->setRedirect('formmoduleinsp_example_display', array('appinspformpk' => $this->appinspformpk, 'appinspdtlpk' => $returnval));
                }
                break;
            case 'EDIT':
                $returnval = Formmoduleinsp_biz::edit_formmoduleinsp($form, $form_state, $this->appinspformpk, $this->appinspdtlpk);
                if ($returnval == 'FAIL') {
                    
                } else {
                    drupal_set_message(t($this->appinspformname. " Form Updated Successfully"));
                    $form_state->setRedirect('formmoduleinsp_example_display', array('appinspformpk' => $this->appinspformpk, 'appinspdtlpk' => $this->appinspdtlpk));
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
