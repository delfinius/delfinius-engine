<div style="position:absolute;top:0;left:74%;width:25%;border:solid 1px black;background-color:white;"><ul>
<?php
	$deepwas=1;
	$Menus=$SiteTree->GetNodeTreeOpenedToNode($node,"visible=1 and ableinmenu=1",true);
	foreach($Menus as $aKey => $aMenu){
		$step=($aMenu['dbs_deep']>$deepwas)?1:-1;
		for(;$aMenu['dbs_deep']!=$deepwas;$deepwas+=$step)
			if($step>0){
				echo "<ul>";
			}else{
				echo "</ul>";
			}
		echo "<li><a " . $modsArray[$aMenu['type']]->MakeSelfHrefParams($aMenu[$SiteTree->cKeyField]) . ">" . CutQuots($aMenu[$SiteTree->cNameField]) . "</a>";
	};
?>
</ul></div>