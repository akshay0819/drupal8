<?php

namespace Drupal\formfields\Controller;

use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\customutil\CustomUtils;

Class FormfieldsController {

    public function formfieldslist() {

        $header = array(
            'apmdpk' => t('Field Name'),
            'apmdtype' => t('Field Type'),
            'apmdlength' => t('Field Length'),
            'apmddesc' => t('Field Desc'),
            'operations' => t('Edit'),
            'deletecomp' => t('Delete'),
        );
        

        $rows = array();
        // $getlist=Product_Biz::getproductlist();
        $query = db_select('appmetadata', 'a');
        $query->fields('a');
        $query->orderBy('a.apmdname', 'ASC');

        $getlist = $query->execute();
       
	$types = CustomUtils::getCodevaluesFormCodetype('MDTY');

        $link_options = array('attributes' => array('class' => array('btn btn-xs default btn-editable'),),);
        $link_options_delete = array('attributes' => array('class' => array('btn', 'btn-danger'),),);
        foreach ($getlist as $item) {
            $idarray = array('apmdpk' => $item->apmdpk);
            $edit_formfields = CustomUtils::editButton('formfields_example_edit', $idarray, 'extrasmall', 'Edit');
            $delete_formfields = CustomUtils::deleteButton('formfields_example_delete', $idarray, 'extrasmall', 'Delete');
            $dispurl = Url::fromRoute('formfields_example_display', array('apmdpk' => $item->apmdpk));
            $display_formfields = \Drupal::l(t($item->apmdname), $dispurl);

            // Row with attributes on the row and some of its cells.
            $rows[] = array(
                'data' => array($display_formfields, $types[$item->apmdtype], $item->apmdlength, $item->apmddesc, $edit_formfields, $delete_formfields)
            );
        }

        $form['tablebody'] = array(
            '#markup' => '',
            '#prefix' => '<div class="kt-portlet kt-portlet--mobile">',
            '#suffix' => '</div>'
        );

        $form['tablebody']['table_heading'] = [
            '#markup' => '<div class="kt-portlet__head-label">
                                <span class="kt-portlet__head-icon">
                                    <i class="kt-font-brand flaticon2-line-chart"></i>
                                </span>
                                <h3 class="kt-portlet__head-title">
                                    List of Form Fields
                                </h3>
                            </div>',
            '#prefix' => '<div class="kt-portlet__head kt-portlet__head--lg">',
            '#suffix' => '</div>'
        ];

//          $url = Url::fromUri('internal:/product/new',NULL);
//        $url = Url::fromRoute('mymodule_addcompany');
//        $url->setOptions($link_options);
//        $add_companylink = \Drupal::l(t('Add Company'), $url);
        $add_formfields = CustomUtils::addButton('formfields_example.form', '', 'medium', 'Add Form Field');

        $form['tablebody']['table_heading']['submit'] = [
            '#markup' => $add_formfields,
            '#prefix' => '<div class="kt-portlet__head-toolbar">
                                        <div class="kt-portlet__head-wrapper">
                                            <div class="kt-portlet__head-actions">',
            '#suffix' => '</div></div></div>',
        ];




        $form['tablebody']['company_table'] = array(
            '#theme' => 'table',
            '#header' => $header,
            '#rows' => $rows,
            '#attributes' => array(
                'id' => 'kt_table_1',
                'class' => "table table-striped- table-bordered table-hover"
            ),
            '#prefix' => '<div class="kt-portlet__body">',
            '#suffix' => '</div>',
        );
//        $form['pager'] = array(
//            '#type' => 'pager'
//        );
   //     $form['#attached']['library'][] = 'productmaster/productmasterlib';


        return $form;
    }

}
