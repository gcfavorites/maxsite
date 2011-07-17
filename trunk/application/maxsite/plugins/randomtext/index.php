<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
 
 
# функция автоподключения плагина
function randomtext_autoload($args = array())
{
	# регистрируем виджет
	mso_register_widget('randomtext_widget', t('Цитаты', 'plugins')); 
}

function randomtext_uninstall($args = array())
{	
	mso_delete_option_mask('randomtext_widget_', 'plugins'); // удалим созданные опции
	return $args;
}

# функция виджета
function randomtext_widget($num = 1)
{
	$widget = 'randomtext_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	// заменим заголовок, чтобы был в  h2 class="box"
	if ( isset($options['header']) and $options['header'] ) 
		$options['header'] = mso_get_val('widget_header_start', '<h2 class="box"><span>') . $options['header'] . mso_get_val('widget_header_end', '</span></h2>');
	else $options['header'] = '';
	
	return randomtext_widget_custom($options, $num);
}

# функция вывода формы настройки виджета
function randomtext_widget_form($num = 1)
{
	$widget = 'randomtext_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = '';
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	$form = '<p><div class="t150">' . t('Заголовок:', 'plugins') . '</div> '. 
			form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ) ;
	
	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function randomtext_widget_update($num = 1) 
{
	$widget = 'randomtext_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins');
}


# основная функция
function randomtext_widget_custom($arg = array(), $num = 1)
{
	if ( !isset($arg['header'])) $arg['header'] = mso_get_val('widget_header_start', '<h2 class="box"><span>') . t('Цитата', 'plugins') .mso_get_val('widget_header_end', '</span></h2>') ;
	if ( !isset($arg['block_start']) ) $arg['block_start'] = '<div class="random-text">';
	if ( !isset($arg['block_end']) ) $arg['block_end'] = '</div>';
	
	$text = '';
	
	# если есть ушка randomtext, то берем текст из неё
	if (function_exists('ushka')) 
	{
		$text = ushka('randomtext', "\n");
	}
	
	if (!$text)
	{
		$text =
		'Армейская дисциплина тяжела, но это тяжесть щита, а не ярма. /А. Ривароль/
		Мораль — это важничанье человека перед природой. /Ф. Ницше/
		Пусть о себе мнит каждый, что хочет. /Овидий/
		Счастье есть лишь мечта, а горе реально. /Ф. Вольтер/
		Соперничество — пища для гения. /Ф. Вольтер/
		Повесть об этом стара, но слава нетленна. /Вергилий/
		Кто не знает, что такое мир, не знает, где он сам… /Марк Аврелий/
		Все понять — значит простить. /А. Сталь/
		Усердный врач подобен пеликану. /К. Прутков/
		Из всех плодов наилучшие приносит хорошее воспитание. /К. Прутков/
		Доблесть милее вдвойне, если доблестный телом прекрасен. /Вергилий/
		Ум всегда в дураках у сердца. /Ф. Ларошфуко/
		Молчание — добродетель дураков. /Ф. Бэкон/
		Плакать готова как раз оттого, что не над чем плакать. /Овидий/
		Предосторожность проста, а раскаяние многосложно. /И. Гёте/
		Дайте человеку цель, ради которой стоит жить, и он сможет выжить в любой ситуации. /Иоганн Вольфганг Гёте/
		Замужество открывает женщинам глаза на саму себя. /Кузьма Чорный/
		Функция литературы — превращать события в идеи. /Д. Сантаяна/
		Сочинять не так уж трудно, труднее всего — зачеркивать лишние ноты. /Брамс/
		Страх — всегдашний спутник неправды. /В. Шекспир/
		Тень живет лишь при свете. /Ж. Ренар/
		Уразумей, чтобы уверовать. /Августин/
		Все — суета сует и томление духа. (Все — суета сует и ловля ветра) /Соломон/
		Человек — это процесс его поступков. /А. Грамши/
		Люди — это малые боги. /Г. Лейбниц/
		Мудрость — это ум, настоянный на совести. /Фазиль Искандер/
		Все свое я ношу с собой. /Ксенофан/
		Слово — тень дела. /Демокрит/
		Худших везде большинство. /Фалес/
		Сущности не следует умножать без необходимости. /Оккам/
		Новые взгляды сквозь старые щели. /Г. Лихтенберг/
		Жестокость законов препятствует их соблюдению. /Ш. Монтескье/
		Ни один льстец не льстит так искусно, как самолюбие. /Ф. Ларошфуко/
		Надежды — сны бодрствующих. /Платон/
		Людей мучают не вещи, а представления о них. /Эпиктет/
		Если хочешь быть красивым, поступи в гусары. /К. Прутков/
		Движущиеся тело и движение удовлетворительно не различимы. /Оккам/
		Кто может — грабит, кто не может — ворует. /Д. И. Фонвизин/
		Счастье подобно бабочке. Чем больше ловишь его, тем больше оно ускользает. Но если вы перенесете свое внимание на другие вещи, оно придет и тихонько сядет Вам на плечо. /В.Франкл (американский психотерапевт)/
		Большинство из нас — это не мы. Наши мысли — это чужие суждения; наша жизнь — мимикрия; наши страсти — цитата. /Оскар Уайльд/
		Все твои «беды» как летний снег: вот он есть, миг — и нет ничего, просто показалось… Не надо только все время тыкаться носом в прошлое или мечтать о том, каким бы могло стать будущее. /Макс Фрай/
		Человек, по-настоящему мыслящий, черпает из своих ошибок не меньше познания, чем из своих успехов. /Джон Дьюи/
		Человек лишь там чего — то добивается, где он сам верит в свои силы. /Людвиг Фейерах/
		Искусство быть мудрым состоит в умении знать, на что не следует обращать внимания. /Уильям Джеймс/
		Чем проще человек выражается, тем легче его понимают. /Фенимор Купер/
		Когда Бог создавал время, он создал его достаточно. /Ирландская поговорка/
		Это великолепные золотые часы на цепочке. Я горжусь ими. Их продал мне мой дедушка, когда лежал на смертном одре. /Вуди Аллен/';
	}
	
	$text = explode("\n", $text);
	$out = trim($text[ mt_rand(0, count($text) - 1) ] );
	
	return $arg['header'] . $arg['block_start'] . $out . $arg['block_end'];
}


# end file