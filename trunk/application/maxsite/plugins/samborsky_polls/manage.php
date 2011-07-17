<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

	$CI = &get_instance();
	
	function sp_set_data(&$data){
		
		// $data['q_question'] = addslashes(trim($_POST['question']));
		$data['q_question'] = $_POST['question'];
		
		// Если вопрос не задан, выходим
		if( empty($data['q_question']) ){
			echo '<div class="samborsky_polls_error">Введите вопрос.</div>';
			return false;
		}
		
		// Защита голосования
		if( isset($_POST['q_protection']) ){
			$data['q_protection'] = $_POST['q_protection'];
		}
		
		// Дата начала голосования
		if( isset($_POST['beginDate']) && preg_match('/(\d{2})\/(\d{2})\/(\d{4})/',$_POST['beginDate'],$out) ){
			$data['q_timestamp'] = gmmktime(0,0,0,$out[1],$out[2],$out[3]);
		}
		else{			
			$data['q_timestamp'] = gmmktime(0,0,0,date("m"),date("d"),date("Y"));
		}
		
		// Это бессрочное голосование?
		if( !isset($_POST['noExpiry']) ){
			// Дата окончания голосования
			if( isset($_POST['expiryDate']) ){
				if( preg_match('/(\d{2})\/(\d{2})\/(\d{4})/',$_POST['expiryDate'],$out) ){
					$data['q_expiry'] = gmmktime(0,0,0,$out[1],$out[2],$out[3]);
				}
			}
		}
		else{
			$data['q_expiry'] = 0;
		}
		
		return true;
	}

	function q_insert(){
		
		$data = array();
		
		if( !sp_set_data($data) )
			return false;
		
		$CI = &get_instance();
		
		$question = new sp_question();
		if( $question->insert($data) ){
			
			$q_totalvotes = 0;
			// Добавляем варианты ответов
			foreach( $_POST as $key => $value ){
				
				if( preg_match('/sp_answer_(\d+)/',$key,$out) ){
					
					$answer = addslashes(trim($_POST[$key]));
					if( !empty($answer) ){
						
						$CI->db->insert('sp_answers',array(
							'a_answer' => $answer,
							'a_qid' => $question->id,
							'a_votes' => isset($_POST['sp_totalvotes_' . $out[1]]) ? $_POST['sp_totalvotes_' . $out[1]] : 0,
							'a_order' => isset($_POST['sp_voteorder_' . $out[1]]) ? $_POST['sp_voteorder_' . $out[1]] : 0
						));
						
						$q_totalvotes += isset($_POST['sp_totalvotes_' . $out[1]]) ? $_POST['sp_totalvotes_' . $out[1]] : 0;
					}
				}
			}
			
			$question->update(array('q_totalvotes' => $q_totalvotes));
			
			
			mso_redirect(getinfo('site_url') . 'admin/samborsky_polls/manage/' . $question->id, true);
			return true;		
		}
		else{
			echo '<div class="samborsky_polls_error">Ошибка при вставке данных в таблицу sp_questions.</div>';
		}
		
		return false;
	}
	
	function q_update(){

		if( is_numeric($id = mso_segment(4)) ){

			$data = array();

			if( !sp_set_data($data) )
				return false;
				
			$question = new sp_question($id);
			if( $question->update($data) ){
				
				echo '<div class="samborsky_polls_message">Голосование обновлено.</div>';
				
				$q_totalvotes = 0;
				// Обновляем ответы
				foreach( $_POST as $key => $value ){
					
					if( preg_match('/sp_answer_(\d+)/',$key,$out) ){
						
						$answer = new sp_answer($out[1]);
						
						// Вариант существует, обновляем
						if( $answer->get() ){
							$answer->update(array(
								'a_answer' => addslashes(trim($_POST[$key])),
								'a_qid' => $question->id,
								'a_votes' => isset($_POST['sp_totalvotes_' . $answer->a_id]) ? $_POST['sp_totalvotes_' . $answer->a_id] : 0,
								'a_order' => isset($_POST['sp_voteorder_' . $answer->a_id]) ? $_POST['sp_voteorder_' . $answer->a_id] : 0
							));
						}
						// ..., создаем
						else{
							$answer->insert(array(
								'a_answer' => addslashes(trim($_POST[$key])),
								'a_qid' => $question->id,
								'a_votes' => isset($_POST['sp_totalvotes_' . $answer->a_id]) ? $_POST['sp_totalvotes_' . $answer->a_id] : 0,
								'a_order' => isset($_POST['sp_voteorder_' . $answer->a_id]) ? $_POST['sp_voteorder_' . $answer->a_id] : 0
							));
						}
						
						$q_totalvotes += isset($_POST['sp_totalvotes_' . $answer->a_id]) ? $_POST['sp_totalvotes_' . $answer->a_id] : 0;
					}
				}
				
				// Устанавливаем значение q_totalvotes
				$question->update(array(
					'q_totalvotes' => $q_totalvotes
				));
			}
			else{
				echo '<div class="samborsky_polls_error">Не удалось обновить таблицу sp_questions.</div>';
			}
		}
	}
	
	function q_close(){
		
		if( is_numeric($id = mso_segment(4)) ){
			$question = new sp_question($id);
			$question->close();
		}
	}
	
	function q_open(){
		
		if( is_numeric($id = mso_segment(4)) ){
			$question = new sp_question($id);
			$question->open();
		}
	}

	// Создаем новое голосование
	if( isset($_POST['submit_create']) ){
		q_insert();
	}
	// Сохраняем голосование
	if( isset($_POST['submit_edit']) ){
		q_update();	
	}
	// Закрываем голосование
	else if( isset($_POST['submit_close']) ){
		q_close();
	}
	// Открываем голосование
	else if( isset($_POST['submit_open']) ){
		q_open();
	}
	
	$mode = mso_segment(5);
	$mode_id = mso_segment(6);
	
	// Удаляем
	if( 'delete' == $mode && is_numeric($mode_id) ){
		
		$CI = &get_instance();
		$CI->db->delete('sp_answers',array('a_id' => $mode_id));
	}
	
	$id = mso_segment(4);
	if( is_numeric($id) ){
		
		$question = new sp_question($id);
		$question->get();
	}

?>

<h1>Добавление/Изменение голосования</h1>

<form method="post">

	<fieldset>
		<legend><strong>Вопрос</strong></legend>
		<!--input type="text" name="question" size="100" value="<?= isset($question) ? htmlspecialchars($question->data->q_question) : '' ?>" -->
		<textarea name="question"><?= isset($question) ? htmlspecialchars($question->data->q_question) : '' ?></textarea>
	</fieldset>
	
	<fieldset>
		<legend><strong>Ответы</strong></legend>
		
		<table id="answers" cellspacing="8">
			<tbody>
			<tr class="nodrop nodrag">
				<th><strong>Порядок</strong></th>
				<th><strong>Текст ответа</strong></th>
				<th align="left"><strong>Голоса</strong></th>
			</tr>
			<?php
				// Это редактирование голосования?
				if( isset($question) ){
					
					$answer = new sp_answer();
					$answer_array = $answer->get_array($question->id);
					
					foreach( $answer_array as $row ){
						
						$delete_url = getinfo('site_url') . 'admin/samborsky_polls/manage/' . $question->id . '/delete/' . $row->a_id;

						echo 
						'<tr class="vote_content">	
							<td class="sp_count"></td>
							<td><input type="text" class="answer" name="sp_answer_',$row->a_id,'" value="',stripslashes($row->a_answer),'"></td>
							<td>
								<input type="hidden" class="vote_order" name="sp_voteorder_',$row->a_id,'">
								<input size="5" type="text" name="sp_totalvotes_',$row->a_id,'" value="',$row->a_votes,'">&nbsp;
								<a href="',$delete_url.'" onclick="return confirm(\'Удалить? Уверены?\');">Удалить</a>
							</td>
						</tr>';
					}
				}
				// Нет, это новое создается
				else{
					echo 
					'<tr>	
						<td class="sp_count"></td>
						<td><input type="text" class="answer" name="sp_answer_1"></td>
						<td><input size="5" type="text" name="sp_totalvotes_1" value="0"></td>
					</tr>
					<tr>	
						<td class="sp_count"></td>
						<td><input type="text" class="answer" name="sp_answer_2"></td>
						<td><input size="5" type="text" name="sp_totalvotes_2" value="0"></td>
					</tr>';
				}
			?>	
			</tbody>
		</table>
		
		<div style="padding: 5px; text-align: center;">
			<input type="button" id="add_answer" value="Добавить ответ">&nbsp;
			<input type="button" id="remove_answer" value="Убрать ответ">
			<br><br><br>
		</div>
		
	</fieldset>
	
	<fieldset>
		<legend><strong>Защита от накрутки</strong></legend>
		
		<select name="q_protection">
			<option value="2"<? if( isset($question) && 2 == $question->data->q_protection ) echo 'selected="TRUE"' ?>>Только для зарегистрированых (users)</option>
			<option value="1"<? if( isset($question) && 1 == $question->data->q_protection ) echo 'selected="TRUE"' ?>>Защита по Coookie</option>
			<option value="0"<? if( isset($question) && 0 == $question->data->q_protection ) echo 'selected="TRUE"' ?>>Без защиты, один пользователь может голосовать много раз</option>
		</select>
	</fieldset>
	
	<fieldset>
		<legend><strong>Дата начала/окончания голосования</strong></legend>
		
		<? if( isset($question) && !$question->data->q_active ) : ?>
		
			Голосование закрыто. Оно 
			
			<? if( $question->data->q_expiry ) : ?>
			проходило с <?= date('m/d/Y',$question->data->q_timestamp) ?> по <?= date('m/d/Y',$question->data->q_expiry) ?>
			<? else : ?>
			было бессрочным
			<? endif ?>
		
		<? else : ?>
		<script type="text/javascript">
		/* <![CDATA[ */
		
			$(document).ready(function(){
				
				var date_format = 'dd.mm.yy';
		
				$("#beginDate,#expiryDate").datepicker({ 
				    showOn: "both", 
				    buttonImage: "<?= getinfo('plugins_url') ?>samborsky_polls/css/calendar.png", 
				    buttonImageOnly: true,
					onChangeMonthYear: function($date){
						this.value = $.datepicker.formatDate(date_format,$date);
					}
				});
				
				$('#noExpiry').click(function(){					
					$('#noExpiry').prop('checked') ? $('#dateRange').hide() : $('#dateRange').show();
				});
				
				$('#noExpiry').prop('checked') ? $('#dateRange').hide() : $('#dateRange').show();
			});
			
		/* ]]> */	
		</script>
		
		
		<div id="dateRange" >	
			Начало: 
			<input type="text" id="beginDate" name="beginDate" value="<?= isset($question) ? date('m/d/Y',$question->data->q_timestamp) : date('m/d/Y') ?>">&nbsp;&nbsp;&nbsp;
			Окончание: 
			<input type="text" id="expiryDate" name="expiryDate" value="<?= ( isset($question) && $question->data->q_expiry ) ? date('m/d/Y',$question->data->q_expiry) : date('m/d/Y') ?>">
		<br><br></div>
		
		<div><label><input type="checkbox" name="noExpiry" id="noExpiry" <? if( isset($question) && !$question->data->q_expiry ) echo 'checked="true"' ?>>&nbsp;Бессрочное голосование</label></div>

		<? endif ?>
		
	</fieldset>
	
	<br><br><br>
	
	<input type="submit" name="<?= isset($question) ? 'submit_edit' : 'submit_create' ?>" value="Сохранить изменения">
	
	<? if( isset($question) && $question->data->q_active ) : ?>
	<input type="submit" name="submit_close" value="Закрыть голосование" onclick="return confirm('Закрыть голосование? Уверены?');">
	<? elseif( isset($question) && !$question->data->q_active ) : ?>
	<input type="submit" name="submit_open" value="Открыть голосование">
	<? endif; ?>
	
</form>

<script>
/* <![CDATA[ */	

	var count = <?= isset($question) ? $CI->db->count_all('sp_answers') : '2' ?>;

/* ]]> */	
</script>