var command_running = false;

$(function(){
	$('.clickable').click(function(event)
	{
		if($(this).data('url'))
		{
			url = $(this).data('url');
			
			if(event.which == 1)
			{
				if($(this).hasClass('external'))
				{
					window.open(url);
				}
				else
				{
					window.location = url;
				}
				
				event.stopPropagation();
				return false;
			}
			else if(event.which == 2)
			{
				window.open(url);
				event.stopPropagation();
				return false;
			}
		}
	});     
	
	
	$('.button-loader').click(function(){
		if(command_running === false)
		{
			$('.button-loader').addClass('disabled').click(function(event){
				event.preventDefault();
				event.stopPropagation();
				return false;
			});
			
			$(this).children('.button-icon').addClass('running')[0].src = "/templates/default/static/images/misc/loading.gif";
			command_running = true;
		}
	});
	
	$('.enabler').change(function(){
		var group = $(this).data("enable-group");
		
		if($(this).is(':checked'))
		{
			$('.disabled').each(function(index, item){
				if($(item).data("disabled-group") == group)
				{
					$(item).children('input').removeAttr('disabled');
					$(item).removeClass('disabled');
				}
			});
		}
		else
		{
			$('form .field').each(function(index, item){
				if($(item).data("disabled-group") == group)
				{
					$(item).children('input').attr('disabled', 'disabled');
					$(item).addClass('disabled');
				}
			});
		}
	})
});
