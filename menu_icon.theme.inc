<?php

/**
 * @file
 * Preprocessors and theme functions of Aggregator module.
 */

use Drupal\Component\Utility\String;
use Drupal\Core\Render\Element;

/**
 * Prepares variables for aggregator item templates.
 *
 * Default template: aggregator-item.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - aggregator_item: An individual feed item for display on the aggregator
 *     page.
 */
function template_preprocess_menu_icons_css_item(&$variables) {
  global $base_url;
  $variables['mlid'] = $variables['mlid'];
  $variables['path'] = $base_url . $variables['path'];
  $variables['size'] = $variables['size'];
  $variables['height'] = $variables['height'];
  $variables['pos'] = $variables['pos'];
  $variables['source'] = $variables['source'];
}
