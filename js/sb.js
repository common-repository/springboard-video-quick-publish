jQuery(document).ready(function() {
	//manualChange();
});

function manualChange() {
	jQuery(".inputbox").change(function() {
		if(jQuery(this).val() != '') {
			var id = jQuery(this).attr('id');
			var video_id = id.split("_");
			
			var params = {};
			var new_val = jQuery(this).val();
			
			if(jQuery('.update-nag').length) {
				jQuery('#loading').css('top', '85px');
			} else {
				jQuery('#loading').css('top', '55px');
			}
			
			jQuery('#loading').show();
			jQuery('#refresh_static').hide();
			jQuery('#refresh_active').show();
			params['video_id'] = video_id[1];
			if(video_id[0] == 'title') {
				params['title'] = encodeURIComponent(new_val);
			} else {
				params['description'] = encodeURIComponent(new_val);
			}
			params['sb_action'] = "updateTitle";
			
			var data = {
				action: 'updateVideoData',
				params: params,
			};
			
			jQuery.post(ajaxurl, data, function(response) {
				jQuery('#loading').hide();
				jQuery('#refresh_static').show();
				jQuery('#refresh_active').hide();
				if(video_id[0] == 'title') {
					jQuery('#title_'+video_id[1], window.parent.document).val(new_val);
				}
			});
		}
	});
}

function editInputValue() {
	//inputboxEdit
	var id = jQuery('.inputboxEdit').attr('id');
	var video_id = id.split("_");
	
	var params = {};
	var new_val = jQuery('#'+id).val();
	
	if(new_val != '') {
		jQuery('#loading').show();
		
		params['video_id'] = video_id[1];
		params['title'] = encodeURIComponent(new_val);
		params['sb_action'] = "updateTitle";
		
		jQuery.ajax({
			type: "POST",
			data: params,
			url: document.URL,
			success: function(data) {
				jQuery('#loading').hide();
				jQuery('#title_'+video_id[1], window.parent.document).val(new_val);
			},
			error: function(data) {
				alert("There was an error, please try again");
			}
		});
	}
}

function editTextAreaValue() {
	//inputboxEdit
	var id = jQuery('.inputboxTextEdit').attr('id');
	var video_id = id.split("_");
	
	var params = {};
	var new_val = jQuery('#'+id).val();
	
	if(new_val != '') {
		jQuery('#loading').show();
		
		params['video_id'] = video_id[1];
		params['description'] = encodeURIComponent(new_val);
		params['sb_action'] = "updateTitle";
		
		jQuery.ajax({
			type: "POST",
			data: params,
			url: document.URL,
			success: function(data) {
				jQuery('#loading').hide();
			},
			error: function(data) {
				alert("There was an error, please try again");
			}
		});
	}
}

function editInputFields() {
	var idTitle = jQuery('.inputboxEdit').attr('id');
	var idDescription = jQuery('.inputboxTextEdit').attr('id');
	var video_id = idTitle.split("_");
	var tags = jQuery("#tags").val();
	var channelId = jQuery("#sb_channels").val();
	var params = {};
	var new_title_val = jQuery('#'+idTitle).val();
	var new_description_val = jQuery('#'+idDescription).val();
	if(new_title_val != '') {
		params['title'] = encodeURIComponent(new_title_val);
	}
	
	if(new_description_val!='') {
		params['description'] = encodeURIComponent(new_description_val);
	}
	params['tags'] =encodeURIComponent(tags);
	params['channel'] = channelId;
	if(new_title_val != '' || new_description_val != '') {
		//jQuery('#loading').show();
		//$('.sb_thumbnail_page').css({'background-color' : 'white', 'opacity' : '0.6', 'filter' : 'alpha(opacity=60)', 'width' : '100px;', 'height' : '300px', 'position' : 'relative'});
		params['video_id'] = video_id[1];
		
		params['sb_action'] = "updateTitle";
		console.log(params);
		var data = {
			action: 'editInputFields',
			params: params,
		};
		
		jQuery.post(ajaxurl, data, function(response) {
			//jQuery('#loading').hide();
			//$('.sb_thumbnail_page').css({'overflow' : 'hidden', 'position' : 'fixed', 'height' : '650px', 'background-color' : '', 'opacity' : '1'});
			if(new_title_val != '') {
				jQuery('#title_'+video_id[1], window.parent.document).val(new_title_val);
			}	
		});
	}
}

function create_quicktag(video, type, player, video_num) {
	console.log("Here is video param: " + video);
		
	var num_video_value = '';
	if(video_num > 0) {
		num_video_value = ' video_num="'+video_num+'"';
	}
	
	jQuery.each(video, function(i){
		var width = jQuery("#player_width").val();
		var height = jQuery("#player_height").val();
		var videoid = video[i] + "";
		
		if(videoid.length) {
			var qMarkPosition = videoid.indexOf('?');
			if(qMarkPosition != -1) {
				videoid = videoid.substr(0,qMarkPosition);
			}		
		}
		
		var quicktag = '[springboard type="'+type+'" id="'+videoid+'"';
		if(type!='cpvVideo')
			quicktag += ' player="'+player+'"';
		quicktag += ' width="'+width+'" height="'+height+'" '+num_video_value+'] ';
		insert_editor_content(quicktag);
	});
	self.parent.tb_remove();
}

function insert_editor_content(content){

	win = top;
	var editor = window.parent.document.getElementById("content");
	var cke_editor = win.document.getElementById('cke_contents_content');
	
	if( cke_editor != null ) editor = cke_editor;
	
	if (editor.style.display == 'none') {
		var ckeE = window.parent.CKEDITOR; 
		if(typeof ckeE != 'undefined' && typeof ckeE.instances != 'undefined' && typeof ckeE.instances.content != 'undefined' && typeof ckeE.instances.content.insertHtml != 'undefined') {
			ckeE.instances.content.insertHtml("<p>"+content+"</p>");
			}
		else {
			window.parent.tinyMCE.execCommand("mceInsertContent", false, "<p>"+content+"</p>");
			}
	} else {
		if(cke_editor) {
			win.edInsertContent(win.document.getElementById('content'), content);
		}
		else { 
			 sbInsertAtCursor(win.document, content);
			 }
	}

}

function getTopWindow(){
	return (window.opener) ? window.opener : (window.opener) ? window.parent : window.top;
}

function animateModalSize(width, height){

	this.originalWidth = Number(jQuery("#TB_window").css("width").replace("px", ""));
	this.originalHeight = Number(jQuery("#TB_iframeContent").css("height").replace("px", "")); // take the height of the iframe, because we ignore the height of the dark gray header (it's outside of the iframe)
	var pageHeight = jQuery(window).height();
	jQuery("#TB_window").css("top", '');
	jQuery("#TB_window").animate(
			{ 
		        width: width + "px",
				height: height + "px",
				marginTop: "-" + ((height + 27) / 2) + "px",
	        	marginLeft: "-" + (width / 2) + "px"
	      	}, 
	      	600 
		);
	
	jQuery("#TB_iframeContent").animate(
		{
			width: width + "px",
			height: height + "px"
		},
		600
	);
}

function deleteVideo(id) {
	var res = confirm("Are you sure ?");
	var params = {};
	
	if(!res){
		return false;
	}
	
	params['video_id'] = id;
	params['sb_action'] = "deleteVideo";
	
	if(jQuery('.update-nag').length) {
		jQuery('#loading').css('top', '85px');
	} else {
		jQuery('#loading').css('top', '55px');
	}
	
	jQuery('#loading').show();
	jQuery('#refresh_static').hide();
	jQuery('#refresh_active').show();
	
	var data = {
		action: 'deleteVideo',
		params: params,
	};
	
	jQuery.post(ajaxurl, data, function(response) {
		jQuery('#video_div_'+id).html('');
		jQuery('#video_div_'+id).hide();
		refreshVideoList();
	});
	
	/*jQuery.ajax({
		type: "POST",
		data: params,
		url: document.URL,
		success: function(data) {
			jQuery('#video_div_'+id).html('');
			jQuery('#video_div_'+id).hide();
			refreshVideoList();
		},
		error: function(data){
			alert("There was an error, please try again");
		}
	});*/
}

function stripslashes (str) {
    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Ates Goral (http://magnetiq.com)
    // +      fixed by: Mick@el
    // +   improved by: marrtins
    // +   bugfixed by: Onno Marsman
    // +   improved by: rezna
    // +   input by: Rick Waldron
    // +   reimplemented by: Brett Zamir (http://brett-zamir.me)
    // +   input by: Brant Messenger (http://www.brantmessenger.com/)
    // +   bugfixed by: Brett Zamir (http://brett-zamir.me)
    // *     example 1: stripslashes('Kevin\'s code');
    // *     returns 1: "Kevin's code"
    // *     example 2: stripslashes('Kevin\\\'s code');
    // *     returns 2: "Kevin\'s code"
    return (str + '').replace(/\\(.?)/g, function (s, n1) {
        switch (n1) {
        case '\\':
            return '\\';
        case '0':
            return '\u0000';
        case '':
            return '';
        default:
            return n1;
        }
    });
}

function search_keypress(evt) {
	evt = (evt) ? evt : window.event;
	var charCode = (evt.which) ? evt.which : evt.keyCode;

    if (charCode == 13) {
    	search();
    	return false;
    }

    return true;

}
var sb = sb || {};
sb.plugin_url = '';

sb.login = sb.login || {};

sb.login = sb.login.partners = [];

sb.login.showPartnersPlayer = function(pub_id,api_key) {
var params = {};
	params['sb_action'] = 'getPartnerPlayers';
	params['publisher_id'] = pub_id;
	params['api_key'] = api_key;
	jQuery('#refresh_active').show();
	jQuery('#sb_player_holder').hide();
	jQuery('#sb_log_save_button').hide();
	
	var data = {
		action: 'getPartnerPlayers',
		params: params,
	};
	
	jQuery.post(ajaxurl, data, function(response) {
		var json_players = jQuery.parseJSON(response);
		console.log(response);
		if(json_players.length <= 0)
			return;
			
		var widgetOptions = '<select name="sb_palyer" id="sb_player" style="width:200px;margin-bottom:3px;">';
		for(var i = 0; i < json_players.length; i++) {
			var selected_val = json_players[i].WidgetPlayer.setAsDefaultPlayer == '1' ? 'selected' : '';
			widgetOptions +='<option '+selected_val+' value=\"'+json_players[i].WidgetPlayer.player_id+'\">'+json_players[i].WidgetPlayer.player_id+' - ' +json_players[i].WidgetPlayer.description +'</option>';
		}
		widgetOptions += '</select>';
		jQuery('#refresh_active').hide();
		jQuery('#sb_player_holder').html(widgetOptions);
		jQuery('#sb_player_holder').show();
		jQuery('#sb_log_save_button').show();
		/*jQuery('#default_player_row').show();
		jQuery('#sb_player').show();
		alert('Verified');*/
	});
	
	
	/*jQuery.ajax({
		type: 'POST',
		data: params,
		url: sb.plugin_url+'/ajax.php',
		success: function(data) { 
			var json_players = jQuery.parseJSON(data);
			if(json_players.length <= 0)
				return;
				
			var widgetOptions = '<select name="sb_palyer" id="sb_player" style="width:200px;margin-bottom:3px;">';
			for(var i = 0; i < json_players.length; i++) {
				var selected_val = json_players[i].WidgetPlayer.setAsDefaultPlayer == '1' ? 'selected' : '';
				widgetOptions +='<option '+selected_val+' value=\"'+json_players[i].WidgetPlayer.player_id+'\">'+json_players[i].WidgetPlayer.player_id+' - ' +json_players[i].WidgetPlayer.description +'</option>';
			}
			widgetOptions += '</select>';
			jQuery('#refresh_active').hide();
			jQuery('#sb_player_holder').html(widgetOptions);
			jQuery('#sb_player_holder').show();
			jQuery('#sb_log_save_button').show();
			/*jQuery('#default_player_row').show();
			jQuery('#sb_player').show();
			alert('Verified');
		}
	});*/
}

function sbInsertAtCursor(doct, myValue) {
    //IE support
	var myField = doct.getElementById('content');
    if (doct.selection) {
        myField.focus();
        sel = doct.selection.createRange();
        sel.text = myValue;
    }
    //MOZILLA and others
    else if (myField.selectionStart || myField.selectionStart == '0') {
        var startPos = myField.selectionStart;
        var endPos = myField.selectionEnd;
        myField.value = myField.value.substring(0, startPos)
            + myValue
            + myField.value.substring(endPos, myField.value.length);
    } else {
        myField.value += myValue;
    }
}