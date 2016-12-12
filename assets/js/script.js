jQuery(document).ready(function ($) {
	
	var $field		= $('input:radio[name=event-type]');
	var $container	= $field.parent().parent().next('.form-row-toggle');
	
	$field.click(function() {
		
        if (this.value == 'private') {
			
			$container.addClass('form-row-toggle-hide');
			$container.removeClass('form-row-toggle-display');
			
			$container.children('#event-location').removeAttr('required');
			
        } else if (this.value == 'public') {
			
			$container.addClass('form-row-toggle-display');
			$container.removeClass('form-row-toggle-hide');
			
			$container.children('#event-location').attr('required', true);

        }
		
    });
	
});