		<table width=180 height=44 border=0 cellpadding=1 cellspacing=1>
		<form method=get action=search.php name=searcherform onsubmit="if(String(this.ss.value).length<3){alert('������ ��� ������ ������ ���� �� ������ 3-� ��������.');this.ss.focus();return false;}else{return true;};">
		<tr><td>&nbsp;</td><td align=right valign=bottom style="font-size:12px;color:#2F2923;font-weight:bold;">����� �� �����</td></tr>
		<tr><td><a title="������" href="javascript:if(String(document.forms['searcherform'].ss.value).length<3){alert('������ ��� ������ ������ ���� �� ������ 3-� ��������.');document.forms['searcherform'].ss.focus();}else{document.forms['searcherform'].submit()};"><img src=simages/search-go.gif border=0 width=23 height=23></a></td><td align=right><input type=text class=text name=ss size=25 value=""></td></tr>
		</form>
		</table>
