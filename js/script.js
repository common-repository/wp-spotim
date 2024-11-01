jQuery(document).ready(function($) {
	$('.spotim-add-new').click(function(e) {
		e.preventDefault();
		var data = {
			action: 'spotim',
			spotim_action: 'new',
			spotim_new_nonce: $(this).attr('data-nonce')
		};
		$.post(ajaxurl, data, function(response) {
			get_spots();
		});
		
	});
	
	$('.spotim_table').on('click', '.spotim-buttons button, .delete-spot, .preview-spot', function(e) {
		e.preventDefault();
		//edit spot
		if($(this).attr('data-action') == 'update') {
			var rules = [];
			var x = 0;
			$.each($(this).parents('td').find('.spot-rules-box'), function() {
				rules[x] = {
						spotim_rules: $(this).find('.spotim_rules').val(),
						spotim_rules_equal: $(this).find('.spot-rules-equal input').val(),
						spotim_sub_rules: $(this).find('.spotim_sub_rules').val()
				};
				x++;
			});
			var data = {
				action: 'spotim',
				spotim_action: 'update',
				spotim_id: $(this).attr('data-value'),
				spotim_title: $(this).parents('td').find('input.spotim_title').val(),
				spotim_content: $(this).parents('td').find('textarea.spotim_textarea').val(),
				spotim_rules: rules,
				spotim_update_nonce: $(this).attr('data-nonce')
			};
			$.post(ajaxurl, data, function(response) {
				var spot_id_title = $('tr.spot-id-'+data.spotim_id).find('.spotim-spot-show .row-title');
				if(spot_id_title.text() !== data.spotim_title) {
					spot_id_title.fadeOut(500, function() {
						spot_id_title.text(data.spotim_title).fadeIn(500);
					});
				}
				var spot_id_status = $('tr.spot-id-'+data.spotim_id).find('.ajax-status');
				if(spot_id_status.length) {
					spot_id_status.html('Spot is Update');
					spot_id_status.fadeIn('fast', function() {
						spot_id_status.fadeOut(4000);
					});
				}
			});
		}
		//delete spot
		if($(this).attr('data-action') == 'delete') {
			var data = {
				action: 'spotim',
				spotim_action: 'delete',
				spotim_id: $(this).attr('data-value'),
				spotim_delete_nonce: $(this).attr('data-nonce')
			};
			$.post(ajaxurl, data, function(response) {
				$('tr.spot-id-'+data.spotim_id).remove();
			});
		}
		
		//preview spot
		if($(this).attr('data-action') == 'preview') {
			if($('body').find('.spotim-preview-box').length) {
				$('body').find('.spotim-preview-box').html("");
			}
			$('body').append('<div class="spotim-preview-box">'+$(this).parents('td').find('textarea.spotim_textarea').val()+"</div>");
		}
		
	});
	
	$('.spotim_table').on('change', '.spotim_rules', function() {
		var sub_rules = $(this).parents('.spot-rules-box').find('.spot-sub-rules');
		var data = {
			action: 'spotim',
			spotim_action: 'rules',
			spotim_id: $(this).attr('data-value'),
			spotim_rules_nonce: $(this).attr('data-nonce'),
			spotim_rules_by: $(this).val()
		};
		$.post(ajaxurl, data, function(response) {
			sub_rules.html(response);
			sub_rules.parents('.spot-rules-box').find('.spot-rules-equal button').eq(0).click();
			if(response !== '') {
				sub_rules.parents('.spot-rules-box').find('.spot-rules-equal').removeClass('spot-rules-equal-hidden');
			}else{
				sub_rules.parents('.spot-rules-box').find('.spot-rules-equal').addClass('spot-rules-equal-hidden');	
			}
		});
	});
	
	$('.spotim_table').on('click', '.spot-rules-equal button', function() {
		if(!$(this).hasClass('active')) {
			$(this).siblings('button').removeClass('active');
			$(this).addClass('active');
			$(this).siblings('input').val($(this).attr('data-value'));
		}
	});
	
	$('.spotim_table').on('click', '.spotim-spot-show .row-title, .spotim-spot-show .edit-spot', function(e) {
		e.preventDefault();
		$(this).parents('td').find('.spotim-spot-hidden').slideToggle();
	});
	
	function get_spots() {
		var data = {
			action: 'spotim',
			spotim_action: 'get_spots',
			get_spots_nonce: $('body').find('input[name="spotim_nonce"]').val()
		};
		$.post(ajaxurl, data, function(response) {
			$('.spotim_table tbody').html(response);
		});
	}
	
	get_spots();
});