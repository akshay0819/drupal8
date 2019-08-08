<?php

namespace Drupal\formbuild\Controller;

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
    public function metadataAutocomplete(Request $request) {
        $matches = array();
        $string = $request->query->get('q');
        $result = db_select('appmetadata', 'n')
                ->fields('n', array('apmdname', 'apmddesc'))
                ->condition(
                db_or()
                ->condition('apmdname', '%' . db_like($string) . '%', 'LIKE')
                ->condition('apmddesc', '%' . db_like($string) . '%', 'LIKE')
                )->execute();

        foreach ($result as $row) {
            $matches[] = ['value' => $row->apmdname, 'label' => $row->apmddesc];
        }

        return new JsonResponse($matches);
    }


}
