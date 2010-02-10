Пример формы:


[form]
    [email=mylo@sait.com]
    [redirect=http://site.com/]
    [subject=Мой форма]

    [field]
        require = 1   
        type = select
        description = Выберите специалиста
        values = Иванов # Петров # Сидоров
        default = Иванов
    [/field]

    [field]
        require = 0   
        type = select
        description = Обращались ли вы к нам раньше?
        values = Нет # Да
        default = Нет
    [/field]

    [field]
        require = 1
        type = textarea
        description = Ваш вопрос
    [/field]
[/form]


Для оформления можно использовать стили в шаблоном css, например:

/* plugin FORMS */

div.forms {background: #eeeeee; padding: 10px 5px 0 5px; border: #E0E0E0 1px solid;}
div.forms div {margin: 5px 0 15px 0; }
div.forms div.break {height: 0; padding: 0; margin: 0; clear: both; }
div.forms span {display: block; float: left; text-align: right; width: 240px; padding: 0 10px 0 0;}
div.forms input {width: 320px;}
div.forms select {width: 320px;}
div.forms textarea {width: 320px; height: 100px;}
div.forms input.forms_checkbox {width: auto;}
div.forms input.forms_submit {width: auto;}
div.forms input.forms_reset {width: auto;}
div.forms-post h2 {color: red; font-size: 18pt; margin: 20px 0;}


