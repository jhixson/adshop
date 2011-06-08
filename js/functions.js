$('document').ready(function() {
	
	setupToolTips();

	thumbStrip();
	
	comboBoxes();
	
	placeAdForm();
	
	viewItem();
	
	toggleLabelTips();
	
	photoUpload();
	
	loginForm();
	
	profileForm();
	
	passwordForm();
	
	//$('#q').focus();
	
	$('a.disabled').click(function(e) {
		e.preventDefault();
	});
	
	$('#clear_favs').click(function(e) {
		e.preventDefault();
		//animated
		/*
		$('#favs ul').animate({'left':'55px'},function() {
			$('#favs ul li:first').remove();
			$(this).css({'left':'0px'});
		});
		*/
		var subcat = $('#favs ul li:last').remove().find('a').attr('rel');
		var favs = JSON.parse($.cookie('favs'));
		favs[subcat] = 0;
		$.cookie('favs', JSON.stringify(favs), {'expires':7,'path':'/'});
		if($('#favs ul li').length == 0)
			$('#favs').html('<h2>Your Choices Will Appear Here</h2>');
		
	});
	
	$('#search_button').click(function() {
		window.location.href = +'/search?q='+$('#q').val();
	});
	
	$('#q').focus(function(e) {
		if ($(this).val() == 'Search')
			$(this).addClass('active').val('');
	}).blur(function(e) {
		if ($(this).val() == '')
			$(this).removeClass('active').val('Search');
	}).keyup(function(e) {
		if(e.which == 13)
			window.location.href = '/search?q='+$('#q').val();
	});
	
	if($('#q').val() != 'Search')
		$('#q').focus();
	
	relativeCenter($('#menu'),$('#menu a.red_button'));	
	
	//relativeCenter($('#container .content'),$('#sold_list'));	
	
	//relativeCenter($('#details'),$('h2.inline'));
	/*
	$('#item_list img').load(function() {
		verticalCenter($('#container .content.item a.img'),$('#container .content.item a.img img'));
	});
	*/
	
	// preload some menu rollover stuff...
	$(['menu_hover_bg.jpg','item_brief_bg.png','car_w.png','dog_w.png','farm_w.png','couch_w.png','ipod_w.png','music_w.png','wrench_w.png','game_w.png','bike_w.png','ticket_w.png','camera_w.png','person_w.png','rare_w.png','tick-green.png','finished_checkmark.png']).preload();
	var sub_active = false;
	$('#menu ul').delegate('li:not(.caption)', 'mouseenter', function(){
		sub_active = false;
		var active = $('#menu ul li a.active');
		if(active.is('.sub'))
			sub_active = true;
		active.addClass('sub');
	});
	$('#menu ul').delegate('li:not(.caption)', 'mouseleave', function(){
		var active = $('#menu ul li a.active');
		if(!sub_active)
			active.removeClass('sub');
	});
	
	$('li.caption a').click(function(e) {
		e.preventDefault();
	});
	
	$('#dialog').jqm({modal:true});
	
	$(window).hashchange();	
});

function toggleLabelTips() {
	$('input.text:not(.edit), textarea.text:not(.edit)').each(function() {
		if($(this).data('val') == null)
			$(this).data('val',$(this).val());
	});
	
	$('input.text:not(.green), textarea.text').live('focus',function() {
		if(!$(this).is('.active') && !$(this).is('.edit'))
			$(this).val('').addClass('active');
		if($(this).is('.edit'))
			$(this).trigger('keyup');
		if($(this).next().is('.field_tip') || !$(this).nextAll('label:visible').is('.error'))
			$(this).nextAll('.field_tip').fadeIn('fast');
	}).blur(function() {
		if($(this).val() == '')
			$(this).removeClass('active').val($(this).data('val'));
		if($(this).nextAll('label').is('.field_tip'))
			$(this).nextAll('label:not(.error)').fadeOut('fast');
	});
}

function viewItem() {
	$('.content.item').mouseenter(function() {
		$(this).addClass('active');
	}).mouseleave(function() {
		$(this).removeClass('active');
	}).click(function(e) {
		$(this).addClass('active');
		$('.content.item').delay(500).removeClass('active');
		if(e.target.nodeName != 'A' && e.target.nodeName != 'SPAN')
			document.location = $('a:first',this).attr('href');
	});
	
	var none_text = '';
	$('a.checkbox[rel=show_crossbreeds]').click(function(e) {
		if($(this).is('.checked')) {
			if($('p.none').length != 0) {
				none_text = $('p.none').text();
				$('p.none').text('No crossbreeds for sale currently of this breed.');
			}
			else {
				$('.content.item').filter(function(i) {
					return !$(this).data('extra-attributes').crossbreed;
				}).fadeOut('fast',function() {
					if($('p.none').length == 0)
						$('<div class="content"><p class="none">No crossbreeds for sale currently of this breed.</p></div>').hide().insertAfter('#item_list:not(:has(li:visible))').fadeIn('fast');
				});
			}
		}
		else {
			if(none_text != '') {
				$('p.none').text(none_text);
			}
			else {
				$('.content:has(.none)').remove()
				$('.content.item').fadeIn('fast');
			}
		}
	});
	
	var saveRemoveAd = function(e) {
		var el = $(e.currentTarget);
		console.log(el);
		var id = el.attr('rel');
		$.post('/request/save_ad',{'item_id':id},
			function(data,status) {
				var dataObj = JSON.parse(data);
				if(dataObj.status == 'saved')
					$('#save_ad').html('<span>Remove from Saved</span');
				else if(dataObj.status == 'removed')
					$('#save_ad').html('<span>Save Ad</span');
					
				//$('#item_list:not(:has(li))').after('<div class="content"><p class="none">To save an ad, use the \'Save Ad\' button on the lower right of any ad page.</p></div>');
				$('<div class="content"><p class="none">To save an ad, use the \'Save Ad\' button on the lower right of any ad page.</p></div>').hide().insertAfter('#item_list:not(:has(li))').fadeIn('fast');
			}
		);
		
		//if(el.is('.remove_ad_button') && !el.closest('ul').prev().is('.subcategories') && $('#q').val() == 'Search')
		if(/saved/.test(window.location.href))
			el.parents('li').remove();
		if(el.is('.red_button_small'))
			el.remove();
	};
	
	$('#save_ad, a.confirm_remove_ad_button').click(function(e) {
		e.preventDefault();
		saveRemoveAd(e);
	});
	
	/*
	$('a.remove_ad_button').click(function(e) {
		e.preventDefault();
		$(this).html('<span>Yes Remove It</span>').addClass('confirm_remove_ad_button');
		$('<a href="#" class="blue_button_small" rel="'+$(this).attr('rel')+'"><span>No</span></a>').click(function(e) {
			$(this).next('.remove_ad_button').html('<span>Remove from Saved</span>').removeClass('confirm_remove_ad_button');
			$(this).remove();
		}).prependTo('.remove_ad_buttons');
	});
	*/
	
	$('.remove_ad_buttons').delegate('a','click',function(e) {
		e.preventDefault();
		if($(this).is('.confirm_remove_ad_button')) {
			$(this).prev('a.blue_button_small').remove();
			saveRemoveAd(e);
		}
		else if($(this).is('.remove_ad_button')) {
			$(this).html('<span>Yes Remove It</span>').addClass('confirm_remove_ad_button');
			$('<a href="#" class="blue_button_small" rel="'+$(this).attr('rel')+'"><span>No</span></a>').click(function(e) {
				$(this).next('.remove_ad_button').html('<span>Remove from Saved</span>').removeClass('confirm_remove_ad_button');
				$(this).remove();
			}).prependTo($(this).parent());
		}
	});
}

function thumbStrip() {
	var overflow_width = 0;
	
	$('#thumbstrip ul#thumb_images a:not(.add_photo)').click(function(e) {
		e.preventDefault();
	});
	
	var set_featured_image = function(e,a) {
		if(!a.is('.no_photo') && !a.is('.add_photo')) {
			e.preventDefault();
			$('#thumbstrip li.thumb').removeClass('active');
			a.closest('li').addClass('active');
			var src = $('img',a).attr('src');
			var ext_exp = new RegExp(/-t(\.\w+)(\?.+)?/);
			var img_ext = ext_exp.exec(src)[1];
			var ts = ext_exp.exec(src)[2];
			var new_src = src.replace(ext_exp,'');
			new_src = new_src+'.jpg';
			
			if($('#place_form').is('.edit'))
				new_src += ts;
			
			//var time = new Date().getTime();
			//console.log(basename(new_src))
			
			$('.featured img').attr('src',new_src);
			//imageSize();
		}
	};
	
	$('#thumbstrip ul#thumb_images').delegate('li.thumb a','mouseenter',function(e) {
		var a = $(this);
		set_featured_image(e,a);
	});
	
	
	$('#thumbstrip a.next_image').click(function(e) {
		e.preventDefault();
		var index = $('#thumbstrip li.thumb.active').index('#thumbstrip li');
		var next = (index+1) % $('#thumbstrip li.thumb').length;
		var a = $('#thumbstrip li.thumb:eq('+next+')').find('a');
		set_featured_image(e,a);
	});
	
	
	
	var scrollerVisible = function() {
		overflow_width = $('.featured img.active').width() - $('.featured').width();
		if(overflow_width > 5) {
			$('.featured #scroller_track').show();
			$('.featured #scroller').width(Math.max(60,($('.featured').width() - overflow_width))).show();
		}
		else {
			$('.featured #scroller_track').hide();
			$('.featured #scroller').hide();
		}
		
		$('.featured #scroller').css({'left':'0px'});
	};
	
	$('.featured #scroller').draggable({
		'axis': 'x',
		'containment': 'parent',
		drag:
			function(e,ui) {
				if($('.featured #scroller').width() == 60)
					$('.featured img.active').css({'right':(ui.position.left*(overflow_width/454))+'px'});
				else
					$('.featured img.active').css({'right':(ui.position.left+10)+'px'});
			}
	});	
	
	$('#thumbstrip li.thumb:first:not(.noactive)').addClass('active');
	$('#thumbstrip li.thumb a:not(.add_photo,.no_photo) img').each(function() {
		if($(this).height() % 2 == 1)
			$(this).height($(this).height()+1);
			
		var pre = [];
		var src = $(this).attr('src');
		var ext_exp = new RegExp(/-t(\.\w+)(\?.+)?/);
		var img_ext = ext_exp.exec(src)[1];
		var ts = ext_exp.exec(src)[2];
		var new_src = src.replace(ext_exp,'');
		new_src = 'upload/'+basename(new_src+'.jpg');
		pre.push(new_src);
		$(pre).preload();
	});
}

function imageSize() {
	//console.log('loaded');
	$('.featured img').css({'maxWidth':'524px','maxHeight':'393px','width':'auto','height':'auto'});
	var w = $('.featured img').width();
	var h = $('.featured img').height();
	if(w < 524 && h < 393) {
		$('.featured img').removeAttr('style');
		if(w/h >= 1.28)
			$('.featured img').css({'maxWidth':'100%','width':'524px'}).removeAttr('width').removeAttr('height');
		else
			$('.featured img').css({'maxHeight':'100%','height':'393px'}).removeAttr('width').removeAttr('height');
	}	
}

var tip_response = '';
function setupToolTips() {
	
	$(document).click(function(e){
		//console.log('doc clicked');
		if($(e.target).parents('#tiptip_holder').length == 0 && $('#tiptip_holder').css('display') == 'block') {
			$('#tiptip_holder').fadeOut(250);
			clear_actives();
		}
	});
	
	var tt_content = function(id) {
		if(tips[id] || tip_response != '') {
			var content = tip_response || tips[id];
			$('#tiptip_holder').find('*').addClass('black');
			$('#tiptip_content').html('<a href="#" id="tiptip_close">close</a>'+content);
			$('#tiptip_content input, #tiptip_content textarea').focus(function() {
					$(this).prev().hide();
				}
			).blur(function(){
				if($(this).val() == '')
					$(this).prev().show();
			});
			if($('#tiptip_content').css('marginTop') != '0px')
				$('#tiptip_content').css({'marginTop':0});
			
			$('.formitem.inset').click(function(){
				$('input, textarea',this).focus();
			});
			tip_response = '';
		}
	};
	
	$('a.tip').click(function(e) {
		if(!$(this).is('.active')) {
			clear_actives();
			$(this).addClass('active');
			$('#tiptip_holder').hide();
		}
		else {
			clear_actives();
		}
	});
	
	$('#footer a[id],#mistake,#email_seller_bottom,#report_ad,#password_reminder').tipTip({position:'top',maxWidth:'420px',delay:0,enter:
		function() {
			
			var id = $(this).attr('id');
			tt_content(id);
			viewAdForm(id);
			
			//$('email_seller_form').validate({showErrors:function(){return false;}});
		}
	});
	
	$('#email_seller_button,#renew_button[href=#],#edit_button[href=#],#safety_button,#remove_button[href=#]').tipTip({position:'left',maxWidth:'420px',delay:0,edgeOffset:5,enter:
		function() {
			var temp_response = tip_response;
			var id = $(this).attr('id');
			tt_content(id);
			if (id == 'email_seller_button' && temp_response == '') {
				window.scrollTo(0, 0);
				$('#tiptip_content').css({'marginTop':'108px'});
			}
			if((id == 'renew_button' || id == 'edit_button') && temp_response == '')
				$('#tiptip_content').css({'marginTop':'8px'});
				
			viewAdForm(id);

			//$('email_seller_form').validate({showErrors:function(){return false;}});
		}
	});
	
	
	$('#trade_ad_link').tipTip({position:'bottom',maxWidth:'420px',delay:0,enter:
		function() {
			$('#trade_ad_link').addClass('active');
			tip_response = '<div class="inner"><p>'+$('#trade_ad_link').attr('title')+'</p><p class="trade_disclaimer">Business name and address displayed, Consumer Protection Act 2007.</p></div>';
			tt_content();			
		}
	});
	
	
	$('#tiptip_close').live('click',function(e){
		e.preventDefault();
		clear_actives();
		$('#tiptip_holder').fadeOut('fast');
	});
	
	$('#send_message_to_seller,#send_message_to_seller_bottom').live('click',function(e){
		e.preventDefault();
		e.stopPropagation();
		var id = $(this).attr('id');
		var item_id = $('#email_seller_button').attr('rel') || $('#email_seller_bottom').attr('rel');
		$('span',this).addClass('disabled').text('Sending...');
		$.post('/request/email_seller',{
			'item_id':item_id,
			'name':$('#item_name').val(),
			'email':$('#item_email').val(),
			'phone':$('#item_phone_prefix').val()+' '+$('#item_phone').val(),
			'message':$('#item_message').val()
			},
			function(data,status) {
				send_message_response(data, status, id);
			}
		);
	});
	
	$('#send_message_to_us,#report_ad_button,#send_correction').live('click',function(e){
		e.preventDefault();
		e.stopPropagation();
		var id = $(this).attr('id');
		var rel = $(this).attr('rel');
		$('span',this).addClass('disabled').text('Sending...');
		$.post('/request/contact_us',{
			'name':$('#contact_name').val(),
			'email':$('#contact_email').val(),
			'phone':$('#contact_phone_prefix').val()+' '+$('#contact_phone').val(),
			'message':$('#contact_message').val(),
			'action':rel,
			'ad':$('h2').text(),
			'ad_link':window.location.href
			},
			function(data,status) {
				send_message_response(data, status, id);
			}
		);
	});
	
	$('#password_reminder').live('click',function(e) {
		e.preventDefault();
		e.stopPropagation();
		$.post('/request/reset_password',{
			'email':$('#item_email').val()
		});
	});
	
	var send_message_response = function(data, status, id) {
		var dataObj = JSON.parse(data);
		$('#tiptip_holder').hide();
		if(dataObj.status == 'ok')
			tip_response = '<div class="inner"><p>'+dataObj.content+'</p></div>';
		else
			tip_response = '<div class="inner"><p>'+dataObj.content+'</p></div>';
			
		if (id == 'send_message_to_seller')
			$('#email_seller_button').trigger('click');
		else if (id == 'send_message_to_seller_bottom')
			$('#email_seller_bottom').trigger('click');
		else if (id == 'send_message_to_us')
			$('#tip4').trigger('click');
		else if (id == 'report_ad_button') 
			$('#report_ad').trigger('click');
		else if (id == 'send_correction') 
			$('#mistake').trigger('click');
	};
	
	var viewAdForm = function(id) {
		$('#tiptip_content .formitem input').each(function() {
			$(this).val('');
		});
		
		$('#view_form').validate();
		
		$('#view_form input[name=item_id]').val($('#details h2').data('item_id'));
		
		var do_login = function(e) {
			$('#view_login_button').addClass('disabled').html('<span>Wait...</span>');
			if($('#view_password').val() == '') {
				$.post('/request/reset_password',{'email':$('#view_email').val()},function(data,status) {
					var respObj = JSON.parse(data);
					tip_response = '<div class="inner"><p>'+respObj.content+'</p></div>';
					$('#tiptip_holder').hide();
					$('#'+id).trigger('click');
				});
			}
			else if($('#view_form').valid()) {
				$.post('/request/valid_user',{'email':$('#view_email').val()},function(data,status) {
					var respObj = JSON.parse(data);
					if(respObj.status == 'ok')
						$('#view_form').submit();
					else {
						tip_response = '<div class="inner"><p>Wrong e-mail entered. Typo?</p></div>';
						$('#tiptip_holder').hide();
						$('#'+id).trigger('click');
					}
				});				
			}
		};
		
		$('#view_login_button').click(function(e) {
			e.preventDefault();
			do_login();
		});
		$('#view_password').keyup(function(e){
			if (e.keyCode == '13')
				do_login();
		});
		
		$('#remove_item_button').click(function(e) {
			e.preventDefault();
			$('#remove_item_button').addClass('disabled').html('<span>Wait...</span>');
				$.post('/request/valid_user',{'email':$('#view_email').val(),'logged_in':true},
					function(data, status, req) {
						var respObj = JSON.parse(data);
						if (respObj.status == 'ok') {
							$('#view_form').submit();
						}
						else {
							tip_response = '<div class="inner"><p>Wrong e-mail entered. Typo?</p></div>';
							$('#tiptip_holder').hide();
							$('#'+id).trigger('click');
						}
					}
				);
		});
	};
	
	var clear_actives = function(){
		$('#recent_grid a, #footer a, #trade_ad_link, #report_ad, #mistake, .action_buttons a, #email_seller_button, #email_seller_bottom').removeClass('active');
	};
}

/*
function comboBoxes() {
	$('.select').click(function(e) {
		e.stopPropagation();
		if (!$(this).next('.options_details').is(':empty')) {
			$('.options_details').prev().not($(this)).next('.options_details').hide();
			toggleSelect($(this));
		}
	});
	
	$('body').click(function() {
		$('.options_details').slideUp(400);
	});
	
	$('.options_details').find('.item:first').css({'borderTop':'none'});
	$('.options_details .item').live('mouseenter',function() {
		$(this).addClass('hovering');
	}).live('mouseleave',function() {
		$(this).removeClass('hovering');
	});
	
	$('.options_details .item').live('click',function(e) {
		e.stopPropagation(e);
		var sel = $(this).parent().prev('.select');
		$('.subname',sel).text($(this).text());
		
		toggleErrors();
	});
	
	$('#category_options .item').click(function(e) {
		var txt = $(this).text();
		getSubcategories(txt);
		//$('#item_price_field').toggleClass('hide', txt == 'Services');
		if(txt == 'Services') {
			$('#item_price_field').hide();
			$('#step_3 .formitem:first .inset').text('Full name (or business name).');
			placeAdForm();
		}
		else {
			$('#item_price_field').show();
			$('#step_3 .formitem:first .inset').text('Your first name is fine.');
		}
		$('label.inset').each(function() {
			$(this).css({'left':$(this).next().position().left}).show();
		});
	});
	
	$('.options_details:empty').prev('.select').find('.subname').addClass('disabled');
}
*/
function comboBoxes() {
	//var selected_option = $('select option[selected=selected]');
	$('select option[selected=selected]').each(function() {
		$(this).parent().prev('.select_container:first').find('.subname').text($(this).text());
	})
	
	$('select').change(function() {
		var val = $(this).val();
		var op = $(this).find('option[value='+val+']').text();
		$(this).prev('.select_container').find('.subname').text(op);
		
		if($(this).attr('id') == 'category_options' || $(this).attr('id') == 'subcategory_options') {
			if (op == 'Services' || op == 'Music Lessons' || op == 'Sports Lessons') {
				$('#item_price_field').hide();
				$('#item_name').data('val','Full name (or business name).').not('.active,.edit').val('Full name (or business name).');
			
				//placeAdForm();
			}
			else {
				$('#item_price_field').show();
				$('#item_name').data('val','Your first name is fine.').not('.active,.edit').val('Your first name is fine.');
			}
		}

		if($(this).attr('id') == 'category_options')
			getSubcategories(val);
		
		if($(this).attr('id') == 'subcategory_options')
			getSubsubcategories(val);
			
		if($(this).attr('id') == 'view_subsubcategory_options') {
			var path = window.location.pathname.split('/');
			window.location.href = '/'+path[1]+'/'+path[2]+'/'+path[3]+'/'+$(this).val();
		}
	});
}

function photoUpload() {
	$('#disable_overlay').remove();
	$('#step_2 .buttons a').removeClass('inactive');
	$('.photo_button_holder:has(img)').next('.photo_button_holder.disabled').removeClass('disabled');
	$('.photo_button_holder').each(function() {
		var index = $(this).index('.photo_button_holder') + 1;
		if ($(this).has('img').length > 0) {
			$(this).addClass('black');
			$(this).not(':has(span.image_number)').append('<span class="image_number"></span>');
			$('span.image_number', this).text(index);
			
			/*
			$(this).not(':has(a.front_image)').append('<a href="#" class="front_image"></a>');
			if ($(this).index('.photo_button_holder') > 0) 
				$('a.front_image', this).text('Use as Front');
			else 
				$('a.front_image', this).text('');
			*/
			$('span.main_image',this).remove();
				
			$(this).not(':has(a.remove_image)').append('<a href="#" class="remove_image"></a>');
			$('a.remove_image', this).text('Remove');
			
			$(this).not(':has(a.rotate_image)').append('<a href="#" class="rotate_image"></a>');
			$('a.rotate_image', this).text('Rotate');
			
			var current_angle = $('img',this).data('angle') || /-(\d+)\.(jpg|jpeg|gif|png|bmp)/i.exec($('img',this).attr('src'))[1];
			$('img',this).data('angle',current_angle);
			$('img',this).data('width',$('img',this).attr('data-width'));
			$('img',this).data('height',$('img',this).attr('data-height'));
		}
		else
			$('strong', this).text('Photo '+index);
	});
	$('.photo_button_holder:has(img):first').append('<span class="main_image">Main Photo</span>');
		
	$('span.phototip').removeClass('red black').toggleClass('hide', $('.photo_button_holder:has(img)').length == 1).text('You can drag photos to rearrange them!');
	if($('.photo_button_holder:has(img)').length == 0)
		$('span.phototip').addClass('black').text('Select the Photo 1 button to begin adding photos.');

	$('#photo_grid').sortable('destroy');
	$('#photo_grid').sortable({'items': 'li:has(img)','tolerance':'pointer','stop':photoUpload});
	$('#photo_grid').disableSelection();
	
	var fake_loading = false;
	/*
	var loading_complete = function(e,qID,fileObj,response,data) {
		if(fake_loading)
			setTimeout(function(){loading_complete(e,qID,fileObj,response,data);},1000);
		else {
			setTimeout(function() {
				console.log(response);
				var respObj = JSON.parse(response);
				var p = $(e.target).parent();
				if($('img',p).length == 0) {
					//var img_w = respObj.content.width;
					//var img_h = respObj.content.height;
					
					//p.find('strong').text('Finishing...');
					$('<div class="image_holder"><img src="'+debug_domain+'/img/upload/'+respObj.content.file+'" /></div>').prependTo(p).hide();
					$('img',p).data('width',respObj.content.width);
					$('img',p).data('height',respObj.content.height);
					var ext = respObj.content.ext;
					var base_file = 'upload/'+basename(respObj.content.file,ext).replace(/-\d+$/,'');				
					var rotators = [base_file+'-90'+ext,base_file+'-180'+ext,base_file+'-270'+ext];
					$(rotators).preload();
				}
			
				$('img',p).load(function(){
					//$(this).removeAttr('width').removeAttr('height').css({'width':'','height':''});
					//imageDimensions($(this));
					//verticalCenter(p,$(this));
					$('.image_holder',p).show();
					$(this).data('angle',0);
					p.addClass('black');
					$('.progress_bar',p).hide();
					p.removeClass('loading').find('strong').text('');
					
					$('object',p).remove();
					photoUpload();
				});
			},2000)
		}
	};*/
	
	
	$('.photo_button_holder:not(.disabled):not(:has(img)):not(:has(object))').find('input.swfupload').uploadify({
		'uploader': '/uploadify.swf',
		'script': '/request/upload',
		'folder': '/img/upload',
		'wmode': 'transparent',
		'width': 75,
		'height': 58,
		'auto': true,
		'sizeLimit':20971520,
		'scriptData':{'session':$.cookie('kohanasession')},
		'onSelect': function(e,qID,fileObj,data) {
			var p = $(e.target).parent();
			$('.progress_bar',p).show();
			$('.progress', p).width(8);
			p.addClass('loading').find('strong').text('Loading...');
			$('img',p).remove();
			$('<div id="disable_overlay"></div>').prependTo('#photogrid_holder');
			$('#step_2 .buttons a').addClass('inactive');
			$('span.phototip').removeClass('hide black').addClass('red').text('Please be patient while photo is finishing upload.');
		},
		'onProgress':function(e,qID,fileObj,data) {
			var p = $(e.target).parent();				
			if (fileObj.size <= 81920) {
				fake_loading = true;
				$('.photo_button_holder.loading .progress').animate({'width': 55},100,
					function() { 
						p.find('strong').text('Wait...'); 
						fake_loading = false;
					}
				);
			}
			else {
				var prog = Math.max(8,55 * (data.percentage / 100));
				$('.progress', p).width(prog);
				if(data.percentage >= 99)
					p.find('strong').text('Wait...');
			}
		},
		'onComplete': function(e,qID,fileObj,response,data) {
			var respObj = JSON.parse(response);
			var p = $(e.target).parent();
			if($('img',p).length == 0) {
				//var img_w = respObj.content.width;
				//var img_h = respObj.content.height;
				
				//p.find('strong').text('Finishing...');
				$('<div class="image_holder"><img src="'+'/img/upload/'+respObj.content.file+'" /></div>').prependTo(p).hide();
				$('img',p).data('width',respObj.content.width);
				$('img',p).data('height',respObj.content.height);
				var ext = respObj.content.ext;
				var base_file = 'upload/'+basename(respObj.content.file,ext).replace(/-\d+$/,'');				
				var rotators = [base_file+'-90'+ext,base_file+'-180'+ext,base_file+'-270'+ext];
				$(rotators).preload();
			}
		
			$('img',p).load(function(){
				//$(this).removeAttr('width').removeAttr('height').css({'width':'','height':''});
				//imageDimensions($(this));
				//verticalCenter(p,$(this));
				$('.image_holder',p).show();
				$(this).data('angle',0);
				p.addClass('black');
				$('.progress_bar',p).hide();
				$('.photo_button_holder.loading .progress').stop(true,true);
				p.removeClass('loading').find('strong').text('');
				
				$('object',p).remove();
				photoUpload();
			});
		},
		'onError':function(e,qID,fileObj,errorObj) {
			//console.log(errorObj);
			var p = $(e.target).parent();
			if(errorObj.type == 'File Size') {
				alert('File must be no larger than 20MB');
				$('.progress_bar',p).hide();
				p.removeClass('loading').find('strong').text('');
				//$('object',p).remove();
				photoUpload();
			}
		},
		'onCancel':function(e,qID,fileObj,data) {
			var p = $(e.target).parent();
			$('.progress_bar',p).hide();
			p.removeClass('loading').find('strong').text('');
			photoUpload();
		}
	});
	
	$('#header a').click(function(e) {
		if($('.photo_button_holder:has(object) .progress_bar:visible').length > 0)
			$('.photo_button_holder:has(object) input').uploadifyCancel(0);
	});
	
	
	$('a.remove_image').unbind('click');
	$('a.remove_image').bind('click',function(e) {
		e.preventDefault();
		var p = $(e.target).parent();
		p.removeClass('black');
		$('a, span',p).remove();
		p.find('img').fadeOut(500,function() {
			p.find('.image_holder, .drag_handle, .remove_image, object').remove();
			//p.next('.photo_button_holder:not(.disabled)').addClass('disabled').find('object').remove();
			p.find('strong').text('Photo '+(p.index('.photo_button_holder')+1));
			
			$('.photo_button_holder:has(img)').each(function() {
				var index = $('.photo_button_holder:has(img):last').index('.photo_button_holder');
				$('.photo_button_holder:eq('+index+')').prependTo($('#photo_grid'));
			});
			
			
			$('.photo_button_holder:not(.disabled):not(:has(img)) ~ .photo_button_holder').each(function() {
				$(this).addClass('disabled').find('object').remove();
			});
			
			photoUpload();
		});
	});
	
	$('a.front_image').unbind('click');
	$('a.front_image').bind('click',function(e) {
		e.preventDefault();
		var p = $(e.target).parent();
		var index = p.index('.photo_button_holder');
		$('.photo_button_holder:eq('+index+')').prependTo($('#photo_grid'));
		photoUpload();
	});
	
	var ajaxManager = $.manageAjax.create('ajaxQueue', { queue: true, cacheResponse: false, preventDoubbleRequests: false }); 
	$('a.rotate_image').unbind('click');
	$('a.rotate_image').bind('click',function(e) {
		e.preventDefault();
		var p = $(e.target).parent();
		var img = $('img',p);
		var new_angle = Math.round(parseInt(img.data('angle'))+90) % 360;

		var temp_width = img.data('width');
		img.data('width',img.data('height'));
		img.data('height',temp_width);
		
		img.data('angle',new_angle.toString());
		var ext_exp = new RegExp(/-\d+(\.\w+)/);
		var img_ext = ext_exp.exec(img.attr('src'))[1];
		var new_src = img.attr('src').replace(ext_exp,'');
		img.attr('src',new_src+'-'+new_angle+img_ext);
		
		ajaxManager.add({type:'post', data: {'image':img.attr('src'),'angle':img.data('angle')}, url: '/request/rotate_image'});
		
		//$.post(debug_domain+'/request/rotate_image',{'image':img.attr('src'),'angle':img.data('angle')});
		
		/*
		img.unbind('load');
		img.load(function() {
			//img.removeAttr('width').removeAttr('height').css({'width':'','height':''});
			//imageDimensions(img);
			verticalCenter(p, img);
		});
		//img.rotate(new_angle);
		*/
	});
}

function imageDimensions(img) {
	
	var img_w = img.width();
	var img_h = img.height();
	
	//console.log(img_w+' '+img_h);

	var new_w = 0;
	var new_h = 0;
	if(img_w / img_h >= 1.28) {
		new_w = 76;
		new_h = Math.round((76 / img_w) * img_h);
	}
	else {
		new_h = 59;
		new_w = Math.round((59 / img_h) * img_w);
	}
	
	img.css({'width':new_w,'height':new_h});
}

function placeAdForm() {
	//console.log('paf');
	
	$('#item_desc').keyup(function(e) {
		wordCount(e, 250);
	});
	
	$('#item_title').keyup(function(e) {
		charCount(e, 45);
	});
	
	$('label.inset').each(function() {
		$(this).css({'left':$(this).next().position().left}).show();
	}).click(function() {
		$(this).next().focus();
	});
	
	$('.formitem input, .formitem textarea').focus(function() {
		$(this).prev('label.inset').hide();
	}).blur(function() {
		if($(this).val() == '')
			$(this).prev('label.inset').show();
	});
	
	$('#item_email').blur(function() {
		if($(this).valid()) {
			$.post('/request/valid_user',{'email':$(this).val()},
				function(data, status, req) {
					var respObj = JSON.parse(data);
					if (respObj.status == 'ok') {
						$('#item_password').val('No password required, simply continue.').removeClass('active').addClass('green').attr('readonly', 'readonly').data('val', 'No password required, simply continue.');
						$('#item_password').siblings('label.field_options').find('small').removeClass('hide');
						$('#item_password').valid();
					}
					else {
						$('#item_password').removeClass('green').removeAttr('readonly').data('val', 'Your password is used to edit your ad.');
						if(!$('#item_password').is(':focus') && !$('#item_password').is('.active'))
							$('#item_password').removeClass('active').val('Your password is used to edit your ad.');
						$('#item_password').siblings('label.field_options').find('small').addClass('hide');
					}
					//$('#step_3').valid();
				}
			);
		}
	});
	
	$('#item_email').keyup(function(e) {
		if($('#item_password').is('.green')) {
			$('#item_password').val('Your password is used to edit your ad.').removeClass('green').removeAttr('readonly').data('val', 'Your password is used to edit your ad.');
			$('#item_password').siblings('label.field_options').find('small').addClass('hide');
		}
	});
	
	$('#place_form form').submit(function(e) {
		e.preventDefault();
	});
	
	/*
	$('#place_form.edit').keyup(function(e) {
		if(e.which == 13) {
			window.location.href = '#step_4';
		}
	});
	*/
	
	//$('.formitem textarea').elastic();
	
	/*
	$('.formitem').click(function(){
		$('input, textarea',this).focus();
	});
	*/
	
	$('#place_form .buttons a.inactive').live('click',function(e) {
		e.preventDefault();
	});
	
	var update_step = function() {
		if($('#place_form').length == 0)
			return false;
		var hash = location.hash || '#step_1';
		var step = hash.split('_')[1] || '1';
		
		if ($('#step_' + (step - 1)).has('label.error:visible').length > 0) {
			location.href = '#step_' + (step - 1);
			return false;
		}
		/*
		else if(step == 3 && $('#place_form').is('.edit')) {
			location.href = '#step_4';
			return false;
		}
		*/
		else if($('.buttons a').is('.inactive')) {
			return false;
		}
		
		$('#place_ad_form .form_section').hide();
		$(hash).show();
		$(hash+' label.inset').each(function() {
			if($(this).next().val() == '')
				$(this).css({'left':$(this).next().position().left}).show();
		});

		$('.circle').removeClass('active');
		$('.circle .num:contains('+step+')').parent('.circle').addClass('active');
		relativeCenter('#container',$('.steps'));
		
		window.scrollTo(0,0);
		
		if (step == 4) {
			$('#two_for_2').parent().hide();
			$('#three_for_4').parent().hide();
			$('.edit_mode_buttons').show();
			updateReview();
		}
		else {
			$('#two_for_2').parent().show();
			$('#three_for_4').parent().show();
			$('.edit_mode_buttons').hide();
		}
		
	};
	
	var center_buttons = function() {
		$('.green_button.place, .red_button.place').each(function(){
			relativeCenter('#container',$(this));
		});
	};
	relativeCenter('#container',$('.edit_mode_buttons'));
	
	
	$('#two_for_2').click(function(e) {
		e.preventDefault();
		var img = $('#three_for_4').removeClass('tick green_button').addClass('red_button').find('span').html('Switch To &euro;3 For 4 Months ?').prev('img').remove();
		$(this).removeClass('red_button').addClass('tick green_button').find('span').before(img).html('You Are Currently Selecting &euro;2 For 2 Months Option');
		center_buttons();
		$('#deal').text('2 for 2');

		$('#paypal_form input[name=item_name]').val('AdShop Ad placement for 2 Months');
		$('#paypal_form input[name=amount]').val('2');
	});
	
	$('#three_for_4').click(function(e) {
		e.preventDefault();
		var img = $('#two_for_2').removeClass('tick green_button').addClass('red_button').find('span').html('Switch To &euro;2 For 2 Months ?').prev('img').remove();
		$(this).removeClass('red_button').addClass('tick green_button').find('span').before(img).html('You Are Currently Selecting &euro;3 For 4 Months Option');
		center_buttons();
		$('#deal').text('3 for 4');
		
		$('#paypal_form input[name=item_name]').val('AdShop Ad placement for 4 Months');
		$('#paypal_form input[name=amount]').val('3');
	});
	
	$('.field_options :checkbox, .buttons :checkbox').each(function(){
		$(this).before('<a class="checkbox" href="#" rel="'+$(this).attr('id')+'">'+$(this).val()+'</a>');
		if($(this).is(':checked'))
			$(this).prev('.checkbox').addClass('checked');
		$(this).hide();
	});
	
	/*
	$('a.checkbox').toggle(function(e) {
		e.preventDefault();
		$(this).addClass('checked');
		$('#'+$(this).attr('rel')).attr('checked','checked');
	},function(e){
		e.preventDefault();
		$(this).removeClass('checked');
		$('#'+$(this).attr('rel')).removeAttr('checked');	
	});
	
	$('a.checkbox[rel=item_trade_ad]').click(function() {
		$('#trade_details').toggleClass('hide',(!$('#item_trade_ad').is(':checked')));
		if($('#trade_details').is('.hide'))
			$('#place_form:not(.edit) #step_3').valid(); 
	});
	*/
	
	$('a.checkbox').click(function(e) {
		e.preventDefault();
		if($(this).is('.checked')) {
			$(this).removeClass('checked');
			$('#'+$(this).attr('rel')).removeAttr('checked');
			if($(this).attr('rel') == 'item_trade_ad')
				$('#trade_details').addClass('hide');
			
		}
		else {
			$(this).addClass('checked');
			if($(this).attr('rel') == 'item_trade_ad')
				$('#trade_details').removeClass('hide');
			$('#'+$(this).attr('rel')).attr('checked','checked');
			//if($('#place_form:not(.edit)').length > 0)
				//$('#step_3').valid();
		}
	});
	
	/*
	$.localScroll({
		target: '#place_form',
		duration: 0,
		hash: true,
		axis: 'x',
		onAfter: update_step,
		onBefore: function(e,anchor,target) {
			var step = get_step();
			if($('#step_'+step).has('label.error:visible').length > 0)
				return false;
		}
	});
	*/
	
	$(window).hashchange(update_step);
	
	$.validator.addMethod("default_data",function(value, element, param) {
		if($(element).is('.green'))
			return true;
		else
			return value != $(element).data('val');
	}, 'This field is required.');
	
	$.validator.addMethod("required_if_visible",function(value, element, param) {
		return !(value == '' && !$(element).parents('#trade_details').is('.hide'));
	}, 'This field is required.');
	
	$('#place_form:not(.edit) #step_1').validate({
		rules: {
			item_title: {
				default_data: true
			},
			item_desc: {
				default_data: true
			}
		}
	});
	$('#place_form:not(.edit) #step_3').validate({
		rules: {
			item_name: {
				default_data: true
			},
			item_phone: {
				default_data: true
			},
			item_password: {
				default_data: true
			},
			item_business_name: {
				required_if_visible: true
			},
			item_business_address: {
				required_if_visible: true
			}
		}
	});
	
	$('#place_form a.edit_button').live('click',function(e) {
		//e.preventDefault();
		var href = $(e.target).attr('href');
		$('.buttons a.button',href).hide();
		$('.buttons a.blue_button').show();
		//$('.buttons a.button:last',href).removeClass('button arrow_right').addClass('blue_button').attr('href','#step_4').html('<span>Review Changes</span>');
	});
	
	$('#place_form .buttons a.right').click(function(e) {
		if(!$(this).closest('form').valid())
			return false;
		else if($(this).closest('form').attr('id') == 'step_2') {
			toggleErrors();
			if($('label.err:visible').length > 0)
				return false;
		}
	});
	
	var process_payment = function(button) {
		var media = [];
		$('#step_4 #thumbstrip li a:not(.add_photo,.no_photo) img').each(function() {
			//var media_item = {'src':basename($(this).attr('src')),'width':$(this).data('width'),'height':$(this).data('height'),'angle':$(this).data('angle')};
			
			var img = $(this);
			var ext_exp = new RegExp(/-t(\.\w+)(\?.+)?/);
			var img_ext = ext_exp.exec(img.attr('src'))[1];
			var new_src = img.attr('src').replace(ext_exp,'');
			var time = new Date().getTime();
		
			var media_item = {
				'src': basename(new_src+img_ext),
				'angle': $(this).data('angle')
			};
			media.push(media_item);
		});
		
		if($('#step_2 #photo_grid').length == 0)
			media.push({'mobile':true});
		
		//var button = $(e.target).attr('id');
				
		$('#step_2 #item_price').val($('#step_2 #item_price').val().replace(/\D/,''));
		
		var data = $('#step_1,#step_2,#step_3').serialize();
		data += '&item_id='+$('#item_id').val();
		data += '&item_cat='+$('#step_2 #category_options').val();
		data += '&item_subcat='+$('#step_2 #subcategory_options').val();
		data += '&item_subsubcat='+$('#step_2 #subsubcategory_options').val();
		data += '&item_location='+$('#step_2 #county_options').val();
		data += '&item_hide_email='+$('#item_hide_email:checked').length;
		data += '&item_trade_ad='+$('#item_trade_ad:checked').length;
		//data += '&item_term='+$('a.place.tick').attr('rel');
		data += '&item_term=3';
		data += '&item_coupon='+$('#item_coupon').val();
		data += '&media='+JSON.stringify(media);
		$.post('/request/save_item',data, 
			function(data, status, req) {
				$('.error,.success').remove();
				var respObj = JSON.parse(data);
				if (respObj.status == 'ok') {
					if(button == 'pay_by_paypal') {
						$('#pp_timestamp').val((new Date().getTime() / 1000).toFixed(0));
						var custObj = [{
							'timestamp': $('#pp_timestamp').val(),
							'uid': respObj.uid,
							'item_id': respObj.item_id
						}];
						$('#pp_custom').val(JSON.stringify(custObj));
						$('#paypal_form').submit();
					}
					else if(button == 'pay_by_phone') {
						$('#dialog').jqmHide();
						$('#dialog').css({'background-color':'#efefef','width':'490px','margin-left':'-245px','top':'22%'}).jqm({ajax:'/zong_iframe.php?item_id='+respObj.item_id}).jqmShow();
					}
					else {
						//$('#dialog').html('<p class="done"><span class="tick">Done</span></p>').jqmShow();
						$('#dialog').addClass('finished').html('<img src="/img/finished_checkmark.png" alt="Done" />').jqmShow();
						if(respObj.content == 'Item updated.')
							window.location.href = '/view/'+$('#item_id').val()+'/'+url_title($('#item_title').val());
						else
							window.location.href = '/';
					}
				}
				else {
					$('#dialog').jqmHide();
					$('.steps').before('<p class="error">' + respObj.content + '</p>');
				}	
			}
		);
	};
	
	$('#pay_by_phone, #pay_by_paypal, #finished_editing').click(function(e) {
		e.preventDefault();
		var button = $(this).attr('id');
		if(button != 'finished_editing')
			$('#dialog').html('<p>Please wait...</p>').jqmShow();
		process_payment(button);		
	});
	
	var process_renewal = function(button) {
		//var button = $(e.target).attr('id');
		var data = 'item_id='+$('#item_id').val();
		//data += '&item_term='+$('a.place.tick').attr('rel');
		data += '&item_term=3';
		data += '&item_coupon='+$('#item_coupon').val();
		$.post('/request/renew_ad',data, 
			function(data, status, req) {
				$('.error,.success').remove();
				var respObj = JSON.parse(data);
				
				if(respObj.status == 'ok') {
					if(button == 'renew_by_paypal') {
						$('#pp_timestamp').val((new Date().getTime() / 1000).toFixed(0));
						var custObj = [{
							'timestamp': $('#pp_timestamp').val(),
							'uid': respObj.uid,
							'item_id': respObj.item_id
						}];
						$('#pp_custom').val(JSON.stringify(custObj));
						$('#paypal_form').submit();
					}
					else if(button == 'renew_by_phone') {
						$('#dialog').jqmHide();
						$('#dialog').css({'background-color':'#efefef','width':'490px','margin-left':'-245px','top':'22%'}).jqm({ajax:'/zong_iframe.php?item_id='+respObj.item_id}).jqmShow();
					}
					else
						$('#container').prepend('<p class="success">'+respObj.content+' <a href="/">Return Home</a></p>');
				}
				else {
					$('#dialog').jqmHide();
					$('#container').prepend('<p class="error">'+respObj.content+'</p>');
				}
				
				//$('#footer').prevUntil('.error,.success').remove();
		});
	};
	
	$('#renew_by_phone, #renew_by_paypal').click(function(e) {
		e.preventDefault();
		var button = $(this).attr('id');
		$('#dialog').html('<p>Please wait...</p>').jqmShow();
		process_renewal(button);
	});
	
	$('#remove_ad').click(function(e) {
		e.preventDefault();
		var data = 'item_id='+$('#item_id').val();
		$.post('/request/remove_ad',data,
			function(data, status, req){
				$('.error,.success').remove();
				var respObj = JSON.parse(data);
				if (respObj.status == 'ok') 
					$('#container').prepend('<p class="success">Your ad has been successfully removed. <a href="/">Return Home</a></p>');
				else 
					$('#container').prepend('<p class="error">' + respObj.content + '</p>');
				
				$('#footer').prevUntil('.error,.success').remove();
			}
		);
	});
	
	$('#pay_with_coupon').click(function(e) {
		e.preventDefault();
		if($(this).is('.start')) {
			$(this).closest('.formitem').css('width','auto').find('label').text('Coupon:').removeClass('long').end().find('input.text').removeClass('hide').end().find('.button').removeClass('start');
		}
		else {
			var data = 'code='+$('#item_coupon').val();
			$.post('/request/valid_coupon',data,
				function(data, status, req){
					$('.error,.success').remove();
					var respObj = JSON.parse(data);
					if (respObj.status == 'ok') 
						process_payment(e);
					else 
						$('#container').prepend('<p class="error">' + respObj.content + '</p>');
				}
			);
		}
	});
	
	$('#renew_with_coupon').click(function(e) {
		e.preventDefault();
		if($(this).is('.start')) {
			$(this).closest('.formitem').css('width','auto').find('label').text('Coupon:').removeClass('long').end().find('input.text').removeClass('hide').end().find('.button').removeClass('start');
		}
		else {
			var data = 'code='+$('#item_coupon').val();
			$.post('/request/valid_coupon',data,
				function(data, status, req){
					$('.error,.success').remove();
					var respObj = JSON.parse(data);
					if (respObj.status == 'ok') 
						process_renewal(e);
					else 
						$('#container').prepend('<p class="error">' + respObj.content + '</p>');
				}
			);
		}
	});
	
	center_buttons();
	update_step();
}

function toggleErrors() {
	$('label.err').each(function() {
		$(this).toggleClass('show',$(this).parent().find('.subname').text() == 'Choose');
	});
}

function updateReview() {
	var title = $('#step_1 #item_title').val();
	var desc = $('#step_1 #item_desc').val();
	var cat = $('#step_2 #item_category').text();
	var subcat = $('#step_2 #item_subcategory').text();
	var county = $('#step_2 #item_county').text();
	var price = $('#step_2 #item_price').val();
	var name = $('#step_3 #item_name').val();
	var phone = $('#step_3 #item_phone_prefix').val()+' '+$('#step_3 #item_phone').val();
	$('#step_4 #details h2').text(title);
	//relativeCenter('#item_title_holder',$('#item_title_holder h2'));
	$('#step_4 .content .button:first span').text(subcat);
	
	price.replace(/\D/,'');
	var price_regex = /([\d,]+)(.\d+)?/.exec(price);
	if(price_regex == null || price == '0')
		$('#step_4 #price').parent().hide();
	else
		$('#step_4 #price').html('&euro;'+number_format(price_regex[1],0,'.',',')).parent().show();
	$('#step_4 #county').text(county);
	$('#step_4 #seller_name').text(name);
	$('#step_4 #phone').text(phone);
	
	$('#step_4 #edit_price').text('edit price or county');
	if(cat == 'Services' || subcat == 'Music Lessons' || subcat == 'Sports Lessons') {
		$('#step_4 #price').parent().hide();
		$('#step_4 #seller_name').text(name+' for a quote');
		$('#step_4 #edit_price').text('edit county');
	}
	
	if(desc) {
		desc = desc.replace(/\n\n/g,'</p><p>');
		desc = desc.replace(/\n/g,'<br />');
	}
	$('#step_4 #item_description_holder').html('<p>'+desc+'</p>');
	
	if($('#item_hide_email:checked').length > 0)
		$('#step_4 a.button.disabled span').text('No E-mails Please');
	else
		$('#step_4 a.button.disabled span').text('E-mail Button Goes Here');
	
	$('#step_4 .featured .img_holder').html('');
	$('#step_4 #thumbstrip ul').html('');		
	
	$('#photo_grid li img').each(function() {
		var img = $(this);
		var ext_exp = new RegExp(/-\d+(\.\w+)(\?.+)?/);
		var img_ext = ext_exp.exec(img.attr('src'))[1];
		var new_src = img.attr('src').replace(ext_exp,'');
		var time = new Date().getTime();
		$('#step_4 #thumbstrip ul').append('<li class="thumb"><div><a href="#"><img src="'+new_src+'-t'+img_ext+'?ts='+time+'" data-angle="'+$(this).data('angle')+'" /></a></div></li>');
	});
	if ($('#step_4 .featured .img_holder img').length == 0)
		$('#step_4 .featured .img_holder ').append('<img />');
	
	if ($('#photo_grid').length == 0) {
		if($('#place_form').is('.edit'))
			$('#step_4 .featured .img_holder img').attr('src', '/img/no_photo_ios_edit.jpg');
		else
			$('#step_4 .featured .img_holder img').attr('src', '/img/no_photo_uploaded_mobile.jpg');
		$('#step_4 #thumbstrip ul').append('<li class="thumb noactive"><a href="#step_4" class="no_photo"><img src="/img/upload/no_photo_uploaded_thumb.gif" /></a></li>');
		$('a#edit_photos').css({'visibility':'hidden'});
	}
	else if ($('#photo_grid li img').length == 0) {
		$('#step_4 .featured .img_holder img').attr('src', '/img/no_photo_uploaded.jpg');
		$('#step_4 #thumbstrip ul').append('<li class="thumb"><a href="#step_2" class="add_photo edit_button"><img src="/img/add_a_photo.gif" /></a></li>');
	}
	else {
		var img = $('#step_4 #thumbstrip li img:first');
		var ext_exp = new RegExp(/-t(\.\w+)(\?.+)?/);
		var img_ext = ext_exp.exec(img.attr('src'))[1];
		var new_src = img.attr('src').replace(ext_exp,'');
		var time = new Date().getTime();
		$('#step_4 .featured .img_holder img').attr('src',new_src+'.jpg?ts='+time);
		//imageSize();
	}

	$('#step_4 #thumbstrip li.thumb:not(.noactive):first').addClass('active');
	//$('#step_4 .img_holder').css({'marginTop':0});	
	$('#step_4 #thumbstrip ul').removeClass().addClass('images-'+$('#photo_grid li img').length);
	
	//$('#paypal_form input[name=item_name]').val('AdShop Ad placement for '+$('a.place.tick').attr('rel')+' Months');
	//var amount = $('a.place.tick').attr('rel') == '2' ? '2' : '3';
	//$('#paypal_form input[name=amount]').val(amount);

	thumbStrip();
}

function loginForm() {
	//$('#login_username').focus();
	$('#login_form').validate();
	
	var do_login = function(e) {
		if($('#login_password').val() == '') {
			$('body').css('cursor','progress');
			$.post('/request/reset_password',{'email':$('#login_username').val()},function(data,status) {
				$('body').css('cursor','');
				var respObj = JSON.parse(data);
				//alert(respObj.content);
				$('#container').prepend('<p class="success">'+respObj.content+'</p>');
				$('#footer').prevUntil('.error,.success').remove()
			});
		}
		else if($('#login_form').valid())
			$('#login_form').submit();
	};
	
	$('#login_button').click(function(e) {
		e.preventDefault();
		do_login();
	});
	$('#login_password').keyup(function(e){
		if (e.keyCode == '13')
			do_login();
	});
}

function passwordForm() {
	$('#new_password').focus().attr('name','new_password'+Math.round(Math.random()*1000));
	$('#password_form').validate();
	
	var do_update = function(e) {
		if($('#password_form').valid()) {
			$('#new_password').attr('name','new_password');
			$('#password_form').submit();
		}
	};
	
	$('#change_password_button').click(do_update);
	$('#new_password').keyup(function(e){
		if (e.keyCode == '13')
			do_update();
	});
}

function profileForm() {
	$('#profile_form').validate();
	
	var save_profile = function(e) {
		e.preventDefault();
		if($('#profile_form').valid())
			$('#profile_form').submit();
	};
	
	$('#save_button').click(save_profile);
}

/*
function viewAdForm() {
	$('#view_form').validate();
	
	var do_login = function(e) {
		if($('#view_password').val() == '') {
			$('#view_login_button').addClass('disabled').html('<span>Please wait...</span>');
			$.post(debug_domain+'/request/reset_password',{'email':$('#view_email').val()},function(data,status) {
				var respObj = JSON.parse(data);
				tip_response = '<div class="inner"><p>'+respObj.content+'</p></div>';
				$('#tiptip_holder').hide();
				$('#tip3').trigger('click');
			});
		}
		else if($('#view_form').valid())
			$('#view_form').submit();
	};
	
	$('#view_login_button').click(function(e) {
		e.preventDefault();
		do_login();
	});
	$('#view_password').keyup(function(e){
		if (e.keyCode == '13')
			do_login();
	});
}
*/

function toggleSelect(sel) {
	sel.next('.options_details').slideToggle(400,function(){
		if(sel.attr('id') == 'footer_county_options')
			window.location.href = '#footer';
	});
}

function wordCount(e,maxlen) {
	//var words = $(e.target).val().split(' ').length;
	var words = $(e.target).val().match(/\w+/g);
	if(words != null) {
		if(words.length > maxlen) {
			e.stopPropagation();
			alert('Field can have up to '+maxlen+' words.');
			$(e.target).val($(e.target).val().substr(0,$(e.target).val().length-1));
		}
		else if(words.length != 0)
			$(e.target).next('label').text(maxlen-words.length+' words remaining.');
	}
	else
		$(e.target).next('label').text(maxlen+' words max.');
}

function charCount(e,maxlen) {
	var chars = $(e.target).val().match(/./g);
	if(chars != null) {
		if(chars.length > maxlen) {
			e.stopPropagation();
			alert('Field can have up to '+maxlen+' characters.');
			$(e.target).val($(e.target).val().substr(0,$(e.target).val().length-1));
		}
		else if(chars.length != 0)
			$(e.target).next('label').text(maxlen-chars.length+' characters remaining.');
	}
	else
		$(e.target).next('label').text(maxlen+' characters max.');
}

function getSubcategories(cat) {
	$('#subcategory_options').parent().after('<div class="select_loader"></div>');
	$.ajax({url:'/request/subcat/'+cat,success:
		function(resp) {
			var respObj = JSON.parse(resp);
			var subcat_content = '<option value="">Choose</option>';
			if($.isEmptyObject(respObj.content)) {
				$('#item_subcategory').addClass('disabled');
				$('#subcategory_options').attr('disabled','disabled');
			}
			else {
				$('#item_subcategory').removeClass('disabled');
				$('#subcategory_options').removeAttr('disabled');
			}
			$.each(respObj.content, function() {
				subcat_content += '<option value="'+this.id+'">'+this.title+'</option>';
			});
			$('#subcategory_options').html(subcat_content).prev('.select_container').find('.subname').text('Choose');
			//$('.options_details').find('.item:first').css({'borderTop':'none'});
			
			$('#subsubcategory_options').parents('.formitem:first').addClass('hide');
			
			$('.select_loader').remove();
			
			//toggleErrors();
		}
	});
}

function getSubsubcategories(subcat) {
	$('#subsubcategory_options').parent().after('<div class="select_loader"></div>');
	$.ajax({url:'/request/subsubcat/'+subcat,success:
		function(resp) {
			var respObj = JSON.parse(resp);
			var subsubcat_content = '<option value="">Choose</option>';
			$.each(respObj.content, function() {
				subsubcat_content += '<option value="'+this.id+'">'+this.title+'</option>';
			});
			$('#subsubcategory_options').html(subsubcat_content).prev('.select_container').find('.subname').text('Choose');
			//$('.options_details').find('.item:first').css({'borderTop':'none'});
			$('#item_subsubcategory').removeClass('disabled');
			$('#subsubcategory_options').removeAttr('disabled');
			
			if (!$.isEmptyObject(respObj.content)) {
				//console.log(subcat);
				$('#subsubcategory_options').parents('.formitem:first').removeClass('hide');
				$('#subsubcategory_label').text(respObj.label+':');
				if(subcat == '12') // 12 == dogs
					$('#item_crossbreed').closest('.field_options').removeClass('hide');
			}
			else 
				$('#subsubcategory_options').parents('.formitem:first').addClass('hide');
				
			$('.select_loader').remove();
			
			//toggleErrors();
		}
	});
}

function relativeCenter(container,div) {
	var diff = Math.max(0,Math.floor($(container).width()/2) - Math.ceil($(div).width()/2));
	div.css({'marginLeft':diff});
}

function verticalCenter(container,div) {
	var diff = Math.max(0,Math.floor($(container).height()/2) - Math.ceil($(div).height()/2));
	div.css({'marginTop':diff});
}

function basename(path, suffix) {
	var b = path.replace(/^.*[\/\\]/g, '');
	//b = b.replace(/\?&?.+=.+/g, '');
	
	if (typeof(suffix) == 'string' && b.substr(b.length-suffix.length) == suffix)
	    b = b.substr(0, b.length-suffix.length);
	
	return b;
}

function number_format (number, decimals, dec_point, thousands_sep) {
	number = (number + '').replace(',', '').replace(' ', '');
	var n = !isFinite(+number) ? 0 : +number,
	    prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
	    sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
	    dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
	    s = '',
	    toFixedFix = function (n, prec) {
	        var k = Math.pow(10, prec);
	        return '' + Math.round(n * k) / k;
	    };
	// Fix for IE parseFloat(0.55).toFixed(0) = 0;
	s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
	if (s[0].length > 3) {
	    s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
	}
	if ((s[1] || '').length < prec) {
	    s[1] = s[1] || '';
	    s[1] += new Array(prec - s[1].length + 1).join('0');
	}
	return s.join(dec);
}

function url_title(title) {
	var separator = '-';

	// Remove all characters that are not the separator, a-z, 0-9, or whitespace
	title = title.replace(/[^-a-z0-9\s]+/ig, '');

	// Replace all separator characters and whitespace by a single separator
	title = title.replace(/[-\s]+/g, separator);

	// Trim separators from the beginning and end
	return title.trim().toLowerCase();
}

if (!window.console) {
    window.console = {
        log: function() {},
        error: function() {},
        warn: function() {}
    };
}

$(document).ajaxError(function(e, req, options, exception) {
	$('.error,.success').remove();
	$('#container').prepend('<p class="error">(' + req.status + ') '+req.statusText+'</p>');
	console.log(req);
});

$(document).ajaxComplete(function(e, req, options) {
	$('p.error').delay(5000).fadeOut('fast');
});

$.fn.preload = function() {
    this.each(function(){
        $('<img/>')[0].src = '/img/'+this;
    });
};

jQuery.extend(jQuery.expr[':'], {
    focus: function(element) { 
        return element == document.activeElement; 
    }
});