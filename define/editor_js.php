<script language=JScript>
/*onerror=displayError;

function displayError(msg, url, line){
   // Error handling routine
   alert("The following error occured:\n\n" + msg);
   return true;  // Suppresses Internet Explorer error
}
*/
<?php include('../define/css-define.js')?>

//-------------------------------------------------
// Набор команд для Эктив Икса редактирования
//-------------------------------------------------

DECMD_BOLD =                      5000
DECMD_COPY =                      5002
DECMD_CUT =                       5003
DECMD_DELETE =                    5004
DECMD_DELETECELLS =               5005
DECMD_DELETECOLS =                5006
DECMD_DELETEROWS =                5007
DECMD_FINDTEXT =                  5008
DECMD_FONT =                      5009
DECMD_GETBACKCOLOR =              5010
DECMD_GETBLOCKFMT =               5011
DECMD_GETBLOCKFMTNAMES =          5012
DECMD_GETFONTNAME =               5013
DECMD_GETFONTSIZE =               5014
DECMD_GETFORECOLOR =              5015
DECMD_HYPERLINK =                 5016
DECMD_IMAGE =                     5017
DECMD_INDENT =                    5018
DECMD_INSERTCELL =                5019
DECMD_INSERTCOL =                 5020
DECMD_INSERTROW =                 5021
DECMD_INSERTTABLE =               5022
DECMD_ITALIC =                    5023
DECMD_JUSTIFYCENTER =             5024
DECMD_JUSTIFYLEFT =               5025
DECMD_JUSTIFYRIGHT =              5026
DECMD_LOCK_ELEMENT =              5027
DECMD_MAKE_ABSOLUTE =             5028
DECMD_MERGECELLS =                5029
DECMD_ORDERLIST =                 5030
DECMD_OUTDENT =                   5031
DECMD_PASTE =                     5032
DECMD_REDO =                      5033
DECMD_REMOVEFORMAT =              5034
DECMD_SELECTALL =                 5035
DECMD_SEND_BACKWARD =             5036
DECMD_BRING_FORWARD =             5037
DECMD_SEND_BELOW_TEXT =           5038
DECMD_BRING_ABOVE_TEXT =          5039
DECMD_SEND_TO_BACK =              5040
DECMD_BRING_TO_FRONT =            5041
DECMD_SETBACKCOLOR =              5042
DECMD_SETBLOCKFMT =               5043
DECMD_SETFONTNAME =               5044
DECMD_SETFONTSIZE =               5045
DECMD_SETFORECOLOR =              5046
DECMD_SPLITCELL =                 5047
DECMD_UNDERLINE =                 5048
DECMD_UNDO =                      5049
DECMD_UNLINK =                    5050
DECMD_UNORDERLIST =               5051
DECMD_PROPERTIES =                5052

//
// Enums
//

// OLECMDEXECOPT  
OLECMDEXECOPT_DODEFAULT =         0 
OLECMDEXECOPT_PROMPTUSER =        1
OLECMDEXECOPT_DONTPROMPTUSER =    2

// DHTMLEDITCMDF
DECMDF_NOTSUPPORTED =             0 
DECMDF_DISABLED =                 1 
DECMDF_ENABLED =                  3
DECMDF_LATCHED =                  7
DECMDF_NINCHED =                  11

// DHTMLEDITAPPEARANCE
DEAPPEARANCE_FLAT =               0
DEAPPEARANCE_3D =                 1 

// OLE_TRISTATE
OLE_TRISTATE_UNCHECKED =          0
OLE_TRISTATE_CHECKED =            1
OLE_TRISTATE_GRAY =               2


var dBaseURL=String(self.location);
dBaseURL=dBaseURL.substring(0,dBaseURL.lastIndexOf('/')+1);


var MENU_SEPARATOR = "";
var ContextMenu = new Array();
var GeneralContextMenu = new Array();
var TableContextMenu = new Array();
var ImageContextMenu = new Array();

function ContextMenuItem(string, cmdId) {
  this.string = string;
  this.cmdId = cmdId;
};

GeneralContextMenu[0] = new ContextMenuItem("Вырезать", DECMD_CUT);
GeneralContextMenu[1] = new ContextMenuItem("Копировать", DECMD_COPY);
GeneralContextMenu[2] = new ContextMenuItem("Вставить", DECMD_PASTE);
GeneralContextMenu[3] = new ContextMenuItem(MENU_SEPARATOR, 0);
GeneralContextMenu[4] = new ContextMenuItem("Гиперссылка", DECMD_HYPERLINK);
TableContextMenu[0] = new ContextMenuItem(MENU_SEPARATOR, 0);
TableContextMenu[1] = new ContextMenuItem("Добавить строку", DECMD_INSERTROW);
TableContextMenu[2] = new ContextMenuItem("Удалить строку", DECMD_DELETEROWS);
TableContextMenu[3] = new ContextMenuItem(MENU_SEPARATOR, 0);
TableContextMenu[4] = new ContextMenuItem("Добавить колонку", DECMD_INSERTCOL);
TableContextMenu[5] = new ContextMenuItem("Удалить колонку", DECMD_DELETECOLS);
TableContextMenu[6] = new ContextMenuItem(MENU_SEPARATOR, 0);
TableContextMenu[7] = new ContextMenuItem("Добавить ячейку", DECMD_INSERTCELL);
TableContextMenu[8] = new ContextMenuItem("Удалить ячейку", DECMD_DELETECELLS);
TableContextMenu[9] = new ContextMenuItem("Объеденить ячейки", DECMD_MERGECELLS);
TableContextMenu[10] = new ContextMenuItem("Разбить ячейку", DECMD_SPLITCELL);
ImageContextMenu[0] = new ContextMenuItem(MENU_SEPARATOR, 0);
ImageContextMenu[1] = new ContextMenuItem("Картинка", DECMD_IMAGE);


DE_E_INVALIDARG = 0x5;
DE_E_ACCESS_DENIED = 0x46;
DE_E_PATH_NOT_FOUND = 0x80070003;
DE_E_FILE_NOT_FOUND = 0x80070002;
DE_E_UNEXPECTED = 0x8000ffff;
DE_E_DISK_FULL = 0x80070027;
DE_E_NOTSUPPORTED = 0x80040100;
DE_E_FILTER_FRAMESET = 0x80100001;
DE_E_FILTER_SERVERSCRIPT = 0x80100002;
DE_E_FILTER_MULTIPLETAGS = 0x80100004;
DE_E_FILTER_SCRIPTLISTING = 0x80100008;
DE_E_FILTER_SCRIPTLABEL = 0x80100010;
DE_E_FILTER_SCRIPTTEXTAREA = 0x80100020;
DE_E_FILTER_SCRIPTSELECT = 0x80100040;
DE_E_URL_SYNTAX = 0x800401E4;
DE_E_INVALID_URL = 0x800C0002;
DE_E_NO_SESSION = 0x800C0003;
DE_E_CANNOT_CONNECT = 0x800C0004;
DE_E_RESOURCE_NOT_FOUND = 0x800C0005;
DE_E_OBJECT_NOT_FOUND = 0x800C0006;
DE_E_DATA_NOT_AVAILABLE = 0x800C0007;
DE_E_DOWNLOAD_FAILURE = 0x800C0008;
DE_E_AUTHENTICATION_REQUIRED = 0x800C0009;
DE_E_NO_VALID_MEDIA = 0x800C000A;
DE_E_CONNECTION_TIMEOUT = 0x800C000B;
DE_E_INVALID_REQUEST = 0x800C000C;
DE_E_UNKNOWN_PROTOCOL = 0x800C000D;
DE_E_SECURITY_PROBLEM = 0x800C000E;
DE_E_CANNOT_LOAD_DATA = 0x800C000F;
DE_E_CANNOT_INSTANTIATE_OBJECT = 0x800C0010;
DE_E_REDIRECT_FAILED = 0x800C0014;
DE_E_REDIRECT_TO_DIR = 0x800C0015;
DE_E_CANNOT_LOCK_REQUEST = 0x8;

var selcount=0;
function HTMLEdit_select(){
	edit_get_objectToFormat();
};


var TableEditCodeSource='<table border=0 cellpadding=0 cellspacing=0 width=100%><tr><td>'+
		'<strong>Вставить таблицу:</strong>&nbsp;Строк:<input type=text size=5 class=text value=3 name=edit_table_rows>&nbsp;Столбцов:<input type=text size=5 class=text value=3 name=edit_table_cols>&nbsp;Тип:&nbsp;<select name=insert_table_type></select>&nbsp;<input type=button class=button value="вставить" onclick="edit_insert_table()">'+
		'</td></tr><tr><td>'+
		'<strong>Объединяемые выбранной ячейкой строки:</strong><input type=text size=5 class=text value=1 name=edit_td_rowspan onfocusin="document.all.EditHTML.DOM.onselectionchange=null;" onfocusout="document.all.EditHTML.DOM.onselectionchange=HTMLEdit_select;">&nbsp;<input type=button class=button value="изменить" onclick="table_rowSpan()">'+
		'</td></tr><tr><td>'+
		'<img src=/toolbarimages/mergecells.gif width=21 height=20 border=0 class=toolbtn onmouseover="tool_btn_over(this);" onmouseout="tool_btn_out(this);" onmousedown="tool_btn_down(this);" onmouseup="tool_btn_up(this);" name="tb_MERGECELLS" onclick="tool_btn_click(this);" alt="Объеденить ячейки">'+
		'<img src=/toolbarimages/splitcells.gif width=21 height=20 border=0 class=toolbtn onmouseover="tool_btn_over(this);" onmouseout="tool_btn_out(this);" onmousedown="tool_btn_down(this);" onmouseup="tool_btn_up(this);" name="tb_SPLITCELL" onclick="tool_btn_click(this);" alt="Разделить ячейку">'+
		'<img src=/toolbarimages/insertcell.gif width=21 height=20 border=0 class=toolbtn onmouseover="tool_btn_over(this);" onmouseout="tool_btn_out(this);" onmousedown="tool_btn_down(this);" onmouseup="tool_btn_up(this);" name="tb_INSERTCELL" onclick="tool_btn_click(this);" alt="Вставить ячейку">'+
		'<img src=/toolbarimages/insertcol.gif width=21 height=20 border=0 class=toolbtn onmouseover="tool_btn_over(this);" onmouseout="tool_btn_out(this);" onmousedown="tool_btn_down(this);" onmouseup="tool_btn_up(this);" name="tb_INSERTCOL" onclick="tool_btn_click(this);" alt="Вставить колонку">'+
		'<img src=/toolbarimages/insertrow.gif width=21 height=20 border=0 class=toolbtn onmouseover="tool_btn_over(this);" onmouseout="tool_btn_out(this);" onmousedown="tool_btn_down(this);" onmouseup="tool_btn_up(this);" name="tb_INSERTROW" onclick="tool_btn_click(this);" alt="Вставить строку">'+
		'<img src=/format.gif border=0 width=10><img src=/toolbarimages/deletecells.gif width=21 height=20 border=0 class=toolbtn onmouseover="tool_btn_over(this);" onmouseout="tool_btn_out(this);" onmousedown="tool_btn_down(this);" onmouseup="tool_btn_up(this);" name="tb_DELETECELLS" onclick="tool_btn_click(this);" alt="Удалить ячейки">'+
		'<img src=/toolbarimages/deletecols.gif width=21 height=20 border=0 class=toolbtn onmouseover="tool_btn_over(this);" onmouseout="tool_btn_out(this);" onmousedown="tool_btn_down(this);" onmouseup="tool_btn_up(this);" name="tb_DELETECOLS" onclick="tool_btn_click(this);" alt="Удалить столбцы">'+
		'<img src=/toolbarimages/deleterows.gif width=21 height=20 border=0 class=toolbtn onmouseover="tool_btn_over(this);" onmouseout="tool_btn_out(this);" onmousedown="tool_btn_down(this);" onmouseup="tool_btn_up(this);" name="tb_DELETEROWS" onclick="tool_btn_click(this);" alt="Удалить строки">'+
		'<img src=/format.gif border=0 width=10>'+
		'<img src=/toolbarimages/valign-top.gif width=21 height=20 border=0 class=toolbtn onmouseover="tool_btn_over(this);" onmouseout="tool_btn_out(this);" onmousedown="tool_btn_down(this);" onmouseup="tool_btn_up(this);" name="tb_VALIGNTOP" onclick="table_valign(\'top\');" alt="Вертикальное выравнивание - верх">'+
		'<img src=/toolbarimages/valign-middle.gif width=21 height=20 border=0 class=toolbtn onmouseover="tool_btn_over(this);" onmouseout="tool_btn_out(this);" onmousedown="tool_btn_down(this);" onmouseup="tool_btn_up(this);" name="tb_VALIGNMIDDLE" onclick="table_valign(\'middle\');" alt="Вертикальное выравнивание - посередине">'+
		'<img src=/toolbarimages/valign-bottom.gif width=21 height=20 border=0 class=toolbtn onmouseover="tool_btn_over(this);" onmouseout="tool_btn_out(this);" onmousedown="tool_btn_down(this);" onmouseup="tool_btn_up(this);" name="tb_VALIGNBOTTOM" onclick="table_valign(\'bottom\');" alt="Вертикальное выравнивание - вниз">'+

		'</td></tr></table>';

function show_hide_table_div(){
	theDiv=document.all.div_edit_tables;
	theDiv.innerHTML=(theDiv.innerHTML=='')?TableEditCodeSource:'';
	init_insert_table_type();
};

function init_insert_table_type(){
	if(document.all.insert_table_type){
		document.all.insert_table_type.options.length=1;
		for(aCounter=0;aCounter<TablesTAGS.length;aCounter++){
			document.all.insert_table_type.options.length=aCounter+1;
			document.all.insert_table_type.options(aCounter).value=TablesTAGS[aCounter][0];
			document.all.insert_table_type.options(aCounter).innerText=TablesTAGS[aCounter][0];
		};
	};
}
var aCurrentElement;

function edit_find_element(){
	var sel=document.all.EditHTML.DOM.selection;
	if(sel.type=="Control"){
		try{
		    cll=sel.createRangeCollection();
    		    var aElement=cll(0).parentElement;
		}catch(e){
		    return null;
		};
	}else{
		var range=sel.createRange();
		var aElement=range.parentElement();
	};
	CanReturn=false;
	while(true){
		for(cTag in StyleTags){
			if(String(aElement.tagName).toUpperCase()==String(cTag).toUpperCase())CanReturn=true;
		};
		if((String(aElement.tagName).toUpperCase()=='BODY')||CanReturn)break;
		aElement=aElement.parentElement;
	};
	if(String(aElement.tagName).toUpperCase()=='BODY'){
		return null;
	}else{
		if(String(aElement.parentElement.tagName).toUpperCase()=='TD')aElement=aElement.parentElement;
	};
	return aElement;
};

function edit_get_selected_elements(){
	inDoc=document.all.EditHTML.DOM;
	var sel=inDoc.selection;
	var temporaryTextRange=inDoc.body.createTextRange();
	if((sel.type!='Text')&&(sel.type!='None'))return null;
	var theRange=sel.createRange();
	var InSelectionAbleTags=new Array();
	for(cTag in StyleTags){
		theElements=inDoc.getElementsByTagName(cTag);
		for(lcounter=0;lcounter<theElements.length;lcounter++){
			temporaryTextRange.moveToElementText(theElements.item(lcounter));
			if(theRange.inRange(temporaryTextRange)) //||theRange.inRange(temporaryTextRange)
				InSelectionAbleTags[cTag]=true;
		};
	};

	document.all.edit_group_formating_tag.onchange=null;
	document.all.edit_group_formating_class.onchange=null;
	document.all.edit_group_formating_tag.options.length=1;
	document.all.edit_group_formating_class.options.length=1;

	counter=0;
	for(theTag in InSelectionAbleTags){
		counter++;
		document.all.edit_group_formating_tag.options.length++;
		document.all.edit_group_formating_tag.options(counter).value=theTag;
		document.all.edit_group_formating_tag.options(counter).innerText=StyleTags[theTag].DispName;
	};
	document.all.edit_group_formating_tag.onchange=edit_group_formating_tag_changed;
	if(counter>=1)document.all.edit_group_formating_tag.selectedIndex=1;
	edit_group_formating_tag_changed();
	delete temporaryTextRange;
};

function edit_do(oCmd,oParam){
	theCommand=eval("DECMD_"+oCmd);
	document.all.EditHTML.ExecCommand(theCommand,oParam);
	if(theCommand==DECMD_IMAGE)edit_scan_for_images();
	edit_get_objectToFormat();
};

function edit_on_content_menu_try(xPos,yPos){
	var menuStrings = new Array();
	var menuStates = new Array();
	var state;
	var i;
	var idx = 0;
	ContextMenu.length = 0;
	for (i=0; i<GeneralContextMenu.length; i++) {
		ContextMenu[idx++] = GeneralContextMenu[i];
	};

	if (document.all.EditHTML.QueryStatus(DECMD_INSERTROW) != DECMDF_DISABLED) {
		for (i=0; i<TableContextMenu.length; i++) {
			ContextMenu[idx++] = TableContextMenu[i];
		};
	};

	if (document.all.EditHTML.QueryStatus(DECMD_IMAGE) != DECMD_IMAGE) {
		for (i=0; i<ImageContextMenu.length; i++) {
			ContextMenu[idx++] = ImageContextMenu[i];
		};
	};


	for (i=0; i<ContextMenu.length; i++) {
		menuStrings[i] = ContextMenu[i].string;
		if (menuStrings[i] != MENU_SEPARATOR) {
			state = document.all.EditHTML.QueryStatus(ContextMenu[i].cmdId);
		} else {
			state = DECMDF_ENABLED;
		}
		if (state == DECMDF_DISABLED || state == DECMDF_NOTSUPPORTED) {
			menuStates[i] = OLE_TRISTATE_GRAY;
		} else if (state == DECMDF_ENABLED || state == DECMDF_NINCHED) {
			menuStates[i] = OLE_TRISTATE_UNCHECKED;
		} else { // DECMDF_LATCHED
			menuStates[i] = OLE_TRISTATE_CHECKED;
		}
	};
	document.all.EditHTML.SetContextMenu(menuStrings, menuStates);
};

function edit_on_content_menu_action(itemIndex){
	if (ContextMenu[itemIndex].cmdId == DECMD_INSERTTABLE) {
//		InsertTable();
	} else if (ContextMenu[itemIndex].cmdId == DECMD_HYPERLINK){
		click_hyperlink();
	}else{
		document.all.EditHTML.ExecCommand(ContextMenu[itemIndex].cmdId, OLECMDEXECOPT_DODEFAULT);
		if(ContextMenu[itemIndex].cmdId==DECMD_IMAGE)edit_scan_for_images();

	};
};

function edit_init_html_events(){
	document.all.EditHTML.DOM.onselectionchange=HTMLEdit_select;
	document.all.edit_change_element_tag.options.length=1;
	theCounter=0;
	for(FormatTag in AvilableFormatTags){
		theCounter++;
		document.all.edit_change_element_tag.options.length=theCounter+1;
		document.all.edit_change_element_tag.options(theCounter).value=FormatTag;
		document.all.edit_change_element_tag.options(theCounter).innerText=AvilableFormatTags[FormatTag];
		
	};
	edit_get_objectToFormat();
};

function edit_init(){
	setTimeout('edit_loadsource()',1000);
};

function edit_cleardoc(){
	try{
		document.all.EditHTML.LoadURL(dBaseURL+'edit_get_textpage.php?rnum='+Math.random());
	}catch(e){
	}
	setTimeout('edit_init_html_events()',1000);
};
function edit_commit(){
	var Disclaimer='Перед сохранением документа убедитесь что указаны все файлы новых изображений в документе.\nДля этого перед сохранением документа заполните появившиеся поля ввода под редактируемым\nтекстом путём нажатия кнопки "Обзор..."\nНажмите ОК в случае если это уже сделано или Отмену в противном случае.'
	if(!confirm(Disclaimer))return;
	var TheHTML=String(document.all.EditHTML.DocumentHTML);
	TheHTML=TheHTML.replace(/(> )([\s\r\n]+)/gi,"$1");
	TheHTML=TheHTML.replace(/height\:\s*\d+px(\;)*/gi,"");
	document.forms['savehtml'].htmlsrc.value=TheHTML;
	document.forms['savehtml'].submit();
};

function edit_loadsource(){
	document.all.EditHTML.LoadURL(dBaseURL+'edit_get_textpage.php?text='+theTextID+'&rnum='+Math.random());
	setTimeout('edit_init_html_events()',1000);
};

function edit_scan_for_images(){
	aPromt='<table border=0 width=100% cellpadding=0 cellspacing=0><tr><td class=border><table border=0 width=100% cellpadding=1 cellspacing=1><tr><td colspan=2 align=center class=header>Файлы вставленных картинок (заполнять после вставки всех картинок):</td></tr>\r\n';
	inDoc=document.all.EditHTML.DOM;
	tdstyle='frm_data2';
	var theCounter=0;
	for(aImage in inDoc.images){
		if(String(inDoc.images[aImage].src).substr(0,4)=='file'){
			theCounter++;
			aPromt+='<tr><td class='+tdstyle+' align=right>'+inDoc.images[aImage].alt+'</td>';
			aPromt+='<td class='+tdstyle+'><input type=file class=text name="uploadfile'+theCounter+'" size=60></td>';
			tdstyle=(tdstyle=='frm_data2')?'frm_data1':'frm_data2';
		};
	};
	aPromt+='</table></td></tr></table>';
	document.all.fileslist.innerHTML=aPromt;
	return theCounter;
};


function edit_group_formating_tag_changed(){
		document.all.edit_group_formating_class.options.length=1;
		document.all.edit_group_formating_class.selectedIndex=0;
		if(document.all.edit_group_formating_tag.selectedIndex==0){
			document.all.edit_group_formating_class.disabled=true;
			return;
		}else{
			document.all.edit_group_formating_class.disabled=false;
		};
		TheTagFmt=StyleTags[document.all.edit_group_formating_tag.options(document.all.edit_group_formating_tag.selectedIndex).value];
		counter=0;
		for(theClass in TheTagFmt.Classes){
			counter++;
			document.all.edit_group_formating_class.options.length++;
			document.all.edit_group_formating_class.options(counter).value=theClass;
			document.all.edit_group_formating_class.options(counter).innerText=TheTagFmt.Classes[theClass];
		};
		document.all.edit_group_formating_class.onchange=edit_group_formating_class_changed;
		document.all.edit_group_formating_class.selectedIndex=0;
};

function edit_group_formating_class_changed(){
	if(document.all.edit_group_formating_tag.selectedIndex==0)return;
	theTargetTag=document.all.edit_group_formating_tag.options(document.all.edit_group_formating_tag.selectedIndex).value;
	NeedClass=document.all.edit_group_formating_class.options(document.all.edit_group_formating_class.selectedIndex).value;
	inDoc=document.all.EditHTML.DOM;
	var sel=inDoc.selection;
	var temporaryTextRange=inDoc.body.createTextRange();
	if((sel.type!='Text')&&(sel.type!='None'))return null;
	var theRange=sel.createRange();
	theElements=inDoc.getElementsByTagName(theTargetTag);
	for(lcounter=0;lcounter<theElements.length;lcounter++){
		temporaryTextRange.moveToElementText(theElements.item(lcounter));
		if(theRange.inRange(temporaryTextRange))
			theElements.item(lcounter).className=NeedClass;
	};
	delete temporaryTextRange;
};


function edit_class_changed(){
	if(aCurrentElement!=null){
		aCurrentElement.className=document.all.edit_change_element_class.options(document.all.edit_change_element_class.selectedIndex).value;
	};
};

function edit_tagname_changed(){
	theTagName=document.all.edit_change_element_tag.options(document.all.edit_change_element_tag.selectedIndex).value;
	if(document.all.edit_change_element_tag.selectedIndex==0)theTagName='p'
	if(aCurrentElement!=null){
		var oNewElement = document.all.EditHTML.DOM.createElement(theTagName);
		oNewElement.innerHTML = aCurrentElement.innerHTML;
		aCurrentElement.replaceNode(oNewElement);
		temporaryTextRange=document.all.EditHTML.DOM.body.createTextRange();
		temporaryTextRange.moveToElementText(oNewElement)
		temporaryTextRange.select();
		delete temporaryTextRange;
	};
};

function edit_get_objectToFormat(){
	aCurrentElement=edit_find_element();
	document.all.edit_change_element_class.onchange=null;
	document.all.edit_change_element_tag.onchange=null;
	document.all.edit_change_element_class.options.length=1;
	aPromt='Выберите элемент.';
	TheDisableChTag=true;
	if(aCurrentElement!=null){
		TheTagFmt=StyleTags[String(aCurrentElement.tagName).toLowerCase()];
		CurrentClassName=aCurrentElement.className;
		aPromt='Стиль для "'+TheTagFmt.DispName+'" : ';
		document.all.edit_change_element_class.options.length=1;
		document.all.edit_change_element_class.selectedIndex=0;
		counter=0;
		for(theClass in TheTagFmt.Classes){
			counter++;
			document.all.edit_change_element_class.options.length++;
			if(CurrentClassName==theClass)document.all.edit_change_element_class.selectedIndex=counter;
			document.all.edit_change_element_class.options(counter).value=theClass;
			document.all.edit_change_element_class.options(counter).innerText=TheTagFmt.Classes[theClass];
		};
		document.all.edit_change_element_class.onchange=edit_class_changed;
		document.all.edit_change_element_tag.selectedIndex=0;
		for(counter=1;counter<document.all.edit_change_element_tag.options.length;counter++)
			if(document.all.edit_change_element_tag.options(counter).value==String(aCurrentElement.tagName).toLowerCase())document.all.edit_change_element_tag.selectedIndex=counter;
		TheDisableChTag=(String(aCurrentElement.tagName).toLowerCase()=='a');
		TheDisableChTag=TheDisableChTag||(String(aCurrentElement.tagName).toLowerCase()=='td');
		document.all.edit_change_element_tag.onchange=edit_tagname_changed;
		table_rowSpan_get();
	};
	document.all.edit_change_element_tag.disabled=TheDisableChTag;
	document.all.div_classeslist.innerHTML=aPromt;
	edit_get_selected_elements();
};

function edit_insert_table(){
	TheCols=Number(document.all.edit_table_cols.value);
	TheCols=(TheCols>0)?TheCols:3;
	TheRows=Number(document.all.edit_table_rows.value);
	TheRows=(TheRows>0)?TheRows:3;
	TableHTML=TablesTAGS[document.all.insert_table_type.selectedIndex][1];
	for(TheCC1=0;TheCC1<TheRows;TheCC1++){          
		TableHTML+='<tr>';
			for(TheCC2=0;TheCC2<TheCols;TheCC2++)TableHTML+='<td class=empty>&nbsp;</td>';
		TableHTML+='</tr>';
	};
	TableHTML+=TablesTAGS[document.all.insert_table_type.selectedIndex][2];

	sel=document.all.EditHTML.DOM.selection;
	range=sel.createRange();
	range.pasteHTML(TableHTML);
};

function table_valign(aValign){
	if(!(String(aCurrentElement.tagName).toLowerCase()=='td'))return;
	aCurrentElement.vAlign=aValign;
};

function table_rowSpan(){
	var SpanCount=Number(document.all.edit_td_rowspan.value)>0?Number(document.all.edit_td_rowspan.value):1;
	if(!(String(aCurrentElement.tagName).toLowerCase()=='td'))return;
	aCurrentElement.rowSpan=SpanCount;
};

function table_rowSpan_get(){
	if(!(String(aCurrentElement.tagName).toLowerCase()=='td'))return;
	if(document.all.edit_td_rowspan)document.all.edit_td_rowspan.value=aCurrentElement.rowSpan;
};

function edit_removeformat(){
	document.all.EditHTML.ExecCommand(DECMD_REMOVEFORMAT);
	var inDoc=document.all.EditHTML.DOM;
	var sel=inDoc.selection;
	var temporaryTextRange=inDoc.body.createTextRange();
	if((sel.type!='Text')&&(sel.type!='None'))return null;
	var theRange=sel.createRange();
	theElements=document.all.EditHTML.DOM.all;
	var fontReplacer=/<font[\s\S]+?(>)([\s\S]+?)(<\/font>)/gi;
	for(lcounter=0;lcounter<theElements.length;lcounter++){
		theElement=theElements.item(lcounter);
		try{
			temporaryTextRange.moveToElementText(theElement);
			if(theRange.inRange(temporaryTextRange)){
				theElement.innerHTML=String(theElement.innerHTML).replace(fontReplacer,"$2");
				theElement.style.cssText='';
				if(theElement.className)theElement.className='';
			};
		}catch(e){};
	};
	delete temporaryTextRange;
};

function modal_dialog_href(inputParams){
//showModelessDialog
	return self.showModalDialog("post.php?text="+theTextID+"&modal=href",inputParams,"center:yes;edge:rized;resizable:no;scroll:no;help:no;status:no;unadorned:yes;dialogWidth:400px;dialogHeight:200px");
};

function click_hyperlink(theBtn){
	var sel=document.all.EditHTML.DOM.selection;
	var siteURLsearcher=/http\:\/\/([\s\S])+?\//gi;
	if(sel.type=="Control"){
		cll=sel.createRangeCollection();
		var aElement=cll(0).parentElement;
	}else{
		var range=sel.createRange();
		var aElement=range.parentElement();
	};
	if(String(aElement.tagName).toUpperCase()=='A'){
		var mdParams=new Array();
		mdParams["href"]=aElement.href;
		mdParams["target"]=aElement.target;
		var matches=String(mdParams["href"]).match(siteURLsearcher);
		if(matches)
			if(dBaseURL.search(matches[0])==0){
				mdParams["href"]=String(mdParams["href"]).substring(matches[0].length-1,String(mdParams["href"]).length);
			};
		res=modal_dialog_href(mdParams);
		if(res){
			if(res["href"]){
				aElement.href=res["href"];
				aElement.target=res["target"];
			}else{
				aElement.removeNode();
			}
		};
	}else{
		if(document.all.EditHTML.QueryStatus(DECMD_HYPERLINK)==DECMDF_ENABLED){
			var mdParams=new Array();
			mdParams["href"]="";
			mdParams["target"]="_self";
			res=modal_dialog_href(mdParams);
			if(res){
				document.all.EditHTML.ExecCommand(DECMD_HYPERLINK,OLECMDEXECOPT_DONTPROMPTUSER,res["href"]);
				sel=document.all.EditHTML.DOM.selection;
				if(sel.type=="Control"){
					cll=sel.createRangeCollection();
					aElement=cll(0).parentElement;
				}else{
					range=sel.createRange();
					aElement=range.parentElement();
				};
//				aElement.href=res["href"];
				aElement.target=res["target"];
			};
		};
	};
};

function toglegrid(){
	document.all.EditHTML.ShowBorders = !document.all.EditHTML.ShowBorders;
};

function togleinvis(){
	document.all.EditHTML.object.ShowDetails = !document.all.EditHTML.object.ShowDetails;
};

</script>
