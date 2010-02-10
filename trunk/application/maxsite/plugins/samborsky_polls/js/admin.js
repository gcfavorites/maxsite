function recount(){
	
	var _i = 1;
	
	$('.sp_count').each(function(i){
		$(this).html(_i++);
	});
}

function reorder(){
	
	var _i = 1;
	
	$('.vote_order').each(function(i){
		$(this).val(_i++);
	});
}


$(document).ready(function(){
	
	recount(); 
	reorder();
	
	$("#answers").tableDnD({
		onDragClass: "sp_drag_class",
		onDrop: function(table, row){
			recount();
			reorder();
		}
	});
	
	$('#add_answer').click(function(){
		
		count++;
		
		$('#answers').append(
			'<tr id="sp_row_' + count + '">' +	
				'<td class="sp_count"></td>' +
				'<td><input type="text" class="answer" name="sp_answer_' + count + '" /></td>' +
				'<td><input size="5" type="text" name="sp_totalvotes_' + count + '" value="0" /></td>' +
			'</tr>'
		);	
		
		recount();
	});
	
	$('#remove_answer').click(function(){
		
		if( count <= 2 ){
			
			alert('Меньше двух ответов быть не может.');
			return false;
		}
		
		if ($('#sp_row_' + count).find('.answer').val() == '') {
			
			$('#sp_row_' + count).remove();
			count--;
			
			recount();
		}
	});


});