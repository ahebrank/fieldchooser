<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * ExpressionEngine Fieldtype Class
 *
 * @package		
 * @category	Fieldtypes
 * @author		
 * @link		
 */

class Fieldchooser_ft extends EE_Fieldtype {
	
	var $info = array(
			'name'		=>	'Fieldchooser',
			'version'	=>	'0.1'
			);

	
	function Fieldchooser_ft()
	{
		parent::EE_Fieldtype();
		
	}

	// --------------------------------------------------------------------

	function install()
	{
			return array(
				'selected_field_id'	=> '1',
			);
	}

	// --------------------------------------------------------------------

	function display_field($data)
	{
		
		$field_id = "field_id_".$this->settings['selected_field_id'];
		
		// Make sure we are on an edit page.
		if ($this->EE->input->get('entry_id')) {
			
			$entry_id = $this->EE->input->get('entry_id');
			$query = $this->EE->db->query("SELECT $field_id FROM exp_channel_data WHERE entry_id = $entry_id");
	
			if ($query->num_rows() == 1)
			{
				$content = $query->result_array();
				return $content[0][$field_id];
			}
		}
	}

	// --------------------------------------------------------------------
	
	function display_settings($settings)
	{	
		if (array_key_exists("selected_field_id", $settings )) {
			$selected_field = $settings['selected_field_id'];
		} else {
			$selected_field = 1;
		}
		$group_id = $this->EE->input->get('group_id');
		$query = $this->EE->db->query("SELECT field_id,field_label,field_name FROM exp_channel_fields WHERE group_id = $group_id");
		if ($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row) {
				if ($settings['field_id'] != $row['field_id']) {
				$field_id = $row['field_id'];
				$results[$field_id] = $row['field_label']; 
				}
				
			}
		} else {
		$results[0] = "No other custom fields defined.";
		}
	
		$this->EE->table->add_row(
				'Custom Field',
		 form_dropdown('selected_field_id', $results, $selected_field));
	}
	

	// --------------------------------------------------------------------

	function save_settings ($data)
	{
		return array_merge($this->settings, $_POST);	
	}

	
} 