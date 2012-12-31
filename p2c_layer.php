<?php

class qa_html_theme_layer extends qa_html_theme_base
{
	/**
	 * (Adds the field to select a permission level for the category)
	 * 
	 * @see qa_html_theme_base::doctype()
	 */
	function doctype()
	{
		$permitoptions = array(
				QA_USER_LEVEL_BASIC 	=> 'Anyone+',
				QA_USER_LEVEL_EXPERT 	=> 'Expert+',
				QA_USER_LEVEL_EDITOR	=> 'Editor+',
				QA_USER_LEVEL_MODERATOR => 'Moderator+',
				QA_USER_LEVEL_ADMIN 	=> 'Admin+',
				QA_USER_LEVEL_SUPER 	=> 'Super Admin'
				);
		
		if( $this->request == 'admin/categories' &&  qa_get('edit') >= 1 ) {
			$p2c = qa_load_module('process', 'Permissions2Categories');
			$categoryvalue = $permitoptions[$p2c->category_permit_level(qa_get('edit'))];

			$this->content['form']['fields'][] = array(
					'tags' => 'NAME="p2c_permit_level" ID="p2c_form"',
					'label' => 'Select permission level requirement',
					'type' => 'select',
					'options' => $permitoptions,
					'value' => $categoryvalue
					);
		}
	
		qa_html_theme_base::doctype();
	}
	
	/**
	 * Adds a layer for a permission check to the question list. If user does not have the permission to view the category the question is not sent to output.
	 * 
	 * @see qa_html_theme_base::q_list_item()
	 */
	function q_list_item($q_item)
	{
		$p2c = qa_load_module('process', 'Permissions2Categories');
		$categoryid = $q_item['raw']['categoryid'];
						
		if ($p2c->has_permit($categoryid))
			qa_html_theme_base::q_list_item($q_item);
	}
	
	/**
	 * Adds a layer for a permission check to the category list. If user does not have the permission to view the category the category list item is not sent to output.
	 *
	 * @see qa_html_theme_base::nav_item()
	 */
	function nav_item($key, $navlink, $class, $level=null)
	{
		$p2c = qa_load_module('process', 'Permissions2Categories');
		
		if ( isset($navlink['categoryid']) && ($class == 'nav-cat' || $class == 'browse-cat') ) {
			$categoryid = $navlink['categoryid'];
			
			if ($p2c->has_permit($categoryid))
				qa_html_theme_base::nav_item($key, $navlink, $class, $level=null);
		}
		
		if ( !isset($navlink['categoryid']) ) //if the navlink is not a category use parent class method.
			qa_html_theme_base::nav_item($key, $navlink, $class, $level=null);
	}
}