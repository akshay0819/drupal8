<?php

namespace Drupal\productmaster\Controller;

use Drupal\productmaster\Product_Biz;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\customutil\CustomUtils;

Class ProductController {

    public function productslist() {

        $form['#attached']['library'][] = 'productmaster/productmaster.lib';
        $header = array(
            'id' => t('Product'),
            'name' => t('Description'),
            'message' => t('Class'),
            'producttype' => t('Type'),
            'operations' => t('Edit'),
            'dleteprod' => t('Delete'),
        );

        $rows = array();
        // $getlist=Product_Biz::getproductlist();
        $query = db_select('frtproduct', 'prod');
        $query->fields('prod');
        $table_sort = $query->extend('Drupal\Core\Database\Query\TableSortExtender')->orderByHeader($header);
        $pager = $table_sort->extend('Drupal\Core\Database\Query\PagerSelectExtender')->limit(10);
        $getlist = $pager->execute();


        $link_options = array(
            'attributes' => array('class' => array(
                    'btn', 'btn btn-sm default btn-editable',
                ),),
        );
        $link_options_delete = array(
            'attributes' => array('class' => array(
                    'btn', 'btn btn-sm red',
                ),),
        );
        $link_options_add = array(
            'attributes' => array('class' => array(
                    'btn', 'btn btn-sm red',
                ),),
        );
        foreach ($getlist as $item) {
            $idarray = array('id' => $item->productpk);
            $link_del = CustomUtils::deleteButton('productmaster_delete', $idarray, 'extrasmall', 'Delete');
            $edit_productlink = CustomUtils::editButton('productmaster_edit', $idarray, 'extrasmall', 'Edit');
            $dispurl = Url::fromRoute('productmaster_display', array('id' => $item->productpk));
            $display_productlink = \Drupal::l(t($item->productdesc), $dispurl);

            // Row with attributes on the row and some of its cells.
            $rows[] = array(
                'data' => array($item->productcode, $display_productlink, $item->productgroup, $item->producttype, $edit_productlink, $link_del)
            );
        }
        $add_productlink = CustomUtils::addButton('productmaster_add', '', 'medium', 'Add Product');

        $form['actions']['submit'] = [
            '#markup' => $add_productlink,
            '#prefix' => '<div>',
            '#suffix' => '</div><br/>',
        ];
        $form['#attached']['library'][] = 'productmaster/productmaster_lib';

        $form['tablebody'] = array(
            '#markup' => '',
            '#prefix' => '<div class="kt-portlet kt-portlet--mobile">',
            '#suffix' => '</div>'
        );

        $form['tablebody']['product_table'] = array(
            '#theme' => 'table',
            '#header' => $header,
            '#rows' => $rows,
            '#attributes' => array(
                'id' => 'kt_table_1',
                'class' => "table table-striped- table-bordered table-hover table-checkable"
            ),
            '#prefix' => '<div class="kt-portlet__body">',
            '#suffix' => '</div>',
        );
        $form['pager'] = array(
            '#type' => 'pager'
        );

        return $form;
    }

}
