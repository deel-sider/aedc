<?php
// <?php
/**
 * ied_plugin_composer
 *
 * A Textpattern CMS plugin for writing, editing and sharing plugins
 *  -> Create and edit admin-side or public plugins
 *  -> Supports plugin lifecycle events and prefs
 *  -> Supports Textpacks
 *  -> Optional syntax checker on save
 *
 * @author Yura Linnyk
 * @author Stef Dawson
 * @author Steve Dickinson
 * @link   http://stefdawson.com/
 */

// TODO:
//  * Use href() (from 4.6.+) for anchor creation to avoid double-encoded ampersands
//  * Figure out why syntax checker doesn't jump to line number sometimes (AJAX fails with error but it's not handled)
//  * Fix CSS/Markup so Textpack strings twisty label doesn't range right
//  * Show which langs have installed strings in the distribution section so the correct langs in the select list can be chosen
//  * Find out why uploading PHP files sometiems throws an error even though it succeeds
//  * jQuery on editor dropdowns in setup
//  * phpdoc

global $ied_plugin_globals;
$ied_plugin_globals = array(
	'css_start' => '<!--',
	'css_end'   => '-->',
	'dlm_start' => '#',
	'dlm_end'   => '',
	'start'     => ' --- BEGIN PLUGIN SECTION ---',
	'end'       => ' --- END PLUGIN SECTION ---',
	'size_help' => '63535',
	'size_css'  => '2000',
	'size_code' => '16777215',
);

if(@txpinterface == 'admin') {
	add_privs('ied_plugin_composer','1,2');
	add_privs('plugin_prefs.ied_plugin_composer','1,2');
	register_tab('extensions', 'ied_plugin_composer', gTxt('ied_plugin_lbl_composer'));
	register_callback('ied_plugin_composer', 'ied_plugin_composer');
	register_callback('ied_plugin_setup', 'plugin_prefs.ied_plugin_composer');
	register_callback('ied_plugin_welcome', 'plugin_lifecycle.ied_plugin_composer');
	register_callback('ied_plugin_inject_css', 'admin_side', 'head_end');

	global $ied_pc_event, $prefs;
	$ied_pc_event = 'ied_plugin_composer';
} else {
	register_callback('ied_plugin_download', 'pretext');
}

// -------------------------------------------------------------
// CSS definitions: hopefully kind to themers
function ied_pc_get_style_rules() {
	$ied_pc_styles = array(
		'ied_plugin' => '
#ied_plugin64 { width:60%; }
#ied_plugin_control h3 { text-align:left; }
input[type="submit"] { margin:0.3em 0.7em; }
.ied_label { margin:0 0.2em 0 0.6em;}
.ied_plugin_setup { float:right; margin:-2em 0 0;}
.ied_plugin_resizehandle { cursor:s-resize; float:left; text-align:center; font-size:1em; width:65%; padding:2px 0 6px; }
.ied_plugin_info_bar { text-align:right; }
#ied_plugin_jumpToLine { width:4em; margin:0 1em 0 0.4em; }
.ied_editForm { width:{edwidth}; margin:0 auto; }
.ied_subdue { color:gray; padding:1px 2px 2px 1px; }
#ied_plugin_tp_controls input[type="text"] { width:16%; }
#ied_plugin_tp_strings ul { list-style-type:none; }
#ied_plugin_tp_strings ul label { margin:0 8px 0 0; }
#ied_plugin_tp_strings ul input { width:450px; }
.ied_plugin_edit_toolbar { text-align:right; width:95%; display:inline-block; margin:-2em 0 0 0; }
#ied_plugin_msgpop { display:none; position:absolute; left:200px; max-width:500px; border:3px ridge #999; opacity:.92; filter:alpha(opacity:92); padding:15px 20px; background-color:#e2dfce; color:#80551e; }
#ied_plugin_msgpop .publish { float:right; }
',
	);

	return $ied_pc_styles;
}

// -------------------------------------------------------------
function ied_plugin_inject_css($evt, $stp) {
	global $ied_pc_event, $event;

	if ($event == $ied_pc_event) {
		$ied_plugin_prefs = ied_pc_get_prefs();
		$ied_plugin_styles = ied_pc_get_style_rules();

		// Possible variable replacements
		$edwidth = get_pref('ied_plugin_editor_width', $ied_plugin_prefs['ied_plugin_editor_width']['default']);
		$stylereps = array(
			'{edwidth}' => $edwidth,
		);

		echo '<style type="text/css">' . strtr($ied_plugin_styles['ied_plugin'], $stylereps) . '</style>';
	}

	return;
}

// -------------------------------------------------------------
// Plugin jumpoff point
function ied_plugin_composer($evt, $stp) {

	$available_steps = array(
		'ied_plugin_code_save'        => true,
		'ied_plugin_create'           => true,
		'ied_plugin_delete'           => true,
		'ied_plugin_edit'             => false,
		'ied_plugin_generate_phpdoc'  => true,
		'ied_plugin_help'             => true,
		'ied_plugin_help_viewer'      => false,
		'ied_plugin_install'          => true,
		'ied_plugin_lang_set'         => true,
		'ied_plugin_table'             => false,
		'ied_plugin_multi_edit'       => true,
		'ied_plugin_prefs'            => false,
		'ied_plugin_restore'          => true,
		'ied_plugin_save'             => true,
		'ied_plugin_save_as_file'     => true,
		'ied_plugin_save_as_php_file' => true,
		'ied_plugin_save_as_textpack' => true,
		'ied_plugin_set_order'        => true,
		'ied_plugin_set_tp_prefix'    => true,
		'ied_plugin_switch_status'    => true,
		'ied_plugin_textpack_del'     => true,
		'ied_plugin_textpack_get'     => true,
		'ied_plugin_textpack_load'    => true,
		'ied_plugin_textpack_save'    => true,
		'ied_plugin_upload'           => true,
		'save_pane_state'             => true,
	);

	if ($stp == 'save_pane_state') {
		$stp = 'ied_plugin_save_pane_state';
	} else if (!$stp or !bouncer($stp, $available_steps)) {
		$stp = 'ied_plugin_table';
	}
	$stp();
}

// -------------------------------------------------------------
// Lifecycle handling, post-install / delete
function ied_plugin_welcome($evt, $stp) {
	$msg = '';
	switch ($stp) {
		case 'installed':
			ied_plugin_prefs_update();
			$msg = 'Thanks for installing the plugin composer. Happy authoring :-)';
			break;
		case 'deleted':
			ied_plugin_prefs_remove(0);
			break;
	}
	return $msg;
}

// -------------------------------------------------------------
// Table of plugins in both database and file system cache
function ied_plugin_table($message='') {
	global $prefs, $ied_pc_event;

	pagetop(gTxt('ied_plugin_lbl_composer'),$message);

	require_privs('ied_plugin_composer');

	$ied_plugin_prefs = ied_pc_get_prefs();

	$lc_opts = do_list(get_pref('ied_plugin_lifecycle_options'));
	$auto_en = get_pref('ied_plugin_auto_enable');

	$cbout[] = '<p><label class="ied_label">'.gTxt('ied_plugin_run_install').'</label>';
	$checked = in_array('installed', $lc_opts);
	$cbout[] = yesnoradio('ied_plugin_installopts', $checked);
	$cbout[] = '<label class="ied_label">'.gTxt('ied_plugin_auto_enable').'</label>';
	$cbout[] = radioset($ied_plugin_prefs['ied_plugin_auto_enable']['content'], 'ied_plugin_autoenable', $auto_en).'</p>';

	extract(gpsa(array('sort', 'dir')));

	if ($sort === '') $sort = get_pref('ied_plugin_sort_column', 'name');
	if ($dir === '') $dir = get_pref('ied_plugin_sort_dir', 'asc');
	$dir = ($dir == 'desc') ? 'desc' : 'asc';
	if (!in_array($sort, array('name', 'status', 'author', 'version', 'load_order'))) $sort = 'name';

	$sort_sql = $sort.' '.$dir;

	set_pref('ied_plugin_sort_column', $sort, 'ied_plugin', PREF_HIDDEN, '', 0, PREF_PRIVATE);
	set_pref('ied_plugin_sort_dir', $dir, 'ied_plugin', PREF_HIDDEN, '', 0, PREF_PRIVATE);

	$switch_dir = ($dir == 'desc') ? 'asc' : 'desc';

	// Top control-panel part of screen
	echo '<h1 class="txp-heading">'.gTxt('ied_plugin_lbl_composer').sp.ied_plugin_anchor($ied_pc_event, 'ied_plugin_help_viewer', '?', array('name' => 'ied_plugin_composer'), array('class' => 'pophelp')).'</h1>'.
		n. '<div id="ied_plugin_control" class="txp-control-panel">'.
		n. sLink($ied_pc_event, 'ied_plugin_prefs', gTxt('ied_plugin_setup'), 'ied_plugin_setup').
		n. '<div class="summary-details"><h3 class="lever txp-summary'.(get_pref('pane_ied_plugin_cpanel_visible') ? ' expanded' : '').'"><a href="#ied_plugin_cpanel">' . gTxt('ied_plugin_cpanel_legend') . '</a></h3><div id="ied_plugin_cpanel" class="toggle" style="display:'.(get_pref('pane_ied_plugin_cpanel_visible') ? 'block' : 'none').'">'.
		n. '<form class="ied_plugin_form" enctype="multipart/form-data" action="index.php" method="post">'.
		n. '<p>'.
		n. '<label for="ied_plugin_newname" class="ied_label">'.gTxt('name').'</label>'.
		n. fInput('text', 'name', '', '', '', '', INPUT_REGULAR, '', 'ied_plugin_newname').
		n. fInput('submit', 'plugin_create', gTxt('ied_plugin_create_new')).
		n. '</p>'.
		n. '<p>'.
		n. '<label for="ied_plugin_file" class="ied_label">'.gTxt('ied_plugin_upload_php').'</label>'.
		n. fInput('file', 'thefile', '', '', '', '', '', '', 'ied_plugin_file').
		n. fInput('submit', 'plugin_upload', gTxt('upload')).
		n. '</p>'.
		n. '<p>'.
		n. '<label for="ied_plugin64" class="ied_label">'.gTxt('ied_plugin_install_txt').'</label>'.
		n. text_area('plugin64', '', '', '', 'ied_plugin64').
		n. fInput('submit', 'plugin_install', gTxt('install')).
		n. '</p>'.
		n. join(n, $cbout).
		n. eInput($ied_pc_event).
		n. sInput('ied_plugin_create').
		n. hInput('MAX_FILE_SIZE', 1000000).
		n. tInput().
		n. '</form>'.
		n. '</div>'.
		n. '</div>'.
		n. '</div>';

	// Main plugin list
	echo n. '<div id="ied_plugin_container" class="txp-container">';

	$rs = safe_rows('*', 'txp_plugin', '1=1 ORDER BY '.$sort_sql);

	if ($rs) {
		echo '<div class="summary-details">'.
			n. '<form action="index.php" id="ied_plugin_db_form" method="post">'.
			n. '<h3 class="lever txp-summary'.(get_pref('pane_ied_plugin_dbplugs_visible') ? ' expanded' : '').'">'.
			n. '<a href="#ied_plugin_dbplugs">' . gTxt('ied_plugin_dbplugs_legend') . '</a>'.
			n. '</h3>'.
			n. '<div id="ied_plugin_dbplugs" class="toggle" style="display:'.(get_pref('pane_ied_plugin_dbplugs_visible') ? 'block' : 'none').'">'.
		n. '<div class="txp-listtables">'.
		n. startTable('', '', 'txp-list').
		n.'<thead>'.
		n. tr(
			n.hCell(fInput('checkbox', 'select_all', 0, '', '', '', '', '', 'select_all'), '', ' title="'.gTxt('toggle_all_selected').'" class="multi-edit"')
			.n.column_head('plugin', 'name', 'ied_plugin_composer', true, $switch_dir, '', '', (('name' == $sort) ? "$dir " : '').'name')
			.n.column_head('author', 'author', 'ied_plugin_composer', true, $switch_dir, '', '', (('author' == $sort) ? "$dir " : '').'author')
			.n.column_head(gTxt('version').' ('.gTxt('plugin_modified').')', 'version', 'ied_plugin_composer', true, $switch_dir, '', '', (('version' == $sort) ? "$dir " : '').'version')
			.n.hCell(gTxt('description'), '', ' class="description"')
			.n.hCell(gTxt('manage'), '', ' class="manage"')
			.n.column_head('active', 'status', 'ied_plugin_composer', true, $switch_dir, '', '', (('status' == $sort) ? "$dir " : '').'status')
		).
		n. '</thead>'.
		n. '<tbody>';

		foreach ($rs as $row) {
			extract($row);
			$ename = ied_plugin_anchor($ied_pc_event, 'ied_plugin_edit', $name, array('name' => $name));
			$hlink = ($help) ? ied_plugin_anchor($ied_pc_event, 'ied_plugin_help_viewer', gTxt('ied_plugin_docs'), array('name' => $name)) : gTxt('none');
			$fnames = ied_plugin_get_name($name, $version);
			$pubtag = ied_plugin_anchor($ied_pc_event, 'ied_plugin_save_as_file', gTxt('publish'), array('name' => $name), array('title' => gTxt('ied_plugin_export', array('{name}' => $fnames[0]))));
			$pubztag = ied_plugin_anchor($ied_pc_event, 'ied_plugin_save_as_file', gTxt('ied_plugin_compress'), array('name' => $name, 'type' => 'zip'), array('title' => gTxt('ied_plugin_export', array('{name}' => $fnames[1]))));
			$modified = (strtolower($code) != (strtolower($code_restore)));
			$plugpref = ($flags & PLUGIN_HAS_PREFS) ? (sp.ied_plugin_anchor('plugin_prefs.'.urlencode($name), '', '['.gTxt('plugin_prefs').']', array('name' => $name), array('class' => 'plugin_prefs'.( ($status) ? '' : ' empty'))) ) : '';

			echo tr(
				n.td(
					fInput('checkbox', 'selected[]', $name)
				,'', 'multi-edit')
				.n.td($ename.$plugpref)
				.n.td(( ($author_uri) ? '<a href="'.txpspecialchars($author_uri).'">'.txpspecialchars($author).'</a>' : txpspecialchars($author)))
				.n.td(( ($modified) ? ied_plugin_anchor($ied_pc_event, 'ied_plugin_restore', $version, array('name' => $name), array('title' => gTxt('ied_plugin_restore_help'), 'onclick' => 'return verify(\''.gTxt('ied_plugin_restore_verify', array('{name}' => $name)).'\');')) : $version) . (($modified) ? sp.'('.gTxt('yes').')' : ''))
				.n.td(txpspecialchars($description))
				.n.td($pubtag .sp. '&#124;' .sp. $pubztag .sp. '&#124;' .sp. $hlink)
				.n.td(ied_plugin_status_link($status,$name,yes_no($status)))
			);
			unset($name,$page);
		}
		echo n. '</tbody>'.
			n. endTable().
			n. '</div>'.
			n. tInput().
			n. '</form>'.
			ied_plugin_multiedit_form('db', '', $sort, $dir, '', '').
			n. '</div>'.
			n. '</div>';
	}

	if (!empty($prefs['plugin_cache_dir']) && file_exists($prefs['plugin_cache_dir'])) {
		$filenames = array();
		$directory = dir($prefs['plugin_cache_dir']);
		while ($file = $directory->read()) {
			if($file != "." && $file != "..") {
				$fileaddr = $prefs['plugin_cache_dir'].DS.$file;
				if (!is_dir($fileaddr)) {
					$filenames[]=$file;
				}
			}
		}
		$directory->close();
		($filenames)?natcasesort($filenames):'';

		$out = array();

		foreach($filenames as $filename) {
			$parts = explode ('.',$filename);
			$fileext = array_pop($parts);
			if ($fileext=='php') {
				$basename = basename($filename);
				$plugin = ied_plugin_read_file($prefs['plugin_cache_dir'].DS.$filename);
				$hlink = ($plugin['help']) ? ied_plugin_anchor($ied_pc_event, 'ied_plugin_help_viewer', gTxt('ied_plugin_docs'), array('filename' => $filename)) : gTxt('none');
				$efile = ied_plugin_anchor($ied_pc_event, 'ied_plugin_edit', $plugin['name'], array('filename' => $filename));
				$fnames = ied_plugin_get_name($plugin['name'], $plugin['version']);
				$plugpref = (($plugin['flags'] & PLUGIN_HAS_PREFS)) ? ' '.ied_plugin_anchor('plugin_prefs.'.urlencode($plugin['name']), '', ' ['.gTxt('plugin_prefs').']') : '';
				$pubtag = ied_plugin_anchor($ied_pc_event, 'ied_plugin_save_as_file', gTxt('publish'), array('filename' => $filename), array('title' => gTxt('ied_plugin_export', array('{name}' => $fnames[0]))));
				$pubztag = ied_plugin_anchor($ied_pc_event, 'ied_plugin_save_as_file', gTxt('ied_plugin_compress'), array('filename' => $filename, 'type' => 'zip'), array('title' => gTxt('ied_plugin_export', array('{name}' => $fnames[1]))));

				$out[] = tr(
					n.td(
						fInput('checkbox', 'selected-cache[]', $filename)
					,'', 'multi-edit')
					.n.td(
						tag($filename,'div',' class="ied_subdue"')
						.(isset($plugin['name']) ? $efile.$plugpref.'<br />' : '').' '
					)
					.n.td(
						( isset($plugin['author_uri']) ? '<a href="'.$plugin['author_uri'].'">' : '' ) .
						( isset($plugin['author']) ? $plugin['author'] : '&nbsp;' ).
						( isset($plugin['author_uri']) ? '</a>' : '' )
					)
					.n.td(
						(isset($plugin['version']) ? $plugin['version'] : '&nbsp;')
					)
					.n.td(
						(isset($plugin['description']) ? $plugin['description'] : '&nbsp;')
					)
					.n.td(
						(isset($plugin['name']) ? $pubtag .sp. '&#124;' .sp. $pubztag
							: tag('&nbsp;', 'span')
						)
						.sp. '&#124;' .sp. $hlink
					)
				);
			}
		}

		if ($out) {
			echo '<div class="summary-details">'.
				n. '<form action="index.php" id="ied_plugin_cache_form" method="post">'.
					n. '<h3 class="lever txp-summary'.(get_pref('pane_ied_plugin_cacheplugs_visible') ? ' expanded' : '').'">'.
					n. '<a href="#ied_plugin_cacheplugs">' . gTxt('ied_plugin_cacheplugs_legend') . '</a>'.
					n. '</h3>'.
					n. '<div id="ied_plugin_cacheplugs" class="toggle" style="display:'.(get_pref('pane_ied_plugin_cacheplugs_visible') ? 'block' : 'none').'">'.
				n. '<div class="txp-listtables ied_plugin_cacheplugs">'.
				n.startTable('', '', 'txp-list').
				n. '<thead>'.
				n. tr(
					n.hCell(fInput('checkbox', 'select_all', 0, '', '', '', '', '', 'select_all'), '', ' title="'.gTxt('toggle_all_selected').'" class="multi-edit"')
					.n.hCell(gTxt('plugin'), '', ' class="name"')
					.n.hCell(gTxt('author'), '', ' class="author"')
					.n.hCell(gTxt('version') . ' ('.gTxt('plugin_modified').')', '', ' class="version"')
					.n.hCell(gTxt('description'), '', ' class="description"')
					.n.hCell(gTxt('manage'), '', ' class="manage"')
				).
				n. '</thead>'.
				n. '<tbody>'.
				n. join(n, $out).
				n. '</tbody>'.
				n. endTable().
				n. '</div>'.
				n. tInput().
				n. '</form>'.
				ied_plugin_multiedit_form('cache', '', $sort, $dir, '', '').
				n. '</div>'.
				n. '</div>';
		}
	}

	echo '</div>'.
		n. script_js( <<<EOS
			$(document).ready(function() {
				$('#ied_plugin_db_form').txpMultiEditForm({
					'checkbox' : 'input[name="selected[]"][type=checkbox]'
				});
				$('#ied_plugin_cache_form').txpMultiEditForm({
					'checkbox' : 'input[name="selected-cache[]"][type=checkbox]'
				});
			});
EOS
				);

	// Show/hide "Options" link by setting the appropriate class on the plugin's TR
	echo script_js(<<<EOS
textpattern.Relay.register('txpAsyncHref.success', function(event, data) {
	jQuery(data.this).closest('tr').find('a.plugin_prefs').toggleClass('empty');
});
EOS
	);
}

// -------------------------------------------------------------
function ied_plugin_multiedit_form($flavour, $page, $sort, $dir, $crit, $search_method) {
	global $ied_pc_event;

	$orders = selectInput('order', array(1=>1, 2=>2, 3=>3, 4=>4, 5=>5, 6=>6, 7=>7, 8=>8, 9=>9), 5, false);
	$stati = selectInput('switch_status', array('toggle' => gTxt('ied_plugin_toggle'), 'on' => gTxt('on'), 'off' => gTxt('off')), 'toggle', false);
	$lifecycles = selectInput('ied_lc_event', array(
		'installed'         => gTxt('ied_plugin_lbl_lc_install'),
		'enabled'           => gTxt('ied_plugin_lbl_lc_enable'),
		'installed,enabled' => gTxt('ied_plugin_lbl_lc_instable'),
		'disabled'          => gTxt('ied_plugin_lbl_lc_disable'),
		'deleted'           => gTxt('ied_plugin_lbl_lc_delete'),
		'disabled,deleted'  => gTxt('ied_plugin_lbl_lc_disdel'),
	));

	if ($flavour === 'db') {
		$methods = array(
			'changestatus' => array('label' => gTxt('changestatus'), 'html' => $stati),
			'changeorder'  => array('label' => gTxt('changeorder'), 'html' => $orders),
			'lifecycle'    => array('label' => gTxt('ied_plugin_lifecycle'), 'html' => $lifecycles),
			'delete'       => gTxt('delete'),
		);
	} else {
		$methods = array(
			'lifecycle' => array('label' => gTxt('ied_plugin_lifecycle'), 'html' => $lifecycles),
			'textpack'  => array('label' => gTxt('ied_plugin_install_textpack')),
			'delete'    => gTxt('delete'),
		);
	}

	return multi_edit($methods, $ied_pc_event, 'ied_plugin_multi_edit', $page, $sort, $dir, $crit, $search_method);
}

// -------------------------------------------------------------
function ied_plugin_multi_edit() {
	global $prefs;

	$selected = ps('selected');
	$selected_cache = ps('selected-cache');
	$method = assert_string(ps('edit_method'));

	if ($selected && is_array($selected))
	{
		$where = "name IN ('".join("','", doSlash($selected))."')";
		$lc_opts = do_list(get_pref('ied_plugin_lifecycle_options'));

		switch ($method)
		{
			case 'delete':
				foreach ($selected as $name)
				{
					if (safe_field('flags', 'txp_plugin', "name ='".doSlash($name)."'") & PLUGIN_LIFECYCLE_NOTIFY)
					{
						load_plugin($name, true);
						if (in_array('disabled', $lc_opts)) {
							callback_event("plugin_lifecycle.$name", 'disabled');
						}
						if (in_array('deleted', $lc_opts)) {
							callback_event("plugin_lifecycle.$name", 'deleted');
						}
					}
				}
				safe_delete('txp_plugin', $where);
				break;

			case 'changestatus':
				switch (ps('switch_status')) {
					case 'on':
						$newstat = '1';
						break;
					case 'off':
						$newstat = '0';
						break;
					case 'toggle':
					default:
						$newstat = '(1-status)';
						break;
				}

				foreach ($selected as $name) {
					if (safe_field('flags', 'txp_plugin', "name ='".doSlash($name)."'") & PLUGIN_LIFECYCLE_NOTIFY) {
						$status = safe_field('status', 'txp_plugin', "name ='".doSlash($name)."'");
						$status = $status ? 'disabled' : 'enabled';
						load_plugin($name, true);
						if (in_array($status, $lc_opts)) {
							callback_event("plugin_lifecycle.$name", $status);
						}
					}
				}

				safe_update('txp_plugin', 'status = '.$newstat, $where);
				break;
	
			case 'changeorder':
				$order = min(max(intval(ps('order')), 1), 9);
				safe_update('txp_plugin', 'load_order = '.$order, $where);
				break;

			case 'lifecycle':
				$lc_evs = do_list(ps('ied_lc_event'));
				foreach ($selected as $name) {
					foreach ($lc_evs as $lc_ev) {
						callback_event("plugin_lifecycle.$name", $lc_ev);
					}
				}
				break;
		}
	} else {
		$selected = array();
	}

	if ($selected_cache && is_array($selected_cache))
	{
		switch ($method)
		{
			case 'delete':
				foreach ($selected_cache as $name) {
					$filenames = array();
					$dir = dir($prefs['plugin_cache_dir']);
					while ($file = $dir->read()) {
						if($file != "." && $file != ".." && in_array($file, $selected_cache)) {
							$fileaddr = $prefs['plugin_cache_dir'].DS.$file;
							if (!is_dir($fileaddr)) {
								unlink($fileaddr);
							}
						}
					}
					$dir->close();
				}
				break;

			case 'lifecycle':
				$lc_evs = do_list(ps('ied_lc_event'));
				foreach ($selected_cache as $name) {
					$name = str_replace('.php', '', $name);
					foreach ($lc_evs as $lc_ev) {
						callback_event("plugin_lifecycle.$name", $lc_ev);
					}
				}
				break;

			case 'textpack':
				// Read the textpack from the .php file and call install_textpack()
				$textpack = array();
				foreach ($selected_cache as $name) {
					$fileaddr = $prefs['plugin_cache_dir'].DS.$name;

					$contents = file($fileaddr);
					$in_tp = $in_comment = false;
					foreach ($contents as $row) {
						if (strpos($row, '/**') === 0) {
							$in_comment = true;
						}
						if (strpos($row, '**/') === 0) {
							$in_comment = false;
						}
						if (strpos($row, 'EOT;') !== false) {
							break;
						}
						if ($in_tp === true) {
							$textpack[] = trim($row);
						}
						if (!$in_comment && strpos($row, '$plugin[\'textpack\']') !== false) {
							$in_tp = true;
                  }
               }
				}

				$done = install_textpack(join(n, $textpack));
			break;
		}
	} else {
		$selected_cache = array();
	}

	$message = '';
	if ($selected || $selected_cache) {
		if ($method === 'delete') {
			$message = gTxt('plugin_deleted', array('{name}' => join(', ', array_merge($selected, $selected_cache))));
		} else if ($method === 'lifecycle') {
			$message = gTxt('ied_plugin_lc_fired', array('{name}' => join(', ', array_merge($selected, $selected_cache)), '{event}' => join(',', $lc_evs)));
		} else if ($method === 'textpack') {
			$message = gTxt('textpack_strings_installed', array('{count}' => $done));
		} else {
			$message = gTxt('plugin_updated', array('{name}' => join(', ', $selected)));
		}
	}
	ied_plugin_table($message);
}

// -------------------------------------------------------------
function ied_plugin_anchor($evt, $stp, $linktext, $nv = array(), $atts = array()) {
	$nv['_txp_token'] = form_token();
	$atts['href'] = '?event='.$evt.($stp ? '&step='.$stp : '').'&'.http_build_query($nv);

	$attribs = '';
	foreach ($atts as $n => $v) {
		$attribs .= ' '.txpspecialchars($n).'="'.txpspecialchars($v).'"';
	}

	return tag($linktext, 'a', $attribs);
}

// -------------------------------------------------------------
function ied_plugin_status_link($status, $name, $linktext) {
	return asyncHref($linktext, array('step' => 'ied_plugin_switch_status', 'thing' => $name),' title="'.($status==1 ? gTxt('disable') : gTxt('enable')).'"' );
}

// -------------------------------------------------------------
function ied_plugin_switch_status() {
	extract(array_map('assert_string', gpsa(array('thing', 'value'))));
	$change = ($value == gTxt('yes')) ? 0 : 1;

	safe_update('txp_plugin', "status = $change", "name = '".doSlash($thing)."'");

	$lc_opts = do_list(get_pref('ied_plugin_lifecycle_options'));
	$stp = $change ? 'enabled' : 'disabled';

	if (in_array($stp, $lc_opts) && (safe_field('flags', 'txp_plugin', "name='".doSlash($thing)."'") & PLUGIN_LIFECYCLE_NOTIFY) ) {
		load_plugin($thing, true);
		$message = callback_event("plugin_lifecycle.$thing", $stp);
	}

	echo gTxt($change ? 'yes' : 'no');
}

// -------------------------------------------------------------
function ied_plugin_set_order() {
	extract(doSlash(gpsa(array('name', 'load_order'))));
	$order = min(max( intval($load_order), 1), 9);
	safe_update('txp_plugin', "load_order = $load_order", "name = '$name'");
	ied_plugin_table(gTxt('plugin_saved', array('{name}' => $name)));
}

// -------------------------------------------------------------
function ied_plugin_delete() {
	$name = doSlash(ps('name'));

	$lc_opts = do_list(get_pref('ied_plugin_lifecycle_options'));
	$lc_dis = in_array('disabled', $lc_opts);
	$lc_del = in_array('deleted', $lc_opts);

	if ( ($lc_del || $lc_dis) && (safe_field('flags', 'txp_plugin', "name='".$name."'") & PLUGIN_LIFECYCLE_NOTIFY) ) {
		load_plugin($name, true);
		if ($lc_dis) {
			callback_event("plugin_lifecycle.$name", 'disabled');
		}
		if ($lc_del) {
			callback_event("plugin_lifecycle.$name", 'deleted');
		}
	}

	safe_delete('txp_plugin', "name='$name'");
	ied_plugin_table(gTxt('plugin_deleted', array('{name}' => $name)));
}

// -------------------------------------------------------------
function ied_plugin_restore() {
	$name = doSlash(gps('name'));
	safe_update("txp_plugin","code = code_restore","name='$name'");
	ied_plugin_table(gTxt('ied_plugin_restored', array('{name}' => $name)));
}

// -------------------------------------------------------------
function ied_plugin_edit($message='', $newfile='') {
	global $prefs, $ied_plugin_globals, $ied_pc_event;

	$newname = trim(gps('newname'));
	$filename = gps('filename');
	$editfile = $filename ? 1 : 0;
	$name = empty($newname) ? gps('name') : $newname;
	$name = ($newfile) ? $newfile : (($filename) ? $filename : $name);

	pagetop(gTxt('ied_plugin_editing', array('{name}' => txpspecialchars($name))), $message);
	require_privs('ied_plugin_composer');

	echo ied_insert_editors();

	if (!$editfile) {
		$rs = safe_row("author, author_uri, version, description, code, help, status, type, load_order, flags", "txp_plugin", "name='".doSlash($name)."'");
		extract($rs);
		list($css,$help) = ($help) ? ied_plugin_extract_hunk($help, "CSS", "<!--|-->", true) : array('',$help);
	} else {
		$plugin = ied_plugin_read_file($prefs['plugin_cache_dir'].DS.$name);
		$filename = $name;
		$name = explode ('.', $name);
		$fileext = array_pop($name);
		$name = implode($name);
		extract($plugin);
		$status = ($fileext=='php')? 1: 0;
	}

	$ifel = get_pref('ied_plugin_interface_elems');
	$distblock = (strpos($ifel, 'distribution') !== false);
	$styleblock = (strpos($ifel, 'style') !== false);
	$distribution = '';

	list ($start_css, $end_css) = ied_plugin_make_markers("CSS", $ied_plugin_globals['css_start'], $ied_plugin_globals['css_end']);

	if ($distblock) {
		$plugin['name'] = $name;
		$plugin['author'] = $author;
		$plugin['author_uri'] = $author_uri;
		$plugin['version'] = $version;
		$plugin['description'] = $description;
		$plugin['help'] = ied_plugin_textile($name, $help, $css, $start_css, $end_css);
		$plugin['code'] = $code;
		$plugin['type'] = $type;
		$plugin['order'] = $load_order;
		$plugin['flags'] = $flags;
		$plugin['md5'] = md5( $plugin['code'] );
		$distribution = '<textarea name="distribution" rows="1" onclick="this.select()">'.base64_encode(serialize($plugin)).'</textarea>';
	}

	for ($i = 1; $i <= 9; $i++) $orders[$i] = $i;

	$tp_pfx = unserialize(get_pref('ied_plugin_tp_prefix', '', 1));
	$tp_pfx = isset($tp_pfx[$name]) ? $tp_pfx[$name] : '';

	$fnames = ied_plugin_get_name($name, $version);
	$namedLink = ($filename) ? array('filename' => $filename) : array('name' => $name);
	$zippedLink = array_merge($namedLink, array('type' => 'zip'));

	$slink = ied_plugin_anchor($ied_pc_event, 'ied_plugin_save_as_file', gTxt('ied_plugin_export', array('{name}' => $fnames[0])), $namedLink);
	$sziplink = ied_plugin_anchor($ied_pc_event, 'ied_plugin_save_as_file', gTxt('ied_plugin_export_zip', array('{name}' => $fnames[1])), $zippedLink);
	$sphplink = ied_plugin_anchor($ied_pc_event, 'ied_plugin_save_as_php_file', gTxt('ied_plugin_save_as', array('{name}' => $fnames[2])), $namedLink);
	$stxtlink = ied_plugin_anchor($ied_pc_event, 'ied_plugin_save_as_textpack', gTxt('ied_plugin_export_textpack'), $namedLink);
	$vhelplinkfull = ($help) ? '[ ' .ied_plugin_anchor($ied_pc_event, 'ied_plugin_help_viewer', gTxt('ied_plugin_docs'), $namedLink) . ' ]' : '';

	$msgpop = '<div id="ied_plugin_msgpop"><input type="button" class="publish" value="'.gTxt('ok').'" onclick="ied_plugin_toggle_msgpop(\'0\');" /><h2>'.gTxt('ied_plugin_msgpop_lbl').'</h2><span class="ied_plugin_msgpop_content"></span></div>';

	$newname = fInput('text', 'newname', $name, '', '', '', INPUT_REGULAR);
	$author_widget = fInput('text', 'author', $author, '', '', '', INPUT_REGULAR);
	$author_uri_widget = fInput('text', 'author_uri', $author_uri, '', '', '', INPUT_REGULAR);
	$version_widget = fInput('text', 'version', $version, 'input-small', '', '',INPUT_SMALL) .sp. (($editfile) ? checkbox('rename_file', '1', 0, '','rename_file') . ' <label for="rename_file">'.gTxt('ied_plugin_rename_file').'</label>' : checkbox('restore_point', '1', 0, '','restore_point') . ' <label for="restore_point">'.gTxt('ied_plugin_restore_point').'</label>');
	$description_widget = fInput('text', 'description', $description, 'input-xlarge', '', '', INPUT_LARGE);
	$codeblock = '<textarea name="code" id="plugin_editor" rows="'.INPUT_REGULAR.'" class="code codepress php" maxlength="'.$ied_plugin_globals['size_code'].'">'.txpspecialchars($code).'</textarea><div class="ied_plugin_info_bar"><span>'.gTxt('ied_plugin_jump_to_line').'</span><input type="text" id="ied_plugin_jumpToLine" size="5" maxlength="6" /><span class="ied_plugin_charsRemain"></span></div>';
	$help_widget = '<textarea name="help" rows="'.INPUT_REGULAR.'" class="mceEditor" maxlength="'.$ied_plugin_globals['size_help'].'">'.txpspecialchars($help).'</textarea><div class="ied_plugin_info_bar"><span class="ied_plugin_charsRemain"></span></div>';
	$css_widget = ($styleblock) ? '<textarea name="css" rows="'.INPUT_MEDIUM.'" class="code" maxlength="'.$ied_plugin_globals['size_css'].'">'.txpspecialchars($css).'</textarea><div class="ied_plugin_info_bar"><span class="ied_plugin_charsRemain"></span></div>' : '';
	$plugstatus = (!$editfile) ? sp.sp.checkbox('status',1,$status, '','status'). ' <label for="status">'.gTxt('ied_plugin_enable').'</label>' : '';
	$plugtype = radio('type',0,(($type==0)?1:0)).gTxt('ied_plugin_type_0')." "
		. radio('type',1,(($type==1)?1:0)).gTxt('ied_plugin_type_1')." "
		. radio('type',2,(($type==2)?1:0)).gTxt('ied_plugin_type_2')." "
		. radio('type',3,(($type==3)?1:0)).gTxt('ied_plugin_type_3')." "
		. radio('type',4,(($type==4)?1:0)).gTxt('ied_plugin_type_4')." "
		. radio('type',5,(($type==5)?1:0)).gTxt('ied_plugin_type_5');
	$plugorder = selectInput('load_order', $orders, $load_order, 0, 0);
	$flaglist = checkbox('flags[]',PLUGIN_HAS_PREFS,(($flags & PLUGIN_HAS_PREFS)?1:0)) . '<label>'.gTxt('ied_plugin_flag_has_prefs').'</label>&nbsp;&nbsp;'
		.checkbox('flags[]',PLUGIN_LIFECYCLE_NOTIFY,(($flags & PLUGIN_LIFECYCLE_NOTIFY)?1:0)) . '<label>'.gTxt('ied_plugin_flag_lifecycle_notify').'</label>&nbsp;&nbsp;';
//		.checkbox('flags[]',0x0004,(($flags & 0x0004)?1:0)) . '<label>Summat else</label>&nbsp;&nbsp;';

	$sub = fInput('submit', '', gTxt('save'), 'publish', '', '', '', '', 'ied_editSave');
	$codesub = (!$editfile) ? '<a class="navlink" name="ied_plugin_code_save" id="ied_plugin_code_save">' . gTxt('ied_plugin_code_save') . '</a>' : '';

	// Language info. ied_visible_langs is the user's choice of which ones they want to see available.
	// ied_available_langs is the list of actual, currently-installed langs
	$ied_listlangs = get_pref('ied_plugin_lang_choose', 'installed');
	$ied_visible_langs = ied_plugin_lang_list($ied_listlangs);
	$ied_available_langs = ($ied_listlangs == 'installed') ? $ied_visible_langs : ied_plugin_lang_list('installed');
	$dflt_lang = get_pref('ied_plugin_lang_default', $prefs['language']);
	$dflt_lang = array_key_exists($dflt_lang, $ied_visible_langs) ? $dflt_lang : $prefs['language'];
	$langsel = selectInput('ied_plugin_tp_lang', $ied_visible_langs, $dflt_lang, '', '', 'ied_plugin_tp_lang')
		.fInput('button', 'ied_plugin_tp_refresh', gTxt('ied_plugin_load'), '', '', '', '', '', 'ied_plugin_tp_refresh');

	$preselected = do_list(get_pref('ied_plugin_lang_selected', ''));

	$op_langs[] = '<select name="ied_plugin_tp_oplangs" id="ied_plugin_tp_oplangs" multiple="multiple"><option value=""></option>';
	foreach ($ied_available_langs as $langcode => $alang) {
		$sel = in_array($langcode, $preselected) ? ' selected="selected"' : '';
		$op_langs[] = '<option value="'.$langcode.'"'.$sel.'>'.$alang.'</option>';
	}
	$op_langs[] = '</select>';

	$tp_strings = array();
	$tp_rows = ied_plugin_textpack_grab($dflt_lang, $tp_pfx);
	foreach ($tp_rows as $tp_string) {
		$apsel = selectInput('ied_plugin_tp_event', array('admin' => gTxt('admin'), 'public' => gTxt('public'), 'common' => gTxt('both')), ($tp_string['event'] == 'public' ? 'public' : ($tp_string['event'] == 'common' ? 'common' : 'admin')) );
		$tp_strings[] = '<li>'.fInput('text', 'textpack_'.$tp_string['name'], $tp_string['data']).' '.$apsel.' <label>'.$tp_string['name'].'</label>'.'</li>';
	}


	$err_prefix = gTxt('ied_plugin_syntax_err');
	$codesave_ok = gTxt('ied_plugin_code_saved');
	$phpdoc = '';

	// TODO
	$classFinder = '/class[\s\n]+(\w+)[\s\n]*\{?(function[\s\n]+(\w+)[\s\n]*\(.*\)[\s\n]*\{?)*/';
	$functionFinder = '/function[\s\n]+(\w+)[\s\n]*\(.*\)[\s\n]*\{?/';
	preg_match_all($functionFinder, $code , $functionArray);
	// selectInput requires key and value to be the same
//dmp($functionArray);
	$fnArray = array();
/*
	foreach($functionArray[1] as $key => $val) {
		$fnArray[$val] = $functionArray[1][$key];
   	}
	$phpdoc = selectInput('ied_plugin_to_phpdoc', $fnArray, '', false, '', 'ied_plugin_to_phpdoc')
		.'<a class="navlink" name="ied_plugin_btn_phpdoc" id="ied_plugin_btn_phpdoc">' . gTxt('ied_plugin_php_doc') . '</a>';
*/
	$phpdoc = '';

	echo
		hed(gTxt('ied_plugin_edit', array('{name}' => $name, '{version}' => $version)).n.$vhelplinkfull, 2).
		n. form(
			'<div id="ied_plugin_sub">'. ($sub).'</div>'
			.n. '<div class="summary-details"><h3 class="lever txp-summary'.(get_pref('pane_ied_plugin_meta_visible') ? ' expanded' : '').'"><a href="#ied_plugin_meta">' . gTxt('ied_plugin_meta_legend') . '</a></h3><div id="ied_plugin_meta" class="toggle" style="display:'.(get_pref('pane_ied_plugin_meta_visible') ? 'block' : 'none').'">'
			.n. '<p><label>' . gTxt('name') . '</label>' . sp . $newname . sp. '<label>' . gTxt('version') . '</label>' . sp . $version_widget . $plugstatus . ( ($filename) ? tag(sp.sp.'('.$filename.')','span',' style="color:gray;"').hInput('filename',$filename) : '' ) . '</p>'
			.n. '<p><label>' . gTxt('description') . '</label>' . sp . $description_widget . '</p>'
			.n. '<p><label>' . gTxt('author') . '</label>' . sp . $author_widget . sp. '<label>' . gTxt('website') . '</label>' .sp. $author_uri_widget. '</p>'
			.n. '<p><label>' . gTxt('ied_plugin_type') . '</label>' . sp . $plugtype . '</p>'
			.n. '<p><label>' . gTxt('ied_plugin_flags') . '</label>' . sp . $flaglist .sp. '<label>' . gTxt('ied_plugin_load_order') . '</label>' . sp . $plugorder . sp.sp . gTxt('ied_plugin_load_order_help') . '</p>'
			.n. '</div></div>'

			.n. '<div class="summary-details"><h3 class="lever txp-summary'.(get_pref('pane_ied_plugin_code_visible') ? ' expanded' : '').'"><a href="#ied_plugin_code">'.gTxt('ied_plugin_code_legend').'</a></h3><div id="ied_plugin_code" class="toggle" style="display:'.(get_pref('pane_ied_plugin_code_visible') ? 'block' : 'none').'">'
			.n. '<span class="ied_plugin_edit_toolbar">' . $msgpop . $phpdoc . sp . $codesub . '</span>'
			.n. '<div>' . $codeblock . '</div>'
			.n. '</div></div>'

			.n. '<div class="summary-details"><h3 class="lever txp-summary'.(get_pref('pane_ied_plugin_tp_strings_visible') ? ' expanded' : '').'"><a href="#ied_plugin_tp_strings">' . gTxt('ied_plugin_tp_legend') . ' <span id="ied_plugin_tp_count"></span></a></h3><div id="ied_plugin_tp_strings" class="toggle" style="display:'.(get_pref('pane_ied_plugin_tp_strings_visible') ? 'block' : 'none').'">'
			.n. '<div id="ied_plugin_tp_controls">'
			.n. '<label>'
			.n. gTxt('ied_plugin_tp_prefix')
			.n. '</label>'
			.n. fInput('text', 'ied_plugin_tp_prefix', $tp_pfx, '', '', '', '', '', 'ied_plugin_tp_prefix')
			.n. sp
			.n. $langsel
			.n. sp
			.n. fInput('hidden', 'ied_plugin_tp_lang_dflt', $dflt_lang, '', '', '', '', '', 'ied_plugin_tp_lang_dflt')
			.n. gTxt('ied_plugin_tp_populate').n.'</label>'.fInput('text', 'ied_plugin_tp_populate', '', '', '', '', '', '', 'ied_plugin_tp_populate') . sp . '<button id="ied_plugin_tp_load">'.gTxt('go').'</button>'
			.n. '<span id="ied_plugin_tp_load_count"></span>'
			.n. '</div>'
			.n. '<a href="#" id="ied_plugin_add_string">+</a>'
			.n. '<ul>'
			.n. join(n, $tp_strings)
			.n. '</ul></div></div>'

			.n. '<div class="summary-details"><h3 class="lever txp-summary'.(get_pref('pane_ied_plugin_docs_visible') ? ' expanded' : '').'"><a href="#ied_plugin_docs">'.gTxt('ied_plugin_docs_legend').'</a></h3><div id="ied_plugin_docs" class="toggle" style="display:'.(get_pref('pane_ied_plugin_docs_visible') ? 'block' : 'none').'">'
			.n. '<div>' . $help_widget . '</div>'
			.n. (($styleblock) ? '<div>' . gTxt( 'css' ) . $css_widget . '</div>' : '')
			.n. '</div></div>'
			.n. '<div>' . $sub . '</div>'

			.n. '<div class="summary-details"><h3 class="lever txp-summary'.(get_pref('pane_ied_plugin_utils_visible') ? ' expanded' : '').'"><a href="#ied_plugin_utils">'.gTxt('ied_plugin_utils_legend').'</a></h3><div id="ied_plugin_utils" class="toggle" style="display:'.(get_pref('pane_ied_plugin_utils_visible') ? 'block' : 'none').'">'
			.n. (($distblock) ? '<div>' . gTxt('ied_plugin_code_dist') . $distribution . '</div>' : '')
			.n. '<div>' . join(n, $op_langs) . '</div>'
			.n. '<div>' . $slink . '</div>'
			.n. '<div>' . $sziplink . '</div>'
			.n. '<div>' . $stxtlink . '</div>'
			.n. '<div>' . $sphplink . '</div>'
			.n. '</div></div>'

			.n. sInput('ied_plugin_save')
			.n. eInput($ied_pc_event)
			.n. hInput('name',$name)
		, '', '', 'post', 'ied_editForm').
		script_js(<<<EOF
var ied_plugin_tp_total = 0;
jQuery.fn.ied_plugin_resizehandle = function(curh) {
	return this.each(function() {
		var me = jQuery(this);
		me.animate({height: curh});
		me.after(
			jQuery('<div class="ied_plugin_resizehandle">--- + ---</div>').bind("mousedown", function(e) {
				var h = me.height();
				var y = e.clientY;
				var moveHandler = function(e) {
					me.height(Math.max(20, e.clientY + h - y));
				};
				var upHandler = function(e) {
					jQuery("html").unbind("mousemove",moveHandler).unbind("mouseup",upHandler);
					newh = me.height();
					setCookie('ied_plugin_edheight', newh, 365);
				};
				jQuery("html").bind("mousemove", moveHandler).bind("mouseup", upHandler);
			})
		);
	});
}

jQuery.fn.selectRange = function(start, end) {
	return this.each(function() {
		if(this.setSelectionRange) {
			this.focus();
			this.setSelectionRange(start, end);
		} else if(this.createTextRange) {
			var range = this.createTextRange();
			range.collapse(true);
			range.moveEnd('character', end);
			range.moveStart('character', start);
			range.select();
		}
	});
};
Array.prototype.unique = function () {
	var r = new Array();
	o:for(var i = 0, n = this.length; i < n; i++) {
		for(var x = 0, y = r.length; x < y; x++) {
			if(r[x]==this[i]) {
				continue o;
			}
		}
		r[r.length] = this[i];
	}
	return r;
}
function ied_goToLine() {
	var line = parseInt(jQuery('#ied_plugin_jumpToLine').val());
	var ied_ed = jQuery('#plugin_editor');
	var ied_edd = document.getElementById('plugin_editor'); // Dunno how to convert a jQuery obj back to DOM
	var lines = ied_ed.val().split('\\n');
	var numchars = 0;
	var count = 0;
	var findstr = '';
	jQuery.each(lines, function() {
		count++;
		if (count >= line) {
			findstr = this;
			return false;
		}
		numchars += (this.length)+2; // Don't ask. +2 is something to do with line endings I think
	});

	// Find the line containing the string we found. Start counting from the line before.
	// Those pesky line endings come into play again so we need to subtract the number
	// of lines found from the start character position *shrug*
	start = ied_ed.val().indexOf(findstr, numchars-count);
	start = (findstr == '') ? start+1 : start;
	end = start+findstr.length;
	ied_ed.selectRange(end-1, end);

	if(document.createEvent) {
		var ied_theCode = ied_ed.val().charCodeAt(end-1);
		if( window.KeyEvent ) {
			var ev = document.createEvent('KeyEvents');
			ev.initKeyEvent('keypress', false, true, window, false, false, false, false, 0, ied_theCode);
		} else {
			var ev = document.createEvent('UIEvents');
			ev.initUIEvent('keypress', false, true, window, 0);
			ev.keyCode = ied_theCode;
		}
		ied_edd.dispatchEvent(ev); // cause scroll to cursor by replacing last char with itself
	}
	ied_ed.selectRange(start, end);
	return false;
}
function ied_plugin_toggle_msgpop(state) {
	var obj = jQuery("#ied_plugin_msgpop");
	if (state != undefined) {
		if (state == 1) {
			obj.show('normal');
		} else {
			obj.hide('normal');
		}
	} else {
		obj.toggle('normal');
	}
}
function ied_plugin_rtrim(str, chars) {
	chars = chars || "\s";
	return str.replace(new RegExp("[" + chars + "]+$", "g"), "");
}
function ied_plugin_update_tp_count() {
	var tp_count = tp_warns = 0;

	jQuery('#ied_plugin_tp_strings ul label').each(function() {
		tp_count++;
		if (jQuery(this).hasClass('warning')) {
			tp_warns++;
		}
	});
	jQuery('#ied_plugin_tp_count').empty().append('('+tp_count+ ' | '+tp_warns+ ' warnings)');

	// Update the global var for use when loading strings
	ied_plugin_tp_total = tp_count;
}

jQuery(function() {
	curh = getCookie('ied_plugin_edheight');
	curh = (curh == null) ? '480' : curh;
	jQuery("#plugin_editor").ied_plugin_resizehandle(parseInt(curh));
	jQuery('textarea[maxlength]').keyup(function(){
		var max = parseInt(jQuery(this).attr('maxlength'));
		if(jQuery(this).val().length > max){
			jQuery(this).val(jQuery(this).val().substr(0, jQuery(this).attr('maxlength')));
		}
		jQuery(this).parent().find('.ied_plugin_charsRemain').html('Chars remaining: '+ (max - jQuery(this).val().length));
	});
	jQuery('textarea[maxlength]').keyup();
	jQuery('#ied_plugin_jumpToLine').keydown(function(e) {
		var code = (e.keyCode ? e.keyCode : e.which);
		if(code == 13) {
			e.preventDefault();
			e.stopPropagation();
			ied_goToLine();
			return false;
		}
	});

	// Store the prefix
	jQuery('#ied_plugin_tp_prefix').blur(function() {
		var pfx = jQuery(this).val();
		sendAsyncEvent(
		{
			event: textpattern.event,
			step: 'ied_plugin_set_tp_prefix',
			plugin: '{$name}',
			prefix: pfx
		});
	});

	// Find all occurrences of gTxt('something')
	jQuery('#plugin_editor, #ied_plugin_tp_prefix').blur(function() {
		var ied_tp_pfx = jQuery('#ied_plugin_tp_prefix').val();
		if (ied_tp_pfx != '') {
			var ied_gtxt_re = /gTxt\([\'\"]([a-zA-Z0-9_]*?)[\"\'][,\)]/gi;
			var ied_tp_ta = jQuery('#plugin_editor').val().replace(/\s*/g,''); // Strip spaces to make lookups easier
			var ied_tp_items = ied_tp_ta.match(ied_gtxt_re);

			// if JS RegExp captured parenthical expressions in global searches or it was easy to inject variables
			// into new RegExp() calls, this loop could be avoided
			var ied_tp_used = [];
			for (var idx = 0; idx < ied_tp_items.length; idx++) {
				var pos = ied_tp_items[idx].lastIndexOf("'");
				pos = (pos == -1) ? ied_tp_items[idx].lastIndexOf('"') : pos;
				tpstr = ied_tp_items[idx].substr(6,pos-6);
				if (tpstr.indexOf(ied_tp_pfx) == 0) {
					ied_tp_used[ied_tp_used.length] = tpstr;
				}
			}

			ied_tp_used = ied_tp_used.unique();

			// List of all current textpack strings in use (as of last Save operation)
			var ied_tp_curr = [];
			jQuery('#ied_plugin_tp_strings label').each(function() {
				ied_tp_curr[ied_tp_curr.length] = jQuery(this).text();
			});

			// Iterate over current array and check if each name is in the used textpack item list.
			// If it is, remove it from the final list.
			for (var idx = 0; idx < ied_tp_curr.length; idx++) {
				if ((pos = jQuery.inArray(ied_tp_curr[idx], ied_tp_used)) > -1) {
					ied_tp_used.splice(pos, 1);
					jQuery('#ied_plugin_tp_strings ul label:contains('+ied_tp_curr[idx]+')').toggleClass('warning', false).next(".ied_plugin_xbtn").remove();
				} else {
					setclass = 1;
					jQuery('#ied_plugin_tp_strings ul label:contains('+ied_tp_curr[idx]+')').toggleClass('warning', true).next(".ied_plugin_xbtn").remove().end().after('<a href="#" class="ied_plugin_xbtn">[x]</a>');
				}
			}
			// For each remaining item that has been used, add an input box
			// TODO: i18n the select options
			for (var idx = 0; idx < ied_tp_used.length; idx++) {
				jQuery('#ied_plugin_tp_strings ul').prepend('<li><input type="text" name="textpack_'+ied_tp_used[idx]+'" value="" /> <select name="ied_plugin_tp_event"><option value="admin">Admin</option><option value="public">Public</option><option value="common">Both</option></select> <label>'+ied_tp_used[idx]+'</label></li>');
			}
			ied_plugin_update_tp_count();
		}
	}).blur();

	// Handle adding new strings manually
	jQuery('#ied_plugin_add_string').click(function(ev) {
		jQuery('#ied_plugin_tp_strings ul').before('<div id="ied_plugin_new_container"><label>'+jQuery('#ied_plugin_tp_prefix').val()+'_<input type="text" name="ied_plugin_tp_newname" id="ied_plugin_tp_newname" value="" /></label></div>');
		jQuery('#ied_plugin_tp_newname').focus();
		ev.preventDefault();
	});
	jQuery(document).on('blur', '#ied_plugin_tp_newname', function() {
		var newname = ied_plugin_rtrim(jQuery('#ied_plugin_tp_prefix').val()+'_'+jQuery('#ied_plugin_tp_newname').val(), '_');
		var newok = true;
		jQuery('#ied_plugin_tp_strings ul li label').each(function() {
			if (jQuery(this).text() == newname) {
				jQuery('#ied_plugin_tp_newname').css('color', '#E00');
				newok = false;
			}
		});
		// TODO: i18n select option text
		if (newok) {
			jQuery('#ied_plugin_tp_strings ul').prepend('<li><input type="text" name="textpack_'+newname+'" value="" /> <select name="ied_plugin_tp_event"><option value="admin">Admin</option><option value="public">Public</option><option value="common">Both</option></select> <label>'+newname+'</label></li>');
			jQuery('#ied_plugin_new_container').remove();
			jQuery('input[name="textpack_'+newname+'"]').focus();
		}
		ied_plugin_update_tp_count();
	});

	// Initialise the generic AJAX error handler
	jQuery('.ied_editForm').ajaxError(function(event, request, settings) {
		var xhr = jQuery(request.responseText);

		// phpdoc generation barfed
		if (settings.data.indexOf('step=ied_plugin_generate_phpdoc') > -1) {
			var msgContent = jQuery("#ied_plugin_msgpop .ied_plugin_msgpop_content");
			status = xhr.find('http-status').attr('value')
			if (status == '200 OK') {
				msgContent.append(xhr.find('ied_plugin_phpdoc').attr('value'));
			} else if (status == '501 Not Implemented') {
				msgContent.append(xhr.find('error_msg').attr('value'));
			}
			ied_plugin_toggle_msgpop('1');
		}

		// code save barfed
		if (settings.data.indexOf('step=ied_plugin_code_save') > -1) {
			var msg = xhr.find('ied_plugin_msg').attr('value');
			var line = xhr.find('ied_plugin_err_line').attr('value');
			jQuery('#ied_plugin_jumpToLine').val(line);
			ied_goToLine();
			var codeobj = jQuery("#plugin_editor");
			codeobj.css({'opacity': '.75'}); // Reduced opacity as a visual cue that something's wrong
			eval(msg); // bleurgh!
		}
	});

	// Handle 'x' button
	jQuery(document).on('click', '.ied_plugin_xbtn', function(event) {
		var elem = jQuery(this).prev('label');
		var tp_lbl = elem.text();

		sendAsyncEvent(
		{
			event: textpattern.event,
			step: 'ied_plugin_textpack_del',
			ied_tp_lbl: tp_lbl
		});

		elem.parent().remove();
		event.preventDefault();
		ied_plugin_update_tp_count();
	});

	// Save textpack string to database
	function ied_plugin_tp_save(event) {
		var elem = jQuery(this);
		var isSel = elem.is('select');
		var tp_lbl = elem.nextAll('label').text();
		var tp_str = (isSel) ? elem.prevAll('input').val() : elem.val();
		var tp_ev = (isSel) ? elem.val() : elem.nextAll('select').val();
		var tp_evt = (tp_ev=='public' || tp_ev=='common') ? tp_ev : jQuery('#ied_plugin_tp_prefix').val();
		var tp_lng = jQuery('#ied_plugin_tp_lang').val();

		sendAsyncEvent(
		{
			event: textpattern.event,
			step: 'ied_plugin_textpack_save',
			ied_tp_evt: tp_evt,
			ied_tp_lbl: tp_lbl,
			ied_tp_lng: tp_lng,
			ied_tp_str: tp_str
		});
	}

	// Handle saving textpack string
	jQuery(document).on('blur', '#ied_plugin_tp_strings ul li input', ied_plugin_tp_save);
	jQuery(document).on('change', '#ied_plugin_tp_strings ul li select', ied_plugin_tp_save);

	// Handle language change
	jQuery("#ied_plugin_tp_lang").change(function(event) {
		jQuery('#ied_plugin_tp_load_count').empty().show();

		var tp_lng = jQuery(this).val();
		var tp_dflt = jQuery('#ied_plugin_tp_lang_dflt').val();
		var sel = '#ied_plugin_tp_strings ul li';
		var numStrings = sel.length;
		var numFetched = 0;

		jQuery(sel).each(function() {
			var obj = jQuery(this);
			var tp_lbl = obj.find('label').text();
			var tp_dest = obj.find('input');

			sendAsyncEvent(
			{
				event: textpattern.event,
				step: 'ied_plugin_textpack_get',
				ied_tp_lbl: tp_lbl,
				ied_tp_lng: tp_lng,
				ied_tp_dflt: tp_dflt
			}, function(data) {
				numFetched++;
				var theVal = data.ied_plugin_tp_string;
				var xl8str = data.ied_plugin_tp_dflt;

				tp_dest.val(theVal);

				if (xl8str == undefined || xl8str == '') {
					obj.removeAttr('title');
				} else {
					obj.attr('title', xl8str);
				}
				if (numFetched < ied_plugin_tp_total) {
					jQuery('#ied_plugin_tp_load_count').text(numFetched + '/' + ied_plugin_tp_total);
				} else {
					jQuery('#ied_plugin_tp_load_count').text('OK').hide('slow');
				}
			},
			'json');
		});
	});

	// Current language refresh
	jQuery('#ied_plugin_tp_refresh').click(function() {
		// Trigger the change event
		jQuery("#ied_plugin_tp_lang").change();
	});

	// Load textpack strings from plugin's custom gTxt()
	jQuery("#ied_plugin_tp_load").click(function(event) {
		var ied_fn = jQuery("#ied_plugin_tp_populate").val();

		jQuery('#ied_plugin_tp_strings ul li').each(function() {
			var obj = jQuery(this);
			var tp_lbl = obj.find('label').text();
			var tp_dest = obj.find('input');

			sendAsyncEvent(
			{
				event: textpattern.event,
				step: 'ied_plugin_textpack_load',
				ied_tp_fn: ied_fn,
				ied_tp_lbl: tp_lbl
			}, function(data) {
				// Paste the returned string into the input box and save it by invoking blur()
				tp_dest.val(data.ied_plugin_tp_string).blur();
			},
			'json');
		});
		event.preventDefault();
	});

	// Handle saving code
	jQuery("#ied_plugin_code_save").click(function(event) {
		var msgarea = jQuery("#ied_plugin_messages");
		msgarea.empty();
		var codeobj = jQuery("#plugin_editor");
		var codeblock = codeobj.val();
		var plugin = '{$name}';

		codeobj.css('opacity', '0.4');
		sendAsyncEvent(
		{
			event: textpattern.event,
			step: 'ied_plugin_code_save',
			plugin: plugin,
			codeblock: codeblock
		}, function(data) {
			codeobj.css({'opacity': '1'});
			var msg = jQuery(data).find('ied_plugin_msg').attr('value');
			eval(msg); // yuk!
		});

		event.preventDefault();
	});

	// Handle generating phpdoc
	jQuery("#ied_plugin_btn_phpdoc").click(function(event) {
		var msgarea = jQuery("#ied_plugin_msgpop");
		var msgContent = jQuery("#ied_plugin_msgpop .ied_plugin_msgpop_content");
		msgContent.empty();
		var fnobj = jQuery("#ied_plugin_to_phpdoc");
		var fn = fnobj.val();
		var plugin = '{$name}';

		sendAsyncEvent(
		{
			event: textpattern.event,
			step: 'ied_plugin_generate_phpdoc',
			plugin: plugin,
			fn: fn
		}, function(data) {
			msgContent.append(jQuery(data).find('ied_plugin_phpdoc').attr('value'));
			ied_plugin_toggle_msgpop('1');
		});

		event.preventDefault();
	});

	jQuery('#ied_plugin_tp_oplangs').change(function() {
		sel = jQuery('#ied_plugin_tp_oplangs option:selected').map(function(){ return this.value }).get().join(', ');
		sendAsyncEvent(
		{
			event: textpattern.event,
			step: 'ied_plugin_lang_set',
			ied_tp_langsel: sel
		});
	});

});
EOF
		);
}

// -------------------------------------------------------------
function ied_plugin_save() {
	global $ied_plugin_globals, $prefs;
	extract(doSlash(gpsa(array('name','newname','filename','code','author','author_uri','version','description','help','css','status','type','load_order','rename_file','restore_point','ied_plugin_tp_prefix'))));
	$flags = gps('flags');

	list ($start_css, $end_css) = ied_plugin_make_markers("CSS", $ied_plugin_globals['css_start'], $ied_plugin_globals['css_end']);
	$extraMsg = $newfilename = $msg1 = $msg2 = '';
	$newname = trim($newname);
	if ($flags) {
		$flagout = 0;
		foreach ($flags as $flag) {
			$flagout |= $flag;
		}
		$flags = $flagout;
	}

	if (empty($newname)) {
		$msg1 = gTxt('ied_plugin_name_first');
		$msgType = E_ERROR;
	} else {
		if (empty($filename)) {
			$hout = array();
			$hout[0] = $help;
			if ($css) { $hout[1] = n.$start_css.n.$css.n.$end_css; }
			safe_update(
				'txp_plugin',
				"name='".$newname."',
				status = ".intval($status).",
				type = ".intval($type).",
				author = '".$author."',
				author_uri = '".$author_uri."',
				version = '".$version."',
				description = '".$description."',
				help = '".join('',$hout)."',
				code = '".$code."',
				flags = ".intval($flags).",
				".(($restore_point == 1)? "code_restore = '".$code."'," : '')."
				load_order = ".$load_order,
				"name = '".$name."'");
			$msg1 = gTxt('plugin_saved', array('{name}' => $newname));
			$msgType = '';
		} else {
			$dir = $prefs['plugin_cache_dir'].DS;
			if (file_exists($dir.$filename)) {
				$filecontent = file($dir.$filename);
			} else {
				if (empty($prefs['plugin_cache_dir'])) {
					$filecontent = '';
					$msg2 = gTxt('ied_plugin_cache_not_set');
				} else {
					$oporder = (isset($prefs['ied_plugin_output_order']) && is_numeric($prefs['ied_plugin_output_order'])) ? $prefs['ied_plugin_output_order'] : 0;
					$helpchunk = ied_plugin_build_template('help', array($help, ' ')); // Use a space to force a CSS hunk in the file
					$codechunk = ied_plugin_build_template('code', $code);
					$filecontent = ied_plugin_build_template("preamble")
						.ied_plugin_build_template("name", $newname)
						.ied_plugin_build_template("html_help")
						.ied_plugin_build_template("version", $version)
						.ied_plugin_build_template("author", $author)
						.ied_plugin_build_template("author_uri", $author_uri)
						.ied_plugin_build_template("description", $description)
						.ied_plugin_build_template("load_order", $load_order)
						.ied_plugin_build_template("type", $type)
						.ied_plugin_build_template("flags", $flags)
						.ied_plugin_build_template("include")
						.(($oporder == 0) ? $codechunk : $helpchunk)
						.(($oporder == 1) ? $codechunk : $helpchunk)
						.ied_plugin_build_template("postamble");
					$msg1 = gTxt('ied_plugin_edit_new');
					$msgType = '';
				}
			}

			$filecontent = ied_plugin_make_array($filecontent);
			$metavars = array("name" => "$newname",
									"version" => "$version",
									"author" => "$author",
									"author_uri" => "$author_uri",
									"description" => "$description",
									"type" => "$type",
									"flags" => "$flags",
									"order" => "$load_order",
							);
			$hunkvars = array("CODE" => doStrip(str_replace('\r\n','
',$code)), // newline workaround
						"HELP" => doStrip(str_replace('\r\n','
',$help)),
						"CSS" => doStrip(str_replace('\r\n','
',$css)),
							);
			foreach ($metavars as $varname => $value) {
				for($idx = 0; $idx < count($filecontent); $idx++) {
					if (strpos($filecontent[$idx], '$plugin[\''.$varname.'\']') === 0) {
						$filecontent[$idx] = '$plugin[\''.$varname.'\'] = '.doQuote($value).';';
						break;
					}
				}
			}
			foreach ($hunkvars as $varname => $hunk) {
				list ($start_delim, $end_delim) = ied_plugin_make_markers($varname, $ied_plugin_globals['dlm_start'], $ied_plugin_globals['dlm_end']);
				$start = array_search($start_delim, $filecontent) + 1;
				$end = array_search($end_delim, $filecontent);
				if (is_numeric($start) && is_numeric($end) && $end >= $start) {
					array_splice($filecontent, $start, $end-$start, $hunk);
				}
			}
			$filecontent = implode("\n", $filecontent);

			if ($filecontent) {
				$fh = fopen($dir.$filename, 'w+');
				fwrite($fh, $filecontent);
				fclose($fh);
			}
			$msg1 = (empty($msg1)) ? gTxt('plugin_saved', array('{name}' => $newname)) : $msg1;
			$msgType = '';

			// Make new file if required
			if ($rename_file == 1) {
				$fnames = ied_plugin_get_name($newname, $version);
				$newfilename = $fnames[2];
				$res = rename($dir.$filename, $dir.$newfilename);
				$extraMsg = ($res) ? gTxt('ied_plugin_renamed') : gTxt('ied_plugin_rename_failed');
			}
		}

		// Store the plugin textpack prefix
		ied_plugin_set_tp_prefix($newname, $ied_plugin_tp_prefix);
	}
	if ($msg2) {
		ied_plugin_table($msg2);
	} else {
		// Check the plugin type matches the code used
		$extraMsg .= (ied_plugin_admin_check($code, $type)) ? '' : strong(gTxt('ied_plugin_check_type'));
		$message = $msg1.$extraMsg;
		ied_plugin_edit(array($message, $msgType), $newfilename);
	}
}

// -------------------------------------------------------------
function ied_plugin_save_as_file() {
	global $prefs, $ied_plugin_globals;
	if (gps('name')) {
		$name = gps('name');
		$rs = safe_row('description, author, author_uri, version, code, help, type, load_order, flags', 'txp_plugin', "name='".doSlash($name)."'");
		extract($rs);

		list($css,$help) = ($help) ? ied_plugin_extract_hunk($help, "CSS", "<!--|-->", true) : array('',$help);
	} elseif (gps('filename')) {
		$plugin=ied_plugin_read_file($prefs['plugin_cache_dir'].DS.gps('filename'));
		extract($plugin);
	}

	$zip = gps('type');
	if (gps('trim')==1) {
		$code=explode("\r\n",$code);
		$code=array_map('trim',$code);
		$code=implode("\r\n",$code);
	}

	// Get any textpack strings
	$textpack = ied_plugin_textpack_build($name);

	list ($start_css, $end_css) = ied_plugin_make_markers("CSS", $ied_plugin_globals['css_start'], $ied_plugin_globals['css_end']);
	$fnames = ied_plugin_get_name($name, $version);

	header('Content-type: text/plain');
	header('Content-Disposition: attachment; filename=' . (($zip === 'zip') ? $fnames[1] : $fnames[0]));

	$types = array('Public' , 'Admin/Public' , 'Library' , 'Admin', 'Admin', 'Admin/Public'); // No gTxt() because the template is English
	$plugin['name'] = $name;
	$plugin['author'] = $author;
	$plugin['author_uri'] = $author_uri;
	$plugin['version'] = $version;
	$plugin['description'] = $description;
	$plugin['code'] = $code;
	$plugin['type'] = $type;
	$plugin['order'] = $load_order;
	$plugin['flags'] = $flags;
//	$plugin['allow_html_help'] = true;
//	$plugin['help_raw'] = $help.$start_css.$css.$end_css;
	$plugin['help'] = ied_plugin_textile($name, $help, $css, $start_css, $end_css);
	$plugin['md5'] = md5( $plugin['code'] );
	if ($textpack) {
		$plugin['textpack'] = $textpack;
	}

	echo '# Name: '.$name.' v'.$version.' '.(($zip === 'zip') ? "(compressed)" : "").'
# Type: '.$types[$type].' plugin
# '.$description.'
# Author: '.$author.'
# URL: '.$author_uri.'
# Recommended load order: '.$load_order.'

# .....................................................................
# This is a plugin for Textpattern CMS - http://textpattern.com/
# To install: textpattern > admin > plugins
# Paste the following text into the \'Install plugin\' box:
# .....................................................................

'.(($zip === 'zip') ? chunk_split(base64_encode(gzencode(serialize($plugin))), 72) : chunk_split(base64_encode(serialize($plugin)), 72));

	die();
}

// -------------------------------------------------------------
function ied_plugin_save_as_php_file() {
	global $prefs;
	if (gps('name')) {
		$name = gps('name');
		$rs = safe_row("description, author, author_uri, version, code, help, type, load_order, flags", "txp_plugin", "name='".doSlash($name)."'");
		extract($rs);

		list($css,$help) = ($help) ? ied_plugin_extract_hunk($help, "CSS", "<!--|-->", true) : array('',$help);
	} elseif (gps('filename')) {
		$plugin=ied_plugin_read_file($prefs['plugin_cache_dir'].DS.gps('filename'));
		extract($plugin);
	}

	$oporder = (isset($prefs['ied_plugin_output_order']) && is_numeric($prefs['ied_plugin_output_order'])) ? $prefs['ied_plugin_output_order'] : 0;
	$fnames = ied_plugin_get_name($name, $version);

	header('Content-type: text/php');
	header('Content-Disposition: attachment; filename=' .$fnames[2]);

	$textpack = ied_plugin_textpack_build($name);
	$helpchunk = ied_plugin_build_template('help', array($help, $css));
	$codechunk = ied_plugin_build_template('code', str_replace("\r\n","\n",$code));

	echo ied_plugin_build_template('preamble').
		ied_plugin_build_template('name', $name).
		ied_plugin_build_template('html_help').
		ied_plugin_build_template('version', $version).
		ied_plugin_build_template('author', $author).
		ied_plugin_build_template('author_uri', $author_uri).
		ied_plugin_build_template('description', $description).
		ied_plugin_build_template('load_order', $load_order).
		ied_plugin_build_template('type', $type).
		ied_plugin_build_template('flags', $flags).
		ied_plugin_build_template('textpack', $textpack).
		ied_plugin_build_template('include').
		(($oporder == 0) ? $codechunk : $helpchunk).
		(($oporder == 1) ? $codechunk : $helpchunk).
		ied_plugin_build_template('postamble');

	die();
}

// -------------------------------------------------------------
function ied_plugin_save_as_textpack() {
	global $prefs;

	if (gps('name')) {
		$name = gps('name');
		$version = safe_field('version', "txp_plugin", "name='".doSlash($name)."'");
	} elseif (gps('filename')) {
		$name = gps('filename');
		$plugin=ied_plugin_read_file($prefs['plugin_cache_dir'].DS.gps('filename'));
		$version = $plugin['version'];
	}

	$langs = gps('lang');
	if (!$langs) {
		$langs = get_pref('ied_plugin_lang_selected', '');
	}

	$force = 0;
	if ($langs == '') {
		$langstr = 'all';
		$force = 1;
	} else {
		$langlist = do_list($langs);
		if (count($langlist) == 1) {
			$langstr = $langlist[0];
		} else {
			$country_codes = array();
			foreach($langlist as $ln) {
				$lparts = do_list($ln, '-');
				$country_codes[] = $lparts[0];
			}
			$langstr = join('+', array_unique($country_codes));
		}
	}
	$textpack = ied_plugin_textpack_build($name, $force);
	$fnames = ied_plugin_get_name($name, $version, $langstr);

	header('Content-type: text/html; charset=UTF-8');
	header('Content-Disposition: attachment; filename=' . $fnames[3]);
	echo $textpack;
	die();
}

// -------------------------------------------------------------
function ied_plugin_build_template($prt, $val='') {
	$css = $help = '';
	if (is_array($val)) {
		$help = $val[0];
		$css = isset($val[1]) ? $val[1] : '';
		$val = '';
	}
	$template = array(
		"preamble" => '<?php'.n.n
						.'// This is a PLUGIN TEMPLATE for Textpattern CMS.'.n.n
						.'// Copy this file to a new name like abc_myplugin.php.  Edit the code, then'.n
						.'// run this file at the command line to produce a plugin for distribution:'.n
						.'// $ php abc_myplugin.php > abc_myplugin-0.1.txt'.n.n,
		"name" => '// Plugin name is optional.  If unset, it will be extracted from the current'.n
						.'// file name. Plugin names should start with a three letter prefix which is'.n
						.'// unique and reserved for each plugin author ("abc" is just an example).'.n
						.'// Uncomment and edit this line to override:'.n
						.'$plugin[\'name\'] = '.doQuote(doSlash($val)).';'.n.n,
		"html_help" => '// Allow raw HTML help, as opposed to Textile.'.n
						.'// 0 = Plugin help is in Textile format, no raw HTML allowed (default).'.n
						.'// 1 = Plugin help is in raw HTML.  Not recommended.'.n
						.'# $plugin[\'allow_html_help\'] = 1;'.n.n,
		"version" => '$plugin[\'version\'] = '.doQuote($val).';'.n,
		"flags" => '// Plugin "flags" signal the presence of optional capabilities to the core plugin loader.'.n
						.'// Use an appropriately OR-ed combination of these flags.'.n
						.'// The four high-order bits 0xf000 are available for this plugin\'s private use'.n
						.'if (!defined(\'PLUGIN_HAS_PREFS\')) define(\'PLUGIN_HAS_PREFS\', 0x0001); // This plugin wants to receive "plugin_prefs.{$plugin[\'name\']}" events'.n
						.'if (!defined(\'PLUGIN_LIFECYCLE_NOTIFY\')) define(\'PLUGIN_LIFECYCLE_NOTIFY\', 0x0002); // This plugin wants to receive "plugin_lifecycle.{$plugin[\'name\']}" events'.n.n
						.'$plugin[\'flags\'] = '.doQuote($val).';'.n.n,
		"textpack" => '// Plugin \'textpack\' is optional. It provides i18n strings to be used in conjunction with gTxt().'.n
						.'// Syntax:'.n
						.'// ## arbitrary comment'.n
						.'// #@event'.n
						.'// #@language ISO-LANGUAGE-CODE'.n
						.'// abc_string_name => Localized String'.n.n
						.(($val)
							? '$plugin[\'textpack\'] = <<<EOT'.n
								.$val.n
								.'EOT;'.n.n
							: '/** Uncomment me, if you need a textpack'.n
								.'$plugin[\'textpack\'] = <<< EOT'.n
								.'#@admin'.n
								.'#@language en-gb'.n
								.'abc_sample_string => Sample String'.n
								.'abc_one_more => One more'.n
								.'#@language de-de'.n
								.'abc_sample_string => Beispieltext'.n
								.'abc_one_more => Noch einer'.n
								.'EOT;'.n
								.'**/'.n
								.'// End of textpack'.n.n),
		"author" => '$plugin[\'author\'] = '.doQuote(doSlash($val)).';'.n,
		"author_uri" => '$plugin[\'author_uri\'] = '.doQuote(doSlash($val)).';'.n,
		"description" => '$plugin[\'description\'] = '.doQuote(doSlash($val)).';'.n.n,
		"load_order" => '// Plugin load order:'.n
						.'// The default value of 5 would fit most plugins, while for instance comment'.n
						.'// spam evaluators or URL redirectors would probably want to run earlier'.n
						.'// (1...4) to prepare the environment for everything else that follows.'.n
						.'// Values 6...9 should be considered for plugins which would work late.'.n
						.'// This order is user-overrideable.'.n
						.'$plugin[\'order\'] = '.doQuote($val).';'.n.n,
		"type" => '// Plugin \'type\' defines where the plugin is loaded'.n
						.'// 0 = public              : only on the public side of the website (default)'.n
						.'// 1 = public+admin        : on both the public and admin side'.n
						.'// 2 = library             : only when include_plugin() or require_plugin() is called'.n
						.'// 3 = admin               : only on the admin side (no AJAX)'.n
						.'// 4 = admin+ajax          : only on the admin side (AJAX supported)'.n
						.'// 5 = public+admin+ajax   : on both the public and admin side (AJAX supported)'.n
						.'$plugin[\'type\'] = '.doQuote($val).';'.n.n,
		"include" => 'if (!defined(\'txpinterface\'))'.n
						.'        @include_once(\'zem_tpl.php\');'.n.n,
		"help" => 'if (0) {'.n
						.'?>'.n
						. (($css) ? '<!--'.n
						.'# --- BEGIN PLUGIN CSS ---'.n
						.$css.n
						.'# --- END PLUGIN CSS ---'.n
						.'-->'.n : '')
						.'<!--'.n
						.'# --- BEGIN PLUGIN HELP ---'.n
						.$help.n
						.'# --- END PLUGIN HELP ---'.n
						.'-->'.n
						.'<?php'.n
						.'}'.n,
		"code" => '# --- BEGIN PLUGIN CODE ---'.n
						.$val.n
						.'# --- END PLUGIN CODE ---'.n,
		"postamble" => '?>',
	);

	return (array_key_exists($prt, $template) ? $template[$prt] : '');
}

// -------------------------------------------------------------
// Create / upload / install a new plugin
function ied_plugin_create() {
	global $ied_plugin_globals;

	extract(doSlash(gpsa(array('name', 'plugin_create', 'plugin_upload', 'plugin_install', 'ied_plugin_autoenable'))));

	if ($plugin_create) {
		$fname = '';
		if ($name == '') {
			ied_plugin_table(array(gTxt('ied_plugin_name_first'), E_ERROR));
			return;
		}
		if (strpos($name, ".php") !== false) {
			$fname = $name;
			$name = basename($name, ".php");
		}
		$exists = fetch('name', 'txp_plugin', 'name', $name);

		// MySQL defaults to set status=1 so we need to explicitly override this if this
		// is a fresh installation
		$state = ($ied_plugin_autoenable==1) ? 1 : 0;

		if (!$exists) {
			if ($fname) {
				// Put plugin in cache dir by faking a POST submission and saving
				$_POST['filename'] = $fname;
				$_POST['newname'] = $name;
				$_POST['status'] = 1; // Cache_dir plugins are always on so this setting is ignored
				$_POST['type'] = 0;
				$_POST['version'] = '0.1';
				$_POST['load_order'] = 5;
				$_POST['flags'] = 0;
				ied_plugin_save();
			} else {
				safe_insert("txp_plugin", "
					name='$name',
					status=$state,
					type=0,
					version='0.1',
					load_order=5,
					flags=0,
					description='',
					help='',
					code='',
					code_restore=''
				");
				ied_plugin_edit(gTxt('ied_plugin_edit_new'), $name);
			}
		} else {
			ied_plugin_table(array(gTxt('plugin').' <strong>'.$name.'</strong> '.gTxt('already_exists'), E_ERROR));
		}

	} else if ($plugin_upload) {
		list ($start_css, $end_css) = ied_plugin_make_markers("CSS", $ied_plugin_globals['css_start'], $ied_plugin_globals['css_end']);

		if (!$_FILES['thefile']['tmp_name']) {
			ied_plugin_table(array(gTxt('ied_plugin_choose_file'), E_ERROR));
			return;
		}

		$info = explode ('.',$_FILES['thefile']['name']);
		$lastpart = count($info)-1;
		$ext = $info[$lastpart];

		if ($ext == 'php') {
			$plugin = ied_plugin_read_file($_FILES['thefile']['tmp_name']);
//	$newname = (empty($plugin['name'])) ? basename($_FILES['thefile']['name'], '.php') : $plugin['name'];
			$newname = ($name) ? $name : doSlash($plugin['name']);

			if (empty($plugin['code'])) {
				$plugin['code'] = file_get_contents($_FILES['thefile']['tmp_name']);
			}

			extract(doSlash($plugin));

			$md5 = md5($code);
			$version = ($version) ? $version : "0.1";
			$help = $help.(($css) ? n.$start_css.n.$css.n.$end_css : '');
			$exists = fetch('name', 'txp_plugin', 'name', $newname);

			// MySQL defaults to set status=1 so we need to explicitly override this if this
			// is a fresh installation
			$state = ($ied_plugin_autoenable==1) ? 'status=1,' : ($exists && ($ied_plugin_autoenable==2) ? '' : 'status=0,');

			if ($exists) {
				// Note: status omitted so it retains its value
				$rs = safe_update(
					"txp_plugin",
					"$state
					type = ".intval($type).",
					author = '$author',
					author_uri = '$author_uri',
					version = '$version',
					description = '$description',
					help = '$help',
					code = '$code',
					code_restore = '$code',
					code_md5 = '$md5',
					flags = ".intval($flags).",
					load_order = ".intval($load_order),
					"name = '$newname'"
				);
			} else {
				$rs = safe_insert(
					"txp_plugin",
					"name = '$newname',
					$state
					type = ".intval($type).",
					author = '$author',
					author_uri = '$author_uri',
					version = '$version',
					description = '$description',
					help = '$help',
					code = '$code',
					code_restore = '$code',
					code_md5 = '$md5',
					flags = ".intval($flags).",
					load_order = ".intval($load_order)
				);
			}

			$msg = ($exists) ? gTxt('ied_plugin_updated', array('{name}' => $newname)) : gTxt('ied_plugin_uploaded', array('{name}' => $newname));
			ied_plugin_table($msg, $newname);

		} else if ($ext = 'txt') {
			$plugin64 = file_get_contents($_FILES['thefile']['tmp_name']);
			$ret = ied_plugin_install($plugin64);

			if ($ret['err'] == '') {
				ied_plugin_table($ret['msg'], $ret['nam']);
	      } else {
				ied_plugin_table(array($ret['msg'], $ret['err']));
			}
		}

	} else if ($plugin_install) {
		$ret = ied_plugin_install(ps('plugin64'));
		if ($ret['err'] == '') {
			ied_plugin_table($ret['msg'], $ret['nam']);
      } else {
			ied_plugin_table(array($ret['msg'], $ret['err']));
		}
	}
}

// -------------------------------------------------------------
// Frankensteined shamelessly from txp_plugin.php
function ied_plugin_install($plugin='') {
	if (strpos($plugin, '$plugin=\'') !== false) {
		@ini_set('pcre.backtrack_limit', '1000000');
		$plugin = preg_replace('@.*\$plugin=\'([\w=+/]+)\'.*@s', '$1', $plugin);
	}

	$plugin = preg_replace('/^#.*$/m', '', $plugin);

	if(trim($plugin)) {

		$plugin = base64_decode($plugin);
		if (strncmp($plugin,"\x1F\x8B",2)===0)
			$plugin = gzinflate(substr($plugin, 10));

		if ($plugin = @unserialize($plugin)) {

			if(is_array($plugin)){

				extract($plugin);

				$type  = empty($type)  ? 0 : min(max(intval($type), 0), 5);
				$order = empty($order) ? 5 : min(max(intval($order), 1), 9);
				$flags = empty($flags) ? 0 : intval($flags);

				$exists = fetch('name', 'txp_plugin', 'name', $name);

				if (isset($help_raw) && empty($plugin['allow_html_help'])) {
						// default: help is in Textile format
						include_once txpath.'/lib/classTextile.php';
						$textile = new Textile();
						$help = $textile->TextileRestricted($help_raw, 0, 0);
				}

				// MySQL defaults to set status=1 so we need to explicitly override this if this
				// is a fresh installation
				$ied_plugin_autoenable = ps('ied_plugin_autoenable');
				$state = ($ied_plugin_autoenable==1) ? 'status=1,' : ($exists && ($ied_plugin_autoenable==2) ? '' : 'status=0,');

				if ($exists) {
					$rs = safe_update(
					   "txp_plugin",
						"$state
						type         = $type,
						author       = '".doSlash($author)."',
						author_uri   = '".doSlash($author_uri)."',
						version      = '".doSlash($version)."',
						description  = '".doSlash($description)."',
						help         = '".doSlash($help)."',
						code         = '".doSlash($code)."',
						code_restore = '".doSlash($code)."',
						code_md5     = '".doSlash($md5)."',
						flags     	 = $flags",
						"name        = '".doSlash($name)."'"
					);

				} else {

					$rs = safe_insert(
					   "txp_plugin",
					   "name         = '".doSlash($name)."',
						$state
						type         = $type,
						author       = '".doSlash($author)."',
						author_uri   = '".doSlash($author_uri)."',
						version      = '".doSlash($version)."',
						description  = '".doSlash($description)."',
						help         = '".doSlash($help)."',
						code         = '".doSlash($code)."',
						code_restore = '".doSlash($code)."',
						code_md5     = '".doSlash($md5)."',
						load_order   = '".$order."',
						flags   	 = $flags"
					);
				}

				if ($rs and $code)
				{
					if (!empty($textpack))
					{
						install_textpack($textpack, false);
					}

					if (ps('ied_plugin_installopts') && ($flags & PLUGIN_LIFECYCLE_NOTIFY) )
					{
						load_plugin($name, true);
						$message = callback_event("plugin_lifecycle.$name", 'installed');
					}

					if (empty($message)) $message = gTxt('plugin_installed', array('{name}' => $name));
					return array('msg' => $message, 'err' => '', 'nam' => $name);
				}

				else
				{
					return array('msg' => gTxt('plugin_install_failed', array('{name}' => $name)), 'err' => E_ERROR);
				}
			}
		}
	}
	return array('msg' => gTxt('bad_plugin_code'), 'err' => E_ERROR);
}

// -------------------------------------------------------------
function ied_plugin_save_pane_state() {
	$panes = array('ied_plugin_tp_strings', 'ied_plugin_code', 'ied_plugin_docs', 'ied_plugin_meta', 'ied_plugin_utils', 'ied_plugin_cpanel', 'ied_plugin_dbplugs', 'ied_plugin_cacheplugs');
	$pane = gps('pane');
	if (in_array($pane, $panes))
	{
		set_pref("pane_{$pane}_visible", (gps('visible') == 'true' ? '1' : '0'), 'ied_plugin', PREF_HIDDEN, 'yesnoradio', 0, PREF_PRIVATE);
		send_xml_response();
	} else {
		send_xml_response(array('http-status' => '400 Bad Request'));
	}
}

// -------------------------------------------------------------
// Reurns an array of filenames;
//  1) the standard plugin
//  2) the compressed plugin
//  3) the PHP template
//  4) the textpack
function ied_plugin_get_name($name, $version, $lang='') {
	$ied_plugin_prefs = ied_pc_get_prefs();

	$op = get_pref('ied_plugin_output_sfile');
	$opc = get_pref('ied_plugin_output_sfilec');
	$opp = get_pref('ied_plugin_output_sfilep');
	$opt = get_pref('ied_plugin_output_sfilet');

	$out = array(
		( (empty($op)) ? $ied_plugin_prefs['ied_plugin_output_sfile']['default'] : $op ),
		( (empty($opc)) ? $ied_plugin_prefs['ied_plugin_output_sfilec']['default'] : $opc ),
		( (empty($opp)) ? $ied_plugin_prefs['ied_plugin_output_sfilep']['default'] : $opp ),
		( (empty($opt)) ? $ied_plugin_prefs['ied_plugin_output_sfilet']['default'] : $opt ),
	);

	$from = array('{name}', '{version}', '{lang}');
	$to = array($name, $version, $lang);

	foreach ($out as $fidx => $fname) {
		$fname = str_replace($from, $to, $fname);
		$out[$fidx] = sanitizeForFile($fname);
	}

	return $out;
}

// -------------------------------------------------------------
// Parse a plugin in Standard Textpattern Template format
function ied_plugin_read_file($filepath) {
	$content = file($filepath);
	$justfile = basename($filepath);
	$parts = explode ('.',$justfile);
	$ext = array_pop($parts);
	$ext = ($ext==$justfile) ? '' : '.'.$ext; // Only assign an extension if one exists
	$source_lines = count($content);
	$commentblock = false;
	$in_textpack = false;
	$metadata = array(
						'name'        => '',
						'version'     => '',
						'revision'    => '',
						'author'      => '',
						'author_uri'  => '',
						'description' => '',
						'order'       => '5',
						'type'        => '',
						'flags'       => '',
						'textpack'    => '',
					);
	for ($idx=0; $idx < $source_lines; $idx++) {
		$content[$idx] = rtrim($content[$idx]);

		// Bomb out if we reach the end of the definition area
		if (strpos( $content[$idx], 'if (!defined(\'txpinterface\')' ) === 0) {
			break;
		}
		if (strpos($content[$idx], '/*') === 0) {
			$commentblock = true;
			continue;
		}
		if ( (strpos($content[$idx], '*/') === 0) || (strpos($content[$idx], '**/') === 0) ) {
			$commentblock = false;
			continue;
		}

		if (strpos($content[$idx], 'EOT;') === 0) {
			$in_textpack = false;
			continue;
		}

		if ($in_textpack) {
			$metadata['textpack'] .= $content[$idx].n;
		}

		if (!$commentblock && strpos($content[$idx], '$plugin[') === 0) {
			// Found a plugin variable so extract it
			$parts = explode(" = ", $content[$idx]);
			$parts[0] = str_replace("'", "", $parts[0]); // Make the match easier!
			$parts[1] = str_replace(";", "", $parts[1]); // Ditto
			preg_match("/plugin\[(.*)\]/", $parts[0], $var); // Extract just the variable name
			if (is_numeric($parts[1])) {
				$parts[1] = "'".$parts[1]."'";
			}
			preg_match("/.*'(.*)'.*/", $parts[1], $val); // Remove anything outside the quotes (e.g. $revision)

			if ($var[1] == 'revision' && isset($val[1]) && !empty($val[1])) {
				$revparts = explode(' ', trim($val[1], '$ '));
				$val[1] = $revparts[count($revparts)-1];
				$val[1] = (empty($val[1])) ? '' : '.' .$val[1];
			}
			if ($var[1] == 'flags' && !isset($val[1])) {
				// Unquoted value; possibly constants
				$val[1] = 0;
				$constants = do_list($parts[1], '|');
				foreach ($constants as $constant) {
					$val[1] |= (defined($constant)) ? constant($constant) : 0;
				}
			}
			if ($var[1] == 'textpack') {
				$in_textpack = true;
				continue;
			}
			$metadata[$var[1]] = $val[1];
		}
	}

	$metadata['name'] = (empty($metadata['name'])) ? basename($justfile, $ext) : $metadata['name'];
	$metadata['load_order'] = $metadata['order'];
	$metadata['version'] .= $metadata['revision'];
	$metadata['help'] = ied_plugin_extract_hunk($content, 'HELP');
	$metadata['css']  = ied_plugin_extract_hunk($content, 'CSS' );
	$metadata['code'] = ied_plugin_extract_hunk($content, 'CODE');

	return $metadata;
}

// -------------------------------------------------------------
// ripped and modded from net-carver's zem_tpl template
function ied_plugin_extract_hunk($content, $hunk, $cmnt="#", $delete=false) {
	$dlm = explode("|", $cmnt);
	$dlmStart = $dlm[0];
	$dlmEnd = (count($dlm) > 1) ? $dlm[1] : '';
	$lines = ied_plugin_make_array($content);

	list ($start_delim, $end_delim) = ied_plugin_make_markers($hunk, $dlmStart, $dlmEnd);
	$start = array_search($start_delim, $lines) + 1;
	$end = array_search($end_delim, $lines);

	// Kludge to get round the delimiter change in v0.83
	if ($hunk == 'CSS' && $end === false) {
		$start_delim = str_replace('---', '***', $start_delim);
		$end_delim = str_replace('---', '***', $end_delim);
		$start = array_search($start_delim, $lines) + 1;
		$end = array_search($end_delim, $lines);
	}

	$extracted = array();
	if (count($lines) > 0 && is_numeric($start) && is_numeric($end) && $end > $start) {
		$extracted = array_slice($lines, $start, $end-$start);
		$lineNum = count($extracted)-1;
		while ($lineNum > 0 && trim($extracted[$lineNum--]) == "") {
			array_pop($extracted);
		}
		while (count($extracted) > 0 && trim($extracted[0]) == "") {
			array_shift($extracted);
		}
		if ($delete) {
			$chopped = array_splice($lines, $start-1, $end-$start+2, "");
		}
	}
	$remains = $lines;
	// Check if the CSS section has <style> tags around it; add them if not
	if ($hunk == "CSS") {
		$numrows = count($extracted);
		if ($numrows > 1) {
			if (strpos($extracted[$numrows-1], '</style>') === false) {
				$extracted[] = '</style>';
			}
			if (strpos($extracted[0], '<style ') === false) {
				array_unshift($extracted, '<style type="text/css">');
			}
		}
	}
	if ($delete) {
		return array(trim(join("\n", $extracted)), trim(join("\n", $remains)));
	} else {
		return trim(join("\n", $extracted));
	}
}

// -------------------------------------------------------------
function ied_plugin_make_array($arr) {
	if (!is_array($arr)) {
		$arr = explode("\n", $arr);
	}
	$source_lines = count($arr);
	for ($idx=0; $idx < $source_lines; $idx++) {
		$arr[$idx] = rtrim($arr[$idx]);
	}
	return $arr;
}

// -------------------------------------------------------------
function ied_plugin_make_markers($hunk, $start, $end) {
	global $ied_plugin_globals;
	$smarker = $start . str_replace("SECTION", $hunk, $ied_plugin_globals['start']) . (($end)?' '.$end:'');
	$emarker = $start . str_replace("SECTION", $hunk, $ied_plugin_globals['end']) . (($end)?' '.$end:'');
	return (array($smarker, $emarker));
}

// -------------------------------------------------------------
// ripped and modded from net-carver's zem_tpl template
function ied_plugin_admin_check($codeblock, $type) {
	// Short circuit since we're only interested in client plugins
	if ($type != '0' || strpos($codeblock, 'txpinterface')===false) {
		return true;
	}

	// Believe it or not this is several orders of magnitude quicker than a single preg_match
	$cb = str_replace(array('\t', ' '), '', $codeblock);
	$cb = str_replace('===', '==', $cb);
	if (strpos($cb, 'txpinterface==\"admin')) return false;
	if (strpos($cb, "txpinterface==\'admin")) return false;
	if (strpos($cb, 'admin\"==txpinterface')) return false;
	if (strpos($cb, "admin\'==txpinterface")) return false;
	if (strpos($cb, 'admin\"==@txpinterface')) return false;
	if (strpos($cb, "admin\'==@txpinterface")) return false;
	return true;
}

// -------------------------------------------------------------
function ied_plugin_textile($name, $help, $css, $start_dlm, $end_dlm) {
	global $prefs, $ied_plugin_globals;

	$tmpdir = $hlpfile = '';
	$changed = true;
	$out = array();
	if (isset($prefs['ied_plugin_output_tmpcache']) && !empty($prefs['ied_plugin_output_tmpcache']) && is_writable($prefs['ied_plugin_output_tmpcache'])) {
		$tmpdir = $prefs['ied_plugin_output_tmpcache'];
		$cache = build_file_path($tmpdir, 'ied_plugin_composer.cache');
		if (!file_exists($cache)) {
			touch($cache); // Create file if it doesn't exist
		}
		$plugs = parse_ini_file($cache);

		$old_md5 = (is_array($plugs) && array_key_exists($name, $plugs)) ? $plugs[$name] : '';
		$curr_md5 = md5($help);
		$hlpfile = build_file_path($tmpdir, $name.'_help.txtl');
		if ($old_md5 == $curr_md5) {
			$out[0] = file_get_contents($hlpfile);
			$changed = false;
		} else {
			$plugs[$name] = $curr_md5;
			$fd = fopen($cache, "w");
			foreach ($plugs as $idx => $val) {
				fwrite($fd, $idx.' = '.$val.n);
			}
			fclose($fd);
		}
	}

	if (!$out) {
		@include_once txpath.'/lib/classTextile.php';
		$out[0] = $help;
		if (class_exists('Textile')) {
			// Try and be a little more intelligent about applying textile or not
			$re = '/h[0-6](\(.*\))?\./';
			if (preg_match($re, $help)) {
				$textile = new Textile();
				$out[0] = $textile->TextileThis($help);
			}
		}
	}

	if ($tmpdir && $changed) {
		if (!file_exists($hlpfile)) {
			touch($hlpfile);
		}
		$fd = fopen($hlpfile, "w");
		fwrite($fd, $out[0]);
		fclose($fd);
	}

	// Replace the triple '---' in the delimiters so the help file can validate
	$start_dlm = str_replace('---', '***', $start_dlm);
	$end_dlm = str_replace('---', '***', $end_dlm);

	$out[1] = ($css) ? "\n$start_dlm\n$css\n$end_dlm\n" : '';
	return substr(join('', $out), 0, $ied_plugin_globals['size_help']+$ied_plugin_globals['size_css']);
}

// -------------------------------------------------------------
// Put the necessary javascript tags and stuff on the page
function ied_insert_editors() {
	global $prefs;

	$out = array();

	$ed = array();
	$ced = get_pref('ied_plugin_editor');
	$hed = get_pref('ied_plugin_help_editor');
	$cop = get_pref('ied_plugin_editor_options');
	$hop = get_pref('ied_plugin_help_editor_options');
	$ed[$ced] = get_pref('ied_plugin_editor_path');
	$ed[$hed] = get_pref('ied_plugin_help_editor_path');

	$cop = $cop ? ','.$cop : '';
	$hop = $hop ? ','.$hop : '';

	foreach ($ed as $editor => $editor_locs) {
		$jsop = array();
		$locs = do_list($editor_locs);
		foreach($locs as $loc) {
			if (strpos($loc, 'css:') !== false) {
				$jsop[] = '<link rel="stylesheet" href="'.substr($loc, 4).'">';
			} else {
				$jsop[] = '<script type="javascript" src="'.$loc.'"></script>';
			}
		}

		$jsop = join(n, $jsop);

		switch ($editor) {
			case "tiny_mce":
				$out[] = <<<EOJS
{$jsop}
<script type="text/javascript">
tinyMCE.init({
	mode : "specific_textareas",
	editor_selector : "mceEditor"
	{$hop}
});
</script>;
EOJS;
			break;
			case "edit_area":
				$out[] = <<<EOJS
{$jsop}
<script type="text/javascript">
// initialisation
editAreaLoader.init({
	id: "plugin_editor",
	syntax: "php"
	{$cop}
});
</script>
EOJS;
			break;
			case "codemirror":
				$out[] = <<<EOJS
{$jsop}
var ied_pc_editor = CodeMirror.fromTextArea(document.getElementById("plugin_editor"), {
	{$cop}
});
EOJS;
			break;
			case "codepress":
				$out[] = $jsop;
			break;
		}
	}

	return implode("\n", $out);
}

// -------------------------------------------------------------
function ied_plugin_help_viewer($message='') {
	global $prefs, $ied_plugin_globals;

	if (gps('name')) {
		$name = gps('name');
		$help = ($name) ? fetch('help','txp_plugin','name',$name) : '';
		list($css,$help) = ($help) ? ied_plugin_extract_hunk($help, "CSS", "<!--|-->", true) : array('',$help);
	} else if (gps('filename')) {
		$plugin = ied_plugin_read_file($prefs['plugin_cache_dir'].DS.gps('filename'));
		extract($plugin);
	}

	$out = '';
	if (empty($help)) {
		$out = gTxt('ied_plugin_help_not_available');
	} else {
		list ($start_css, $end_css) = ied_plugin_make_markers("CSS", $ied_plugin_globals['css_start'], $ied_plugin_globals['css_end']);
		$out = ied_plugin_textile($name, $help, $css, $start_css, $end_css);
	}

	pagetop(gTxt('ied_plugin_view_help', array('{name}' => $name)), $message);
	echo n. '<div id="plugin_container" class="txp-container txp-view">'.
		n. '<div class="text-column">' . $out . '</div>'.
		n. '</div>';
}

// -------------------------------------------------------------
function ied_plugin_wrap_widget($widget) {
	return '<span class="edit-value">'.$widget.'</span>';
}

// -------------------------------------------------------------
// Stub with correct signature for lifecycle callback
function ied_plugin_setup($evt='', $stp='') {
	ied_plugin_prefs();
}

// -------------------------------------------------------------
// Display the composer's setup / prefs panel
function ied_plugin_prefs($message='') {
	global $ied_pc_event;

	require_privs('plugin_prefs.'.$ied_pc_event);

	if (ps('ied_plugin_pref_save')) {
		ied_plugin_prefs_update();
		$message = gTxt('preferences_saved');
	}

	$ied_plugin_prefs = ied_pc_get_prefs();

	pagetop(gTxt('ied_plugin_lbl_setup'), $message);

	$btnSave = fInput('submit', 'submit', gTxt('save'), 'publish');

	echo '<h1 class="txp-heading">' . gTxt('ied_plugin_lbl_setup') . '</h1>'.
		script_js(<<<EOJS
var ied_plugin_path_re = new RegExp("^.*[/\\]", "g")
function ied_plugin_prefswap(selID, selValue) {
	var id = selID+'_path';
	var nuval = ((basename($("#"+id).val()) == selValue) ? $("#"+id).val() : dirname($("#"+id).val())+selValue);
	if ($("#"+selID)[0].selectedIndex == 0) {
		$("#"+id).attr("disabled", true); ;
	} else {
		$("#"+id).attr("disabled", false); ;
		$("#"+id).val(nuval);
	}
}
function basename(path, suffix) {
	return path.replace(ied_plugin_path_re, '');
}
function dirname(path) {
	return path.match(ied_plugin_path_re);
}
jQuery(function() {
	jQuery(".ied_plugin_setup select option:selected").each(function(obj) {
		var item = jQuery(this);
		ied_plugin_prefswap(item.parent().attr('id'), item.val());
	});
});
EOJS
		);

	$out = array();
	$out[] = n.'<div class="plugin-column">';
	$out[] = '<form name="ied_pc_prefs" id="ied_pc_prefs" action="index.php" method="post">';
	$out[] = eInput($ied_pc_event).sInput('ied_plugin_prefs');

	$last_grp = '';
	foreach ($ied_plugin_prefs as $idx => $prefobj) {
		if ($last_grp != $prefobj['group']) {
			$out[] = hed(gTxt($prefobj['group']), 2);
		}
		$last_grp = $prefobj['group'];
		$subout = array();
		$label = '<span class="edit-label">'
				.'<label>'.gTxt($idx).'</label>'
				.'</span>';
		$val = get_pref($idx, $prefobj['default'], 1);
		$vis = (isset($prefobj['visible']) && !$prefobj['visible']) ? 'empty' : '';
		switch ($prefobj['html']) {
			case 'text_input':
				$subout[] = ied_plugin_wrap_widget(fInput('text', $idx, $val, '', '', '', INPUT_REGULAR, '', $idx));
			break;
			case 'textarea':
				$subout[] = text_area($idx, '', '', $val, $idx);
			break;
			case 'yesnoradio':
				$subout[] = ied_plugin_wrap_widget(yesnoRadio($idx, $val));
			break;
			case 'radioset':
				$subout[] = ied_plugin_wrap_widget(radioSet($prefobj['content'], $idx, $val));
			break;
			case 'checkboxset':
				$vals = do_list($val);
				$lclout = array();
				foreach ($prefobj['content'] as $cb => $val) {
					$checked = in_array($cb, $vals);
					$lclout[] = checkbox($idx.'[]', $cb, $checked). '<label>' . gTxt($val) . '</label>';
				}
				$subout[] = ied_plugin_wrap_widget(join(n, $lclout));
			break;
			case 'selectlist':
				$subout[] = ied_plugin_wrap_widget(selectInput($idx, $prefobj['content'][0], $val, $prefobj['content'][1]));
			break;
			default:
				if ( strpos($prefobj['html'], 'ied_plugin_') !== false && is_callable($prefobj['html']) ) {
					$subout[] = ied_plugin_wrap_widget($prefobj['html']($idx, $val));
				}
			break;
		}
		$out[] = graf($label . n.join(n ,$subout), ($vis ? ' class="'.$vis.'"' : ''));
	}
	$out[] = graf(fInput('submit', 'ied_plugin_pref_save', gTxt('save'), 'publish'));
	$out[] = tInput();
	$out[] = '</form></div>';

	echo join(n, $out);

/*

		echo tr(tda(strong(gTxt('prefs_title')), ' colspan="2"') . tda($btnRemove, $btnStyle) );
		echo '<form method="post" action="?event=ied_plugin_composer&#38;step=ied_plugin_prefs_update">';
		for ($idx = 0; $idx < $numRows; $idx++) {
			$a = $rs[$idx];
			$label = '<label for="'.$a['name'].'">'.gTxt($a['name']).':</label>';
			$out = tda($label, ' style="text-align: right; vertical-align: middle;"');
			switch($a['name']) {
				case "ied_plugin_editor_path":
				case "ied_plugin_help_editor_path":
				case "ied_plugin_output_sfile":
				case "ied_plugin_output_sfilec":
				case "ied_plugin_output_sfilep":
				case "ied_plugin_output_tmpcache":
					$out .= td(fInput('text', $a['name'], $a['val'], 'edit', '', '', 50, '', $a['name']));
					break;
				case "ied_plugin_editor":
//					$out .= td(selectInput($a['name'], array('none' => 'None', 'edit_area' => 'Edit Area', 'codepress' => 'CodePress'), $a['val'], '', ' onchange="ied_plugin_prefswap(this.id, this.value);"', $a['name']));
					$out .= td(selectInput($a['name'], array('none' => 'None', 'edit_area' => 'Edit Area'), $a['val'], '', ' onchange="ied_plugin_prefswap(this.id, this.value);"', $a['name']));
					break;
				case "ied_plugin_help_editor":
					$out .= td(selectInput($a['name'], array('textile' => 'None (Textile)', 'tiny_mce' => 'Tiny MCE'), $a['val'], '', ' onchange="ied_plugin_prefswap(this.id, this.value);"', $a['name']));
					break;
				case "ied_plugin_interface_elems":
					$out .= td(
						checkbox('ied_plugin_interface_elems[]', 'distribution', strpos($a['val'], 'distribution') !== false)
						. '<label>'.gTxt('ied_plugin_if_el_dist').'</label>'
						. checkbox('ied_plugin_interface_elems[]', 'style', strpos($a['val'], 'style') !== false)
						. '<label>'.gTxt('ied_plugin_if_el_style').'</label>'
					);
					break;
				case "ied_plugin_lang_default":
					$out .= td(selectInput($a['name'], ied_plugin_lang_list('all'), $a['val'], '', '', $a['name']));
					break;
				case "ied_plugin_lang_choose":
					$out .= td(selectInput($a['name'], array('installed' => gTxt('ied_plugin_langs_installed'), 'all' => gTxt('ied_plugin_langs_all')), $a['val'], '', '', $a['name']));
					break;
				case "ied_plugin_output_order":
					$out .= td(radio($a['name'],0,(($a['val']==0)?1:0)).gTxt('output_code_first')." " . radio($a['name'],1,(($a['val']==1)?1:0)).gTxt('output_help_first')." ");
					break;
				case "ied_plugin_editor_width":
					$out .= td(fInput('text', $a['name'], $a['val'], 'edit', '', '', 5, '', $a['name']));
					break;
			}
			echo tr($out);
		}
		echo tr(tda($btnSave, $btnStyle));
		echo '</form>';
	} else if ($numRows > 0 && $numRows < $numReqPrefs) {
		echo tr(tda(strong(gTxt('prefs_title')), ' colspan="2"'));
		echo tr(tda(strong(gTxt('prefs_some')).br.br
						.gTxt('prefs_some_explain').br.br
						.gTxt('prefs_some_options'), ' colspan="2"'));
		echo tr(tda($btnRemove,$btnStyle) . tda($btnInstall, $btnStyle));
	} else {
		echo tr(tda(strong(gTxt('prefs_title')), ' colspan="2"'));
		echo tr(tda(gTxt('prefs_not_installed'), ' colspan="2"'));
		echo tr(tda($btnInstall, $btnStyle));
	}

	echo endTable();
*/

}

// -------------------------------------------------------------
// Save plugin prefs from setup panel
function ied_plugin_prefs_update() {
	global $prefs;

	$ied_plugin_prefs = ied_pc_get_prefs();
	$saved = ps('ied_plugin_pref_save');

	// Loop through each plugin setting and make sure it's in the prefs table, as follows:
	// 1) if the value has been POSTed from the prefs panel, set the passed value
	// 2) if the value exists in the $prefs (i.e. run on upgrade), set the existing value
	// 3) if neither exist, set the plugin default value
	foreach ($ied_plugin_prefs as $key => $prefobj) {
		$val = ($saved || isset($_POST[$key])) ? ps($key) : ((isset($prefs[$key])) ? $prefs[$key] : $prefobj['default']);
		$val = (is_array($val)) ? join(', ', $val) : $val;
		set_pref($key, doSlash($val), 'ied_plugin', $prefobj['type'], $prefobj['html'], $prefobj['position']);
	}
}

// -------------------------------------------------------------
// Delete plugin prefs
function ied_plugin_prefs_remove($showpane='1') {
	safe_delete('txp_prefs', "name like 'ied_plugin_%'");

	if ($showpane) {
		$message = gTxt('ied_plugin_prefs_deleted');
		ied_plugin_prefs($message);
	}
}

// -------------------------------------------------------------
function ied_plugin_lang_list($flavour='installed') {
	global $prefs;

	$ied_langs = array();
	if ($flavour == 'installed') {
		// Self-join to get all the installed langs and language strings in one step
//		$installed_langs = safe_query('select t1.lang, t2.data from '.PFX.'txp_lang as t1, '.PFX.'txp_lang as t2 WHERE t1.lang = t2.name GROUP BY lang');
		$ied_langs = safe_column('lang', 'txp_lang', '1=1 GROUP BY lang');
	} else {
		// Grab all available langs from the RPC server
		require_once txpath.'/lib/IXRClass.php';

		$client = new IXR_Client(RPC_SERVER);

		// Get items from RPC
		@set_time_limit(5);
		if ($client->query('tups.listLanguages',$prefs['blog_uid'])) {
			$response = $client->getResponse();
			foreach ($response as $language) {
				$ied_langs[] = $language['language'];
			}
		}
	}

	// Build the select list array
	$langlist = array();
	foreach ($ied_langs as $ied_lang) {
		$langlist[$ied_lang] = gTxt($ied_lang);
	}

	return $langlist;
}

// -------------------------------------------------------------
function ied_plugin_textpack_build($name, $force_all = 0) {
	global $prefs;

	$fetch_lang = null;

	if ($force_all === 0) {
		$fetch_lang = gps('lang');
	}
	if (!$fetch_lang) {
		$fetch_lang = ($force_all === 1) ? join(',', array_keys(ied_plugin_lang_list('installed'))) : $prefs['ied_plugin_lang_selected'];
	}

	$tpout = array();
	if ($fetch_lang) {
		$chosen_lang = get_pref('ied_plugin_lang_default', '');
		$dflt_lang = ($chosen_lang === '') ? $prefs['language'] : $chosen_lang; // Guard against situations when the chosen default lang is 'any'
		$tp_pfx = unserialize(get_pref('ied_plugin_tp_prefix', '', 1));
		$tp_pfx = isset($tp_pfx[$name]) ? $tp_pfx[$name] : '';
		$tp_rows = ied_plugin_textpack_grab($fetch_lang, $tp_pfx);

		if ($tp_rows) {
			$ctr = 0;
			$prevlang = '';

			// Go through all the languages and put the default language at the start of the array
			foreach ($tp_rows as $row) {
				// Add the event marker
				$theEvent = in_array($row['event'], array('public', 'common')) ? $row['event'] : $tp_pfx;
				if ($prevlang != $row['lang']) {
					$ctr++;
				}

				$idx = ($row['lang'] == $dflt_lang) ? 0 : $ctr;
				$tplang[$idx][$theEvent][$row['lang']][$row['name']] = $row['data'];
				$prevlang = $row['lang'];
				$prevevent = $row['event'];
			}

			ksort($tplang); // Make sure default language is actually first

			// Build the final textpack array with language markers.
			// Note the marker for the default language may (should!) be omitted if the author wants
			// the strings to be installed regardless of language on destination server.
			// If a specific language is set and the user does not have that language
			// installed, the strings would not be inserted
			$prevevent = '';
			foreach ($tplang as $idx => $langblock) {
				foreach ($langblock as $ev => $codeblock) {
					$tpheader = array();
					$tpstrings = array();
					if ($prevevent != $ev) {
						$tpheader[] = '#@'.$ev;
					}
					foreach ($codeblock as $code => $data) {
						if ( ($idx == 0 && $chosen_lang) || ($idx > 0) ) {
							$tpheader[] = '#@language '.$code;
						}
						foreach ($data as $key => $val) {
							// Don't output empty strings
							if ($val) {
								$tpstrings[] = $key . ' => ' . $val;
							}
						}
						if ($tpstrings) {
							$tpout = array_merge($tpout, $tpheader, $tpstrings);
						}
					}
				}
			}
		}
	}
	return join(n, $tpout);
}

// -------------------------------------------------------------
function ied_plugin_textpack_grab($lang, $prefix) {

	if ($lang === 'IED_ALL') {
		$lang_query = '';
	} else {
		$lang = (empty($lang)) ? get_pref('language', 'en-gb') : $lang;
		$langs = quote_list(do_list($lang));
		$lang_query = "lang IN (".join(', ', $langs).") AND ";
	}

	return ($prefix) ? safe_rows('name, data, lang, event', 'txp_lang', $lang_query."name LIKE '".doSlash($prefix)."%' ORDER BY event,lang,name") : array();
}

// -------------------------------------------------------------
// *** AJAX calls
// -------------------------------------------------------------
function ied_plugin_lang_set() {
	$sel = doSlash(gps('ied_tp_langsel'));
	set_pref('ied_plugin_lang_selected', $sel, 'ied_plugin', PREF_HIDDEN, 'text_input', 0, PREF_PRIVATE);
}
// -------------------------------------------------------------
// Store the plugin textpack prefix
function ied_plugin_set_tp_prefix($plugname='', $pfx='') {
	global $app_mode;

	$plugname = ($plugname) ? $plugname : gps('plugin');
	$pfx = ($pfx) ? $pfx : gps('prefix');

	if ($pfx) {
		$curr_pfx = unserialize(get_pref('ied_plugin_tp_prefix'));
		$curr_pfx[$plugname] = $pfx;
		set_pref('ied_plugin_tp_prefix', serialize($curr_pfx), 'ied_plugin', PREF_HIDDEN, 'text_input');
	}
}
// -------------------------------------------------------------
// TODO: sanitize $fn
// Return a string from a (type 4 or 5) plugin gTxt() function/method.
function ied_plugin_textpack_load() {
	$fn = doSlash(gps('ied_tp_fn'));
	$lbl = doSlash(gps('ied_tp_lbl'));
	$ret = '';

	$fnparts = do_list($fn, '::');
	if (count($fnparts) == 2) {
		// Callable class -> method
		$fobj = array($fnparts[0], $fnparts[1]);
	} else {
		$fobj = $fnparts[0];
	}
	if (is_callable($fobj)) {
		$ret = call_user_func_array($fobj, array($lbl));
	}

	if ($ret) {
		echo json_encode(array('ied_plugin_tp_string' => $ret));
	}
}
// -------------------------------------------------------------
function ied_plugin_textpack_del() {
	$lbl = doSlash(gps('ied_tp_lbl'));

	$ret = safe_delete('txp_lang', "name='$lbl'");
}
// -------------------------------------------------------------
function ied_plugin_textpack_save() {
	$lbl = doSlash(gps('ied_tp_lbl'));
	$str = doSlash(gps('ied_tp_str'));
	$lng = doSlash(gps('ied_tp_lng'));
	$evt = doSlash(gps('ied_tp_evt'));

	$where = "name='$lbl' AND lang='$lng'";
	$ret = safe_update('txp_lang', "data='$str', event='$evt'", $where);
	if ($ret && (mysql_affected_rows() or safe_count('txp_lang', $where))) {
		// Update OK: do nothing else
	} else {
		$ret = safe_insert('txp_lang', "name='$lbl', lang='$lng', event='$evt', data='$str'");
	}
}
// -------------------------------------------------------------
function ied_plugin_textpack_get() {
	$lbl = doSlash(gps('ied_tp_lbl'));
	$lng = doSlash(gps('ied_tp_lng'));
	$dflt = doSlash(gps('ied_tp_dflt'));

	$rs = safe_rows('lang, data', 'txp_lang', "name='$lbl' AND (lang='$lng' OR lang='$dflt')");
	$out = array();
	foreach ($rs as $row) {
		if (($row['lang'] == $dflt) && ($lng != $dflt)) {
			$out['ied_plugin_tp_dflt'] = $row['data'];
		} else {
			$out['ied_plugin_tp_string'] = $row['data'];
		}
	}
	echo json_encode($out);
}

// -------------------------------------------------------------
function ied_plugin_code_save() {
	global $theme;

	$syntax_check = get_pref('ied_plugin_syntax_check');

	$plug = doSlash(ps('plugin'));
	$code = ps('codeblock');

	$ret = ($syntax_check) ? ied_plugin_check_syntax_err($code) : false;
	$msg = '';

	if ($ret === false) {
		$ret = @safe_update('txp_plugin', "code='".doSlash($code)."'", "name='$plug'");
		if ($ret) {
			$msg = $theme->announce_async(gTxt('ied_plugin_code_saved'));
		} else {
			$msg = $theme->announce_async(array(gTxt('ied_plugin_code_saved_fail'), E_ERROR));
		}
		send_xml_response(array('ied_plugin_msg' => $msg));
	} else {
		$msg = $theme->announce_async(array(htmlentities($ret[0], ENT_QUOTES), E_ERROR));
		send_xml_response(array('http-status' => '412 Precondition Failed', 'ied_plugin_msg' => $msg, 'ied_plugin_err_line' => $ret[1]));
	}
}

/**
 * Check the syntax of some PHP code.
 * Mostly from a comment in http://php.net/manual/en/function.php-check-syntax.php
 * @param string $code PHP code to check.
 * @return boolean|array If false, then check was successful, otherwise an array(message,line) of errors is returned.
 */
function ied_plugin_check_syntax_err($code) {
	if (!defined('CR')) define('CR',chr(13));
	if (!defined('LF')) define('LF',chr(10));

	$braces=0;
	$inString=0;
	foreach (token_get_all('<?php ' . $code) as $token) {
		if (is_array($token)) {
			switch ($token[0]) {
				case T_CURLY_OPEN:
				case T_DOLLAR_OPEN_CURLY_BRACES:
				case T_START_HEREDOC: ++$inString; break;
				case T_END_HEREDOC:   --$inString; break;
			}
		} else if ($inString & 1) {
			switch ($token) {
				case '`': case '\'':
				case '"': --$inString; break;
			}
		} else {
			switch ($token) {
				case '`': case '\'':
				case '"': ++$inString; break;
				case '{': ++$braces; break;
				case '}':
					if ($inString) {
						--$inString;
					} else {
						--$braces;
						if ($braces < 0) break 2;
					}
					break;
			}
		}
	}
	$inString = @ini_set('log_errors', false);
	$token = @ini_set('display_errors', true);
	ob_start();
	$braces || $code = "if(0){{$code}\n}";
	if (eval($code) === false) {
		if ($braces) {
			$braces = PHP_INT_MAX;
		} else {
			false !== strpos($code,CR) && $code = str_replace(CR,LF,str_replace(CRLF,LF,$code));
			$braces = substr_count($code,LF);
		}
		$code = ob_get_clean();
		$code = strip_tags($code);
		if (preg_match("'syntax error, (.+) in .+ on line (\d+)$'s", $code, $code)) {
			$code[2] = (int) $code[2];
			$code = $code[2] <= $braces
				? array($code[1], $code[2])
				: array('unexpected $end' . substr($code[1], 14), $braces);
		} else $code = array('syntax error', 0);
	} else {
		ob_end_clean();
		$code = false;
	}
	@ini_set('display_errors', $token);
	@ini_set('log_errors', $inString);
	return $code;
}

// Reflection utility for phpdoc generation
function ied_plugin_reflunction_factory($callback) {
	if (is_array($callback)) {
		// must be a class method
		list($class, $method) = $callback;
		return new ReflectionMethod($class, $method);
	}

	// class::method syntax
	if (is_string($callback) && strpos($callback, "::") !== false) {
		list($class, $method) = explode("::", $callback);
		return new ReflectionMethod($class, $method);
	}

	// objects as functions (PHP 5.3+)
	if (version_compare(PHP_VERSION, "5.3.0", ">=") && method_exists($callback, "__invoke")) {
		return new ReflectionMethod($callback, "__invoke");
	}

	// assume it's a function
	return new ReflectionFunction($callback);
}
function ied_plugin_generate_phpdoc() {
	$plug = gps('plugin');
	$obj = gps('fn');
	$ret = load_plugin($plug);
	if ($ret) {
		try {
			$obj = ied_plugin_reflunction_factory($obj);
			$name = $obj->getName();
			$doc = $obj->getDocComment();

			if ($doc) {
				//TODO: make list of allowed docblock tags a pref
				//TODO: parse existing doc block and append any new vars/params
				preg_match_all('/\s*\*\s*\@(abstract|access|author|category|copyright|deprecated|example|final|filesource|global|ignore|internal|license|link|method|name|package|param|property|return|see|since|static|staticvar|subpackage|todo|tutorial|uses|var|version)\s+(\w+)\s+(\$\w+(?::\w+|->\w+)*)\s+(\[(\&|\+|abstract|final|private|protected|public|static)\])*\s+(.*)/', $doc, $matches, PREG_SET_ORDER);

				$tags = array();
				// Reshuffle the matches to index on [tag][var]
				foreach ($matches as $idx => $data) {
					$ful = isset($data[0]) ? $data[0] : '';
					$tag = isset($data[1]) ? $data[1] : '';
					$typ = isset($data[2]) ? $data[2] : '';
					$var = isset($data[3]) ? $data[3] : '';
					$mod = isset($data[4]) ? $data[4] : '';
					$tags[$tag][$var] = array(
						'type' => $typ,
						'full' => $ful,
						'mod'  => $mod,
					);
				}
			}

			$fntype = array('meta' => array(), 'type' => array());

			if (method_exists($obj, 'isConstructor')) {
				// Dealing with a class method
				if ($obj->isConstructor()) {
					$fntype['meta'][] = ' [c]';
				}
				if ($obj->isPublic()) {
					$fntype['mod'][] = '[public]';
				}
				if ($obj->isPrivate()) {
					$fntype['mod'][] = '[private]';
				}
				if ($obj->isProtected()) {
					$fntype['mod'][] = '[protected]';
				}
				if ($obj->isAbstract()) {
					$fntype['mod'][] = '[abstract]';
				}
				if ($obj->isFinal()) {
					$fntype['mod'][] = '[final]';
				}
				if ($obj->isStatic()) {
					$fntype['mod'][] = '[static]';
				}
			}

			$params = $obj->getParameters();
			$param_list = array();
			if ($params != null) {
				foreach($params as $param) {
					$has_dflt = false;
					$type = 'string';

					// Crude type checker
					if ($param->isDefaultValueAvailable()) {
						$has_dflt = true;
						$dflt = $param->getDefaultValue();

						if (is_bool($dflt)) {
							$type = 'boolean';
							$dflt = $dflt ? 'true' : 'false';
						} else if (is_null($dflt)) {
							$dflt = 'NULL';
						} else if ($dflt == (string)(int)$dflt) {
							$type = 'integer';
						} else if ($dflt == (string)(float)$dflt) {
							$type = 'float';
						} else if (is_numeric($dflt)) {
							$type = 'number';
						} else if (is_array($dflt)) {
							$type = 'array';
						}

						if ( ($type == 'string' || $type == 'array') && empty($dflt) ) {
							$dflt = 'empty';
						} else if ($type == 'string' && $dflt != 'NULL') {
							$dflt = '"'.$dflt.'"';
						}
					}

					$item = '@param'.t.$type.t.'$'.$param->getName();
					if($param->isPassedByReference()) {
						$item .= ' [&]';
					}
					if($param->allowsNull()) {
						$item .= ' [+]';
					}
					$item .= ($has_dflt) ? t.'(Default: ' .$dflt. ')' : t;
					$item .= t.'Param description';
					$param_list[] = $item;
				}
			}

			$final = '<pre>';
			$final .= "/**".br;
			$final .= " * $name" . ((!empty($fntype['meta'])) ? $fntype['meta'] : '') .t. (($fnType['mod']) ? join(' ', $fnType['mod']).' ' : '') . "Description".br;
			$final .= " *".br;
			$final .= " * Summary goes here".br;
			$final .= " *".br;
			foreach ($param_list as $item) {
				$final .= " * ". $item.br;
			}
			$final .= " */</pre>".br;

			send_xml_response(array('ied_plugin_phpdoc' => $final));
		} catch (Exception $ex) {
			send_xml_response( array( 'http-status' => '501 Not Implemented', 'error_msg' => $ex->getMessage() . gTxt('ied_plugin_fn_not_exist') ) );
		}
	} else {
		send_xml_response(array('http-status' => '400 Bad Request'));
	}

/*	$classInterfaces=$reflection->getInterfaces();
	//get information about the interfaces
	if($classInterfaces != null)
	{
	fwrite($hf,"\n</i>\t</td></tr>\n\t<tr>".
	"<td align=\"center\" colspan=\"0\">".
	"<font face=\"arial\" size=\"2\"".
	" color=\"purple\">Implemented".
	" interfaces:</td><td align=\"center\"".
	" colspan=\"0\"><font face=\"arial\"".
	" size=\"2\" color=\"black\"><b>Name</b>".
	"</font></td><td align=\"center\"".
	" colspan=\"3\"><font face=\"arial\"".
	" size=\"2\" color=\"black\"><b>".
	"Description</b></font></td></tr>\n");
	 foreach($classInterfaces as $in)
		{
		fwrite($hf,"\t<tr><td></td><td align=".
		"\"center\">");
		fwrite($hf,$in->getName());
		fwrite($hf,"</td><td align=\"center\"".
		" colspan=\"3\"><i>\n");
		fwrite($hf,$in->getDocComment());
		fwrite($hf,"</i></td></tr>\n");
		}
	 }

	//get the superclass information
	$superclass=$reflection->getParentClass();
	if ($superclass != null){
		fwrite($hf,"\t<tr><td align=\"center\"".
		 "colspan=\"0\"><font face=\"arial\"".
		 " size=\"2\" color=\"purple\">".
		 "The superclass is:</td><td".
		 " align=\"center\" colspan=\"4\">".
		 "<font face=\"arial\" size=\"2\"".
		 " color=\"black\"><b>\n");
		fwrite($hf,$superclass->getName());
		fwrite($hf,"</font></td></tr>\n");
		}

	//get information about the constants
	$constants=$reflection->getConstants();
	if($constants != null)
	  {
	  $constantsNumber=count($constants);
	  fwrite($hf,"\t<tr><td align=\"center\"".
	  " colspan=\"0\"><font face=\"arial\"".
	  " size=\"2\" color=\"purple\">".
	  "Constants:</td><td align=\"center\"".
	  " colspan=\"0\"><font face=\"arial\"".
	  " size=\"2\" color=\"black\"><b>Name</b>".
	  "</font></td><td align=\"center\"".
	  " colspan=\"3\"><font face=\"arial\"".
	  " size=\"2\" color=\"black\"><b>Value</b>".
	  "</font></td></tr>\n");
	  foreach($constants as $keys=>$value)
		{
		fwrite($hf,"\t<tr><td></td><td".
		" align=\"center\">");
		fwrite($hf,$keys);
		fwrite($hf,"</td><td align=\"center\"".
		" colspan=\"3\">");
		fwrite($hf,$value);
		fwrite($hf,"</td></tr>\n");
		}
	  }

	//get information about properties
	$properties=$reflection->getProperties();
	if($properties != null)
	  {
	  fwrite($hf,"\t<tr><td align=\"center\"".
	  " colspan=\"0\"><font face=\"arial\"".
	  " size=\"2\" color=\"purple\">".
	  "Properties:</td><td align=\"center\"".
	  " colspan=\"0\"><font face=\"arial\"".
	  " size=\"2\" color=\"black\"><b>Name</b>".
	  "</font></td><td align=\"center\"".
	  " colspan=\"3\"><font face=\"arial\"".
	  " size=\"2\" color=\"black\"><b>".
	  "Modifiers</b></font></td></tr>\n");
	  foreach($properties as $in)
		 {
		 fwrite($hf,"\t<tr><td></td><td".
		 " align=\"center\" colspan=\"0\">");
		 fwrite($hf,$in->getName());
		 fwrite($hf,"</td><td align=\"center\"".
		 " colspan=\"3\">");

		 if($in->isPublic())
				 { fwrite($hf,"[public]"); }
		 if($in->isPrivate())
				 { fwrite($hf,"[private]"); }
		 if($in->isProtected())
				 { fwrite($hf,"[protected]"); }
		 if($in->isStatic())
				 { fwrite($hf,"[static]"); }
		 fwrite($hf,"</td></tr>\n");
		 }
	  }
*/

}

// ------------------------
// List of supported javascript syntax highlighter / code editors
// NB: no i18n since these are the names of the projects
function ied_plugin_code_editors($name, $val='') {
	$eds['none'] = gTxt('none');
	$eds['edit_area'] = 'EditArea';
	$eds['codemirror'] = 'CodeMirror';
	$eds['codepress'] = 'CodePress';
	return selectInput($name, $eds, $val, false);
}

// ------------------------
// List of supported javascript help editors
// NB: no i18n since these are the names of the projects
function ied_plugin_help_editors($name, $val='') {
	$eds['textilee'] = 'Textile';
	$eds['tiny_mce'] = 'TinyMCE';
	return selectInput($name, $eds, $val, false);
}

// ------------------------
// List of language options
function ied_plugin_lang_options($name, $val='') {
	$lngs['installed'] = gTxt('ied_plugin_langs_installed');
	$lngs['all'] = gTxt('ied_plugin_langs_all');
	return selectInput($name, $lngs, $val, false);
}

// ------------------------
// List of language options
function ied_plugin_lang_default($name, $val='') {
	$langs = array_merge(array('' => gTxt('ied_plugin_any')), ied_plugin_lang_list('all'));
	return selectInput($name, $langs, $val, false);
}

// ------------------------
// Settings for the plugin
function ied_pc_get_prefs() {
	global $prefs;

	$ied_pc_prefs = array(
		'ied_plugin_editor' => array(
			'html'     => 'ied_plugin_code_editors',
			'type'     => PREF_HIDDEN,
			'position' => 10,
			'default'  => 'none',
			'group'    => 'ied_plugin_if_settings',
		),
		'ied_plugin_editor_path' => array(
			'html'     => 'text_input',
			'type'     => PREF_HIDDEN,
			'position' => 20,
			'default'  => hu.'js/',
			'group'    => 'ied_plugin_if_settings',
		),
		'ied_plugin_editor_options' => array(
			'html'     => 'textarea',
			'type'     => PREF_HIDDEN,
			'position' => 30,
			'default'  => '',
			'group'    => 'ied_plugin_if_settings',
		),
		'ied_plugin_editor_width' => array(
			'html'     => 'text_input',
			'type'     => PREF_HIDDEN,
			'position' => 40,
			'default'  => '90%',
			'group'    => 'ied_plugin_if_settings',
		),
		'ied_plugin_help_editor' => array(
			'html'     => 'ied_plugin_help_editors',
			'type'     => PREF_HIDDEN,
			'position' => 50,
			'default'  => 'textile',
			'group'    => 'ied_plugin_if_settings',
		),
		'ied_plugin_help_editor_path' => array(
			'html'     => 'text_input',
			'type'     => PREF_HIDDEN,
			'position' => 60,
			'default'  => hu.'js/',
			'group'    => 'ied_plugin_if_settings',
		),
		'ied_plugin_help_editor_options' => array(
			'html'     => 'textarea',
			'type'     => PREF_HIDDEN,
			'position' => 70,
			'default'  => 'theme_advanced_toolbar_location : "top",
theme_advanced_buttons1 : "bold,italic,underline,strikethrough,forecolor,backcolor,removeformat,numlist,bullist,outdent,indent,justifyleft,justifycenter,justifyright,justifyfull",
theme_advanced_buttons2 : "link,unlink,separator,ibrowser,separator,search,replace,separator,cut,copy,paste,separator,code,separator,formatselect",
theme_advanced_buttons3 : ""',
			'group'    => 'ied_plugin_if_settings',
		),
		'ied_plugin_interface_elems' => array(
			'html'     => 'checkboxset',
			'type'     => PREF_HIDDEN,
			'position' => 80,
			'content'  => array('distribution' => 'ied_plugin_if_el_dist', 'style' => 'ied_plugin_if_el_style'),
			'default'  => 'style',
			'group'    => 'ied_plugin_if_settings',
		),
		'ied_plugin_lifecycle_options' => array(
			'html'     => 'checkboxset',
			'type'     => PREF_HIDDEN,
			'position' => 90,
			'content'  => array('installed' => 'ied_plugin_lbl_lc_install', 'enabled' => 'ied_plugin_lbl_lc_enable', 'disabled' => 'ied_plugin_lbl_lc_disable', 'deleted' => 'ied_plugin_lbl_lc_delete'),
			'default'  => '',
			'group'    => 'ied_plugin_prefs',
		),
		'ied_plugin_auto_enable' => array(
			'html'     => 'radioset',
			'type'     => PREF_HIDDEN,
			'position' => 100,
			'content'  => array('0' => gTxt('no'), '1' => gTxt('yes'), '2' => gTxt('ied_plugin_same')),
			'default'  => '2',
			'group'    => 'ied_plugin_prefs',
		),
		'ied_plugin_syntax_check' => array(
			'html'     => 'yesnoradio',
			'type'     => PREF_HIDDEN,
			'position' => 110,
			'default'  => '1',
			'group'    => 'ied_plugin_prefs',
		),
		'ied_plugin_lang_choose' => array(
			'html'     => 'ied_plugin_lang_options',
			'type'     => PREF_HIDDEN,
			'position' => 120,
			'default'  => 'installed',
			'group'    => 'ied_plugin_prefs',
		),
		'ied_plugin_lang_default' => array(
			'html'     => 'ied_plugin_lang_default',
			'type'     => PREF_HIDDEN,
			'position' => 130,
			'default'  => '',
			'group'    => 'ied_plugin_prefs',
		),
		'ied_plugin_output_order' => array(
			'html'     => 'radioset',
			'type'     => PREF_HIDDEN,
			'position' => 140,
			'content'  => array('0' => gTxt('ied_plugin_lbl_op_code_first'), '1' => gTxt('ied_plugin_lbl_op_help_first')),
			'default'  => '0',
			'group'    => 'ied_plugin_prefs',
		),
		'ied_plugin_output_sfile' => array(
			'html'     => 'text_input',
			'type'     => PREF_HIDDEN,
			'position' => 150,
			'default'  => '{name}_v{version}.txt',
			'group'    => 'ied_plugin_prefs',
		),
		'ied_plugin_output_sfilec' => array(
			'html'     => 'text_input',
			'type'     => PREF_HIDDEN,
			'position' => 160,
			'default'  => '{name}_v{version}_zip.txt',
			'group'    => 'ied_plugin_prefs',
		),
		'ied_plugin_output_sfilep' => array(
			'html'     => 'text_input',
			'type'     => PREF_HIDDEN,
			'position' => 170,
			'default'  => '{name}_v{version}.php',
			'group'    => 'ied_plugin_prefs',
		),
		'ied_plugin_output_sfilet' => array(
			'html'     => 'text_input',
			'type'     => PREF_HIDDEN,
			'position' => 180,
			'default'  => '{name}_v{version}_{lang}_textpack.txt',
			'group'    => 'ied_plugin_prefs',
		),
		'ied_plugin_output_tmpcache' => array(
			'html'     => 'text_input',
			'type'     => PREF_HIDDEN,
			'position' => 190,
			'default'  => $prefs['tempdir'],
			'group'    => 'ied_plugin_prefs',
		),
	);

	return $ied_pc_prefs;
}

/*
 * Public tag: List plugins, filtered by name or prefix
 */
function ied_plugin_list($atts = array(), $thing = null) {
	global $ied_plugin_data;

	extract(lAtts(array(
		'from'       => 'database', // database, cache (or both)
		'name'       => '', // List of plugin names to return
		'prefix'     => '', // Plugin prefixes
		'exclude'    => '', // names to exclude from the list
		'type'       => '', // 0-5 or comma-separated combos thereof
		'wraptag'    => '',
		'class'      => '',
		'break'      => '',
		'breakclass' => '',
		'html_id'    => '',
		'form'       => '',
	),$atts));

	$thing = (empty($form)) ? ((empty($thing)) ? '<txp:ied_plugin_info item="name" />' : $thing) : fetch_form($form);

	$location = do_list($from);
	$names = do_list($name);
	$prefixes = do_list($prefix);
	$excludes = do_list($exclude);

	if (in_array('database', $location)) {
		$sql = array();
		$sql[] = '1';
		if ($name) {
			$sql[] = "name IN ('".join("','", doSlash($names))."')";
		}
		if ($prefix) {
			$sqlor = array();
			foreach ($prefixes as $pfx) {
				$sqlor[] = "name LIKE '".doSlash($pfx)."%'";
			}
			$sql[] = '(' . join(' OR ', $sqlor) . ')';
		}
		if ($exclude) {
			$sql[] = "name NOT IN ('".join("','", doSlash($excludes))."')";
		}

		$rs = safe_rows('*', 'txp_plugin', join(' AND ', $sql) . ' ORDER BY name');
	}

	// TODO: Add the meta data from matching plugins in the cache folder
	if (in_array('cache', $location)) {
		
	}

	$out = array();
	$ied_pd_saved = $ied_plugin_data;
	foreach ($rs as $row) {
		$ied_plugin_data = $row;
		$out[] = parse($thing);
		$ied_plugin_data = array();
	}
	$ied_plugin_data = $ied_pd_saved;

	return ($wraptag) ? doWrap($out, $wraptag, $break, $class, $breakclass, '', '', $html_id) : join($break, $out);
}

/**
 * Public tag: Display plugin data for form/container usage
 */
function ied_plugin_info($atts, $thing = null) {
	global $ied_plugin_data;

	extract(lAtts(array(
		'item'    => '',
		'wraptag' => '',
		'break'   => '',
		'class'   => '',
		'debug'   => 0,
	), $atts));

	$pdata = is_array($ied_plugin_data) ? $ied_plugin_data : array();

	if ($debug) {
		echo '++ AVAILABLE INFO ++';
		dmp($pdata);
	}

	$items = do_list($item);
	$out = array();

	foreach ($items as $it) {
		if (isset($pdata[$it])) {
			$out[] = $pdata[$it];
		}
	}

	return doWrap($out, $wraptag, $break, $class);
}

/**
 * Public tag: List of available textpack information.
 */
function ied_plugin_textpacks($atts, $thing = null)
{
	global $ied_plugin_data;

	extract(lAtts(array(
		'name'     => '',
		'filename' => '',
		'lang'     => 'IED_ALL',
		'wraptag'  => '',
		'break'    => '',
		'class'    => '',
		'form'     => '',
	), $atts));

	if (!$name && !$filename) {
		return;
	}

	if ($name) {
		$theName = $name;
	} else if ($filename) {
		$theName = $filename;
	}

	$thing = (empty($form)) ? ((empty($thing)) ? '<txp:ied_plugin_info item="lang" />' : $thing) : fetch_form($form);

	$langs = array();
	$tp_prefixes = unserialize(get_pref('ied_plugin_tp_prefix', ''));

	if (isset($tp_prefixes[$theName])) {
		$strings = ied_plugin_textpack_grab($lang, $tp_prefixes[$theName]);
		foreach ($strings as $row) {
			if (array_search($row['lang'], $langs) === false) {
				$langs[] = $row['lang'];
			}
		}
	}
	$out = array();
	$ied_pd_saved = $ied_plugin_data;
	$idx = 0;
	$num_langs = count($langs);

	foreach ($langs as $row) {
		$ied_plugin_data['lang'] = $row;
		$ied_plugin_data['first_lang'] = (($idx === 0) ? 1 : 0);
		$ied_plugin_data['last_lang'] = (($idx === $num_langs - 1) ? 1 : 0);
		$out[] = parse($thing);
		$ied_plugin_data['lang'] = $ied_plugin_data['first_lang'] = $ied_plugin_data['last_lang'] = '';
		$idx++;
	}
	$ied_plugin_data = $ied_pd_saved;

	return doWrap($out, $wraptag, $break, $class);
}

/**
 * Public tag: Download a plugin
 */
function ied_plugin_download_link($atts, $thing = null)
{
	extract(lAtts(array(
		'type'     => 'compressed', // uncompressed, compressed, template, textpack
		'name'     => '',
		'filename' => '',
		'label'    => 'Download',
		'class'    => '',
		'lang'     => 'IED_ALL',
		'form'     => '',
		'escape'   => 'html',
	), $atts));

	if (!$name && !$filename) {
		return;
	}

	$amp = ($escape === 'html') ? '&amp;' : '&';

	if ($name) {
		$theName = $amp.'name='.urlencode($name);
	} else if ($filename) {
		$theName = $amp.'filename='.urlencode($filename);
	}

	$theClass = '';
	if ($class) {
		$theClass = ' class="'.$class.'"';
	}
	$langopt = '';
	if ($lang) {
		$langs = do_list($lang);
		$langopt = $amp.'lang='.join(',', $langs);
	}

	$linkName = (empty($form)) ? ((empty($thing)) ? $label : parse($thing)) : parse_form($form);

	if ($type === 'compressed') {
		return href($linkName, '?ied_plugin_download=1'.$theName.$amp.'type=zip'.$langopt, $theClass);
	} else if ($type === 'uncompressed') {
		return href($linkName, '?ied_plugin_download=1'.$theName.$amp.'type=txt'.$langopt, $theClass);
	} else if ($type === 'template') {
		return href($linkName, '?ied_plugin_download=1'.$theName.$amp.'type=php'.$langopt, $theClass);
	} else if ($type === 'textpack') {
		return href($linkName, '?ied_plugin_download=1'.$theName.$amp.'type=textpack'.$langopt, $theClass);
	}
}

/**
 * Handles downloading plugin content
 */
function ied_plugin_download() {
	if (gps('ied_plugin_download')) {
		$type = gps('type');
		switch ($type) {
			case 'zip':
			case 'txt':
				ied_plugin_save_as_file();
				break;
			case 'php':
				ied_plugin_save_as_php_file();
				break;
			case 'textpack':
				ied_plugin_save_as_textpack();
				break;
		}
	}
}