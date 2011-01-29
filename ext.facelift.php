<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Facelift
 *
 * This extension works in conjunction with its accessory to various features the control panel.
 *
 * @package   Facelift
 * @author    Kevin Thompson <thompson.kevind@gmail.com>
 * @link      http://github.com/kevinthompson/facelift
 * @copyright Copyright (c) 2010 Kevin Thompson
 * @license   http://creativecommons.org/licenses/by-sa/3.0/   Attribution-Share Alike 3.0 Unported
 */

class Facelift_ext
{
	var $settings        = array();
	var $name            = 'Facelift';
	var $version         = '1.0';
	var $description     = 'Improves the ExpressionEngine Control Panel by adding a number of small features.';
	var $settings_exist  = 'y';
	var $docs_url		 = '';

	function Facelift_ext($settings='')
	{
	    $this->settings = $settings;
	    $this->EE =& get_instance();
	}
	
	// --------------------------------
	//  Settings
	// --------------------------------  

	function settings()
	{	
		$settings = array();
		
		$settings['facelift_draggable'] = array('r', array('yes' => $this->EE->lang->line('yes'), 'no' => $this->EE->lang->line('no')), 'yes');
		$settings['facelift_fixed_acc'] = array('r', array('yes' => $this->EE->lang->line('yes'), 'no' => $this->EE->lang->line('no')), 'yes');
	
		return $settings;
	}	
	
	function sessions_end($session)
	{
		// Ajax Functionality
		if($this->EE->input->post('facelift_ajax') != '')
		{

			// add json lib if < PHP 5.2
			include_once 'includes/jsonwrapper/jsonwrapper.php';
			
			// Draggable Processing
			if($this->settings['facelift_draggable'] == 'yes' && $this->EE->input->post('facelift_ajax') != ''){

				// decode json data
				$fields = json_decode($this->EE->input->post('facelift_ajax'));
				$db = json_decode($this->EE->input->post('facelift_db'));
			
				// store new values
				$sql = "UPDATE " . $db->table . " SET " . $db->field . " = CASE " . $db->id . " ";
			
				foreach($fields as $index => $field)
				{
					$field = (array) $field;
					$index += 1;
					$sql .= "WHEN " . $field[$db->id] . " THEN " . $index . " ";
					$csv .= ($csv != '' ? ',' : '') . $field[$db->id];
					$group_id = ($field['group_id'] != '' ? $field['group_id'] : "");
				}
			
				$sql .= "END WHERE " . $db->id . " IN (" . $csv . ")" . ($group_id != '' ? " AND group_id = " . $group_id : "");
			
				$this->EE->db->query($sql);
			
				// kill ee execution
				exit();
			}
		}
	}
	
		
	function activate_extension()
	{
		$this->settings = array(
			'facelift_draggable'	=> 'yes',
			'facelift_fixed_acc'	=> 'yes',
		);

		$this->EE->db->query($this->EE->db->insert_string('exp_extensions',
	    	array(
				'extension_id' => '',
		        'class'        => ucfirst(get_class($this)),
		        'method'       => 'sessions_end',
		        'hook'         => 'sessions_end',
		        'settings'     => serialize($this->settings),
		        'priority'     => 10,
		        'version'      => $this->version,
		        'enabled'      => "y"
				)
			)
		);
	}


	function update_extension($current='')
	{
	    if ($current == '' OR $current == $this->version)
	    {
	        return FALSE;
	    }
	    
		$this->EE->db->query("UPDATE exp_extensions 
	     	SET version = '". $this->EE->db->escape_str($this->version)."' 
	     	WHERE class = '".ucfirst(get_class($this))."'");
	}

	
	function disable_extension()
	{	    
		$this->EE->db->query("DELETE FROM exp_extensions WHERE class = '".ucfirst(get_class($this))."'");
	}

}

/* End of file ext.facelift.php */
/* Location: ./system/expressionengine/third_party/facelift/ext.facelift.php */