<?php

namespace Drupal\formmoduleinsp\Controller;

use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\customutil\CustomUtils;

Class FormmoduleinspController {

    public function formmoduleinsplist($appinspformpk) {

        
	$headerquery = db_select('appinspectionform', 'a');
        $headerquery->fields('a');
        $headerquery->condition('appinspformpk', $appinspformpk, '=');
        $result = $headerquery->execute()->fetchAssoc();
	$header = array(
            'slno' => t('Sl. No'),
            'chapter' => t('Chapter'),
            'requirements' => t('Requirements'),
            'checklist' => t('Checklist'),
            'evidence' => t('Evidence'),
            'comments' => t('Comments'),
            'operations' => t('Edit'),
        //    'deletecomp' => t('Delete'),
        );

        
        $rows = array();
        // $getlist=Product_Biz::getproductlist();
        $query = db_select('appinspectiondtl', 'a');
        $query->fields('a');
        $query->condition('appinspformpk', $appinspformpk, '=');
        

        $getlist = $query->execute();
       


        $link_options = array('attributes' => array('class' => array('btn btn-xs default btn-editable'),),);
        $link_options_delete = array('attributes' => array('class' => array('btn', 'btn-danger'),),);
        foreach ($getlist as $item) {
            $idarray = array('appinspformpk' => $appinspformpk, 'appformpk' => 1);
            $edit_forminspection = CustomUtils::editButton('formmoduleinsp_example_edit', $idarray, 'extrasmall', 'Edit');
          //  $delete_forminspection = CustomUtils::deleteButton('formmoduleinsp_example_delete', $idarray, 'extrasmall', 'Delete');
            $dispurl = Url::fromRoute('formmoduleinsp_example_display', array('appinspformpk' => $appinspformpk, 'appformpk' => 1));
            $display_forminspection = \Drupal::l(t($item->slno), $dispurl);

            // Row with attributes on the row and some of its cells.
            $rows[] = array(
                'data' => array($display_forminspection, $item->chapter, $item->requirements, $item->checklist, $item->evidence, $item->comments, $edit_forminspection)
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
                                    List of ' . $result['appinspformname'] . '
                                </h3>
                            </div>',
            '#prefix' => '<div class="kt-portlet__head kt-portlet__head--lg">',
            '#suffix' => '</div>'
        ];

//          $url = Url::fromUri('internal:/product/new',NULL);
//        $url = Url::fromRoute('mymoduleinsp_addcompany');
//        $url->setOptions($link_options);
//        $add_companylink = \Drupal::l(t('Add Company'), $url);
        $add_formmoduleinsp = CustomUtils::addButton('formmoduleinsp_example.form', array('appinspformpk' => $appinspformpk), 'medium', 'Add ' . $result['appinspformname'].' Form');

        $form['tablebody']['table_heading']['submit'] = [
            '#markup' => $add_formmoduleinsp,
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


        return $form;
    }

}
