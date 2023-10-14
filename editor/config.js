/*
Copyright (c) 2003-2012, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
 */
CKEDITOR.editorConfig = function(config) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	config.toolbar = 'gt_toolbar';
	 
	config.toolbar_gt_toolbar =
		[
			{ name: 'document', items : [ 'Source'] },
		{ name: 'clipboard', items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
		{ name: 'editing', items : [ 'Find','Replace','-','SelectAll','-','SpellChecker', 'Scayt' ] },
		{ name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat' ] },
		'/',
		{ name: 'paragraph', items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote','CreateDiv',
		'-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','BidiLtr','BidiRtl' ] },
		{ name: 'links', items : [ 'Link','Unlink','Anchor' ] },
		{ name: 'insert', items : [ 'Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak','Iframe' ] },
		'/',
		{ name: 'styles', items : [ 'Styles','Format','Font','FontSize' ] },
		{ name: 'colors', items : [ 'TextColor','BGColor' ] },
		{ name: 'tools', items : [ 'Maximize', 'ShowBlocks','-','About' ] }
		];
	
	config.toolbar_literacy =
		[
			{ name: 'document', items : [ 'Source'] },
		{ name: 'clipboard', items : [ 'Cut','Copy','PasteText','-','Undo','Redo' ] },
		{ name: 'basicstyles', items : [ 'Bold','Italic','Underline','-','RemoveFormat' ] },
		{ name: 'paragraph', items : [ 'NumberedList','BulletedList',
		'-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock' ] },
		'/',
		{ name: 'links', items : [ 'Link','Unlink' ] },
		{ name: 'insert', items : [ 'Image','Table','HorizontalRule','SpecialChar'] },
		{ name: 'styles', items : [ 'Format','FontSize' ] },
		{ name: 'colors', items : [ 'TextColor','BGColor' ] },
		{ name: 'tools', items : [ 'Maximize' ] }
		];
	
	config.filebrowserBrowseUrl = '/editor/kcfinder/browse.php?type=files';
	config.filebrowserImageBrowseUrl = '/editor/kcfinder/browse.php?type=images';
	config.filebrowserFlashBrowseUrl = '/editor/kcfinder/browse.php?type=flash';
	config.filebrowserUploadUrl = '/editor/kcfinder/upload.php?type=files';
	config.filebrowserImageUploadUrl = '/editor/kcfinder/upload.php?type=images';
	config.filebrowserFlashUploadUrl = '/editor/kcfinder/upload.php?type=flash';
};
