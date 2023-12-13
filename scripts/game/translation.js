$(function() {
	$("#tabs").tabs();

	var $tabs = $('#tabs').tabs({
		tabTemplate: '<li><a href="#{href}">#{label}</a></li>',
	});
});