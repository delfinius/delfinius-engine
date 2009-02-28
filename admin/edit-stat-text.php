<?php
    include ("common-admin.php");
    $TheTextName=$HTTP_POST_VARS['textname'];
    if(($TheTextName=='')){
	header("Location : index.php");
	exit();
    };
    $sql="select textname, textid from stattexts where textname='$TheTextName'";
    if($db->sql_query($sql)){
	if($db->sql_numrows()>0){
	    $TheTextID=$db->sql_fetchfield('textid');
	}else{
	    $TheTextID=text_create_new();
	    $sql="insert into stattexts (textname,textid) values ('$TheTextName',$TheTextID)";
	    $db->sql_query($sql);
	};
    }else{
	$sqlerror=$db->sql_error();
	die ($sqlerror['message']);
    };
    include ("top.php");
?>

<script>
function stattexteditor_edit(TheTextName){
	document.forms['stattexts'].textname.value=TheTextName;
	document.forms['stattexts'].submit();
};
</script>
<form method=post action=post.php enctype="multipart/form-data" name=edit_go_form target=editor_frame><input type=hidden name=textID value=<?php echo $TheTextID?>></form>
<?php echo drwTableBegin('100%',0)?>
<tr><td class=header align=center>Редактирование статических текстов на сайте</td></tr>
<tr><td class=data1 align=left><p class=main>Статические тексты - тексты представляющие собой составную часть разделов определённых программистом. Используются для заполнения текстовых вставок в результатах работы серверных сценариев. Для редактирования того или иного щёлкните на его название ниже.</p></td></tr>
<tr><td class=border align=center>
	<table border=0 cellpadding=3 width=100% cellspacing=1>
	<tr>
<?php
	$counter=0;
	foreach($TextIDs as $aKey => $aName){
		if($counter%3==0) echo '</tr><tr>';
		if($aKey==$TheTextName){
			echo "<td align=center class=frm_data2 style=\"font-weight:bold;\">" . $TextIDs[$aKey] . "</td>";
		}else{
			echo "<td align=center class=frm_data1 style=\"font-weight:bold;\"><a href=\"javascript:stattexteditor_edit('$aKey')\">" . $TextIDs[$aKey] . "</a></td>";
		};
		$counter++;
	};
?>
	</tr>
	</table>
</td></tr>
<tr><td class=data1 align=center height=100% ><iframe name="editor_frame" border=0 width=100% height=400 marginwidth=0 marginheight=0 scrolling=yes frameborder=0></iframe></td></tr>
<?php echo drwTableEnd()?>
<script>
	document.forms['edit_go_form'].submit();
</script>
<?php include ("bottom.php")?>
