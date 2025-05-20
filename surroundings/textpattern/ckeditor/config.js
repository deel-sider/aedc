/**
 * @license Copyright (c) 2003-2015, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	config.allowedContent = true;
	config.resize_minHeight = 800;
	config.height=950;
	config.width=950;
	config.extraPlugins = 'codemirror,scayt';
	config.fontSize_defaultLabel = '1rem';
};
