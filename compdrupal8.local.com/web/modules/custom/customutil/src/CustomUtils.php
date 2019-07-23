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
