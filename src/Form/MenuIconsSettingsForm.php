<?php
/**
 * @file
 * Contains \Drupal\menu_icons\Form\MenuIconsSettingsForm.
 */
namespace Drupal\menu_icons\Form;

use Drupal\Core\Form\ConfigFormBase;

/**
 * Configure book settings for this site.
 */
class MenuIconsSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'menu_icons_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, array &$form_state) {

    $config = \Drupal::config('menu_icons.settings');
    $form['menu_icons_default_icon'] = array(
      '#type' => 'textfield',
      '#title' => t('Icon path'),
      '#required' => FALSE,
      '#default_value' => $config->get('default_icon'),
      '#description' => $this->t('A Drupal path to the icon or image to use as a default.'),
    );

    $options = array();
    $image_styles = \Drupal::entityManager()->getStorage('image_style')->loadMultiple();
    foreach ($image_styles as $style) {
      $options[$style->name] = $style->label;
    }

    if (!empty($options)) {
      $form['menu_icons_image_style_default'] = array(
        '#type' => 'select',
        '#title' => t('Image default style'),
        '#default_value' => $config->get('style'),
        '#description' => $this->t('Choose a default !link to be used for menu icons. This setting can be overwritten per menu item.', array('!link' => l(t('Image style'), 'admin/config/media/image-styles'))),
        '#required' => FALSE,
        '#options' => $options,
      );
    }

    $form['menu_icons_image_folder'] = array(
      '#type' => 'textfield',
      '#title' => t('Icon folder'),
      '#default_value' => $config->get('folder'),
      '#description' => $this->t('The name of the files directory in which the new uploaded icons will be stored. This folder will be created in the files directory'),
      '#required' => FALSE,
    );

    $form['menu_icons_position'] = array(
      '#type' => 'select',
      '#title' => t('Position'),
      '#default_value' => $config->get('position'),
      '#options' => array(
        'top' => t('top'),
        'bottom' => t('bottom'),
        'right' => t('right'),
        'left' => t('left'),
      ),
      '#required' => FALSE,
    );

    $form['menu_icons_hide_titles'] = array(
      '#type' => 'checkbox',
      '#title' => t('Hide menu titles if icon is present'),
      '#default_value' => $config->get('title_display'),
      '#description' => $this->t('Check this to hide menu titles and display only the icon, if an icon is configured. You will need to clear the theme registry cache after changing this option for it to take effect.'),
    );

    $form['menu_icons_use_css'] = array(
      '#type' => 'checkbox',
      '#title' => t('Provide default CSS for placing menu icons into the menu'),
      '#default_value' => $config->get('use_css'),
      '#description' => $this->t("Advanced: uncheck this box if you do not want to enable the Menu Icon style sheet that's provided by default. If you uncheck this box, you must provide your own CSS for Menu Icons to appear!"),
    );
    $form['array_filter'] = array('#type' => 'value', '#value' => TRUE);

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, array &$form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, array &$form_state) {
    $this->config('menu_icons.settings')
      ->set('menu_icons_default_icon', $form_state['values']['menu_icons_default_icon'])
      ->set('style', $form_state['values']['menu_icons_image_style_default'])
      ->set('folder', $form_state['values']['menu_icons_image_folder'])
      ->set('position', $form_state['values']['menu_icons_position'])
      ->set('title_display', $form_state['values']['menu_icons_hide_titles'])
      ->set('use_css', $form_state['values']['menu_icons_use_css'])
      ->save();
    parent::submitForm($form, $form_state);
  }
}
