<?php
function slotBegin(){
	return "<table width=100% border=0 cellpadding=0 cellspacing=0 height=100%>" .
		"<tr><td valign=top width=8 valign=top><img src=simages/data-1.gif border=0 width=8 height=206></td>" .
		"<td width=* valign=top>" .
		"<table border=0 cellpadding=0 width=100% cellspacing=0>" .
		"<tr><td height=1 align=left style=\"background-image:url('simages/data-2.gif');background-repeat:no-repeat;background-postition-x:left;\"><img src=format.gif border=0 width=1 height=1></td></tr>" .
		"<tr><td height=* align=left>";

};
function slotEnd(){
	return "</td></tr></table>" .
		"</td></tr></table>";
};
?>