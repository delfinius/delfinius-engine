<?php
    include ("common-admin.php");
    $CurNode=$HTTP_GET_VARS['node'];
    $CurNode=($CurNode>=0)?$CurNode:-1;
?>
<html>
<head>
<title>дерево сайта</title>
<style>
body	{margin-top:0px;margin-left:0px;margin-bottom:0px;margin-right:0px; background-color:#FFFFFF; font-family: Tahoma; color:#000000; font-size:12px;
	scrollbar-face-color:#FFFFFF; scrollbar-shadow-color: #808080;
	scrollbar-highlight-color: #AAAAAA; scrollbar-3dlight-color:#AAAAAA; scrollbar-darkshadow-color:#808080;
	scrollbar-track-color: #EEEEEE; scrollbar-arrow-color: #EEEEEE}


a {  color: green; text-decoration: underline; }
a:visited {   text-decoration: underline; }
a:active {  text-decoration: underline;}
a:link {  text-decoration: underline; }
a:hover {  text-decoration: none;}
td	{font-size:11px;background-color:#EEEEEE;font-weight:bold}
td.spacer	{font-size:2px;background-color:#FFFFFF;}
</style>
</head>
<script>
function goto(theNode){
	self.parent.gotonode(theNode);
};
</script>
<body marginwidth=0 marginheight=0>
<table width=100% border=0 cellpadding=0 cellspacing=0>
<a href="javascript:gotonode(0)" title="корневой раздел">
<?php
	$aStyle=($CurNode==0)?' style="color:red;"':'';
	echo '<tr><td nowrap><a name="root"></a><img src=../format.gif border=0 height=1 width=1><a href="javascript:goto(0)"' . $aStyle . '>корень дерева</a></td></tr>';
	echo '<tr><td class=spacer>&nbsp;</td></tr>';
	$theTree=$SiteTree->GetTree(0);
	foreach($theTree as $aKey => $aRec){
		$Deep=$aRec['dbs_deep'];
		$aStyle=($aRec[$SiteTree->cKeyField]==$CurNode)?' style="color:red;"':'';
		$tdSAtyle=($aRec['visible']!=1)?' style="background-color:#333333;color:white;"':'';
		echo "<tr><td nowrap $tdSAtyle><a name=\"" . $aRec[$SiteTree->cKeyField] ."\"></a><img src=/format.gif border=0 height=1 width=" . ($Deep*10) . "><a href=\"javascript:goto('" . $aRec[$SiteTree->cKeyField] . "')\" $aStyle title=\"" . CutQuots($aRec[$SiteTree->cNameField]) . "\">" . CutQuots($aRec[$SiteTree->cNameField]) . "</a> [" . $aRec[$SiteTree->cSortField] . "]</td></tr>";
		echo '<tr><td class=spacer>&nbsp;</td></tr>';
	};
?>
</table>
</body>
</html>
