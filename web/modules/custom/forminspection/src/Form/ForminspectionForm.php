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
            '#prefix' => '<div class="col-md-6">&nbsp;</div><div class="col-md-6">&nbsp;</div><div class="col-md-6">',
            '#suffix' => '</div>'
        ];
	}
        else {
	$edit_formfields = CustomUtils::addButton('forminspection_example_edit', array('appinspformpk' => $appinspformpk), 'medium', 'Edit Inspection');

        $form['buttons']['submitedit'] = [
            '#markup' => $edit_formfields,
            '#prefix' => '<div class="col-md-6">&nbsp;</div><div class="col-md-6">&nbsp;</div><div class="kt-portlet__head-toolbar">
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
            '#suffix' => isset($this->display_mode) ? '</div></div></div><div class="col-md-6"></div>' : '</div></div></div>',
        ];
        $form['appinspformid'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Inspection Form Id'),
            '#default_value' =>  ($form_state->getValue('appinspformid') != false)? $form_state->getValue('appinspformid') :$forminspectiondet['appinspformid'],
	    '#attributes' =>  isset($this->display_mode) ? ['readonly' => 'readonly'] : [], 
            '#prefix' => '<div class="col-md-6">',
            '#suffix' => '</div>'
        ];

        $form['appinspformname'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Form Name'),
            '#default_value' => ($form_state->getValue('appinspformname') != false)? $form_state->getValue('appinspformname') :$forminspectiondet['appinspformname'],
            '#attributes' =>  isset($this->display_mode) ? ['readonly' => 'readonly'] : [], 
            '#prefix' => '<div class="col-md-6">',
            '#suffix' => '</div>'
        ];

	$form['appinspauditor'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Auditee User'),
            '#default_value' => ($form_state->getValue('appinspauditor') != false)? $form_state->getValue('appinspauditor') :$forminspectiondet['appinspauditor'],
            '#attributes' =>  isset($this->display_mode) ? ['readonly' => 'readonly'] : [], 
            '#prefix' => '<div class="col-md-6">',
            '#suffix' => '</div>'
        ];

	$form['appinspauditee'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Audited User'),
            '#default_value' => ($form_state->getValue('appinspauditee') != false)? $form_state->getValue('appinspauditee') :$forminspectiondet['appinspauditee'],
            '#attributes' =>  isset($this->display_mode) ? ['readonly' => 'readonly'] : [], 
            '#prefix' => '<div class="col-md-6">',
            '#suffix' => '</div>'
        ];

	$form['appauditdate'] = [
            '#type' => 'date',
            '#title' => $this->t('Audit Date'),
            '#default_value' => ($form_state->getValue('appauditdate') != false)? $form_state->getValue('appauditdate') : (!empty($forminspectiondet['appauditdate']) ? date('Y-m-d', strtotime($forminspectiondet['appauditdate'])) : date('Y-m-d')),
            '#attributes' =>  isset($this->display_mode) ? ['readonly' => 'readonly'] : [], 
            '#prefix' => '<div class="col-md-6">',
            '#suffix' => '</div>'
        ];
	$form['appinspstatus'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Inspection Status'),
            '#default_value' => ($form_state->getValue('appinspstatus') != false)? $form_state->getValue('appinspstatus') :$forminspectiondet['appinspstatus'],
            '#attributes' =>  isset($this->display_mode) ? ['readonly' => 'readonly'] : [], 
            '#prefix' => '<div class="col-md-6">',
            '#suffix' => '</div>'
        ];
	$form['appinspcomments'] = [
            '#type' => 'textarea',
            '#title' => $this->t('Auditee Comments'),
            '#default_value' => ($form_state->getValue('appinspcomments') != false)? $form_state->getValue('appinspcomments') :$forminspectiondet['appinspcomments'],
            '#attributes' =>  isset($this->display_mode) ? ['readonly' => 'readonly'] : [], 
            '#prefix' => '<div class="col-md-6">',
            '#suffix' => '</div>'
        ];
	$form['appinspfeedback'] = [
            '#type' => 'textarea',
            '#title' => $this->t('Auditor Feedback'),
            '#default_value' => ($form_state->getValue('appinspfeedback') != false)? $form_state->getValue('appinspfeedback') :$forminspectiondet['appinspfeedback'],
            '#attributes' =>  isset($this->display_mode) ? ['readonly' => 'readonly'] : [], 
            '#prefix' => '<div class="col-md-6">',
            '#suffix' => '</div>'
        ];
	$form['field_container'] = array(
            '#type' => 'container',
//            '#weight' => 80,
            '#tree' => TRUE,
            // Set up the wrapper so that AJAX will be able to replace the fieldset.
            '#prefix' => '<div id="js-ajax-elements-wrapper">',
            '#suffix' => '</div>',
        );
        $count = count($details);
//drupal_set_message($form_state->get('field_deltas'));
        if ($form_state->get('field_deltas') == '') {
            $form_state->set('field_deltas', range(0, $count - 1));
        }

        $field_count = $form_state->get('field_deltas');
        // if ($form_state->get('arraycount') == '') {
        $form_state->set('arraycount', $count);
//        }


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
                '#title' => t('Sl No'),
                '#default_value' => isset($details[$delta]->slno) ? $details[$delta]->slno : $delta,
		'#attributes' => array('readonly' => 'readonly'),
                '#prefix' => '<div class="col-md-5 col-sm-10">',
                '#suffix' => '</div>',
            );

            $form['field_container'][$delta]['chapter'] = array(
                '#type' => 'textfield',
                '#title' => t('Chapter No'),
                '#default_value' => $details[$delta]->chapter,
		'#maxlength' => 1000,
                '#prefix' => '<div class="col-md-5 col-sm-10">',
                '#suffix' => '</div>',
            );

            $form['field_container'][$delta]['requirements'] = array(
                '#type' => 'textarea',
                '#title' => t('Requirements'),
                '#default_value' => $details[$delta]->requirements,
                '#prefix' => '<div class="col-md-5 col-sm-10">',
                '#suffix' => '</div>',
            );

	    $form['field_container'][$delta]['checklist'] = array(
                '#type' => 'textarea',
                '#title' => t('Checklist'),
                '#default_value' => $details[$delta]->checklist,
                '#prefix' => '<div class="col-md-5 col-sm-10">',
                '#suffix' => '</div>',
            );

	    $form['field_container'][$delta]['evidence'] = array(
                '#type' => 'textarea',
                '#title' => t('Evidence'),
                '#default_value' => $details[$delta]->evidence,
                '#prefix' => '<div class="col-md-5 col-sm-10">',
                '#suffix' => '</div>',
            );

	    $form['field_container'][$delta]['comments'] = array(
                '#type' => 'textarea',
                '#title' => t('Comments'),
                '#default_value' => $details[$delta]->comments,
                '#prefix' => '<div class="col-md-5 col-sm-10">',
                '#suffix' => '</div>',
            );

	    $form['field_container'][$delta]['feedback'] = array(
                '#type' => 'textarea',
                '#title' => t('Feedback'),
                '#default_value' => $details[$delta]->feedback,
                '#prefix' => '<div class="col-md-5 col-sm-10">',
                '#suffix' => '</div>',
            );

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
                '#prefix' => '<div class="col-md-2 col-sm-10">',
                '#suffix' => '</div>',
            );
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
	$edit_formfields = CustomUtils::addButton('forminspection_example_edit', array('appinspformpk' => $appinspformpk), 'medium', 'Edit Inspection');

        $form['actions']['submitedit'] = [
            '#markup' => $edit_formfields,
            '#prefix' => '<div class="col-md-6">&nbsp;</div><div class="col-md-6">&nbsp;</div><div class="kt-portlet__head-toolbar">
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
            '#suffix' => '</div></div></div>',
        ];
        $form['formbodyend'] = [
            '#markup' => '</form></div></div>'
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
//        $form_state->setReinspection();
    }

    public function fapiExampleMultistepFormNextSubmit(array &$form, FormStateInterface $form_state) {
        $form_state->setReinspection();
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $appinspformpk = $form_state->getValue('appinspformpk');
        
        $vals = $form_state->getValues();

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
        $form_state->setReinspection();
        return $form['textfields_container'];
    }

}
