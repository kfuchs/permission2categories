<?php

class p2c_category_permission 
{	
	function process_event($event, $userid, $handle, $cookieid, $params) 
	{
		echo 'hello ajax'.$event;
	}
	
	/*
	function process_event($event, $userid, $handle, $cookieid, $params) {
		if (qa_opt('expert_question_enable')) {
			switch ($event) {
				case 'q_post':
					if(qa_post_text('is_expert_question') == 'yes' ||  (in_array(qa_opt('expert_question_type'),array(1,2)) && !qa_get_logged_in_userid()) || qa_opt('expert_question_type') == 3) {
						require_once QA_INCLUDE_DIR.'qa-app-post-update.php';
						qa_question_set_hidden($params, true, $userid, $handle, $cookieid, array(), array());
	
						qa_db_query_sub(
								"INSERT INTO ^postmeta (post_id,meta_key,meta_value) VALUES (#,'is_expert_question','1')",
								$params['postid']
						);
					}
			}
		}
	}
	*/
	
	/*
	 * Retrives the permission levels for catagories from the qa_categorymetas table and sets up an associative array with category id => permission level
	 *
	 */
	function get_category_permit_levels() 
	{
		$category_permit_levels = array();
		$category_permissions = qa_db_read_all_assoc(qa_db_query_sub('
				SELECT categoryid, content 
				FROM ^categorymetas 
				WHERE title=\'p2c_permission_level\'
				'));

		foreach ($category_permissions as $value)
			$category_permit_levels[$value['categoryid']] = $value['content'];
		
		return $category_permit_levels;
	}
	
	
	/*
	 * Checks the permission level needed to access $categoryid
	 * 
	 */
	function category_permit_level($categoryid) 
	{
		$all_permit_levels = $this->get_category_permit_levels();

		if ( array_key_exists($categoryid, $all_permit_levels) )
			return $all_permit_levels[$categoryid];
		else 
			return 0;	
	}
	
	
	/*
	 * Returns true if the logged in user has the required permission level to access $categoryid else false
	 * 
	 */
	function has_permit($categoryid) 
	{
		if ( qa_get_logged_in_level() >= $this->category_permit_level($categoryid) )
			return true;
		else
			return false;
	}
	
	function set_catpermissions() 
	{
	
	}

}