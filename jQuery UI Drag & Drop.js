jQuery(document).ready(function($){

	//auto sort selected member
	

	//move members
	$('.move-members').click(function(){
		$('ul.memeber-list li').each(function(i){
			if($(this).find('input').attr('checked')){
				$('.selected-member-list').append($(this).clone());
				$(this).remove();
			}
		});
	});

	//remove selected members
	$('.member-remove').click(function(){
		var id = $(this).data('id');
		$(this).parent('#'+id).remove();
	});
	$('.selected-member-list').sortable();
	$('.selected-member-list').disableSelection();
});
