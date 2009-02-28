function CreateStyleDef(aName,aDispName){
	this.Name=aName;
	this.DispName=aDispName;
	this.Classes=new Array();
};

var StyleTags=new Array();
StyleTags['td']=new CreateStyleDef('td','������');
	StyleTags['td'].Classes['empty']='������';
	StyleTags['td'].Classes['header']='���������';
	StyleTags['td'].Classes['colheader']='��������� �������';
	StyleTags['td'].Classes['data1']='������ ��������. ��� 1';
	StyleTags['td'].Classes['data2']='������ ��������. ��� 2';
	StyleTags['td'].Classes['border']='������';


StyleTags['p']=new CreateStyleDef('p','�����');
	StyleTags['p'].Classes['main']='�������� �����';
	StyleTags['p'].Classes['mainbold']='�������� ����� �����������';

StyleTags['li']=new CreateStyleDef('li','�������');
	StyleTags['li'].Classes['main']='��������';
	StyleTags['li'].Classes['mainbold']='�����������';
StyleTags['a']=new CreateStyleDef('a','������');
	StyleTags['a'].Classes['white']='�����';
StyleTags['h1']=new CreateStyleDef('a','��������� 1');
	StyleTags['h1'].Classes['header_lt']='������������';
StyleTags['h2']=new CreateStyleDef('a','��������� 2');
	StyleTags['h2'].Classes['header_lt']='������������';
StyleTags['h3']=new CreateStyleDef('a','��������� 3');
	StyleTags['h3'].Classes['header_lt']='������������';
StyleTags['h4']=new CreateStyleDef('a','��������� 4');
	StyleTags['h4'].Classes['header_lt']='������������';
StyleTags['h5']=new CreateStyleDef('a','��������� 5');
	StyleTags['h5'].Classes['header_lt']='������������';

var AvilableFormatTags=new Array();
AvilableFormatTags['h1']='��������� 1';
AvilableFormatTags['h2']='��������� 2';
AvilableFormatTags['h3']='��������� 3';
AvilableFormatTags['h4']='��������� 4';
AvilableFormatTags['h5']='��������� 5';
AvilableFormatTags['p']='�����';
AvilableFormatTags['li']='������� ��������';

//--------------���� ��������� ������------
var TablesTAGS=new Array()
TablesTAGS[0]=new Array('� ������ ������','<table border=0 cellpadding=0 cellspacing=0><tr><td class=border><table border=0 cellpadding=1 cellspacing=1 width=100% height=100%>','</table></td></tr></table>');
TablesTAGS[1]=new Array('��� �����','<table border=0 cellpadding=1 cellspacing=1>','</table>');
TablesTAGS[2]=new Array('� ������� ������','<table border=1 cellpadding=1 cellspacing=1>','</table>');
