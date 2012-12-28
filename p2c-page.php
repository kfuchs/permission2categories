<?php

class p2c_page {

	var $directory;
	var $urltoroot;


	function load_module($directory, $urltoroot)
	{
		$this->directory=$directory;
		$this->urltoroot=$urltoroot;
	}


	function suggest_requests() // for display in admin interface
	{
		return array(
				array(
						'title' => 'Example',
						'request' => 'admin/p2c',
						'nav' => 'M', // 'M'=main, 'F'=footer, 'B'=before main, 'O'=opposite main, null=none
				),
		);
	}


	function match_request($request)
	{
		if ($request=='admin/p2c')
			return true;

		return false;
	}


	function process_request($request)
	{
		$qa_content=qa_content_prepare();

			$qa_content['title']=qa_lang_html('admin/admin_title').' - '.qa_lang_html('admin/categories_title');
	
	$qa_content['error']=qa_admin_page_error();
	
	if ($setmissing) {
		$qa_content['form']=array(
			'tags' => 'METHOD="POST" ACTION="'.qa_path_html(qa_request()).'"',
			
			'style' => 'tall',
			
			'fields' => array(
				'reassign' => array(
					'label' => isset($editcategory)
						? qa_lang_html_sub('admin/category_no_sub_to', qa_html($editcategory['title']))
						: qa_lang_html('admin/category_none_to'),
					'loose' => true,
				),
			),
			
			'buttons' => array(
				'save' => array(
					'label' => qa_lang_html('main/save_button'),
				),
				
				'cancel' => array(
					'tags' => 'NAME="docancel"',
					'label' => qa_lang_html('main/cancel_button'),
				),
			),
			
			'hidden' => array(
				'dosetmissing' => '1', // for IE
				'edit' => @$editcategory['categoryid'],
				'missing' => '1',
			),
		);

		qa_set_up_category_field($qa_content, $qa_content['form']['fields']['reassign'], 'reassign',
			$categories, @$editcategory['categoryid'], qa_opt('allow_no_category'), qa_opt('allow_no_sub_category'));
			
	
	} elseif (isset($editcategory)) {

		$qa_content['form']=array(
			'tags' => 'METHOD="POST" ACTION="'.qa_path_html(qa_request()).'"',
			
			'style' => 'tall',

			'ok' => qa_get('saved') ? qa_lang_html('admin/category_saved') : (qa_get('added') ? qa_lang_html('admin/category_added') : null),
			
			'fields' => array(
				'name' => array(
					'id' => 'name_display',
					'tags' => 'NAME="name" ID="name"',
					'label' => qa_lang_html(count($categories) ? 'admin/category_name' : 'admin/category_name_first'),
					'value' => qa_html(isset($inname) ? $inname : @$editcategory['title']),
					'error' => qa_html(@$errors['name']),
				),
				
				'questions' => array(),
				
				'delete' => array(),
				
				'reassign' => array(),
				
				'slug' => array(
					'id' => 'slug_display',
					'tags' => 'NAME="slug"',
					'label' => qa_lang_html('admin/category_slug'),
					'value' => qa_html(isset($inslug) ? $inslug : @$editcategory['tags']),
					'error' => qa_html(@$errors['slug']),
				),
				
				'content' => array(
					'id' => 'content_display',
					'tags' => 'NAME="content"',
					'label' => qa_lang_html('admin/category_description'),
					'value' => qa_html(isset($incontent) ? $incontent : @$editcategory['content']),
					'error' => qa_html(@$errors['content']),
					'rows' => 2,
				),
			),
			
			'buttons' => array(
				'save' => array(
					'label' => qa_lang_html(isset($editcategory['categoryid']) ? 'main/save_button' : 'admin/add_category_button'),
				),
				
				'cancel' => array(
					'tags' => 'NAME="docancel"',
					'label' => qa_lang_html('main/cancel_button'),
				),
			),
			
			'hidden' => array(
				'dosavecategory' => '1', // for IE
				'edit' => @$editcategory['categoryid'],
				'parent' =>  @$editcategory['parentid'],
				'setparent' => (int)$setparent,
			),
		);
		
		
		if ($setparent) {
			unset($qa_content['form']['fields']['delete']);
			unset($qa_content['form']['fields']['reassign']);
			unset($qa_content['form']['fields']['questions']);
			unset($qa_content['form']['fields']['content']);
			
			$qa_content['form']['fields']['parent']=array(
				'label' => qa_lang_html('admin/category_parent'),
			);
				
			$childdepth=qa_db_category_child_depth($editcategory['categoryid']);
	
			qa_set_up_category_field($qa_content, $qa_content['form']['fields']['parent'], 'parent',
				isset($incategories) ? $incategories : $categories, isset($inparentid) ? $inparentid : @$editcategory['parentid'],
				true, true, QA_CATEGORY_DEPTH-1-$childdepth, @$editcategory['categoryid']);
				
			$qa_content['form']['fields']['parent']['options']['']=qa_lang_html('admin/category_top_level');
			
			@$qa_content['form']['fields']['parent']['note'].=qa_lang_html_sub('admin/category_max_depth_x', QA_CATEGORY_DEPTH);

		} elseif (isset($editcategory['categoryid'])) { // existing category
			if ($hassubcategory) {
				$qa_content['form']['fields']['name']['note']=qa_lang_html('admin/category_no_delete_subs');
				unset($qa_content['form']['fields']['delete']);
				unset($qa_content['form']['fields']['reassign']);

			} else {
				$qa_content['form']['fields']['delete']=array(
					'tags' => 'NAME="dodelete" ID="dodelete"',
					'label' =>
						'<SPAN ID="reassign_shown">'.qa_lang_html('admin/delete_category_reassign').'</SPAN>'.
						'<SPAN ID="reassign_hidden" STYLE="display:none;">'.qa_lang_html('admin/delete_category').'</SPAN>',
					'value' => 0,
					'type' => 'checkbox',
				);
			
				$qa_content['form']['fields']['reassign']=array(
					'id' => 'reassign_display',
					'tags' => 'NAME="reassign"',
				);
				
				qa_set_up_category_field($qa_content, $qa_content['form']['fields']['reassign'], 'reassign',
					$categories, $editcategory['parentid'], true, true, null, $editcategory['categoryid']);
			}
			
			$qa_content['form']['fields']['questions']=array(
				'label' => qa_lang_html('admin/total_qs'),
				'type' => 'static',
				'value' => '<A HREF="'.qa_path_html('questions/'.qa_category_path_request($categories, $editcategory['categoryid'])).'">'.
								( ($editcategory['qcount']==1)
									? qa_lang_html_sub('main/1_question', '1', '1')
									: qa_lang_html_sub('main/x_questions', number_format($editcategory['qcount']))
								).'</A>',
			);

			if ($hassubcategory && !qa_opt('allow_no_sub_category')) {
				$nosubcount=qa_db_count_categoryid_qs($editcategory['categoryid']);
				
				if ($nosubcount)
					$qa_content['form']['fields']['questions']['error']=
						strtr(qa_lang_html('admin/category_no_sub_error'), array(
							'^q' => number_format($nosubcount),
							'^1' => '<A HREF="'.qa_path_html(qa_request(), array('edit' => $editcategory['categoryid'], 'missing' => 1)).'">',
							'^2' => '</A>',
						));
			}
			
			qa_set_display_rules($qa_content, array(
				'position_display' => '!dodelete',
				'slug_display' => '!dodelete',
				'content_display' => '!dodelete',
				'parent_display' => '!dodelete',
				'children_display' => '!dodelete',
				'reassign_display' => 'dodelete',
				'reassign_shown' => 'dodelete',
				'reassign_hidden' => '!dodelete',
			));

		} else { // new category
			unset($qa_content['form']['fields']['delete']);
			unset($qa_content['form']['fields']['reassign']);
			unset($qa_content['form']['fields']['slug']);
			unset($qa_content['form']['fields']['questions']);
		
			$qa_content['focusid']='name';
		}
		
		if (!$setparent) {
			$pathhtml=qa_category_path_html($categories, @$editcategory['parentid']);
			
			if (count($categories)) {
				$qa_content['form']['fields']['parent']=array(
					'id' => 'parent_display',
					'label' => qa_lang_html('admin/category_parent'),
					'type' => 'static',
					'value' => (strlen($pathhtml) ? $pathhtml : qa_lang_html('admin/category_top_level')),
				);
				
				$qa_content['form']['fields']['parent']['value']=
					'<A HREF="'.qa_path_html(qa_request(), array('edit' => @$editcategory['parentid'])).'">'.
					$qa_content['form']['fields']['parent']['value'].'</A>';
				
				if (isset($editcategory['categoryid']))
					$qa_content['form']['fields']['parent']['value'].=' - '.
						'<A HREF="'.qa_path_html(qa_request(), array('edit' => $editcategory['categoryid'], 'setparent' => 1)).
						'" STYLE="white-space: nowrap;"><SPAN>'.qa_lang_html('admin/category_move_parent').'</A>';
			}

			$positionoptions=array();
			
			$previous=null;
			$passedself=false;
			
			foreach ($categories as $key => $category)
				if (!strcmp($category['parentid'], @$editcategory['parentid'])) {
					if (isset($previous))
						$positionhtml=qa_lang_html_sub('admin/after_x', qa_html($passedself ? $category['title'] : $previous['title']));
					else
						$positionhtml=qa_lang_html('admin/first');
		
					$positionoptions[$category['position']]=$positionhtml;
		
					if (!strcmp($category['categoryid'], @$editcategory['categoryid']))
						$passedself=true;
						
					$previous=$category;
				}
			
			if (isset($editcategory['position']))
				$positionvalue=$positionoptions[$editcategory['position']];
	
			else {
				$positionvalue=isset($previous) ? qa_lang_html_sub('admin/after_x', qa_html($previous['title'])) : qa_lang_html('admin/first');
				$positionoptions[1+@max(array_keys($positionoptions))]=$positionvalue;
			}
	
			$qa_content['form']['fields']['position']=array(
				'id' => 'position_display',
				'tags' => 'NAME="position"',
				'label' => qa_lang_html('admin/position'),
				'type' => 'select',
				'options' => $positionoptions,
				'value' => $positionvalue,
			);
			
			if (isset($editcategory['categoryid'])) {
				$catdepth=count(qa_category_path($categories, $editcategory['categoryid']));
					
				if ($catdepth<QA_CATEGORY_DEPTH) {
					$childrenhtml='';
					
					foreach ($categories as $category)
						if (!strcmp($category['parentid'], $editcategory['categoryid']))
							$childrenhtml.=(strlen($childrenhtml) ? ', ' : '').
								'<A HREF="'.qa_path_html(qa_request(), array('edit' => $category['categoryid'])).'">'.qa_html($category['title']).'</A>'.
								' ('.$category['qcount'].')';
					
					if (!strlen($childrenhtml))
						$childrenhtml=qa_lang_html('admin/category_no_subs');
					
					$childrenhtml.=' - <A HREF="'.qa_path_html(qa_request(), array('addsub' => $editcategory['categoryid'])).
						'" STYLE="white-space: nowrap;"><B>'.qa_lang_html('admin/category_add_sub').'</B></A>';
					
					$qa_content['form']['fields']['children']=array(
						'id' => 'children_display',
						'label' => qa_lang_html('admin/category_subs'),
						'type' => 'static',
						'value' => $childrenhtml,
					);
				} else {
					$qa_content['form']['fields']['name']['note']=qa_lang_html_sub('admin/category_no_add_subs_x', QA_CATEGORY_DEPTH);
				}
				
			}
		}
			
	} else {
		$qa_content['form']=array(
			'tags' => 'METHOD="POST" ACTION="'.qa_path_html(qa_request()).'"',
			
			'ok' => $savedoptions ? qa_lang_html('admin/options_saved') : null,
			
			'style' => 'tall',
			
			'fields' => array(
				'intro' => array(
					'label' => qa_lang_html('admin/categories_introduction'),
					'type' => 'static',
				),
			),
			
			'buttons' => array(
				'save' => array(
					'tags' => 'NAME="dosaveoptions"',
					'label' => qa_lang_html('main/save_button'),
				),
				
				'add' => array(
					'tags' => 'NAME="doaddcategory"',
					'label' => qa_lang_html('admin/add_category_button'),
				),			
			),
		);

		if (count($categories)) {
			unset($qa_content['form']['fields']['intro']);
			
			$navcategoryhtml='';

			foreach ($categories as $category)
				if (!isset($category['parentid']))
					$navcategoryhtml.='<A HREF="'.qa_path_html('admin/categories', array('edit' => $category['categoryid'])).'">'.
						qa_html($category['title']).'</A> - '.qa_lang_html_sub('main/x_questions', $category['qcount']).'<BR/>';

			$qa_content['form']['fields']['nav']=array(
				'label' => qa_lang_html('admin/top_level_categories'),
				'type' => 'static',
				'value' => $navcategoryhtml,
			);
				
			$qa_content['form']['fields']['allow_no_category']=array(
				'label' => qa_lang_html('options/allow_no_category'),
				'tags' => 'NAME="option_allow_no_category"',
				'type' => 'checkbox',
				'value' => qa_opt('allow_no_category'),
			);
			
			if (!qa_opt('allow_no_category')) {
				$nocatcount=qa_db_count_categoryid_qs(null);
				
				if ($nocatcount)
					$qa_content['form']['fields']['allow_no_category']['error']=
						strtr(qa_lang_html('admin/category_none_error'), array(
							'^q' => number_format($nocatcount),
							'^1' => '<A HREF="'.qa_path_html(qa_request(), array('missing' => 1)).'">',
							'^2' => '</A>',
						));
			}
			
			$qa_content['form']['fields']['allow_no_sub_category']=array(
				'label' => qa_lang_html('options/allow_no_sub_category'),
				'tags' => 'NAME="option_allow_no_sub_category"',
				'type' => 'checkbox',
				'value' => qa_opt('allow_no_sub_category'),
			);

		} else
			unset($qa_content['form']['buttons']['save']);
	}

	if (qa_get('recalc')) {
		$qa_content['form']['ok']='<SPAN ID="recalc_ok">'.qa_lang_html('admin/recalc_categories').'</SPAN>';
		
		$qa_content['script_rel'][]='qa-content/qa-admin.js?'.QA_VERSION;
		$qa_content['script_var']['qa_warning_recalc']=qa_lang('admin/stop_recalc_warning');
		
		$qa_content['script_onloads'][]=array(
			"qa_recalc_click('dorecalccategories', document.getElementById('recalc_ok'), null, 'recalc_ok');"
		);
	}
	
	$qa_content['navigation']['sub']=qa_admin_sub_navigation();

	
	return $qa_content;
	}

}


/*
 Omit PHP closing tag to help avoid accidental output
*/