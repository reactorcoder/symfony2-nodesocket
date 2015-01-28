/* 
 * Main Javascript file to handle a gritter (notificiation jquery alert)
 * using socket and listening message callled by event emit using NodeSocket
 * bundle
 * 
 * Extended from YiiNodeSocket
 * 
 * @author Marin Sagovac, 2014
 * 
 */

jQuery(function($) {

	$.extend($.gritter.options, { 
        position: 'bottom-left', // defaults to 'top-right' but can be 'bottom-left', 'bottom-right', 'top-left', 'top-right' (added in 1.7.1)
		fade_in_speed: 'medium', // how fast notifications fade in (string or int)
		fade_out_speed: 2000, // how fast the notices fade out
		time: 10000 // hang on the screen for...
	});
        
	var socket = new NodeSocket();
	socket.debug(true);
	socket.on('message', function (message) {
	    $.gritter.add({
	    	title: message.title,
	    	text: message.text,
	    	sticky: ((typeof message.sticky !== "undefined") ? message.sticky : false),
	    	time: '',
	    	class_name: ((typeof message.class_name !== "undefined" && message.class_name.length>0) ? message.class_name : ' gritter-light gritter-info')
	    });
	    
	    var _url = ((typeof message.url !== "undefined" && message.url.length>0) ? message.url : '#');
	    var _icon = ((typeof message.icon !== "undefined" && message.icon.length>0) ? message.icon : null);
	    
	    var _cnt = parseInt($("#ntf_cnt").text(), 10);
	    _cnt = ((typeof _cnt !== "undefined" && !isNaN(_cnt)) ? (_cnt + 1) : 1);
	    $(".cnt", $("#hnotif")).text(_cnt);
	    $("i.fa-bell", $("#btn_notifications")).removeClass('icon-animated-bell').addClass('icon-animated-bell');
	    
	    $("#hnotif_header", $("#hnotif")).addNotification({
	    	limit: 10,
	    	time: message.time,
	    	url: _url,
	    	text: message.title,
	    	icon: _icon
		});
	});
});

$.fn.addNotification = function(options) {
	
	var _icon="", ml=0;
	
	var settings = $.extend({
		limit: 10,
		url: "#",
		text: "",
		time: "",
		icon_class: "",
		icon: null,
		badge: null,
		badge_class: null		
		}, options );
	
	if (settings.icon) {
		settings.icon_class += " "+settings.icon;
		_icon= '<i class="btn btn-xs no-hover '+settings.icon_class+'"></i> ';
		ml=30;
	} 
	
	var _txt = '<li class="notif_items unread"><a href="'+settings.url+'"><div class="clearfix">';
	if (settings.badge)
	{
		if (settings.badge_class) settings.badge_class = "badge-"+settings.badge_class; 
		
		_txt += '<span class="pull-left "><i class="btn btn-xs no-hover '+settings.icon_class+'"></i> '+settings.text+'</span>'; 	
		_txt += '<span class="pull-right badge '+settings.badge_class+'">'+settings.badge+'</span>'; 	
	} 
	else 
	{
		_txt += _icon + settings.text; 
	}
	_txt += '<br style="clear: both;" /><span style="margin-left: '+ml+'px;" class="smaller-90"><i class="ace-icon fa fa-clock-o"></i> '+settings.time+'</span>';
	_txt +='</div></a></li>';
	
	$(_txt).insertAfter($(this));
	
	return $(this);
}
