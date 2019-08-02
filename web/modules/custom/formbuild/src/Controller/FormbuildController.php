<?php

namespace Drupal\formbuild\Controller;

use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\customutil\CustomUtils;

Class FormbuildController {

    public function formbuildlist() {

        $header = array(
            'apmdgpk' => t('ID'),
            'apmdgroupname' => t('Form Template Name'),
            'goto' => t('GoTo'),
            'operations' => t('Edit'),
            'deletecomp' => t('Delete'),
        );
        

        $rows = array();
        // $getlist=Product_Biz::getproductlist();
        $query = db_select('appmdgroup', 'a');
        $query->fields('a');
      

        $getlist = $query->execute();
       


        $link_options = array('attributes' => array('class' => array('btn btn-xs default btn-editable'),),);
        $link_options_delete = array('attributes' => array('class' => array('btn', 'btn-danger'),),);
        foreach ($getlist as $item) {
            $idarray = array('apmdgpk' => $item->apmdgpk);
            $goto_formbuild = CustomUtils::editButton('formmodule_example.list', $idarray, 'extrasmall', 'GoTo Form');
            $edit_formbuild = CustomUtils::editButton('formbuild_example_edit', $idarray, 'extrasmall', 'Edit');
            $delete_formbuild = CustomUtils::deleteButton('formbuild_example_delete', $idarray, 'extrasmall', 'Delete');
            $dispurl = Url::fromRoute('formbuild_example_display', array('apmdgpk' => $item->apmdgpk));
            $display_formbuild = \Drupal::l(t($item->apmdgroupname), $dispurl);

            // Row with attributes on the row and some of its cells.
            $rows[] = array(
                'data' => array($item->apmdgroupid, $display_formbuild, $goto_formbuild, $edit_formbuild, $delete_formbuild)
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
                                    List of Form Templates
                                </h3>
                            </div>',
            '#prefix' => '<div class="kt-portlet__head kt-portlet__head--lg">',
            '#suffix' => '</div>'
        ];

//          $url = Url::fromUri('internal:/product/new',NULL);
//        $url = Url::fromRoute('mymodule_addcompany');
//        $url->setOptions($link_options);
//        $add_companylink = \Drupal::l(t('Add Company'), $url);
        $add_formbuild = CustomUtils::addButton('formbuild_example.form', '', 'medium', 'Add Form Template');

        $form['tablebody']['table_heading']['submit'] = [
            '#markup' => $add_formbuild,
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
       // $form['#attached']['library'][] = 'productmaster/productmasterlib';


        return $form;
    }

}
