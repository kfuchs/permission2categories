<?php

/**
 * This is an override of the core function 'qa_page_q_post_rules'.
 * Adds another permissions check to see if the user has the right permit level for the category the question is in. 
 * If not the question will be blocked.
 * 
 * @see qa_page_q_post_rules() in core files
 */
function qa_page_q_post_rules($post, $parentpost=null, $siblingposts=null, $childposts=null)
{
	//setup vars and initiate p2c class
	$p2c = qa_load_module('process', 'Permissions2Categories');
	$categoryid = $post['categoryid'];
	
	// run the original function and get all the info
	$rules=qa_page_q_post_rules_base($post, $parentpost, $siblingposts, $childposts);
	
	//check to see if user has permission to view the category, if not, then hide the question
	if (!$p2c->has_permit($categoryid))
		$rules['viewable']=0;

	return $rules;
}
