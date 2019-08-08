<?php

namespace Drupal\forminspection\Controller;

use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\customutil\CustomUtils;

Class ForminspectionController {

    public function forminspectionlist() {

        $header = array(
            'appinspformid' => t('Inspection ID'),
            'appinspformname' => t('Form Name'),
            'appinspauditor' => t('Auditee User'),
            'appinspauditee' => t('Audited User'),
            'appauditdate' => t('Audit Date'),
            'appinspstatus' => t('GoTo'),
            'operations' => t('Edit'),
            'deletecomp' => t('Delete'),
        );
        

        $rows = array();
        // $getlist=Product_Biz::getproductlist();
        $query = db_select('appinspectionform', 'a');
        $query->fields('a');
      

        $getlist = $query->execute();
       


        $link_options = array('attributes' => array('class' => array('btn btn-xs default btn-editable'),),);
        $link_options_delete = array('attributes' => array('class' => array('btn', 'btn-danger'),),);
        foreach ($getlist as $item) {
            $idarray = array('appinspformpk' => $item->appinspformpk);
            $goto_forminspection = CustomUtils::editButton('formmoduleinsp_example.form', $idarray, 'extrasmall', 'GoTo Form');
            $edit_forminspection = CustomUtils::editButton('forminspection_example_edit', $idarray, 'extrasmall', 'Edit');
            $delete_forminspection = CustomUtils::deleteButton('forminspection_example_delete', $idarray, 'extrasmall', 'Delete');
            $dispurl = Url::fromRoute('forminspection_example_display', array('appinspformpk' => $item->appinspformpk));
            $display_forminspection = \Drupal::l(t($item->appinspformid), $dispurl);

            // Row with attributes on the row and some of its cells.
            $rows[] = array(
                'data' => array($display_forminspection, $item->appinspformname, $item->appinspauditor, $item->appinspauditee, date('m / d / Y', strtotime($item->appauditdate)), $goto_forminspection, $edit_forminspection, $delete_forminspection)
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
                                    List of Inspection
                                </h3>
                            </div>',
            '#prefix' => '<div class="kt-portlet__head kt-portlet__head--lg">',
            '#suffix' => '</div>'
        ];

//          $url = Url::fromUri('internal:/product/new',NULL);
//        $url = Url::fromRoute('mymodule_addcompany');
//        $url->setOptions($link_options);
//        $add_companylink = \Drupal::l(t('Add Company'), $url);
        $add_forminspection = CustomUtils::addButton('forminspection_example.form', '', 'medium', 'Add Inspection Form');

        $form['tablebody']['table_heading']['submit'] = [
            '#markup' => $add_forminspection,
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
