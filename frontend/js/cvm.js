var command_running = false;

$(function(){
	$('.button-loader').click(function(){
		if(command_running === false)
		{
			$('.button-loader').addClass('disabled').click(function(event){
				event.preventDefault();
				event.stopPropagation();
				return false;
			});
			
			$(this).children('.button-icon').addClass('running')[0].src = "/images/loading.gif";
			command_running = true;
		}
	});
});
