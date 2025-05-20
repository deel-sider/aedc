<?php

/*
	adi_menu - Section hierarchy, section menu and breadcrumb trail

	Written by Adi Gilbert

	Released under the GNU General Public License

	Version history:
	1.3.1	- lifecycle "upgrade" pseudo-event
			- fixed links to section tab in TXP 4.5
	1.3		- TXP 4.5-ified
			- moved install/uninstall to plugin options
			- Textpack added
			- tooltips thrown in the tip
			- fix: preferences fully deleted on uninstall
			- enhancement: adi_menu admin section redirect warning message
			- enhancement: admin preference "Prevent widowed words in article titles" now obeyed (for springworks)
			- code tidy up & old code removed
			- change: removed import functionality
			- change: admin tab name changed "adi_menu" -> "Menu"
			- change: use pluggable_ui instead of old inject method in Article tab
	1.2		- section-specific article sort (for bg)
			- enhancement: adi_menu admin sections link to TXP section tab
			- fix: ampersands in article titles (thanks renobird)
			- fix: admin styling for Hive
			- now uses lifecycle events for install/uninstall
	1.1.1	- fixed: one adi_menu tag affecting operation of another (thanks Teemu)
			- fix: renamed some globals
			- fix: restrict style to adi_menu_admin tab only
	1.1		- enhancement: section redirection (for me & caruso_g)
			- enhancement: alternative section titles
			- enhancement: adi_menu admin tab now only shows admin options (i.e. install/uninstall/import) if relevant
			- enhancement: admin tab sanity checks - sections renamed or links deleted
			- enhancement: write tab section popup list format & indent preferences (for Bloke)
			- enhancement: new article method, attribute 'new_article_mode' to switch off ('test' attribute ignored)
			- new attribute: 'odd_even' to generate <li> classes on odd/even items
			- change: section sort now defaults to "adi_menu_sort" (use sort="" to override)
			- fix: dodgy bold in hierarchy summary, added italics & "default" now "Home"
			- fix: errors in admin tab if sections renamed
			- fix: submenus not being generated properly (again)
	1.0		- testing only, not officially released
	0.12.1	- fix: error on error page when sub_menu_level > 1 (thanks CeBe)
			- fix: error with submenu if active section excluded in admin
	0.12	- new attribute 'current_children_only' (for ttr & caruso_g)
			- enhancement: new (automatic) way of coping with accented characters (thanks ttr)
			- enhancement: minor admin tab improvements - admin options (i.e. install/uninstall/import) only shown if relevant
			- fix: error when adi_menu_breadcrumb used more than once on a page (thanks jcd)
			- fix: current_children_only not functioning correctly (thanks jcd)
			- fix: couple of uninitialised variables generating error_log messages (lines 827, 837)
	0.11	- new adi_menu attributes: 'wraptag', 'wraptag_id', 'wraptag_class'
			- enhancement: sections attribute now overrides admin excluded sections
	0.10	- enhancement: speaking blocks
			- new adi_menu attributes: 'speaking_block' & 'speaking_block_form'
			- new adi_menu attributes: 'label', 'labeltag', 'label_class', 'label_id'
			- fix: empty <ul></ul> output for section-less submenus
	0.9.2	- fix: static sections (sed_section_fields) visible to non-Publisher users in Write tab (thanks Didjee)
			- fix: sections with clones wrongly indented in Write tab section popup
	0.9.1	- fix: accented characters getting escaped in admin (for jpdupont)
			- enhancement: new 'escape' attribute option: htmlspecial
	0.9		- new adi_menu attribute: 'sub_menu_level' (for emerald)
			- fix: selected section in Write tab changes to first section in list after saving new article
	0.8.1	- fix: submenus not being generated properly
			- fix: suppressed output of 'id=""' & 'class=""' in top level <ul> when menu_id="" & class=""
	0.8		- enhancement: section hierarchy in Write tab section popup
			- new adi_menu attributes: 'first_class' & 'last_class' (for renobird)
			- new adi_menu attributes: 'list_prefix' & 'prefix_class' (for nubian)
			- new adi_menu attribute: suppress_url (for renobird)
			- enhancement: adi_menu & adi_menu_breadcrumb tags now output error message if adi_menu not installed
			- fix: MySQL v4 errors in adi_menu admin (for debegray)
				- used 'TINYINT(1) DEFAULT 0' instead of 'BOOLEAN DEFAULT FALSE'
				- where clause '1=1' instead of 'TRUE'
			- fix: loop detection failure with A->B->C->B sub-loops
			- optimisation: removed section_list['name'] & $hierarchy['name'] array elements
			- articles enhancement (beta) new attributes: 'article_class', 'article_position', 'article_sort'
	0.7		- new adi_menu & adi_menu_breadcrumb attribute: 'escape'
			- new adi_menu attribute: 'active_ancestors' (for Didjee)
			- fix: adi_menu admin hierarchy summary now shows children again
			- fix: no longer get double row of admin page tabs before adi_menu installed
	0.6		- enhancement: article capability added
			- new adi_menu attributes: 'articles', 'article_attr', 'article_include', 'article_exclude'
			- new adi_menu attributes: 'include_children', 'include_current' & 'active_parent' (for macTigers)
			- new adi_menu attribute: 'active_articles_only' (for FireFusion)
			- new adi_menu attribute: 'list_span' (sIFR support, for Lasse Gyrn)
			- code optimisations (thanks to net-carver)
	0.5		- fix: adi_menu errors if 'default' section excluded (thanks Uli)
			- fix: adi_menu tab section hierarchy would generate lots of errors if adi_menu not installed
			- enhancement: sections attribute functionality improved (for Si & freischwimmen)
				- children now output automatically
				- new tags 'include_parent' & 'include_childless'
	0.4		- enhancement: adi_breadcrumb tag attribute 'link_last' - last section crumb in list displayed in plain text (now the default behaviour)
			- fix: adi_menu_breadcrumb error when visiting sections/pages that don't exist when in clean URL mode
			- fix: adi_menu_breadcrumb displayed default section as link regardless of 'link' attribute setting
	0.3		- enhancement: new adi_menu tag attribute 'link_span' - wrap <span>...</span> around contents of links
			- enhancement: new adi_menu tag attributes 'list_id' & 'list_id_prefix' - output unique IDs to <li> elements
			- enhancement: new adi_menu tag attribute 'active_li_class' - output class on active <li>
			- enhancement: adi_menu admin now displays summary of configured section hierarchy
			- modification: adi_breadcrumb tag attribute 'sep' deprecated for 'separator'
	0.2		- fix: adi_menu_breadcrumb error when visiting pages that are excluded in adi_menu admin
			- fix: adi_menu_breadcrumb now copes with section loops, error message output
			- fix: adi_menu tag can now be used more than once on a page
			- enhancement: adi_menu admin section loop warning message
			- enhancement: adi_menu admin now displays sections in alphabetical order
	0.1		- initial release

*/

global $adi_menu_debug,$adi_menu_db_debug,$adi_menu_article_form,$adi_menu_sql_fields;

$adi_menu_article_form = 'adi_menu_articles'; //  for old article mode
$adi_menu_sql_fields = 'name,title,adi_menu_parent,adi_menu_title,adi_menu_exclude,adi_menu_clone,adi_menu_sort,adi_menu_redirect_section,adi_menu_redirect_link,adi_menu_alt_title';

if (@txpinterface == 'admin') {

	$adi_menu_debug = 0; // display admin debug info
	$adi_menu_db_debug = 0; // display database debug info

	// using plugin options/lifecycle/pluggable_ui (4.2.0), so say toodle-oo if the need arises
	if (!version_compare(txp_version,'4.2.0','>=')) return;

	adi_menu_init();
}

function adi_menu_init() {
// general admin setup
	global $prefs,$event,$adi_menu_gtxt,$adi_menu_url,$adi_menu_prefs,$adi_menu_debug,$adi_menu_db_debug,$adi_menu_sed_sf_installed,$adi_menu_cnk_st_installed,$adi_menu_txp450;

	$adi_menu_txp450 = (version_compare(txp_version,'4.4.1','>'));

	$adi_menu_installed = adi_menu_installed();

# --- BEGIN PLUGIN TEXTPACK ---
	$adi_menu_gtxt = array(
		'adi_alt_title' => 'Alternative title',
		'adi_clone' => 'Clone',
		'adi_clone_title' => 'Clone title',
		'adi_install_fail' => 'Unable to install',
		'adi_installed' => 'Installed',
		'adi_menu_loop_warning' => 'Parent/child loops found',
		'adi_menu_parent_warning' => 'Sections with non-existant parents',
		'adi_menu_redirect_link_warning' => 'Sections redirected to non-existant links',
		'adi_menu_redirect_section_warning' => 'Sections redirected to non-existant sections',
		'adi_menu_summary_note' => 'The above configuration will generate the following section hierarchy',
		'adi_menu_summary_note_key' => 'sections in <b>bold</b> are redirected, sections in <i>italics</i> have alternative titles',
		'adi_menu_summary_footnote' => 'The menu may be modified further using adi_menu tag attributes',
		'adi_menu_update_fail' => 'Menu update failed',
		'adi_menu_updated' => 'Menu updated',
		'adi_not_installed' => 'Not installed',
		'adi_redirect_section' => 'Redirect section',
		'adi_redirect_link_id' => 'Redirect link ID',
		'adi_summary' => 'Summary',
		'adi_textpack_fail' => 'Textpack installation failed',
		'adi_textpack_feedback' => 'Textpack feedback',
		'adi_textpack_online' => 'Textpack also available online',
		'adi_uninstall' => 'Uninstall',
		'adi_uninstall_fail' => 'Unable to uninstall',
		'adi_uninstalled' => 'Uninstalled',
		'adi_update_menu' => 'Update menu',
		'adi_update_prefs' => 'Update preferences',
		'adi_write_tab_select_format' => 'Write tab section list format',
		'adi_write_tab_select_indent' => 'Write tab section list indent',
	);
# --- END PLUGIN TEXTPACK ---

	// Textpack
	$adi_menu_url = array(
		'textpack' => 'http://www.greatoceanmedia.com.au/files/adi_textpack.txt',
		'textpack_download' => 'http://www.greatoceanmedia.com.au/textpack/download',
		'textpack_feedback' => 'http://www.greatoceanmedia.com.au/textpack/?plugin=adi_menu',
	);
	if (strpos($prefs['plugin_cache_dir'],'adi') !== FALSE) // use Adi's local version
		$adi_menu_url['textpack'] = $prefs['plugin_cache_dir'].'/adi_textpack.txt';

	// plugin lifecycle
	register_callback('adi_menu_lifecycle','plugin_lifecycle.adi_menu');

	// set the privilege levels
	add_privs('adi_menu_admin','1,2,3,6');

	// adi_menu admin tab under 'Presentation'
	register_tab('presentation','adi_menu_admin','Menu');
	register_callback('adi_menu_admin','adi_menu_admin');
	if ($adi_menu_installed)
		register_callback('adi_menu_article_tab','article_ui','section');

	// default plugin preference settings
	$adi_menu_prefs = array(
		'write_tab_select_indent'	=> '0', // or 0
		'write_tab_select_format'	=> 'name', // or 'title'
		'write_tab_select_default'	=> '0', // or 1
	);

	if ($adi_menu_installed) {
		// check out other plugins & their versions
		$adi_menu_sed_sf_installed = safe_row("version","txp_plugin","status = 1 AND name='sed_section_fields'",$adi_menu_db_debug);
		$adi_menu_cnk_st_installed = safe_row("version","txp_plugin","status = 1 AND name='cnk_section_tree'",$adi_menu_db_debug);
	}

	// plugin options
	$adi_menu_plugin_status = fetch('status','txp_plugin','name','adi_menu',$adi_menu_db_debug);
	if ($adi_menu_plugin_status) { // proper install - options under Plugins tab
		add_privs('plugin_prefs.adi_menu'); // defaults to priv '1' only
		register_callback('adi_menu_options','plugin_prefs.adi_menu');
	}
	else { // txpdev - options under Extensions tab
		add_privs('adi_menu_options');
		register_tab('extensions','adi_menu_options','adi_menu options');
		register_callback('adi_menu_options','adi_menu_options');
	}

	// style
	if ($event == 'adi_menu_admin')
		register_callback('adi_menu_style','admin_side','head_end');
}

function adi_menu_admin($event, $step) {
// adi_menu admin action!
	global $prefs,$adi_menu_sed_sf_installed,$adi_menu_cnk_st_installed,$adi_menu_prefs,$adi_menu_debug,$adi_menu_db_debug,$adi_menu_gtxt;

	$installed = adi_menu_installed();

	$something = gps("something");
	$res = FALSE;

	if ($installed) {
		if ($step == "pref_update") {
			foreach ($adi_menu_prefs as $name => $value) {
				if ($adi_menu_debug)
					echo $name.'='.ps($name).' ';
				adi_menu_prefs($name,doStripTags(ps($name)));
			}
		   	pagetop("adi_menu admin",gTxt('preferences_saved'));
		}
		else if ($step == "update") {
	   		pagetop("adi_menu admin",adi_menu_gtxt('adi_menu_updated'));
			if ($adi_menu_debug) {
				echo "<br/>Parent: ";
				print_r(ps('parent'));
				echo "<br/>Exclude: ";
				print_r(ps('exclude'));
				echo "<br/>Clone: ";
				print_r(ps('clone'));
				echo "<br/>Clone title: ";
				print_r(ps('custom_clone_title'));
				echo "<br/>Sort: ";
				print_r(ps('sort'));
				echo "<br/>Redirect section: ";
				print_r(ps('redirect_section'));
				echo "<br/>Redirect link: ";
				print_r(ps('redirect_link'));
				echo "<br/>Alt title: ";
				print_r(ps('alt_title'));
			}
			$parent = doStripTags(ps('parent'));
			$custom_clone_title = doStripTags(ps('custom_clone_title'));
			$exclude = doStripTags(ps('exclude'));
			$clone = doStripTags(ps('clone'));
			$sort = doStripTags(ps('sort'));
			$redirect_section = doStripTags(ps('redirect_section'));
			$redirect_link = doStripTags(ps('redirect_link'));
			$alt_title = doStripTags(ps('alt_title'));
			$sections = adi_menu_get_sections();
			adi_menu_update($sections,$parent,$custom_clone_title,$exclude,$clone,$sort,$redirect_section,$redirect_link,$alt_title);
		}
		else // do nothing
		   	pagetop('adi_menu admin');
	}
	else { // not installed
			pagetop("adi_menu admin",array(adi_menu_gtxt('adi_not_installed'),E_ERROR));
	}

	if ($installed) {
		adi_menu_upgrade(); // txpdev
		// get to work
		$db_sections = adi_menu_get_sections();
		if ($adi_menu_debug) {
			echo 'VERSIONS:';
			echo '<pre>';
			$version = safe_field("version", "txp_plugin", "name='adi_menu'");
			echo 'adi_menu: '.(empty($version)?'not installed':$version.' installed').'<br/>';
			echo 'TXP: '.$prefs['version'].'<br/>';
			echo 'PHP: '.phpversion().'<br/>';
			echo 'MySQL: '.mysql_get_server_info().'<br/>';
			echo '</pre><hr/>';
			echo 'DATABASE:';
			dmp($db_sections);
			echo '<hr/>';
			echo 'SECTION LEVELS:';
			global $sort;
			$section_list = adi_menu_section_list('',TRUE);
			$hierarchy = adi_menu_hierarchy($section_list,'',0);
			$section_levels = adi_menu_section_levels($hierarchy,1); // top level = 1, etc
			dmp($section_levels);
			echo '<hr/>';
			if ($adi_menu_sed_sf_installed) {
				print 'sed_section_fields v'.$adi_menu_sed_sf_installed['version'].' is installed & active';
				echo '<hr/>';
			}
		}

		// sanity checks
		// check for section loops
		$out = "";
		foreach ($db_sections as $index => $section)
			if (adi_menu_loop_check($db_sections,$section['adi_menu_parent'],array()))
				$out .= " ".$index;
		if ($out)
			echo tag('** '.adi_menu_gtxt('adi_menu_loop_warning').': '.$out.' **','p',' class="adi_menu_warning warning"');
		// check for missing parents
		$missing_parents = adi_menu_parent_check($db_sections);
		$out = "";
		foreach ($missing_parents as $section => $parent)
			$out .= $section.' ('.$parent.') ';
		if ($out)
			echo tag('** '.adi_menu_gtxt('adi_menu_parent_warning').': '.$out.'**','p',' class="adi_menu_warning warning"');
		// check for missing redirect sections
		$missing_sections = adi_menu_redirect_section_check($db_sections);
		$out = "";
		foreach ($missing_sections as $section => $redirect_section)
			$out .= $section.' ('.$redirect_section.') ';
		if ($out)
			echo tag('** '.adi_menu_gtxt('adi_menu_redirect_section_warning').': '.$out.'**','p',' class="adi_menu_warning warning"');
		// check for missing redirect links
		$missing_links = adi_menu_redirect_link_check($db_sections);
		$out = "";
		foreach ($missing_links as $section => $link)
			$out .= $section.' ('.$link.') ';
		if ($out)
			echo tag('** '.adi_menu_gtxt('adi_menu_redirect_link_warning').': '.$out.'**','p',' class="adi_menu_warning warning"');

		// output adi_menu settings table
		echo form(
			startTable('list','',"edit-table txp-list").
			tr(
				hcell(gTxt('section')).
				hcell(gTxt('title')).
				hcell(adi_menu_gtxt('adi_alt_title')).
				hcell(gTxt('exclude')).
				hcell(gTxt('parent')).
				hcell(gTxt('sort_value')).
				hcell(adi_menu_gtxt('adi_clone')).
				hcell(adi_menu_gtxt('adi_clone_title')).
				hcell(adi_menu_gtxt('adi_redirect_section')).
				hcell(adi_menu_gtxt('adi_redirect_link_id'))
			).
			adi_menu_display_settings($db_sections).
			endTable().
			tag(
				fInput("submit","update",adi_menu_gtxt('adi_update_menu'),"smallerbox").
				eInput("adi_menu_admin").sInput("update"),
				'div'
			)
			,''
			,''
			,'post'
			,'adi_menu_form'
		);
	}

	// output hierarchy summary
	global $sections,$exclude,$sort,$default_first,$include_children,$default_title,$menu_id,$escape,$clone_title,$parent_class;
	if ($installed) {
		$sections=$exclude="";
		$sort="adi_menu_sort";
		$default_first="1";
		$include_children="1";
		$menu_id = "mainmenu";
		$escape = '';
		$default_title = 'Home';
		$clone_title = 'Summary';
		$parent_class = 'menuparent';
		$section_list = adi_menu_section_list();
		$hierarchy = adi_menu_hierarchy($section_list,'',0);
		$out = adi_menu_markup($hierarchy,0);
		echo '<div id="adi_menu_summary">';
		echo tag(adi_menu_gtxt('adi_summary'),'h2');
		$summary_note = ' ('.adi_menu_gtxt('adi_menu_summary_note_key').')';
		echo tag(adi_menu_gtxt('adi_menu_summary_note').$summary_note.'.','p');
		echo tag(adi_menu_gtxt('adi_menu_summary_footnote').'.',"p");
		echo join($out);
		echo '</div>';
	}

	// plugin preferences form
	if ($installed)
	    echo form(
			tag(gTxt('edit_preferences'),"h2")
			.graf(
				adi_menu_gtxt('adi_write_tab_select_format').':&nbsp;&nbsp;&nbsp;'.
				tag(gTxt('name'),'label').' '.
				radio('write_tab_select_format','name',(adi_menu_prefs('write_tab_select_format') == 'name')).'&nbsp;&nbsp;'.
				tag(gTxt('title'),'label').' '.
				radio('write_tab_select_format','title',(adi_menu_prefs('write_tab_select_format') == 'title')).'&nbsp;&nbsp;'
			)
			.graf(
				adi_menu_gtxt('adi_write_tab_select_indent').':&nbsp;&nbsp;&nbsp;'.
				tag(gTxt('yes'),'label').' '.
				radio('write_tab_select_indent','1',(adi_menu_prefs('write_tab_select_indent') == '1')).'&nbsp;&nbsp;'.
				tag(gTxt('no'),'label').' '.
				radio('write_tab_select_indent','0',(adi_menu_prefs('write_tab_select_indent') == '0')).'&nbsp;&nbsp;'
			)
	        .fInput("submit", "do_something",adi_menu_gtxt('adi_update_prefs'), "smallerbox","",'')
	        .eInput("adi_menu_admin").sInput("pref_update")
			,'','','post','adi_menu_form'
		);

	if ($adi_menu_debug) {
		echo "<hr/>Event: ".$event."<br/>Step: ".$step."<br/>Something: ".$something.'<hr/>';
		echo 'PREFS:<br/>'; // should create list automatically
		foreach ($adi_menu_prefs as $name => $value)
			echo $name.': '.adi_menu_prefs($name).'<br/>';
		echo '<hr/>';
	}
}

function adi_menu_options($event,$step) {
// plugin options page
	global $adi_menu_debug,$adi_menu_url,$adi_menu_plugin_status;

	$message = '';

//	$installed = adi_menu_installed();
//	if ($installed)
//		adi_menu_upgrade();

	// dance steps
	if ($step == 'textpack') {
		if (function_exists('install_textpack')) {
			$adi_textpack = file_get_contents($adi_menu_url['textpack']);
			if ($adi_textpack) {
				$result = install_textpack($adi_textpack);
				$message = gTxt('textpack_strings_installed', array('{count}' => $result));
				$textarray = load_lang(LANG); // load in new strings
			}
			else
				$message = array(adi_menu_gtxt('adi_textpack_fail'),E_ERROR);
		}
	}
	else if ($step == 'install') {
		$result = adi_menu_install();
		$result ? $message = adi_menu_gtxt('adi_installed') : $message = array(adi_menu_gtxt('adi_install_fail'),E_ERROR);
	}
	else if ($step == 'uninstall') {
		$result = adi_menu_uninstall();
		$result ? $message = adi_menu_gtxt('adi_uninstalled') : $message = array(adi_menu_gtxt('adi_uninstall_fail'),E_ERROR);
	}

	// generate page
	pagetop('adi_menu - '.gTxt('plugin_prefs'),$message);

	$install_button =
		tag(
			form(
				fInput("submit", "do_something", gTxt('install'), "publish","",'return verify(\''.gTxt('are_you_sure').'\')')
				.eInput($event).sInput("install")
				,'','','post','adi_menu_nstall_button'
			)
			,'div'
			,' style="text-align:center"'
		);
	$uninstall_button =
		tag(
	    	form(
				fInput("submit", "do_something", adi_menu_gtxt('adi_uninstall'), "publish","",'return verify(\''.gTxt('are_you_sure').'\')')
				.eInput($event).sInput("uninstall")
				,'','','post','adi_menu_nstall_button adi_menu_uninstall_button'
			)
			,'div'
			,' style="margin-top:5em"');

	if ($adi_menu_plugin_status) // proper plugin install, so lifecycle takes care of install/uninstall
		$install_button = $uninstall_button = '';

	$installed = adi_menu_installed();
	if ($installed) {
		adi_menu_upgrade();
		// options
		echo tag(
			tag('adi_menu '.gTxt('plugin_prefs'),'h2')
			// textpack links
			.graf(href(gTxt('install_textpack'),'?event='.$event.'&amp;step=textpack'))
			.graf(href(adi_menu_gtxt('adi_textpack_online'),$adi_menu_url['textpack_download']))
			.graf(href(adi_menu_gtxt('adi_textpack_feedback'),$adi_menu_url['textpack_feedback']))
			.$uninstall_button
			,'div'
			,' style="text-align:center"'
		);
	}
	else // install button
	    echo $install_button;

	if ($adi_menu_debug) {
		echo "<p><b>Event:</b> ".$event.", <b>Step:</b> ".$step."</p>";
		echo '<b>$adi_textpack ('.$adi_menu_url['textpack'].'):</b>';
		$adi_textpack = file_get_contents($adi_menu_url['textpack']);
		dmp($adi_textpack);
	}

}

function adi_menu_gtxt($phrase,$atts=array()) {
// will check installed language strings before embedded English strings - to pick up Textpack
// - for TXP standard strings gTxt() & adi_menu_gtxt() are functionally equivalent
	global $adi_menu_gtxt;

	if (gTxt($phrase, $atts) == $phrase) // no TXP translation found
		if (array_key_exists($phrase,$adi_menu_gtxt)) // adi translation found
			return $adi_menu_gtxt[$phrase];
		else // last resort
			return $phrase;
	else // TXP translation
		return gTxt($phrase,$atts);
}

function adi_menu_style() {
// some style for the admin page
	echo
		'<style type="text/css">
			h2 { font-weight:bold }
			#adi_menu_summary { margin:2em 10em 2em; padding:1em 0; border:solid #c0c0c0; border-width:1px 0 }
			#adi_menu_summary ul li li { margin-left:2em }
			#adi_menu_summary .menu_redirect > a { font-weight:bold }
			#adi_menu_summary .menu_alt_title > a { font-style:italic }
			.adi_menu_warning { margin:1em; text-align:center; font-weight:bold }
			.adi_menu_form { margin-top:2em; text-align:center }
			.adi_menu_form div { margin-top:2em }
		</style>';
}

function adi_menu_column_found($column) {
// check for presence of a column in txp_section table
	global $adi_menu_db_debug;

	$rs = safe_query('SELECT * FROM '.safe_pfx('txp_section'),$adi_menu_db_debug);
	$a = nextRow($rs);
	return array_key_exists($column, $a);
}

function adi_menu_installed() {
// if 'adi_menu_parent' column present then assume adi_menu is installed
	return(adi_menu_column_found('adi_menu_parent'));
}

function adi_menu_truncate_text($text,$limit) {
// not currently used
	$words = explode(" ", $text);
	$truncated_text = '';
	foreach ($words as $index => $this_word) {
		$truncated_text .= $this_word.' ';
		if (strlen($truncated_text) >= $limit)
			break;
	}
	$truncated_text = trim($truncated_text);
	if ($truncated_text != $text)
		$truncated_text .= ' ...';
	return $truncated_text;
}

function adi_menu_display_settings($sections) {
// generate section's table row in admin settings table
	global $prefs,$adi_menu_txp450;

	$out = '';
	foreach ($sections as $index => $section) {
		$title = $section['title'];
		$parent = $section['adi_menu_parent'];
		$custom_clone_title = $section['adi_menu_title'];
		$exclude = $section['adi_menu_exclude'];
		$clone = $section['adi_menu_clone'];
		$sort = $section['adi_menu_sort'];
		$redirect_section = $section['adi_menu_redirect_section'];
		$redirect_link = $section['adi_menu_redirect_link'];
		$alt_title = $section['adi_menu_alt_title'];
		$out .= tr(
			// section name & link to section tab
			($adi_menu_txp450 ?
				tda('<a href="http://'.$prefs['siteurl'].'/textpattern/?event=section&amp;step=section_edit&amp;name='.$index.'">'.$index.'</a>') :
				tda('<a href="http://'.$prefs['siteurl'].'/textpattern/?event=section#section-'.$index.'">'.$index.'</a>')
			)
//			.tda(htmlspecialchars(adi_menu_truncate_text($title,12)))
			.tda(htmlspecialchars($title))
			.tda(finput("text","alt_title[$index]",$alt_title))
			.tda(checkbox("exclude[$index]", "1", $exclude))
			.tda(adi_menu_section_popup("parent[$index]",$parent))
			.tda(finput("text","sort[$index]",$sort,'','','',4))
			.tda(checkbox("clone[$index]", "1", $clone))
			.tda(finput("text","custom_clone_title[$index]",$custom_clone_title))
			.tda(adi_menu_section_popup("redirect_section[$index]",$redirect_section))
			.tda(adi_menu_link_popup("redirect_link[$index]",$redirect_link))
		);
	}
	return $out;
}

function adi_menu_section_popup($select_name,$value) {
// generate section popup list for admin settings table
// where 'TRUE' not supported on MySQL 4.0.27 (OK in MySQL 5+), so use 1=1
	$rs = safe_column('name', 'txp_section', '1=1');
	if ($rs)
		return selectInput($select_name, $rs, $value, TRUE);
	return false;
}

function adi_menu_link_popup($select_name,$value) {
// generate link popup list for admin settings table
	$rs = safe_column('id', 'txp_link', '1=1');
	if ($rs)
		return selectInput($select_name, $rs, $value, TRUE);
	return false;
}

function adi_menu_update($sections,$parent,$custom_clone_title,$exclude,$clone,$sort,$redirect_section,$redirect_link,$alt_title) {
// update txp_section table in database
	global $adi_menu_db_debug;

	foreach ($sections as $index => $section) {
		$where = 'name="'.$index.'"';
		$set = 'adi_menu_parent="'.doSlash($parent[$index]).'"';
		safe_update('txp_section',$set,$where,$adi_menu_db_debug);
		$set = 'adi_menu_title="'.doSlash($custom_clone_title[$index]).'"';
		safe_update('txp_section',$set,$where,$adi_menu_db_debug);
		empty($exclude[$index]) ? $set = 'adi_menu_exclude="0"' : $set = 'adi_menu_exclude="1"';
		safe_update('txp_section',$set,$where,$adi_menu_db_debug);
		empty($clone[$index]) ? $set = 'adi_menu_clone="0"' : $set = 'adi_menu_clone="1"';
		safe_update("txp_section",$set,$where,$adi_menu_db_debug);
		$set = 'adi_menu_sort="'.doSlash($sort[$index]).'"';
		safe_update('txp_section',$set,$where,$adi_menu_db_debug);
		$set = 'adi_menu_alt_title="'.doSlash($alt_title[$index]).'"';
		safe_update('txp_section',$set,$where,$adi_menu_db_debug);
		$set = 'adi_menu_redirect_section="'.doSlash($redirect_section[$index]).'"';
		safe_update('txp_section',$set,$where,$adi_menu_db_debug);
		if (!empty($redirect_link)) { // there might not be any links defined in the TXP database!
			$set = 'adi_menu_redirect_link="'.doSlash($redirect_link[$index]).'"';
			safe_update('txp_section',$set,$where,$adi_menu_db_debug);
		}
	}
}

function adi_menu_install() {
// add adi_menu's columns to txp_section table
// note: TINYINT(1) DEFAULT 0 = BOOLEAN DEFAULT FALSE
	global $adi_menu_db_debug;

	if (adi_menu_installed())
		return TRUE;
	else
		return safe_query("ALTER TABLE ".safe_pfx("txp_section")." ADD adi_menu_parent VARCHAR(128) DEFAULT '';",$adi_menu_db_debug)
			&& safe_query("ALTER TABLE ".safe_pfx("txp_section")." ADD adi_menu_title VARCHAR(128) DEFAULT '';",$adi_menu_db_debug)
			&& safe_query("ALTER TABLE ".safe_pfx("txp_section")." ADD adi_menu_exclude TINYINT(1) DEFAULT 0 NOT NULL;",$adi_menu_db_debug)
			&& safe_query("ALTER TABLE ".safe_pfx("txp_section")." ADD adi_menu_clone TINYINT(1) DEFAULT 0 NOT NULL;",$adi_menu_db_debug)
			&& safe_query("ALTER TABLE ".safe_pfx("txp_section")." ADD adi_menu_sort TINYINT(3) UNSIGNED DEFAULT 0 NOT NULL;",$adi_menu_db_debug);
}

function adi_menu_uninstall() {
// remove adi_menu's columns from txp_section table
	global $adi_menu_db_debug;

	// remove traditional columns
	$res = safe_query("ALTER TABLE ".safe_pfx("txp_section")." DROP COLUMN adi_menu_parent;",$adi_menu_db_debug)
		&& safe_query("ALTER TABLE ".safe_pfx("txp_section")." DROP COLUMN adi_menu_title;",$adi_menu_db_debug)
		&& safe_query("ALTER TABLE ".safe_pfx("txp_section")." DROP COLUMN adi_menu_exclude;",$adi_menu_db_debug)
		&& safe_query("ALTER TABLE ".safe_pfx("txp_section")." DROP COLUMN adi_menu_clone;",$adi_menu_db_debug)
		&& safe_query("ALTER TABLE ".safe_pfx("txp_section")." DROP COLUMN adi_menu_sort;",$adi_menu_db_debug);
	if (adi_menu_column_found('adi_menu_redirect_section')) // remove version 1.0 columns
		$res = $res
			&& safe_query("ALTER TABLE ".safe_pfx("txp_section")." DROP COLUMN adi_menu_redirect_section;",$adi_menu_db_debug)
			&& safe_query("ALTER TABLE ".safe_pfx("txp_section")." DROP COLUMN adi_menu_redirect_link;",$adi_menu_db_debug);
	if (adi_menu_column_found('adi_menu_accesskey')) // remove version 1.0beta only columns
		$res = $res
			&& safe_query("ALTER TABLE ".safe_pfx("txp_section")." DROP COLUMN adi_menu_accesskey;",$adi_menu_db_debug)
			&& safe_query("ALTER TABLE ".safe_pfx("txp_section")." DROP COLUMN adi_menu_tabindex;",$adi_menu_db_debug);
	if (adi_menu_column_found('adi_menu_alt_title')) // remove version 1.1 columns
		$res = $res && safe_query("ALTER TABLE ".safe_pfx("txp_section")." DROP COLUMN adi_menu_alt_title;",$adi_menu_db_debug);
	// delete preferences
	$res = $res && safe_delete('txp_prefs',"name LIKE 'adi_menu_%'",$adi_menu_db_debug);
	return $res;
}

function adi_menu_upgrade() {
// add additional adi_menu columns to txp_section table, if required
	global $adi_menu_db_debug;

	// record the number of 'default' articles
	$rs = safe_rows('id','textpattern',"section='default'");
	adi_menu_prefs('write_tab_select_default',count($rs));
	// upgrade actions
	$res = TRUE;
	if (!adi_menu_column_found('adi_menu_redirect_section')) // add version 1.0 columns
		$res = safe_query("ALTER TABLE ".safe_pfx("txp_section")." ADD adi_menu_redirect_section VARCHAR(128) DEFAULT '';",$adi_menu_db_debug)
			&& safe_query("ALTER TABLE ".safe_pfx("txp_section")." ADD adi_menu_redirect_link TINYINT(3) UNSIGNED DEFAULT 0 NOT NULL;",$adi_menu_db_debug);
	if (!adi_menu_column_found('adi_menu_alt_title')) // add version 1.1 columns
		$res = $res && safe_query("ALTER TABLE ".safe_pfx("txp_section")." ADD adi_menu_alt_title VARCHAR(128) DEFAULT '';",$adi_menu_db_debug);
	return $res;
}

function adi_menu_lifecycle($event,$step) {
// a matter of life & death
// $event:	"plugin_lifecycle.adi_menu"
// $step:	"installed", "enabled", "disabled", "deleted"
// TXP 4.5: reinstall/upgrade only triggers "installed" event (now have to manually detect whether upgrade required)
	global $adi_menu_debug,$adi_menu_txp450;

	$result = '?';
	// set upgrade flag if upgrading/reinstalling in TXP 4.5+
	$upgrade = (($step == "installed") && $adi_menu_txp450 && adi_menu_installed());
	if ($step == 'enabled') {
			$result = $upgrade = adi_menu_install();
//		if (adi_menu_installed())
//			$result = $upgrade = adi_menu_upgrade();
//		else { // install, then upgrade
//			if (adi_menu_install())
//				$result = adi_menu_upgrade();
//		}
	}
	else if ($step == 'deleted')
		$result = adi_menu_uninstall();
	if ($upgrade)
		$result = $result && adi_menu_upgrade();
	if ($adi_menu_debug)
		echo "Event=$event Step=$step Result=$result Upgrade=$upgrade";
}

function adi_menu_add_form() {
// add adi_menu's article form for old article mode
	global $adi_menu_article_form;

	if (!safe_field('name', 'txp_form', "name='".$adi_menu_article_form."'")) {
		$form = <<<EOF
<li class="menu_article"><txp:permlink><txp:title/></txp:permlink></li>
EOF;
		safe_insert('txp_form',
			"name='".$adi_menu_article_form."',
			type='article',
			Form='".doSlash($form)."'");
	}
}

function adi_menu_prefs($name,$new_value=NULL) {
// set/read preferences
	global $prefs,$adi_menu_prefs;

	if ($new_value !== NULL) { // save new value
		set_pref('adi_menu_'.$name,$new_value,'adi_menu_admin',2);
		$prefs = get_prefs(); // re-sample $prefs
	}
	// take value from $prefs or, if not set, from $adi_menu_prefs[])
	isset($prefs['adi_menu_'.$name]) ? $value = $prefs['adi_menu_'.$name] : $value = $adi_menu_prefs[$name];
	return $value;
}

function adi_menu_get_sections() {
// get section information from txp_section table in database
	global $adi_menu_sql_fields;

	$sql_tables = safe_pfx('txp_section');
	$rs = safe_query("SELECT ".$adi_menu_sql_fields." FROM ".$sql_tables." ORDER BY name");
	while ($a = nextRow($rs)) {
		extract($a); // set 'name','title','parent' etc in $a
		$out[$name] = $a;
	}
	return $out;
}

function adi_menu_loop_check($section_list,$parent,$ancestors) {
// check for section parent/child loops
	if (empty($parent)) // no more ancestors
		return FALSE;
	else {
		if (array_key_exists($parent,$ancestors)) // loop found
			return TRUE;
		else {
			$ancestors[$parent]=''; // add parent to list of ancestors
			if (array_key_exists($parent,$section_list)) // check that parent exists (section rename issue)
				return adi_menu_loop_check($section_list,$section_list[$parent]['adi_menu_parent'],$ancestors);
			else
				return FALSE;
		}
	}
}

function adi_menu_parent_check($db_sections) {
// check for missing parents or link IDs
	$missing_parents = array();
	foreach($db_sections as $section => $value) {
		$parent = $value['adi_menu_parent'];
		if (!empty($parent))
			if (!array_key_exists($parent,$db_sections)) // check that parent exists (section deleted/renamed?)
				$missing_parents[$section] = $parent;
	}
	return $missing_parents;
}

function adi_menu_redirect_section_check($db_sections) {
// check section redirects
	$missing_sections = array();
	foreach($db_sections as $section => $value) {
		$redirect_section = $value['adi_menu_redirect_section'];
		if (!empty($redirect_section))
			if (!array_key_exists($redirect_section,$db_sections)) // check that redirect section exists (section deleted/renamed?)
				$missing_sections[$section] = $redirect_section;
	}
	return $missing_sections;
}

function adi_menu_redirect_link_check($db_sections) {
// check link redirects
	$missing_links = array();
	foreach($db_sections as $section => $value) {
		$link = $value['adi_menu_redirect_link'];
		if ($link)
			if (!adi_menu_get_link($link)) // check that link exists (link deleted?)
				$missing_links[$section] = $link;
	}
	return $missing_links;
}

function adi_menu_get_link($link_id) {
// read link from TXP database
	$url = '';
	if ($link_id) {
		$sql_fields = 'url';
		$sql_table = 'txp_link';
		$sql_where = 'id='.$link_id;
		$a = safe_row($sql_fields,$sql_table,$sql_where);
		if ($a) // check if link ID actually exists
			$url = $a['url'];
		else
			$url = '';
	}
	return $url;
}

function adi_menu_section_list($exclude_list='',$ignore_exclude_field=FALSE) {
// create list of required sections
	global $sort,$default_first,$default_title,$sections,$adi_menu_sql_fields;

	if (empty($sort)) $sort = 'name'; // default sort method
	if ($sections) { // explicit list of included sections which should override admin excluded sections
		$include_option = do_list($sections);
		$include_option = join("','", doSlash($include_option));
		$include_option = "OR name IN ('$include_option')";
	}
	else
		$include_option = '';
	if ($exclude_list) {
		$exclude_list = do_list($exclude_list);
		$exclude_list = join("','", doSlash($exclude_list));
		$exclude_list = "AND name NOT IN ('$exclude_list')";
	}
	$ignore_exclude_field ?
		$exclude_option = "1=1" : // dummy/filler WHERE condition
		$exclude_option = "adi_menu_exclude = 0";
	if ($include_option)
		$exclude_option = '('.$exclude_option.' '.$include_option.')';
	$rs = safe_rows_start($adi_menu_sql_fields, 'txp_section', "$exclude_option $exclude_list order by ".$sort);
	if ($rs) {
		$section_list = array();
		while ($a = nextRow($rs)) {
			extract($a); // sets 'name','title','adi_menu_parent' etc in $a
			// create URL & add it to $a, noting any redirection along the way
			if ($a['adi_menu_redirect_section'])
				$a['url'] = pagelinkurl(array('s' => $a['adi_menu_redirect_section']));
			else if ($a['adi_menu_redirect_link'])
					$a['url'] = adi_menu_get_link($a['adi_menu_redirect_link']);
			else
				$a['url'] = pagelinkurl(array('s' => $name));
			unset($a['name']); // remove 'name' element (name used as index in $section_list)
			$section_list[$name] = $a;
		}
		if (array_key_exists('default',$section_list)) { // default section included in menu
			if ($section_list && $default_title) // set default section title
				$section_list['default']['title'] = $default_title;
			if ($section_list && $default_first) { // shift default section to beginning
				$remember['default'] = $section_list['default']; // remember 'default' element
				unset($section_list['default']); // remove 'default' element
				$section_list = array_merge($remember, $section_list); // join together, 'default' now at beginning
			}
		}
		return $section_list;
	}
}


function adi_menu_article_tab($event,$step,$default,$rs) {
// tweak the article tab (section popup list)
	if ((adi_menu_prefs('write_tab_select_format') != 'name') || adi_menu_prefs('write_tab_select_indent')) {
		$pattern = '#name="Section".*</select>#sU';
		$insert = 'adi_menu_article_section_popup';
		$out = preg_replace_callback($pattern, $insert, $default);
		return $out;
	}
	else // don't fiddle with anything
		return $default;
}

function adi_menu_section_indent($level) {
// create indent for section in popup
	$level -= 1;
	$out = '';
	if ($level)
		for ($x=1; $x <= $level; $x++)
			$out .="&nbsp;&nbsp;";
	return $out;
}

function adi_menu_article_section_popup() {
// generate markup for section popup menu for Article/Write tab
	global $sort,$step,$adi_menu_sed_sf_installed;

	$section_list = adi_menu_section_list('',TRUE);
	$hierarchy = adi_menu_hierarchy($section_list,'',0);
	$section_levels = adi_menu_section_levels($hierarchy,1); // top level = 1, etc
	if ($step == 'edit') { // editing existing ($step = GET var) or creating new article ($step = POST var)
		if (!empty($GLOBALS['ID'])) // newly-saved article, get section from POST vars
			$select_section = gps('Section');
		else { // existing article, get ID from GET vars & section from database
			$article_id = gps('ID');
			$select_section = safe_field("section", "textpattern","id=".$article_id);
		}
	}
	else // empty article
		$select_section = getDefaultSection(); // default section for articles (defined in Sections tab)
	$out = 'name="Section" class="list">';
	foreach ($section_levels as $name => $level) { // create indented section popup list
		if ($adi_menu_sed_sf_installed) { // sed_sections_fields installed & active so check if static section
			$data = _sed_sf_get_data($name);
			$data_array = sed_lib_extract_name_value_pairs($data);
			if (isset($data_array['ss']))
				$ss = $data_array['ss']; // 0 or 1 from prefs
			else
				$ss = 0; // not found in prefs
			if ($ss) // static section according to sed_section_fields
				if (!has_privs('sed_sf.static_sections')) // only Publisher should see static sections
					continue; // omit from section select list
		}
		if ($name == 'default') // shouldn't really have 'default' in the list
			if (!adi_menu_prefs('write_tab_select_default')) // only allow it if there're some 'default' articles
				continue;
		strcasecmp($name, $select_section) == 0 ?
			$selected = 'selected="selected"' :
			$selected = '';
		if (adi_menu_prefs('write_tab_select_format') == 'name')
			$display = $name;
		else // must be 'title' then
			$display = $section_list[$name]['title'];
		$out .= '<option value="'.$name.'"'.$selected.'>';
		if (adi_menu_prefs('write_tab_select_indent')) // indent
			$out .= adi_menu_section_indent($level);
		$out .= $display.'</option>';
	}
	$out .= '</select>';
	return $out;
}

function adi_menu_section_levels($hierarchy,$level) {
// create list, indexed by section, of level in hierarchy (top = 1 etc.) - used in admin for popup
	static $section_levels;
	foreach ($hierarchy as $index => $section)
		if (!$section['clone']) { // ignore clones
			$section_levels[$index] = $level; // set level in array, indexed by section name
			adi_menu_section_levels($section['child'],$level+1);
		}
	return $section_levels;
}

function adi_menu_lineage($section_list,$child) {
// determine the ancestry
	global $linkclass,$label,$title,$include_default,$separator,$s,$link,$link_last,$adi_breadcrumb_escape,$adi_menu_lineage_count;
	global $is_article_list; // TXP global variable

	$out = array();
	if (!array_key_exists($child, $section_list)) { // bomb out if section not found
		$out[] = '?';
		return $out;
	}
	if ($s == $child) $adi_menu_lineage_count++;
	if ($adi_menu_lineage_count > 1) { // bomb out if loop found
		$out[] = "Warning, section loop found: ";
		return $out;
	}
	if ($section_list[$child]['adi_menu_parent']) // has parent
		$out = array_merge($out,adi_menu_lineage($section_list,$section_list[$child]['adi_menu_parent']));
	else { // top of the food chain
		if (($include_default) && ($child != 'default')) // if (include default) AND (not at 'default' yet)
			$out = array_merge($out,adi_menu_lineage($section_list,'default')); // do extra, 'default' iteration
		else
			$out[] = $label; // add the "You are here" bit
	}
	$title ? // output section's title or not
		$crumb = $section_list[$child]['title'] :
		$crumb = $child;
	$crumb = adi_menu_htmlentities($crumb,$adi_breadcrumb_escape);
	if (($s == $child) && (!$link_last) && ($is_article_list)) // if (last breadcrumb) AND (link_last=0) AND (not single article), switch off link mode
		$link = FALSE;
	$link ? // output section as a link or not
		$out[] = tag($crumb,'a',' class="'.$linkclass.'" href="'.$section_list[$child]['url'].'"') :
		$out[] = $crumb;
	if ($s != $child) $out[] = $separator; // add separator if not last crumb
	return $out;
}

function adi_menu_breadcrumb($atts) {
// <txp:adi_menu_breadcrumb /> tag
	global $s; // the current section
	global $label,$separator,$sep,$title,$link,$linkclass,$include_default;
	global $sections,$exclude,$sort,$default_first,$default_title,$include_default,$link_last,$adi_breadcrumb_escape,$adi_menu_lineage_count;

	$sections=$exclude="";
	$sort="NULL";
	$default_first=$include_default="1";

	extract(lAtts(array(
		'label'				=> 'You are here: ',	// string to prepend to the output
		'separator'			=> ' &#187; ',			// string to be used as the breadcrumb separator (default: >>)
		'sep'				=> '',					// deprecated - use 'separator'
		'title'				=> '1',					// display section titles or not
		'link'				=> '1',					// output sections as links or not
		'linkclass'			=> 'noline',			// class for breadcrumb links
		'link_last'			=> '0',					// display last section crumb as link or not
		'include_default'	=> '1',					// include 'default' section or not
		'default_title'		=> 'Home',				// title for 'default' section
		'escape'			=> '',					// escape HTML entities in section titles
	), $atts));

	if (!adi_menu_installed()) return "<em>adi_menu not installed!</em>";
	$default_title = trim($default_title);
	if ($sep) $separator = $sep; // deprecated attribute 'sep', use 'separator' instead
	$adi_breadcrumb_escape = $escape;

	/* adi_menu_breadcrumb - main procedure */
	$section_list = adi_menu_section_list('',TRUE);
	$adi_menu_lineage_count = 0; // global variable used instead of static, so that adi_menu_breadcrumb can have multiple instances
	$out = adi_menu_lineage($section_list,$s);
	return join($out);
}

function adi_menu_get_article_attr($article_attr) {
// parse article_attr and return list, indexed by attribute name
// e.g. article_attr='time="any" limit="5"'
// THERE MUST BE A BETTER WAY!
	$attr_list = preg_replace("#([a-z_]+=)#", ",\\1", $article_attr) . ","; // creates e.g. ',time="any",limit="5"'
	$attr_list = trim($attr_list,','); // strip extraneous commas
	$attr_list = explode(',',$attr_list); // convert to array
	$attributes = array();
	foreach ($attr_list as $index => $attr) {
		$a = explode('=',$attr);
		if (count($a) == 2) { // safety valve
			$attribute = trim($a[0]); // remove whitespace
			$value = trim($a[1]); // remove whitespace
			$value = trim($value,'"'); // remove double quotes
			$attributes[$attribute] = $value;
		}
	}
	return $attributes;
}

function adi_menu_get_articles($section_article_list) {
// create list of articles from database, indexed by section & sub-indexed by article id (prefixed with 'article_')
// - sort value taken from article_sort attribute
// - much code taken from publish.php doArticles()
	global $s,$article_list,$article_sort,$article_attr,$active_articles_only,$new_article_mode,$section_article_sort;

	$article_list = array();
	$attributes = adi_menu_get_article_attr($article_attr);
	// create some blanks
	$category=$statusq=$search=$id=$excerpted=$month=$author=$keywords=$custom=$frontpage='';
	// set defaults
	$status = 'live';
	$time = " and Posted <= now()";
	if ($new_article_mode)
		$limit = 9999;
	else
		$limit = 10;
	$offset = 0;
	// analyse article attributes
	foreach ($attributes as $attribute => $value) {
		switch ($attribute) {
			case 'author':
				if ($value)
					$author = (!$value) ? '' : " and AuthorID IN ('".join("','", doSlash(do_list($value)))."')";
				break;
			case 'category':
				if ($value) {
					$category = join("','", doSlash(do_list($value)));
					$category = (!$category)  ? '' : " and (Category1 IN ('".$category."') or Category2 IN ('".$category."'))";
				}
				break;
			case 'excerpted':
				if ($value)
					$excerpted = ($value=='y')  ? " and Excerpt !=''" : '';
				break;
			case 'keywords':
				if ($value) {
					$keys = doSlash(do_list($value));
					foreach ($keys as $key) {
						$keyparts[] = "FIND_IN_SET('".$key."',Keywords)";
					}
					$keywords = " and (" . join(' or ',$keyparts) . ")";
				}
				break;
			case 'limit':
				if ($value)
					$limit = $value;
				break;
			case 'month':
				if ($value)
					$month = (!$value) ? '' : " and Posted like '".doSlash($value)."%'";
				break;
			case 'offset':
				if ($value)
					$offset = $value;
				break;
			case 'sort': // will override article_sort attribute
				if ($value)
					$article_sort = $value;
				break;
			case 'status':
				if ($value) {
					$status = in_array(strtolower($value), array('sticky', '5')) ? 5 : 4;
					$statusq = ' and Status = '.intval($status);
				}
				break;
			case 'time':
				switch ($value) {
					case 'any':
						$time = ""; break;
					case 'future':
						$time = " and Posted > now()"; break;
				}
				break;
		}
	}
	// custom fields
	$customFields = getCustomFields();
	$customlAtts = array_null(array_flip($customFields));
	if ($customFields) {
		foreach($customFields as $cField)
			if (isset($attributes[$cField]))
				$customPairs[$cField] = $attributes[$cField];
		if(!empty($customPairs))
			$custom = buildCustomSql($customFields,$customPairs);
	}
	// retrieve articles from database
	foreach ($section_article_list as $index => $this_section) {
		if ($active_articles_only && !(($this_section == $s))) // then only allow articles if section is active
			continue;
		$section = (!$this_section)   ? '' : " and Section IN ('".join("','", doSlash(do_list($this_section)))."')"; // ?????? DOES IT NEED TO BE THIS COMPLEX ????
		// section-specific article sort
		if (array_key_exists($this_section,$section_article_sort))
			$this_sort = $section_article_sort[$this_section];
		else
			$this_sort = $article_sort;
		// set up WHERE clause
		$where = "1=1" . $statusq . $time . $search . $id . $category . $section . $excerpted . $month . $author . $keywords . $custom . $frontpage;
		$match = '';
		$rs = safe_rows_start("ID,Title,url_title,Posted,Section".$match,'textpattern',
			$where.' order by '.doSlash($this_sort).' limit '.intval($offset).', '.intval($limit));
		while($a = nextRow($rs)) {
			$new_index = 'article_'.$a['ID']; // create unique array index - i.e. for article 23, index is article_23
			$article_list[$this_section][$new_index]['title'] = html_entity_decode($a['Title']); // ampersands are escaped in article titles (but not in section titles)
			$article_list[$this_section][$new_index]['url'] = permlinkurl($a);
			$article_list[$this_section][$new_index]['section'] = FALSE; // TRUE=section, FALSE=article
			$article_list[$this_section][$new_index]['sort'] = rand(); // THIS MAY TAKE SORT NUMBER FROM SOMEWHERE ELSE EVENTUALLY
			$article_list[$this_section][$new_index]['clone'] = FALSE; // dummy filler
			$article_list[$this_section][$new_index]['redirect_section'] = ''; // dummy filler
			$article_list[$this_section][$new_index]['redirect_link'] = ''; // dummy filler
			$article_list[$this_section][$new_index]['alt_title'] = ''; // dummy filler
			$article_list[$this_section][$new_index]['parent'] = ''; // dummy filler
			$article_list[$this_section][$new_index]['child'] = array(); // dummy filler
		}
	}
	return $article_list;
}

function adi_menu_speaking_block($name) {
// retrieve specified section's sticky article excerpt for the speaking block
	global $speaking_block_form;

	$sb_tag_attr = 'section="'.$name.'" status="sticky"';
	if ($speaking_block_form) // use specified form
		$sb_article_tag = '<txp:article_custom '.$sb_tag_attr.' form="'.$speaking_block_form.'"/>';
	else // use default form
		$sb_article_tag = '<txp:article_custom '.$sb_tag_attr.'><txp:excerpt /></txp:article_custom>';
	$sb_text = trim(parse($sb_article_tag));
	if ($sb_text) // don't want empty spans
		return '<span class="speaking_block">'.$sb_text.'</span>';
}

function adi_menu_sort_hierarchy($a, $b) {
// comparison function for uasort()
	global $article_sort;

	$this_sort = $article_sort;
	$reverse = preg_match("/ desc/i",$this_sort);
	$this_sort = preg_replace("/ asc/i","",$this_sort); // remove " asc" MULTIPLE SPACES!!!
	$this_sort = preg_replace("/ desc/i","",$this_sort); // remove " desc" MULTIPLE SPACES!!!
	if (strcasecmp($this_sort,'title') == 0)
    	$reverse ?
			$res = strcasecmp($b["title"], $a["title"]) :
			$res = strcasecmp($a["title"], $b["title"]);
	else if (strcasecmp($this_sort,'adi_menu_sort') == 0)
    	$res = strcasecmp($a["sort"], $b["sort"]);
	else // no sort (i.e. database order)
    	$res = 0;
	return $res;
}

function adi_menu_hierarchy($section_list,$this_section,$clone) {
// create $hierarchy, indexed by section/article ID, using information from $section_list
	global $clone_title,$new_article_mode,$article_list,$article_position,$default_first,$articles;

	$hierarchy = array();
	if (($article_position == 'before') && $new_article_mode && $articles) { // insert articles if "BEFORE"
		if (array_key_exists($this_section,$article_list))
			$hierarchy = $article_list[$this_section];
	}
	if ($clone) { // clone parent as its child
		$hierarchy[$this_section]['title'] =
			$section_list[$this_section]['adi_menu_title'] ?
				$section_list[$this_section]['adi_menu_title'] :
				$clone_title; // use default clone title
		$hierarchy[$this_section]['url'] = $section_list[$this_section]['url'];
		$hierarchy[$this_section]['section'] = TRUE; // I'm a section
		$hierarchy[$this_section]['sort'] = rand(); // sort number
		$hierarchy[$this_section]['clone'] = TRUE; // I'm a clone
		$hierarchy[$this_section]['parent'] = $this_section;
		$hierarchy[$this_section]['redirect_section'] = $section_list[$this_section]['adi_menu_redirect_section'];
		$hierarchy[$this_section]['redirect_link'] = $section_list[$this_section]['adi_menu_redirect_link'];
		$hierarchy[$this_section]['alt_title'] = $section_list[$this_section]['adi_menu_alt_title'];
		$hierarchy[$this_section]['child'] = array(); // that's enough inbreeding
	}
	foreach ($section_list as $index => $section) { // find children
		if ($section['adi_menu_parent'] == $this_section) {
			$hierarchy[$index]['title'] = $section['title'];
			$hierarchy[$index]['url'] = $section['url'];
			$hierarchy[$index]['section'] = TRUE; // I'm a section
			$hierarchy[$index]['sort'] = $section['adi_menu_sort']; // adi_menu admin sort order
			$hierarchy[$index]['clone'] = FALSE; // I'm not a clone
			$hierarchy[$index]['parent'] = $section['adi_menu_parent'];
			$hierarchy[$index]['redirect_section'] = $section['adi_menu_redirect_section'];
			$hierarchy[$index]['redirect_link'] = $section['adi_menu_redirect_link'];
			$hierarchy[$index]['alt_title'] = $section['adi_menu_alt_title'];
			$hierarchy[$index]['child'] = adi_menu_hierarchy($section_list,$index,$section['adi_menu_clone']);
		}
	}
	if (!($article_position == 'before') && $new_article_mode && $articles) { // default to inserting articles AFTER sections (i.e. not "before")
		if (array_key_exists($this_section,$article_list)) // i.e. articles found
			$hierarchy = array_merge($hierarchy,$article_list[$this_section]);
		if (($article_position == 'dovetail') && array_key_exists($this_section,$article_list)) { // sort current level of hierarchy (sections AND articles) if there're articles here
			uasort($hierarchy, "adi_menu_sort_hierarchy");
		}
	}
	return $hierarchy;
}

function adi_menu_prune($old_hierarchy,$include_list) {
// remove sections from the hierarchy that are not on the include list (+ include_parent & include_childless)
	global $include_parent,$include_childless;

	$hierarchy = array();
	foreach ($old_hierarchy as $index => $section) {
		if (array_search($index, $include_list) !== FALSE) { // copy section/children but look no further
			if (!empty($section['child']) && (!$include_parent)) // copy children only
				$hierarchy += $section['child'];
			else if (($include_parent) || ($include_childless)) // copy section (& children)
				$hierarchy[$index] = $section;
		}
		else if (!empty($section['child'])) // delve deeper into hierarchy
			$hierarchy += adi_menu_prune($section['child'],$include_list);
	}
	return $hierarchy;
}

function adi_menu_descendents($hierarchy,$parent) {
// create list of descendents, indexed by section/article ID
	global $descendent_list;

	foreach ($hierarchy as $index => $section) {
		// add child to parent's list
		$descendent_list[$parent][] = $index;
		if ($section['child']) { // found some grandchildren
			// follow the family tree
			adi_menu_descendents($section['child'],$index);
			// add child's descendents to parent's list
			$descendent_list[$parent] = array_merge($descendent_list[$parent],$descendent_list[$index]);
		}
		else // create empty list for the childless
			$descendent_list[$index] = array();
	}
	return $descendent_list;
}

function adi_menu_find_ancestor($level) {
	global $descendent_list,$section_levels,$s,$adi_menu_debug;

	$found = '';
	foreach ($descendent_list as $index => $list) {
		$index == 'adi_menu_root' ? // special case
			$this_level = 0 :
			$this_level = $section_levels[$index];
		if (array_search($s,$list) !== FALSE) {
			if ($level == $this_level) {
				$found = $index;
			}
		}
	}
	return $found;
}

function adi_menu_active($index,$hierarchy,$active_section,$active_parent,$active_article) {
// determines whether current element should be active or not
// NOTE:	in articles mode - lowest parent is parent of active article
//			in non-articles mode - lowest parent is parent of active section
	global $s,$descendent_list,$active_ancestors,$pretext,$articles;

	if ($articles) { // articles part of menu structure
		$active_section = ($active_section && !$pretext['id']); // active section (but not if displaying article) BEWARE: resetting $active_section here
		$active_section_parent = ($active_parent && !$pretext['id'] && array_key_exists($s,$hierarchy[$index]['child'])); // parent section of active section (but not if displaying an article)
		$active_article_parent = ($active_parent && (strcasecmp($s, $index) == 0)); // parent of active article
		$active_section_ancestor = ($active_ancestors && (array_search($s,$descendent_list[$index]) !== FALSE)); // ancestor of active section
		$active_article_ancestor = ($active_ancestors && (array_search('article_'.$pretext['id'],$descendent_list[$index]) !== FALSE)); // ancestor of active article
		return $active_section || $active_section_parent || $active_article_parent || $active_section_ancestor || $active_article_ancestor || $active_article;
	}
	else { // don't care about articles
		$active_section_parent = ($active_parent && array_key_exists($s,$hierarchy[$index]['child'])); // parent section of active section
		$active_section_ancestor = ($active_ancestors && (array_search($s,$descendent_list[$index]) !== FALSE)); // ancestor of active section
		return $active_section || $active_section_parent || $active_section_ancestor;
	}
}

function adi_menu_markup($hierarchy,$level) {
// output <ul>/<li> markup from given $hierarchy
	global $prefs,$menu_id,$parent_class,$active_class,$s,$class,$link_span,$list_id,$list_id_prefix,$active_li_class,$articles,$include_children,$active_parent,$list_span,$active_ancestors,$descendent_list,$first_class,$last_class,$list_prefix,$prefix_class,$suppress_url,$new_article_mode,$article_class,$pretext,$active_article_class,$speaking_block,$label,$labeltag,$label_class,$label_id,$current_children_only,$adi_menu_escape,$odd_even;

	$out = array();
	$css_id = $css_class = '';
	if (!$level) { // set HTML ID & CSS class on top level <ul> only
		if ($menu_id) $css_id = ' id="'.$menu_id.'"';
		if ($class) $css_class = ' class="'.$class.'"';
		if (($label) && !empty($hierarchy))
			$labeltag ?
				$out[] = doTag($label,$labeltag,$label_class,'',$label_id) :
				$out[] = $label;
	}
	if (!empty($hierarchy)) // suppress <ul> if empty hierarchy
		$out[] = '<ul'.$css_id.$css_class.'>';
	// get list of section names from $hierarchy and determine first & last
	$keys = array_keys($hierarchy);
	$first_section = reset($keys);
	$last_section = end($keys);
	$odd = FALSE;
	foreach ($hierarchy as $index => $section) {
		$odd = !$odd;
		$parent = !empty($hierarchy[$index]['child']);
		$li_class_list = '';
		if ($parent && $include_children)  // not a parent if not including the children
			$li_class_list .= ' '.$parent_class;
		$active_section = FALSE;
		$active_article = FALSE;
		if ($section['section'])
			$active_section = (strcasecmp($s, $index) == 0);
		else { // $section is actually an article!
			$li_class_list .= ' '.$article_class;
			$article_id = preg_replace('/article_/','',$index); // convert 'article_id#' to 'id#'
			$active_article = ($article_id == $pretext['id']); // is the article active?
		}
		if (($hierarchy[$index]['redirect_section']) || ($hierarchy[$index]['redirect_link'])) // redirected (to section or link ID)
			$li_class_list .= ' menu_redirect';
		if ($hierarchy[$index]['redirect_link']) // redirected (to link ID)
			$li_class_list .= ' menu_ext_link';
		$title = adi_menu_htmlentities($hierarchy[$index]['title'],$adi_menu_escape);
		if ($hierarchy[$index]['alt_title']) { // alternative title specified
			$li_class_list .= ' menu_alt_title';
			$title = adi_menu_htmlentities($hierarchy[$index]['alt_title'],$adi_menu_escape);
		}
		if (!($section['section']) && ($prefs['title_no_widow'])) // dewidow article title if required
			$title = noWidow($title);
		$url = $hierarchy[$index]['url']; // although URL may get suppressed later ...
		$clone = $hierarchy[$index]['clone'];
		$first = ($index == $first_section) && $first_class; // only flag first section if required
		$last = ($index == $last_section) && $last_class; // only flag last section if required
		$link_span ?
			$link_content = '<span>'.$title.'</span>' :
			$link_content = $title;
		if ($speaking_block)
			$link_content = $link_content.adi_menu_speaking_block($index);
		$clone ? // section is a clone, so make ID unique
			$clone_suffix = '_clone' :
			$clone_suffix = '';
		$list_id ?
			$li_id = ' id="'.$list_id_prefix.$index.$clone_suffix.'"' :
			$li_id = '';
		$active_li = $active_li_class && adi_menu_active($index,$hierarchy,$active_section,$active_parent,$active_article);
		if ($active_li) $li_class_list .= ' '.$active_li_class;
		$article_list = '';
		$article_parent = FALSE; // NOT NEEDED IN NEW SYSTEM: $PARENT WORKS FOR SECTION & ARTICLE PARENTS
		if (!$parent && $articles && !$new_article_mode) // not a section parent so check for articles in advance
			if (!$clone) { // but don't want duplicates
				$article_list = adi_menu_articles($index,'ul');
				if ($article_list != "") { // articles found, so add parent class (i.e. article parent)
					$li_class_list .= ' '.$parent_class;
					$article_parent = TRUE;
				}
			}
		if ($parent || $article_parent) // suppress URL on parents only
			switch ($suppress_url) {
				case 'all':
					$url = '#'; break;
				case 'top':
					if (!$level) $url = '#'; break;
				/*case 'section':
					if ($parent) $url = '#'; break;
				case 'article':
					if ($article_parent) $url = '#'; break;*/
				default:
					/* do nothing */ break;
			}
		if ($first) $li_class_list .= ' '.$first_class;
		if ($last) $li_class_list .= ' '.$last_class;
		if ($odd_even) $li_class_list .= ' '.(($odd) ? 'menu_odd' : 'menu_even');
		$li_class_list ?
			$css_class = ' class="'.trim($li_class_list).'"' :
			$css_class = '';
		$out[] = '<li'.$li_id.$css_class.'>';
		$active_a = $active_class && adi_menu_active($index,$hierarchy,$active_section,$active_parent,$active_article);
		if ($list_span) $out[] = '<span>';
		if ($list_prefix) $out[] = '<span class="'.$prefix_class.'">'.$list_prefix.'</span>';
		$out[] = tag($link_content,'a',($active_a ? ' class="'.$active_class.'"' : '').' href="'.$url.'"');
		if ($list_span) $out[] = '</span>';
		$find_the_kids = TRUE;
		if ($current_children_only)
			$find_the_kids = (0 == strcasecmp($s, $index)) || array_key_exists($s,$hierarchy[$index]['child']) || (array_search($s,$descendent_list[$index]) !== FALSE); // active section or parent of active or ancestor of active
		if ($parent && $include_children && $find_the_kids) // find the kids (but only if include_children=1)
			$out = array_merge($out,adi_menu_markup($hierarchy[$index]['child'],$level+1));
		else if ($articles && !$new_article_mode) // not a section parent but an article parent so insert articles here
			$out[] = $article_list;
		$out[] = "</li>";
	}
	if (isset($index) && $articles && !$new_article_mode)
	// above condition prevents tag errors with attribute combinations that result in no sections
	//  e.g. (include_parent="0" && sections="all childless") OR (sections="list" && exclude="same list")
		if ($hierarchy[$index]['parent'] != '') // last sibling was a child, so check for parent's articles here
			$out[] = adi_menu_articles($hierarchy[$index]['parent'],'');
	if (!empty($hierarchy)) // suppress <ul> if empty hierarchy
		$out[] = "</ul>";
	return $out;
}

function adi_menu_articles($section,$wraptag) {
// generate list of articles/markup using <txp:article_custom />
	global $s,$articles,$article_attr,$adi_menu_article_form,$section_article_list,$active_articles_only;

	if (@txpinterface != 'admin') { // articles not relevent in admin interface
		$allowed = array_search($section, $section_article_list) !== FALSE; // check if articles allowed
		if ($active_articles_only) // then only allow articles if section is active
			$allowed &= ($section == $s);
		if (!$allowed) return '';
		$article_attr == '' ?
			$attr = '' :
			$attr = ' '.$article_attr;
		$article_list = trim(parse('<txp:article_custom section="'.$section.'"'.$attr.' form="'.$adi_menu_article_form.'" />'));
		if ($article_list == "")
			return '';
		else // articles found
			if ($wraptag == "")
				return $article_list;
			else
				return tag($article_list,$wraptag,'');
	}
}

function adi_menu_array_subtract($array1,$array2) {
// values in array2 that are not in array1 are removed
	$new = array();
	$count1 = count($array1);
	$count2 = count($array2);
	foreach ($array1 as $index1 => $value1) {
		$found = FALSE;
		foreach ($array2 as $index2 => $value2) {
			$found = $value1 == $value2;
			if ($found) break;
		}
		$found ? $found = FALSE : $new[] = $value1;
	}
	return $new;
}

function adi_menu_htmlentities($string,$method) {
// escape supplied string using:
// 	new:
//		"" = default: ENT_COMPAT,'UTF-8' (thanks ttr)
// 	old:
//		"html" = htmlentities (ALL special characters translated)
//		"htmlspecial" htmlspecialchars (only & " ' < > translated)
	if ($method) { // use manual sledgehammer
		if ($method == 'html') $string = htmlentities($string);
		if ($method == 'htmlspecial') $string = htmlspecialchars($string);
	}
	else
		$string = htmlentities($string,ENT_COMPAT,'UTF-8');
	return $string;
}

function adi_menu($atts) {
// the <txp:adi_menu /> tag
	global $prefs,$s,$pretext,$out,$sort,$menu_id,$parent_class,$active_class,$exclude,$sections,$include_parent,$include_childless,$default_title,$default_first,$clone_title,$class,$link_span,$list_id,$list_id_prefix,$active_li_class,$articles,$article_attr,$section_article_list,$include_children,$active_parent,$active_articles_only,$list_span,$active_ancestors,$descendent_list,$first_class,$last_class,$list_prefix,$prefix_class,$suppress_url,$new_article_mode,$article_list,$article_class,$article_position,$active_article_class,$article_sort,$section_levels,$speaking_block,$speaking_block_form,$label,$labeltag,$label_class,$label_id,$current_children_only,$adi_menu_escape,$adi_menu_prefs,$odd_even,$section_article_sort;

	extract(lAtts(array(
		'active_class'			=> 'active_class',	// CSS class for current section (<a>)
		'active_li_class'		=> '',				// CSS class for current section (<li>)
		'active_parent'			=> '0',				// set active class on parent of current section
		'active_ancestors'		=> '0',				// set active class on all ancestors of current section
		'class'					=> 'section_list',	// CSS class for top level <ul>
		'include_default'		=> '1',				// include 'default' section
		'default_title'			=> 'Home',			// title for 'default' section
		'escape'				=> '',				// escape HTML entities in section titles
		'exclude'				=> '',				// list of sections to be excluded
		'sections'				=> '',				// exclusive list of sections (default = all)
		'include_current'		=> '0',				// include the current section (used with sections="...")
		'include_parent'		=> '1',				// output parent of included sections' children
		'include_children'		=> '1',				// output children as well as parents
		'include_childless'		=> '0',				// output childless section
		'current_children_only'	=> '0',				// only output children of current section
		//'children_only'			=> '0',				// used internally (if used as attribute = include_parent="0")
		'sub_menu_level'		=> '0',				// the level of the submenu to output
		'sort'					=> 'adi_menu_sort',	// section sort (use '' for database order)
		'menu_id'				=> 'mainmenu',		// CSS ID for top level <ul>
		'parent_class'			=> 'menuparent',	// CSS class for parent <li>
		'default_first'			=> '1',				// section 'default' to be listed first
		'clone_title'			=> 'Summary',		// default title of child clone
		'link_span'				=> '0',				// <span> contents of link or not
		'list_id'				=> '0',				// output <li> IDs or not
		'list_id_prefix'		=> 'menu_',			// <li> ID prefix
		'list_span'				=> '0',				// <span> contents of <li> or not
		'first_class'			=> '',				// CSS class on first <li> of list
		'last_class'			=> '',				// CSS class on last <li> of list
		'odd_even'				=> '0',				// CSS classes applied to odd/even list items
		'list_prefix'			=> '',				// prefix added to menu list items
		'prefix_class'			=> 'menu_prefix',	// class added to <span> around prefixes
		'suppress_url'			=> '0',				// set URL to "#" on section links
		'speaking_block'		=> '0',				// enable <span>speaking ... block</span>
		'speaking_block_form'	=> '',				// alternative form for speaking block
		'wraptag'				=> '',				// wrap a tag around <ul>
		'wraptag_class'			=> 'menu_wrapper',	// class for wraptag
		'wraptag_id'			=> '',				// id for wraptag
		'label'					=> '',				// label string to precede menu
		'labeltag'				=> '',				// tag to wrap around label
		'label_class'			=> 'menu_label',	// CSS class for label's tag
		'label_id'				=> '',				// HTML ID for label's tag
		'articles'				=> '0',				// include articles in menu
		'article_class'			=> 'menu_article',	// CSS class on <li> containing article link
		'article_attr'			=> '',				// comma separated list of additional article_custom attributes
		'article_exclude'		=> '',				// list of sections not to have articles
		'article_include'		=> '',				// exclusive list of sections to have articles (default = all)
		'article_position'		=> 'after',			// where to place articles: before, after or dovetail with sections
		'article_sort'			=> 'Posted desc',	// article sort
		'active_articles_only'	=> '',				// list articles in the current active section only
		'new_article_mode'		=> '1',				// new article mode on by default
		'test'					=> '0',				// switch on new article mode in 1.0 (now ignored)
		'debug'					=> '0'
	), $atts));

	if (!adi_menu_installed()) return "<em>adi_menu not installed!</em>";

	// tidy up supplied attribute values
	$children_only = 0; // ex-attribute may be changed later
	if ($sub_menu_level == "1") // switch to standard mode
		$sub_menu_level = 0;
	// don't have to worry about escaping default title because it's stored in $section_list & processed later
	$clone_title = trim($clone_title);
	if (empty($clone_title)) // don't want it to be empty
		$clone_title = 'Summary';
	// section sort
	$sort = trim($sort); // MySQL error if sort = " "
	empty($sort) ? $sort = 'NULL' : $sort = doSlash($sort); // = database order by default
	// article sort
	$fallback_sort = 'Posted desc';
	$article_sort = trim($article_sort);
	empty($article_sort) ? $article_sort = 'NULL' : $article_sort = doSlash($article_sort); // set to NULL if article_sort="" supplied
	$article_sort_list = explode(';',$article_sort); // split article_sort "title;section1:posted desc;section2:custom_2"
	$section_article_sort = array();
	$default_sort = '';
	foreach ($article_sort_list as $this_sort) {
		$this_section_sort = explode(':',$this_sort);
		if (count($this_section_sort) > 1) // e.g. "section1:posted desc"
			$section_article_sort[trim($this_section_sort[0])] = doSlash(trim($this_section_sort[1]));
		else // e.g. "title; ..."
			if ($this_sort)
				$default_sort = doSlash(trim($this_sort));
			else // e.g. ";section1:title ..."
				$default_sort = 'NULL';
	}
	empty($default_sort) ? $article_sort = doSlash($fallback_sort) : $article_sort = $default_sort;

	// seem to recall this is required to avoid interference from adi_menu_breadcrumb escape attribute
	$adi_menu_escape = $escape;

	// make sure currently active section IS included e.g. when section normally excluded from main menu but a submenu is required on it's own (separately linked-to) page
	if ($sub_menu_level)
		empty($sections) ? $sections = $s : $sections = $sections.",$s";

	// get section info from database (uses $sections & $exclude attributes at this point)
	if (!$include_default) // add default to exclude list
		empty($exclude) ? $exclude = "default" : $exclude .= ",default";
	$section_list = adi_menu_section_list($exclude);

	// set up required sections for later processing in hierarchy
	$sections = trim($sections);
	if ($include_current) { // add current section to list
		empty($sections) ? $sections = $s : $sections = $sections.",$s";
		// add current section's parent to list (otherwise won't get siblings when child is current)
		if (!empty($section_list[$s]['adi_menu_parent']))
			$sections = $sections.','.$section_list[$s]['adi_menu_parent'];
	}
	if (!empty($sections)) // specific sections (& their children) selected
		$include_list = do_list($sections); // trim spaces around section names in array

	// do articles bit
	if ($articles) {
		// sections that are allowed articles
		if (empty($article_include))
			$section_article_include = array_keys($section_list); // all sections
		else
			$section_article_include = do_list($article_include); // trim spaces around section names in array
		// sections that are NOT allowed articles
		if (empty($article_exclude))
			$section_article_exclude = array();
		else
			$section_article_exclude = do_list($article_exclude); // trim spaces around section names in array
		// sections_article_list = section_article_include minus section_article_exclude
		$section_article_list = adi_menu_array_subtract($section_article_include,$section_article_exclude);
		if ($new_article_mode)
			$article_list = adi_menu_get_articles($section_article_list);
		else
			$article_list = array();
	}

	// set up hierarchy
	$hierarchy = $original_hierarchy = adi_menu_hierarchy($section_list,'',0);

	// descendent list
	$descendent_list = array();
	$descendent_list = adi_menu_descendents($hierarchy,'adi_menu_root');

	// submenus
	if ($sub_menu_level) {
		$section_levels = adi_menu_section_levels($hierarchy,1); // top level = 1, etc
		$this_section = $s; // don't want to muck with $s
		if (empty($this_section)) $this_section = 'default'; // to cope with error page (thanks CeBe)
		// three submenu scenarios
		if ($section_levels[$this_section] == $sub_menu_level) { // ONE: current section at required level
			$include_list[] = $section_list[$this_section]['adi_menu_parent']; // add current's parent to list to get siblings
			$children_only = "1"; // but don't want current section's parent itself
		}
		else if ($section_levels[$this_section] == ($sub_menu_level - 1)) { // TWO: current sections' children at required level
			$include_list[] = $this_section; // add current section to list to get children
			$children_only = "1"; // but don't want current section itself
		}
		else {
			$ancestor = adi_menu_find_ancestor($sub_menu_level);
			if ($ancestor) { // THREE: there're ancestors at required level
				// add ancestor's parent to list to get siblings
				$include_list[] = $section_list[adi_menu_find_ancestor($sub_menu_level)]['adi_menu_parent'];
				$children_only = "1"; // don't want ancestor's parent itself
			}
			else
				$include_list = array();
		}
	}

	// prune & massage hierarchy
	if (empty($sections) && !$include_parent) // make up list of top level sections (& cater for old submenu mode)
		$include_list = array_keys($hierarchy);
	if (isset($include_list)) { // prune hierarchy
		if ($children_only) $include_parent = "0";
		$hierarchy = adi_menu_prune($hierarchy,$include_list);
	}

	// DEBUG
	if ($debug) {
		echo "SECTIONS FROM DATABASE (all)<br/>";
		dmp(adi_menu_get_sections());
		echo "SECTION INCLUDE LIST (all if empty)<br/>";
		if (isset($include_list)) dmp($include_list); else echo "<br/>";
		echo "SECTION EXCLUDE LIST (none if empty)<br/>";
		if (isset($exclude)) dmp($exclude); else echo "<br/>";
		echo "SECTION LIST<br/>";
		dmp($section_list);
		if ($articles) {
			echo "SECTION ARTICLE INCLUDE LIST<br/>";
			dmp($section_article_include);
			echo "SECTION ARTICLE EXCLUDE LIST<br/>";
			dmp($section_article_exclude);
			echo "SECTION ARTICLE LIST (include minus exclude)<br/>";
			dmp($section_article_list);
			if ($new_article_mode) {
				echo "ARTICLE LIST<br/>";
				dmp($article_list);
			}
		}
		else
			echo '(ARTICLE MODE NOT SELECTED)<br/><br/>';
		echo 'ARTICLE SORT (default='.$article_sort.')<br/>';
		dmp($section_article_sort);
		echo "ORIGINAL HIERARCHY<br/>";
		dmp($original_hierarchy);
		if (isset($include_list)) {
			echo "PRUNED HIERARCHY<br/>";
			dmp($hierarchy);
		}
		else
			echo '(HIERARCHY NOT PRUNED)<br/><br/>';
		if (isset($section_levels)) {
			echo "SECTION LEVELS<br/>";
			dmp($section_levels);
		}
		echo "DESCENDENT LIST<br/>";
		dmp($descendent_list);
		echo "VARIOUS<br/><pre>";
		echo "Current section = $s<br/>";
		echo "Current section's level = $section_levels[$s]<br/>";
		echo "Submenu level = $sub_menu_level<br/>";
		echo "Current article id = ".$pretext['id']."<br/>";
		echo "Articles = $articles<br/>";
		echo "New article mode = $new_article_mode";
		echo '</pre>';
		echo "SUPPLIED ATTRIBUTES<br/>";
		dmp($atts);
		echo "ARTICLE ATTRIBUTES<br/>";
		dmp(adi_menu_get_article_attr($article_attr));
		echo "PREFS<br/><pre>";
		foreach ($adi_menu_prefs as $name => $value)
			echo $name.': '.adi_menu_prefs($name).'<br/>';
		echo "</pre>VERSIONS<br/>";
		echo '<pre>';
		$version = safe_field("version", "txp_plugin", "name='adi_menu'");
		$status = safe_field("status", "txp_plugin", "name='adi_menu'");
		echo __FUNCTION__.': '.(empty($version)?'not installed':$version.($status?' (active)':' (not active)')).'<br/>';
		echo 'TXP: '.$prefs['version'].'<br/>';
		echo 'PHP: '.phpversion().'<br/>';
		echo 'MySQL: '.mysql_get_server_info();
		echo '</pre>';
	}

	// generate markup
	$out = adi_menu_markup($hierarchy,0);
	$out = trim(join($out));
	if ($out && $wraptag) { // add wraptag stuff
		$wrap_attr = '';
		if ($wraptag_id) $wrap_attr .= ' id="'.$wraptag_id.'"';
		if ($wraptag_class) $wrap_attr .= ' class="'.$wraptag_class.'"';
		$out = tag($out,$wraptag,$wrap_attr);
	}

	// output markup to page
	return $out;
}
