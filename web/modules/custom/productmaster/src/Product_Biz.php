<?php

namespace Drupal\productmaster;

Class Product_Biz {

    static function getproductlist($id = NULL) {

        $query = db_select('frtproduct', 'prod');
        $query->fields('prod');
        $result = $query->execute()->fetchAll();
        return $result;
    }

    static function getproductdet($id = NULL) {

        if (!empty($id)) {
            $query = db_select('frtproduct', 'prod');
            $query->fields('prod');
            $query->condition('productpk', $id, '=');

            $result = $query->execute()->fetchAssoc();
        }

        return $result;
    }

    static function getproductsdetail($id = NULL) {

        if (!empty($id)) {
            $query = db_select('frtproduct_details', 'proddet');
            $query->fields('proddet');
            $query->condition('productpk', $id, '=');

            $result = $query->execute()->fetchAll();
        }

        return $result;
    }

    static function delete_product($id) {
        db_delete('frtproduct')
                ->condition('productpk', $id)
                ->execute();
    }

    static function save_product($form, $form_state) {
        $values = $form_state->getValues();

        $insertid = db_insert('frtproduct')
                ->fields(array(
                    'productcode' => $values['productcode'],
                    'productdesc' => $values['productdesc'],
                    'producttype' => $values['producttype'],
                    'pglclass' => $values['pglclass'],
                    'productcatg' => $values['productcatg'],
                    'productrating' => $values['productrating'],
                    'picture' => $_FILES['files']['name']['productimg'],
                    'orgstatus' => $values['orgstatus'],
                    'croptype' => $values['croptype'],
                    'season' => $values['season'],
                ))
                ->execute();

        if ($insertid) {
            $validators = array(
                'file_validate_extensions' => array('pdf doc docx rtf txt xls xlsx csv bmp jpg jpeg png gif tiff'),
            );
            $uri = 'public://';
            if ($file = file_save_upload('productimg', $validators, $uri . "items/")) {
                $file_content = file_get_contents($file->filepath);
            }
            return $insertid;
        } else {
            return 'FAIL';
        }
    }

    static function edit_product($form, $form_state, $id = '') {
        $values = $form_state->getValues();
        echo '<pre/>';
        print_r($values);
        exit;
        $upid = db_update('frtproduct')
                ->fields(array(
                    'productcode' => $values['productcode'],
                    'productdesc' => $values['productdesc'],
                    'producttype' => $values['producttype'],
                    'pglclass' => $values['pglclass'],
                    'productcatg' => $values['productcatg'],
                    'productrating' => $values['productrating'],
                    'orgstatus' => $values['orgstatus'],
                    'croptype' => $values['croptype'],
                    'season' => $values['season'],
                ))
                ->condition('productpk', $id, '=')
                ->execute();

        if ($upid) {
            return $id;
        } else {
            return 'FAIL';
        }
    }

}
