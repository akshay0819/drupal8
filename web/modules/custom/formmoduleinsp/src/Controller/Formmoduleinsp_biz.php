<?php

namespace Drupal\formmoduleinsp\Controller;
use Drupal\file\Entity\File;

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
    static function getforminspectionlistdet($forminspectionpk = NULL) {
	$result = array();
        if (!empty($forminspectionpk)) {
            $headerquery = db_select('appinspectionform', 'a');
            $headerquery->fields('a');
            $headerquery->condition('appinspformpk', $forminspectionpk, '=');
            $result = $headerquery->execute()->fetchAssoc();

        }

        return $result;
    }
    static function getforminspectionlistdtl($forminspectionpk = NULL) {
        $result = array();
        if (!empty($forminspectionpk)) {
            $headerquery = db_select('appinspectiondtl', 'a');
            $headerquery->fields('a');
            $headerquery->condition('appinspformpk', $forminspectionpk, '=');
            $result = $headerquery->execute()->fetchAll();

        }

        return $result;
    }

    static function save_formmoduleinsp($form, $form_state, $appinspformpk = '', $apmdgroupname = '') {
       /* $values = $form_state->getValues();
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
        }*/return;
   }



    static function edit_formmoduleinsp($form, $form_state, $appinspformpk = '', $appinspdtlpk = '') {

        $values = $form_state->getValues();
        
        $transaction = db_transaction();

        $update = db_update('appinspectiondtl')
                ->fields([
                    'chapter' => $values['chapter'],
                    'requirements' => $values['requirements'],
                    'checklist' => $values['checklist'],
                    'evidence' => $values['evidence'],
                    'comments' => $values['comments'],
                    'docstatus' => $values['docstatus'],
                    'compstatus' => $values['compstatus'],
                    'feedback' => $values['feedback'],
                    'status' => $values['status']
                ])
                ->condition('appinspdtlpk', $appinspdtlpk, '=')
                ->execute();
	$image = $form_state->getValue('attachment');

   if ($image[0]) {
	foreach ($image as $val) {
	$file = File::load($val);
        $file->setPermanent();
	$file->save();
	$file_usage = \Drupal::service('file.usage'); 
	$file_usage->add($file, 'formmoduleinsp', $appinspformpk . '-' . $appinspdtlpk, \Drupal::currentUser()->id());
	$query = db_select('file_managed', 'a');
	$query->join('file_usage', 'b', 'b.fid = a.fid');
        $query->fields('a');
        $query->fields('b');
        $query->condition('b.type', $appinspformpk.'-'.$appinspdtlpk, '=');
        $query->orderBy('a.fid', 'DESC');
        $query->range(0, 1);
        $attach = $query->execute()->fetchAssoc();
	
	$insertid = db_insert('appattachment')
                ->fields([
                    'fid' => $attach['fid'],
                    'uid' => $attach['uid'],
                    'filename' => $attach['filename'],
                    'filemime' => $attach['filemime'],
                    'uri' => $attach['uri'],
                    'module' => $attach['module'],
                    'type' => $attach['type']
                ])
                ->execute();
	}
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
