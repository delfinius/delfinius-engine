<?php
	include('common-admin.php');
//////////////////////////////////////////////
// Начало вывода html для вставки гиперссылки
	if(isset($HTTP_GET_VARS["modal"])){
		if($HTTP_GET_VARS["modal"]=="href"){
			$newlinktext=(isset($HTTP_GET_VARS["href"]))?$HTTP_GET_VARS["href"]:"";
			$newlinktarget=(isset($HTTP_GET_VARS["target"]))?$HTTP_GET_VARS["target"]:"";
			$textID=$HTTP_GET_VARS["text"];
			$aTree=$SiteTree->GetTree(0,"`visible`=1");
			$stOptions="";
			foreach($aTree as $aKey => $aElement){
				$deepstr=str_pad("",$aElement["dbs_deep"]*12,"&nbsp;&nbsp;",STR_PAD_LEFT);
				$stOptions.="<option value=\"$contentscript?id=" . $aElement[$SiteTree->cKeyField] . "\">$deepstr" . CutQuots($aElement[$SiteTree->cNameField]);
			};
			$sql="select `text`, `filename`, `originalname` from `texts_atts` where `text`=$textID";
			$db->sql_query($sql);
			$atOptions="";
			while($row=$db->sql_fetchrow()){
				$atOptions.="<option value=\"" . $row["filename"] . "\">" . CutQuots($row["originalname"]);
			};
			if($HTTP_POST_VARS["action"]=="attachfile"){
				if($HTTP_POST_FILES["attachment"]){
					$filename=$HTTP_POST_FILES["attachment"]["name"];
					$dotpos = strrpos($filename,'.');
					$fileExt = substr ($filename,$dotpos);
					$newFileName=edit_find_freefilename($textsattachmentsdir . "attachment_" . $textID . "_", 1, $fileExt);
					if(copy($HTTP_POST_FILES["attachment"]["tmp_name"], $doc_root . $newFileName)){
						$sql="insert into `texts_atts` (`text`, `filename`, `originalname`) values ($textID, '" . addslashes($newFileName) . "', '" . addslashes($filename) . "')";
						$db->sql_query($sql);
						$newlinktext=$newFileName;
						$atOptions.="<option value=\"" . $newlinktext . "\">" . CutQuots($filename);
					};
				};
				echo "<form name=newdata><input type=hidden name=newfile value=\"$newlinktext\"><select name=newfileslist>$atOptions</select></form>";
				exit();
			};
		?>


<html><head><title>свойства гиперссылки</title>
<meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=windows-1251">
<?php include ("../style.php");?>
</head>
<script>
var globargs=new Array();
globargs["href"]="<?php echo $newlinktext?>";
globargs["target"]="<?php echo $newlinktarget?>";
function onloadevent(){
	var args=(String(globargs["href"]).length>0)?globargs:self.dialogArguments;
	if(!args)return;
	theForm=document.forms["dialogform"];
	for(counter=0;counter<theForm.seltarget.options.length;counter++){
		if(theForm.seltarget.options[counter].value==args["target"])theForm.seltarget.selectedIndex=counter;
	};

	for(counter=0;counter<theForm.sitetree.options.length;counter++){
		if(theForm.sitetree.options[counter].value==args["href"]){
			theForm.sitetree.selectedIndex=counter;
			theForm.linktype[1].checked=true;
		};
	};

	for(counter=0;counter<theForm.sitetree.options.length;counter++){
		if(theForm.sitetree.options[counter].value==args["href"]){
			theForm.sitetree.selectedIndex=counter;
			theForm.linktype[1].checked=true;
		};
	};

	for(counter=0;counter<theForm.attachments.options.length;counter++){
		if(theForm.attachments.options[counter].value==args["href"]){
			theForm.attachments.selectedIndex=counter;
			theForm.linktype[2].checked=true;
		};
	};
	if(theForm.linktype[1].checked){
		theForm.sitetree.focus();
	}else if(theForm.linktype[2].checked){
		theForm.attachments.focus();
	}else if(theForm.linktype[0].checked){
		if(args['href'])theForm.href.value=args['href'];
		theForm.href.focus();
	}else{
		if(args['href'])theForm.href.value="";
	};
};

function okay(){
	retv=new Array();
	if(theForm.linktype[1].checked){
		retv["href"]=document.forms["dialogform"].sitetree.options[document.forms["dialogform"].sitetree.selectedIndex].value;
	}else if(theForm.linktype[2].checked){
		if(document.forms["dialogform"].attachments.selectedIndex<0){
			alert("Выберите или загрузите файл!");
			return;
		};
		retv["href"]=document.forms["dialogform"].attachments.options[document.forms["dialogform"].attachments.selectedIndex].value;
	}else{
		retv["href"]=document.forms["dialogform"].href.value;
	};
	retv["target"]=document.forms["dialogform"].seltarget.options[document.forms["dialogform"].seltarget.selectedIndex].value;
	self.returnValue=retv;
	self.close();
};

function uploadedfile(){
	if(!(uploadframe.document.forms.length>0))return;
	theForm=document.forms["dialogform"];
	theForm.attachments.selectedIndex=0;
	theForm.attachments.options.length=1;
	for(counter=0;counter<uploadframe.document.forms["newdata"].newfileslist.options.length;counter++){
		theForm.attachments.options.length=counter+1;
		theForm.attachments.options(counter).value=uploadframe.document.forms["newdata"].newfileslist.options(counter).value;
		theForm.attachments.options(counter).innerText=uploadframe.document.forms["newdata"].newfileslist.options(counter).innerText;
	};
	globargs["href"]=uploadframe.document.forms["newdata"].newfile.value;
	onloadevent();
};

</script>
<body onload=onloadevent()><table border=0 width=100% height=100% cellpadding=1 cellspacing=1>
<form name=dialogform onsubmit="okay();return false;">
<tr><td class=data1 align=right>открыть в:</td><td class=data1 align=left>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<select name=seltarget><option value=_self>том же окне<option value=_blank>новом окне</select></td></tr>
<tr><td class=data1 align=right>адрес:</td><td class=data1 align=left><input type=radio name=linktype checked><input type=text class=text size=30 name=href></td></tr>
<tr><td class=data1 align=right>или ветка:</td><td class=data1 align=left><input type=radio name=linktype><select name=sitetree style="width:250px;"><?php echo $stOptions?></select></td></tr>
<tr><td class=data1 align=right>или файл:</td><td class=data1 align=left><input type=radio name=linktype><select name=attachments style="width:250px;"><?php echo $atOptions?></select></td></tr>
</form>
<form name=fileuploadform method=post target=uploadframe onsubmit="alert('Сейчас будет произведена загрузка файла.\nДождитесь обновления списка файлов.');return true;" enctype="multipart/form-data">
<tr><td class=data1 align=right valign=top>&nbsp;</td><td class=data1 align=left>новый:&nbsp;<input type=file name=attachment size=18 class=text>&nbsp;<input type=submit class=button value="загрузить"><input type=hidden name=action value=attachfile>
</td></tr>
</form>
<tr><td class=data1 align=center colspan=2><input type=button class=button value="  ОК  " onclick="okay()">&nbsp;<input type=button class=button value=" отмена " onclick="self.close()"></td></tr>
</table>
<iframe width=1 height=1 name=uploadframe onload=uploadedfile()></iframe>
</body></html>


		<?php
		};
		exit();
	};
// Конец вывода html для вставки гиперссылки
//////////////////////////////////////////////

	$canuseeditor = true;//strstr($HTTP_SERVER_VARS["HTTP_USER_AGENT"],"MSIE");
	$theTextID = 1;
	$Result='';
	$theTextID=($HTTP_POST_VARS['textID']>0)?$HTTP_POST_VARS['textID']:$theTextID;
	$InputHTML=edit_cutbody($HTTP_POST_VARS['htmlsrc']);
	$InputHTML=edit_replace_images($InputHTML,$theTextID);
	if(strlen($InputHTML)>0){
//		$sql="update texts set text='" . str_replace("'","''", addslashes($InputHTML)) . "' where id=" . $theTextID;
		$sql="update texts set text='" . str_replace("'","''", $InputHTML) . "' where id=" . $theTextID;
		if(!$db->sql_query($sql)){
		    $sqlerror=$db->sql_error();
		    die($sqlerror['message']);
		};
	};

?>
<html>
<head>
<title>Визуальный HTML-редактор</title>
<meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=windows-1251">
<style>
img.toolbtn	{filter:gray();border:solid 1px #808080}
</style>
</head>
<script>
var TextNum=1
</script>
<?php include ("./../define/editor_js.php");?>
<script>
var theTextID=<?php echo $theTextID?>;

function tool_btn_over(theBtn){
	theBtn.style.border='solid 1px #000000';
	theBtn.style.filter='';
};

function tool_btn_out(theBtn){
	theBtn.style.border='solid 1px #808080';
	theBtn.style.filter='gray()';
};

function tool_btn_down(theBtn){
	theBtn.style.filter='Invert()';
};

function tool_btn_up(theBtn){
	theBtn.style.filter='';
};

function tool_btn_click(theBtn){
	theCommand=String(theBtn.name);
	theCommand=theCommand.substring(3,theCommand.length);
	try{
		edit_do(theCommand);
	}catch(e){};
};


function editinhtml(){
	if(confirm('Если вы редактировали текст, перед переходом в режим правки HTML необходимо сохранить изменения.\r\nПродолжить?'))
		document.forms['edithtml'].submit();
};

function tool_btn_click_prop(theBtn){
	theCommand=String(theBtn.name);
	theCommand=theCommand.substring(3,theCommand.length);
	theCommand=eval("DECMD_"+theCommand);
	alert(document.all.EditHTML.QueryStayus(theCommand));
	document.all.EditHTML.ExecCommand(theCommand);
};

</script>
<?php include ('../style.php')?>
<style>
td.border	{background-color:black;}
td.frm_data1	{background-color:#EEEEEE;color:#000000;}
td.frm_data2	{background-color:#FFFFFF;color:#000000;}
</style>
<body onload="edit_init()">
<table border=0 width=100% height=100% cellpadding=0 cellspacing=0><tr><td bgcolor=#777777 c_lass=border><table cellpadding=0 cellspacing=2 width=100% height=100%>
<tr><td valign=top>
	<table width=100% border=0 cellpadding=0 cellspacing=0><tr><td class=border>
	<table width=100% border=0 cellpadding=2 cellspacing=1>
	<tr><td bgcolor=#AAAAAA colspan=4><img src=/toolbarimages/new.gif width=21 height=20 border=0 class=toolbtn onmouseover="tool_btn_over(this);" onmouseout="tool_btn_out(this);" onmousedown="tool_btn_down(this);" onmouseup="tool_btn_up(this);" name='tb_NEW' onclick="edit_cleardoc()" alt="Очистить"><img
	 src=/toolbarimages/open.gif width=21 height=20 border=0 class=toolbtn onmouseover="tool_btn_over(this);" onmouseout="tool_btn_out(this);" onmousedown="tool_btn_down(this);" onmouseup="tool_btn_up(this);" name='tb_OPEN' onclick="edit_loadsource();" alt="Загрузить исходный вариант"><img
	 src=/toolbarimages/save.gif width=21 height=20 border=0 class=toolbtn onmouseover="tool_btn_over(this);" onmouseout="tool_btn_out(this);" onmousedown="tool_btn_down(this);" onmouseup="tool_btn_up(this);" name='tb_SAVE' onclick="edit_commit();" alt="Сохранить изменения"><img src=/format.gif border=0 width=10><img
	 src=/toolbarimages/copy.gif width=21 height=20 border=0 class=toolbtn onmouseover="tool_btn_over(this);" onmouseout="tool_btn_out(this);" onmousedown="tool_btn_down(this);" onmouseup="tool_btn_up(this);" name='tb_COPY' onclick="tool_btn_click(this);" alt="Копировать"><img
	 src=/toolbarimages/cut.gif width=21 height=20 border=0 class=toolbtn onmouseover="tool_btn_over(this);" onmouseout="tool_btn_out(this);" onmousedown="tool_btn_down(this);" onmouseup="tool_btn_up(this);" name='tb_CUT' onclick="tool_btn_click(this);" alt="Вырезать"><img
	 src=/toolbarimages/paste.gif width=21 height=20 border=0 class=toolbtn onmouseover="tool_btn_over(this);" onmouseout="tool_btn_out(this);" onmousedown="tool_btn_down(this);" onmouseup="tool_btn_up(this);" name='tb_PASTE' onclick="tool_btn_click(this);" alt="Вставить"><img src=/format.gif border=0 width=10><img
	 src=/toolbarimages/undo.gif width=21 height=20 border=0 class=toolbtn onmouseover="tool_btn_over(this);" onmouseout="tool_btn_out(this);" onmousedown="tool_btn_down(this);" onmouseup="tool_btn_up(this);" name='tb_UNDO' onclick="tool_btn_click(this);" alt="Вернуть"><img
	 src=/toolbarimages/redo.gif width=21 height=20 border=0 class=toolbtn onmouseover="tool_btn_over(this);" onmouseout="tool_btn_out(this);" onmousedown="tool_btn_down(this);" onmouseup="tool_btn_up(this);" name='tb_REDO' onclick="tool_btn_click(this);" alt="Повторить"><img src=/format.gif border=0 width=10><img
	 src=/toolbarimages/hyperlink.gif width=21 height=20 border=0 class=toolbtn onmouseover="tool_btn_over(this);" onmouseout="tool_btn_out(this);" onmousedown="tool_btn_down(this);" onmouseup="tool_btn_up(this);" name='tb_HYPERLINK' onclick="click_hyperlink(this);" alt="Гиперссылка"><img
	 src=/toolbarimages/table.gif width=21 height=20 border=0 class=toolbtn onmouseover="tool_btn_over(this);" onmouseout="tool_btn_out(this);" onmousedown="tool_btn_down(this);" onmouseup="tool_btn_up(this);" name='tb_TABLE' onclick="show_hide_table_div();" alt="Работа с таблицами"><img
	 src=/toolbarimages/image.gif width=21 height=20 border=0 class=toolbtn onmouseover="tool_btn_over(this);" onmouseout="tool_btn_out(this);" onmousedown="tool_btn_down(this);" onmouseup="tool_btn_up(this);" name='tb_IMAGE' onclick="tool_btn_click(this);" alt="Вставить картинку или изменять свойства выбранной"><img src=/format.gif border=0 width=10><img
	 src=/toolbarimages/invisibles.gif width=21 height=20 border=0 class=toolbtn onmouseover="tool_btn_over(this);" onmouseout="tool_btn_out(this);" onmousedown="tool_btn_down(this);" onmouseup="tool_btn_up(this);" name='tb_INVISIBLES' onclick="togleinvis()" alt="вкл/выкл невидимые элементы"><img
	 src=/toolbarimages/grid.gif width=21 height=20 border=0 class=toolbtn onmouseover="tool_btn_over(this);" onmouseout="tool_btn_out(this);" onmousedown="tool_btn_down(this);" onmouseup="tool_btn_up(this);" name='tb_ABLEGRID' onclick="toglegrid()" alt="вкл/выкл отображение границ таблиц"></td>
	</tr>
	<tr><td bgcolor=#AAAAAA colspan=4><img src=/toolbarimages/bold.gif width=21 height=20 border=0 class=toolbtn onmouseover="tool_btn_over(this);" onmouseout="tool_btn_out(this);" onmousedown="tool_btn_down(this);" onmouseup="tool_btn_up(this);" name='tb_BOLD' onclick="tool_btn_click(this);" alt="Жирный"><img
	 src=/toolbarimages/italic.gif width=21 height=20 border=0 class=toolbtn onmouseover="tool_btn_over(this);" onmouseout="tool_btn_out(this);" onmousedown="tool_btn_down(this);" onmouseup="tool_btn_up(this);" name='tb_ITALIC' onclick="tool_btn_click(this);" alt="Курсив"><img
	 src=/toolbarimages/underline.gif width=21 height=20 border=0 class=toolbtn onmouseover="tool_btn_over(this);" onmouseout="tool_btn_out(this);" onmousedown="tool_btn_down(this);" onmouseup="tool_btn_up(this);" name='tb_UNDERLINE' onclick="tool_btn_click(this);" alt="Подчёркнутый"><img
	 src=/toolbarimages/removeformat.gif width=21 height=20 border=0 class=toolbtn onmouseover="tool_btn_over(this);" onmouseout="tool_btn_out(this);" onmousedown="tool_btn_down(this);" onmouseup="tool_btn_up(this);" name='tb_REMOVEFORMAT' onclick="edit_removeformat();" alt="Удалить форматирование"><img src=/format.gif border=0 width=10><img
	 src=/toolbarimages/justifyleft.gif width=21 height=20 border=0 class=toolbtn onmouseover="tool_btn_over(this);" onmouseout="tool_btn_out(this);" onmousedown="tool_btn_down(this);" onmouseup="tool_btn_up(this);" name='tb_JUSTIFYLEFT' onclick="tool_btn_click(this);" alt="Выровнять по левому краю"><img
	 src=/toolbarimages/justifycenter.gif width=21 height=20 border=0 class=toolbtn onmouseover="tool_btn_over(this);" onmouseout="tool_btn_out(this);" onmousedown="tool_btn_down(this);" onmouseup="tool_btn_up(this);" name='tb_JUSTIFYCENTER' onclick="tool_btn_click(this);" alt="Выровнять по центру"><img
	 src=/toolbarimages/justifyright.gif width=21 height=20 border=0 class=toolbtn onmouseover="tool_btn_over(this);" onmouseout="tool_btn_out(this);" onmousedown="tool_btn_down(this);" onmouseup="tool_btn_up(this);" name='tb_JUSTIFYRIGHT' onclick="tool_btn_click(this);" alt="Выровнять по правому краю"><img src=/format.gif border=0 width=10><img
	 src=/toolbarimages/orderlist.gif width=21 height=20 border=0 class=toolbtn onmouseover="tool_btn_over(this);" onmouseout="tool_btn_out(this);" onmousedown="tool_btn_down(this);" onmouseup="tool_btn_up(this);" name='tb_ORDERLIST' onclick="tool_btn_click(this);" alt="Нумерованый список"><img
	 src=/toolbarimages/unorderlist.gif width=21 height=20 border=0 class=toolbtn onmouseover="tool_btn_over(this);" onmouseout="tool_btn_out(this);" onmousedown="tool_btn_down(this);" onmouseup="tool_btn_up(this);" name='tb_UNORDERLIST' onclick="tool_btn_click(this);" alt="Список"><img
	 src=/toolbarimages/outdent.gif width=21 height=20 border=0 class=toolbtn onmouseover="tool_btn_over(this);" onmouseout="tool_btn_out(this);" onmousedown="tool_btn_down(this);" onmouseup="tool_btn_up(this);" name='tb_OUTDENT' onclick="tool_btn_click(this);" alt="Уменьшить отступ"><img
	 src=/toolbarimages/indent.gif width=21 height=20 border=0 class=toolbtn onmouseover="tool_btn_over(this);" onmouseout="tool_btn_out(this);" onmousedown="tool_btn_down(this);" onmouseup="tool_btn_up(this);" name='tb_INDENT' onclick="tool_btn_click(this);" alt="Увеличить отступ"><img src=/format.gif border=0 width=10><img
	 src=/toolbarimages/html.gif width=21 height=20 border=0 class=toolbtn onmouseover="tool_btn_over(this);" onmouseout="tool_btn_out(this);" onmousedown="tool_btn_down(this);" onmouseup="tool_btn_up(this);" name='tb_HTML' onclick="editinhtml()" alt="В виде html"></td>

	</tr>
	<tr><td class=frm_data2 valign=middle width=250 align=right><div id=div_classeslist></div></td>
	<td class=frm_data2 width=200><select name=edit_change_element_class><option value="">--нет задано--</select></td>
	<td class=frm_data2 valign=middle width=120 align=right>Преобразовать в:</td>
	<td class=frm_data2><select name=edit_change_element_tag><option value="">--нет задано--</select></td>
	</tr>

	<tr><td class=frm_data2 valign=middle width=250 align=right>Групповое форматирование для:</td>
	<td class=frm_data2 width=200><select name=edit_group_formating_tag><option value="">--нет задано--</select></td>
	<td class=frm_data2 valign=middle width=120 align=right>стиль:</td>
	<td class=frm_data2><select name=edit_group_formating_class><option value="">--нет задано--</select></td>
	</tr>

	<tr><td class=frm_data1 valign=middle colspan=4><div id=div_edit_tables></div></td></tr>
	</table></td></tr></table>
</td></tr>
<tr><td height=100% valign=top bgcolor=#BBBBBB>
<?php echo (!$canuseeditor)?"<p class=main style=\"color:#AA0000; font-weight:bold;\">Извините, но для редактирования текстов с расширенным форматированием необходимо использовать обозреваиель Microsoft&reg; Internet Explorer (версия 5.0 или выше). С помощью текущего обозревателя редактирование текстов срасширенным форматированием невозможно!</p>":''?>
<object ID="EditHTML" classid="CLSID:2D360201-FFF5-11d1-8D03-00A0C959BC0A" width=100% height=100%><param name=ShowBorders value=True><param name=SourceCodePreservation value=False></object>

<script LANGUAGE="javascript" FOR="EditHTML" EVENT="ShowContextMenu">
return edit_on_content_menu_try()
</script>

<script LANGUAGE="javascript" FOR="EditHTML" EVENT="ContextMenuAction(itemIndex)">
return edit_on_content_menu_action(itemIndex)
</script>

</td></tr>
<form name=savehtml method=post action=post.php enctype="multipart/form-data">
<input type=hidden name=textID value="<?php echo $theTextID?>">
<tr><td><div id=fileslist></div>
</td></tr>
<input type=hidden name=htmlsrc value="">
</form>
<form name=edithtml method=post action=post-html.php><input type=hidden name=textID value="<?php echo $theTextID?>"></form>
</table></td></tr></table>
</html>
