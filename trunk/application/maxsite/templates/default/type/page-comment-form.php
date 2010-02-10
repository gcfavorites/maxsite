<?php if (!defined('BASEPATH')) exit('No direct script access allowed');?>

<?php

// pr($MSO);

?>

<div class="comment-form">
	<form action="" method="post">
		<input type="hidden" name="comments_page_id" value="<?= $page_id ?>" />
		<?= mso_form_session('comments_session') ?>
		
		<?php  if (!is_login()) { ?>
		
			<?php if (! $comuser = is_login_comuser()) { ?>
			<div class="comments-noreg">
				<input type="radio" name="comments_reg" id="comments_reg_1" value="noreg"  checked="checked" class="no-margin"" /> <span class="black">Не регистрировать/аноним</span> <br />
				
				<label for="comments_author">Ваше имя</label>
				<input type="text" name="comments_author" class="text" onfocus="document.getElementById('comments_reg_1').checked = 'checked';" />
				<p style="margin: 10px 0 0 0;"><span>Используйте нормальные имена. Ваш комментарий будет опубликован после проверки.</span></p>
			</div>		
			
			<div class="comments-reg">
				<input type="radio" name="comments_reg" id="comments_reg_2" value="reg" class="no-margin" /> <span class="black">Если вы уже зарегистрированы как комментатор или хотите зарегистрироваться, укажите пароль и свой действующий email. <br />(<i>При регистрации на указанный адрес придет письмо с кодом активации и ссылкой на ваш персональный аккаунт, где вы сможете изменить свои данные, включая адрес сайта, ник, описание, контакты и т.д.</i>)</span><br />
				
				<label for="comments_email">E-mail</label>
				<input type="text" name="comments_email" value="" class="text" onfocus="document.getElementById('comments_reg_2').checked = 'checked';" /><br />

				<label for="comments_password">Пароль</label>
				<input type="password" name="comments_password" value="" class="text" onfocus="document.getElementById('comments_reg_2').checked = 'checked';" /><br />
			</div>
			<?php  } else { // comusers?>
				
				<input type="hidden" name="comments_email" value="<?= $comuser['comusers_email'] ?>" />
				<input type="hidden" name="comments_password" value="<?= $comuser['comusers_password'] ?>" />
				<input type="hidden" name="comments_password_md" value="1" />
				<input type="hidden" name="comments_reg" value="reg" />
				
				<div class="comments-user comments-comuser">
					Привет, <?= $comuser['comusers_nik'] ?>!
				</div>
			
			<?php  } ?>
			
		<?php  } else { // users?>
			<input type="hidden" name="comments_user_id" value="<?= getinfo('users_id') ?>" />
		
			<div class="comments-user">
				Привет, <?= getinfo('users_nik') ?>!
			</div>
		
		<?php  } ?>
		
		<div class="comments-textarea">
			
			<label for="comments_content">Ваш комментарий</label>
			<?php mso_hook('comments_content_start')  ?>
			<textarea name="comments_content" id="comments_content" rows="20"></textarea>

			<?php mso_hook('comments_content_end')  ?>
			
			<div><input name="comments_submit" type="submit" value="Отправить" class="comments_submit" /></div>
		</div>
		
	</form>
</div><!-- div class=comment-form -->
