// ----------------------------------------------------------------------------
// markItUp!
// ----------------------------------------------------------------------------
// Copyright (C) 2008 Jay Salvat
// http://markitup.jaysalvat.com/
// ----------------------------------------------------------------------------
myBbcodeSettings = {
  nameSpace:          "bbcode", // Useful to prevent multi-instances CSS conflict
  previewParserPath:  "~/sets/bbcode/preview.php",
  markupSet: [
      {name:'Жирный', key:'B', openWith:'[b]', closeWith:'[/b]', className:"bold"}, 
      {name:'Курсив', key:'I', openWith:'[i]', closeWith:'[/i]', className:"italic"}, 
      {name:'Подчеркнутый', key:'U', openWith:'[u]', closeWith:'[/u]', className:"underline"}, 
	  {name:'Зачеркнутый', key:'S', openWith:'[s]', closeWith:'[/s]', className:"stroke"}, 
      {separator:'---------------' },
      {name:'Левое выравнивание', openWith:'[left]', closeWith:'[/left]', className:"left"}, 
      {name:'Центировать', openWith:'[center]', closeWith:'[/center]', className:"center"},       
	  {name:'Правое выравнивание', openWith:'[right]', closeWith:'[/right]', className:"right"}, 
	  {name:'По ширине', openWith:'[div=text-align:justify]', closeWith:'[/div]', className:"justify"}, 	  
      {separator:'---------------' },
      {name:'Рисунок', key:'P', replaceWith:'[img][![url]!][/img]', className:"picture"}, 
      {name:'Flash (flv, mp4) видео', replaceWith:'[flash(640,480)][![Url]!][/flash]', className:"flash"}, 
      {name:'Аудиоплеер (mp3)', replaceWith:'[audio=[![Url]!]]', className:"audio"}, 
      {name:'Ссылка', key:'L', openWith:'[url=[![Url]!]]', closeWith:'[/url]', placeHolder:'Your text to link here...', className:"link"},
      {separator:'---------------' },
      {name:'Цвет', openWith:'[color=[![Color]!]]', closeWith:'[/color]', className:"colors", dropMenu: [
          {name:'Желтый', openWith:'[color=yellow]', closeWith:'[/color]', className:"col1-1" },
          {name:'Оранжевый', openWith:'[color=orange]', closeWith:'[/color]', className:"col1-2" },
          {name:'Красный', openWith:'[color=red]', closeWith:'[/color]', className:"col1-3" },
          {name:'Синий', openWith:'[color=blue]', closeWith:'[/color]', className:"col2-1" },
          {name:'Фиолетовый', openWith:'[color=purple]', closeWith:'[/color]', className:"col2-2" },
          {name:'Зеленый', openWith:'[color=green]', closeWith:'[/color]', className:"col2-3" },
          {name:'Белый', openWith:'[color=white]', closeWith:'[/color]', className:"col3-1" },
          {name:'Серый', openWith:'[color=gray]', closeWith:'[/color]', className:"col3-2" },
          {name:'Черный', openWith:'[color=black]', closeWith:'[/color]', className:"col3-3" }
      ]},
      {name:'Размер', openWith:'[size=[![Text size]!]]', closeWith:'[/size]', className:"fonts", dropMenu :[  
          {name:'Большой', openWith:'[size=200]', closeWith:'[/size]', className:"big" },
          {name:'Нормальный', openWith:'[size=100]', closeWith:'[/size]', className:"normal" },
          {name:'Маленький', openWith:'[size=50]', closeWith:'[/size]', className:"small" }  
      ]},
      {separator:'---------------' },
      {name:'Маркированный список', openWith:'[list]\n', closeWith:'\n[/list]', className:"list-bullet"}, 
      {name:'Числовой список', openWith:'[ol]\n', closeWith:'\n[/ol]', className:"list-numeric"}, 
      {name:'Элемент списка', openWith:'[*] ', className:"list-item"}, 
      {separator:'---------------' },
	  {name:'Короткая новость', replaceWith:'\n[cut]\n', className:"cut"}, 
	  {name:'Принудительный перенос', replaceWith:'[br]\n', className:"br"}, 
	  {name:'Разделитель', replaceWith:'[div(break)][/div]', className:"break"}, 
      {separator:'---------------' },
      {name:'Заголовок 1', openWith:'[h1]', closeWith:'[/h1]', className:"h1"}, 
      {name:'Заголовок 2', openWith:'[h2]', closeWith:'[/h2]', className:"h2"}, 
      {name:'Заголовок 3', openWith:'[h3]', closeWith:'[/h3]', className:"h3"}, 	      
	  {name:'Заголовок 4', openWith:'[h4]', closeWith:'[/h4]', className:"h4"}, 
      {name:'Заголовок 5', openWith:'[h5]', closeWith:'[/h5]', className:"h5"}, 	  
      {separator:'---------------' },
      {name:'Код', openWith:'[code]', closeWith:'[/code]', className:"code", dropMenu: [
          {name:'Текст', openWith:'[code lang=text]', closeWith:'[/code]', className:"text" },
          {name:'C#', openWith:'[code lang=csharp]', closeWith:'[/code]', className:"csharp" },
          {name:'CSS', openWith:'[code lang=css]', closeWith:'[/code]', className:"css" },
          {name:'Delphi', openWith:'[code lang=delphi]', closeWith:'[/code]', className:"delphi" },
          {name:'JavaScript', openWith:'[code lang=js]', closeWith:'[/code]', className:"js" },
          {name:'PHP', openWith:'[code lang=php]', closeWith:'[/code]', className:"php" },
          {name:'SQL', openWith:'[code lang=sql]', closeWith:'[/code]', className:"sql" },
          {name:'XML', openWith:'[code lang=xml]', closeWith:'[/code]', className:"xml" }
	  ]}, 
      {name:'PHP', openWith:'[php]', closeWith:'[/php]', className:"php"}, 
      {name:'PRE', openWith:'[pre]', closeWith:'[/pre]', className:"add"}, 
      {separator:'---------------' },
      {name:'Очистить выделенное от BB кодов', className:"clean", replaceWith:function(h) { return h.selection.replace(/\[(.*?)\]/g, "") }, className:"clean"},
      /*{name:'Предпросмотр', className:"preview", call:'preview', className:"preview"}*/
   ]
}