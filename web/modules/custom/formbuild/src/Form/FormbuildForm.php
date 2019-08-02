<?php

namespace Drupal\formbuild\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\customutil\CustomUtils;
use Drupal\formbuild\Controller\Formbuild_biz;
use Drupal\formbuild\Controller\AutocompleteController;


/**
 * Form with examples on how to use cache.
 */
class FormbuildForm extends FormBase {

    public $apmdgpk;
      

    /**
     * {@inheritdoc}
     */
    public function getFormId() {
        return 'formbuild_example_form';
    }


    public function buildForm(array $form, FormStateInterface $form_state, $formmode = '', $apmdgpk = '') {

        $this->apmdgpk = $apmdgpk;
        $this->formmode = $formmode;
        $display_mode = FALSE;
        if ($this->formmode == 'DISPLAY') {

            $this->display_mode = TRUE;
        }

        $formbuilddet = Formbuild_biz::getformbuilddet($apmdgpk);

        $form['formtitle'] = [
            '#markup' => '<i class="fa fa-gift"></i> &nbsp;Template Info',
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
	$edit_formfields = CustomUtils::addButton('formbuild_example_edit', array('apmdgpk' => $apmdgpk), 'medium', 'Edit Form Template');

        $form['buttons']['submitedit'] = [
            '#markup' => $edit_formfields,
            '#prefix' => '<div class="col-md-6">&nbsp;</div><div class="col-md-6">&nbsp;</div><div class="kt-portlet__head-toolbar">
                                        <div class="kt-portlet__head-wrapper">
                                            <div class="kt-portlet__head-actions">',
            '#suffix' => '</div></div></div>&nbsp;&nbsp;',
        ];
	$add_formfields = CustomUtils::addButton('formbuild_example.form', '', 'medium', 'Add Form Template');

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
        $url = Url::fromRoute('formbuild_example.list');
        $url->setOptions($link_options);
        $cancel_formmodulelink = \Drupal::l(t('Cancel'), $url);

        $form['buttons']['cancel'] = [
            '#markup' => $cancel_formmodulelink,
            '#prefix' => '<div class="kt-portlet__head-toolbar">
                                        <div class="kt-portlet__head-wrapper">
                                            <div class="kt-portlet__head-actions">',
            '#suffix' => isset($this->display_mode) ? '</div></div></div><div class="col-md-6"></div>' : '</div></div></div>',
        ];
        $form['apmdgroupid'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Template Code'),
            '#default_value' =>  ($form_state->getValue('apmdgroupid') != false)? $form_state->getValue('apmdgroupid') :$formbuilddet['apmdgroupid'],
	    '#attributes' =>  isset($this->display_mode) ? ['readonly' => 'readonly'] : [], 
            '#prefix' => '<div class="col-md-6">',
            '#suffix' => '</div>'
        ];

        $form['apmdgroupname'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Template Name'),
            '#default_value' => ($form_state->getValue('apmdgroupname') != false)? $form_state->getValue('apmdgroupname') :$formbuilddet['apmdgroupname'],
            '#attributes' =>  isset($this->display_mode) ? ['readonly' => 'readonly'] : [], 
            '#prefix' => '<div class="col-md-6">',
            '#suffix' => '</div>'
        ];

	$form['apmdfields'] = [
            '#type' => 'textfield',
            '#autocomplete_route_name' => 'formbuild_example.autocomplete',
	    '#title' => $this->t('Template Fields'),
            '#default_value' => ($form_state->getValue('apmdfields') != false) ? $form_state->getValue('apmdfields') : implode(",", json_decode($formbuilddet['apmdfields'], true)),
            '#attributes' =>  isset($this->display_mode) ? ['readonly' => 'readonly'] : [], 
            '#prefix' => '<div class="col-md-6">',
            '#suffix' => '</div>'
        ];

        
	$form['aptablefields'] = [
            '#type' => 'textfield',
            '#autocomplete_route_name' => 'formbuild_example.autocomplete',
	    '#title' => $this->t('Template Table Fields'),
            '#default_value' => ($form_state->getValue('aptablefields') != false) ? $form_state->getValue('aptablefields') : implode(",", json_decode($formbuilddet['aptablefields'], true)),
            '#attributes' =>  isset($this->display_mode) ? ['readonly' => 'readonly'] : [], 
            '#prefix' => '<div class="col-md-6">',
            '#suffix' => '</div>'
        ];

        
        $form['apkeyfields'] = [
            '#type' => 'textfield',
            '#autocomplete_route_name' => 'formbuild_example.autocomplete',
	    '#title' => $this->t('Template Keys'),
            '#rows' => 2,
            '#default_value' => ($form_state->getValue('apkeyfields') != false) ? $form_state->getValue('apkeyfields') : implode(",", json_decode($formbuilddet['apkeyfields'], true)),
            '#attributes' =>  isset($this->display_mode) ? ['readonly' => 'readonly'] : [], 
            '#prefix' => '<div class="col-md-6">',
            '#suffix' => '</div>'
        ];
	if (!isset($this->display_mode)) {
	$form['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Submit'),
            '#prefix' => '<div class="col-md-6"></div><div class="col-md-6">&nbsp;</div><div class="col-md-6">&nbsp;</div><div class="col-md-6">',
            '#suffix' => '</div>'
        ];
	}
        else {
	$edit_formfields = CustomUtils::addButton('formbuild_example_edit', array('apmdgpk' => $apmdgpk), 'medium', 'Edit Form Template');

        $form['actions']['submitedit'] = [
            '#markup' => $edit_formfields,
            '#prefix' => '<div class="col-md-6">&nbsp;</div><div class="col-md-6">&nbsp;</div><div class="col-md-6"></div><div class="kt-portlet__head-toolbar">
                                        <div class="kt-portlet__head-wrapper">
                                            <div class="kt-portlet__head-actions">',
            '#suffix' => '</div></div></div>&nbsp;&nbsp;',
        ];
	$add_formfields = CustomUtils::addButton('formbuild_example.form', '', 'medium', 'Add Form Template');

        $form['actions']['submitadd'] = [
            '#markup' => $add_formfields,
            '#prefix' => '<div class="kt-portlet__head-toolbar">
                                        <div class="kt-portlet__head-wrapper">
                                            <div class="kt-portlet__head-actions">',
            '#suffix' => '</div></div></div>&nbsp;&nbsp;',
        ];
	}
        $url = Url::fromRoute('formbuild_example.list');
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
 	if ($form_state->getValue('apmdgroupid') == '') {
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
 	$apmdgroupid = db_query("SELECT apmdgpk from {appmdgroup} WHERE apmdgroupid = :apmdgroupid AND apmdgpk <> :apmdgpk LIMIT 1", array(":apmdgroupid" => $form_state->getValue('apmdgroupid'), ":apmdgpk" => $this->apmdgpk))->fetchField();

        if (!empty($apmdgroupid)) {
            $form_state->setErrorByName('apmdgroupid', $this->t('There is already one Group with this id. Please enter different apmdgroupid'));
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
        $apmdgpk = $form_state->getValue('apmdgpk');
        // drupal_set_message(t('An error occurred and processing did not complete.'), 'error');


        // $values = $form_state->getValues();

        // $insertid = db_insert('tragformbuild')
        //         ->fields(array(
        //             'formbuildno' => $values['formbuildno'],
        //             'formbuilddate' => $values['formbuilddate'],
        //             'packdate' => $values['packdate'],
        //             'usebydate' => $values['usebydate'],
        //             'netweight' => $values['netweight'],
        //             'packedweight' => $values['packedweight'],
        //             'formbuildtext' => $values['formbuildtext'],
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
        //                         // 'updatedby' => $values['formbuildtext'],
        //                 ))
        //                 ->execute();
        //     }
        // }

        // //$form_state->setRebuild();
        // $form_state->setRedirect('formbuild_example.list');

        $vals = $form_state->getValues();

        switch ($this->formmode) {
            case 'NEW':
                $returnval = Formbuild_biz::save_formbuild($form, $form_state);
                if ($returnval == 'FAIL') {                    
                } else {
                    drupal_set_message(t("Form Template Saved Successfully"));
                    $form_state->setRedirect('formbuild_example_display', array('apmdgpk' => $returnval));
                }
                break;
            case 'EDIT':
                $returnval = Formbuild_biz::edit_formbuild($form, $form_state, $this->apmdgpk);
                if ($returnval == 'FAIL') {
                    
                } else {
                    drupal_set_message(t("Form Template Updated Successfully"));
                    $form_state->setRedirect('formbuild_example_display', array('apmdgpk' => $this->apmdgpk));
                }
                break;
            default:
                $form_state->setRedirect('formbuild_example.list');
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
