<?php

namespace Drupal\formmoduleinsp\Controller;

Class Formmoduleinsp_biz {

    static function getformmoduleinspdet($appinspformpk = NULL, $appinspdtlpk = NULL) {
	$result = array();
        if (!empty($appinspformpk)) {
            $headerquery = db_select('appinspectiondtl', 'a');
            $headerquery->fields('a');
            $headerquery->condition('appinspformpk', $appinspformpk, '=');
            $headerquery->condition('appinspdtlpk', $appinspdtlpk, '=');
            $result = $headerquery->execute()->fetchAssoc();

        }

        return $result;
    }
    
    static function save_formmoduleinsp($form, $form_state, $appinspformpk = '', $apmdgroupname = '') {
        $values = $form_state->getValues();
	unset($values['submitup']);
        unset($values['submit']);
        unset($values['form_build_id']);
        unset($values['form_token']);
        unset($values['form_id']);
        unset($values['op']);
        //DbTransaction
        $transaction = db_transaction();
	$insertid = db_insert('appform')
                ->fields(array(
                    'appformid' => $appinspformpk,
                    'appgroupname' => $apmdgroupname,
                    'appgroupfields' => json_encode($values, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT),
                    'createdby' => \Drupal::currentUser()->id()
                ))
                ->execute();

        if (!$insertid) {
            $transaction->rollback();
        } else {
            return $insertid;
        }
   }



    static function edit_formmoduleinsp($form, $form_state, $appinspformpk = '', $appinspdtlpk = '') {

        $values = $form_state->getValues();
        
        $transaction = db_transaction();

        $update = db_update('appinspectiondtl')
                ->fields(array(
                    'chapter' => $values['chapter'],
                    'requirements' => $values['requirements'],
                    'checklist' => $values['checklist'],
                    'evidence' => $values['evidence'],
                    'comments' => $values['comments'],
                    'docstatus' => $values['docstatus'],
                    'compstatus' => $values['compstatus'],
                    'feedback' => $values['feedback'],
                    'status' => $values['status']
                ))
                ->condition('appinspdtlpk', $appinspdtlpk, '=')
                ->execute();
	$validators = array(
                'file_validate_extensions' => array('pdf doc docx rtf txt xls xlsx csv bmp jpg jpeg png gif tiff'),
            );
            $uri = 'public://';
            if ($file = file_save_upload('attachment', $validators, $uri . "items/")) {
                $file_content = file_get_contents($file->filepath);
            }
        

        if (!$update) {
            $transaction->rollback();
        } else {
            return $update;
        }
    }

    static function delete_formmoduleinsp($appinspdtlpk) {
        db_delete('appform')
                ->condition('appinspdtlpk', $appinspdtlpk)
                ->execute();
    }

}
