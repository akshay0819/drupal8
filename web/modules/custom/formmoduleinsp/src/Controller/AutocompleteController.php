<?php

namespace Drupal\formmoduleinsp\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
//use Drupal\Component\Utility\SafeMarkup;
//use Drupal\Component\Utility\Tags;
use Drupal\Component\Utility\Unicode;

/**
 * Defines a route controller for entity autocomplete form elements.
 */
class AutocompleteController extends ControllerBase {

    /**
     * Handler for autocomplete request.
     */
    public function moduleinspAutocomplete(Request $request) {
        $matches = array();
        $string = $request->query->get('q');
        $result = db_select('frtproduct', 'n')
                ->fields('n', array('productdesc', 'productpk'))
                ->condition('productdesc', '%' . db_like($string) . '%', 'LIKE')
//                ->condition('productcode', '%' . db_like($string) . '%', 'LIKE')
                ->execute();

        foreach ($result as $row) {
            $matches[$row->productpk] = $row->productdesc;
        }

        return new JsonResponse($matches);
    }

}
