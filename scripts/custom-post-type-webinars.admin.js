jQuery(document).ready(function() {

	Plugin = {

		init:function(){

			// Cache
			Plugin.webinarStart = jQuery('#webinar-date-start');
			Plugin.webinarStartTimeHour = jQuery('#webinar-from-hour');
			Plugin.webinarStartTimeMin = jQuery('#webinar-from-minute');
			Plugin.webinarEnd = jQuery('#webinar-date-end');
			Plugin.webinarEndTimeHour = jQuery('#webinar-to-hour');
			Plugin.webinarEndTimeMin = jQuery('#webinar-to-minute');
			Plugin.multiDay = jQuery('#webinar-multi-day');
			Plugin.multiDayWrapper = jQuery('.multi-day');
			Plugin.time = jQuery('#webinar-time');
			Plugin.timeWrapper = jQuery('.webinar-time');

			// Bootstrap
			Plugin.bindEvents();
		},

		bindEvents:function(){

			// Multi day
			Plugin.multiDayDisplay();
			Plugin.multiDay.change(function(){
				Plugin.multiDayDisplay();
			});

			// Time
			Plugin.timeDisplay();
			Plugin.time.change(function(){
				Plugin.timeDisplay();
			});

		},

		multiDayDisplay:function(){
			if ( Plugin.multiDay.prop('checked') ) {
				Plugin.multiDayWrapper.show();
				if ( '' === Plugin.webinarEnd.val() )
					Plugin.webinarEnd.val( Plugin.webinarStart.val() );
			} else {
				Plugin.multiDayWrapper.hide();
				Plugin.webinarEnd.val('');
			}
		},

		timeDisplay:function(){
			if ( Plugin.time.prop('checked') ) {
				if ( '' === Plugin.webinarStartTimeHour.val() )
					Plugin.webinarStartTimeHour.val('12');
				if ( '' === Plugin.webinarStartTimeMin.val() )
					Plugin.webinarStartTimeMin.val('00');
				if ( '' === Plugin.webinarEndTimeHour.val() )
					Plugin.webinarEndTimeHour.val('13');
				if ( '' === Plugin.webinarEndTimeMin.val() )
					Plugin.webinarEndTimeMin.val('00');
				Plugin.timeWrapper.show();
			} else {
				Plugin.timeWrapper.hide();
				Plugin.timeWrapper.find('input').val('');
			}
		}

	};

	Plugin.init();

});
