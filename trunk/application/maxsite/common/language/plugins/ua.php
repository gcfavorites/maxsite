<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * Language file
 * (c) http://max-3000.com/
 * Author: (c) Pavonine, Wave
 * Author URL: http://pavonine.com.ua/
 * Update URL: http://wave.fantregata.com/page/ukrainian-for-maxsite-cms
 */
 
$lang['Доступ запрещен'] = 'Доступ заборонено';
$lang['Сохранить изменения'] = 'Зберегти зміни';
$lang['Обновлено!'] = 'Оновити!';
$lang['Заголовок:'] = 'Заголовок:';
$lang['Формат:'] = 'Формат:';
$lang['Сортировка:'] = 'Сортування:';
$lang['Обновлено!'] = 'Оновлено!';
$lang['Админ-доступ к настройкам'] = 'Адмін-доступ до опцій';
$lang['Админ-доступ к настройкам '] = 'Адмін-доступ до опцій ';


# admin_ip
$lang['Обновлено! Обязательно сохраните секретный адрес сейчас!'] = 'Оновлено! Обов’язково збережіть секретну адресу зараз!';
$lang['Admin IP'] = 'IP Адміністратора';
$lang['Вы можете указать IP с которых разрешен доступ в админ-панель. Если пользователь попытается войти в панель управления с другого IP, то ему будет отказано в доступе.'] = 'Ви можете вказати IP, з яких дозволений доступ до адмін-панелі. Якщо користувач спробує ввійти в майстерню з іншого IP, то отримає відмову в доступі.';
$lang['На тот случай, если у администратора сменится IP, следует указать секретный адрес (URL), по которому можно очистить список разрешенных IP. Сохраняйте этот секретный адрес в надежном месте. В случае, если вы его забудете у вас не будет другой возможности, кроме как отключить плагин (удалить его файлы) или вручную исправить базу данных.'] = 'На той випадок, якщо в адміністратора змінився IP, слід вказати конкретну адресу (URL), за якою можна очистити список дозволених IP. Зберігайте цю секретну адресу в надійному місці. В випадку, якщо ви її забули, в вас не буде іншої можливості окрім фізичного видалення модуля чи ручного корегування Бази Даних.';
$lang['Если секретный адрес не указан, то сбросить список будет невозможно.'] = 'Якщо таємну адресу не вказано, то буде неможливо відновити пароль';
$lang['Если список IP пуст, то доступ в админ-панель разрешен с любого IP.'] = 'Якщо список IP порожній, то доступ в майстерню дозволений з будь-якого IP.';
$lang['Следует указывать только цифры и английские буквы. Другие символы не допустимы!'] = 'Слід вказувати тільки англійські літери та арабські цифри. Інші символи неприпустимі!';
$lang['Текущий адрес:'] = 'Дана адреса:';
$lang['Укажите разрешенные IP по одному в каждой строчке'] = 'Вкажіть дозволені IP (по одному в рядку)';
$lang['Ваш текущий IP:'] = 'Ваш поточний IP';
$lang['Будьте внимательны! Обязательно указывайте свой текущий IP!'] = 'Будьте уважні! Обов’язково вказуйте свій поточний IP';
$lang['Админ-доступ к редактированию разрешенных IP'] = 'Адмін-доступ до редагування дозволених IP';

# antispam
$lang['Админ-доступ к antispam'] = 'Адмін-доступ до антиспаму';
$lang['Антиспам'] = 'Антиспам';
$lang['Для вашего IP комментирование запрещено!'] = 'Коментування з вашого IP заборонено!';
$lang['Вы используете запрещенные слова!'] = 'Ви використовуєте заборонені слова!';
$lang['С помощью этого плагина вы можете активно бороться со спамерами. Обратите внимание, что комментарии авторов публикуются без модерации.'] = 'За допомогою цього модуля ви можете активно боротися зі спамерами. Зверніть увагу на те, що коментарі авторів публікуються без модерації (попереднього схвалення).';
$lang['Настройки'] = 'Налаштування';
$lang['Включить антиспам'] = 'Ввімкнути антиспам';
$lang['Вести лог отловленных спамов'] = 'Вести лог поміченого спаму';
$lang['Отправлять комментарий на модерацию, если в нем встречается, хоть одна ссылка.'] = 'Відсилати коментар на попереднє схвалення (премодерацію), якщо в ньому помічений хоч один лінк (посилання).';
$lang['Файл для логов:'] = 'Файл для логів';
$lang['Посмотреть'] = 'Переглянути';
$lang['Черный список IP'] = 'Чорний список IP';
$lang['Укажите IP, с которых недопустимы комментарии. Один IP в одной строчке.'] = 'Вкажіть IP, з яких коментування недопустиме. Один IP — один рядок.';
$lang['Черный список слов'] = 'Чорний список слів';
$lang['Укажите слова, которые нельзя использовать в комментариях. Одно слово в одной строчке.'] = 'Вкажіть слова, які не можна використовувати в коментарях. Одне слово — один рядок.';
$lang['Слова для модерации'] = 'Слова для попередньго схвалення (модерації)';
$lang['Укажите слова, которые принудительно отравляют комментарий на премодерацию. Одно слово в одной строчке. Обратите внимание, что этот список проверяется только если пройдена проверка на Черные списки.'] = 'Вкажіть слова, які примусово надсилають коментар на попереднє схвалення (премодерацію). Одне слово — один рядок. Зверніть увагу — цей список перевіряється тільки тоді, коли коментар пройшов перевірку на Чорні списки.';
$lang['Номера комюзеров, которые всегда попадают в модерацию'] = 'Вкажіть користувачів, які завжди потрапляють на попереднє схвалення (премодерацію)';
$lang['Укажите номера комюзеров, которые принудительно отравляют комментарий на премодерацию. Один номер в одной строчке. Обратите внимание, что этот список проверяется только если пройдена проверка на Черные списки.'] = 'Вкажіть слова користувачів, які примусово надсилають коментар на попереднє схвалення (премодерацію). Одне слово — один рядок. Зверніть увагу — цей список перевіряється тільки тоді, коли коментар пройшов перевірку на Чорні списки.';

# authors
$lang['Авторы'] = 'Автори';

#bbcode
$lang['Настройки плагина bbcode'] = 'Опції модулю bbcode';
$lang['Укажите необходимые опции.'] = 'Вкажіть необхідні опції.';
$lang['Где использовать'] = 'Де працювати';
$lang['Укажите, где должен работать плагин'] = 'Де модуль повинен працювати';
$lang['1||На страницах #2||В комментариях #3||На страницах и в комментариях'] = '1||На сторінках #2||В коментарях #3||На сторінках та в коментарях';
$lang['Конвертирует тэги BBCode в HTML. См.'] = 'Перетворює BBCode на HTML. Див.';

# calendar
$lang['Календарь'] = 'Календар';
$lang['Январь'] = 'Січень';
$lang['Февраль'] = 'Лютий';
$lang['Март'] = 'Березень';
$lang['Апрель'] = 'Квітень';
$lang['Май'] = 'Травень';
$lang['Июнь'] = 'Червень';
$lang['Июль'] = 'Липень';
$lang['Август'] = 'Серпень';
$lang['Сентябрь'] = 'Вересень';
$lang['Октябрь'] = 'Жовтень';
$lang['Ноябрь'] = 'Листопад';
$lang['Декабрь'] = 'Грудень';
$lang['Пн'] = 'Пн';
$lang['Вт'] = 'Вт';
$lang['Ср'] = 'Ср';
$lang['Чт'] = 'Чт';
$lang['Пт'] = 'Пт';
$lang['Сб'] = 'Сб';
$lang['Вс'] = 'Нд';

# captcha
$lang['Введите нижние символы'] = 'Введіть нижні символи';
$lang['Защита от спама: введите только нижние символы'] = 'Захист від спаму: введіть тільки нижні символи';
$lang['(обязательно)'] = '(обов’язково)';

# catclouds
$lang['Облако рубрик'] = 'Хмарка категорій';
$lang['Мин. размер'] = 'Мін. розмір';
$lang['Макс. размер'] = 'Макс. розмір';
$lang['Номер рубрики:'] = 'Номер категорії';
$lang['Начало блока:'] = 'Початок блоку:';
$lang['Конец блока:'] = 'Кінець блоку:';
$lang['По количеству записей (обратно)'] = 'По кількості дописів (зворотньо)';
$lang['По количеству записей'] = 'По кількості дописів';
$lang['По алфавиту'] = 'За порядком абетки';
$lang['По алфавиту (обратно)'] = 'За порядком абетки (зворотньо)';

# category
$lang['Рубрики'] = 'Категорії';
$lang['Например:'] = 'Наприклад';
$lang['Формат текущей:'] = 'Формат поточної:';
$lang['Например:'] = 'Наприклад:';
$lang['Включить только:'] = 'Ввімкнути тільки:';
$lang['Укажите номера рубрик через запятую или пробел'] = 'Вкажіть номери категорій (через кому або пробіл)';
$lang['Исключить:'] = 'Виключити:';
$lang['Если нет записей:'] = 'Якщо нема дописів:';
$lang['Отображать рубрику (количество записей ведется без учета опубликованности)'] = 'Відображати категорію (кількість записів обчислюється незалежно від того, особисті записи, чи ні)';
$lang['Скрывать рубрику (количество записей ведется только по опубликованным)'] = 'Приховувати категорію (кількість записів обчислюється незалежно від того, особисті записи, чи ні)';
$lang['По имени рубрики'] = 'За назвою категорії';
$lang['По ID рубрики'] = 'За ID категорії';
$lang['По выставленному menu order'] = 'За виставленому menu order';
$lang['Порядок:'] = 'Порядок:';
$lang['Прямой'] = 'Прямий';
$lang['Обратный'] = 'Зворотній';
$lang['Включать потомков:'] = 'Ввімкнути нащадків:';
$lang['Всегда'] = 'Завжди';
$lang['Только если явно указана рубрика'] = 'Тільки якщо явно вказана категорія';

# comment_button
$lang['Полужирный'] = 'Напівжирний';
$lang['Курсив'] = 'Курсив';
$lang['Подчеркнутый'] = 'Підкреслений';
$lang['Зачеркнутый'] = 'Закреслений';
$lang['Цитата'] = 'Цитата';
$lang['Код'] = 'Код';
$lang['Код или преформатированный текст'] = 'Код або преформатований текст';

# down_count
$lang['Админ-доступ к настройкам счетчика переходов (Download count)'] = 'Адмін-доступ до налаштувань лічильника переходів';
$lang['Счетчик переходов'] = 'Лічильник переходів';
$lang['Подсчет количества переходов по ссылке. Обрамите нужную ссылку в [dc]...[/dc]'] = 'Лічильник переходів по посиланню. Вставте посилання у [dc]...[/dc]';
$lang['Данная ссылка доступна только со <a href="%s">страниц сайта</a>'] = 'Це посилання доступне тільки з <a href="%s">сторінок сайту</a>';
$lang['Запрещен переход по этой ссылке с чужого сайта'] = 'Перехід з чужого сайту по цьому посиланню заборонений';
$lang['Количество переходов'] = 'Кількість переходів';
$lang['С помощью этого плагина вы можете подсчитывать количество скачиваний или переходов по ссылке. Для использования плагина обрамите нужную ссылку в код [dc]ваша ссылка[/dc]'] = 'За допомогою цього модуля ви можете підраховувати кількість завантажень чи переходів по посиланню. Для використання модуля обрамте посилання в код [dc]ваше посилання[/dc]';
$lang['Файл для хранения количества скачиваний:'] = 'Файл для зберігання кількості завантажень';
$lang['Префикс URL:'] = 'Префікс URL';
$lang['ссылка'] = 'посилання';
$lang['Запретить переходы с чужих сайтов'] = 'Заборонити перехід з чужих сайтів';
$lang['Выводить в title реальный адрес'] = 'Виводити в title справжній адрес';
$lang['Статистика переходов'] = 'Статистика переходів';
$lang['переходов'] = 'переходів';

# editor_dumb
$lang['Ссылка'] = 'Посилання';
$lang['Картинка'] = 'Картинка';
$lang['Отрезать текст'] = 'Обрізати текст';

# events
$lang['События'] = 'Події';
$lang['Формат даты:'] = 'Формат дати';
$lang['Как это <a href="http://ru.php.net/date" target="_blank">принято в PHP</a>'] = 'Як це <a href="http://ru.php.net/date" target="_blank">прийнято PHP</a>';
$lang['Указывайте по одному событию в каждом абзаце в формате:'] = 'Вказуйте по одній події в кожному обзаці в форматі:';
$lang['<strong>дата</strong> в формате yyyy-mm-dd'] = '<strong>дата</strong> в форматі yyyy-mm-dd';
$lang['<strong>до</strong> - выводить событие до наступления N-дней'] = '<strong>до</strong> - виводити подію до настання n днів';
$lang['<strong>после</strong> - выводить событие после прошествия N-дней'] = '<strong>після</strong> - виводити подію після проходу n днів';
$lang['<strong>В тексте события</strong> можно использовать HTML'] = '<strong>В тексті події</strong> можна використовувати HTML';
$lang['<strong>ПРИМЕР:</strong> 2008-09-01 | 3 | 1 | Пора в школу!'] = '<strong>НАПРИКЛАД:</strong> 2008-09-01 | 3 | 1 | Пора до школи!';
$lang['Понедельник'] = 'Понеділок';
$lang['Вторник'] = 'Вівторок';
$lang['Среда'] = 'Середа';
$lang['Четверг'] = 'Четвер';
$lang['Пятница'] = 'П’ятниця';
$lang['Суббота'] = 'Субота';
$lang['Воскресенье'] = 'Неділя';
$lang['января'] = 'січня';
$lang['февраля'] = 'лютого';
$lang['марта'] = 'березня';
$lang['апреля'] = 'квітня';
$lang['мая'] = 'травня';
$lang['июня'] = 'червня';
$lang['июля'] = 'липня';
$lang['августа'] = 'серпня';
$lang['сентября'] = 'вересня';
$lang['октября'] = 'жовтня';
$lang['ноября'] = 'листопада';
$lang['декабря'] = 'грудня';
$lang['янв'] = 'січ';
$lang['фев'] = 'лют';
$lang['мар'] = 'бер';
$lang['апр'] = 'кві';
$lang['май'] = 'тра';
$lang['июн'] = 'чер';
$lang['июл'] = 'лип';
$lang['авг'] = 'сер';
$lang['сен'] = 'вер';
$lang['окт'] = 'жов';
$lang['ноя'] = 'лис';
$lang['дек'] = 'гру';

# faq
$lang['К списку'] = 'До списку';

# favorites
$lang['Избранное'] = 'Улюблене';
$lang['Ссылки:'] = 'Посилання:';
$lang['Указывайте по одной ссылке в каждом абзаце в формате: <strong>тип/ссылка | название</strong>'] = 'Вказуйте по одному посиланню в абзаці в форматі: <strong>тип/посилання | назва</strong>';
$lang['<strong>тип/ссылка</strong> - указывается от адреса сайта, например'] = '<strong>тип/посилання</strong> - вказується від адреси сайту, наприклад';
$lang['Для главной страницы укажите: <strong> / | Главная</strong>'] = 'Для головної сторінки вкажіть: <strong> / | Главная</strong>';

# feedburner
$lang['Админ-доступ к feedburner'] = 'Адмін-доступ до feedburner';
$lang['Адрес вашего фида в FeedBurner.com:'] = 'Адреса вашої стрічки (фіда)';
$lang['Плагин FeedBurner'] = 'Модуль FeedBurner';
$lang['Плагин выполняет перенаправление вашего основного rss на сервис feedburner.com.'] = 'Модуль виконує перенавравлення вашої основної стрічки RSS на сервіс feedburner.com.';

# feedburner_count
$lang['Настройка FeedBurner Count от samborsky.com'] = 'Налаштування FeedBurner Count';
$lang['Ошибка!'] = 'Помилка!';
$lang['Настройка FeedBurner Count от <a href="http://www.samborsky.com/">samborsky.com</a>'] = 'Налаштування FeedBurner Count від <a href="http://www.samborsky.com/">samborsky.com</a>';
$lang['Здравствуйте, последний раз счетчик обновлялся'] = 'Вітаємо, останній раз лічильник оновлявся';
$lang['Последнее показание счетчика:'] = 'Останні показання лічильника:';

# feedcount
$lang['Виджет подсчета подписчиков RSS'] = 'Віджет підрахунку читачів RSS';
$lang['Сегодня:'] = 'Сьогодні:';
$lang['Вчера:'] = 'Вчора:';
$lang['[COUNT] - подписчиков сегодня, [COUNTOLD] - подписчиков вчера'] = '[COUNT] - читачів сьогодні, [COUNTOLD] - читачів вчора';

# forms
$lang['Неверный email!'] = 'Невірний e-mail!';
$lang['Привет роботам! :-)'] = 'Привіт шелезякам! :-)';
$lang['Заполните все необходимые поля!'] = 'Заповніть всі необхідні поля!';
$lang['Вами отправлено сообщение:'] = 'Вами надіслане повідомлення:';
$lang['Ваше сообщение отправлено!'] = 'Ваше повідомлення надіслане!';
$lang['Ваше имя*'] = 'Ваше ім’я (*)';
$lang['Ваш email*'] = 'Ваш e-mail (*)';
$lang['Ваше имя'] = 'Ваше ім’я';
$lang['Ваш email'] = 'Ваш e-mail';
$lang['Защита от спама:'] = 'Захист від спаму:';
$lang['Отправить копию письма на ваш e-mail'] = 'Надсилати копію листа на ваш e-mail';
$lang['Отправить'] = 'Надіслати';
$lang['Очистить форму'] = 'Очистити форму';

# last_comments
$lang['Последние комментарии'] = 'Останні коментарі';
$lang['Количество:'] = 'Кількість:';
$lang['Количество слов:'] = 'Кількість слів:';
$lang['Количество символов в одном слове:'] = 'Кількість символів в одному слові:';
$lang['Комментатор'] = 'Коментатор';

# last_pages
$lang['Последние записи'] = 'Останні дописи';
$lang['Формат даты:'] = 'Формат дати:';
$lang['Тип страниц:'] = 'Тип сторінок:';
$lang['Исключить рубрики:'] = 'Виключати категорії:';
$lang['Включить рубрики:'] = 'Включати категорії:';
$lang['Сортировка:'] = 'Сортування:';
$lang['Порядок сортировки:'] = 'Порядок сортування:';
$lang['По дате'] = 'По даті:';
$lang['По алфавиту'] = 'За абеткою';
$lang['Прямой'] = 'Прямий';
$lang['Обратный'] = 'Зворотній';

# links
$lang['Ссылки'] = 'Посилання';
$lang['Указывайте по одной ссылке в каждом абзаце в формате:'] = 'Вказуйте по одному посиланню в кожному абзаці в форматі:';
$lang['обрамить ссылку в noindex, если не нужно - указать пробел'] = 'обрамити посилання в noindex, якщо не потрібно - вказати пробіл';
$lang['открыть ссылку в новом окне, если не нужно - указать пробел'] = 'відкрити посилання в новому вікні, якщо не потрібно - вказати пробіл';

# login_form
$lang['Форма логина'] = 'Форма логіну';
$lang['Привет,'] = 'Агов,';
$lang['выйти'] = 'вийти';
$lang['управление'] = 'керування';
$lang['своя страница'] = 'власна сторінка';
$lang['Логин (email):'] = 'Логін (email):';
$lang['Пароль:'] = 'Пароль:';

# page_comments
$lang['Самое комментируемое'] = 'Найкоментованіше';
$lang['Количество записей:'] = 'Кількість записів:';
$lang['Формат:'] = 'Формат:';
$lang['название записи'] = 'назва допису';
$lang['количество комментариев'] = 'кількість коментарів';
$lang['ссылка'] = 'посилання';

# page_parent
$lang['Родительские/дочерние страницы'] = 'Батьківські/дочірні сторінки';
$lang['Номер страницы:'] = 'Номер сторінки:';

# page_views
$lang['Самое читаемое'] = 'Найбільш читане';
$lang['просмотров в день'] = 'переглядів в день';
$lang['всего просмотров'] = 'всього переглядів';

# pagination
$lang['Первая'] = 'Перша';
$lang['предыдущая'] = 'попередня';
$lang['следующая'] = 'наступна';
$lang['последняя'] = 'Остання';

# random_gal
$lang['Галерея'] = 'Галерея';
$lang['Галерея:'] = 'Галерея:';
$lang['Количество:'] = 'Кількість:';
$lang['CSS-cтиль блока:'] = 'CSS-стиль блоку:';
$lang['CSS-cтиль img:'] = 'CSS-стиль img:';
$lang['Свой HTML-блок:'] = 'Свій HTML-блок:';

# random_pages
$lang['Случайные статьи'] = 'Випадкові сторінки';
$lang['Тип страниц:'] = 'Тип сторінок';

# randomtext
$lang['Цитаты'] = 'Цитати';
$lang['Цитата'] = 'Цитата';

# rater
$lang['Рейтинг страниц'] = 'Рейтинг сторінок';
$lang['Голосов:'] = 'Голосів:';
$lang['Текущая оценка:'] = 'Поточна оцінка:';
$lang['название записи'] = 'назва запису';
$lang['всего голосов'] = 'всього голосів';
$lang['общий бал (деление общего рейтинга на кол-во голосов) - округлен до целого'] = 'загальний бал (ділення всього рейтингу на кількість голосів) — округлений до цілого';
$lang['общий бал (дробный)'] = 'загальний бал (десятковий)';
$lang['Общий бал:'] = 'Загальний бал:';
$lang['Вы уже голосовали!'] = 'Ви вже проголосували!';
$lang['Ваша оценка:'] = 'Ваша оцінка:';
$lang['Средняя оценка'] = 'Середня оцінка';
$lang['из'] = 'з';
$lang['проголосовавших'] = 'проголосувавших';

#redirect
$lang['Редирект'] = 'Перенаправлення';
$lang['Редиректы'] = 'Перенаправлення';
$lang['С помощью этого плагина вы можете организовать редиректы со своего сайта. Укажите исходный и конечный адрес через «|», например:'] = 'За допомогою цього модуля ви можете налаштувати перенаправлення зі свого сайту. Вкажіть начальну та кінцеву адресу через «|», наприклад:';
$lang['При переходе к странице вашего сайта «http://mysite.com/about» будет осуществлен автоматический редирект на указанный «http://newsite.com/hello».'] = 'При зверненні до сторінки вашого сайту «http://mysite.com/about» відідувач автоматично буде направлений на вказану адресу «http://newsite.com/hello».';
$lang['Третьим параметром вы можете указать тип редиректа: 301 или 302.'] = 'Третім параметром ви можете вказати тип перенаправлення: 301 або 302';
$lang['Также можно использовать регулярные выражения.'] = 'Також ви можете використовувати регулярні вирази.';

# search_form
$lang['Форма поиска'] = 'Форма пошуку';
$lang['Что искать?'] = 'Що шукати?';
$lang['Поиск'] = 'Пошук';
$lang['Текст подсказки:'] = 'Текст підказки:';
$lang['Текст на кнопке:'] = 'Текст на кнопці:';
$lang['CSS-стиль текста:'] = 'CSS-стиль тексту';
$lang['CSS-стиль кнопки:'] = 'CSS-стиль кнопки';

# sitemap
$lang['Воспользуйтесь картой сайта'] = 'Скористайтесь картою сайту';

# tabs
$lang['Табы (закладки)'] = 'Вкладки';
$lang['Табы:'] = 'Вкладки:';
$lang['Указывайте по одному табу в каждом абзаце в формате: <strong>заголовок | виджет номер</strong>'] = 'Вказуйте по одній вкладці в кожному абзаці в форматі: <strong>заголовок | віджет номер</strong>';
$lang['Например: <strong>Цитаты | randomtext_widget 1</strong>'] = 'Наприклад: <strong>Цитати | randomtext_widget 1</strong>';
$lang['Для ушки: <strong>Цитаты | ушка_цитаты</strong>'] = 'Для вушки: <strong>Цитати | ушка_цитаты</strong>';
$lang['Использовать:'] = 'Використовувати:';
$lang['Виджет (функция и номер через пробел)'] = 'Віджет (функція й номер через пробіл)';
$lang['Ушка (только название)'] = 'Вушка (тільки назва)';

# tagclouds
$lang['Облако тэгов/меток'] = 'Хмарка теґів';
$lang['Мин. размер (%):'] = 'Мін. розмір (%):';
$lang['Макс. размер (%):'] = 'Макс. розмір (%):';
$lang['Макс. меток:'] = 'Макс. теґів:';
$lang['Миним. меток:'] = 'Мін. теґів:';
$lang['Отображать только метки, которых более указанного количества. (0 - без ограничений)'] = 'Відображати тільки ті теґи, яких більше вказаної кількості. (0 - без обмежень)';
$lang['Начало блока:'] = 'Початок блоку:';
$lang['Конец блока:'] = 'Кінець блоку:';
$lang['Сортировка:'] = 'Сортування:';
$lang['По количеству записей (обратно)'] = 'По кількості записів (зворотньо)';
$lang['По количеству записей'] = 'По кількості записів';
$lang['По алфавиту'] = 'За абеткою';
$lang['По алфавиту (обратно)'] = 'За абеткою (зворотньо)';

# text_block
$lang['Текстовый блок'] = 'Текстовий блок';
$lang['Текст:'] = 'Текст:';
$lang['Тип:'] = 'Тип';
$lang['HTML или текст'] = 'HTML або текст';
$lang['Можно использовать HTML-тэги. Если тип PHP, то код должен выполняться без ошибок!'] = 'Можна використовувати HTML-теґи. Якщо тип PHP, то код повинен виконуватися без помилок!';

# twitter
$lang['Мой Twitter'] = 'Мій твіттер:';
$lang['Адрес:'] = 'Адреса:';
$lang['Количество записей:'] = 'Кількість записів:';
$lang['Формат вывода:'] = 'Формат виводу:';
$lang['Формат даты:'] = 'Формат дати:';
$lang['Количество слов:'] = 'Кількість слів:';

# ushki
$lang['Ушки'] = 'Вушки';
$lang['Админ-доступ к Ушкам'] = 'Адмін-доступ до Вушок';
$lang['С помощью ушек вы можете размещать произвольный html/php код в шаблоне, виджете или прочих плагинах. Ушки удобно использовать для вывода счетчика, рекламы и т.п. Просто создайте ушку, а потом укажите её имя в виджете или с помощью кода:'] = 'За допомогою Вушок ви можете розміщувати довільний html/php кож в шаблоні, віджеті чи інших модулях. Вушки зручно використовувати для виводу пузомірок, реклами і таке інше. Просто створіть вушку, а опісля вкажіть її назву в віджеті, або за допомогою коду:';
$lang['Вы можете вывести произвольную ушку прямо в тексте. Данный код выведет ушку «reklama»:'] = 'Ви можете вивести довільну вушку прямо в тексті. Даний код виведе вушку «reklama»:';
$lang['Ушка добавлена!'] = 'Вушка додана!';
$lang['Необходимо указать название ушки!'] = 'Необхідно вказати назву вушки!';
$lang['Обновлено!'] = 'Оновлено!';
$lang['Новая ушка:'] = 'Нова вушка:';
$lang['Добавить новую ушку'] = 'Додати нову вушку';
$lang['Удалить'] = 'Видалити';
$lang['Сохранить изменения'] = 'Зберегти зміни';
$lang['Настройки ушек'] = 'Налаштування вушок';
$lang['Заголовок (блока):'] = 'Заголовок (блоку):';
$lang['Ушка (название):'] = 'Вушка (назва):';

# wpconvert
# пока здесь, потом перенесу в каталог плагина
$lang['Админ-доступ к wpconvert'] = 'Адмін-доступ до wpconvert';
$lang['Угу, зщас... У тебя сайт накроется, кто отвечать будет?! В ЛЕС!!!'] = 'ага, уже біжу… В тебе сайт мідним тазом накриється, хто відповідати буде?! В ЛІС!!!';
$lang['Файл:'] = 'Файл:';
$lang['Сайт:'] = 'Сайт:';
$lang['Ссылка:'] = 'Посилання:';
$lang['Все рубрики:'] = 'Всі категорії:';
$lang['Всего записей:'] = 'Всього дописів:';
$lang['Статус:'] = 'Статус';
$lang['Тип страницы:'] = 'Тип сторінки:';
$lang['Рубрики:'] = 'Категорії:';
$lang['Метки:'] = 'Теґи:';
$lang['Дата:'] = 'Дата:';
$lang['Комментарии:'] = 'Коментарі:';
$lang['Новый slug:'] = 'Нове посилання:';
$lang['Текст:'] = 'Текст:';
$lang['Комментарии:'] = 'Коментарі:';
$lang['Готово! Проверка выполнена!<br>Предположительно запросов к БД будет:'] = 'Готово! Перевірка виконана!<br>Теоретично запитів до Бази Даних буде:';
$lang['Измененных slug (url):'] = 'Змінених посиланнь (url):';
$lang['Всего записей:'] = 'Всього дописів:';
$lang['Всего комментариев:'] = 'Всього коментарів:';
$lang['Что за ерунду ты мне подсовываешь? Файл-то пустой!'] = 'Що за дрібниці ти мені подаєш? Файл-то порожній!';
$lang['Файл'] = 'Файл';
$lang['не найден! Загрузите его в каталог /uploads/ Можно через Загрузку.'] = 'не знайдений! Завантажте його в директорію /uploads/. Можна через Завантаження.';
$lang['Добавленные рубрики'] = 'Додані категорії';
$lang['Добавленные страницы'] = 'Додані сторінки';
$lang['Готово! Конвертирование выполнено!'] = 'Готово! Конвертування виконано!';
$lang['С помощью данной страницы вы можете конвертировать WordPress-данные для MaxSite CMS. Для начала вам нужно выполнить экспорт из WordPress. Для выполнения экспорта вам нужно скопировать файл export-max.php в каталог wp-admin. После этого наберите адрес http://сайт/wp-admin/export-max.php Выполните экспорт. Обратите внимание, что я проверял на WordPress 2.3.3. На других версиях ничего не гарантирую. Из-за некоторых ошибок и особенности формата, собственный wp-экспорт не подойдет для наших целей.'] = 'За допомогою цієї сторінки ви можете конвертувати дані WordPress для MaxSite CMS. Для початку вам слід виконати експорт з Wordpress. Для виконання експорту вам потрібно скопіювати файл export-max.php в теку wp-admin. Опісля введіть адресу http://ваш.сайт/wp-admin/export-max.php. Виконайте експорт. Зверніть увагу, що перевірялось на Wordpress 2.3.3. На інших версіях нічого не гарантується. Через деякі помилки і особливості формату, власний wp-експорт не підійде для наших цілей.';
$lang['Экспорт я рекомендую сделать частями так, чтобы размер одного файла не превышал 300-400Кб. При конвертировании это позволит уменьшить нагрузку на сервер, а также позволит обойти ограничения хостинга на время выполнения скриптов и максимальный размер файла. В итоге у вас получится несколько xml-файлов.'] = 'Експорт рекомендовано зробити так, щоб розмір одного файлу не перевищував 300-400Кб. При конвертуванні це дозволить зменшити навантаження на сервер, а також дозволить обійти обмеження хостингу на час виконання скриптів та максимальний розмір файлу. В результати в нас вийде кілька xml-файлів.';
$lang['Перед конвертацией вам следует открыть каждый xml-файл в FireFox. Если браузер ругается на какие-то ошибки, то вам следует их исправить прямо в файле. К сожалению WordPress может неверно формировать xml-файл, но я постарался исправить ошибки в своем export-max.php.'] = 'Перед конвертацією вам варто відкрити xml-файл в Firefox. Якщо браузер повідомляє на якісь-то помилки, то вам варто виправити їх прямо в файлі. На жаль, Wordpress може невірно оформити xml-файл, та я намагаюсь виправити помилки в своєму export-max.php.';
$lang['Лишь только после того, как FireFox отобразит дерево элементов без ошибок, вы можете загрузить файл в каталог /uploads/. Можно через Загрузки.'] = 'Лише після того, як FireFox відобразить дерево елементів без помилок, вы можете завантажити файл в директорію /uploads/. Можна через Завантаження.';
$lang['Перед началом конвертации нужно выполнить проверку. Для этого нажмите кнопку «Проверить файл». В результате вы увидите отчет о проверке. И лишь в случае отсутствия ошибок, можно запустить конвертацию.'] = 'Лиш після того як вогнелис відобразить дерево елементів без помилок, ви можете завантажити файл в директорію /uploads/. Можна через Завантаження.';
$lang['<b>Правила конвертирования.</b> Копируются все тексты, включая обычные записи и постоянные страницы. В записях сохраняется slug (короткая ссылка) при условии, что в системе еще нет такой. Если есть, то добавляется префикс 1, 2 и т.д. В комментариях копируется только текст и имя. Остальные данные не используются. Рубрики создаются по их названию. Если такое название уже есть, то используется существующая рубрика. Иерархия конвертируемых рубрик полностью теряется. Записи конвертируются только со статусом publish, static и draft.'] = '<b>Правила конвертування.</b> Копіюються всі тексти, включаючи звичайні записи й постійні сторінки. В записах зберігається посилання при умові, що в системі ще такого нема. Якщо є, то додається префікс 1, 2 і т.д. Ієрархія конвертованих категорій повінстю втрачається. Категорії створюються по їх назві. Якщо така назва вже є, то використовується існуюча категорія. Конвертуються тільки записи зі статусом publish, static и draft.';
$lang['Обратите внимание, что процесс конвертирования очень ресурсоемкий. Прежде всего он потребует много php-памяти, а также множество SQL-запросов к БД. При конвертировании система попробует установить большее время выполнения php-скриптов, чтобы сервер принудительно не сбросил соединение. Однако не на всех хостингах такая возможность может сработать. Если сервар слабый, то он может не успеть обработать все SQL-запросы. В этом случае вам придется уменьшить размер xml-файла и попытаться выполнить конвертирование заново по частям.'] = 'Зверніть увагу, що процес конвертування дуже ресурсоємний. Перш за все він вимагає багато php-пам’яті, а також виконує багато SQL-запитів до Бази Даних. При конвертуванні ваша система може встановити вдовгий час виконання php-скриптів, щоб сервер примусово не скинув з’єднаннє Однак не на всіх хостингах може спрацювати. Якщо сервер слабкий, то він може не встигати обробляти всі SQL-запити. В такому випадку вам доведеться зменшити розмір xml-файлу й спробувати провести конвертування спочатку по частинах.';
$lang['При конвертировании система автоматически проверяет уже существующие рубрики и записи. Если таковые уже есть, то они не добавляются. Это позволяет избежать дублирования. Ну и кроме того, вы можете не опасаться, что при повторной конвертации данные снова добавятся.'] = 'При конвертування система перевіряє вде існуючі категорії й записи. Якщо такі вже є, то вони не додаються. Це дозволить уникнути дублювання. Ну і ви ще можете не турбуватися, що при повторній конвертації дані продублюються.';
$lang['После конвертирования можно деактивировать этот плагин, а также удалить xml-файлы. Также рекомендую очистить кэш: удалить файлы в <u>system/cache/rss/</u>'] = 'Після конвертування можна деактивувати цей плагін а також видалити xml-файли. Також рекомендовано очистити кеш: видалити файли в <u>system/cache/rss/</u>';
$lang['После конвертирования старые адреса вида <u>http://site/slug</u> сохранятся. Но следует иметь ввиду, что на MaxSite CMS принята немного другая структура ссылок: <u>http://site/page/slug</u> (т.н. синонимы ссылок). Поэтому переживать, что ссылки на других ресурсах потеряются, не следует. При условии, конечно, то новый slug совпадает со старым (во время проверки файла это видно).'] = 'Після конвертування старі адреси вигляду <u>http://site/slug</u> зберігаються. Але слід мати на увазі, що в Max Site CMS прийнята трішки інша система посиланнь: <u>http://site/page/slug</u>. Тому переживати, що посилання на інших ресурсах втратяться не варто. При умові, звісно, що нове посилання співпадає з старим (під час перевірки файлу це видно).';
$lang['<u>ВАЖНО!</u> Настоятельно рекомедую перед началом конвертирования <u>сделать дамп текущей базы данных</u>! В случае ошибок, вы быстро сможете восстановить прежнее состояние своего сайта. Не игнорируйте это замечание!'] = '<u>ВАЖЛИВО!</u> Настирливо рекомендую перед початком конвертації <u>зробити дамп поточної бази даних</u>! В разі помилок ви можете швидко відновити працездатність власного сайту. Не ігоноруйте це попередження!';
$lang['Я понял и согласен взять на себя всю ответственность за использование данного конвертера! Дамп также сделал и умею с ним работать'] = 'Я зрозумів і беру на себе всю відповідальність, за використання даного конвертера! Дамп вже зроблений і я вмію з ним працювати';
$lang['Выберите файл:'] = 'Оберіть файл:';
$lang['Проверить файл'] = 'Перевірити файл';
$lang['Запустить конвертацию'] = 'Запустити конвертацію';



# end file
