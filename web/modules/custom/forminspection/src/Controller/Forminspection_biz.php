<?php

namespace Drupal\forminspection\Controller;

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
        //DbTransaction
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
echo "<pre>";
print_r($values);
echo "</pre>";die();
//echo $insertid;
die();
	foreach ($values['field_container'] as $key => $value) {
//foreach ($value['field_container'] as $val1) {

//echo "<pre>";
//print_r($val1);
//echo $val1->requirements;
//echo $val1['slno'];
//echo "</pre>";
//die();
//}
/*
		$insertdtl = db_insert('appinspectiondtl')
		        ->fields(array(
		            'appinspformpk' => $insertid,
		            'slno' => $value['slno'],
		            'chapter' => $value['chapter'],
		            'requirements' => $value['requirements'],
		            'checklist' => $value['checklist'],
		            'evidence' => $value['evidence'],
		            'comments' => $value['comments'],
		            'feedback' => $value['feedback']
		        ))
		        ->execute();
*/

$insertdtl = db_insert('appinspectiondtl')
		        ->fields(array(
		            'appinspformpk' => $insertid,
		            'slno' => $value->slno,
		            'chapter' => $value->chapter,
		            'requirements' => $value->requirements,
		            'checklist' => $value->checklist,
		            'evidence' => $value->evidence,
		            'comments' => $value->comments,
		            'feedback' => $value->feedback
		        ))
		        ->execute();

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

        foreach ($values['field_container'] as $key => $value) {
	if (empty($value['appinspdtlpk'])) {
		$insertdtl = db_insert('appinspectiondtl')
		        ->fields(array(
		            'appinspformpk' => $appinspformpk,
		            'slno' => $value['slno'],
		            'chapter' => $value['chapter'],
		            'requirements' => $value['requirements'],
		            'checklist' => $value['checklist'],
		            'evidence' => $value['evidence'],
		            'comments' => $value['comments'],
		            'feedback' => $value['feedback']
		        ))
		        ->execute();
	}
	else {
		$updatedtl = db_update('appinspectiondtl')
		        ->fields(array(
		            'chapter' => $value['chapter'],
		            'requirements' => $value['requirements'],
		            'checklist' => $value['checklist'],
		            'evidence' => $value['evidence'],
		            'comments' => $value['comments'],
		            'docstatus' => $value['docstatus'],
		            'compstatus' => $value['compstatus'],
		            'feedback' => $value['feedback'],
		            'status' => $value['status']
		        ))
		        ->condition('appinspformpk', $appinspformpk, '=')
		        ->condition('appinspdtlpk', $value['appinspdtlpk'], '=')
		        ->execute();
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
