/**
 * @license Copyright (c) 2003-2019, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here.
	// For complete reference see:
	// https://ckeditor.com/docs/ckeditor4/latest/api/CKEDITOR_config.html

	// The toolbar groups arrangement, optimized for two toolbar rows.

	// Set the most common block elements.
	config.format_tags = 'p;h1;h2;h3;pre';

	config.extraPlugins = ['colorbutton', 'colordialog', 'clipboard', 'copyformatting', 'emoji', 'font', 'find', 'forms', 'format', 'justify', 'table', 'tableresize', 'tabletools', 'image'];

	// Simplify the dialog windows.
	config.removeDialogTabs = 'image:advanced;link:advanced';
};
