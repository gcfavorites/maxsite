<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * Language file
 * (c) http://max-3000.com/
 * Author: (c) Александр Шиллинг
 * Author URL: http://www.thedignity.biz
 * Update URL: http://thedignity.biz/maxsite-cms
 */
 
$lang['Доступ запрещен'] = 'Unberechtigt! Sie sind nicht bevollmächtigt';
$lang['Сохранить изменения'] = 'Veränderungen gespeichert';
$lang['Обновлено!'] = 'Aktualisiert!';
$lang['Заголовок:'] = 'Titel:';
$lang['Формат:'] = 'Format:';
$lang['Сортировка:'] = 'Sortieren:';
$lang['Обновлено!'] = 'Aktualiesiert!';
$lang['Админ-доступ к настройкам'] = 'Admin-Zugriff zur Einstellungen';
$lang['Админ-доступ к настройкам '] = 'Admin-Zugriff zur Einstellungen ';


# admin_ip
$lang['Обновлено! Обязательно сохраните секретный адрес сейчас!'] = 'Aktualisiert! Speichern Sie bitte Ihre Sicherheits-URL jetzt ab <strong>now</strong>';
$lang['Admin IP'] = 'Admin IP';
$lang['Вы можете указать IP с которых разрешен доступ в админ-панель. Если пользователь попытается войти в панель управления с другого IP, то ему будет отказано в доступе.'] =
'You can specify IP, via which you allow access to admin-panel. If user try to login from other IP — he get access denied.';
$lang['На тот случай, если у администратора сменится IP, следует указать секретный адрес (URL), по которому можно очистить список разрешенных IP. Сохраняйте этот секретный адрес в надежном месте. В случае, если вы его забудете у вас не будет другой возможности, кроме как отключить плагин (удалить его файлы) или вручную исправить базу данных.'] =
'Upon that event if beside admin is changed IP, follows to indicate the secret address (URL), over which possible clear the list allowed(permitted) by IP. Save this secret address to reliable place. If and when you will forget beside it you will not be other possibility, except hang up plugin (delete(remove) its files or manually correct the database.';
$lang['Если секретный адрес не указан, то сбросить список будет невозможно.'] = 'Wenn die Sicherheits-URL nicht eingeben ist, kann man die Liste nicht zurücksetzten.';
$lang['Если список IP пуст, то доступ в админ-панель разрешен с любого IP.'] = 'Wenn die liste mit IP leer ist, zugriff erlauben mit alle IP';
$lang['Следует указывать только цифры и английские буквы. Другие символы не допустимы!'] = 'Es sind nur Buchstaben und Zahle erlaubt.';
$lang['Текущий адрес:'] = 'Aktuelle URL';
$lang['Укажите разрешенные IP по одному в каждой строчке'] = 'Liste der erlaubte IP — eins pro Zahle';
$lang['Ваш текущий IP:'] = 'Ihre aktuelle IP:';
$lang['Будьте внимательны! Обязательно указывайте свой текущий IP!'] = 'Vorsicht! geben Sie unbedingt Ihre Aktuelle IP ein';
$lang['Админ-доступ к редактированию разрешенных IP'] = 'Admin Zugriff zum Ändern der erlaubte IP';

# antispam
$lang['Админ-доступ к antispam'] = 'Admin zugriff zur ANTISPAM';
$lang['Антиспам'] = 'Antispam';
$lang['Для вашего IP комментирование запрещено!'] = 'Kommentare für Ihre IP sind nicht erlaubt';
$lang['Вы используете запрещенные слова!'] = 'Sie benutzen verbotene Wörter!';
$lang['С помощью этого плагина вы можете активно бороться со спамерами. Обратите внимание, что комментарии авторов публикуются без модерации.'] =
'Mit diesem Plugin können Sie Gut Spam bekämpfen. Bitte beachten Sie das die Kommentare von Autor ohne Überprüfung publiziert werden';
$lang['Настройки'] = 'Einstellungen';
$lang['Включить антиспам'] = 'Antispam einschalten';
$lang['Вести лог отловленных спамов'] = 'Ein Spam-log führen';
$lang['Отправлять комментарий на модерацию, если в нем встречается, хоть одна ссылка.'] = 'Kommentar zur Überprüfung senden, sobald ein Link in Kommentar vorkommt.';
$lang['Файл для логов:'] = 'Datei für log';
$lang['Посмотреть'] = 'Anzeigen';
$lang['Черный список IP'] = 'Schwarze Liste IP';
$lang['Укажите IP, с которых недопустимы комментарии. Один IP в одной строчке.'] = 'Speichere hier die IP, Kommentar dessen gesperrt wurden. Ein IP auf eine Linie.';
$lang['Черный список слов'] = 'Schwarze liste der Wörter';
$lang['Укажите слова, которые нельзя использовать в комментариях. Одно слово в одной строчке.'] = 'Geben Sie die Wörter ein, die nicht erlaubt sind. Ein Wort pro Zahle.';
$lang['Слова для модерации'] = 'Wörter für Überprüfung';
$lang['Укажите слова, которые принудительно отравляют комментарий на премодерацию. Одно слово в одной строчке. Обратите внимание, что этот список проверяется только если пройдена проверка на Черные списки.'] =
'Geben Sie die Wörter ein, die erzwingen den Kommentar zur Moderation zu senden. Ein Wort pro Zeile. Bitte beachten Sie, das die Liste nur dann geprüft wird, wenn die Schwarze Liste Prüfung beendet ist.';
$lang['Номера комюзеров, которые всегда попадают в модерацию'] = 'Nummer der Komuser, die immer zur Überprüfung gesendet werden';
$lang['Укажите номера комюзеров, которые принудительно отравляют комментарий на премодерацию. Один номер в одной строчке. Обратите внимание, что этот список проверяется только если пройдена проверка на Черные списки.'] =
'Indicate the number an комюзеров, who by force send comments to moderation. One number at one line. Attention that this list is inspected only if checking is passed for Blacklists.';

# authors
$lang['Авторы'] = 'Autors';

#bbcode
$lang['Настройки плагина bbcode'] = 'Einstellungen des Plugins bbcode';
$lang['Укажите необходимые опции.'] = 'Wählen Sie die benötigte Optionen aus.';
$lang['Где использовать'] = 'Wo benutzen';
$lang['Укажите, где должен работать плагин'] = 'Geben Sie an, wo der Plugin arbeiten soll';
$lang['1||На страницах #2||В комментариях #3||На страницах и в комментариях'] = '1||Auf den Seiten #2||In den Kommentar #3||Auf den Seiten und in den Kommentar';
$lang['Конвертирует тэги BBCode в HTML. См.'] = 'Konvertiere BBCode-tags zu HTML. Schau';

# calendar
$lang['Календарь'] = 'Kalender';
$lang['Январь'] = 'Januar';
$lang['Февраль'] = 'Februar';
$lang['Март'] = 'März';
$lang['Апрель'] = 'April';
$lang['Май'] = 'Mai';
$lang['Июнь'] = 'Juni';
$lang['Июль'] = 'Juli';
$lang['Август'] = 'August';
$lang['Сентябрь'] = 'September';
$lang['Октябрь'] = 'Oktober';
$lang['Ноябрь'] = 'November';
$lang['Декабрь'] = 'Dezember';
$lang['Пн'] = 'montag';
$lang['Вт'] = 'dienstag';
$lang['Ср'] = 'mittwoch';
$lang['Чт'] = 'donnerstag';
$lang['Пт'] = 'freitag';
$lang['Сб'] = 'samstag';
$lang['Вс'] = 'sonntag';

# captcha
$lang['Введите нижние символы'] = 'Geben Sie die Symbole die unten stehen ein';
$lang['Защита от спама: введите только нижние символы'] = 'Spamschutz: geben Sie bitte nur die unten stehende Symbole ein';
$lang['(обязательно)'] = '(Pflicht)';

# catclouds
$lang['Облако рубрик'] = 'Rubrik Wolke';
$lang['Мин. размер'] = 'Min. Größe';
$lang['Макс. размер'] = 'Max. Größe';
$lang['Номер рубрики:'] = 'Nummer der Rubrik:';
$lang['Начало блока:'] = 'Anfang des blocks';
$lang['Конец блока:'] = 'Ende des blocks';
$lang['По количеству записей (обратно)'] = 'Nach der Anzahl der Einträge (rückwärts)';
$lang['По количеству записей'] = 'Nach der Anzahl der Einträge';
$lang['По алфавиту'] = 'Nach Alphabet';
$lang['По алфавиту (обратно)'] = 'Nach Alphabet (rückwärts)';

# category
$lang['Рубрики'] = 'Kategorie';
$lang['Например:'] = 'Beispiel:';
$lang['Формат текущей:'] = 'Aktuelle Format:';
$lang['Например:'] = 'Beispiel:';
$lang['Включить только:'] = 'Nur einschalten:';
$lang['Укажите номера рубрик через запятую или пробел'] = 'Wählen Sie die Nummer eines Rubrik, Trennen sie mit Komma oder Leerzeichen';
$lang['Исключить:'] = 'Auslassen:';
$lang['Если нет записей:'] = 'Wenn keine Einträge:';
$lang['Отображать рубрику (количество записей ведется без учета опубликованности)'] = 'Zeige Rubrik (der Zähler zählt die Publizierte Einträge nicht mit)';
$lang['Скрывать рубрику (количество записей ведется только по опубликованным)'] = 'Verstecke Rubrik (der Zähler zählt nur die Publizierte Einträge)';
$lang['По имени рубрики'] = 'Nach Name der Rubrik';
$lang['По ID рубрики'] = 'Nach ID der Rubrik';
$lang['По выставленному menu order'] = 'Nach dem menu';
$lang['Порядок:'] = 'Rein-folge';
$lang['Прямой'] = 'Direkt';
$lang['Обратный'] = 'Rückwärts';
$lang['Включать потомков:'] = 'Mit kinder';
$lang['Всегда'] = 'Immer';
$lang['Только если явно указана рубрика'] = 'Nur wenn die Rubrik ausgewählt ist';

# comment_button
$lang['Полужирный'] = 'Fett';
$lang['Курсив'] = 'Kursiv';
$lang['Подчеркнутый'] = 'Unterstrichen';
$lang['Зачеркнутый'] = 'Durchgestrichen';
$lang['Цитата'] = 'Zitat';
$lang['Код'] = 'Code';
$lang['Код или преформатированный текст'] = 'Code oder preformatted Text';

# down_count
$lang['Админ-доступ к настройкам счетчика переходов (Download count)'] = 'Admin zugriff zur Einstellungen des «Download Zähler»';
$lang['Счетчик переходов'] = 'Download Zähler';
$lang['Подсчет количества переходов по ссылке. Обрамите нужную ссылку в [dc]...[/dc]'] = 'Download Zähler. Umrahmen den Link Sie mit [dc]...[/dc]';
$lang['Данная ссылка доступна только со <a href="%s">страниц сайта</a>'] = ' This link enabled only from <a href="%s">pages of site</a>';
$lang['Запрещен переход по этой ссылке с чужого сайта'] = 'Übergang von Fremde Seite verboten';
$lang['Количество переходов'] = 'Anzahl der Übergänge';
$lang['С помощью этого плагина вы можете подсчитывать количество скачиваний или переходов по ссылке. Для использования плагина обрамите нужную ссылку в код [dc]ваша ссылка[/dc]'] =
'Mit diesem plugin könne Sie die Anzahl der Übergänge oder Anzahl der Downloads über einen Link Zählen, fügen Sie dafür diese Tags ein [dc]Ihr Link[/dc]';
$lang['Файл для хранения количества скачиваний:'] = 'Daetei wo die Information über die Downloads gespeichert wird';
$lang['Префикс URL:'] = 'Prefix URL';
$lang['ссылка'] = 'Link';
$lang['Запретить переходы с чужих сайтов'] = 'Übergang von andere Seiten verbieten';
$lang['Выводить в title реальный адрес'] = 'Zeige in Titel die reale Adresse';
$lang['Статистика переходов'] = 'Anzahl der Übergänge';
$lang['переходов'] = 'Übergänge';

# editor_dumb
$lang['Ссылка'] = 'Link';
$lang['Картинка'] = 'Bild';
$lang['Отрезать текст'] = 'Text schneiden';

# events
$lang['События'] = 'Ereignis:';
$lang['Формат даты:'] = 'Datum Format:';
$lang['Как это <a href="http://ru.php.net/date" target="_blank">принято в PHP</a>'] = 'Wie in <a href="http://php.net/date" target="_blank">php</a>';
$lang['Указывайте по одному событию в каждом абзаце в формате:'] = 'Ein Ereignis pro Zahle:';
$lang['<strong>дата</strong> в формате yyyy-mm-dd'] = '<strong>Datum</strong> in Format yyyy-mm-dd';
$lang['<strong>до</strong> - выводить событие до наступления N-дней'] = '<strong>Bis</strong> — zeige die Veranstaltungen vor N-Tagen';
$lang['<strong>после</strong> - выводить событие после прошествия N-дней'] = '<strong>Nach</strong> — zeige die Veranstaltungen nach N-Tagen';
$lang['<strong>В тексте события</strong> можно использовать HTML'] = '<strong>In dem Text<strong> html erlauben';
$lang['<strong>ПРИМЕР:</strong> 2008-09-01 | 3 | 1 | Пора в школу!'] = '<strong>Beispiel:</strong> 2008-09-01 | 3 | 1 | Zeit für Schule!';
$lang['Понедельник'] = 'Montag';
$lang['Вторник'] = 'Dienstag';
$lang['Среда'] = 'Mittwoch';
$lang['Четверг'] = 'Donnerstag';
$lang['Пятница'] = 'Freitag';
$lang['Суббота'] = 'Samstag';
$lang['Воскресенье'] = 'Sonntag';
$lang['января'] = 'januar';
$lang['февраля'] = 'februar';
$lang['марта'] = 'märz';
$lang['апреля'] = 'april';
$lang['мая'] = 'mai';
$lang['июня'] = 'juni';
$lang['июля'] = 'juli';
$lang['августа'] = 'august';
$lang['сентября'] = 'september';
$lang['октября'] = 'oktober';
$lang['ноября'] = 'november';
$lang['декабря'] = 'dezember';
$lang['янв'] = 'jan';
$lang['фев'] = 'feb';
$lang['мар'] = 'mar';
$lang['апр'] = 'apr';
$lang['май'] = 'mai';
$lang['июн'] = 'jun';
$lang['июл'] = 'jul';
$lang['авг'] = 'aug';
$lang['сен'] = 'sep';
$lang['окт'] = 'okt';
$lang['ноя'] = 'nov';
$lang['дек'] = 'dez';

# faq
$lang['К списку'] = 'Zu der Liste';

# favorites
$lang['Избранное'] = 'Favoriten';
$lang['Ссылки:'] = 'Links';
$lang['Указывайте по одной ссылке в каждом абзаце в формате: <strong>тип/ссылка | название</strong>'] = 'Geben Sie jeweils einen Link in einer Zahle an in Format: <strong>Typ/Link | title</strong>';
$lang['<strong>тип/ссылка</strong> - указывается от адреса сайта, например'] = '<strong>Typ/link</strong> — vor Adresse der Seite, zum Beispiel';
$lang['Для главной страницы укажите: <strong> / | Главная</strong>'] = 'Für die Startseite benutzen Sie: <strong> / | Startseite</strong>';

# feedburner
$lang['Админ-доступ к feedburner'] = 'Admin zugriff zu feedburner';
$lang['Адрес вашего фида в FeedBurner.com:'] = 'URL von Ihrem feed in FeedBurner.com';
$lang['Плагин FeedBurner'] = 'Plugin FeedBurner';
$lang['Плагин выполняет перенаправление вашего основного rss на сервис feedburner.com.'] = 'Plugin überträgt Ihre rss zu feedburner.com Service.';

# feedburner_count
$lang['Настройка FeedBurner Count от samborsky.com'] = 'Einstellungen von FeedBurner Count von samborsky.com';
$lang['Ошибка!'] = 'Fehler!';
$lang['Настройка FeedBurner Count от <a href="http://www.samborsky.com/">samborsky.com</a>'] = 'Einstellungen von FeedBurner Count von <a href="http://www.samborsky.com/">samborsky.com</a>';
$lang['Здравствуйте, последний раз счетчик обновлялся'] = 'Hallo. letztes mal wurde der Zähler aktualisiert am';
$lang['Последнее показание счетчика:'] = 'Letzter Zählerstand:';

# feedcount
$lang['Виджет подсчета подписчиков RSS'] = 'Widget - Zähler von RSS Abo';
$lang['Сегодня:'] = 'Heute';
$lang['Вчера:'] = 'Gestern:';
$lang['[COUNT] - подписчиков сегодня, [COUNTOLD] - подписчиков вчера'] = '[COUNT] - abo heute, [COUNTOLD] - Abo gestern';

# forms
$lang['Неверный email!'] = 'Falscher E-mail';
$lang['Привет роботам! :-)'] = 'Hallo, Roboter! :-)';
$lang['Заполните все необходимые поля!'] = 'Fühlen Sie bitte alle Pflichtfelder aus!';
$lang['Вами отправлено сообщение:'] = 'Sie haben ein Nachricht gesendet:';
$lang['Ваше сообщение отправлено!'] = 'Ihre Nachricht wurde gesendet!';
$lang['Ваше имя*'] = 'Name*';
$lang['Ваш email*'] = 'E-mail';
$lang['Защита от спама:'] = 'Spamschutz';
$lang['Отправить копию письма на ваш e-mail'] = 'Sende eine Kopie an mein E-mail';
$lang['Отправить'] = 'Senden';
$lang['Очистить форму'] = 'Löschen';

# last_comments
$lang['Последние комментарии'] = 'Letzter Kommentar';
$lang['Количество:'] = 'Anzahl:';
$lang['Количество слов:'] = 'Anzahl der Wörter:';
$lang['Количество символов в одном слове:'] = 'Anzahl der Symbole in einem Wort:';
$lang['Комментатор'] = 'Kommentator';

# last_pages
$lang['Вывод последних записей'] = 'Zeige die letzte Seite';
$lang['Последние записи'] = 'Letzte Seite';
$lang['Формат даты:'] = 'Datum format:';
$lang['Формат комментариев:'] = 'Kommentar Format:';
$lang['Тип страниц:'] = 'Seiten Typ:';
$lang['Исключить рубрики:'] = 'Rubrik ausschalten:';
$lang['Включить рубрики:'] = 'Rubrik einschalten:';
$lang['Сортировка:'] = 'Sortieren:';
$lang['Порядок сортировки:'] = 'Sortierreinfolge:';
$lang['По дате'] = 'Nach Datum';
$lang['По алфавиту'] = 'Nach Alphabet';
$lang['Прямой'] = 'Direkt';
$lang['Обратный'] = 'Rückwärts';
$lang[' - комментариев: '] = ' — Kommentar: ';

# links
$lang['Ссылки'] = 'Links';
$lang['Указывайте по одной ссылке в каждом абзаце в формате:'] = 'Ein Link in einer Zahle in Format:';
$lang['обрамить ссылку в noindex, если не нужно - указать пробел'] = 'Link in noindex. Wenn nicht gebraucht — leerzeichen einfügen';
$lang['открыть ссылку в новом окне, если не нужно - указать пробел'] = 'Link in neuen Fenster. Wenn nicht gebraucht — Leerzeichen einfügen';

# login_form
$lang['Форма логина'] = 'Login Form';
$lang['Привет,'] = 'Hallo';
$lang['выйти'] = 'Abmelden';
$lang['управление'] = 'Admin-Panel';
$lang['своя страница'] = 'Eigene Seite';
$lang['Логин (email):'] = 'Anmelden (E-mail)';
$lang['Пароль:'] = 'Passwort:';

# page_comments
$lang['Самое комментируемое'] = 'Meist kommentiert';
$lang['Количество записей:'] = 'Anzahl der Einträge';
$lang['Формат:'] = 'Format:';
$lang['название записи'] = 'Überschrift des Eintrages';
$lang['количество комментариев'] = 'Anzahl der Kommentare';
$lang['ссылка'] = 'link';

# page_parent
$lang['Родительские/дочерние страницы'] = 'Eltern/Kinder Seiten';
$lang['Номер страницы:'] = 'Nummer der Seite:';

# page_views
$lang['Виджет «Самое читаемое»'] = 'Widget «Meist gelesen»';
$lang['Самое читаемое'] = 'Meist gelesen';
$lang['Тип записей:'] = 'Typ des Eintrages:';
$lang['просмотров в день'] = 'Anzahl pro Tag';
$lang['всего просмотров'] = 'Alle Anzeige';
$lang['Просмотров в сутки: '] = 'Anzahl pro 24h: ';

# pagination
$lang['Первая'] = 'Erste';
$lang['предыдущая'] = 'vorherige';
$lang['следующая'] = 'nächste';
$lang['последняя'] = 'Letzte';

# random_gal
$lang['Галерея'] = 'Bilder Galerie';
$lang['Галерея:'] = 'Bilder Galerie:';
$lang['Количество:'] = 'Anzahl:';
$lang['CSS-cтиль блока:'] = 'CSS-style vob block:';
$lang['CSS-cтиль img:'] = 'CSS-style von img:';
$lang['Свой HTML-блок:'] = 'Ihre HTML-block:';

# random_pages
$lang['Случайные статьи'] = 'Zufalls Artikel';
$lang['Тип страниц:'] = 'Typ der Seite';

# randomtext
$lang['Цитаты'] = 'Zitat';
$lang['Цитата'] = 'Zitat';

# rater
$lang['Рейтинг страниц'] = 'Rating der Webseite';
$lang['Голосов:'] = 'Stimmen:';
$lang['Текущая оценка:'] = 'Aktuelle Note:';
$lang['название записи'] = 'Überschrift einer Seite';
$lang['всего голосов'] = 'Alle Stimmen';
$lang['общий бал (деление общего рейтинга на кол-во голосов) - округлен до целого'] = 'Gesamt Punkte (teilung des Gesamtes Rating durch die Anzahl der Stimmen) – gerundet auf Ganzes';
$lang['общий бал (дробный)'] = 'Gesamt Punkte (fractional)';
$lang['Общий бал:'] = 'Gesamt Punkte';
$lang['Вы уже голосовали!'] = 'Sie haben bereits Ihre Stimme abgegeben!';
$lang['Ваша оценка:'] = 'Ihre Note:';
$lang['Средняя оценка'] = 'Mittlere Note';
$lang['из'] = 'von';
$lang['проголосовавших'] = 'stimmen';

#redirect
$lang['Редирект'] = 'Weiterleitung';
$lang['Редиректы'] = 'Weiterleitungen';
$lang['С помощью этого плагина вы можете организовать редиректы со своего сайта. Укажите исходный и конечный адрес через «|», например:'] = 'Mit diesem Plugin können Sie eine Weiterleitung von Ihre Webseite erstellen. Geben Sie die quell und end Adresse ein «|», Bsp.:';
$lang['При переходе к странице вашего сайта «http://mysite.com/about» будет осуществлен автоматический редирект на указанный «http://newsite.com/hello».'] = 'Wenn man auf die Seite «http://mysite.com/about» wird der Besucher auf die Seite «http://newsite.com/hello» umgeleitet.';
$lang['Третьим параметром вы можете указать тип редиректа: 301 или 302.'] = 'Dritter Paramether Typ ist: 301 oder 302';
$lang['Также можно использовать регулярные выражения.'] = 'Sie können auch regexps benutzen.';

# search_form
$lang['Форма поиска'] = 'Such Form';
$lang['Что искать?'] = 'Was wird gesucht?';
$lang['Поиск'] = 'Suchen';
$lang['Текст подсказки:'] = 'Text der Hilfe';
$lang['Текст на кнопке:'] = 'Text auf dem Knopf';
$lang['CSS-стиль текста:'] = 'CSS-style von Text';
$lang['CSS-стиль кнопки:'] = 'CSS-style von Knopf';

# sitemap
$lang['Воспользуйтесь картой сайта'] = 'Benutzen Sie die Sitemap';

# tabs
$lang['Табы (закладки)'] = 'Tabs';
$lang['Табы:'] = 'Tabs:';
$lang['Указывайте по одному табу в каждом абзаце в формате: <strong>заголовок | виджет номер</strong>'] = 'Ein Tab pro Zahle in Format: <strong>Überschrift | Widget Nummer';
$lang['Например: <strong>Цитаты | randomtext_widget 1</strong>'] = 'Beispiel: <strong>Zitaten | randomtext_widget 1</strong>';
$lang['Для ушки: <strong>Цитаты | ушка_цитаты</strong>'] = 'Für Ohr (ushka): <strong>Zitaten | ushka_quote</strong>';
$lang['Использовать:'] = 'Benutze:';
$lang['Виджет (функция и номер через пробел)'] = 'Widget (Funktion und Nummer mit Leerzeichen)';
$lang['Ушка (только название)'] = 'Ohr (Ushka - nur Überschrift)';

# tagclouds
$lang['Облако тэгов/меток'] = 'Tags Wolke';
$lang['Мин. размер (%):'] = 'Min Größe (%):';
$lang['Макс. размер (%):'] = 'Max Größe (%);';
$lang['Макс. меток:'] = 'Max tags:';
$lang['Миним. меток:'] = 'Min tags:';
$lang['Отображать только метки, которых более указанного количества. (0 - без ограничений)'] = 'Zeige die Tags nur dann an, wenn Sie mehr als eingegeben Zahl ist (0 - für unedlich)';
$lang['Начало блока:'] = 'Beginn des block:';
$lang['Конец блока:'] = 'Ende des block:';
$lang['Сортировка:'] = 'Sortieren:';
$lang['По количеству записей (обратно)'] = 'Nach Anzahl der Einträge (rückwärts)';
$lang['По количеству записей'] = 'Nach Anzahl der Einträge';
$lang['По алфавиту'] = 'Nach Alphabet';
$lang['По алфавиту (обратно)'] = 'Nach Alphabet (rückwärts)';

# text_block
$lang['Текстовый блок'] = 'Text block';
$lang['Текст:'] = 'Text:';
$lang['Тип:'] = 'Type:';
$lang['HTML или текст'] = 'HTML oder text';
$lang['Можно использовать HTML-тэги. Если тип PHP, то код должен выполняться без ошибок!'] = 'html-tags sind erlaubt. Wenn PHP — muss ohne Fehler sein!';

# twitter
$lang['Мой Twitter'] = 'Mein Twitter';
$lang['Адрес:'] = 'Url:';
$lang['Количество записей:'] = 'Seiten:';
$lang['Формат вывода:'] = 'Ausgabe Format:';
$lang['Формат даты:'] = 'Datum Format:';
$lang['Количество слов:'] = 'Wörter:';

# ushki
$lang['Ушки'] = 'Ohren';
$lang['Админ-доступ к Ушкам'] = 'Administration Ohren';
$lang['С помощью ушек вы можете размещать произвольный html/php код в шаблоне, виджете или прочих плагинах. Ушки удобно использовать для вывода счетчика, рекламы и т.п. Просто создайте ушку, а потом укажите её имя в виджете или с помощью кода:'] =
'Mit den Ohren können Sie beliegen html/php code in Ihre Template einfügen, widget oder andere Plugins. Ohren kann man leicht benutzen für die Ausgabe von Zähler, Werbung Banner, etc. Erstellen Sie einfach ein Ohr und dann geben Sie Ihre Name in Widget oder mit Hilfe des Codes:';
$lang['Вы можете вывести произвольную ушку прямо в тексте. Данный код выведет ушку «reklama»:'] = 'Sie können Ihr Ohr gleich in Text Ausgeben. Folgender Code gibt den Ohr «reklama» aus:';
$lang['Ушка добавлена!'] = 'Ohr wurde Hinzugefügt';
$lang['Необходимо указать название ушки!'] = 'Brauche einen Überschrift für Ohr!';
$lang['Обновлено!'] = 'Aktualisiert!';
$lang['Новая ушка:'] = 'Neu Ohr:';
$lang['Добавить новую ушку'] = 'Erstelle einen neuen Ohr';
$lang['Удалить'] = 'Löschen';
$lang['Сохранить изменения'] = 'Speichern';
$lang['Настройки ушек'] = 'Ohr Einstellungen';
$lang['Заголовок (блока):'] = 'Überschrift (von block)';
$lang['Ушка (название):'] = 'Ohr (Bezeichnung/Überschrift):';

# wpconvert
# пока здесь, потом перенесу в каталог плагина
$lang['Админ-доступ к wpconvert'] = 'Admin zugriff für wpconvert';
$lang['Угу, зщас... У тебя сайт накроется, кто отвечать будет?! В ЛЕС!!!'] = 'Nein! Es kann schiff gehen';
$lang['Файл:'] = 'Datei:';
$lang['Сайт:'] = 'Seite:';
$lang['Ссылка:'] = 'Url:';
$lang['Все рубрики:'] = 'Alle Rubriken:';
$lang['Всего записей:'] = 'Seiten:';
$lang['Статус:'] = 'Status:';
$lang['Тип страницы:'] = 'Page Type:';
$lang['Рубрики:'] = 'Rubriken:';
$lang['Метки:'] = 'Tags:';
$lang['Дата:'] = 'Datum:';
$lang['Комментарии:'] = 'Kommentars:';
$lang['Новый slug:'] = 'Neue slug:';
$lang['Текст:'] = 'Text:';
$lang['Комментарии:'] = 'Kommentar:';
$lang['Готово! Проверка выполнена!<br>Предположительно запросов к БД будет:'] = 'Fertig! Überprüfung erfolgreich! Ungefähre Anzahl der Zugriffe auf die DB';
$lang['Измененных slug (url):'] = 'Verändere den slug (url)';
$lang['Всего записей:'] = 'Alle Einträge';
$lang['Всего комментариев:'] = 'Alle Kommentare';
$lang['Что за ерунду ты мне подсовываешь? Файл-то пустой!'] = 'Hmmm... Datei ist leider leer!';
$lang['Файл'] = 'Datei';
$lang['не найден! Загрузите его в каталог /uploads/ Можно через Загрузку.'] = 'Nicht gefunden! Lade hoch in die /uploads/ Ordner';
$lang['Добавленные рубрики'] = 'Rubrik hinzufügen';
$lang['Добавленные страницы'] = 'Seite hinzufügen';
$lang['Готово! Конвертирование выполнено!'] = ' fertig!';
$lang['Готово! Конвертирование выполнено!'] = 'erfolgreich!';
$lang['Экспорт я рекомендую сделать частями так, чтобы размер одного файла не превышал 300-400Кб. При конвертировании это позволит уменьшить нагрузку на сервер, а также позволит обойти ограничения хостинга на время выполнения скриптов и максимальный размер файла. В итоге у вас получится несколько xml-файлов.'] =
'The Export I recommend to do(make) the parts so that size(amount) of one file did not exceed 300-400Кб. Under(Upon) конвертировании this(it) will allow to reduce the load on(upon;in;to;for;at;per;for) server, as well as will allow to avoid(get round) the restrictions(limits) хостинга for running time скриптов and maximum(peak) size(amount) of the file. In total beside(at;by) you are got(received) several xml-files.';
$lang['Перед конвертацией вам следует открыть каждый xml-файл в FireFox. Если браузер ругается на какие-то ошибки, то вам следует их исправить прямо в файле. К сожалению WordPress может неверно формировать xml-файл, но я постарался исправить ошибки в своем export-max.php.'] =
'Before конвертацией you should open each(every) xml-file in(to;at) FireFox. If браузер swears on(upon;in;to;for;at;per;for) some(any) errors, that you should their correct straight(head-on) in(to;at) file. Regrettably WordPress can(may;be able) untrue form(shape) xml-file, but I tried to correct the errors in(to;at) its export-max.php.';
$lang['Лишь только после того, как FireFox отобразит дерево элементов без ошибок, вы можете загрузить файл в каталог /uploads/. Можно через Загрузки.'] =
'Only not until FireFox will display(map;feature) the tree(wood) an element without error, you may load(boot;store) the file in(to;at) directory /uploads/. Possible through(across;via;over;in) Boot(Store).';
$lang['Перед началом конвертации нужно выполнить проверку. Для этого нажмите кнопку «Проверить файл». В результате вы увидите отчет о проверке. И лишь в случае отсутствия ошибок, можно запустить конвертацию.'] =
'Before beginning конвертации it is necessary to execute(accomplish;run;perform;fulfill;carry out) check(audit;test;inspection;examine;checking). For(On;Of;To;With) this press(hit) the button(knob;drawing pin)  to Check(Test;Audit;Inspect;Examine) the file . As a result you see the report about(of;on;to;for) check(audit;test;inspection;examine;checking). And only in the event of absence error, possible start(launch) converting.';
$lang['<b>Правила конвертирования.</b> Копируются все тексты, включая обычные записи и постоянные страницы. В записях сохраняется slug (короткая ссылка) при условии, что в системе еще нет такой. Если есть, то добавляется префикс 1, 2 и т.д. В комментариях копируется только текст и имя. Остальные данные не используются. Рубрики создаются по их названию. Если такое название уже есть, то используется существующая рубрика. Иерархия конвертируемых рубрик полностью теряется. Записи конвертируются только со статусом publish, static и draft.'] =
'<b>Rules of converting.</b> are Copied all texts, including usual(ordinary) record(entry;writing) and constant pages. In(To;At) record(entry;writing) is saved slug (the short reference) provided that in(to;at) system else no such. If there is, that is added prefix 1, 2 and etc. In(To;At) комментариях is copied only text and name(first name). Rest given are not used. The Rubrics upon their name(title). If such name(title) already there is, that is used existing rubric. The Hierarchy of the converted rubrics completely gets lost. Record(Entry;Writing) are converted with(since) status publish only, static and draft.';
$lang['Обратите внимание, что процесс конвертирования очень ресурсоемкий. Прежде всего он потребует много php-памяти, а также множество SQL-запросов к БД. При конвертировании система попробует установить большее время выполнения php-скриптов, чтобы сервер принудительно не сбросил соединение. Однако не на всех хостингах такая возможность может сработать. Если сервар слабый, то он может не успеть обработать все SQL-запросы. В этом случае вам придется уменьшить размер xml-файла и попытаться выполнить конвертирование заново по частям.'] =
'Call attention that process converting much(highly;very much) hevy. First of all he will require much(many;plenty of) php-memories, as well as ensemble(manifold;great number) SQL-request to(towards) BD. Under(Upon) конвертировании system will try to install(fix) greater running time php-скриптов that server by force has not thrown join(joining). However not on(upon;in;to;for;at;per;for) all хостингах such possibility(capacity) can(may;be able) operate. If сервар weak, that he can(may;be able) not have(make progress) time to to process(handle) all SQL-requests. In this case you to come to reduce the size(amount) xml-file and try to execute(accomplish;run;perform;fulfill;carry out) конвертирование on(over;along;down;under) a parts anew.';
$lang['При конвертировании система автоматически проверяет уже существующие рубрики и записи. Если таковые уже есть, то они не добавляются. Это позволяет избежать дублирования. Ну и кроме того, вы можете не опасаться, что при повторной конвертации данные снова добавятся.'] =
'Under(Upon) converting system automatically checks(tests;audits;inspects;examines) already existing rubrics and record(entry;writing). If such already there is, that they are not added. This(It) allows to avoid(elude) duplication. Well and besides(furthermore), you may not fear that under(upon) the repeated(recurrent) конвертации given are once again added.';
$lang['После конвертирования можно деактивировать этот плагин, а также удалить xml-файлы. Также рекомендую очистить кэш: удалить файлы в <u>system/cache/rss/</u>'] =
'After converting possible deactivate this plugin, as well as delete(remove) xml-files. Also(Too;Either;As well) recommend to clean(clear;decontaminate) cache: delete(remove) files in(to;at) <u>system/cache/rss/</u>';
$lang['После конвертирования старые адреса вида <u>http://site/slug</u> сохранятся. Но следует иметь ввиду, что на MaxSite CMS принята немного другая структура ссылок: <u>http://site/page/slug</u> (т.н. синонимы ссылок). Поэтому переживать, что ссылки на других ресурсах потеряются, не следует. При условии, конечно, то новый slug совпадает со старым (во время проверки файла это видно).'] =
'After converting old address of the type(air;view;complexion) <u>http://site/slug</u> is saved. But follows to have in view of that on(upon;in;to;for;at;per;for) MaxSite CMS is accepted little other structures of the references: <u>http://site/page/slug</u> (t.n. synonyms of the references). So outlive that references to the other resource get lost, does not follow. At condition(term), certainly(of course), that new slug complies with old (during checking the file this(it) is seen).';
$lang['<u>ВАЖНО!</u> Настоятельно рекомедую перед началом конвертирования <u>сделать дамп текущей базы данных</u>! В случае ошибок, вы быстро сможете восстановить прежнее состояние своего сайта. Не игнорируйте это замечание!'] =
'<u>WICHTIG!</u> Wir empfehlen Ihnen einen <u>Backup der Datenbank(basis)</u> zu machen! Bei Fehler, können Sie Ihre Seite leicht wiederherstellen. Das ist eine wichtige Empfehlung!';
$lang['Я понял и согласен взять на себя всю ответственность за использование данного конвертера! Дамп также сделал и умею с ним работать'] =
'Ich bin einverstanden und Hafte selbst für die Benutzung des Konverters! Backup ist gemacht und ich weiß wie man im Falle eines Fehler von Backup alles wiederherstellt';
$lang['Выберите файл:'] = 'Datei Auswählen';
$lang['Проверить файл'] = 'Datei Prüfen';
$lang['Запустить конвертацию'] = 'Konvertierung starten';


# end file
