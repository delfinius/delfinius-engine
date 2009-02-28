<?php
class clsGalleryModule extends clsModule{
	function clsGalleryModule($modName,$modDName,$dbconnector){
		global $SiteMainURL, $sipath;
		parent::clsModule($modName,$modDName,$dbconnector);
		$this->version="1.0.1";
		$this->helpstring="<p>Модуль реализует галерею картинок с preview-изображениями.</p>";
		$this->SearchAble=false;

		$this->prms["PageSize"]=new ConfigParam("PageSize");
		$this->prms["PageSize"]->Description="Количество отображаемых составляющих \"item\" на одной странице.";
		$this->prms["PageSize"]->DataType="int";
		$this->prms["PageSize"]->Value=20;
		$this->prms["PageSize"]->Protected=false;

		$this->prms["AdminPageSize"]=new ConfigParam("AdminPageSize");
		$this->prms["AdminPageSize"]->Description="Количество отображаемых записей на странице модератора.";
		$this->prms["AdminPageSize"]->DataType="int";
		$this->prms["AdminPageSize"]->Value=50;
		$this->prms["AdminPageSize"]->Protected=true;

		$this->prms["MaxWidth"]=new ConfigParam("MaxWidth");
		$this->prms["MaxWidth"]->Description="Максимальная ширина картинки. В случае если ширина загружаемого изображения превышает допустимую - изображение будет уменьшено до необходимого.";
		$this->prms["MaxWidth"]->DataType="int";
		$this->prms["MaxWidth"]->Value=500;
		$this->prms["MaxWidth"]->Protected=false;

		$this->prms["MaxHeight"]=new ConfigParam("MaxHeight");
		$this->prms["MaxHeight"]->Description="Максимальная высота картинки. В случае если ширина загружаемого изображения превышает допустимую - изображение будет уменьшено до необходимого.";
		$this->prms["MaxHeight"]->DataType="int";
		$this->prms["MaxHeight"]->Value=600;
		$this->prms["MaxHeight"]->Protected=false;

		$this->prms["PreviewSize"]=new ConfigParam("PreviewSize");
		$this->prms["PreviewSize"]->Description="Размер маленькой картинки (превью).";
		$this->prms["PreviewSize"]->DataType="int";
		$this->prms["PreviewSize"]->Value=150;
		$this->prms["PreviewSize"]->Protected=false;

		$this->prms["PreviewResizeBy"]=new ConfigParam("PreviewResizeBy");
		$this->prms["PreviewResizeBy"]->Description="Выравнивание размеров previw-изображений по высоте (в противном случае выравнивание - по ширине). Размер для выравнивания задаётся предыдущим параметром.";
		$this->prms["PreviewResizeBy"]->DataType="bool";
		$this->prms["PreviewResizeBy"]->Value=false;
		$this->prms["PreviewResizeBy"]->Protected=false;

		$this->prms["PageTemplate"]=new ConfigParam("PageTemplate");
		$this->prms["PageTemplate"]->Description="Шаблон отображения страницы. Допускаемые для замены значения: itemslist, pager, nextpage, prevpage, totalpages, currentpage";
		$this->prms["PageTemplate"]->DataType="memo";
		$this->prms["PageTemplate"]->Value="<table border=0 cellpadding=2 cellspacing=3 width=100%>--itemslist--<tr><td width=33% align=right>--prevpage--</td><td width=34% align=center>страница № --currentpage-- (всего страниц: --totalpages--)</td><td width=33% align=left>--nextpage--</td></tr><tr><td align=center colspan=3>--pager--</td></table>";
		$this->prms["PageTemplate"]->Protected=false;

		$this->prms["DetailTemplate"]=new ConfigParam("DetailTemplate");
		$this->prms["DetailTemplate"]->Description="Шаблон отображения страницы, содержащей подробную информацию об изображении. Допускаемые для замены значения: все которые есть в PageTemplate , а так же pubdate, name, image, description, stripdescription, backhref";
		$this->prms["DetailTemplate"]->DataType="memo";
		$this->prms["DetailTemplate"]->Value="<table border=0 cellpadding=2 cellspacing=3 width=100%><tr><td colspan=3 align=center>--image--<hr width=100% size=1 color=black></td></tr>--itemslist--<tr><td width=33% align=right>--prevpage--</td><td width=34% align=center>страница № --currentpage-- (всего страниц: --totalpages--)</td><td width=33% align=left>--nextpage--</td></tr></table>";
		$this->prms["DetailTemplate"]->Protected=false;


    		$this->prms["plTemplate"]=new ConfigParam("plTemplate");
		$this->prms["plTemplate"]->Description="Шаблон отображения списка страниц. Заменяет pager в шаблоне PageTemplate. Допускаемое для замены значение: pageslist";
		$this->prms["plTemplate"]->DataType="memo";
		$this->prms["plTemplate"]->Value="страница:&nbsp;--pageslist--";
		$this->prms["plTemplate"]->Protected=false;

    		$this->prms["plNext"]=new ConfigParam("plNext");
		$this->prms["plNext"]->Description="Шаблон отображения перехода к следующей странице. Заменяет nextpage в шаблоне PageTemplate. Допускаемое для замены значение: href, number";
		$this->prms["plNext"]->DataType="char";
		$this->prms["plNext"]->Value="<a href=\"--href--\">&gt;&gt; (--number--)</a>";
		$this->prms["plNext"]->Protected=false;

    		$this->prms["plPrev"]=new ConfigParam("plPrev");
		$this->prms["plPrev"]->Description="Шаблон отображения перехода к предыдущей странице. Заменяет prevpage в шаблоне PageTemplate. Допускаемое для замены значение: href, number";
		$this->prms["plPrev"]->DataType="char";
		$this->prms["plPrev"]->Value="<a href=\"--href--\">&lt;&lt; (--number--)</a>";
		$this->prms["plPrev"]->Protected=false;
	
		$this->prms["ItemTemplate"]=new ConfigParam("ItemTemplate");
		$this->prms["ItemTemplate"]->Description="Шаблон отображения одного набора элементов галереи внутри шаблона PageTemplate. Допускаемые для замены значения: image, name, stripdescription, description, mininame, filename, href_detail, href_wnd, pubdate." .
				" После имени каждого заменяемого поля должна стоять цифра обозначающая его порядковый номер в наборе, который будет выводиться.";
		$this->prms["ItemTemplate"]->DataType="memo";
		$this->prms["ItemTemplate"]->Value="<tr><td align=center><a href=\"--href_detail1--\">--image1--</a></td><td align=center><a href=\"--href_detail2--\">--image2--</a></td><td align=center><a href=\"--href_detail3--\">--image3--</a></td></tr>";
		$this->prms["ItemTemplate"]->Protected=false;

		$this->prms["ItemElements"]=new ConfigParam("ItemElements");
		$this->prms["ItemElements"]->Description="Количество элементов галереи в одном ItemTemplate.";
		$this->prms["ItemElements"]->DataType="int";
		$this->prms["ItemElements"]->Value=3;
		$this->prms["ItemElements"]->Protected=false;

		$this->prms["ItemsDevider"]=new ConfigParam("ItemsDevider");
		$this->prms["ItemsDevider"]->Description="html-код разделяющий отдельные записи на странице";
		$this->prms["ItemsDevider"]->DataType="char";
		$this->prms["ItemsDevider"]->Value="";
		$this->prms["ItemsDevider"]->Protected=false;

		$this->prms["plInactiveTemplate"]=new ConfigParam("plInactiveTemplate");
		$this->prms["plInactiveTemplate"]->Description="Шаблон ссылки на неактивную страницу списка. Допускаемые для замены значения: link, pagenum";
		$this->prms["plInactiveTemplate"]->DataType="char";
		$this->prms["plInactiveTemplate"]->Value="<a href=\"--link--\">--pagenum--</a>";
		$this->prms["plInactiveTemplate"]->Protected=false;

		$this->prms["plActiveTemplate"]=new ConfigParam("plActiveTemplate");
		$this->prms["plActiveTemplate"]->Description="Шаблон ссылки на текущую страницу списка. Допускаемые для замены значения: link, pagenum";
		$this->prms["plActiveTemplate"]->DataType="char";
		$this->prms["plActiveTemplate"]->Value="<a href=\"--link--\" style=\"color:red;\">--pagenum--</a>";
		$this->prms["plActiveTemplate"]->Protected=false;

		$this->prms["plDevider"]=new ConfigParam("plDevider");
		$this->prms["plDevider"]->Description="Символы-разделители ссылок на страницы";
		$this->prms["plDevider"]->DataType="char";
		$this->prms["plDevider"]->Value="&nbsp;|&nbsp;";
		$this->prms["plDevider"]->Protected=false;

		$this->prms["DateFormat"]=new ConfigParam("DateFormat");
		$this->prms["DateFormat"]->Description="Формат вывода дат. (http://www.php.net/manual/en/function.date.php)";
		$this->prms["DateFormat"]->DataType="char";
		$this->prms["DateFormat"]->Value="d.m.Y";
		$this->prms["DateFormat"]->Protected=false;

		$this->itemsTable="mod_" . $modName . "_items";
		$this->ListingSize=0;
		$this->PagesCount=0;
		$this->imagesPath=$sipath;
	}

	function ClientScript($theNode, $theFormPrefix, $thePage=1){
		$LocalthePage=$thePage;
	  	$retVal='';
		$ModifedFormPrefix=substr($theFormPrefix,0,strlen($theFormPrefix)-1);
		$ModifedFormPrefix.=" name=\"mod_gallery_action_form\">";
		$retVal.=$ModifedFormPrefix . "<input type=hidden name=mod_action value=\"\"><input type=hidden name=id value=0><input type=hidden name=page value=$LocalthePage><input type=hidden name=param1 value=\"\"></form>";
		$retVal.="<script>";
		$retVal.="function mod_gallery_action(theAction,thePage,theParam1){";
		$retVal.="document.forms['mod_gallery_action_form'].mod_action.value=theAction;\n";
		$retVal.="if(thePage)document.forms['mod_gallery_action_form'].page.value=thePage;";
		$retVal.="if(theParam1)document.forms['mod_gallery_action_form'].param1.value=theParam1;";
		$retVal.="document.forms['mod_gallery_action_form'].submit();\n";
		$retVal.="};";
		$retVal.="function mod_gallery_edittext(theTextID){" .
			"	self.showModalDialog(\"post-dialog.php?text=\"+theTextID,\"\",\"center:yes;edge:rized;resizable:no;scroll:no;help:no;status:no;unadorned:yes;dialogWidth:720px;dialogHeight:600px\");\n" .
			"}";
		$retVal.="</script>";
		return $retVal;
	}

	function MakeAdminOuput($theNode, $theFormPrefix, $theSessionSettings){
		global $HTTP_POST_VARS, $HTTP_POST_FILES, $doc_root;
		$retVal='';
		$mod_action=$HTTP_POST_VARS["mod_action"];
		$PageNum=$HTTP_POST_VARS['page'];
		$PageNum=($PageNum>0)?$PageNum:1;
		$retVal.="<table border=0 cellpadding=0 cellspacing=0>" . $this->ClientScript($theNode, $theFormPrefix, $PageNum) . "</table>";
		if($mod_action=="edit"){
			$id=$HTTP_POST_VARS["param1"];
			$retVal.=$this->MakeAdminEditorScreen($theNode,$theFormPrefix,$id,$PageNum);
		};
		
		if($mod_action=="append"){
			$descr=text_create_new();
			$sql="insert into `" . $this->itemsTable . "` (`node`, `name`, `description`, `mininame`, `filename`, `miniwidth`, `miniheight`, `width`, `height`, `pubdate`, `visible`, `deleted`) values ($theNode,'',$descr,'','',0,0,0,0," . time() . ", 0,0)";
			if(!$this->dbc->sql_query($sql)){
				$sqlerror=$this->dbc->sql_error();
				die($sqlerror["message"]);
			};
			$id=$this->dbc->sql_nextid();
			$retVal.=$this->MakeAdminEditorScreen($theNode,$theFormPrefix,$id,$PageNum);
		};

		if($mod_action=="delete"){
			$sql="update `" . $this->itemsTable . "` set `deleted`=1 where `id`=" . $HTTP_POST_VARS["param1"];
			if(!$this->dbc->sql_query($sql)){
				$sqlerror=$this->dbc->sql_error();
				die($sqlerror["message"]);
			};
		};

		if($mod_action=="update"){
			$itemID=$HTTP_POST_VARS["id"];
			$pubdate=PostToDate("pubdate");
			$mininame=$HTTP_POST_VARS["oldmininame"];
			$miniwidth=$HTTP_POST_VARS["oldminiwidth"];
			$miniheight=$HTTP_POST_VARS["oldminiheight"];
			$filename=$HTTP_POST_VARS["oldfilename"];
			$width=$HTTP_POST_VARS["oldwidth"];
			$height=$HTTP_POST_VARS["oldheight"];
			$name=$HTTP_POST_VARS["name"];
			$visible=($HTTP_POST_VARS["visible"]=="on")?1:0;
			if(($HTTP_POST_VARS["replaceimage"]=="on")&&isset($HTTP_POST_FILES["newimage"])){
				list($newwidth, $newheight, $newtype, $newattr)=getimagesize($HTTP_POST_FILES["newimage"]["tmp_name"]);
				$source=imagecreatefromjpeg($HTTP_POST_FILES["newimage"]["tmp_name"]);
				if($source){
					if($this->prms["PreviewResizeBy"]->Value){
						$tumbheight=$this->prms["PreviewSize"]->Value;
						$tumbwidth=($newwidth/$newheight)*$tumbheight;
					}else{
						$tumbwidth=$this->prms["PreviewSize"]->Value;
						$tumbheight=($newheight/$newwidth)*$tumbwidth;
					};
					$tumb=imagecreatetruecolor($tumbwidth, $tumbheight);
					imagecopyresampled($tumb, $source, 0, 0, 0, 0, $tumbwidth, $tumbheight, $newwidth, $newheight);
					$newmininame=$this->name . "_" . $theNode . "_" . $itemID . "mini.jpg";
					if(imagejpeg($tumb,$doc_root . $this->imagesPath . $newmininame)){
						$mininame=$newmininame;
						$miniwidth=$tumbwidth;
						$miniheight=$tumbheight;
					};
					imagedestroy($tumb);
					$scalefactor=$this->prms["MaxWidth"]->Value/$newwidth;
					$scalefactor2=$this->prms["MaxHeight"]->Value/$newheight;
					$scalefactor=($scalefactor2<$scalefactor)?$scalefactor2:$scalefactor;
					$scalefactor=($scalefactor>1)?1:$scalefactor;
					$rswidth=$newwidth*$scalefactor;
					$rsheight=$newheight*$scalefactor;
					$final=imagecreatetruecolor($rswidth, $rsheight);
					imagecopyresampled($final, $source, 0, 0, 0, 0, $rswidth, $rsheight, $newwidth, $newheight);
					$newfilename=$this->name . "_" . $theNode . "_" . $itemID . "orig.jpg";
					if(imagejpeg($final,$doc_root . $this->imagesPath . $newfilename)){
						$filename=$newfilename;
						$width=$rswidth;
						$height=$rsheight;
					};
					imagedestroy($final);
					imagedestroy($source);
				};
			};

			$sql="update `" . $this->itemsTable . "` set `name`='$name', `mininame`='$mininame', `filename`='$filename', `miniwidth`='$miniwidth', `miniheight`='$miniheight', `width`='$width', `height`='$height', `pubdate`=$pubdate, `visible`=$visible where `id`=$itemID";
			if(!$this->dbc->sql_query($sql)){
				$sqlerror=$this->dbc->sql_error();
				die($sqlerror["message"]);
			};
		};

		$retVal.=$this->MakeAdminItemsList($theNode,$theFormPrefix,$PageNum);
		return $retVal;
	}

    	function MakeAdminItemsList($theNode,$theFormPrefix,$thePage){
		$PageSize=$this->prms["AdminPageSize"]->Value;
		$PageNum=$thePage;
		$retVal=drwTableBegin('100%','') . "<tr><td class=colheader colspan=5>существующие изображения в галерее</td></tr>";
		$sql="select `id`, `node`, `name`, `description`, `mininame`, `filename`, `miniwidth`, `miniheight`, `width`, `height`, `pubdate`, `deleted`, `visible` from `" . $this->itemsTable . "` where `deleted`<>1 and `node`=$theNode order by `pubdate` desc";
		if(!$this->dbc->sql_query($sql)){
			$sqlerror=$this->dbc->sql_error();
			die($sqlerror["message"]);
		};
		$tdclass="data1";
		$counter=0;
		while($row=$this->dbc->sql_fetchrow()){
			$counter++;
			if(($counter>(($PageNum-1)*$PageSize))&&($counter<=(($PageNum)*$PageSize))){
				$wdate=date("d.m.Y H:i",$row["pubdate"]);
				$name=(strlen($row["name"])>0)?$row["name"]:'';
				$name=CutQuots($name);
				$miniimg=(strlen($row["mininame"])>0)?"<img src=\"" . $this->imagesPath . $row["mininame"] . "\" border=0 width=" . $row["miniwidth"] . " height=" . $row["miniheight"] . ">":"";
				$tdstyle=($row["visible"]==1)?"":" style=\"background-color:black;\"";
				$retVal.="<tr><td class=$tdclass align=center$tdstyle>$miniimg</td><td class=$tdclass align=center>[$wdate]</td>";
				$retVal.="<td class=$tdclass>$name</td>";
				$retVal.="<td class=$tdclass align=center><input type=button class=button value=\"редактировать\" onclick=\"mod_gallery_action('edit',$thePage," . $row['id'] . ")\"></td>";
				$retVal.="<td class=$tdclass align=center><input type=button class=button value=\"удалить\" onclick=\"mod_gallery_action('delete',$thePage," . $row['id'] . ")\"></td>";
				$retVal.="</tr>";
				$tdclass=($tdclass=="data1")?"data2":"data1";
			};
		};
		$retVal.="<tr><td class=colheader align=center colspan=5><input type=button class=button value=\"добавить новое изображение\" onclick=\"mod_gallery_action('append',$thePage,0)\"></td></tr>";
		$retVal.=drwTableEnd();
		$PagerStr='';
		$lastPage=($counter%$PageSize==0)?0:1;
		$lastPage+=($counter/$PageSize);
		if($lastPage>=2){
			$PagerStr.=drwTableBegin('100%','') . "<tr><td width=10% nowrap align=right class=colheader>страница:</td><td class=data1>";
			for($fcounter=1;$fcounter<=$lastPage;$fcounter++){
				if($fcounter==$PageNum){
					$PagerStr.="<a style=\"color:gray;\">$fcounter</a> ";
				}else{
					$PagerStr.="<a href=\"javascript:mod_gallery_action('',$fcounter,0)\">$fcounter</a> ";
				};
			};
			$PagerStr.="</td></tr>" . drwTableEnd();
		};
		$retVal.=$PagerStr;

		return $retVal;
	}  	

    	function MakeAdminEditorScreen($theNode,$theFormPrefix,$theID,$thePage){
		$retVal=drwTableBegin('100%','') . "<tr><td class=colheader colspan=2>редактирование данных об изображении в галерее</td></tr>";
		$sql="select `id`, `node`, `name`, `description`, `mininame`, `filename`, `miniwidth`, `miniheight`, `width`, `height`, `pubdate`, `deleted`, `visible` from `" . $this->itemsTable . "` where `deleted`<>1 and `node`=$theNode and `id`=" . $theID;
		if(!$this->dbc->sql_query($sql)){
			$sqlerror=$this->dbc->sql_error();
			die($sqlerror["message"]);
		};
		if($row=$this->dbc->sql_fetchrow()){
			$wdate=date("d.m.Y H:i",$row["pubdate"]);
			$name=CutQuots($row["name"]);
			$ModifedFormPrefix=substr($theFormPrefix,0,strlen($theFormPrefix)-1);
			$ModifedFormPrefix.=" enctype=\"multipart/form-data\">";
			$retVal.=$ModifedFormPrefix ."<input type=hidden name=mod_action value=update><input type=hidden name=id value=$theID>";
			$retVal.="<tr><td class=data2 align=right>дата публикации:</td><td class=data2 align=left>" . DatePicker("pubdate", $row["pubdate"]) . "</td></tr>";
			$retVal.="<tr><td class=data1 align=right>название:</td><td class=data1 align=left><input type=text class=text name=name size=40 value=\"$name\"></td></tr>";
			$retVal.="<tr><td class=data2 align=right>описание:</td><td class=data2 align=left><input type=button class=button value=\"редактировать текст описания\" onclick=\"mod_gallery_edittext(" . $row["description"] . ")\"></td></tr>";

			$miniimg=(strlen($row["mininame"])>0)?"<img src=\"" . $this->imagesPath . $row["mininame"] . "\" border=0 width=" . $row["miniwidth"] . " height=" . $row["miniheight"] . ">":"";

			$retVal.="<tr><td class=data1 align=right valign=top>существующее изображение:</td><td class=data1 align=left>$miniimg</td></tr>";
			$retVal.="<input type=hidden name=oldminiwidth value=" . $row["miniwidth"] . ">";
			$retVal.="<input type=hidden name=oldminiheight value=" . $row["miniheight"] . ">";
			$retVal.="<input type=hidden name=oldwidth value=" . $row["width"] . ">";
			$retVal.="<input type=hidden name=oldheight value=" . $row["height"] . ">";
			$retVal.="<input type=hidden name=oldfilename value=\"" . $row["filename"] . "\">";
			$retVal.="<input type=hidden name=oldmininame value=\"" . $row["mininame"] . "\">";

			$retVal.="<tr><td class=data2 valign=top align=right>заменить изображение:</td><td class=data2 align=left><input type=checkbox name=replaceimage><br><input type=file class=text size=30 name=newimage></td></tr>";

			$checked=($row["visible"]==1)?" checked":"";
			$retVal.="<tr><td class=data1 valign=top align=right>доступность на сайте:</td><td class=data2 align=left><input type=checkbox name=visible $checked></td></tr>";
			$retVal.="<tr><td class=data2 colspan=2 align=center><input type=submit class=button value=\"обновить\"></td></tr>";
			$retVal.="</form>";
		};
		$retVal.=drwTableEnd();
		return $retVal;
	}  	

	function MakeUserOuput($theNode, $theFormPrefix){
		global $HTTP_POST_VARS, $HTTP_GET_VARS, $client_ip;
		$showscript="<script>
			function gallery_showimage(afilename,awidth,aheight,aname){
				var bu=String(self.location);
				bu=bu.substring(0,bu.lastIndexOf('/'));
				var ttw='<html><head><title>'+aname+'</title><style>body {margin-left:0px;margin-top:0px;margin-right:0px;margin-bottom:0px;}</style></head>';
				ttw+='<body marginwidth=0 marginheight=0><img src=\"'+bu+'$this->imagesPath'+afilename+'\" width='+awidth+' height='+aheight+' border=0 alt=\"'+aname+'\"></body></html>';
				var previewwnd=self.open('','previewwnd','width='+awidth+',height='+aheight);
				previewwnd.document.open('text/html');
				previewwnd.document.write(ttw);
				previewwnd.document.close();
				previewwnd.document.title=aname;
				return;
			};
		</script>";
		$retArray=array();
		$retVal="";
		$PageNum=$HTTP_GET_VARS['page'];
		$PageNum=($PageNum>0)?$PageNum:1;
		$itemslist=$this->MakeUserListing($theNode, $theFormPrefix, $PageNum);
		$pageslist=$this->MakePageChanger($theNode, $theFormPrefix, $PageNum);
		if($PageNum>1){
			$prevpage=$this->prms["plPrev"]->Value;
			$prevpage=str_replace("--number--",($PageNum-1),$prevpage);
			$prevpage=str_replace("--href--",$theFormPrefix . "&page=" . ($PageNum-1),$prevpage);
		}else{
			$prevpage="";
		};
		if($PageNum<$this->PagesCount){
			$nextpage=$this->prms["plNext"]->Value;
			$nextpage=str_replace("--number--",($PageNum+1),$nextpage);
			$nextpage=str_replace("--href--",$theFormPrefix . "&page=" . ($PageNum+1),$nextpage);
		}else{
			$nextpage="";
		};
		if($HTTP_GET_VARS["detail"]>0){
			$sql="select `t1`.`id`, `t1`.`node`, `t1`.`name`, `t1`.`description`, `t1`.`mininame`, `t1`.`filename`, `t1`.`miniwidth`, `t1`.`miniheight`, `t1`.`width`, `t1`.`height`, `t1`.`pubdate`, `texts`.`text` as `desctext` from `" . $this->itemsTable . "` as `t1` inner join `texts` on `texts`.`id`=`t1`.`description` where `t1`.`deleted`<>1 and `t1`.`node`=$theNode and `t1`.`visible`=1 and `t1`.`id`='" . $HTTP_GET_VARS["detail"] . "'";
			$this->dbc->sql_query($sql);
			if($row=$this->dbc->sql_fetchrow()){
				$name=CutQuots($row["name"]);
				$image=(strlen($row["filename"])>0)?"<img src=\"" . $this->imagesPath . $row["filename"] . "\" border=0 width=" . $row["width"] . " height=" . $row["height"] . " alt=\"$name\">":"";
				$description=$row["desctext"];
				$stripdescription=striphtml($description);
				$retVal=$this->prms["DetailTemplate"]->Value;
				$retVal=str_replace("--name--",$name,$retVal);
				$retVal=str_replace("--image--",$image,$retVal);
				$retVal=str_replace("--description--",$description,$retVal);
				$retVal=str_replace("--stripdescription--",$stripdescription,$retVal);
				$retVal=str_replace("--backhref--",$theFormPrefix . "&page=" . $PageNum,$retVal);
				$retVal=str_replace("--pubdate--",date($this->prms["DateFormat"]->Value,$row["pubdate"]),$retVal);
			}else{
				$retVal=$this->prms["PageTemplate"]->Value;
			};
		}else{
			$retVal=$this->prms["PageTemplate"]->Value;
		};
		$retVal=str_replace("--itemslist--",$itemslist,$retVal);
		$retVal=str_replace("--pager--",$pageslist,$retVal);
		$retVal=str_replace("--totalpages--",($this->PagesCount==0)?1:$this->PagesCount,$retVal);
		$retVal=str_replace("--currentpage--",$PageNum,$retVal);
		$retVal=str_replace("--nextpage--",$nextpage,$retVal);
		$retVal=str_replace("--prevpage--",$prevpage,$retVal);

		$retVal=$showscript . $retVal;
		$retArray[0]=$retVal;
		return $retArray;
	}	

	function MakeUserListing($theNode, $theFormPrefix, $thePage){
		$retVal='';
		$itemsize=$this->prms["ItemElements"]->Value;
		$PageSize=$itemsize*$this->prms["PageSize"]->Value;
		$ItemTemplate=$this->prms["ItemTemplate"]->Value;
		$Devider=$this->prms["ItemsDevider"]->Value;
		$PageNum=$thePage;

		$sql="select `t1`.`id`, `t1`.`node`, `t1`.`name`, `t1`.`description`, `t1`.`mininame`, `t1`.`filename`, `t1`.`miniwidth`, `t1`.`miniheight`, `t1`.`width`, `t1`.`height`, `t1`.`pubdate`, `texts`.`text` as `desctext` from `" . $this->itemsTable . "` as `t1` inner join `texts` on `texts`.`id`=`t1`.`description` where `t1`.`deleted`<>1 and `t1`.`node`=$theNode and `t1`.`visible`=1 order by `t1`.`pubdate` desc";
		if(!$this->dbc->sql_query($sql)){
			$sqlerror=$this->dbc->sql_error();
			die($sqlerror['message']);
		};
		$counter=0;
		$initemcounter=0;
		$totalitems=0;
		while($row=$this->dbc->sql_fetchrow()){
			$counter++;
			$InPage=(($counter>(($PageNum-1)*$PageSize))&&($counter<=(($PageNum)*$PageSize)));
			if($InPage){
				if($initemcounter==0)$OneItem=$ItemTemplate;
				$initemcounter++;
				$name=CutQuots($row["name"]);
				$miniimg=(strlen($row["mininame"])>0)?"<img src=\"" . $this->imagesPath . $row["mininame"] . "\" border=0 width=" . $row["miniwidth"] . " height=" . $row["miniheight"] . " alt=\"$name\">":"";
				$href_wnd="javascript:gallery_showimage('" . $row["filename"] . "'," . $row["width"] . "," . $row["height"] . ",'" . CutQuots($row["name"]) . "')";
				$href_detail="$theFormPrefix&page=$thePage&detail=".$row["id"];
				$description=$row["desctext"];
				$stripdescription=striphtml($description);
				$OneItem=str_replace("--pubdate" . $initemcounter . "--",date($this->prms["DateFormat"]->Value,$row["pubdate"]),$OneItem);
				$OneItem=str_replace("--name" . $initemcounter . "--",$name,$OneItem);
				$OneItem=str_replace("--description" . $initemcounter . "--",$description,$OneItem);
				$OneItem=str_replace("--stripdescription" . $initemcounter . "--",$stripdescription,$OneItem);
				$OneItem=str_replace("--href_wnd" . $initemcounter . "--",$href_wnd,$OneItem);
				$OneItem=str_replace("--href_detail" . $initemcounter . "--",$href_detail,$OneItem);
				$OneItem=str_replace("--image" . $initemcounter . "--",$miniimg,$OneItem);
				if($initemcounter==3){
					$initemcounter=0;
					$retVal.=$OneItem . $Devider;
				};
			};
		};
		if($initemcounter>0){
			while($initemcounter<=3){
				$initemcounter++;
				$OneItem=str_replace("--pubdate" . $initemcounter . "--","",$OneItem);
				$OneItem=str_replace("--name" . $initemcounter . "--","",$OneItem);
				$OneItem=str_replace("--description" . $initemcounter . "--","",$OneItem);
				$OneItem=str_replace("--stripdescription" . $initemcounter . "--","",$OneItem);
				$OneItem=str_replace("--href_wnd" . $initemcounter . "--","",$OneItem);
				$OneItem=str_replace("--href_detail" . $initemcounter . "--","",$OneItem);
				$OneItem=str_replace("--image" . $initemcounter . "--","",$OneItem);
			};
			$retVal.=$OneItem . $Devider;
		};
		if(strlen($retVal)>strlen($Devider))$retVal=substr($retVal,0,(strlen($retVal)-strlen($Devider)));
		$this->ListingSize=ceil($counter/$itemsize);
//		echo $this->ListingSize;
		return $retVal;
	}  	

	function MakePageChanger($theNode, $theFormPrefix, $thePage){
		$retVal='';
		$counter=$this->ListingSize;
		$PageSize=$this->prms["PageSize"]->Value;
		$PageNum=$thePage;
		$lastPage=($counter%$PageSize==0)?0:1;
		$PageHref='';
		$PageLink='';
		$Devider=$this->prms["plDevider"]->Value;
		$lastPage+=($counter/$PageSize);
		if($lastPage<2)return "";
		for($fcounter=1;$fcounter<=$lastPage;$fcounter++){
			$PageHref=$theFormPrefix . '&page=' . $fcounter;
			$PageLink=($fcounter==$PageNum)?$this->prms["plActiveTemplate"]->Value:$this->prms["plInactiveTemplate"]->Value;
			$PageLink=str_replace("--link--",$PageHref,$PageLink);
			$PageLink=str_replace("--pagenum--",$fcounter,$PageLink);
			$retVal.=$PageLink . $Devider;
		};
		if(strlen($retVal)>strlen($Devider))$retVal=substr($retVal,0,(strlen($retVal)-strlen($Devider)));
		if(strlen($retVal)>0)$retVal=str_replace("--pageslist--",$retVal,$this->prms['plTemplate']->Value);
		$this->PagesCount=floor($lastPage);
		return $retVal;
	}

	function Install($reinstall=false){
		$installsql="DROP TABLE IF EXISTS `$this->itemsTable`;
			CREATE TABLE `$this->itemsTable` (
			`id` int(11) NOT NULL auto_increment,
			`node` int(11) NOT NULL default '0',
			`name` varchar(250) NOT NULL default '',
			`description` int(11) NOT NULL default '0',
			`mininame` varchar(250) NOT NULL default '',
			`filename` varchar(250) NOT NULL default '',
			`miniwidth` int(11) NOT NULL default '0',
			`miniheight` int(11) NOT NULL default '0',
			`width` int(11) NOT NULL default '0',
			`height` int(11) NOT NULL default '0',
			`pubdate` int(11) NOT NULL default '0',
			`visible` int(11) NOT NULL default '0',
			`deleted` int(11) NOT NULL default '0',
			PRIMARY KEY  (`id`)
			)";
		$splitedinstructions=split(";",$installsql);
		foreach($splitedinstructions as $oneinstruction)
			if($reinstall){
				$this->dbc->sql_query($oneinstruction);
			}else if(strstr($oneinstruction,"DROP TABLE")==FALSE){
					$this->dbc->sql_query($oneinstruction);
				};
	}

};



$theGalleryModule=new clsGalleryModule("gallery","галерея",$db);
$modsArray["gallery"]=$theGalleryModule;

?>