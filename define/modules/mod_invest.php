<?php

class clsInvestModule extends clsModule{


	function clsInvestModule($modName,$modDName,$dbconnector){
	    parent::clsModule($modName,$modDName,$dbconnector);
	    $this->SearchAble=true;
	    $this->version='1.0.1';
	    $this->helpstring='<p>������ ��� ������ ��������.</p>';
	    $this->modTable="mod_invest";

	}

	function MakeAdminOuput($theNode, $theFormPrefix, $theSessionSettings){
	    $retVal='������ �� �������������';
	    return $retVal;
	}

	function MakeUserOuput($theNode, $theFormPrefix){
		global $HTTP_POST_VARS, $coreParams, $SiteMainURL;
	    $retVal=array();
		if($HTTP_POST_VARS["action"]=="step1"){
			$messagetext="����� �������:\r\n";
			$messagetext.="\r\n\r\n";
			$messagetext.="�������: " . $HTTP_POST_VARS["lastname"] . "\r\n";
			$messagetext.="���: " . $HTTP_POST_VARS["firstname"] . "\r\n";
			$messagetext.="��������: " . $HTTP_POST_VARS["surename"] . "\r\n";
			$messagetext.="���������� �������: " . $HTTP_POST_VARS["phone"] . "\r\n";
			$messagetext.="����������� �����: " . $HTTP_POST_VARS["email"] . "\r\n";
			$fromemail=$coreParams["webmasteremail"]->Value;
			$mailheader="MIME-Version: 1.0\nContent-type: text/plain; charset=windows-1251\nDate: " . gmdate('D, d M Y H:i:s', time()) . " UT\nX-Priority: Normal\nX-Mailer: PHP\n";
			$mailheader.="From: " . $fromemail . "<$fromemail>\n";
			if(mail($fromemail,"����� ������� �� ����� $SiteMainURL" ,$messagetext,$mailheader)){
				//echo "����� �� �����������!!!";
			};
			$form='<h4>��� 2. �������� ���������.</h4><form method=post action="' . $theFormPrefix . '"><input type=hidden name=action value=step2><input type=hidden name=lastname value="' . CutQuots($HTTP_POST_VARS["lastname"]) . '">
					<input type=hidden name=firstname value="' . CutQuots($HTTP_POST_VARS["firstname"]) . '">
					<input type=hidden name=surename value="' . CutQuots($HTTP_POST_VARS["surename"]) . '">
					<input type=hidden name=phone value="' . CutQuots($HTTP_POST_VARS["phone"]) . '">
					<input type=hidden name=email value="' . CutQuots($HTTP_POST_VARS["email"]) . '">
					<table width=100% border=0 cellspacing=0 cellpadding=0><tr><td>';
			$form.='��� ���������� �������� � �������� ����� ��� ���������� ����������� �� ���������� �����������:<br><br>
				<a href="http://www.itinvest.ru/editorfiles/File/documents/contract.pdf">������� �� ���������� ������������</A> (���� .pdf, ������ 118 Kb)<br>
				<a href="http://www.itinvest.ru/editorfiles/File/documents/reglament.pdf">��������� ����������� ������������ (���������� �1)</A> (���� .pdf, ���-��� 496 Kb) <br>
				<a href="http://www.itinvest.ru/editorfiles/File/documents/dogv_depo.pdf">������������ �������</A> (���� .pdf, ������ 172 Kb) <BR>
				<a href="http://www.itinvest.ru/editorfiles/File/documents/depo-tarif.pdf">����. �2. ������ �� ������������ ������������</A> (���� .pdf, ������ 107 Kb) <br>
				<br>
				<a href="http://www.itinvest.ru/editorfiles/File/documents/risk.pdf">����������� � ������ (���������� �1)</A> (���� .pdf, ������ 177 Kb) <br>
				<a href="http://www.itinvest.ru/editorfiles/File/documents/tarif_total.pdf">������ (���������� �2)</A> (���� .pdf, ������ 150 Kb) <br>
				<a href="http://www.itinvest.ru/editorfiles/File/documents/agreement_sed.pdf">���������� � ��������� � ������������� ����������-�������� ������� (���������� �3)</A> (���� .pdf, ������ 193 Kb) <br>
				<a href="http://www.itinvest.ru/editorfiles/File/documents/uodd.pdf">������� ������������� ������������ ������������ (����� ����) (���-������� �1)</A> (���� .pdf, ������ 345 Kb) <br>
				<a href="http://www.itinvest.ru/editorfiles/File/documents/pril1.pdf">�������� �������� ����������, ������� ������ ��������� ����-����� (���������� �1 � ����)</A> (���� .pdf, ������ 203 Kb) <br>
				<a href="http://www.itinvest.ru/editorfiles/File/documents/pril2.pdf">�������� �������� ����������, ������� ��������� �������� �� ���� (���������� �2 � ����)</A> (���� .pdf, ������ 347 Kb) <br>';
			$form.="</td></tr><tr><td align=right><input type=submit class=button value=\"��� 3 >>\"></td></tr></table></form>";
			$retVal[0]=$form;
		}else if($HTTP_POST_VARS["action"]=="step2"){
			$form='<h4>��� 3. ���������� ������ ��� ���������� �������� � �������� �����.</h4><form method=post action="' . $theFormPrefix . '" name="form_regP"><input type=hidden name=action value=step3>';
			$form.='
<script>
function Months1()
{
  document.write(
\'<option value=""></option>\'+
\'<option value=01>������</option>\'+
\'<option value=02>�������</option>\'+
\'<option value=03>�����</option>\'+
\'<option value=04>������</option>\'+
\'<option value=05>���</option>\'+
\'<option value=06>����</option>\'+
\'<option value=07>����</option>\'+
\'<option value=08>�������</option>\'+
\'<option value=09>��������</option>\'+
\'<option value=10>�������</option>\'+
\'<option value=11>������</option>\'+
\'<option value=12>�������</option>\');
}

function Months2()
{
  document.write(
\'<option value=""></option>\'+
\'<option value=01>������</option>\'+
\'<option value=02>�������</option>\'+
\'<option value=03>�����</option>\'+
\'<option value=04>������</option>\'+
\'<option value=05>���</option>\'+
\'<option value=06>����</option>\'+
\'<option value=07>����</option>\'+
\'<option value=08>�������</option>\'+
\'<option value=09>��������</option>\'+
\'<option value=10>�������</option>\'+
\'<option value=11>������</option>\'+
\'<option value=12>�������</option>\');
}

function Months3()
{
  document.write(
\'<option value=""></option>\'+
\'<option value=01>������</option>\'+
\'<option value=02>�������</option>\'+
\'<option value=03>�����</option>\'+
\'<option value=04>������</option>\'+
\'<option value=05>���</option>\'+
\'<option value=06>����</option>\'+
\'<option value=07>����</option>\'+
\'<option value=08>�������</option>\'+
\'<option value=09>��������</option>\'+
\'<option value=10>�������</option>\'+
\'<option value=11>������</option>\'+
\'<option value=12>�������</option>\');
}

function Months()
{
  document.write(
\'<option value=""></option>\'+
\'<option value=01>������</option>\'+
\'<option value=02>�������</option>\'+
\'<option value=03>�����</option>\'+
\'<option value=04>������</option>\'+
\'<option value=05>���</option>\'+
\'<option value=06>����</option>\'+
\'<option value=07>����</option>\'+
\'<option value=08>�������</option>\'+
\'<option value=09>��������</option>\'+
\'<option value=10>�������</option>\'+
\'<option value=11>������</option>\'+
\'<option value=12>�������</option>\');
}

function CheckInput(frm) {
	DoDates(frm);
		if(!document.form_regP.Last_Name.value) {
			alert(\'���������� ������� ������� !\');
			return false;
		}
		if(!document.form_regP.Name.value) {
			alert(\'���������� ������� ��� !\');
			return false;
		}
		if(!document.form_regP.Name_2.value) {
			alert(\'���������� ������� �������� !\');
			return false;
		}
		if(!document.form_regP.B_dateD.value) {
			alert(\'���������� ������� ���� �������� !\');
			return false;
		}
		if(!document.form_regP.B_dateM.value) {
			alert(\'���������� ������� ���� �������� !\');
			return false;
		}
		if(!document.form_regP.B_dateY.value) {
			alert(\'���������� ������� ���� �������� !\');
			return false;
		}
		if(!document.form_regP.Phone.value) {
			alert(\'���������� ������� ������� (� ����� ������) !\');
			return false;
		}
		if(!document.form_regP.E_mail.value) {
			alert(\'���������� ������� ����� ����������� ����� !\');
			return false;
		}
		if(!document.form_regP.D_num.value) {
			alert(\'���������� ������� � �������� !\');
			return false;
		}
		if(!document.form_regP.D_ser.value) {
			alert(\'���������� ������� ����� �������� !\');
			return false;
		}
		if(!document.form_regP.D_dateD.value) {
			alert(\'���������� ������� ���� ������ �������� !\');
			return false;
		}
		if(!document.form_regP.D_dateM.value) {
			alert(\'���������� ������� ���� ������ �������� !\');
			return false;
		}
		if(!document.form_regP.D_dateY.value) {
			alert(\'���������� ������� ���� ������ �������� !\');
			return false;
		}
		if(!document.form_regP.D_who.value) {
			alert(\'���������� ������� ��� ����� ������� !\');
			return false;
		}
		if(!document.form_regP.Adr_reg.value) {
			alert(\'���������� ������� ����� ����������� !\');
			return false;
		}
		if(!document.form_regP.Ind_reg.value) {
			alert(\'���������� ������� ������ ����������� !\');
			return false;
		}
		if(!document.form_regP.Adr_reg.value) {
			alert(\'���������� ������� ����� ����������� !\');
			return false;
		}
		if(!document.form_regP.Zip_Code.value) {
			alert(\'���������� ������� �������� ������!\');
			return false;
		}
		return true;
	}
function add0(dn)
{
  dn=dn.replace(/\s| /g,"");
  if (!isNaN(dn)&&(dn.length==1)) return (\'0\'+dn);
  else return dn;
}

function DoDates(subf)
{
subf.B_date.value = add0(subf.B_dateD.value)+subf.B_dateM.value+subf.B_dateY.value;
subf.D_date.value = add0(subf.D_dateD.value)+subf.D_dateM.value+subf.D_dateY.value;
}

function txt_box() {
	if(document.form_regP.post_adr.checked) {document.form_regP.txtbox.value=document.form_regP.Adr_reg.value;
			document.form_regP.txtind.value=document.form_regP.Ind_reg.value;}
}

function txt_sab() {
	if(document.form_regP.adr_fakt.checked) {document.form_regP.txtsab.value=document.form_regP.Adr_reg.value;}
}

</script>	
<TABLE border=0 cellpadding=1 cellspacing=1 width=470>
<tr><td>
    <table border="0" width="100%" bgcolor=#ffffff>
        <th colspan=2 align="left">1. ����� ������</th>
        <tr>
            <td align="right" width="30%"><font color=#FF0000>* </font>�������: </td>
            <td width="70%"><input type="text" size="30" name="Last_Name" maxLength=30 value="' . $HTTP_POST_VARS["lastname"] . '"> </td>
        </tr>
        <tr>
            <td align="right" width="30%"><font color=#FF0000>* </font>���: </td>
            <td width="70%"><input type="text" size="30" name="Name" maxLength=30 value="' . $HTTP_POST_VARS["firstname"] . '"> </td>
        </tr>
        <tr>
            <td align="right" width="30%"><font color=#FF0000>* </font>��������: </td>
            <td width="70%"><input type="text" size="30" name="Name_2" maxLength=30 value="' . $HTTP_POST_VARS["surename"] . '"> </td>
        </tr>
        <tr>
            <td align="right" width="30%"><font color=#FF0000>* </font>���� ��������: </td>
            <td width="70%"><input type="text" name="B_dateD" maxLength=2 style="width:21px" value="">
                            <select name="B_dateM" size="1"><script language="Javascript">Months();</script></select>
                            <input type="text" size="3" name="B_dateY" maxLength=4 style="width:37px" value="">
                            <input type="hidden" name="B_date">
            </td>
        </tr>
        <tr>
            <td align="right" width="30%"><font color=#FF0000>* </font>����� ��������: </td>
            <td width="70%"><input type="text" size="30" name="palace_born" maxLength=30 value=""> </td>
        </tr>
        <tr>
            <td align="right" width="30%"><font color=#FF0000>* </font>�����������: </td>
            <td width="70%">
                <select name="Country" size="1">
<option value="���������" >���������</option>
<option value="�������" >�������</option>
<option value="�����������" >�����������</option>
<option value="�������" >�������</option>
<option value="�����" >�����</option>
<option value="������" >������</option>
<option value="�������" >�������</option>
<option value="������� � �������" >������� � �������</option>
<option value="���������" >���������</option>
<option value="�������" >�������</option>
<option value="����������" >����������</option>
<option value="��������� �������" >��������� �������</option>
<option value="���������" >���������</option>
<option value="��������" >��������</option>
<option value="�������" >�������</option>
<option value="�����" >�����</option>
<option value="����������" >����������</option>
<option value="�������" >�������</option>
<option value="�����" >�����</option>
<option value="��������" >��������</option>
<option value="�������" >�������</option>
<option value="������ � �����������" >������ � �����������</option>
<option value="��������" >��������</option>
<option value="��������" >��������</option>
<option value="������" >������</option>
<option value="�������-����" >�������-����</option>
<option value="�������" >�������</option>
<option value="�����" >�����</option>
<option value="�������" >�������</option>
<option value="�������" >�������</option>
<option value="��������������" >��������������</option>
<option value="�������" >�������</option>
<option value="���������" >���������</option>
<option value="�������" >�������</option>
<option value="�����" >�����</option>
<option value="�����" >�����</option>
<option value="������" >������</option>
<option value="������" >������</option>
<option value="����" >����</option>
<option value="���������" >���������</option>
<option value="������" >������</option>
<option value="������-�����" >������-�����</option>
<option value="��������" >��������</option>
<option value="��������" >��������</option>
<option value="�������" >�������</option>
<option value="������" >������</option>
<option value="������" >������</option>
<option value="�����" >�����</option>
<option value="�������" >�������</option>
<option value="��������" >��������</option>
<option value="������������� ������" >������������� ������</option>
<option value="������" >������</option>
<option value="����" >����</option>
<option value="������" >������</option>
<option value="�������� �����" >�������� �����</option>
<option value="��������" >��������</option>
<option value="�������" >�������</option>
<option value="�����" >�����</option>
<option value="���������" >���������</option>
<option value="��������" >��������</option>
<option value="����" >����</option>
<option value="����" >����</option>
<option value="��������" >��������</option>
<option value="��������" >��������</option>
<option value="�������" >�������</option>
<option value="������" >������</option>
<option value="�����" >�����</option>
<option value="����-�����" >����-�����</option>
<option value="���������" >���������</option>
<option value="��������" >��������</option>
<option value="�������" >�������</option>
<option value="������" >������</option>
<option value="�����" >�����</option>
<option value="�����" >�����</option>
<option value="����" >����</option>
<option value="��������" >��������</option>
<option value="��������" >��������</option>
<option value="�����" >�����</option>
<option value="��������" >��������</option>
<option value="��������� �������" >��������� �������</option>
<option value="�����" >�����</option>
<option value="����� (����)" >����� (����)</option>
<option value="����� (����������)" >����� (����������)</option>
<option value="�����-����" >�����-����</option>
<option value="���-�"�����" >���-�"�����</option>
<option value="����" >����</option>
<option value="������" >������</option>
<option value="����" >����</option>
<option value="������" >������</option>
<option value="������" >������</option>
<option value="�������" >�������</option>
<option value="�����" >�����</option>
<option value="�����" >�����</option>
<option value="�����" >�����</option>
<option value="�����������" >�����������</option>
<option value="����������" >����������</option>
<option value="��������" >��������</option>
<option value="����������" >����������</option>
<option value="����������" >����������</option>
<option value="���������" >���������</option>
<option value="������" >������</option>
<option value="��������" >��������</option>
<option value="����" >����</option>
<option value="��������" >��������</option>
<option value="������" >������</option>
<option value="�������" >�������</option>
<option value="��������� �������" >��������� �������</option>
<option value="�������" >�������</option>
<option value="����������" >����������</option>
<option value="��������" >��������</option>
<option value="��������" >��������</option>
<option value="������" >������</option>
<option value="��������" >��������</option>
<option value="������" >������</option>
<option value="�������" >�������</option>
<option value="�����" >�����</option>
<option value="�����" >�����</option>
<option value="�����" >�����</option>
<option value="�������" >�������</option>
<option value="����������" >����������</option>
<option value="���������" >���������</option>
<option value="����� ��������" >����� ��������</option>
<option value="��������" >��������</option>
<option value="�����. ������.������" >�����. ������.������</option>
<option value="����" >����</option>
<option value="��������" >��������</option>
<option value="�����" >�����</option>
<option value="������" >������</option>
<option value="�����-����� ������" >�����-����� ������</option>
<option value="��������" >��������</option>
<option value="����" >����</option>
<option value="������" >������</option>
<option value="����������" >����������</option>
<option value="������" selected>������</option>
<option value="������" >������</option>
<option value="�������" >�������</option>
<option value="���������" >���������</option>
<option value="���-������" >���-������</option>
<option value="���-���� � ��������" >���-���� � ��������</option>
<option value="���������� ������" >���������� ������</option>
<option value="���������" >���������</option>
<option value="����������� �������" >����������� �������</option>
<option value="���-������� � ������" >���-������� � ������</option>
<option value="���-���� � �����" >���-���� � �����</option>
<option value="�������" >�������</option>
<option value="����-�����" >����-�����</option>
<option value="��������" >��������</option>
<option value="�����" >�����</option>
<option value="��������" >��������</option>
<option value="��������" >��������</option>
<option value="����. ����� �������" >����. ����� �������</option>
<option value="���������� �������" >���������� �������</option>
<option value="������" >������</option>
<option value="�����" >�����</option>
<option value="�������" >�������</option>
<option value="������-�����" >������-�����</option>
<option value="�����������" >�����������</option>
<option value="�������" >�������</option>
<option value="�������" >�������</option>
<option value="��������" >��������</option>
<option value="����" >����</option>
<option value="�����" >�����</option>
<option value="�������� � ������" >�������� � ������</option>
<option value="������" >������</option>
<option value="�����" >�����</option>
<option value="���������" >���������</option>
<option value="������" >������</option>
<option value="������" >������</option>
<option value="����������" >����������</option>
<option value="�������" >�������</option>
<option value="�������" >�������</option>
<option value="�����" >�����</option>
<option value="���������" >���������</option>
<option value="���������" >���������</option>
<option value="�������" >�������</option>
<option value="��������" >��������</option>
<option value="���������������.����" >���������������.����</option>
<option value="���" >���</option>
<option value="�����" >�����</option>
<option value="����" >����</option>
<option value="���������" >���������</option>
<option value="������" >������</option>
<option value="���-�����" >���-�����</option>
<option value="�������" >�������</option>
<option value="�������������� �����" >�������������� �����</option>
<option value="�������" >�������</option>
<option value="�������" >�������</option>
<option value="�������" >�������</option>
<option value="���������" >���������</option>
<option value="����-����������� ���" >����-����������� ���</option>
<option value="������" >������</option>
<option value="������" >������</option>
            </select> </td>
        </tr>
        <tr>
            <td align="right" width="30%">���:</td>
            <td width="70%"><input type="text" size="20" name="f_inn" maxLength=30 value=""></td>
        </tr>
      </table>
</td></tr>
</table>
<TABLE border=0 cellpadding=1 cellspacing=1 width=470>
<tr><td bgcolor=#ffffff>
      <table border=0 width="100%"  bgcolor=#ffffff>
        <th colspan=2 align="left">2. ���������� ������</th>
        <tr>
            <td align="right"><font color=#FF0000>* </font>��� ���������: </td>
            <td><select name=type_document>
<option value="������� ���������� ��">������� ���������� ��</option>
<option value="����������� �������">����������� �������</option>
<option value="������������� �������">������������� �������</option>
<option value="��� �� ����������">��� �� ����������</option>
</select>
</td>
        </tr>
        <tr>
            <td align="right" width="30%"><font color=#FF0000> * </font>������� �����: </td>
            <td width="70%"><input type="text" size="8" name="D_ser" maxLength=20 value=""></td><tr>
			<tr><td align="right"><font color=#FF0000>* </font>� </td><td><input type="text" size="8" name="D_num" maxLength=20 value=""></tr>
			<tr><td align="right"><font color=#FF0000> * </font>���� ������ </td><td><input type="text" name="D_dateD" maxLength=2 style="width:21px" value=""> <select name="D_dateM" size="1"><script language="Javascript">Months3();</script></select> <input type="text" size="3" name="D_dateY" maxLength=4 style="width:37px" value=""></td></tr>
                            <input type="hidden" name="D_date">
            </td>
        </tr>
        <tr>
            <td align="right"><font color=#FF0000>* </font>��� �����:</td>
            <td ><input type="text" size="30" name="D_who" maxLength=100 value=""></td>
        </tr>
        <tr>
            <td align="right" width="30%"><font color=#FF0000>* </font>������ ������ �����������: </td>
            <td width="70%"><input type="text" size="30" name="Ind_reg" maxLength=30 value=""> </td>
        </tr>
        <tr>
            <td align="right" nowrap><font color=#FF0000>* </font>����� ���������� �����������: </td>
            <td><textarea name="Adr_reg" rows="2" cols="30" OnChange="txt_box();txt_sab();"></textarea></td>
        </tr>
        <tr>
            <td align="right" nowrap><input type=checkbox name=adr_fakt value="1" checked OnClick="JavaScript:document.getElementById(\'txtsab\').readOnly=!document.getElementById(\'txtsab\').readOnly;document.getElementById(\'txtind\').readOnly=false;document.form_regP.txtsab.value=\'\';txt_sab();"></td>
            <td>����� ������������ ���������� ��������� � ������� ���������� �����������</td>
        </tr>
        <tr>
            <td align="right" nowrap><input type=checkbox name=post_adr value="1" checked OnClick="JavaScript:document.getElementById(\'txtbox\').readOnly=!document.getElementById(\'txtbox\').readOnly;document.getElementById(\'txtind\').readOnly=false;document.form_regP.txtbox.value=\'\';document.form_regP.txtind.value=\'\';txt_box();"></td>
            <td>�������� ����� ��������� � ������� ���������� �����������</td>
        </tr>
      </table>
</td></tr>
</table>
<TABLE border=0 cellpadding=1 cellspacing=1 width=470>
<tr><td bgcolor=#ffffff>
      <table border=0 width="100%"  bgcolor=#ffffff>
        <th colspan=2 align="left">3. ���������� ����������</th>
        <tr>
            <td align="right" width="30%"><font color=#FF0000>* </font>����������� �����: </td>
            <td width="70%"><input type="text" size="25" name="E_mail" maxLength=30 value="' . $HTTP_POST_VARS["email"] . '"> </td>
        </tr>
        <tr>
            <td align="right" width="30%"><b id=a_Phone><font color=#ff0000>* </font></b>�������� (� ����� ������): </td>
            <td width="70%"><input type="text" size="4" name="Phone_kod" maxLength=10 value="()">&nbsp;<input type="text" size="20" name="Phone" maxLength=60 value=""></td>
        </tr>
        <tr>
            <td align="right" width="30%">���� (� ����� ������): </td>
            <td width="70%"><input type="text" size="4" name="Fax_kod" maxLength=10 value="()">&nbsp;<input type="text" size="20" name="Fax" maxLength=30 value=""> </td>
        </tr>
        <tr>
            <td align="right" width="30%"><b id=a_Zip_Code><font color=#ff0000>* </font></b>�������� ������: </td>
            <td width="70%"><input type="text" id="txtind" size="25" name="Zip_Code" maxLength=30 value="" readonly> </td>
        </tr>
        <tr>
            <td align="right" width="30%"><b id=a_Adress><font color=#ff0000>* </font></b>�������� �����: </td>
            <td ><textarea name="Adress" id="txtbox" rows="2" cols="35" readonly></textarea></td>
        </tr>
        <tr>
            <td align="right" width="30%" nowrap><b id=a_Adress><font color=#ff0000>* </font></b>����� ������������ ����������:</td>
            <td ><textarea name="post_adr_fakt" id="txtsab" rows="2" cols="35" readonly></textarea></td>
        </tr>
      </table>
</td></tr>
<tr><td align=right><input type="submit"  class="button" onClick="return CheckInput(this.form);" value="��� 4 >>"></td></tr>
</table>
</form>';
			
			$retVal[0]=$form;
		}else if($HTTP_POST_VARS["action"]=="step3"){
			$messagetext="����� ������� (��� 3):\r\n";
			$messagetext.="\r\n\r\n";
			$messagetext.="�������: " . $HTTP_POST_VARS["Last_Namename"] . "\r\n";
			$messagetext.="���: " . $HTTP_POST_VARS["Name"] . "\r\n";
			$messagetext.="��������: " . $HTTP_POST_VARS["Name_2"] . "\r\n";

			$messagetext.="���� ��������: " . $HTTP_POST_VARS["B_date"] . "\r\n";
			$messagetext.="����� ��������: " . $HTTP_POST_VARS["palace_born"] . "\r\n";
			$messagetext.="�����������: " . $HTTP_POST_VARS["Country"] . "\r\n";
			$messagetext.="���: " . $HTTP_POST_VARS["f_inn"] . "\r\n";
			$messagetext.="��� ���������: " . $HTTP_POST_VARS["type_document"] . "\r\n";
			$messagetext.="������� �����: " . $HTTP_POST_VARS["D_ser"] . "\r\n";
			$messagetext.="����� : " . $HTTP_POST_VARS["D_num"] . "\r\n";
			$messagetext.="���� ������: " . $HTTP_POST_VARS["D_date"] . "\r\n";
			$messagetext.="��� �����: " . $HTTP_POST_VARS["D_who"] . "\r\n";
			$messagetext.="������ ������ �����������: " . $HTTP_POST_VARS["Ind_reg"] . "\r\n";
			$messagetext.="����� ���������� �����������: " . $HTTP_POST_VARS["Adr_reg"] . "\r\n";
			$messagetext.="�������� ������: " . $HTTP_POST_VARS["Zip_Code"] . "\r\n";
			$messagetext.="�������� �����: " . $HTTP_POST_VARS["Adress"] . "\r\n";
			$messagetext.="����� ������������ ����������: " . $HTTP_POST_VARS["post_adr_fakt"] . "\r\n";
			
			$messagetext.="�������: " . $HTTP_POST_VARS["Phone_kod"] . " " . $HTTP_POST_VARS["Phone"] . "\r\n";
			$messagetext.="����: " . $HTTP_POST_VARS["Fax_kod"] . " " . $HTTP_POST_VARS["Fax"] . "\r\n";
			$messagetext.="����������� �����: " . $HTTP_POST_VARS["E_mail"] . "\r\n";
			$fromemail=$coreParams["webmasteremail"]->Value;
			$mailheader="MIME-Version: 1.0\nContent-type: text/plain; charset=windows-1251\nDate: " . gmdate('D, d M Y H:i:s', time()) . " UT\nX-Priority: Normal\nX-Mailer: PHP\n";
			$mailheader.="From: " . $fromemail . "<$fromemail>\n";
			if(mail($fromemail,"����� ������� �� ����� $SiteMainURL  (��� 3)" ,$messagetext,$mailheader)){
				//echo "����� �� �����������!!!";
			};
			
			$form='<h4>��� 4. ���������� ����������. ������ � �������� �������.</h4>
			<p>����� ���������� ������ ���������� ����� ��������� ��������� � ������������� � ��������� �� ���������� � ������������ ������������, ����������� ��������, � ����� �������� ��������-������. ��� ���� ���������� ����� �������.</p>
			<p>�������� ����� ����������� ��� ���������� ���������� � ������� (�� ��������������� ������ ��.��������)</p>
			<p>����������� �������� ��������� � ������ � �������� <a href="content.php?id=12">���</a> ����� ���������� ��� �� ����� �� ��������� � ������ �����. ����� ���������, ��������������, � ������� ���� ������.</p>
			<p>����� ���������� ���������� ��� ���������� �������� ��� �� ����� ����������� �����, ��������� � ������, ������ ������ ������� � �������� ������� � ����������.</p>';
			$retVal[0]=$form;
		}else if($HTTP_POST_VARS["action"]=="step4"){
		}else{
			$form="<h4>��� 1. ���������� ����������.</h4><form method=post action=\"" . $theFormPrefix . "\"><input type=hidden name=action value=step1>";
			$form.="<table width=100% border=0 cellspacing=0 cellpadding=0>";
			$form.="<tr><td align=right>�������:</td><td><input type=text class=text name=lastname size=30></td></tr>";
			$form.="<tr><td align=right>���:</td><td><input type=text class=text name=firstname size=30></td></tr>";
			$form.="<tr><td align=right>��������:</td><td><input type=text class=text name=surename size=30></td></tr>";
			$form.="<tr><td align=right>�������:</td><td><input type=text class=text name=phone size=30></td></tr>";
			$form.="<tr><td align=right>E-mail:</td><td><input type=text class=text name=email size=30></td></tr>";
			$form.="<tr><td></td><td align=right><input type=submit class=button value=\"��� 2 >>\"></td></tr>";
			$form.="</table></form>";
			$retVal[0]=$form;
		};
	    return $retVal;
	}


}

$theInvestModule=new clsInvestModule('invest','���������� ��������',$db);
$modsArray['invest']=$theInvestModule;
?>