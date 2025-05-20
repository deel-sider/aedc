<?php
if (@txpinterface == 'admin')
{
	register_callback('atb_editarea_page', 'page');
	register_callback('atb_editarea_form', 'form');
	register_callback('atb_editarea_style', 'css');
	register_callback('atb_editarea_plugin', 'admin_side', 'body_end');
}

function atb_editarea_page() { atb_editarea('html', 'html'); }
function atb_editarea_form() { atb_editarea('form', 'html'); }
function atb_editarea_style() { atb_editarea('css', 'css'); }
function atb_editarea_plugin() {
	if ( 'js' === gps('event') ) { atb_editarea('js', 'js'); }
}

function atb_editarea( $did, $syntax ) {

	echo <<<EOS
<script language="javascript" type="text/javascript" src="edit_area/edit_area/edit_area_full.js"></script>
<script language="javascript" type="text/javascript">
editAreaLoader.init({
	id : "$did"		// textarea id
	,syntax: "$syntax"			// syntax to be uses for highgliting
	,start_highlight: true		// to display with highlight mode on start-up
});
</script>
EOS;
}
