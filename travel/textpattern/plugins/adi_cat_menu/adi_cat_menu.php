<?php

/*
	adi_cat_menu - Category & article menu

	Written by Adi Gilbert

	Released under the GNU General Public License

	Version history:
	0.5		- new attribute: 'list_empty_cats' (for floodfish)
			- new attributes 'list_id' & 'list_id_prefix' (for floodfish)
			- new attributes: 'wraptag', 'wraptag_id', 'wraptag_class'
			- new attribute: 'active_parent' (for kbarlow)
	0.4		- new adi_cat_menu attributes: 'link' (for jpdupont), 'article_form' & 'rss_article_form' (for floodfish)
			- enhancement: extended 'categories' & 'exclude' functionality to section sensitive mode
			- fix: suppress empty <ul></ul> (e.g. when no categories found in section sensitive mode) (for jpdupont)
	0.3		- enhancement: restored 'section', 'this_section' attributes & extended functionality to cover article lists
			- enhancement: restored 'exclude' attribute functionality
			- enhancement: restored 'categories' attribute functionality
			- new adi_cat_menu attribute: 'section-sensitive'
	0.2		- enhancement: rss_unlimited_categories mode
			- help: updated instructions regarding article <li> active class
			- help: documented "sort' attribute
	0.1		- initial release

*/

global $thing,$atts,$txp407;

$txp407 = version_compare($prefs['version'], '4.0.7', '>=');

function adi_cat_menu_articles($category,$wraptag='',$article_attr='') {
// retrieve articles using <txp:article_custom /> or <txp:rss_unlimited_categories_article_list /> tags
	global $rss_unlimited,$s,$section,$this_section,$txp407,$article_form,$rss_article_form;
	$article_attr == '' ? // any article_custom attributes specified?
		$attr = '' :
		$attr = ' '.$article_attr;
	// set default "all sections" attribute
	if ($rss_unlimited)
		$section_attribute = ' section="*"';
	else
		$section_attribute = '';
	if ($section) // explicit section specified
		$section_attribute = ' section="'.$section.'"';
	if ($this_section) // current section required
		$section_attribute = ' section="'.$s.'"';
	$rss_unlimited ?
		$article_list = trim(parse('<txp:rss_unlimited_categories_article_list category="'.$category.'"'.$attr.' '.$section_attribute.' form="'.$rss_article_form.'" />')) :
		$article_list = trim(parse('<txp:article_custom category="'.$category.'"'.$attr.' '.$section_attribute.' form="'.$article_form.'" />'));
	if ($article_list == "")
		return '';
	else // articles found
		if ($wraptag == "")
			return $article_list;
		else
			return tag($article_list,$wraptag,'');
}

function adi_cat_menu_if($atts, $thing) {
// for use in the Form to identify the active article
// copy of TXP 4.0.7 if_article_id
	global $thisarticle, $pretext;
	assert_article();
	extract(lAtts(array(
		'id' => $pretext['id'],
	), $atts));
	if ($id)
		return parse(EvalElse($thing, in_list($thisarticle['thisid'], $id)));
}

function adi_cat_menu($atts) {
// <txp:adi_cat_menu /> tag
	global $prefs,$s,$c,$thisarticle,$rss_unlimited,$section,$this_section,$txp407,$article_form,$rss_article_form;

	extract(lAtts(array(
		'class'				=> 'cat_menu',		// class applied to top level <ul>
		'menu_id'			=> '',				// CSS ID for top level <ul>
		'active_class'		=> 'active_class',	// class applied to active <li>
		'parent'			=> '',				// category parent
		'categories'		=> '',				// list of categories to include
		'exclude'			=> '',				// list of categories to exclude
		'restrict'			=> '',				// category1 or category2
		'sort'				=> '',				// category sort options
		'link'				=> '1',				// output categories as links
		'messy_url'			=> '0',				// force links to be output in messy URL format
		'list_id'			=> '0',				// output <li> IDs or not
		'list_id_prefix'	=> 'cat_menu_',		// <li> ID prefix
		'article_attr'		=> '',				// attributes to pass to article_custom
		'article_form'		=> 'adi_cat_menu_articles', // form to use with article_custom
		'active_only'		=> '0',				// only show articles in currently active category
		'active_parent'		=> '0',				// apply active class to category <li> when in individual article mode (SHOULD BE DEFAULT BEHAVIOUR?)
		'rss_unlimited'		=> '0',				// rss_unlimited_categories mode
		'rss_article_form'	=> 'adi_cat_menu_rss_articles', // form to use with rss_unlimited_categories_article_list
		'section'			=> '',				// link categories to specified section
		'this_section'		=> '',				// link categories to current section (overrides 'section')
		'section_sensitive'	=> '0',				// display categories only that have articles belonging to specified 'section' or 'this_section'
		'list_empty_cats'	=> '1',				// display empty categories (i.e. categories that have no articles)
		'type'				=> 'article',		// leftover from TXP category_list
		'wraptag'			=> '',				// wrap a tag around the menu
		'wraptag_class'		=> 'menu_wrapper',	// class for wraptag
		'wraptag_id'		=> '',				// id for wraptag
		'debug'				=> '0',
	), $atts));

	// set up the basics
	//$break = 'li';
	//$wraptag = 'ul';
	$sort = doSlash($sort);
	$include_list = '';
	if ($categories) {
		$include_list = do_list($categories);
		$include_list = join("','",doSlash($include_list));
	}
	$exclude_list = '';
	if ($exclude) {
		$exclude_list = do_list($exclude);
		$exclude_list = join("','",doSlash($exclude_list));
	}

	// debug action
	if ($debug) {
		echo "VERSIONS";
		echo '<pre>';
		$version = safe_field("version", "txp_plugin", "name='adi_cat_menu'");
		$status = safe_field("status", "txp_plugin", "name='adi_cat_menu'");
		echo __FUNCTION__.': '.(empty($version)?'not installed':$version.($status?' (active)':' (not active)')).'<br/>';
		echo 'TXP: '.$prefs['version'].($txp407 ? ' (4.0.7+)' : ' (pre-4.0.7)').'<br/>';
		echo 'PHP: '.phpversion().'<br/>';
		echo 'MySQL: '.mysql_get_server_info();
		echo '</pre>';
		echo 'SUPPLIED ATTRIBUTES:';
		dmp($atts);
		echo "CURRENT CATEGORY = $c<br/>";
		echo "CURRENT SECTION = $s<br/>";
		echo "INITIAL INCLUDE LIST = $include_list<br/>";
		echo "INITIAL EXCLUDE LIST = $exclude_list<br/>";
	}

	// here we go
	if ($section_sensitive) {
		if ($this_section) // current section required, override $section
			$section = $s;
		// set some cbs_category_list attribute values
		$showcount = FALSE;
		$sticky = FALSE;
		$posted = '';
		// some clever stuff from cbs_category_list
		if ($section == 'default') {
			$table = ', `'.PFX.'txp_section` AS s';
			$sqlsection = 's.name AND s.on_frontpage = 1';
		}
		else {
			$table = '';
			$sqlsection = "'$section'";
		}
		// process cbs_category_list attributes
		$parent = ($parent) ? " AND c.parent = '$parent'" : '';
		$showcount = ($showcount == 'true') ? ', COUNT(*) AS count' : '';
		$sticky = ($sticky == 'true') ? '5' : '4';
		switch($posted) {
			case 'all':
				$posted = '';
				break;
			case 'future':
				$posted = ' AND t.Posted >= now()';
				break;
			default:
				$posted = ' AND t.Posted < now()';
		}
		// I did these bits!
		$sort ?
			$sort = ' ORDER BY c.'.$sort :
			$sort = ' ORDER BY c.name asc';
		if ($include_list)
			$include_list = " AND c.name IN ('$include_list')";
		if ($exclude_list)
			$exclude_list = " AND c.name NOT IN ('$exclude_list')";
		// some magic SQL from cbs_category_list (with some include/exclude extras)
		$rs = startRows(
			'SELECT c.name, c.title'.$showcount.' FROM `'.PFX.'txp_category` AS c, `'.PFX.'textpattern` AS t'.$table
			.' WHERE (c.name = t.Category1 OR c.name = t.Category2)'
			.$include_list
			.$exclude_list
			.' AND c.type = \'article\' AND t.Section = '.$sqlsection.$parent.$posted
			.' AND t.Status = '.$sticky.' GROUP BY c.name'.$sort
			,$debug);
	}
	else { // not section sensitive
		if ($parent) // exclude parent from list
			if ($exclude_list) // combine parent & exclude list
				$exclude_list = $exclude_list.','.$parent;
			else // just exclude parent
				$exclude_list = $parent;
		if ($debug) echo "NEW EXCLUDE LIST = $exclude_list";
		// based on code from TXP 4.0.6 category_list tag
		if ($exclude_list) {
			$exclude_list = "AND name NOT IN ('$exclude_list')";
		}
		if ($parent) {
			$qs = safe_row('lft, rgt', 'txp_category', "name = '".doSlash($parent)."'",$debug);
			if ($qs) {
				extract($qs);
				$rs = safe_rows_start('name, title', 'txp_category',
					"(lft between $lft and $rgt) and type = '".doSlash($type)."' and name != 'default' $exclude_list order by ".($sort ? $sort : 'lft ASC'),$debug);
			}
		}
		else if ($include_list) { // explicit list of categories supplied
			$rs = safe_rows_start('name, title', 'txp_category',
				"type = '".doSlash($type)."' and name in ('$include_list') order by ".($sort ? $sort : "field(name, '$include_list')"),$debug);
		}
		else { // empty parent attribute (= all categories)
			$rs = safe_rows_start('name, title', 'txp_category',
				"type = '$type' and name not in('default','root') $exclude_list order by ".($sort ? $sort : 'name ASC'),$debug);
		}
	}

	// if parent category not found then we fail in same way as TXP category_list with "Undefined variable: rs" - COULD DO BETTER?
	if ($rs) {
		$out = array();
		empty($class) ? // set up <ul> class
			$ul_class = '' :
			$ul_class = ' class="'.$class.'"';
		empty($menu_id) ? // set up <ul> ID
			$ul_id = '' :
			$ul_id = ' id="'.$menu_id.'"';
		$empties = '';

		// determine which categories current article belongs to (if individual article mode) or generate emmpty list if not
		$article_cats = array();
		if (!empty($thisarticle)) { // individual article being displayed
			if ($debug) {
				echo 'CURRENT ARTICLE:';
				dmp($thisarticle);
			}
			// get current article's category(ies) (single article) (from TXP if_article_category tag)
			if (!empty($thisarticle['category1'])) $article_cats[] = $thisarticle['category1'];
			if (!empty($thisarticle['category2'])) $article_cats[] = $thisarticle['category2'];
			$article_cats = array_unique($article_cats);
			if ($debug) {
				echo 'CURRENT ARTICLE\'S CATEGORIES ('.$thisarticle['title'].'):';
				dmp($article_cats);
			}
		}

		// work through retrieved list of categories
		while ($a = nextRow($rs)) {
			extract($a); // extract category's $name & $title
			if ($debug) {
				echo 'CATEGORY '.$title.':';
				dmp($a);
			}
			if ($name) { // i.e. category name
				// set section for category link (if this_section set then current section, else use supplied section)
				$section = ($this_section) ? ( $s == 'default' ? '' : $s ) : $section; // note: default = ''
				$title = htmlspecialchars($title);

				// determine if current category is active (can't tell if individual article being displayed)
				// note use of strcasecmp - category names can be in different case if permanent link mode in TXP 4.0.5 is not messy!
				$active_category = (strcasecmp($c, $name) == 0);

				// determine if category is parent to individual article
				$parent_category = FALSE;
				if (!empty($article_cats)) { // contains individual article's categories (if any) - empty if not individual article display mode
					$parent_category = (!(array_search($name,$article_cats) === FALSE));
					if ($debug && $parent_category) echo 'PARENT OF CURRENT ARTICLE<br/><br/>';
				}

				// set <li> class: IF active_class attribute set AND (category currently active OR (active_parent_category attribute set AND category is parent of active individual article))
				$li_class = ($active_class AND ($active_category OR ($active_parent AND $parent_category))) ?
					' class="'.$active_class.'"' :
					'';
				$list_id ?
					$li_id = ' id="'.$list_id_prefix.$title.'"' :
					$li_id = '';
				$li_tag = '<li'.$li_class.$li_id.'>';
				$messy_url ?
					$cat_url = hu.'?c='.$name : // messy URL
					$cat_url = pagelinkurl(array('s' => $section, 'c' => $name)); // render link according to Permanent Link Mode
				$link ?
					$a_tag = '<a href="'.$cat_url.'">'.$title.'</a>' :
					$a_tag = $title;

				// determine whether to list articles or not
				$list_articles = TRUE; // default setting
				if ($active_only)
					$list_articles = $active_category || $parent_category; // list articles if category is active or is parent of current individual article
				if ($list_articles) {
					$articles_found = trim(adi_cat_menu_articles($name,'ul',$article_attr));
					if (!$articles_found && $debug) $empties .= ' '.$title;
				}
				else
					$articles_found = '';
				if ($list_empty_cats || (!$list_empty_cats && $articles_found))  // generate category list item
					$out[] = $li_tag.$a_tag.$articles_found.'</li>';
			}
			if ($debug) echo '<hr />';
		}

		if ($debug) $empties = '<li>[Empties: '.$empties.']</li>';

		if ($out) { // it's a wrap
			$out = '<ul'.$ul_class.$ul_id.'>'.$empties.join($out).'</ul>';
			if ($wraptag) { // double wrapped for your protection
				$wrap_attr = '';
				if ($wraptag_id) $wrap_attr .= ' id="'.$wraptag_id.'"';
				if ($wraptag_class) $wrap_attr .= ' class="'.$wraptag_class.'"';
				$out = tag($out,$wraptag,$wrap_attr);
			}
			return $out;
		}
		else
			return '';

	}
	return '';
}
