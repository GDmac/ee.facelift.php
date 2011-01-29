<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Facelift
 *
 * This extension works in conjunction with its extension to add draggable sorting to additional areas of the control panel.
 *
 * @package   Facelift
 * @author    Kevin Thompson <thompson.kevind@gmail.com>
 * @link      http://github.com/kevinthompson/facelift
 * @copyright Copyright (c) 2010 Kevin Thompson
 * @license   http://creativecommons.org/licenses/by-sa/3.0/   Attribution-Share Alike 3.0 Unported
 */

class Facelift_acc {

	var $name			= 'Facelift';
	var $id				= 'facelift';
	var $extension		= 'Facelift_ext';
	var $version		= '1.0';
	var $description	= 'Improves the ExpressionEngine Control Panel by adding a number of small features.';
	
	var $sections		= array();
	var $settings		= array();
	var $current_page	= array();
	
	var $pages			= array(				
		'category_update' => array(
			'table' => 'exp_categories',
			'field'	=> 'cat_order',
			'id'	=> 'cat_id'
		),
		'category_editor' => array(
			'table' => 'exp_categories',
			'field'	=> 'cat_order',
			'id'	=> 'cat_id'
		),
		'field_management' => array(
			'table' => 'exp_channel_fields',
			'field'	=> 'field_order',
			'id'	=> 'field_id',
			'hideOrder'		=> true
		),
		'status_management' => array(
			'table' => 'exp_statuses',
			'field'	=> 'status_order',
			'id'	=> 'status_id'
		)
	);

	/**
	 * Constructor
	 */
	function Facelift_acc()
	{
		$this->EE =& get_instance();
		$this->EE->lang->loadfile('facelift');
		$this->settings = $this->get_settings();
		
		if($this->EE->input->get('M') != '' && array_key_exists($this->EE->input->get('M'),$this->pages)){
			
			$this->current_page = $this->pages[$this->EE->input->get('M')];
			
			$this->EE->cp->load_package_js('jquery.tablednd_0_5');
			$this->EE->cp->load_package_js('jquery.json-2.2.min');
			$this->EE->cp->load_package_js('facelift');
			$this->EE->cp->add_to_foot('
			<script type="text/javascript">
			//<![CDATA[
			EE.facelift = {
				draggable		: {
					enabled		: "' . $this->settings['facelift_draggable'] . '",
					table 		: "' . $this->current_page['table'] . '",
					field		: "' . $this->current_page['field'] . '",
					id 			: "' . $this->current_page['id'] . '"
				}
			}
			//]]>
			</script>
			');
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Set Sections
	 *
	 * Set content for the accessory
	 *
	 * @access	public
	 * @return	void
	 */
	function set_sections()
	{	
		$this->settings = $this->get_settings();
		
		$scripts 	= array(
			'inline'	=> array(),
			'ready'		=> array(),
			'load'		=> array()
		);
		$styles 	= array();
		
		// ------------------------------
		// Global Scripts & Styles
		// ------------------------------		

		array_push($scripts['inline'],'$("#accessoryTabs .' . $this->id  . '").parent("li").hide();');

		
		// ------------------------------
		// Draggable Sections
		// ------------------------------

		if($this->settings['facelift_draggable'] == 'yes' && isset($this->current_page['hideOrder']) && $this->current_page['hideOrder']){
			array_push($scripts['inline'],'$(".mainTable").find("tr th:nth-child(2),tr td:nth-child(2)").hide();');
		}
		
		
		// ------------------------------
		// Fixed Positioned Accessories
		// ------------------------------
		
		if($this->settings['facelift_fixed_acc'] == 'yes'){
			array_push($styles,'#accessoriesDiv { position:fixed; width:100%; bottom:0; }');
		}
		
		// ------------------------------
		// Assemble Accessory
		// ------------------------------
		
		$section  = '<script type="text/javascript">' 				. "\n";

		foreach($scripts['inline'] as $script) $section .= $script	. "\n";

		$section .= '$(function(){'					 				. "\n";
		foreach($scripts['ready'] as $script) $section .= $script	. "\n";
		$section .= '});'							 				. "\n";

		$section .= '$(window).load(function(){'	 				. "\n";
		foreach($scripts['load'] as $script) $section .= $script	. "\n";
		$section .= '});'							 				. "\n";

		$section .= '</script>' 									. "\n";
		
		$section .= '<style type="text/css">'						. "\n";
		foreach($styles as $style) $section .= $style				. "\n";
		$section .= '</style>' 										. "\n";
		
		$this->sections[$section] = "These are not the accessories you're looking for.";
	}
	
	// --------------------------------------------------------------------

	/**
	 * Get Settings
	 *
	 * Get settings form extension
	 *
	 * @access	public
	 * @return	array
	 */
	function get_settings($all_sites = FALSE)
	{
		$get_settings = $this->EE->db->query("SELECT settings 
			FROM exp_extensions 
			WHERE class = '".$this->extension."' 
			LIMIT 1");
		
		$this->EE->load->helper('string');
		
		if ($get_settings->num_rows() > 0 && $get_settings->row('settings') != '')
        {
        	$settings = strip_slashes(unserialize($get_settings->row('settings')));
        	$settings = ($all_sites == FALSE && isset($settings[$this->EE->config->item('site_id')])) ? 
        		$settings[$this->EE->config->item('site_id')] : 
        		$settings;
        }
        else
        {
        	$settings = array();
        }
        return $settings;
	}	

	// --------------------------------------------------------------------

}
// END CLASS

/* End of file acc.facelift.php */
/* Location: ./system/expressionengine/third_party/facelift/acc.facelift.php */