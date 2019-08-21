<?php

namespace Drupal\forminspection\Controller;
use Drupal\file\Entity\File;

Class Forminspection_biz {

    static function getforminspectiondet($forminspectionpk = NULL) {
	$result = array();
        if (!empty($forminspectionpk)) {
            $headerquery = db_select('appinspectionform', 'a');
            $headerquery->fields('a');
            $headerquery->condition('appinspformpk', $forminspectionpk, '=');
            $result = $headerquery->execute()->fetchAssoc();

        }

        return $result;
    }
    static function getforminspectiondtl($forminspectionpk = NULL) {
        $result = array();
        if (!empty($forminspectionpk)) {
            $headerquery = db_select('appinspectiondtl', 'a');
            $headerquery->fields('a');
            $headerquery->condition('appinspformpk', $forminspectionpk, '=');
            $result = $headerquery->execute()->fetchAll();

        }

        return $result;
    }

    static function save_forminspection($form, $form_state) {

        $values = $form_state->getValues();
        $transaction = db_transaction();

        $insertid = db_insert('appinspectionform')
                ->fields(array(
                    'appinspformid' => $values['appinspformid'],
                    'appinspformname' => $values['appinspformname'],
                    'appinspauditor' => $values['appinspauditor'],
                    'appinspauditee' => $values['appinspauditee'],
                    'appauditdate' => $values['appauditdate'],
                    'appinspstatus' => $values['appinspstatus'],
                    'appinspcomments' => $values['appinspcomments'],
                    'appinspfeedback' => $values['appinspfeedback'],
                    'createdby' => \Drupal::currentUser()->id() 
                ))
                ->execute();
	    $validators = array(
                'file_validate_extensions' => array('pdf doc docx rtf txt xls xlsx csv bmp jpg jpeg png gif tiff'),
            );
            $uri = 'public://';
            if ($file = file_save_upload('attachment', $validators, $uri . "items/")) {
                $file_content = file_get_contents($file->filepath);
            }
	for ($i = 0; $i < 100; $i++) {
		if (empty($form_state->getValue(['field_container',$i,'slno']))) continue;
		$insertdtl = db_insert('appinspectiondtl')
		        ->fields(array(
		            'appinspformpk' => $insertid,
		            'slno' => $form_state->getValue(['field_container',$i,'slno']),
		            'chapter' => $form_state->getValue(['field_container',$i,'chapter']),
		            'requirements' => $form_state->getValue(['field_container',$i,'requirements']),
		            'checklist' => $form_state->getValue(['field_container',$i,'checklist']),
		            'evidence' => $form_state->getValue(['field_container',$i,'evidence']),
		            'comments' => $form_state->getValue(['field_container',$i,'comments']),
		            'feedback' => $form_state->getValue(['field_container',$i,'feedback'])
		        ))
		        ->execute();
		if (!$insertdtl) {$transaction->rollback();break;}
	}
        if (!$insertid) {
            $transaction->rollback();
        } else {
            return $insertid;
        }
   }



    static function edit_forminspection($form, $form_state, $appinspformpk = '') {

        $values = $form_state->getValues();
        //DbTransaction

        $transaction = db_transaction();

        $update = db_update('appinspectionform')
                ->fields(array(
                    'appinspformid' => $values['appinspformid'],
                    'appinspformname' => $values['appinspformname'],
                    'appinspauditor' => $values['appinspauditor'],
                    'appinspauditee' => $values['appinspauditee'],
                    'appauditdate' => $values['appauditdate'],
                    'appinspstatus' => $values['appinspstatus'],
                    'appinspcomments' => $values['appinspcomments'],
                    'appinspfeedback' => $values['appinspfeedback'],
                    'updatedby' => \Drupal::currentUser()->id(),
                    'updatedtime' => date('Y-m-d H:i:s', time())
                ))
                ->condition('appinspformpk', $appinspformpk, '=')
                ->execute();
	$validators = array(
                'file_validate_extensions' => array('pdf doc docx rtf txt xls xlsx csv bmp jpg jpeg png gif tiff'),
            );
            $uri = 'public://';
            if ($file = file_save_upload('attachment', $validators, $uri . "items/")) {
                $file_content = file_get_contents($file->filepath);
            }
        for ($i = 0; $i < 100; $i++) {
		if (empty($form_state->getValue(['field_container',$i,'slno']))) continue;
	if (empty($form_state->getValue(['field_container',$i,'appinspdtlpk']))) {
		$insertdtl = db_insert('appinspectiondtl')
		        ->fields(array(
		            'appinspformpk' => $appinspformpk,
		            'slno' => $form_state->getValue(['field_container',$i,'slno']),
		            'chapter' => $form_state->getValue(['field_container',$i,'chapter']),
		            'requirements' => $form_state->getValue(['field_container',$i,'requirements']),
		            'checklist' => $form_state->getValue(['field_container',$i,'checklist']),
		            'evidence' => $form_state->getValue(['field_container',$i,'evidence']),
		            'comments' => $form_state->getValue(['field_container',$i,'comments']),
		            'feedback' => $form_state->getValue(['field_container',$i,'feedback'])
		        ))
		        ->execute();
		if (!$insertdtl) {$transaction->rollback();break;}
	}
	else {
		$updatedtl = db_update('appinspectiondtl')
		        ->fields(array(
		            'chapter' => $form_state->getValue(['field_container',$i,'chapter']),
		            'requirements' => $form_state->getValue(['field_container',$i,'requirements']),
		            'checklist' => $form_state->getValue(['field_container',$i,'checklist']),
		            'evidence' => $form_state->getValue(['field_container',$i,'evidence']),
		            'comments' => $form_state->getValue(['field_container',$i,'comments']),
		            'docstatus' => $form_state->getValue(['field_container',$i,'docstatus']),
		            'compstatus' => $form_state->getValue(['field_container',$i,'compstatus']),
		            'feedback' => $form_state->getValue(['field_container',$i,'feedback']),
		            'status' => $form_state->getValue(['field_container',$i,'status'])
		        ))
		        ->condition('appinspformpk', $appinspformpk, '=')
		        ->condition('appinspdtlpk', $form_state->getValue(['field_container',$i,'appinspdtlpk']), '=')
		        ->execute();
		if (!$updatedtl) {$transaction->rollback();break;}
	}
	}
        if (!$update) {
            $transaction->rollback();
        } else {
            return $update;
        }
    }

    static function delete_forminspection($appinspformpk) {
        db_delete('appinspectionform')
                ->condition('appinspformpk', $appinspformpk)
                ->execute();
        db_delete('appinspectiondtl')
                ->condition('appinspformpk', $appinspformpk)
                ->execute();
    }

}
