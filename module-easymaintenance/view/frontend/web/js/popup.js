require([
	'jquery',
	'jquery/ui',
	'jquery/validate',
	'mage/translate',
	'Magento_Ui/js/modal/alert'
], function(jQuery, translate, alert){
	
	openFeedbackWindow = function  (ele,upd_ele,id)
	{
		jQuery('#'+ele).fadeIn();
		jQuery('#backgroundpopup').css('display', 'block');
	}

	closeFeedbackWindow = function (ele1){
		jQuery('#'+ele1).fadeOut();
		jQuery('#backgroundpopup').fadeOut();
		jQuery('#div.error-massage').each(function(ele){
			ele.hide();
		});
	}

	sendFeedback = function(url) {
		jQuery('#frm_feedback').submit(function(event) {
			event.preventDefault();
			var feedback_form = jQuery('#frm_feedback').validate();

			if( feedback_form && feedback_form.valid() ){
				jQuery('#loader').show();
				jQuery('#btnsubmit').attr('disabled', true);
				var parameters = jQuery('#frm_feedback').serialize();

				jQuery.ajax({
					url: url,
					type: 'POST',
					dataType: 'json',
					data: parameters,
				})
				.done(function(data, transport) {
					if( transport == 'success' ){
						jQuery('#success_message').html( data.message );

						if( data.result == 'success' ){
							jQuery('#success_message').removeClass('feedback-error-msg');
							jQuery('#success_message').addClass('feedback-success-msg');
							jQuery('#btnsubmit').removeAttr('disabled');
						} else {
							jQuery('#success_message').removeClass('feedback-success-msg');
							jQuery('#success_message').addClass('feedback-error-msg');
							jQuery('#btnsubmit').removeAttr('disabled');
						}

						jQuery('#loader').hide();
						jQuery('#success_message').show().fadeIn();

						setTimeout(function(){
							closeFeedbackWindow('feedback_information');
							feedback_form.reset();
							jQuery('#btnsubmit').removeAttr('disabled');
						}, 6000);

						return false;
					}
				}).fail(function() {
					alert({
						content : 'Internal Server Error'
					});
				});
				return false;
			}
		});
		
	}


	easysiteNotify = function(notifyurl){
		jQuery('#frm_notify').submit(function(event) {
			event.preventDefault();

			var notify_form = jQuery('#frm_notify').validate();

			if( notify_form && notify_form.valid() ){
				var parameters = jQuery('#frm_notify').serializeArray();
				jQuery('#loader_notify').show();

				jQuery('#notifybtnsubmit').attr('disabled', true);
				jQuery.ajax({
					url: notifyurl,
					type: 'POST',
					dataType: 'json',
					data: parameters,
				})
				.done(function(data, transport) {
					if( transport == 'success' ){
						jQuery('#success_message_notify').html( jQuery.mage.__(data.message) );
						if( data.result == 'success' ){
							jQuery('#success_message_notify').removeClass('error-msg');
							jQuery('#success_message_notify').addClass('success-msg');
							jQuery('#notifybtnsubmit').removeAttr('disabled');
						} else {
							jQuery('#success_message_notify').removeClass('success-msg');
							jQuery('#success_message_notify').addClass('error-msg');
							jQuery('#notifybtnsubmit').removeAttr('disabled');
						}
						jQuery('#loader_notify').hide();
						jQuery('#success_message_notify').show().fadeIn();
						setTimeout(function(){
							// notify_form.reset();
							jQuery('#notifybtnsubmit').removeAttr('disabled');
						}, 6000);
						return false;
					}
				}).fail(function(data) {
					alert({
						content : 'Internal Server Error'
					});
				});
				return false;
			}
			
		});
	
	}

	window.openFeedbackWindow = openFeedbackWindow;
	window.closeFeedbackWindow = closeFeedbackWindow;
	
	window.easysiteNotify = easysiteNotify;
	window.sendFeedback = sendFeedback;
});
