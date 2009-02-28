<table width=175 border=0 cellpadding=0 cellspacing=0>
<?php
	$Menus=$SiteTree->GetNodeTreeOpenedToNode($node,"visible=1 and ableinmenu=1",true);
	$wassub=false;
	$menuicon=($symbolfn=="10.jpg")?"bmenu-1-1.gif":"menu-1-1.gif";
	foreach($Menus as $aKey => $aMenu){
		if($aMenu["dbs_deep"]==1){
			if($wassub){
				echo "<tr><td colspan=2 height=4><img src=simages/menudev-1.gif border=0 width=175 height=4></td></tr>";
				$wassub=false;
			}
			if(!$aMenu["dbs_opened"]){
				echo "<tr><td height=5><img src=format.gif border=0 width=1 height=5></td></tr>";
				echo "<tr><td width=17 height=41><img src=simages/$menuicon width=17 height=41 border=0></td>";
				echo "<td background=simages/menubg-1-1.gif width=158><div style=\"margin-left:10px;\"><a class=menu1 " . $modsArray[$aMenu['type']]->MakeSelfHrefParams($aMenu[$SiteTree->cKeyField]) . ">" . CutQuots($aMenu[$SiteTree->cNameField]) . "</a></div></td>";
				echo "</tr>";
			}else{
				echo "<tr><td height=5><img src=format.gif border=0 width=1 height=5></td></tr>";
				echo "<tr><td width=17 height=41><img src=simages/menu-1-2.gif width=17 height=41 border=0></td>";
				echo "<td background=simages/menubg-1-2.gif width=158><div style=\"margin-left:4px;\"><a class=menu2 " . $modsArray[$aMenu['type']]->MakeSelfHrefParams($aMenu[$SiteTree->cKeyField]) . ">" . CutQuots($aMenu[$SiteTree->cNameField]) . "</a></div></td>";
				echo "</tr>";
			};
		}else{
			$wassub=true;
			$deepwidth=(($aMenu["dbs_deep"]-1)*7);
			echo "<tr><td bgcolor=#ebeef0>&nbsp;</td><td bgcolor=#ebeef0>";
			echo "<table border=0 cellpadding=0 cellspacing=0><tr><td valign=top><img src=format.gif border=0 height=1 width=$deepwidth><img src=simages/menu-2-1.gif width=6 height=11 border=0></td>";
			echo "<td valign=top><a class=menu3 " . $modsArray[$aMenu['type']]->MakeSelfHrefParams($aMenu[$SiteTree->cKeyField]) . ">";
			echo CutQuots($aMenu[$SiteTree->cNameField]) . "</a></td></tr></table></td></tr>";
			echo "<tr><td height=3 bgcolor=#ebeef0><img src=format.gif border=0 width=1 height=3></td></tr>";
		};
	};
	if($wassub){
		echo "<tr><td colspan=2 height=4><img src=simages/menudev-1.gif border=0 width=175 height=4></td></tr>";
	}
	echo "<tr><td colspan=2 height=1><img src=format.gif border=0 width=175 height=1></td></tr>";
?>
<tr><td colspan=2 align=center><img src=simages/statlink.gif border=0 width=176 height=97><br><a class=menu3 href="https://u.billing.epn.ru/" target=_blank>проверить статистику</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<br><br><br>
<a href="http://extrim.it" target=_blank><img src="/simages/extrim.gif" width=142 height=48 border=0></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
</td></tr>
</table>