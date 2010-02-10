function sp_polls_vote(q_id){
	
	var data = 'q_id=' + q_id; 
	
	// Получаем результаты голосования
	$('.sp_question_' + q_id).each(function(i){
		
		if( true == $(this).attr('checked') ){
			
			data += '&a_id[]=' + $(this).val();
		}
	});
	
	// Отправляем POST запрос
	var ajax_path = $('#sp_ajax_path_' + q_id).val();
	
	if( ajax_path.length ){
		
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: ajax_path,
			data: data,
			beforeSend:
				function(){
			        $('#sp_polls_loader_' + q_id).show();
					$('#sp_polls_' + q_id).hide();
				},
			complete: 
				function(){
					$('#sp_polls_loader_' + q_id).hide();
					$('#sp_polls_' + q_id).show();
				},
			success:
				function(json,textStatus){
					
					// Если произошла ошибка
					if( 1 == json.error_code){
						alert(json.error_description);
					}
					else{
						$('#sp_polls_' + q_id).html( json.resp );
						$('#sp_polls_loader_' + q_id).hide();
						$('#sp_polls_' + q_id).show();
					}
				},
			error:
				function(){
					$('#sp_polls_' + q_id).show();
					$('#sp_polls_loader_' + q_id).hide();
					
					alert('Ошибка браузера.');
				},
		});
	}	
}
