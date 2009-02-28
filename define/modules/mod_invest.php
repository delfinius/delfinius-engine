<?php

class clsInvestModule extends clsModule{


	function clsInvestModule($modName,$modDName,$dbconnector){
	    parent::clsModule($modName,$modDName,$dbconnector);
	    $this->SearchAble=true;
	    $this->version='1.0.1';
	    $this->helpstring='<p>Модуль для заказа матрасов.</p>';
	    $this->modTable="mod_invest";

	}

	function MakeAdminOuput($theNode, $theFormPrefix, $theSessionSettings){
	    $retVal='ничего не настраивается';
	    return $retVal;
	}

	function MakeUserOuput($theNode, $theFormPrefix){
		global $HTTP_POST_VARS, $coreParams, $SiteMainURL;
	    $retVal=array();
		if($HTTP_POST_VARS["action"]=="step1"){
			$messagetext="Новый договор:\r\n";
			$messagetext.="\r\n\r\n";
			$messagetext.="Фамилия: " . $HTTP_POST_VARS["lastname"] . "\r\n";
			$messagetext.="Имя: " . $HTTP_POST_VARS["firstname"] . "\r\n";
			$messagetext.="Отчество: " . $HTTP_POST_VARS["surename"] . "\r\n";
			$messagetext.="Контактный телефон: " . $HTTP_POST_VARS["phone"] . "\r\n";
			$messagetext.="Электронная почта: " . $HTTP_POST_VARS["email"] . "\r\n";
			$fromemail=$coreParams["webmasteremail"]->Value;
			$mailheader="MIME-Version: 1.0\nContent-type: text/plain; charset=windows-1251\nDate: " . gmdate('D, d M Y H:i:s', time()) . " UT\nX-Priority: Normal\nX-Mailer: PHP\n";
			$mailheader.="From: " . $fromemail . "<$fromemail>\n";
			if(mail($fromemail,"новый договор на сайте $SiteMainURL" ,$messagetext,$mailheader)){
				//echo "почта не отправилась!!!";
			};
			$form='<h4>Шаг 2. Основные документы.</h4><form method=post action="' . $theFormPrefix . '"><input type=hidden name=action value=step2><input type=hidden name=lastname value="' . CutQuots($HTTP_POST_VARS["lastname"]) . '">
					<input type=hidden name=firstname value="' . CutQuots($HTTP_POST_VARS["firstname"]) . '">
					<input type=hidden name=surename value="' . CutQuots($HTTP_POST_VARS["surename"]) . '">
					<input type=hidden name=phone value="' . CutQuots($HTTP_POST_VARS["phone"]) . '">
					<input type=hidden name=email value="' . CutQuots($HTTP_POST_VARS["email"]) . '">
					<table width=100% border=0 cellspacing=0 cellpadding=0><tr><td>';
			$form.='Для заключения договора и открытия счета Вам необходимо ознакомится со следующими документами:<br><br>
				<a href="http://www.itinvest.ru/editorfiles/File/documents/contract.pdf">Договор на брокерское обслуживание</A> (файл .pdf, размер 118 Kb)<br>
				<a href="http://www.itinvest.ru/editorfiles/File/documents/reglament.pdf">Регламент брокерского обслуживания (приложение №1)</A> (файл .pdf, раз-мер 496 Kb) <br>
				<a href="http://www.itinvest.ru/editorfiles/File/documents/dogv_depo.pdf">Депозитарный договор</A> (файл .pdf, размер 172 Kb) <BR>
				<a href="http://www.itinvest.ru/editorfiles/File/documents/depo-tarif.pdf">Прил. №2. Тарифы на депозитарное обслуживание</A> (файл .pdf, размер 107 Kb) <br>
				<br>
				<a href="http://www.itinvest.ru/editorfiles/File/documents/risk.pdf">Уведомление о рисках (приложение №1)</A> (файл .pdf, размер 177 Kb) <br>
				<a href="http://www.itinvest.ru/editorfiles/File/documents/tarif_total.pdf">Тарифы (приложение №2)</A> (файл .pdf, размер 150 Kb) <br>
				<a href="http://www.itinvest.ru/editorfiles/File/documents/agreement_sed.pdf">Соглашение о признании и использовании электронно-цифровой подписи (приложение №3)</A> (файл .pdf, размер 193 Kb) <br>
				<a href="http://www.itinvest.ru/editorfiles/File/documents/uodd.pdf">Условия осуществления Депозитарной деятельности (далее УОДД) (при-ложение №1)</A> (файл .pdf, размер 345 Kb) <br>
				<a href="http://www.itinvest.ru/editorfiles/File/documents/pril1.pdf">Перечень образцов документов, которые должны заполнять Депо-ненты (Приложение №1 к УОДД)</A> (файл .pdf, размер 203 Kb) <br>
				<a href="http://www.itinvest.ru/editorfiles/File/documents/pril2.pdf">Перечень образцов документов, которые Депоненты получают на руки (Приложение №2 к УОДД)</A> (файл .pdf, размер 347 Kb) <br>';
			$form.="</td></tr><tr><td align=right><input type=submit class=button value=\"Шаг 3 >>\"></td></tr></table></form>";
			$retVal[0]=$form;
		}else if($HTTP_POST_VARS["action"]=="step2"){
			$form='<h4>Шаг 3. Заполнение данных для оформления договора и открытия счета.</h4><form method=post action="' . $theFormPrefix . '" name="form_regP"><input type=hidden name=action value=step3>';
			$form.='
<script>
function Months1()
{
  document.write(
\'<option value=""></option>\'+
\'<option value=01>Января</option>\'+
\'<option value=02>Февраля</option>\'+
\'<option value=03>Марта</option>\'+
\'<option value=04>Апреля</option>\'+
\'<option value=05>Мая</option>\'+
\'<option value=06>Июня</option>\'+
\'<option value=07>Июля</option>\'+
\'<option value=08>Августа</option>\'+
\'<option value=09>Сентября</option>\'+
\'<option value=10>Октября</option>\'+
\'<option value=11>Ноября</option>\'+
\'<option value=12>Декабря</option>\');
}

function Months2()
{
  document.write(
\'<option value=""></option>\'+
\'<option value=01>Января</option>\'+
\'<option value=02>Февраля</option>\'+
\'<option value=03>Марта</option>\'+
\'<option value=04>Апреля</option>\'+
\'<option value=05>Мая</option>\'+
\'<option value=06>Июня</option>\'+
\'<option value=07>Июля</option>\'+
\'<option value=08>Августа</option>\'+
\'<option value=09>Сентября</option>\'+
\'<option value=10>Октября</option>\'+
\'<option value=11>Ноября</option>\'+
\'<option value=12>Декабря</option>\');
}

function Months3()
{
  document.write(
\'<option value=""></option>\'+
\'<option value=01>Января</option>\'+
\'<option value=02>Февраля</option>\'+
\'<option value=03>Марта</option>\'+
\'<option value=04>Апреля</option>\'+
\'<option value=05>Мая</option>\'+
\'<option value=06>Июня</option>\'+
\'<option value=07>Июля</option>\'+
\'<option value=08>Августа</option>\'+
\'<option value=09>Сентября</option>\'+
\'<option value=10>Октября</option>\'+
\'<option value=11>Ноября</option>\'+
\'<option value=12>Декабря</option>\');
}

function Months()
{
  document.write(
\'<option value=""></option>\'+
\'<option value=01>Января</option>\'+
\'<option value=02>Февраля</option>\'+
\'<option value=03>Марта</option>\'+
\'<option value=04>Апреля</option>\'+
\'<option value=05>Мая</option>\'+
\'<option value=06>Июня</option>\'+
\'<option value=07>Июля</option>\'+
\'<option value=08>Августа</option>\'+
\'<option value=09>Сентября</option>\'+
\'<option value=10>Октября</option>\'+
\'<option value=11>Ноября</option>\'+
\'<option value=12>Декабря</option>\');
}

function CheckInput(frm) {
	DoDates(frm);
		if(!document.form_regP.Last_Name.value) {
			alert(\'Пожалуйста введите Фамилию !\');
			return false;
		}
		if(!document.form_regP.Name.value) {
			alert(\'Пожалуйста введите Имя !\');
			return false;
		}
		if(!document.form_regP.Name_2.value) {
			alert(\'Пожалуйста введите Отчество !\');
			return false;
		}
		if(!document.form_regP.B_dateD.value) {
			alert(\'Пожалуйста введите Дату рождения !\');
			return false;
		}
		if(!document.form_regP.B_dateM.value) {
			alert(\'Пожалуйста введите Дату рождения !\');
			return false;
		}
		if(!document.form_regP.B_dateY.value) {
			alert(\'Пожалуйста введите Дату рождения !\');
			return false;
		}
		if(!document.form_regP.Phone.value) {
			alert(\'Пожалуйста введите Телефон (с кодом города) !\');
			return false;
		}
		if(!document.form_regP.E_mail.value) {
			alert(\'Пожалуйста введите адрес Электронной почты !\');
			return false;
		}
		if(!document.form_regP.D_num.value) {
			alert(\'Пожалуйста введите № Паспорта !\');
			return false;
		}
		if(!document.form_regP.D_ser.value) {
			alert(\'Пожалуйста введите серию Паспорта !\');
			return false;
		}
		if(!document.form_regP.D_dateD.value) {
			alert(\'Пожалуйста введите дату выдачи Паспорта !\');
			return false;
		}
		if(!document.form_regP.D_dateM.value) {
			alert(\'Пожалуйста введите дату выдачи Паспорта !\');
			return false;
		}
		if(!document.form_regP.D_dateY.value) {
			alert(\'Пожалуйста введите дату выдачи Паспорта !\');
			return false;
		}
		if(!document.form_regP.D_who.value) {
			alert(\'Пожалуйста введите кем выдан Паспорт !\');
			return false;
		}
		if(!document.form_regP.Adr_reg.value) {
			alert(\'Пожалуйста введите Адрес регистрации !\');
			return false;
		}
		if(!document.form_regP.Ind_reg.value) {
			alert(\'Пожалуйста введите Индекс регистрации !\');
			return false;
		}
		if(!document.form_regP.Adr_reg.value) {
			alert(\'Пожалуйста введите Адрес регистрации !\');
			return false;
		}
		if(!document.form_regP.Zip_Code.value) {
			alert(\'Пожалуйста введите почтовый индекс!\');
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
        <th colspan=2 align="left">1. Общие данные</th>
        <tr>
            <td align="right" width="30%"><font color=#FF0000>* </font>Фамилия: </td>
            <td width="70%"><input type="text" size="30" name="Last_Name" maxLength=30 value="' . $HTTP_POST_VARS["lastname"] . '"> </td>
        </tr>
        <tr>
            <td align="right" width="30%"><font color=#FF0000>* </font>Имя: </td>
            <td width="70%"><input type="text" size="30" name="Name" maxLength=30 value="' . $HTTP_POST_VARS["firstname"] . '"> </td>
        </tr>
        <tr>
            <td align="right" width="30%"><font color=#FF0000>* </font>Отчество: </td>
            <td width="70%"><input type="text" size="30" name="Name_2" maxLength=30 value="' . $HTTP_POST_VARS["surename"] . '"> </td>
        </tr>
        <tr>
            <td align="right" width="30%"><font color=#FF0000>* </font>Дата рождения: </td>
            <td width="70%"><input type="text" name="B_dateD" maxLength=2 style="width:21px" value="">
                            <select name="B_dateM" size="1"><script language="Javascript">Months();</script></select>
                            <input type="text" size="3" name="B_dateY" maxLength=4 style="width:37px" value="">
                            <input type="hidden" name="B_date">
            </td>
        </tr>
        <tr>
            <td align="right" width="30%"><font color=#FF0000>* </font>Место рождения: </td>
            <td width="70%"><input type="text" size="30" name="palace_born" maxLength=30 value=""> </td>
        </tr>
        <tr>
            <td align="right" width="30%"><font color=#FF0000>* </font>Гражданство: </td>
            <td width="70%">
                <select name="Country" size="1">
<option value="Австралия" >Австралия</option>
<option value="Австрия" >Австрия</option>
<option value="Азербайджан" >Азербайджан</option>
<option value="Албания" >Албания</option>
<option value="Алжир" >Алжир</option>
<option value="Ангола" >Ангола</option>
<option value="Андорра" >Андорра</option>
<option value="Антигуа и Барбуда" >Антигуа и Барбуда</option>
<option value="Аргентина" >Аргентина</option>
<option value="Армения" >Армения</option>
<option value="Афганистан" >Афганистан</option>
<option value="Багамские острова" >Багамские острова</option>
<option value="Бангладеш" >Бангладеш</option>
<option value="Барбадос" >Барбадос</option>
<option value="Бахрейн" >Бахрейн</option>
<option value="Белиз" >Белиз</option>
<option value="Белоруссия" >Белоруссия</option>
<option value="Бельгия" >Бельгия</option>
<option value="Бенин" >Бенин</option>
<option value="Болгария" >Болгария</option>
<option value="Боливия" >Боливия</option>
<option value="Босния и Герцеговина" >Босния и Герцеговина</option>
<option value="Ботсвана" >Ботсвана</option>
<option value="Бразилия" >Бразилия</option>
<option value="Бруней" >Бруней</option>
<option value="Буркина-Фасо" >Буркина-Фасо</option>
<option value="Бурунди" >Бурунди</option>
<option value="Бутан" >Бутан</option>
<option value="Вануату" >Вануату</option>
<option value="Ватикан" >Ватикан</option>
<option value="Великобритания" >Великобритания</option>
<option value="Венгрия" >Венгрия</option>
<option value="Венесуэла" >Венесуэла</option>
<option value="Вьетнам" >Вьетнам</option>
<option value="Габон" >Габон</option>
<option value="Гаити" >Гаити</option>
<option value="Гайана" >Гайана</option>
<option value="Гамбия" >Гамбия</option>
<option value="Гана" >Гана</option>
<option value="Гватемала" >Гватемала</option>
<option value="Гвинея" >Гвинея</option>
<option value="Гвинея-Бисау" >Гвинея-Бисау</option>
<option value="Германия" >Германия</option>
<option value="Гондурас" >Гондурас</option>
<option value="Гренада" >Гренада</option>
<option value="Греция" >Греция</option>
<option value="Грузия" >Грузия</option>
<option value="Дания" >Дания</option>
<option value="Джибути" >Джибути</option>
<option value="Доминика" >Доминика</option>
<option value="Доминиканская респуб" >Доминиканская респуб</option>
<option value="Египет" >Египет</option>
<option value="Заир" >Заир</option>
<option value="Замбия" >Замбия</option>
<option value="Западное Самоа" >Западное Самоа</option>
<option value="Зимбабве" >Зимбабве</option>
<option value="Израиль" >Израиль</option>
<option value="Индия" >Индия</option>
<option value="Индонезия" >Индонезия</option>
<option value="Иордания" >Иордания</option>
<option value="Ирак" >Ирак</option>
<option value="Иран" >Иран</option>
<option value="Ирландия" >Ирландия</option>
<option value="Исландия" >Исландия</option>
<option value="Испания" >Испания</option>
<option value="Италия" >Италия</option>
<option value="Йемен" >Йемен</option>
<option value="Кабо-Верде" >Кабо-Верде</option>
<option value="Казахстан" >Казахстан</option>
<option value="Камбоджа" >Камбоджа</option>
<option value="Камерун" >Камерун</option>
<option value="Канада" >Канада</option>
<option value="Катар" >Катар</option>
<option value="Кения" >Кения</option>
<option value="Кипр" >Кипр</option>
<option value="Киргизия" >Киргизия</option>
<option value="Кирибати" >Кирибати</option>
<option value="Китай" >Китай</option>
<option value="Колумбия" >Колумбия</option>
<option value="Коморские острова" >Коморские острова</option>
<option value="Конго" >Конго</option>
<option value="Корея (КНДР)" >Корея (КНДР)</option>
<option value="Корея (республика)" >Корея (республика)</option>
<option value="Коста-Рика" >Коста-Рика</option>
<option value="Кот-Д"Ивуар" >Кот-Д"Ивуар</option>
<option value="Куба" >Куба</option>
<option value="Кувейт" >Кувейт</option>
<option value="Лаос" >Лаос</option>
<option value="Латвия" >Латвия</option>
<option value="Лесото" >Лесото</option>
<option value="Либерия" >Либерия</option>
<option value="Ливан" >Ливан</option>
<option value="Ливия" >Ливия</option>
<option value="Литва" >Литва</option>
<option value="Лихтенштейн" >Лихтенштейн</option>
<option value="Люксенбург" >Люксенбург</option>
<option value="Маврикий" >Маврикий</option>
<option value="Мавритания" >Мавритания</option>
<option value="Мадагаскар" >Мадагаскар</option>
<option value="Македония" >Македония</option>
<option value="Малави" >Малави</option>
<option value="Малайзия" >Малайзия</option>
<option value="Мали" >Мали</option>
<option value="Мальдивы" >Мальдивы</option>
<option value="Мальта" >Мальта</option>
<option value="Марокко" >Марокко</option>
<option value="Маршаловы острова" >Маршаловы острова</option>
<option value="Мексика" >Мексика</option>
<option value="Микронезия" >Микронезия</option>
<option value="Мозамбик" >Мозамбик</option>
<option value="Молдавия" >Молдавия</option>
<option value="Монако" >Монако</option>
<option value="Монголия" >Монголия</option>
<option value="Мьянма" >Мьянма</option>
<option value="Намибия" >Намибия</option>
<option value="Науру" >Науру</option>
<option value="Непал" >Непал</option>
<option value="Нигер" >Нигер</option>
<option value="Нигерия" >Нигерия</option>
<option value="Нидерланды" >Нидерланды</option>
<option value="Никарагуа" >Никарагуа</option>
<option value="Новая Зеландия" >Новая Зеландия</option>
<option value="Норвегия" >Норвегия</option>
<option value="Объед. Арабск.Эмират" >Объед. Арабск.Эмират</option>
<option value="Оман" >Оман</option>
<option value="Пакистан" >Пакистан</option>
<option value="Палау" >Палау</option>
<option value="Панама" >Панама</option>
<option value="Папуа-Новая Гвинея" >Папуа-Новая Гвинея</option>
<option value="Парагвай" >Парагвай</option>
<option value="Перу" >Перу</option>
<option value="Польша" >Польша</option>
<option value="Португалия" >Португалия</option>
<option value="Россия" selected>Россия</option>
<option value="Руанда" >Руанда</option>
<option value="Румыния" >Румыния</option>
<option value="Сальвадор" >Сальвадор</option>
<option value="Сан-Марино" >Сан-Марино</option>
<option value="Сан-Томе и Принсипи" >Сан-Томе и Принсипи</option>
<option value="Саудовская Аравия" >Саудовская Аравия</option>
<option value="Свазиленд" >Свазиленд</option>
<option value="Сейшельские Острова" >Сейшельские Острова</option>
<option value="Сен-Винсент и Гренад" >Сен-Винсент и Гренад</option>
<option value="Сен-Китс и Невис" >Сен-Китс и Невис</option>
<option value="Сенегал" >Сенегал</option>
<option value="Сент-Люсия" >Сент-Люсия</option>
<option value="Сингапур" >Сингапур</option>
<option value="Сирия" >Сирия</option>
<option value="Словакия" >Словакия</option>
<option value="Словения" >Словения</option>
<option value="Соед. Штаты Америки" >Соед. Штаты Америки</option>
<option value="Соломоновы острова" >Соломоновы острова</option>
<option value="Сомали" >Сомали</option>
<option value="Судан" >Судан</option>
<option value="Суринам" >Суринам</option>
<option value="Сьерра-Леоне" >Сьерра-Леоне</option>
<option value="Таджикистан" >Таджикистан</option>
<option value="Таиланд" >Таиланд</option>
<option value="Тайвань" >Тайвань</option>
<option value="Танзания" >Танзания</option>
<option value="Того" >Того</option>
<option value="Тонга" >Тонга</option>
<option value="Тринидад и Тобаго" >Тринидад и Тобаго</option>
<option value="Тувалу" >Тувалу</option>
<option value="Тунис" >Тунис</option>
<option value="Туркмения" >Туркмения</option>
<option value="Турция" >Турция</option>
<option value="Уганда" >Уганда</option>
<option value="Узбекистан" >Узбекистан</option>
<option value="Украина" >Украина</option>
<option value="Уругвай" >Уругвай</option>
<option value="Фиджи" >Фиджи</option>
<option value="Филиппины" >Филиппины</option>
<option value="Финляндия" >Финляндия</option>
<option value="Франция" >Франция</option>
<option value="Хорватия" >Хорватия</option>
<option value="Центральноафрик.респ" >Центральноафрик.респ</option>
<option value="Чад" >Чад</option>
<option value="Чехия" >Чехия</option>
<option value="Чили" >Чили</option>
<option value="Швейцария" >Швейцария</option>
<option value="Швеция" >Швеция</option>
<option value="Шри-Ланка" >Шри-Ланка</option>
<option value="Эквадор" >Эквадор</option>
<option value="Экваториальная Гвине" >Экваториальная Гвине</option>
<option value="Эритрея" >Эритрея</option>
<option value="Эстония" >Эстония</option>
<option value="Эфиопия" >Эфиопия</option>
<option value="Югославия" >Югославия</option>
<option value="Южно-Африканская рес" >Южно-Африканская рес</option>
<option value="Ямайка" >Ямайка</option>
<option value="Япония" >Япония</option>
            </select> </td>
        </tr>
        <tr>
            <td align="right" width="30%">ИНН:</td>
            <td width="70%"><input type="text" size="20" name="f_inn" maxLength=30 value=""></td>
        </tr>
      </table>
</td></tr>
</table>
<TABLE border=0 cellpadding=1 cellspacing=1 width=470>
<tr><td bgcolor=#ffffff>
      <table border=0 width="100%"  bgcolor=#ffffff>
        <th colspan=2 align="left">2. Паспортные данные</th>
        <tr>
            <td align="right"><font color=#FF0000>* </font>Тип документа: </td>
            <td><select name=type_document>
<option value="паспорт гражданина РФ">паспорт гражданина РФ</option>
<option value="иностранный паспорт">иностранный паспорт</option>
<option value="удостоверение офицера">удостоверение офицера</option>
<option value="вид на жительство">вид на жительство</option>
</select>
</td>
        </tr>
        <tr>
            <td align="right" width="30%"><font color=#FF0000> * </font>Паспорт серия: </td>
            <td width="70%"><input type="text" size="8" name="D_ser" maxLength=20 value=""></td><tr>
			<tr><td align="right"><font color=#FF0000>* </font>№ </td><td><input type="text" size="8" name="D_num" maxLength=20 value=""></tr>
			<tr><td align="right"><font color=#FF0000> * </font>дата выдачи </td><td><input type="text" name="D_dateD" maxLength=2 style="width:21px" value=""> <select name="D_dateM" size="1"><script language="Javascript">Months3();</script></select> <input type="text" size="3" name="D_dateY" maxLength=4 style="width:37px" value=""></td></tr>
                            <input type="hidden" name="D_date">
            </td>
        </tr>
        <tr>
            <td align="right"><font color=#FF0000>* </font>кем выдан:</td>
            <td ><input type="text" size="30" name="D_who" maxLength=100 value=""></td>
        </tr>
        <tr>
            <td align="right" width="30%"><font color=#FF0000>* </font>Индекс адреса регистрации: </td>
            <td width="70%"><input type="text" size="30" name="Ind_reg" maxLength=30 value=""> </td>
        </tr>
        <tr>
            <td align="right" nowrap><font color=#FF0000>* </font>Адрес постоянной регистрации: </td>
            <td><textarea name="Adr_reg" rows="2" cols="30" OnChange="txt_box();txt_sab();"></textarea></td>
        </tr>
        <tr>
            <td align="right" nowrap><input type=checkbox name=adr_fakt value="1" checked OnClick="JavaScript:document.getElementById(\'txtsab\').readOnly=!document.getElementById(\'txtsab\').readOnly;document.getElementById(\'txtind\').readOnly=false;document.form_regP.txtsab.value=\'\';txt_sab();"></td>
            <td>адрес фактического проживания совпадает с адресом постоянной регистрации</td>
        </tr>
        <tr>
            <td align="right" nowrap><input type=checkbox name=post_adr value="1" checked OnClick="JavaScript:document.getElementById(\'txtbox\').readOnly=!document.getElementById(\'txtbox\').readOnly;document.getElementById(\'txtind\').readOnly=false;document.form_regP.txtbox.value=\'\';document.form_regP.txtind.value=\'\';txt_box();"></td>
            <td>почтовый адрес совпадает с адресом постоянной регистрации</td>
        </tr>
      </table>
</td></tr>
</table>
<TABLE border=0 cellpadding=1 cellspacing=1 width=470>
<tr><td bgcolor=#ffffff>
      <table border=0 width="100%"  bgcolor=#ffffff>
        <th colspan=2 align="left">3. Контактные координаты</th>
        <tr>
            <td align="right" width="30%"><font color=#FF0000>* </font>Электронная почта: </td>
            <td width="70%"><input type="text" size="25" name="E_mail" maxLength=30 value="' . $HTTP_POST_VARS["email"] . '"> </td>
        </tr>
        <tr>
            <td align="right" width="30%"><b id=a_Phone><font color=#ff0000>* </font></b>Телефоны (с кодом города): </td>
            <td width="70%"><input type="text" size="4" name="Phone_kod" maxLength=10 value="()">&nbsp;<input type="text" size="20" name="Phone" maxLength=60 value=""></td>
        </tr>
        <tr>
            <td align="right" width="30%">Факс (с кодом города): </td>
            <td width="70%"><input type="text" size="4" name="Fax_kod" maxLength=10 value="()">&nbsp;<input type="text" size="20" name="Fax" maxLength=30 value=""> </td>
        </tr>
        <tr>
            <td align="right" width="30%"><b id=a_Zip_Code><font color=#ff0000>* </font></b>Почтовый индекс: </td>
            <td width="70%"><input type="text" id="txtind" size="25" name="Zip_Code" maxLength=30 value="" readonly> </td>
        </tr>
        <tr>
            <td align="right" width="30%"><b id=a_Adress><font color=#ff0000>* </font></b>Почтовый адрес: </td>
            <td ><textarea name="Adress" id="txtbox" rows="2" cols="35" readonly></textarea></td>
        </tr>
        <tr>
            <td align="right" width="30%" nowrap><b id=a_Adress><font color=#ff0000>* </font></b>Адрес фактического проживания:</td>
            <td ><textarea name="post_adr_fakt" id="txtsab" rows="2" cols="35" readonly></textarea></td>
        </tr>
      </table>
</td></tr>
<tr><td align=right><input type="submit"  class="button" onClick="return CheckInput(this.form);" value="шаг 4 >>"></td></tr>
</table>
</form>';
			
			$retVal[0]=$form;
		}else if($HTTP_POST_VARS["action"]=="step3"){
			$messagetext="Новый договор (шаг 3):\r\n";
			$messagetext.="\r\n\r\n";
			$messagetext.="Фамилия: " . $HTTP_POST_VARS["Last_Namename"] . "\r\n";
			$messagetext.="Имя: " . $HTTP_POST_VARS["Name"] . "\r\n";
			$messagetext.="Отчество: " . $HTTP_POST_VARS["Name_2"] . "\r\n";

			$messagetext.="Дата рождения: " . $HTTP_POST_VARS["B_date"] . "\r\n";
			$messagetext.="Место рождения: " . $HTTP_POST_VARS["palace_born"] . "\r\n";
			$messagetext.="Гражданство: " . $HTTP_POST_VARS["Country"] . "\r\n";
			$messagetext.="ИНН: " . $HTTP_POST_VARS["f_inn"] . "\r\n";
			$messagetext.="Тип документа: " . $HTTP_POST_VARS["type_document"] . "\r\n";
			$messagetext.="Паспорт серия: " . $HTTP_POST_VARS["D_ser"] . "\r\n";
			$messagetext.="Номер : " . $HTTP_POST_VARS["D_num"] . "\r\n";
			$messagetext.="Дата выдачи: " . $HTTP_POST_VARS["D_date"] . "\r\n";
			$messagetext.="Кем выдан: " . $HTTP_POST_VARS["D_who"] . "\r\n";
			$messagetext.="Индекс адреса регистрации: " . $HTTP_POST_VARS["Ind_reg"] . "\r\n";
			$messagetext.="Адрес постоянной регистрации: " . $HTTP_POST_VARS["Adr_reg"] . "\r\n";
			$messagetext.="Почтовый индекс: " . $HTTP_POST_VARS["Zip_Code"] . "\r\n";
			$messagetext.="Почтовый адрес: " . $HTTP_POST_VARS["Adress"] . "\r\n";
			$messagetext.="Адрес фактического проживания: " . $HTTP_POST_VARS["post_adr_fakt"] . "\r\n";
			
			$messagetext.="Телефон: " . $HTTP_POST_VARS["Phone_kod"] . " " . $HTTP_POST_VARS["Phone"] . "\r\n";
			$messagetext.="Факс: " . $HTTP_POST_VARS["Fax_kod"] . " " . $HTTP_POST_VARS["Fax"] . "\r\n";
			$messagetext.="Электронная почта: " . $HTTP_POST_VARS["E_mail"] . "\r\n";
			$fromemail=$coreParams["webmasteremail"]->Value;
			$mailheader="MIME-Version: 1.0\nContent-type: text/plain; charset=windows-1251\nDate: " . gmdate('D, d M Y H:i:s', time()) . " UT\nX-Priority: Normal\nX-Mailer: PHP\n";
			$mailheader.="From: " . $fromemail . "<$fromemail>\n";
			if(mail($fromemail,"новый договор на сайте $SiteMainURL  (шаг 3)" ,$messagetext,$mailheader)){
				//echo "почта не отправилась!!!";
			};
			
			$form='<h4>Шаг 4. Подписание документов. Доступ к торговой системе.</h4>
			<p>После заполнения анкеты необходимо лично подписать заявления о присоединении к договорам на брокерское и депозитарное обслуживание, технический протокол, в офисе компании «Планета-Инвест». При себе необходимо иметь паспорт.</p>
			<p>Возможен выезд специалиста для подписания документов к клиенту (по предварительной заявке см.контакты)</p>
			<p>Подписанные брокером документы и пароль к созданию <a href="content.php?id=12">ЭЦП</a> будут отправлены Вам по почте на указанный в Анкете адрес. Время получения, ориентировочно, в течение двух недель.</p>
			<p>После подписания документов для оформления договора Вам на адрес электронной почты, указанной в анкете, вышлют пароль доступа к торговой системе и инструкцию.</p>';
			$retVal[0]=$form;
		}else if($HTTP_POST_VARS["action"]=="step4"){
		}else{
			$form="<h4>Шаг 1. Контактная информация.</h4><form method=post action=\"" . $theFormPrefix . "\"><input type=hidden name=action value=step1>";
			$form.="<table width=100% border=0 cellspacing=0 cellpadding=0>";
			$form.="<tr><td align=right>Фамилия:</td><td><input type=text class=text name=lastname size=30></td></tr>";
			$form.="<tr><td align=right>Имя:</td><td><input type=text class=text name=firstname size=30></td></tr>";
			$form.="<tr><td align=right>Отчество:</td><td><input type=text class=text name=surename size=30></td></tr>";
			$form.="<tr><td align=right>Телефон:</td><td><input type=text class=text name=phone size=30></td></tr>";
			$form.="<tr><td align=right>E-mail:</td><td><input type=text class=text name=email size=30></td></tr>";
			$form.="<tr><td></td><td align=right><input type=submit class=button value=\"Шаг 2 >>\"></td></tr>";
			$form.="</table></form>";
			$retVal[0]=$form;
		};
	    return $retVal;
	}


}

$theInvestModule=new clsInvestModule('invest','Заключение договора',$db);
$modsArray['invest']=$theInvestModule;
?>