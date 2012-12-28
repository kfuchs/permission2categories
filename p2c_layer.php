<?php

class qa_html_theme_layer extends qa_html_theme_base
{
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
		
		if( $this->request == 'admin/categories' && ((isset($_GET['edit']) > 0) || isset($_POST['doaddcategory']) == 'Add Category') ) {
			$this->content['form']['fields'][] = array(
					'tags' => 'NAME="p2c_permit_level" ID="p2c_form"',
					'label' => 'Select permission level requirement',
					'type' => 'select',
					'options' => $permitoptions,
					);
		}
/*
		if ( isset($_GET['saved']) == 1 || isset($_GET['added']) == 1 ){
			echo 'hello';
			print_r($_POST);
		}
	*/	
		qa_html_theme_base::doctype();
	}
	
	
	function q_list_item($q_item)
	{
		$p2c = new p2c_category_permission();
		$categoryid = $q_item['raw']['categoryid'];
						
		if ($p2c->has_permit($categoryid))
			qa_html_theme_base::q_list_item($q_item);
	}
}