/**
 * @license Copyright (c) 2003-2015, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	config.allowedContent = true;
	config.resize_minHeight = 700;
	config.height=350;
	config.extraPlugins = 'codemirror,scayt';
    config.image_prefillDimensions = false;
    config.image2_prefillDimensions = false;
    config.scayt_autoStartup = true;
    config.scayt_sLang = 'en_GB';
};

CKEDITOR.replace( 'body', {
                            disallowedContent : 'img{style}'
                          }
);
