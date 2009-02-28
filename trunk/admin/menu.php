<script>
function gotonode(theNode){
	document.forms['node'].node.value=theNode;
	document.forms['node'].submit();
};
function module_params(theModule){
	document.forms['params_for_module'].module.value=theModule;
	document.forms['params_for_module'].submit();
};

function module_params_sa(theModule){
	document.forms['params_for_module_sa'].module.value=theModule;
	document.forms['params_for_module_sa'].submit();
};
</script>
<?php echo drwTableBegin(180,0)?>
<form method=post action="moderators.php" name=moderators><input type=hidden name=temp value="temp"></form>
<form method=post action="params.php" name=params><input type=hidden name=temp value="temp"></form>
<form method=post action="logout.php" name=logout><input type=hidden name=temp value="temp"></form>
<form method=get action="node.php" name=node><input type=hidden name=node value="0"></form>
<form method=get action="config-module.php" name=params_for_module><input type=hidden name=module value=""></form>
<form method=get action="config-module-sa.php" name=params_for_module_sa><input type=hidden name=module value=""></form>
<form method=post action="password.php" name=password><input type=hidden name=temp value="temp"></form>


<tr><td class=colheader align=center>меню</td></tr>
<?php
	if(HaveAccess('root')) echo '<tr><td class=frm_data2 align=center><a href="javascript:document.forms[\'moderators\'].submit()">модераторы</a></td></tr>';
?>
<tr><td class=frm_data2 align=center><a href="index.php#help">помощь</a></td></tr>
<tr><td class=frm_data2 align=center><strong>дерево сайта:</strong></td></tr>
<tr><td class=frm_data2 align=center>
<iframe width=178 scrolling=yes style="border:solid 1px black;" height=300 border=0 frameborder=0 src="tree-frame.php?node=<?php echo $TheCurrentNode?>"></iframe>
</td></tr>
<?php
	if(HaveAccess('params')){
		echo '<tr><td class=frm_data2 align=center><strong>параметры:</strong></td></tr>';
		echo '<tr><td class=frm_data2 align=center><a href="javascript:module_params(\'core\')">общие параметры сайта</a></td></tr>';
		foreach($SAmodsArray as $aKey => $aMod)
			echo "<tr><td class=frm_data2 align=center><a href=\"javascript:module_params_sa('" . $aMod->name . "')\">модуль \"" . CutQuots($aMod->realname) . "\"</a></td></tr>";
		foreach($modsArray as $aKey => $aMod)
			echo "<tr><td class=frm_data2 align=center><a href=\"javascript:module_params('" . $aMod->name . "')\">модуль \"" . CutQuots($aMod->realname) . "\"</a></td></tr>";
	}
?>
<tr><td class=frm_data2 align=center><a href="javascript:document.forms['password'].submit()">личные данные</a></td></tr>
<tr><td class=frm_data2 align=center><a href="javascript:document.forms['logout'].submit()">выход</a></td></tr>

</tr>
<?php echo drwTableEnd()?>