(function($){

    FLBuilder.registerModuleHelper('widget', {

        init: function()
        {
            var form    = $('.fl-builder-settings'),
                missing = form.find('.fl-builder-widget-missing'),
								inputs = form.find('input');

						$.each(inputs, function(i,v){
							$(v).addClass('fl-ignore-validation');
						})
            if(missing.length > 0) {
                form.find('.fl-builder-settings-save, .fl-builder-settings-save-as').hide();
            }
        }
    });

})(jQuery);
