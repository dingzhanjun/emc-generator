$(document).ready(function()
{
	// Change position of login form to center of desktop
	var viewPortHeight = parseInt(document.documentElement.clientHeight);
	var scrollHeight = parseInt(document.body.getBoundingClientRect().top);
	hedu =parseInt(viewPortHeight-337-180);
	hemoi=hedu/2;
	thu=Math.abs(scrollHeight)+hemoi;
	height = Math.abs(scrollHeight) + parseInt(viewPortHeight);
	$("#login_form").css('margin-top',thu);
	$("#main_tabs").css('margin-top',thu);
	
	$(".main_content").css('min-height',viewPortHeight);
	$(".bg_main_content").css('min-height', viewPortHeight - 100);
	// Effect for below tabs
	$("#below_tabs .below_tab_icon").hover(function()
	{
		if (!$(this).hasClass('active'))
			$(this).stop().animate({'top':'-25px'},300);
	},function()
	{
		if (!$(this).hasClass('active'))
			$(this).stop().animate({'top':'0'});
	});
	
	$("#main_tabs a").live('click',function()
	{
		var viewPortWidth = parseInt(document.documentElement.clientWidth);
		var position = $("#grounds_manager").offset();
		var position_top = position.top;
		var position_left = position.left;
		var left = (20 - position_left) + "px";
		var right = (parseInt(viewPortWidth) + parseInt(left) - 96 - 35) + "px";
		var topTemp = (100 + 16 - position_top);
		var top = 0;
		
		top =  topTemp + "px";
		$(".tab_label").css('width','96px');
		$(".tab_label").css('display','block');
		$("#main_tabs a").css('width', '96px');
		$("#main_tabs a img").animate({'width':'96px', 'height': '96px'}, 1000);
		$("#grounds_manager").animate({'top': top, 'left': left}, 1000);
		$("#pages_manager").animate({'top': top, 'left': right}, 1000);
		
		topTemp = topTemp + 96 + 30;
		top =  topTemp + "px";
		$("#users_manager").animate({'top': top, 'left': left}, 1000);
		$("#gallery_manager").animate({'top': top, 'left': right}, 1000);
		
		topTemp = topTemp + 96 + 30;
		top =  topTemp + "px";
		$("#services_manager").animate({'top': top, 'left': left}, 1000);
		$("#backup_manager").animate({'top': top, 'left': right}, 1000);
		
		topTemp = topTemp + 96 + 30;
		top =  topTemp + "px";
		$("#cms_manager").animate({'top': top, 'left': left}, 1000);
		$("#system_manager").animate({'top': top, 'left': right}, 1000);
		
		var link = $(this).attr('rel');
		var t = setTimeout(function()
		{
			window.location = link;
		},1500);
		
	});
	
	
	// SET POSITION FOR LEFT - RIGHT TABS
	var height = $("#left_tabs").outerHeight();
	var top = parseInt((viewPortHeight - height) / 2);
	
	$("#left_tabs").css('top',top);
	$("#right_tabs").css('top',top);
	
});

// TINY MCE - WYSIWYG EDITOR
tinyMCE.init({
	// General options
	mode : "specific_textareas",
	editor_selector : "editor",
	theme : "advanced",
	plugins : "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist,autosave,visualblocks",

	// Theme options
	theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
	theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
	theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
	theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak,restoredraft,visualblocks",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_statusbar_location : "bottom",
	theme_advanced_resizing : true,

	// Drop lists for link/image/media/template dialogs
	template_external_list_url : "lists/template_list.js",
	external_link_list_url : "lists/link_list.js",
	external_image_list_url : "lists/image_list.js",
	media_external_list_url : "lists/media_list.js",

	// Style formats
	style_formats : [
		{title : 'Bold text', inline : 'b'},
		{title : 'Red text', inline : 'span', styles : {color : '#ff0000'}},
		{title : 'Red header', block : 'h1', styles : {color : '#ff0000'}},
		{title : 'Example 1', inline : 'span', classes : 'example1'},
		{title : 'Example 2', inline : 'span', classes : 'example2'},
		{title : 'Table styles'},
		{title : 'Table row 1', selector : 'tr', classes : 'tablerow1'}
	],

	// Replace values for the template plugin
	template_replace_values : {
		username : "Some User",
		staffid : "991234"
	}
});