<?php

/**
 * @file
 * Contains \Drupal\menu_icons\CssGenerate\MenuIconsCssGenerator.
 */

namespace Drupal\menu_icons\CssGenerate;

use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\menu_icons\CssGenerate\MenuIconsCssGeneratorInterface;

/**
 * Generate the CSS for the menu icons.
 */
class MenuIconsCssGenerator implements MenuIconsCssGeneratorInterface{

  /**
   * Build CSS based on menu IDs
   *
   * @return A string with the CSS
   */
  public function generateMenuIconsCss() {
    $css = "";
    $result = db_query("SELECT mlid, options FROM {menu_links}");
    $pos = \Drupal::config('menu_icons.settings')->get('position');
    $absolute = \Drupal::config('menu_icons.settings')->get('title_display');

    foreach ($result as $item) {
      $options = unserialize($item->options);

      if (isset($options['menu_icon']) && $options['menu_icon']['enable'] && !empty($options['menu_icon']['path']) && file_exists($options['menu_icon']['path'])) {

        $image_path = $options['menu_icon']['path'];
        $image_style = (isset($options['menu_icon']['image_style']) && !empty($options['menu_icon']['image_style'])) ? $options['menu_icon']['image_style'] : NULL;
        if ($image_style) {
          $source_uri = $image_path;
          $style = ImageStyle::load($image_style);
          $image_path = $style->buildUri($source_uri);
          if (!file_exists($image_path)) {
            $style = ImageStyle::load($image_style);
            $derivative = $style->createDerivative($source_uri, $image_path);
          }
        }

        // Retrieve the image dimensions
        $image = Drupal::service('image.factory')->get($image_path);
        $wrapper = file_stream_wrapper_get_instance_by_scheme(file_uri_scheme($image_path));
        $image_url = '/' . $wrapper->getDirectoryPath() . '/' . file_uri_target($image_path);
        $size = $pos == 'right' || $pos == 'left' ? $image->getWidth() : $image->getHeight();
        // Support private filesystem
        $menu_css = array(
          '#theme' => 'menu_icons_css_item',
          '#mlid' => $item->mlid,
          '#path' => $image_url,
          '#size' => $size,
          '#height' => $image->getHeight(),
          '#pos' => $pos,
          '#source' => $source_uri
        );
        $css .= drupal_render($menu_css);
      }
    }
    $csspath = 'public://css';
    if (!empty($css)) {
      file_prepare_directory($csspath, FILE_CREATE_DIRECTORY);
      file_unmanaged_delete($csspath . '/menu_icons.css');
      file_unmanaged_save_data($css, $csspath . '/menu_icons.css', FILE_EXISTS_REPLACE);
    }
    else {
      file_unmanaged_delete($csspath . '/menu_icons.css');
    }
  }

  public function helloWorld() {
    drupal_set_message(t('A new menu icon has been set for this node. Please refresh the page to view it.'));
  }
}
