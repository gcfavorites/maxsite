Описание INI-файла
------------------

Обратите внимание, что в этом файле следует использовать синтаксис INI-файлов. В случае ошибок, система вернет false.


[Название сайта]		 -  секция начинается с [] - здесь следует указать название опции
options_key = name_site  - ключ опции - только по английски, без пробелов и спецсимволов
options_type = general   - группа опции (templates - опции шаблонов, plugins - опции плагинов, general - основные настройки сайта ). 
type = textfield         - тип выводимого поля
description =            - описание подсказка под полем
default =                - значение по-умолчанию


Типы полей:
textfield - обычный input 
select    -  выпадающий список
checkbox  - отметка 
radio     - радиобоксы - выбор одного варианта из нескольких
textarea  - текстовое поле многострочное


values = 1 # 2 # 3 - варианты выбора для select и radio

default для checkbox достаточно указать любое значение для отмеченного варианта. 0 или пусто будет означать неотметка.

Специальный параметр для radio - можно указать текст/html разделителей полей. По-умолчанию это <br>
delimer = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"

Для select в values можно указывать значения через «||» будет: значение||описание для отображения

Если скрипт выдает ошибку парсинга, значит нужно заключать все значения в кавычки ""

Для типа textarea можно указать перенос строки в виде _NR_ - он автоматически пербразуется в перенос строки \n

Для типа textfield можно указать кавычку (") в виде _QUOT_ - он автоматически пербразуется в &quot; 


[Титул]
options_key = title
options_type = general
type = textfield
description = "Короткое название сайта. Это описание будет использовано в title вашего сайта."
default = 

[Описание сайта]
options_key = description
options_type = general
type = textfield
description = "Кратко опишите свой сайт. Это описание будет использовано в meta description вашего сайта."
default = 

[Ключевые слова]
options_key = keywords
options_type = general
type = textfield
description = "Введите ключевые слова сайта. Эти слова будут использованы в meta keywords."
default = 

[Смещение времени]
options_key = time_zone
options_type = general
type = select
values = "0.00||Гринвич (0) # 1.00 # 2.00||Киев (+2) # 3.00||Москва (+3) # 4.00 # 5.00 # 6.00 # 7.00 # 8.00 # 9.00 # 10.00 # 11.00 # 12.00 # -1.00 # -2.00 # -3.00 # -4.00 # -5.00 # -6.00 # -7.00 # -8.00 # -9.00 # -10.00 # -11.00 # -12.00"
default = 0.00
description = "Выберите смещение времени. Оно будет учитываться при выводе дат на сайте."


[Избранный пост]
options_key = _checkbox
options_type = general
type = checkbox
default = 1
description = "описание"

[Ваш текст]
options_key = _textarea
options_type = general
type = textarea
default = "Какой-то дефолтный <b>текст</b>_NR_Другой абзац"
description = "описание текста"

[Выберите возраст]
options_key = _radio
options_type = general
type = radio
delimer = "<br>"
values = "0-9 # 10-19 # 20+"
default = "10-19"
description = "описание"



