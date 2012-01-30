<?php

/*
 *  Starting the configurator rendering
 */

function sc_params_pane($album) {
  $gall_id = $album['gall_id'];
  $type = $album['gall_type'];
  $form = '<form method="post" action="'.fgallery_get_edit_url($gall_id).'" id="sc-configurator-form" 
                 onSubmit="return fgallery_checkform()" enctype="multipart/form-data"><div>';
  $params_xml = simplexml_load_file(FGALLERY_ABSPATH . '/xml/params_'.$type.'.xml');
  $settings = fgallery_get_album_settings($gall_id);
  $s = array('element_name'=>'sc_gallery__source');
  $source = fgallery_get_settings_param($s, $settings);
  // Preparing jQuery UI tabs
  $tab_controls = "<ul>";
  $tab_controls .= '<li><a href="#sc-group-general">' . __('General','fgallery') . '</a></li>';
  foreach ($params_xml->params->group as $g) {
    // if php 4 is running
    if (FGALLERY_PHP4_MODE) {
        $temp = $g->attributes();
        $g = array();
        $g['name'] = $temp['name'];
        $g['title'] = $temp['title'];
        $g['type'] = $temp['type'];
    }
    // endif
    if (isset($g['type']) && $g['type'] == 'source') {
         if ($g['name'] == $source){
            $tab_controls .= '<li><a href="#sc-group-' . $g['name'] . '">' . $g['title'] . '</a></li>';
         }
    } else {
            $tab_controls .= '<li><a href="#sc-group-' . $g['name'] . '">' . $g['title'] . '</a></li>';
    }
  }

  $tab_controls .= "</ul>";
  $form .= $tab_controls;
  $form .= fgallery_render_edit_album_form($album);
  // Form the list of parameters in groups
  // if php 4 is running
  if (FGALLERY_PHP4_MODE) {
      foreach ($params_xml->params->group as $g) {
            $temp = $g->attributes();
            if (isset($temp['type']) && $temp['type'] == 'source') {
                       if ($temp['name'] == $source){ 
                            $form .= '<div id="sc-group-' . $temp['name'] . '" class="sc-skin-params-tab-panel">';
                            $zebra = 'even';
                            foreach ($g->p as $p) {
                              $p_temp = $p->attributes();
                              if (function_exists($fn = 'sc_controls_' . $p_temp['control'])) {
                                    $p_temp['element_name'] = 'sc_' . $temp['name'] . '__' . $p_temp['name'];
                                    $p_temp['default'] = fgallery_get_settings_param($p_temp, $settings);
                                    $p_temp['zebra'] = $zebra;
                                    $form .= $fn($p_temp);
                                    $zebra = $zebra == 'even' ? 'odd' : 'even';
                              }
                            }
                            $form .= '</div>';
                       }
            } else {
                  $form .= '<div id="sc-group-' . $temp['name'] . '" class="sc-skin-params-tab-panel">';
                            $zebra = 'even';
                            foreach ($g->p as $p) {
                              $p_temp = $p->attributes();
                              if (function_exists($fn = 'sc_controls_' . $p_temp['control'])) {
                                    $p_temp['element_name'] = 'sc_' . $temp['name'] . '__' . $p_temp['name'];
                                    $p_temp['default'] = fgallery_get_settings_param($p_temp, $settings);
                                    $p_temp['zebra'] = $zebra;
                                    $form .= $fn($p_temp);
                                    $zebra = $zebra == 'even' ? 'odd' : 'even';
                              }
                            }
                            $form .= '</div>';
          }
       }
    //end php 4 clause
  } else { 
    // if php 5 is running
      foreach ($params_xml->params->group as $g) {
          if (isset($g['type']) && $g['type'] == 'source') {
               if ($g['name'] == $source){ 
                    $form .= '<div id="sc-group-' . $g['name'] . '" class="sc-skin-params-tab-panel">';
                    $zebra = 'even';
                    foreach ($g->p as $p) {
                      if (function_exists($fn = 'sc_controls_' . $p['control'])) {
                            $p['element_name'] = 'sc_' . $g['name'] . '__' . $p['name'];
                            $p['default'] = fgallery_get_settings_param($p, $settings);
                            $p['zebra'] = $zebra;
                            $form .= $fn($p);
                            $zebra = $zebra == 'even' ? 'odd' : 'even';
                      }
                    }
                    $form .= '</div>';
               }
          } else {
                   $form .= '<div id="sc-group-' . $g['name'] . '" class="sc-skin-params-tab-panel">';
                    $zebra = 'even';
                    foreach ($g->p as $p) {
                      if (function_exists($fn = 'sc_controls_' . $p['control'])) {
                            $p['element_name'] = 'sc_' . $g['name'] . '__' . $p['name'];
                            $p['default'] = fgallery_get_settings_param($p, $settings);
                            $p['zebra'] = $zebra;
                            $form .= $fn($p);
                            $zebra = $zebra == 'even' ? 'odd' : 'even';
                      }
                    }
                    $form .= '</div>';
          }
      }
    // end php condition
   }
   // "Save" button
   if (isset($_GET['page']) && $_GET['page'] == 'fgallery_add') {
       $button_text = __('Next Step');
       $form .= '<input type="hidden" name="fgallery_just_added" value="1" />';
   } else {
       $button_text = __('Save');
   }
  $form .= '<input name="sc_submit" id="save_gallery_settings" type="submit" value="'.$button_text.'" /><span id="configurator_message"></span>';
  $form .= wp_nonce_field('fgallery_settings','fgallery_settings_field');
  $form .= '</div></form>';
  return $form;
}

/**
 * Element text
 */
function sc_controls_text($p) {
  $output = '<div class="form-item ' . $p['zebra'] . '" >'
       . '<label for="' . $p['element_name'] . '">' . $p['title'] . ':</label>'
       . '<input name="' . $p['element_name'] . '" id="' . $p['element_name'] . '" type="text" value="' . (string)$p['default'] .'" size="10" maxlength="255" class="form-text '.$p['class'].'"/>';
       if ($p['type'] == 'file') {
         $output .= '<input type="file" id="'.$p['element_name'].'_file" name="'.$p['element_name'].'_file" class="configurator_file" />';
       }
    $output .= '</div>';
    return $output;
}

/**
 * Element - checkbox.
 */
function sc_controls_checkbox($p) {
  $checked = (int)$p['default'] ? 'checked="checked"' : '';
  if ($checked == '' && (string)$p['default'] == 'true') $checked = 'checked = "checked"';
  return '<div class="form-item ' . $p['zebra'] . '" >'
       . '<input name="' . $p['element_name'] . '" type="hidden" value="0" />' 
       . '<input name="' . $p['element_name'] . '" id="' . $p['element_name'] . '" type="checkbox" ' . $checked . ' class="form-checkbox" value="'.$p['values'].'"/>'
       . '<label for="' . $p['element_name'] . '">' . $p['title'] . '</label>'
       . '</div>';
}

/**
 * Element - select.
 */
function sc_controls_select($p) {
  $options_raw = explode(',', $p['values']);
  $options = '';
  foreach ($options_raw as $o) {
    $selected = (string) $p['default'] == $o ? 'selected="selected"' : '';
    $options .= '<option value="' . $o . '" ' . $selected . '>' . $o . '</option>';
  }
  return '<div class="form-item ' . $p['zebra'] . '" >'
       . '<label for="' . $p['element_name'] . '">' . $p['title'] . ':</label>'
       . '<select name="' . $p['element_name'] . '" id="' . $p['element_name'] . '" class="form-select">' . $options . '</select>'
       . '</div>';
}

/**
 * Element - slider (jQuery UI slider).
 */
function sc_controls_slider($p) {
  $values = explode(':', $p['values']); // format "1..5:1"
  $range = $values[0];
  $range = explode('..', $range);
  $min = $range[0];
  $max = $range[1];

  $step = $values[1];

  // Validate values
  if (!is_numeric($step) || !is_numeric($min) || !is_numeric($max)) {
    return '<div class="form-item ' . $p['zebra'] . '" >Parse error</div>';
  }

  return '<div class="form-item ' . $p['zebra'] . '" >'
       . '<label for="' . $p['element_name'] . '">' . $p['title'] . ':</label>'
       . '<input name="' . $p['element_name'] . '" id="' . $p['element_name'] . '" type="text" value="' . $p['default'] .'" size="3" maxlength="3" readonly="readonly" class="sc-slider-val form-text" min="' . $min . '" max="' . $max . '" step="' . $step .'"/>'
       . '</div>';
}

/**
 * Form element - color picker (Farbtastic).
 */
function sc_controls_color($p) {
  return '<div class="form-item ' . $p['zebra'] . '" >'
       . '<label for="' . $p['element_name'] . '">' . $p['title'] . ':</label>'
       . '<input name="' . $p['element_name'] . '" id="' . $p['element_name'] . '" type="text" value="' . str_replace('0x', '#', $p['default']) .'" size="10" maxlength="7" class="sc-color-val form-text"/>'
       . '</div>';
}
