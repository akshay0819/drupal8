<?php

namespace Drupal\forminspection\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Render\Element\Textarea;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\customutil\CustomUtils;
use Drupal\forminspection\Controller\Forminspection_biz;
use Drupal\forminspection\Controller\AutocompleteController;
use Drupal\file\Entity\File;


/**
 * Form with examples on how to use cache.
 */
class ForminspectionForm extends FormBase {

    public $appinspformpk;
      

    /**
     * {@inheritdoc}
     */
    public function getFormId() {
        return 'forminspection_example_form';
    }


    public function buildForm(array $form, FormStateInterface $form_state, $formmode = '', $appinspformpk = '') {

        $this->appinspformpk = $appinspformpk;
        $this->formmode = $formmode;
        $display_mode = FALSE;
        if ($this->formmode == 'DISPLAY') {

            $this->display_mode = TRUE;
        }

        $forminspectiondet = Forminspection_biz::getforminspectiondet($appinspformpk);
	$details = Forminspection_biz::getforminspectiondtl($appinspformpk);

        $form['formtitle'] = [
            '#markup' => '<i class="fa fa-gift"></i> &nbsp;Inspection Info',
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
            '#prefix' => '<div class="row"><div class="col-md-6">&nbsp;</div><div class="col-md-6">&nbsp;</div><div class="col-md-6">',
            '#suffix' => '</div>'
        ];
	}
        else {
	$edit_formfields = CustomUtils::addButton('forminspection_example_edit', array('appinspformpk' => $appinspformpk), 'medium', 'Edit Inspection');

        $form['buttons']['submitedit'] = [
            '#markup' => $edit_formfields,
            '#prefix' => '<div class="row"><div class="col-md-6">&nbsp;</div><div class="col-md-6">&nbsp;</div><div class="kt-portlet__head-toolbar">
                                        <div class="kt-portlet__head-wrapper">
                                            <div class="kt-portlet__head-actions">',
            '#suffix' => '</div></div></div>&nbsp;&nbsp;',
        ];
	$add_formfields = CustomUtils::addButton('forminspection_example.form', '', 'medium', 'Add Inspection');

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
        $url = Url::fromRoute('forminspection_example.list');
        $url->setOptions($link_options);
        $cancel_formmodulelink = \Drupal::l(t('Cancel'), $url);

        $form['buttons']['cancel'] = [
            '#markup' => $cancel_formmodulelink,
            '#prefix' => '<div class="kt-portlet__head-toolbar">
                                        <div class="kt-portlet__head-wrapper">
                                            <div class="kt-portlet__head-actions">',
            '#suffix' => isset($this->display_mode) ? '</div></div></div></div>' : '</div></div></div></div>',
        ];
	$form['formcoverstart']['#markup'] = '<div class="kt-portlet">';
        $form['formbody']['#markup'] = '<div class="kt-portlet__body">';
        
        $form['tabs'] = [
            '#markup' => '',
            '#prefix' => '<ul class="nav nav-tabs nav-tabs-line nav-tabs-bold nav-tabs-line-3x nav-tabs-line-brand" role="tablist">',
            '#suffix' => '</ul>',
        ];
        $form['tabs']['one'] = [
            '#markup' => '<a class="nav-link active" data-toggle="tab" href="#kt_tabs_2_1">Audit</a>',
            '#prefix' => '<li class="nav-item">',
            '#suffix' => '</li>',
        ];
        $form['tabs']['two'] = [
            '#markup' => '<a class="nav-link" data-toggle="tab" href="#kt_tabs_2_2">Feedback</a>',
            '#prefix' => '<li class="nav-item">',
            '#suffix' => '</li>',
        ];
        $form['tabs']['three'] = [
            '#markup' => '<a class="nav-link" data-toggle="tab" href="#kt_tabs_2_3">Completed</a>',
            '#prefix' => '<li class="nav-item">',
            '#suffix' => '</li>',
        ];

        $form['tabscontent'] = [
//            '#markup' => '',
            '#prefix' => '<div class="tab-content">',
            '#suffix' => '</div>',
        ];
        $form['tabscontent']['one'] = [
//            '#markup' =>  '',
            '#prefix' => '<div class="tab-pane active" id="kt_tabs_2_1" role="tabpanel">',
            '#suffix' => '</div>',
        ];
        $form['tabscontent']['two'] = [
//            '#markup' =>  '',
            '#prefix' => '<div class="tab-pane" id="kt_tabs_2_2" role="tabpanel">',
            '#suffix' => '</div>',
        ];
        $form['tabscontent']['three'] = [
            '#markup' => '',
            '#prefix' => '<div class="tab-pane" id="kt_tabs_2_3" role="tabpanel">',
            '#suffix' => '</div>',
        ];

        $form['tabscontent']['one']['productinfo1'] = $this->inspectiontab1($forminspectiondet);
        $form['tabscontent']['two']['productinfo2'] = $this->inspectiontab2($forminspectiondet);
        $form['tabscontent']['three']['productinfo3'] = $this->inspectiontab3($forminspectiondet);
        $form['formbodyend']['#markup'] = '</div>';
        $form['formcoverend']['#markup'] = '</div>';
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
            '#prefix' => '<div class="row"><div class="col-md-6">&nbsp;</div><div class="col-md-6">&nbsp;</div><div class="col-md-6">',
            '#suffix' => '</div>'
        ];
	}
        else {
	$edit_formfields = CustomUtils::addButton('forminspection_example_edit', array('appinspformpk' => $appinspformpk), 'medium', 'Edit Inspection');

        $form['actions']['submitedit'] = [
            '#markup' => $edit_formfields,
            '#prefix' => '<div class="row"><div class="col-md-6">&nbsp;</div><div class="col-md-6">&nbsp;</div><div class="kt-portlet__head-toolbar">
                                        <div class="kt-portlet__head-wrapper">
                                            <div class="kt-portlet__head-actions">',
            '#suffix' => '</div></div></div>&nbsp;&nbsp;',
        ];
	$add_formfields = CustomUtils::addButton('forminspection_example.form', '', 'medium', 'Add Inspection');

        $form['actions']['submitadd'] = [
            '#markup' => $add_formfields,
            '#prefix' => '<div class="kt-portlet__head-toolbar">
                                        <div class="kt-portlet__head-wrapper">
                                            <div class="kt-portlet__head-actions">',
            '#suffix' => '</div></div></div>&nbsp;&nbsp;',
        ];
	}
        $url = Url::fromRoute('forminspection_example.list');
        $url->setOptions($link_options);
        $cancel_formmodulelink = \Drupal::l(t('Cancel'), $url);

        $form['actions']['cancel'] = [
            '#markup' => $cancel_formmodulelink,
            '#prefix' => '<div class="kt-portlet__head-toolbar">
                                        <div class="kt-portlet__head-wrapper">
                                            <div class="kt-portlet__head-actions">',
            '#suffix' => '</div></div></div></div>',
        ];
        $form['formbodyend'] = [
            '#markup' => '</form></div></div>'
        ];


        $form['#attributes']['enctype'] = 'multipart/form-data';
        $form['#attached']['library'][] = 'forminspection/forminspection';
        return $form;
    }
    public function inspectiontab1($forminspectiondet) {

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
            '#title' => $this->t('Auditee User'),
            '#default_value' => $forminspectiondet['appinspauditor'],
            '#attributes' =>  isset($this->display_mode) ? ['readonly' => 'readonly'] : [], 
            '#prefix' => '<div class="col-md-6">',
            '#suffix' => '</div>'
        ];

	$form['appinspauditee'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Audited User'),
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
	

        return $form;
    }

    public function inspectiontab2($forminspectiondet) {

        $form['attachment'] = [
            '#type' => 'file',
            '#title' => t('Attachment'),
            '#prefix' => '<div class="row"><div class="col-md-12 col-sm-12">',
            '#suffix' => '</div></div>',
	    '#upload_location' => 'public://items'
	];
        return $form;
    }

    public function inspectiontab3($forminspectiondet) {
       // $prodtypes = Product_utils::productmaster_get_producttypes('ACCT');


        $form['date'] = [
            '#title' => $this->t('Date'),
            '#type' => 'date',
            '#attributes' => array('type' => 'date', 'min' => '-25 years', 'max' => '+5 years'),
            '#date_format' => 'd/m/Y',
            '#attributes' =>  isset($this->display_mode) ? ['readonly' => 'readonly'] : [], 
            '#prefix' => '<div class="col-md-6 col-sm-12">',
            '#suffix' => '</div></div>',
        ];


        return $form;
    }

     /**
     * @param array $form
     * @param \Drupal\Core\Form\FormStateInterface $form_state
     */
    function inspectionAjaxExampleAddMoreRemove(array &$form, FormStateInterface $form_state) {
        // Get the triggering item
        $delta_remove = $form_state->getTriggeringElement()['#parents'][1];

        // Store our form state
        $field_deltas_array = $form_state->get('field_deltas');

        // Find the key of the item we need to remove
        $key_to_remove = array_search($delta_remove, $field_deltas_array);

        // Remove our triggered element
        unset($field_deltas_array[$key_to_remove]);

        // Rebuild the field deltas values
        $form_state->set('field_deltas', $field_deltas_array);

        // Rebuild the form
        $form_state->setRebuild();

        // Return any messages set
        drupal_get_messages();
    }

    /**
     * @param array $form
     * @param \Drupal\Core\Form\FormStateInterface $form_state
     *
     * @return mixed
     */
    function inspectionAjaxExampleAddMoreRemoveCallback(array &$form, FormStateInterface $form_state) {
        return $form['field_container'];
    }

    /**
     * @param array $form
     * @param \Drupal\Core\Form\FormStateInterface $form_state
     */
    function inspectionAjaxExampleAddMoreAddOne(array &$form, FormStateInterface $form_state) {

        // Store our form state
        $field_deltas_array = $form_state->get('field_deltas');
        $fieldstotal = $form_state->get('arraycount');
        if (empty($fieldstotal)) {
            $fieldstotal = 0;
        };
        // check to see if there is more than one item in our array
        if (count($field_deltas_array) > 0) {
            // Add a new element to our array and set it to our highest value plus one
            $field_deltas_array[] = max($field_deltas_array) + 1;
        } else {
            // Set the new array element to 0
            $field_deltas_array[] = 0;
        }

        // Rebuild the field deltas values
        $form_state->set('field_deltas', $field_deltas_array);

        $form_state->setRebuild();

        // Return any messages set
        drupal_get_messages();
    }

    /**
     * @param array $form
     * @param \Drupal\Core\Form\FormStateInterface $form_state
     *
     * @return mixed
     */
    function inspectionAjaxExampleAddMoreAddOneCallback(array &$form, FormStateInterface $form_state) {
        return $form['field_container'];
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
            $form_state->setErrorByName('apkeyfields', $this->t('Please enter key fields'));
        }
 	$apmdgroupid = db_query("SELECT appinspformpk from {appmdgroup} WHERE apmdgroupid = :apmdgroupid AND appinspformpk <> :appinspformpk LIMIT 1", array(":apmdgroupid" => $form_state->getValue('apmdgroupid'), ":appinspformpk" => $this->appinspformpk))->fetchField();

        if (!empty($apmdgroupid)) {
            $form_state->setErrorByName('apmdgroupid', $this->t('There is already one Group with this id. Please enter different apmdgroupid'));
        }
        */
//        $form_state->setRebuild();
    }

    public function fapiExampleMultistepFormNextSubmit(array &$form, FormStateInterface $form_state) {
        $form_state->setRebuild();
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $appinspformpk = $form_state->getValue('appinspformpk');
        
       // $vals = $form_state->getValues();

        switch ($this->formmode) {
            case 'NEW':
                $returnval = Forminspection_biz::save_forminspection($form, $form_state);
                if ($returnval == 'FAIL') {                    
                } else {
                    drupal_set_message(t("Form Inspection Saved Successfully"));
                    $form_state->setRedirect('forminspection_example_display', array('appinspformpk' => $returnval));
                }
                break;
            case 'EDIT':
                $returnval = Forminspection_biz::edit_forminspection($form, $form_state, $this->appinspformpk);
                if ($returnval == 'FAIL') {
                    
                } else {
                    drupal_set_message(t("Form Inspection Updated Successfully"));
                    $form_state->setRedirect('forminspection_example_display', array('appinspformpk' => $this->appinspformpk));
                }
                break;
            default:
                $form_state->setRedirect('forminspection_example.list');
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
