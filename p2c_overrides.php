<?php
function qa_admin_sub_navigation()
{
	$navigation = qa_admin_sub_navigation_base();
	//print_r($navigation);
	
	return $navigation;
}


function qa_page_q_post_rules($post, $parentpost=null, $siblingposts=null, $childposts=null)
{
	//setup vars and initiate p2c class
	$p2c = new p2c_category_permission;
	$categoryid = $post['categoryid'];
	
	// run the original function and get all the info
	$rules=qa_page_q_post_rules_base($post, $parentpost=null, $siblingposts=null, $childposts=null);
	
	//check to see if user has permission to view the category, if not, then hide the question
	if (!$p2c->has_permit($categoryid))
		$rules['viewable']=0;

	return $rules;
}