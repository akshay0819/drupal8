<?php

namespace Drupal\customutil;

use Drupal\Core\Url;
use Drupal\Core\Link;

Class CustomUtils {

    static function getCodevaluesFormCodetype($codetype) {
        $result = db_select('tragcodevalues', 'fc')
                ->fields('fc', array('code', 'codetype', 'description'))
                ->condition('codetype', $codetype, '=')
                ->execute()
                ->fetchAll();
        foreach ($result as $key) {
            $matches[$key->code] = $key->description;
        }
        return $matches;
    }

    static function jsonCapture($jsongroup, $form = [], $form_state = [], $values = [], $display = NULL) {
        $getfields = json_decode($jsongroup, true);
	$ftype = ['DATE' => 'date', 'CHAR' => 'textfield', 'AUTO' => 'textfield', 'SELECT' => 'select', 'FLOAT' => 'textfield', 'CHECK' => 'checkboxes', 'INT' => 'textfield', 'RADIO' => 'textfield', 'TEXT' => 'textarea'];
	foreach ($getfields as $fld) {
	$desc = db_query("SELECT apmddesc from {appmetadata} WHERE apmdname = :apmdname AND apmdtype <> 'HEAD' LIMIT 1", array(":apmdname" => $fld))->fetchField();
	$type = db_query("SELECT apmdtype from {appmetadata} WHERE apmdname = :apmdname AND apmdtype <> 'HEAD' LIMIT 1", array(":apmdname" => $fld))->fetchField();
	$options = json_decode(db_query("SELECT apmdoptions from {appmetadata} WHERE apmdname = :apmdname AND apmdtype <> 'HEAD' LIMIT 1", array(":apmdname" => $fld))->fetchField(), true);
	$form[$fld] = [
            '#type' => $ftype[$type],
          //  '#title' => $this->t($desc),
            '#default_value' => ($form_state->getValue($fld) != false) ? $form_state->getValue($fld) : $values[$fld],
	    '#attributes' =>  isset($display) ? ['readonly' => 'readonly'] : [], 
	];
	if ($type == 'DATE') {
	  $form[$fld]['#default_value'] = !empty($values[$fld]) ? date('Y-m-d', strtotime($values[$fld])) : date('Y-m-d');
	}
	if ($type == 'SELECT' || $type == 'RADIO' || $type == 'CHECK') {
	  if (isset($display)) $form[$fld]['#disabled'] = TRUE;
	  $form[$fld]['#options'] = $options;
	}
	}
        return $form;
    }

    static function deleteButton($formroute, $idArray, $size = NULL, $text = NULL) {
        if ($size == 'extrasmall') {
            $class = "btn btn-xs";
        } else if ($size == 'small') {
            $class = "btn btn-sm";
        } else if ($size == 'medium') {
            $class = "btn default";
        } else if ($size == 'large') {
            $class = "btn default btn-lg";
        } else {
            $class = "btn default";
        }
        if ($text == '') {
            $txt = 'Delete';
        } else {
            $txt = $text;
        }
        $link_options_delete = array(
            'attributes' => array('class' => array(
                    'btn', 'kt-font-danger ', $class,
                ),),
        );

        $durl = Url::fromRoute($formroute, $idArray);
        $durl->setOptions($link_options_delete);
        $link_render_array_delete = array(
            '#title' => array('#markup' => '<i class="la la-trash"></i>' . $txt),
            '#type' => 'link',
            '#url' => $durl,
        );

        return \Drupal::service('renderer')->renderRoot($link_render_array_delete);
    }

    static function editButton($formroute, $idArray, $size = NULL, $text = NULL) {
        if ($size == 'extrasmall') {
            $class = "btn btn-xs";
        } elseif ($size == 'small') {
            $class = "btn btn-sm";
        } else if ($size == 'medium') {
            $class = "btn default";
        } else if ($size == 'large') {
            $class = "btn default btn-lg";
        } else {
            $class = "btn default";
        }
        if ($text == '') {
            $txt = 'Edit';
        } else {
            $txt = $text;
        }
        $link_options = array(
            'attributes' => array('class' => array(
                    'btn', 'kt-font-success ', $class,
                ),),
        );

        $durl = Url::fromRoute($formroute, $idArray);
        $durl->setOptions($link_options);
        $link_render_array = array(
            '#title' => array('#markup' => '<i class="la la-edit"></i>' . $txt),
            '#type' => 'link',
            '#url' => $durl,
        );

        return \Drupal::service('renderer')->renderRoot($link_render_array);
    }

    static function addButton($formroute, $id = NULL, $size = NULL, $text = NULL) {
        if ($size == 'extrasmall') {
            $class = "btn btn-xs";
        } else if ($size == 'small') {
            $class = "btn btn-sm";
        } else if ($size == 'medium') {
            $class = "btn default";
        } else if ($size == 'large') {
            $class = "btn default btn-lg";
        } else {
            $class = "btn default";
        }
        if ($text == '') {
            $txt = 'Add';
        } else {
            $txt = $text;
        }
        $link_options = array(
            'attributes' => array('class' => array(
                    'btn btn-brand btn-elevate btn-icon-sm ', $class,
                ),),
        );
	if (!empty($id))
          $durl = Url::fromRoute($formroute, $id);
	else        
	  $durl = Url::fromRoute($formroute);
        $durl->setOptions($link_options);
        $link_render_array = array(
            '#title' => array('#markup' => '<i class="la la-plus"></i>' . $txt),
            '#type' => 'link',
            '#url' => $durl,
        );

        return \Drupal::service('renderer')->renderRoot($link_render_array);
    }
    
    static function CancelButton($formroute, $id = NULL, $size = NULL, $text = NULL) {
        if ($size == 'extrasmall') {
            $class = "btn btn-xs";
        } else if ($size == 'small') {
            $class = "btn btn-sm";
        } else if ($size == 'medium') {
            $class = "btn default";
        } else if ($size == 'large') {
            $class = "btn default btn-lg";
        } else {
            $class = "btn default";
        }
        if ($text == '') {
            $txt = 'Cancel';
        } else {
            $txt = $text;
        }
        $link_options = array(
            'attributes' => array('class' => array(
                    'btn btn-danger btn-elevate btn-icon-sm ', $class,
                ),),
        );

        $durl = Url::fromRoute($formroute);
        $durl->setOptions($link_options);
        $link_render_array = array(
            '#title' => array('#markup' => '<i class="la la-close"></i>' . $txt),
            '#type' => 'link',
            '#url' => $durl,
        );

        return \Drupal::service('renderer')->renderRoot($link_render_array);
    }

}
