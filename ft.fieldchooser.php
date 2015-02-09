<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * ExpressionEngine Fieldtype Class
 *
 * @package   Fieldchooser
 * @category  Fieldtypes
 * @author    Andy Hebrank
 * @link      https://github.com/ahebrank/fieldchooser
 */

// define the old-style EE object
if (!function_exists('ee')) {
    function ee() {
        static $EE;
        if (! $EE) {
          $EE = get_instance();
        }
        return $EE;
    }
}

class Fieldchooser_ft extends EE_Fieldtype {
  
  var $info = array(
      'name'    =>  'Fieldchooser',
      'version' =>  '0.1'
      );


  // --------------------------------------------------------------------

  function install() {
      return array(
        'choice_fields'  => '',
      );
  }

  // --------------------------------------------------------------------
  // Front end -- just give the field name of the selected (for use in conditionals)
  function replace_tag($data, $params = array(), $tagdata = false) {
    return $data;
  }

  // --------------------------------------------------------------------
  // CP entry page

  function display_field($data) {

    // storing fieldnames in per-channel settings
    $field_names = explode('|', $this->settings['choice_fields']);
      
    // gather the list of fields
    $query = ee()->db->select('field_id, field_name, field_label')
      ->from('channel_fields')
      ->where_in('field_name', $field_names)
      ->get();

    if (!$query->num_rows()) return "";

    $results = $query->result_array();

    $field_name_lookup = $this->_get_list($results, $this->settings['field_id'], 'field_name', 'field_label');

    // add in the JS to selectively hide fields
    // get a list of all the field_ids
    $field_id_lookup = $this->_get_list($results, $this->settings['field_id'], 'field_id', 'field_name');

    // hide all of these, handle dropbox changen to set visibility on a single field
    // need a name -> id lookup
    $dom_ids = array();
    $dom_lookup = array();
    foreach ($field_id_lookup as $id => $name) {
      $dom_ids[] = "#hold_field_" . $id;
      $dom_lookup[] = "'" . $name . "' : '#hold_field_" . $id . "'";
    }

    $script = '<script type="text/javascript">(function() {';
    $script .= "var field_lookup = {" . implode(', ', $dom_lookup) . "};";
    $script .= "var hide_field_dom_ids = '" . implode(', ', $dom_ids) . "';";
    $script .= "var bind_chooser = function() {
      $('select[name=field_id_" . $this->field_id . "]').on('change', function(e) {
        $(hide_field_dom_ids).hide();
        var show_field_id = field_lookup[$(this).val()];
        $(show_field_id).show();
      });
    }; $(hide_field_dom_ids).hide(); bind_chooser(); })();</script>\n";

    if (!empty($field_name_lookup)) {
      return form_dropdown($this->field_name, $field_name_lookup) . $script;
    }
  }

  // --------------------------------------------------------------------
  // CP channel field setup

  function display_settings($settings) { 
    if (array_key_exists("choice_fields", $settings )) {
      $selected_fields = explode('|', $settings['choice_fields']);
    } else {
      $selected_fields = array();
    }
    $group_id = ee()->input->get('group_id');
    $query = ee()->db->select("field_id, field_label, field_name")
      ->from('channel_fields')
      ->where('group_id', $this->EE->input->get('group_id'))
      ->get();

    // construct a lookup
    $field_lookup = $this->_get_list($query, $settings['field_id'], 'field_name');
    if (!empty($field_lookup)) {
      $this->EE->table->add_row(
          'Allow user to select',
       form_multiselect('choice_fields[]', $field_lookup, $selected_fields));
    }
  }
  

  // --------------------------------------------------------------------

  function save_settings ($data) {
    // serialize the fields
    $_POST['choice_fields'] = implode('|', $_POST['choice_fields']);
    return array_merge($this->settings, $_POST);
  }

  private function _get_list($result, $my_field_id, $key = 'field_name', $val = 'field_label') {

    if (is_object($result)) {
      if (!$result->num_rows()) return array();
      $result = $result->result_array();
    }

    $field_lookup = array();
    foreach ($result as $row) {
      // make sure I don't include myself
      if ($my_field_id == $row['field_id']) continue;
        
      $field_lookup[$row[$key]] = $row[$val]; 
    }

    return $field_lookup;
  }

  
} 