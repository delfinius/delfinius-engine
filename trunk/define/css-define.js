function CreateStyleDef(aName,aDispName){
	this.Name=aName;
	this.DispName=aDispName;
	this.Classes=new Array();
};

var StyleTags=new Array();
StyleTags['td']=new CreateStyleDef('td','Ячейка');
	StyleTags['td'].Classes['empty']='Пустая';
	StyleTags['td'].Classes['header']='Заголовок';
	StyleTags['td'].Classes['colheader']='Заголовок колонки';
	StyleTags['td'].Classes['data1']='Ячейка листинга. Тип 1';
	StyleTags['td'].Classes['data2']='Ячейка листинга. Тип 2';
	StyleTags['td'].Classes['border']='Бордюр';


StyleTags['p']=new CreateStyleDef('p','Абзац');
	StyleTags['p'].Classes['main']='Основной абзац';
	StyleTags['p'].Classes['mainbold']='Основной абзац увеличенный';

StyleTags['li']=new CreateStyleDef('li','Листинг');
	StyleTags['li'].Classes['main']='Основной';
	StyleTags['li'].Classes['mainbold']='Увеличенный';
StyleTags['a']=new CreateStyleDef('a','Ссылка');
	StyleTags['a'].Classes['white']='Белая';
StyleTags['h1']=new CreateStyleDef('a','Заголовок 1');
	StyleTags['h1'].Classes['header_lt']='Подсвеченный';
StyleTags['h2']=new CreateStyleDef('a','Заголовок 2');
	StyleTags['h2'].Classes['header_lt']='Подсвеченный';
StyleTags['h3']=new CreateStyleDef('a','Заголовок 3');
	StyleTags['h3'].Classes['header_lt']='Подсвеченный';
StyleTags['h4']=new CreateStyleDef('a','Заголовок 4');
	StyleTags['h4'].Classes['header_lt']='Подсвеченный';
StyleTags['h5']=new CreateStyleDef('a','Заголовок 5');
	StyleTags['h5'].Classes['header_lt']='Подсвеченный';

var AvilableFormatTags=new Array();
AvilableFormatTags['h1']='Заголовок 1';
AvilableFormatTags['h2']='Заголовок 2';
AvilableFormatTags['h3']='Заголовок 3';
AvilableFormatTags['h4']='Заголовок 4';
AvilableFormatTags['h5']='Заголовок 5';
AvilableFormatTags['p']='Абзац';
AvilableFormatTags['li']='Элемент листинга';

//--------------Таги различных таблиц------
var TablesTAGS=new Array()
TablesTAGS[0]=new Array('С тонкой рамкой','<table border=0 cellpadding=0 cellspacing=0><tr><td class=border><table border=0 cellpadding=1 cellspacing=1 width=100% height=100%>','</table></td></tr></table>');
TablesTAGS[1]=new Array('Без рамки','<table border=0 cellpadding=1 cellspacing=1>','</table>');
TablesTAGS[2]=new Array('С обычной рамкой','<table border=1 cellpadding=1 cellspacing=1>','</table>');
