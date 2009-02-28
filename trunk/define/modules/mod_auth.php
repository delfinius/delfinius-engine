<?php
class clsAuthModule extends clsStandAloneModule{
	function clsAuthModule($modName,$modDName,$dbconnector){
		global $SessionSettings;
	 	parent::clsStandAloneModule($modName,$modDName,$dbconnector);
		$this->version="1.0.0";
		$this->helpstring="<p>Модуль авторизации реализует разграничение доступа к разделам сайта различным пользователям, работу с личными сообщениями.</p>";

		$this->prms["moderemail"]=new ConfigParam("moderemail");
		$this->prms["moderemail"]->Description="Почтовый адрес модератора. Туда будут отправлятья уведомления о появлении новых заявок на регистрацию и прочее...";
		$this->prms["moderemail"]->DataType="char";
		$this->prms["moderemail"]->Value="delfin@extrim.it";
		$this->prms["moderemail"]->Protected=false;
	
		$this->prms["disableenter"]=new ConfigParam("disableenter");
		$this->prms["disableenter"]->Description="Запретить входить всем зарегистрированным пользователям. Иногда имеет смысл.";
		$this->prms["disableenter"]->DataType="bool";
		$this->prms["disableenter"]->Value=false;
		$this->prms["disableenter"]->Protected=false;

		$this->prms["disableentermessage"]=new ConfigParam("disableentermessage");
		$this->prms["disableentermessage"]->Description="Сообщение выводимое сейтом в случе если запрещён вход всем пользователям.";
		$this->prms["disableentermessage"]->DataType="memo";
		$this->prms["disableentermessage"]->Value="<p class=main>Извините, но по какой-то причине администратор сайта закрыл вход в систему всем пользователям.</p>";
		$this->prms["disableentermessage"]->Protected=false;
		
		$this->prms["AdminPageSize"]=new ConfigParam("AdminPageSize");
		$this->prms["AdminPageSize"]->Description="Количество отображаемых пользователей на странице модератора.";
		$this->prms["AdminPageSize"]->DataType='int';
		$this->prms["AdminPageSize"]->Value=50;
		$this->prms["AdminPageSize"]->Protected=true;

		$this->prms["NeedLoginPage"]=new ConfigParam("NeedLoginPage");
		$this->prms["NeedLoginPage"]->Description="html-код, который отображается при попытке неавторизованного пользователя просмотреть страницу, трубующую авторизации. Допускаемые для замены значения - globalenterform, twidth (ширина таблицы), registerlink (ссылка на страницу регистрации)";
		$this->prms["NeedLoginPage"]->DataType="memo";
		$this->prms["NeedLoginPage"]->Value="<table border=0 width=--twidth-- cellpadding=0 cellspacing=0>" .
			"<tr><td align=left bgcolor=#EAEAEA height=1><img src=format.gif border=0 height=1></td></tr>" . 
			"<tr><td align=left><p style=\"color:#9D0000;text-align:justify;\">Для просмотра данного раздела Вы должны быть авторизованы на сайте. Если Вы зарегистрированы, то введите свои учётные данные в форме ниже. В противном случае Вам необходимо пройти процедуру <a href=\"--registerlink--\">регистрации</a>.</td></tr>" . 
			"<tr><td>--globalenterform--</td></tr></table>";
		$this->prms["enterform"]->Protected=false;

		$this->prms["enterform"]=new ConfigParam("enterform");
		$this->prms["enterform"]->Description="Форма для входа пользователя, используемая в слоте \"авторизация\". Допускаемые для замены значения - form (&lt;form name=authenterform ... &gt;), twidth (ширина таблицы), registerlink (ссылка на страницу регистрации)";
		$this->prms["enterform"]->DataType="memo";
		$this->prms["enterform"]->Value="<table border=0 width=--twidth-- cellpadding=1 cellspacing=0>--form--" .
			"<tr><td class=data1 align=left style=\"color:#9D0000\">e-mail<br><input type=text class=text size=15 maxlength=200 name=email></td></tr>" . 
			"<tr><td class=data1 align=left style=\"color:#9D0000\">пароль<br><input type=password name=password size=15 class=text></td></tr>" . 
			"<tr><td class=data1 align=left><a href=\"javascript:document.forms['authenterform'].submit()\" title=\"войти в систему\">войти в систему</a><input type=submit style=\"width:0px;height:0px;\"><br>" . 
			"<a href=\"--registerlink--\" title=\"зарегистрироваться в системе\">зарегистрироваться</a></td></tr>" . 
			"</form></table>";
		$this->prms["enterform"]->Protected=false;


		$this->prms["globalenterform"]=new ConfigParam("globalenterform");
		$this->prms["globalenterform"]->Description="Форма для входа пользователя, используемая в страницах сайта. Допускаемые для замены значения - form (&lt;form name=authenterform2 ... &gt;), twidth (ширина таблицы), registerlink (ссылка на страницу регистрации)";
		$this->prms["globalenterform"]->DataType="memo";
		$this->prms["globalenterform"]->Value="<table border=0 cellpadding=0 cellspacing=4>--form--" . 
			"<tr><td align=left>e-mail<br><input type=text class=text size=25 maxlength=200 name=email></td>" . 
			"<td align=left>пароль<br><input type=password name=password size=25 class=text></td></tr>" . 
			"<tr><td class=data1 align=right colspan=2><a href=\"javascript:document.forms['authenterform2'].submit()\" title=\"войти в систему\">войти в систему</a><input type=submit style=\"width:0px;height:0px;\">" . 
			"</td></tr></table>";
		$this->prms["globalenterform"]->Protected=false;

		$this->prms["authorizedslot"]=new ConfigParam("authorizedslot");
		$this->prms["authorizedslot"]->Description="Форма для пользователя, зашедшего в систему. Допускаемые для замены значения - logoutform (&lt;form name=authlogoutform ... &gt;), twidth (ширина таблицы), regdatalink";
		$this->prms["authorizedslot"]->DataType="memo";
		$this->prms["authorizedslot"]->Value="<table border=0 width=--twidth-- cellpadding=0 cellspacing=0>--logoutform--" .
			"<tr><td class=data1 align=left><img src=format.gif border=0 height=3><br>Добрый день!<br><span style=\"color:#9D0000\">--username--</span><br></td></tr>" . 
			"<tr><td height=3><img src=format.gif border=0 height=3></td></tr>" . 
			"<tr><td height=1 bgcolor=#506E9F><img src=format.gif border=0 height=1></td></tr>" . 
//			"<tr><td class=data1 align=left>&nbsp;&nbsp;&nbsp;<a href=\"--pmlink--\">Личные сообщения</a><div align=right><div style=\"text-align:left;width:100px;font-size:11px;\"><span style=\"color:#9D0000;\">Новых: --pmunreaded--</span><br>Всего: --pmtotal--</div></div></td></tr>" . 
			"<tr><td class=data1 align=left>&nbsp;&nbsp;&nbsp;<a href=\"--regdatalink--\">Личные настройки</a></td></tr>" . 
			"<tr><td height=3><img src=format.gif border=0 height=3></td></tr>" . 
			"<tr><td height=1 bgcolor=#506E9F><img src=format.gif border=0 height=1></td></tr>" . 
			"<tr><td class=data1 align=center><img src=format.gif border=0 height=2><br><a href=\"javascript:document.forms['authlogoutform'].submit()\" title=\"выйти из системы\">выйти из системы</a></td></tr></form></table>";
		$this->prms["authorizedslot"]->Protected=false;

		$this->prms["errorlogin"]=new ConfigParam("errorlogin");
		$this->prms["errorlogin"]->Description="html-код отображаемый в случае неуспешной попытки авторизоваться на сайте.";
		$this->prms["errorlogin"]->DataType="memo";
		$this->prms["errorlogin"]->Value="<h4>Что-то не так!</h4>--enterform--";
		$this->prms["errorlogin"]->Protected=false;

		$this->prms["registerformtemplate"]=new ConfigParam("registerformtemplate");
		$this->prms["registerformtemplate"]->Description="Шаблон регистрационной формы. Допускаемые для замены поля: form (&lt;form name=registerform .... &gt;), itenslist (список полей формы)";
		$this->prms["registerformtemplate"]->DataType="memo";
		$this->prms["registerformtemplate"]->Value="<p class=main>Добро пожаловать... бла бла бла.... введите регистрационные данные.</p><center><table border=0 cellpadding=0 cellspacing=4>--form-- --itemslist--<tr><td colspan=2 align=center><input type=submit class=button value=\"отправить заявку\"></td></tr></form></table>";
		$this->prms["registerformtemplate"]->Protected=false;

		$this->prms["userinfoformtemplate"]=new ConfigParam("userinfoformtemplate");
		$this->prms["userinfoformtemplate"]->Description="Шаблон формы для изменения данных пользователя. Допускаемые для замены поля: form (&lt;form name=registerform .... &gt;), itenslist (список полей формы)";
		$this->prms["userinfoformtemplate"]->DataType="memo";
		$this->prms["userinfoformtemplate"]->Value="<p class=main>Тут вы можете поменять некоторые свои регистрационные данные.</p><center><table border=0 cellpadding=0 cellspacing=4>--form-- --itemslist--<tr><td colspan=2 align=center><input type=submit class=button value=\"обновить\"></td></tr></form></table>";
		$this->prms["userinfoformtemplate"]->Protected=false;

		$this->prms["registerformitem"]=new ConfigParam("registerformitem");
		$this->prms["registerformitem"]->Description="Шаблон одного поля регистрационной формы. Допускаемые для замены значения - fieldname, fieldedit .";
		$this->prms["registerformitem"]->DataType="memo";
		$this->prms["registerformitem"]->Value="<tr><td align=right valign=top style=\"color:#9D0000\">--fieldname--:</td><td align=left valign=top>--fieldedit--</td></tr>";
		$this->prms["registerformitem"]->Protected=false;
	
		$this->prms["registerformdevider"]=new ConfigParam("registerformdevider");
		$this->prms["registerformdevider"]->Description="Разделитель полей формы";
		$this->prms["registerformdevider"]->DataType="char";
		$this->prms["registerformdevider"]->Value="";
		$this->prms["registerformdevider"]->Protected=false;
	
		$this->prms["reg.error.already"]=new ConfigParam("reg.error.already");
		$this->prms["reg.error.already"]->Description="Сообщение системы на повторную попытку регистрации";
		$this->prms["reg.error.already"]->DataType="memo";
		$this->prms["reg.error.already"]->Value="<h4>Извините.</h4><p class=main>Ваш запрос на регистрацию не может быть принят так как в системе уже зарегистрирован (или находится в стадии регистрации) пользователь с указанным адресом электронной почты.</p>";
		$this->prms["reg.error.already"]->Protected=false;
	
		$this->prms["reg.success"]=new ConfigParam("reg.success");
		$this->prms["reg.success"]->Description="Сообщение об успешном приёме заявки на регистрацию";
		$this->prms["reg.success"]->DataType="memo";
		$this->prms["reg.success"]->Value="<h4>Поздравляем!</h4><p class=main>Ваш запрос на регистрацию принят системой. Вас уведомят по электронной почте о принятом решении по поводу Вашей регистрации</p>";
		$this->prms["reg.success"]->Protected=false;

		$this->prms["reg.emailmessage"]=new ConfigParam("reg.emailmessage");
		$this->prms["reg.emailmessage"]->Description="Сообщение отправляемое пользователю в случае его успешной регистрации модератором. Допускаемые для замены значения - password, email";
		$this->prms["reg.emailmessage"]->DataType="memo";
		$this->prms["reg.emailmessage"]->Value="Ваша заявка на регистрацию в системе рассмотрена положительно.\nВаш e-mail: --email--\nВаш пароль: --password--";
		$this->prms["reg.emailmessage"]->Protected=false;

		$this->prms["pm.listing.pagesize"]=new ConfigParam("pm.listing.pagesize");
		$this->prms["pm.listing.pagesize"]->Description="Количество отображаемых личных сообщений на одной странице";
		$this->prms["pm.listing.pagesize"]->DataType="int";
		$this->prms["pm.listing.pagesize"]->Value=20;
		$this->prms["pm.listing.pagesize"]->Protected=false;
	
		$this->prms["pm.listing.pager"]=new ConfigParam("pm.listing.pager");
		$this->prms["pm.listing.pager"]->Description="Шаблон отображения списка страниц. Заменяет pager в шаблонах списков сообщения. Допускаемое для замены значение: pageslist";
		$this->prms["pm.listing.pager"]->DataType="char";
		$this->prms["pm.listing.pager"]->Value="страница:&nbsp;--pageslist--";
		$this->prms["pm.listing.pager"]->Protected=false;

		$this->prms["pm.listing.pager.active"]=new ConfigParam("pm.listing.pager.active");
		$this->prms["pm.listing.pager.active"]->Description="Шаблон ссылки на текущую страницу списка. Допускаемые для замены значения: link, pagenum";
		$this->prms["pm.listing.pager.active"]->DataType="char";
		$this->prms["pm.listing.pager.active"]->Value="<a href=\"--link--\" style=\"color:red;\">--pagenum--</a>";
		$this->prms["pm.listing.pager.active"]->Protected=false;
	
		$this->prms["pm.listing.pager.inactive"]=new ConfigParam("pm.listing.pager.inactive");
		$this->prms["pm.listing.pager.inactive"]->Description="Шаблон ссылки на страницу списка. Допускаемые для замены значения: link, pagenum";
		$this->prms["pm.listing.pager.inactive"]->DataType="char";
		$this->prms["pm.listing.pager.inactive"]->Value="<a href=\"--link--\">--pagenum--</a>";
		$this->prms["pm.listing.pager.inactive"]->Protected=false;

		$this->prms["pm.listing.pager.devider"]=new ConfigParam("pm.listing.pager.devider");
		$this->prms["pm.listing.pager.devider"]->Description="Символы-разделители ссылок на страницы";
		$this->prms["pm.listing.pager.devider"]->DataType="char";
		$this->prms["pm.listing.pager.devider"]->Value="&nbsp;&nbsp;";
		$this->prms["pm.listing.pager.devider"]->Protected=false;
	
		$this->prms["pm.inbox.listing"]=new ConfigParam("pm.inbox.listing");
		$this->prms["pm.inbox.listing"]->Description="Шаблон вывода списка входящих личных сообщений. Допускаемые для замены значения - itemslist, total, unreaded, pager, deleteitemslink, form (&lt;form name=pmlistingform .... &gt;)";
		$this->prms["pm.inbox.listing"]->DataType="memo";
		$this->prms["pm.inbox.listing"]->Value="<table width=100% border=0 cellpadding=0 cellspacing=0><tr><td class=colheader>&nbsp;</td><td class=colheader align=center>тема</td>" .
							"<td class=colheader align=center>отправитель</td><td class=colheader align=center>дата</td></tr>--form--" .
							"--itemslist--" .
							"<tr><td class=colheader>&nbsp;</td><td class=colheader><a style=\"color:white;font-weight:normal;\" href=\"--deleteitemslink--\">удалить отмеченные</a></td><td class=colheader colspan=2 align=right>--pager--</td></tr>" .
							"</form></table>";
		$this->prms["pm.inbox.listing"]->Protected=false;

		$this->prms["pm.inbox.listing.item"]=new ConfigParam("pm.inbox.listing.item");
		$this->prms["pm.inbox.listing.item"]->Description="Шаблон вывода одного сообщения в списке входящих личных сообщений. Допускаемые для замены значения - subject, message, from, to, readhref, deletehref, date, deletecheckbox";
		$this->prms["pm.inbox.listing.item"]->DataType="memo";
		$this->prms["pm.inbox.listing.item"]->Value="<tr><td align=center>--deletecheckbox--</td>".
							"<td align=left><a href=\"--readhref--\">--subject--</a></td>" .
							"<td align=center>--from--</td>" .
							"<td align=center>--date--</td>" .
							"</tr>";
		$this->prms["pm.inbox.listing.item"]->Protected=false;
	
		$this->prms["pm.outbox.listing.devider"]=new ConfigParam("pm.outbox.listing.devider");
		$this->prms["pm.outbox.listing.devider"]->Description="Шаблон вывода списка отправленных личных сообщений. Допускаемые для замены значения - subject, text, from, to, readhref, deletehref, date, deletecheckbox";
		$this->prms["pm.outbox.listing.devider"]->DataType="char";
		$this->prms["pm.outbox.listing.devider"]->Value="";
		$this->prms["pm.outbox.listing.devider"]->Protected=false;

		$this->prms["pm.outbox.listing"]=new ConfigParam("pm.outbox.listing");
		$this->prms["pm.outbox.listing"]->Description="Шаблон вывода списка отправленных личных сообщений. Допускаемые для замены значения - itemslist, total, unreaded, pager, deleteitemslink, form (&lt;form name=pmlistingform .... &gt;)";
		$this->prms["pm.outbox.listing"]->DataType="memo";
		$this->prms["pm.outbox.listing"]->Value="<table width=100% border=0 cellpadding=0 cellspacing=0><tr><td class=colheader>&nbsp;</td><td class=colheader align=center>тема</td>" .
							"<td class=colheader align=center>получаель</td><td class=colheader align=center>дата</td></tr>--form--" .
							"--itemslist--" .
							"<tr><td class=colheader>&nbsp;</td><td class=colheader style=\"font-weight:normal;\"><a class=header href=\"--deleteitemslink--\">удалить отмеченные</a></td><td class=colheader colspan=2 align=right>--pager--</td></tr>" .
							"</form></table>";
		$this->prms["pm.outbox.listing"]->Protected=false;

		$this->prms["pm.outbox.listing.item"]=new ConfigParam("pm.outbox.listing.item");
		$this->prms["pm.outbox.listing.item"]->Description="Шаблон вывода одного сообщения в списке отправленных личных сообщений. Допускаемые для замены значения - subject, message, from, to, readhref, deletehref, date, deletecheckbox";
		$this->prms["pm.outbox.listing.item"]->DataType="memo";
		$this->prms["pm.outbox.listing.item"]->Value="<tr><td align=center>--deletecheckbox--</td>".
							"<td align=left><a href=\"--readhref--\">--subject--</a></td>" .
							"<td align=center>--to--</td>" .
							"<td align=center>--date--</td>" .
							"</tr>";
		$this->prms["pm.outbox.listing.item"]->Protected=false;
	
		$this->prms["pm.outbox.listing.devider"]=new ConfigParam("pm.outbox.listing.devider");
		$this->prms["pm.outbox.listing.devider"]->Description="Шаблон вывода списка отправленных личных сообщений. Допускаемые для замены значения - subject, text, from, to, readhref, deletehref, date, deletecheckbox";
		$this->prms["pm.outbox.listing.devider"]->DataType="char";
		$this->prms["pm.outbox.listing.devider"]->Value="";
		$this->prms["pm.outbox.listing.devider"]->Protected=false;

		$this->prms["pm.inbox.message.read"]=new ConfigParam("pm.inbox.message.read");
		$this->prms["pm.inbox.message.read"]->Description="Шаблон просмотра одного сообщения в папке входящие. Допускаемые для замены значения - subject, text, from, to, date, deletehref, replyhref, backhref";
		$this->prms["pm.inbox.message.read"]->DataType="memo";
		$this->prms["pm.inbox.message.read"]->Value="<table width=100% border=0 cellpadding=0 cellspacing=0>".
								"<tr><td class=colheader height=17 style=\"font-weight:normal;\">&nbsp;<a style=\"font-weight:normal;color:white;\" href=\"--replyhref--\">Написать ответ</a> | <a style=\"font-weight:normal;color:white;\" href=\"--deletehref--\">Удалить сообщение</a> | <a style=\"font-weight:normal;color:white;\" href=\"--backhref--\">Вернуться к списку</a></td></tr>" .
								"<tr><td>" .
								"<table width=100% border=0 cellpadding=1 cellspacing=1><tr><td width=15% style=\"font-size:12px;color:#989898\" align=right>тема:&nbsp;</td><td style=\"color:#506E9F\">--subject--</td></tr><tr><td style=\"font-size:12px;color:#989898\" align=right>отправитель:&nbsp;</td><td style=\"color:#506E9F\">--from--</td></tr><tr><td style=\"font-size:12px;color:#989898\" align=right>дата:&nbsp;</td><td style=\"color:#506E9F\">--date--</td></tr></table>" .
								"</td></tr>" .
								"<tr><td height=1 bgcolor=#CECECE><img src=format.gif border=0 height=1></td></tr>" .
								"<tr><td height=3><img src=format.gif border=0 height=3></td></tr>" .
								"<tr><td style=\"color:#506E9F\">--message--</td></tr>" .
								"</table>";
		$this->prms["pm.inbox.message.read"]->Protected=false;

		$this->prms["pm.outbox.message.read"]=new ConfigParam("pm.outbox.message.read");
		$this->prms["pm.outbox.message.read"]->Description="Шаблон просмотра одного сообщения в папке отправленные. Допускаемые для замены значения - subject, text, from, to, date, deletehref, replyhref, backhref";
		$this->prms["pm.outbox.message.read"]->DataType="memo";
		$this->prms["pm.outbox.message.read"]->Value="<table width=100% border=0 cellpadding=0 cellspacing=0>".
								"<tr><td class=colheader height=17 style=\"font-weight:normal;\">&nbsp;<a style=\"font-weight:normal;color:white;\" href=\"--replyhref--\">Написать ответ</a> | <a style=\"font-weight:normal;color:white;\" href=\"--deletehref--\">Удалить сообщение</a> | <a style=\"font-weight:normal;color:white;\" href=\"--backhref--\">Вернуться к списку</a></td></tr>" .
								"<tr><td>" .
								"<table width=100% border=0 cellpadding=1 cellspacing=1><tr><td width=15% style=\"font-size:12px;color:#989898\" align=right>тема:&nbsp;</td><td style=\"color:#506E9F\">--subject--</td></tr><tr><td style=\"font-size:12px;color:#989898\" align=right>получатель:&nbsp;</td><td style=\"color:#506E9F\">--to--</td></tr><tr><td style=\"font-size:12px;color:#989898\" align=right>дата:&nbsp;</td><td style=\"color:#506E9F\">--date--</td></tr></table>" .
								"</td></tr>" .
								"<tr><td height=1 bgcolor=#CECECE><img src=format.gif border=0 height=1></td></tr>" .
								"<tr><td height=3><img src=format.gif border=0 height=3></td></tr>" .
								"<tr><td style=\"color:#506E9F\">--message--</td></tr>" .
								"</table>";
		$this->prms["pm.outbox.message.read"]->Protected=false;

		$this->prms["DateFormat"]=new ConfigParam("DateFormat");
		$this->prms["DateFormat"]->Description="Формат вывода дат. (http://www.php.net/manual/en/function.date.php)";
		$this->prms["DateFormat"]->DataType='char';
		$this->prms["DateFormat"]->Value="d.m.Y";
		$this->prms["DateFormat"]->Protected=false;

		$this->usersTable="mod_auth_users";
		$this->usersNeedsTable="mod_auth_needle";
		$this->regdataTable="mod_auth_users_info";
		$this->pmTable="mod_auth_pms";
		$this->enterLogTable="mod_auth_log";
		$this->interfaceScript="/auth.php";

		$this->RegTemplateInited=false;
		$this->RegTemplate=array();
		$this->AbleFieldTypes=array("bool"=>"Булево",
				"memo"=>"Длинный текст",
				"char"=>"Строка (250 символов)",
				"datetime"=>"Дата",
				"int"=>"Целочисленное",
				"float"=>"Десятичное"
			);
		$this->authorized=($SessionSettings["siteuserid"]>0);
		$this->inboxinfo=array();
	}

	function RegDataTemplate(){
		if($this->RegTemplateInited)return $this->RegTemplate;
		$sql="select `id`, `fname`, `dname`, `description`, `req`, `datatype`, `sort` from `" .$this->usersNeedsTable . "` order by `sort`";
		$this->dbc->sql_query($sql);
		while($row=$this->dbc->sql_fetchrow()){
			$this->RegTemplate[$row["fname"]]=$row;
		};
		$this->RegTemplateInited=true;
		return $this->RegTemplate;
	}

	function ClientScript($theFormPrefix, $thePage=1){
		$LocalthePage=$thePage;
	  	$retVal='';
		$ModifedFormPrefix=substr($theFormPrefix,0,strlen($theFormPrefix)-1);
		$ModifedFormPrefix.=" name=\"mod_auth_action_form\">";
		$retVal.=$ModifedFormPrefix . "<input type=hidden name=mod_auth_action value=\"\"><input type=hidden name=id value=0><input type=hidden name=page value=$LocalthePage><input type=hidden name=param1 value=\"\"><input type=hidden name=param2 value=\"\"></form>";
		$retVal.="<script>";
		$retVal.="function mod_action(theAction,thePage,theParam1,theParam2){";
		$retVal.="document.forms['mod_auth_action_form'].mod_auth_action.value=theAction;\n";
		$retVal.="if(thePage)document.forms['mod_auth_action_form'].page.value=thePage;";
		$retVal.="if(theParam1)document.forms['mod_auth_action_form'].param1.value=theParam1;";
		$retVal.="if(theParam2)document.forms['mod_auth_action_form'].param2.value=theParam2;";
		$retVal.="document.forms['mod_auth_action_form'].submit();\n";
		$retVal.="};";
		$retVal.="</script>";
		return $retVal;
	}

	function GetUserData($userID){
		$retVal=$this->RegDataTemplate();
		$sql="select `user`, `field`, `bool`, `memo`, `char`, `datetime`, `int`, `float` from `" . $this->regdataTable . "` where `user`=$userID";
		$this->dbc->sql_query($sql);
		while($row=$this->dbc->sql_fetchrow()){
			if(isset($retVal[$row["field"]])){
				$retVal[$row["field"]]["value"]=$row[$retVal[$row["field"]]["datatype"]];
			};
		};
		return $retVal;
	}

	function UpdateUserField($theField,$theCollection,$inputName,$userID){
		$retVal="";
		switch($theField["datatype"]){
			case "float":{
				$valStr=str_replace(",",".",$theCollection[$inputName]);
				$retVal.=$valStr;
				break;
			};
			case "int":{
				$valStr=str_replace(",","",$theCollection[$inputName]);
				$retVal.=$valStr;
				break;
			};
			case "datetime":{
				$valStr=PostToDate($inputName);
				$retVal.=date("d.m.Y",$valStr);
				break;
			};
			case "bool":{
				$valStr=($theCollection[$inputName]=="on")?1:0;
				$retVal.=($valStr==1)?"ДА":"НЕТ";
				break;
			};
			default:{
				$valStr="'" . $theCollection[$inputName] . "'";
				$retVal.=stripslashes($theCollection[$inputName]);
				break;
			};
		};
		$sql="select `user`, `field` from `" . $this->regdataTable . "` where `user`=$userID and `field`='" . $theField["fname"] . "'";
		$this->dbc->sql_query($sql);
		if($this->dbc->sql_numrows()>0){
			$sql="update `" . $this->regdataTable . "` set `" . $theField["datatype"] . "`=$valStr where `user`=$userID and `field`='" . $theField["fname"] . "'";
			$this->dbc->sql_query($sql);
		}else{
			$sql="insert into `" . $this->regdataTable . "` (`user`,`field`,`" . $theField["datatype"] . "`) values($userID,'" . $theField["fname"] . "',$valStr)";
			$this->dbc->sql_query($sql);
		};
		return $retVal;
	}

	function FieldEditor($theField,$inputName){
		$retVal="";
		if(!isset($theField["value"]))$theField["value"]="";
		switch($theField["datatype"]){
			case "memo":{
				$retVal="<textarea name=\"$inputName\" cols=40 rows=6>" . CutQuots($theField["value"]) . "</textarea>";
				break;
			};
			case "float":{
				$retVal="<input type=text size=30 style=\"text-align:right;\" class=text name=\"$inputName\" value=\"" . fmtFloat($theField["value"]) . "\">";
				break;
			};
			case "int":{
				$retVal="<input type=text size=30 style=\"text-align:right;\" class=text name=\"$inputName\" value=\"" . $theField["value"] . "\">";
				break;
			};
			case "datetime":{
				$retVal=DatePicker($inputName,$theField["value"]);
				break;
			};
			case "bool":{
				$checked=($theField["value"]==1)?" checked":"";
				$retVal="<input type=checkbox name=\"$inputName\" $checked>";
				break;
			};
			default:{
				$retVal="<input type=text size=40 class=text name=\"$inputName\" value=\"" . CutQuots($theField["value"]) . "\">";
				break;
			};
		};
		return $retVal;
	}

	function ClientFieldEditor($theField,$inputName){
		$retVal="";
		if(!isset($theField["value"]))
			if($theField["datatype"]=="datetime"){
				$theField["value"]=time();
			}else{
				$theField["value"]="";
			};
		switch($theField["datatype"]){
			case "memo":{
				$retVal="<textarea name=\"$inputName\" cols=40 rows=6>" . CutQuots($theField["value"]) . "</textarea>";
				break;
			};
			case "float":{
				$retVal="<input type=text size=30 style=\"text-align:right;\" class=text name=\"$inputName\" value=\"" . fmtFloat($theField["value"]) . "\">";
				break;
			};
			case "int":{
				$retVal="<input type=text size=30 style=\"text-align:right;\" class=text name=\"$inputName\" value=\"" . $theField["value"] . "\">";
				break;
			};
			case "datetime":{
				$retVal=DatePickerWOT($inputName,$theField["value"]);
				break;
			};
			case "bool":{
				$checked=($theField["value"]==1)?" checked":"";
				$retVal="<input type=checkbox name=\"$inputName\" $checked>";
				break;
			};
			default:{
				$retVal="<input type=text size=40 class=text name=\"$inputName\" value=\"" . CutQuots($theField["value"]) . "\">";
				break;
			};
		};
		return $retVal;
	}

	function MakeParamsOuput($theFormPrefix){
		global $HTTP_POST_VARS;
		$pageNum=$HTTP_POST_VARS["page"];
		$pageNum=($pageNum>0)?$pageNum:1;
		$retVal=drwTableBegin("100%","");
		$retVal.=$this->ClientScript($theFormPrefix,$pageNum);
		$retVal.="<tr><td class=colheader align=center><a class=header href=\"javascript:mod_action('showorders',1,0)\">заявки на регистрацию</a></td><td class=colheader align=center><a class=header href=\"javascript:mod_action('showregistered',1,0)\">все пользователи</a></td><td class=colheader align=center><a class=header href=\"javascript:mod_action('showbanned',1,0)\">отключенные</a></td></tr>";
		$retVal.="<tr><td class=colheader align=center><a class=header href=\"javascript:mod_action('regfields',1,0)\">поля регистрации</a></td></tr>";
		$retVal.=drwTableEnd();
		$mod_action=$HTTP_POST_VARS["mod_auth_action"];
		if(($mod_action=="showallusers")||($mod_action=="showorders")||($mod_action=="showregistered")||($mod_action=="showbanned")){
			$retVal.=$this->ShowAdminUsersList($mod_action,$pageNum);
		};
		if($mod_action=="userinfo"){
			$retVal.=$this->ShowAdminUserInfo($theFormPrefix, $HTTP_POST_VARS["param1"]);
		};
		if($mod_action=="regfields"){
			$retVal.=$this->UsersFieldsShow($theFormPrefix);
		};
		return $retVal;
	}

	function UsersFieldsShow($theFormPrefix){
		global $HTTP_POST_VARS;
		$retVal="";
		$subaction=$HTTP_POST_VARS["param1"];
		if($subaction=="newfield"){
			$sql="insert into `" .$this->usersNeedsTable . "` (`fname`,`req`) values ('unnamed',0)";
			$this->dbc->sql_query($sql);
			$HTTP_POST_VARS["param2"]=$this->dbc->sql_nextid();
			$subaction="regfields_edit";
		};
		if($subaction=="regfields_update"){
			$req=($HTTP_POST_VARS["req"]=="on")?1:0;
			if($HTTP_POST_VARS["delete"]!="on"){
				$sql="update `" .$this->usersNeedsTable . "` set `fname`='" . $HTTP_POST_VARS["fname"] . "', `dname`='" . $HTTP_POST_VARS["dname"] . "', `description`='" . $HTTP_POST_VARS["description"] . "', `req`=$req, `datatype`='" . $HTTP_POST_VARS["datatype"] . "', `sort`='" . $HTTP_POST_VARS["sort"] . "' where `id`=" . $HTTP_POST_VARS["param2"];
				$subaction="regfields_edit";
			}else{
				$sql="delete from `" .$this->usersNeedsTable . "` where `id`=" . $HTTP_POST_VARS["param2"];
			};
			$this->dbc->sql_query($sql);
		};
		if($subaction=="regfields_edit"){
			$sql="select `id`, `fname`, `dname`, `description`, `req`, `datatype`, `sort` from `" .$this->usersNeedsTable . "` where `id`=" . $HTTP_POST_VARS["param2"];
			$this->dbc->sql_query($sql);
			if($row=$this->dbc->sql_fetchrow()){
				$retVal.="<br>" . drwTableBegin('100%','');
				$retVal.=$theFormPrefix . "<input type=hidden name=mod_auth_action value=regfields><input type=hidden name=param1 value=regfields_update><input type=hidden name=param2 value=" . $row["id"] .">";
				$retVal.="<tr><td class=colheader colspan=2 align=center>обновить информацию о поле</td></tr>";
				$retVal.="<tr><td class=data1 align=right>название:</td><td class=data1 align=left><input type=text class=text name=fname size=40 value=\"" . CutQuots($row["fname"]) . "\"></td></tr>";
				$retVal.="<tr><td class=data2 align=right>выводимое название:</td><td class=data2 align=left><input type=text class=text name=dname size=40 value=\"" . CutQuots($row["dname"]) . "\"></td></tr>";
				$retVal.="<tr><td class=data1 align=right valign=top>описание:</td><td class=data1 align=left><textarea name=description cols=40 rows=6>" . CutQuots($row["description"]) . "</textarea></td></tr>";
				$checked=($row["req"]==1)?" checked":"";
				$retVal.="<tr><td class=data2 align=right>обязательное:</td><td class=data2 align=left><input type=checkbox name=req$checked></td></tr>";
				$retVal.="<tr><td class=data1 align=right>тип данных:</td><td class=data1 align=left><select name=datatype>";
				foreach($this->AbleFieldTypes as $typeKey => $typeName){
					$selected=($row["datatype"]==$typeKey)?" selected":"";
					$retVal.="<option value=\"$typeKey\"$selected>" . CutQuots($typeName);
				};
				$retVal.="</select></td></tr>";
				$retVal.="<tr><td class=data2 align=right>сортировка:</td><td class=data2 align=left><input type=text class=text name=sort value=\"" . $row["sort"] . "\"></td></tr>";
				$retVal.="<tr><td class=data1 colspan=2 align=center><input type=checkbox name=delete> - удалить&nbsp;&nbsp;&nbsp;<input type=submit class=button value=\"обновить\"></td></tr>";
				$retVal.="</form>" . drwTableEnd();
			};
		};
		$retVal.="<br>" . drwTableBegin('100%','');
		$retVal.="<tr><td class=colheader colspan=5 align=center>Поля данных пользователей</td></tr>";
		$retVal.="<tr>";
		$retVal.="<td class=frm_data1 align=center>название</td>";
		$retVal.="<td class=frm_data1 align=center>описание</td>";
		$retVal.="<td class=frm_data1 align=center>обязательное</td>";
		$retVal.="<td class=frm_data1 align=center>тип данных</td>";
		$retVal.="<td class=frm_data1 align=center>сортировка</td>";
		$retVal.="</tr>";
		$sql="select `id`, `fname`, `dname`, `description`, `req`, `datatype`, `sort` from `" .$this->usersNeedsTable . "` order by `sort`";
		$this->dbc->sql_query($sql);
		$tdclass="data1";
		while($row=$this->dbc->sql_fetchrow()){
			$req=($row["req"]==1)?"ДА":"НЕТ";
			$retVal.="<tr>";
			$retVal.="<td class=$tdclass align=left><a href=\"javascript:mod_action('regfields',1,'regfields_edit'," . $row["id"] . ")\">" . CutQuots($row["fname"]) . " (" . CutQuots($row["dname"]) . ")</a></td>";
			$retVal.="<td class=$tdclass align=left>" . CutQuots($row["description"]) . "</td>";
			$retVal.="<td class=$tdclass align=left>" . $req . "</td>";
			$retVal.="<td class=$tdclass align=left>" . $this->AbleFieldTypes[$row["datatype"]] . "</td>";
			$retVal.="<td class=$tdclass align=left>" . $row["sort"] . "</td>";
			$tdclass=($tdclass=="data1")?"data2":"data1";
		};
		$retVal.=$theFormPrefix . "<input type=hidden name=mod_auth_action value=regfields><input type=hidden name=param1 value=\"newfield\"><input type=hidden name=param2 value=2>";
		$retVal.="<tr><td class=$tdclass colspan=5 align=center><input type=submit class=button value=\"добавить поле\"></td></tr>";
		$retVal.="</form>";
		$retVal.=drwTableEnd();
		return $retVal;
	}


	function ShowAdminUserInfo($theFormPrefix, $userID){
		global $HTTP_POST_VARS, $SiteMainURL;
		$retVal="";
		$subaction=$HTTP_POST_VARS["param2"];
		if($subaction=="update_maininfo"){
			if($HTTP_POST_VARS["delete"]=="on"){
				$sql="delete from `" . $this->usersTable . "` where `id`=" . $userID;
				$this->dbc->sql_query($sql);
			}else{
				$enabled=($HTTP_POST_VARS["enabled"]=="on")?1:0;
				$registered=($HTTP_POST_VARS["registered"]=="on")?1:0;
				$sql="select `registered` from `" . $this->usersTable . "` where `id`=" . $userID;
				$this->dbc->sql_query($sql);
				$alreadyreg=$this->dbc->sql_fetchfield("registered");
				if(($registered==1)&&($alreadyreg!=1)){
					$mailheader="MIME-Version: 1.0\nContent-type: text/plain; charset=windows-1251\nDate: " . gmdate("D, d M Y H:i:s", time()) . " UT\nX-Priority: 3\nX-MSMail-Priority: Normal\nX-Mailer: PHP";
					mail(stripslashes($HTTP_POST_VARS["email"]),"$SiteMainURL :: success registration",stripslashes($HTTP_POST_VARS["mailmessage"]),"$mailheader\nfrom:$SiteMainURL <webmaster@$SiteMainURL>");
				};
				$sql="update `" . $this->usersTable . "` set `displayname`='" . $HTTP_POST_VARS["displayname"] . "', `email`='" . $HTTP_POST_VARS["email"] . "', `password`='" . $HTTP_POST_VARS["password"] . "', `enabled`=$enabled, `adminnote`='" . $HTTP_POST_VARS["adminnote"] . "', `registered`=$registered where `id`=" . $userID;
				$this->dbc->sql_query($sql);
			};
		};
		if($subaction=="update_subinfo"){
			$UserTemplate=$this->RegDataTemplate();
			foreach($UserTemplate as $aField){
				$this->UpdateUserField($aField,$HTTP_POST_VARS,"spec_" . $aField["fname"],$userID);
			};
		};
		$sql="select u.`id`, u.`displayname`, u.`email`, u.`password`, u.`regdate`, u.`regip`, u.`enabled`, u.`adminnote`, u.`registered` from `" . $this->usersTable . "` as u where u.`id`=" . $userID;
		if(!$this->dbc->sql_query($sql)){
			$sqlerror=$this->dbc->sql_error();
			die($sqlerror["message"]);
		};
		if(!($row=$this->dbc->sql_fetchrow()))return $retVal;
		$allData=$this->GetUserData($userID);
		$retVal.=drwTableBegin("100%","");
		$retVal.="<tr><td class=colheader colspan=2 align=center>стандартные данные о пользователе</td></tr>";
		$retVal.=$theFormPrefix . "<input type=hidden name=mod_auth_action value=userinfo><input type=hidden name=param1 value=$userID><input type=hidden name=param2 value=update_maininfo>";
		$retVal.="<tr><td class=data1 align=right>Выводимое имя:</td><td class=data1 align=left><input type=text class=text size=40 name=displayname value=\"" . CutQuots($row["displayname"]) . "\"></td></tr>";
		$retVal.="<tr><td class=data2 align=right>E-mail:</td><td class=data2 align=left><input type=text class=text size=40 name=email value=\"" . CutQuots($row["email"]) . "\"></td></tr>";
		$retVal.="<tr><td class=data1 align=right>Пароль:</td><td class=data1 align=left><input type=text class=text size=40 name=password value=\"" . CutQuots($row["password"]) . "\"></td></tr>";
		$retVal.="<tr><td class=data2 align=right>Дата регистрации:</td><td class=data2 align=left>" . date("d.m.Y H:i",$row["regdate"]) . "</td></tr>";
		$retVal.="<tr><td class=data1 align=right>IP-адрес регистрации:</td><td class=data2 align=left>" . $row["regip"] . "</td></tr>";
		$checked=($row["enabled"]==1)?" checked":"";
		$retVal.="<tr><td class=data2 align=right>Разрешить вход:</td><td class=data2 align=left><input type=checkbox name=enabled $checked></td></tr>";
		$retVal.="<tr><td class=data1 align=right valign=top>Заметки:</td><td class=data2 align=left><textarea name=adminnote cols=40 rows=7>" . $row["adminnote"] . "</textarea></td></tr>";
		if($row["registered"]!=1){
			$mmess=$this->prms["reg.emailmessage"]->Value;
			$mmess=str_replace("--email--",CutQuots($row["email"]),$mmess);
			$mmess=str_replace("--password--",CutQuots($row["password"]),$mmess);
			$retVal.="<tr><td class=data2 align=right>Зарегистрировать:</td><td class=data2 align=left><input type=checkbox name=registered></td></tr>";
			$retVal.="<tr><td class=data1 align=right>Отклонить заявку:</td><td class=data1 align=left><input type=checkbox name=delete></td></tr>";
			$retVal.="<tr><td class=data2 align=center colspan=2>Текст уведомления о положительной регистрации по электронной почте:</td></tr>";
			$retVal.="<tr><td class=data2 align=center colspan=2><textarea name=mailmessage cols=60 rows=6>$mmess</textarea></td></tr>";
		}else{
			$retVal.="<input type=hidden name=registered value=\"on\">";
		};
		$retVal.="<tr><td class=data1 colspan=2 align=center><input type=submit class=button value=\"обновить\"></td></tr>";
		$retVal.="</form>" . drwTableEnd() . "<br>";
		if(count($allData)>0){
			$retVal.=drwTableBegin("100%","");
			$retVal.="<tr><td class=colheader colspan=2 align=center>расширенные данные о пользователе</td></tr>";
			$retVal.=$theFormPrefix  . "<input type=hidden name=mod_auth_action value=userinfo><input type=hidden name=param1 value=$userID><input type=hidden name=param2 value=update_subinfo>";
			$tdclass="data1";
			foreach($allData as $fKey => $fData){
				$feditor="";
				$retVal.="<tr><td class=$tdclass align=right valign=top>" . CutQuots($fData["dname"]) . ": </td>";
				$retVal.="<td class=$tdclass>" . $this->FieldEditor($fData,"spec_" . $fData["fname"]) . "</td></tr>";
				$tdclass=($tdclass=="data1")?"data2":"data1";
			};
			$retVal.="<tr><td class=data1 colspan=2 align=center><input type=submit class=button value=\"обновить\"></td></tr>";
			$retVal.="</form>" . drwTableEnd() . "<br>";
		};
		return $retVal;
	}

	function ShowAdminUsersList($filter,$PageNum){
		$retVal=drwTableBegin("100%","");
		$retVal.="<tr><td class=colheader align=center>отображаемое имя</td>";
		$retVal.="<td class=colheader align=center>e-mail</td>";
		$retVal.="<td class=colheader align=center>статус</td>";
		$retVal.="<td class=colheader align=center>дата заявки</td>";
		$retVal.="</tr>";
		$whereclause="";
		if($filter=="showorders"){
			$whereclause=" where u.`registered`=0";
		};
		if($filter=="showbanned"){
			$whereclause=" where u.`registered`=1 and u.`enabled`=0";
		};
		if($filter=="showregistered"){
			$whereclause=" where u.`registered`=1";
		};
		$sql="select u.`id`, u.`displayname`, u.`email`, u.`password`, u.`regdate`, u.`regip`, u.`enabled`, u.`adminnote`, u.`registered` from `" . $this->usersTable . "` as u $whereclause order by u.`displayname`";
		if(!$this->dbc->sql_query($sql)){
			$sqlerror=$this->dbc->sql_error();
			die($sqlerror["message"]);
		};
		$tdclass="data1";
		$PageSize=$this->prms["AdminPageSize"]->Value;
		$counter=0;
		while($row=$this->dbc->sql_fetchrow()){
			$counter++;
			$InPage=(($counter>(($PageNum-1)*$PageSize))&&($counter<=(($PageNum)*$PageSize)));
			if($InPage){
				$rowstyle="";
				$userstate="доступ открыт";
				$regdate=date("d.m.Y",$row["regdate"]);
				$admnote=CutQuots($row["adminnote"]);
				if($row["enabled"]==0){
					$rowstyle="style=\"background-color:gray;color:white;\"";
					$userstate="доступ закрыт";
				};
				if($row["registered"]==0){
					$rowstyle="style=\"background-color:lightblue;\"";
					$userstate="НЕ зарегистрирован";
				};
				$retVal.="<tr>";
				$retVal.="<td class=$tdclass $rowstyle><a href=\"javascript:mod_action('userinfo',0," . $row["id"] . ")\">&nbsp;" . CutQuots($row["displayname"]) . "</a></td>";
				$retVal.="<td class=$tdclass $rowstyle><a href=\"mailto:" . $row["email"] . "\">" . $row["email"] . "</a></td>";
				$retVal.="<td class=$tdclass $rowstyle align=center width=30%><strong>$userstate</strong></td>";
				$retVal.="<td class=$tdclass $rowstyle align=center>[$regdate]</td>";
				$retVal.="</tr>";
				$retval.="<tr><tdclass=$tdclass $rowstyle colspan=4>$admnote</td></tr>";
				$tdclass=($tdclass=="data1")?"data2":"data1";
			};
		};
		$retVal.=drwTableEnd();
		$lastPage=0;
//		echo $counter . " % " . $PageSize;
		$lastPage=(($counter%$PageSize)==0)?0:1;
		$lastPage+=($counter/$PageSize);
		if($lastPage<2)return $retVal;
		$retVal.="страница:";
		for($fcounter=1;$fcounter<=$lastPage;$fcounter++){
			$PageHref="javascript:mod_action('$filter',$fcounter,0)";
			$PageLink=($fcounter==$PageNum)?" $fcounter":" <a href=\"$PageHref\">$fcounter</a>";
			$retVal.=$PageLink;
		};
		return $retVal;
	}

	function DisplayAuthSlot(){
		$retVal="";
		if($this->authorized){
			$retVal.=$this->DisplayAuthorizedSlot(145);
		}else{
			$retVal.=$this->DisplayEnterForm(139);
		};
		return $retVal;
	}

	function DisplayEnterForm($fWidth="100%"){
		$retVal="";
		$formtag="<form name=authenterform method=post action=\"" . $this->interfaceScript . "\"><input type=hidden name=action value=trylogin>";
		$enterform=str_replace("--form--",$formtag,$this->prms["enterform"]->Value);
		$enterform=str_replace("--twidth--",$fWidth,$enterform);
		$enterform=str_replace("--registerlink--",$this->interfaceScript . "?register",$enterform);
		$retVal.=$enterform;
		return $retVal;
	}

	function DisplayCommonEnterForm($fWidth="100%"){
		$retVal="";
		$formtag="<form name=authenterform2 method=post action=\"" . $this->interfaceScript . "\"><input type=hidden name=action value=trylogin>";
		$enterform=str_replace("--form--",$formtag,$this->prms["globalenterform"]->Value);
		$enterform=str_replace("--twidth--",$fWidth,$enterform);
		$enterform=str_replace("--registerlink--",$this->interfaceScript . "?register",$enterform);
		$retVal.=$enterform;
		return $retVal;
	}

	function DisplayNeedAuthPage($fWidth="100%"){
		$retVal=$this->prms["NeedLoginPage"]->Value;
		$retVal=str_replace("--globalenterform--",$this->DisplayCommonEnterForm($fWidth),$retVal);
		$retVal=str_replace("--twidth--",$fWidth,$retVal);
		$retVal=str_replace("--registerlink--",$this->interfaceScript . "?register",$retVal);
		return $retVal;
	}


	function DisplayAuthorizedSlot($fWidth="100%"){
		global $SessionSettings;
		$retVal="";
		$ibi=$this->GetInboxInfo();
		$formtag="<form name=authlogoutform method=post action=\"" . $this->interfaceScript . "\"><input type=hidden name=action value=logout>";
		$logoutform=str_replace("--logoutform--",$formtag,$this->prms["authorizedslot"]->Value);
		$logoutform=str_replace("--twidth--",$fWidth,$logoutform);
		$logoutform=str_replace("--regdatalink--",$this->interfaceScript . "?regdata",$logoutform);
		$logoutform=str_replace("--pmlink--",$this->interfaceScript . "?pm",$logoutform);
		$logoutform=str_replace("--pmunreaded--",$ibi["unreaded"],$logoutform);
		$logoutform=str_replace("--pmtotal--",$ibi["total"],$logoutform);
		$logoutform=str_replace("--username--",CutQuots($SessionSettings["siteuserdisplayname"]),$logoutform);
		$retVal.=$logoutform;
		return $retVal;
	}


	function CheckCredintals($email,$password){
		global $SessionSettings, $client_ip;
		if($this->prms["disableenter"]->Value)return 1;
		if($this->authorized)return true;
		$sql="select `id`,`displayname`, `email`, `regdate`, `regip`, `registered`, `enabled` from `$this->usersTable` where `email`='" . addslashes($email) . "' and `password`='" . addslashes($password) . "'";
		$this->dbc->sql_query($sql);
		if($this->dbc->sql_numrows()>0){
			$row=$this->dbc->sql_fetchrow();
			if(($row["enabled"]==1)&&($row["registered"]==1)){
				$SessionSettings["siteuserid"]=$row["id"];
				$SessionSettings["siteuserdisplayname"]=$row["displayname"];
				WriteSessionSettings();
				$sql="insert into `" . $this->enterLogTable . "` (`logdate`, `eventtype`, `description`, `user`, `remoteip`) values (" . time() . ", 'успешный вход', 'пользователь " . addslashes($row["displayname"]) . " успешно вошёл в систему', " . $row["id"] . ", '$client_ip')";
				$this->dbc->sql_query($sql);
				$this->authorized=true;
				return 0;
			}else{
				return -1;
			};
		}else{
			return -1;
		}
	}

	function UserLogout(){
		global $SessionSettings;
		$SessionSettings=array();
		WriteSessionSettings();
		$this->authorized=false;
	}

	function ErrorLoginMessage(){
		$retVal=$this->prms["errorlogin"]->Value;
		$retVal=str_replace("--enterform--",$this->DisplayCommonEnterForm(400),$retVal);
		return $retVal;
	}

	function TryToRegister(){
		global $HTTP_POST_VARS,$client_ip,$SiteMainURL;
		$retVal="";
		$sql="select `id`, `displayname`, `email`, `password`, `regdate`, `regip`, `enabled`, `adminnote`, `registered` from `" . $this->usersTable . "` where `email`='" . $HTTP_POST_VARS["email"] . "'";
		$this->dbc->sql_query($sql);
		if($this->dbc->sql_numrows()>0){
			return $this->prms["reg.error.already"]->Value;
		};
		$sql="insert into `" . $this->usersTable . "` (`displayname`, `email`, `password`, `regdate`, `regip`, `enabled`, `adminnote`, `registered`) values ('" . $HTTP_POST_VARS["displayname"] . "', '" . $HTTP_POST_VARS["email"] . "', '" . $HTTP_POST_VARS["password"] . "', " . time() . ", '" . $client_ip . "', 0, '', 0)";
		$this->dbc->sql_query($sql);
		$userID=$this->dbc->sql_nextid();
		$UserTemplate=$this->RegDataTemplate();
		$messagedet="";
		$messagedet.="Наименование: " . stripslashes($HTTP_POST_VARS["displayname"]) . "\r\n";
		foreach($UserTemplate as $aField){
			$dispValue=$this->UpdateUserField($aField,$HTTP_POST_VARS,"spec_" . $aField["fname"],$userID);
			$messagedet.=$aField["dname"] . ": " . $dispValue . "\r\n";
		};
		$mailheader="MIME-Version: 1.0\nContent-type: text/plain; charset=windows-1251\nDate: " . gmdate("D, d M Y H:i:s", time()) . " UT\nX-Priority: 3\nX-MSMail-Priority: Normal\nX-Mailer: PHP";
		mail($this->prms["moderemail"]->Value,"$SiteMainURL :: new request to register","$SiteMainURL :: new request to register\r\n\r\n$messagedet","$mailheader\nfrom:$SiteMainURL <webmaster@$SiteMainURL>");
		$retVal=$this->prms["reg.success"]->Value;
		return $retVal;

	}
	function RegisterPage(){
		$theScript="<script>" .
			"function register_checkform(frm){" .
			"if(String(frm.password.value).length<4){" .
			"alert('Слишком короткий пароль!\\nПароль должен быть длиннее трёх символов...');" .
			"return false;" .
			"};" .
			"if(frm.password.value!=frm.password2.value){" .
			"alert('Не совпадают два пароля!');" .
			"return false;".
			"};" .
			"var chs=new String(frm.email.value);" .
			"var testreg=/(^\\w+(\\.)*(-)*\\w*@\\w+(-)*\\w*(\\.\\w+)+$)/gi;" .
			"if((chs=='')||(chs.match(testreg)==null)){" .
			"alert('Введите корректно адрес электронной почты.');frm.email.focus();" .
			"return false;" .
			"};";
		$retVal="";
		$retVal.=$this->prms["registerformtemplate"]->Value;
		$itenslist="";
		$devider=$this->prms["registerformdevider"]->Value;
		$oneitem=$this->prms["registerformitem"]->Value;
		$oneitem=str_replace("--fieldname--","Наименование",$oneitem);
		$oneitem=str_replace("--fieldedit--","<input type=text class=text name=displayname maxlength=200 size=40>",$oneitem);
		$itenslist.=$oneitem . $devider;
		$oneitem=$this->prms["registerformitem"]->Value;
		$oneitem=str_replace("--fieldname--","Эл. почта",$oneitem);
		$oneitem=str_replace("--fieldedit--","<input type=text class=text name=email maxlength=200 size=40>",$oneitem);
		$itenslist.=$oneitem . $devider;
		$oneitem=$this->prms["registerformitem"]->Value;
		$oneitem=str_replace("--fieldname--","Пароль",$oneitem);
		$oneitem=str_replace("--fieldedit--","<input type=password class=text name=password maxlength=200 size=40>",$oneitem);
		$itenslist.=$oneitem . $devider;
		$oneitem=$this->prms["registerformitem"]->Value;
		$oneitem=str_replace("--fieldname--","Подтверждение пароля",$oneitem);
		$oneitem=str_replace("--fieldedit--","<input type=password class=text name=password2 maxlength=200 size=40>",$oneitem);
		$itenslist.=$oneitem . $devider;

		$regtemplate=$this->RegDataTemplate();
		foreach($regtemplate as $fKey => $aField){
			$reqsymb="";
			if($aField["req"]==1){
				$reqsymb="&nbsp;*";
				if(($aField["datatype"]=="char")||($aField["datatype"]=="memo")||($aField["datatype"]=="int")||($aField["datatype"]=="float"))
					$theScript.="if(frm.spec_" . $aField["fname"] . ".value==''){alert('Поле \"" . addslashes($aField["dname"]) . "\" является обязательным!');frm.spec_" . $aField["fname"] . ".focus();return false;};";
			};
			$oneitem=$this->prms["registerformitem"]->Value;
			$oneitem=str_replace("--fieldname--",$aField["dname"] . $reqsymb,$oneitem);
			$oneitem=str_replace("--fieldedit--",$this->ClientFieldEditor($aField,"spec_" . $aField["fname"]),$oneitem);
			$itenslist.=$oneitem . $devider;
		};
		$theScript.="return true;" .
			"};" .
			"</script>";
		$retVal=$theScript .$retVal;

		$formtag="<form name=registerform onsubmit=\"return register_checkform(this)\" method=post action=\"" . $this->interfaceScript . "\"><input type=hidden name=action value=tryregister>";
		$retVal=str_replace("--form--",$formtag,$retVal);
		$retVal=str_replace("--itemslist--",$itenslist,$retVal);
		return $retVal;
	}



	function UserInfoPage(){
		global $SessionSettings, $HTTP_POST_VARS;
		$retVal="";
		if(!($SessionSettings["siteuserid"]>0))return "";
		if($HTTP_POST_VARS["action"]=="tryupdateuserdata"){
		    $sql="select `id`,`displayname`, `email`, `regdate`, `regip`, `registered`, `enabled` from `$this->usersTable` where `id`=" . $SessionSettings["siteuserid"] . " and `password`='" . $HTTP_POST_VARS["curpassword"] . "'";
		    if(!$this->dbc->sql_query($sql)){
		    	    $sqlerror=$this->dbc->sql_error();
			    die($sqlerror["message"]);
			    return "";
		    };
		    if($row=$this->dbc->sql_fetchrow()){
			if($HTTP_POST_VARS["changepassword"]=="on"){
			    $sql="update `" . $this->usersTable . "` set `password`=" . $HTTP_POST_VARS["password"] . " where `id`=" . $SessionSettings["siteuserid"];
			    $this->dbc->sql_query($sql);
			};
			$UserTemplate=$this->RegDataTemplate();
			foreach($UserTemplate as $aField){
				$this->UpdateUserField($aField,$HTTP_POST_VARS,"spec_" . $aField["fname"],$SessionSettings["siteuserid"]);
			};
			$retVal.="<center><span style=\"color:red;\">Информация обновлена.</span></center>";
		    }else{
			$retVal.="<center><span style=\"color:red;\">Информация не обновлена. Не верный пароль.</span></center>";
		    };
		};
		$sql="select `id`,`displayname`, `email`, `regdate`, `regip`, `registered`, `enabled` from `$this->usersTable` where `id`=" . $SessionSettings["siteuserid"];
		if(!$this->dbc->sql_query($sql)){
			$sqlerror=$this->dbc->sql_error();
			die($sqlerror["message"]);
			return "";
		};
		if(!$row=$this->dbc->sql_fetchrow())return "";
		$theScript="<script>" .
			"function register_checkform(frm){" .
			"if((frm.changepassword.checked)&&(String(frm.password.value).length<4)){" .
			"alert('Слишком короткий пароль!\\nПароль должен быть длиннее трёх символов...');" .
			"return false;" .
			"};" .
			"if((frm.changepassword.checked)&&(frm.password.value!=frm.password2.value)){" .
			"alert('Не совпадают два пароля!');" .
			"return false;".
			"};" ;
		$retVal.=$this->prms["userinfoformtemplate"]->Value;
		$itenslist="";
		$devider=$this->prms["registerformdevider"]->Value;
		$oneitem=$this->prms["registerformitem"]->Value;
		$oneitem=str_replace("--fieldname--","Наименование",$oneitem);
		$oneitem=str_replace("--fieldedit--",CutQuots($row["displayname"]),$oneitem);
		$itenslist.=$oneitem . $devider;
		$oneitem=$this->prms["registerformitem"]->Value;
		$oneitem=str_replace("--fieldname--","Эл. почта",$oneitem);
		$oneitem=str_replace("--fieldedit--",CutQuots($row["email"]),$oneitem);
		$itenslist.=$oneitem . $devider;
		$oneitem=$this->prms["registerformitem"]->Value;
		$oneitem=str_replace("--fieldname--","Текущий пароль",$oneitem);
		$oneitem=str_replace("--fieldedit--","<input type=password class=text name=curpassword maxlength=200 size=40>",$oneitem);
		$itenslist.=$oneitem . $devider;
		$oneitem=$this->prms["registerformitem"]->Value;
		$oneitem=str_replace("--fieldname--","Изменять пароль",$oneitem);
		$oneitem=str_replace("--fieldedit--","<input type=checkbox name=changepassword>",$oneitem);
		$itenslist.=$oneitem . $devider;
		$oneitem=$this->prms["registerformitem"]->Value;
		$oneitem=str_replace("--fieldname--","Новый пароль",$oneitem);
		$oneitem=str_replace("--fieldedit--","<input type=password class=text name=password maxlength=200 size=40>",$oneitem);
		$itenslist.=$oneitem . $devider;
		$oneitem=$this->prms["registerformitem"]->Value;
		$oneitem=str_replace("--fieldname--","Подтверждение нового пароля",$oneitem);
		$oneitem=str_replace("--fieldedit--","<input type=password class=text name=password2 maxlength=200 size=40>",$oneitem);
		$itenslist.=$oneitem . $devider;

		$regtemplate=$this->GetUserData($SessionSettings["siteuserid"]);
		foreach($regtemplate as $fKey => $aField){
			$reqsymb="";
			if($aField["req"]==1){
				$reqsymb="&nbsp;*";
				if(($aField["datatype"]=="char")||($aField["datatype"]=="memo")||($aField["datatype"]=="int")||($aField["datatype"]=="float"))
					$theScript.="if(frm.spec_" . $aField["fname"] . ".value==''){alert('Поле \"" . addslashes($aField["dname"]) . "\" является обязательным!');frm.spec_" . $aField["fname"] . ".focus();return false;};";
			};
			$oneitem=$this->prms["registerformitem"]->Value;
			$oneitem=str_replace("--fieldname--",$aField["dname"] . $reqsymb,$oneitem);
			$oneitem=str_replace("--fieldedit--",$this->ClientFieldEditor($aField,"spec_" . $aField["fname"]),$oneitem);
			$itenslist.=$oneitem . $devider;
		};
		$theScript.="return true;" .
			"};" .
			"</script>";
		$retVal=$theScript .$retVal;

		$formtag="<form name=registerform onsubmit=\"return register_checkform(this)\" method=post action=\"" . $this->interfaceScript . "?regdata\"><input type=hidden name=action value=tryupdateuserdata>";
		$retVal=str_replace("--form--",$formtag,$retVal);
		$retVal=str_replace("--itemslist--",$itenslist,$retVal);
		return $retVal;
	}

	function PrivateMessages(){
		global $SessionSettings, $HTTP_POST_VARS;
		$retVal="";
		if(!($SessionSettings["siteuserid"]>0))return "";
		$sql="select `id`, `from`, `to`, `sentdate`, `sent`, `readdate`, `readed`, `subject`, `message`, `senderip`, `recipientip` from `$this->pmTable` where `to`=" . $SessionSettings["siteuserid"] . " order by `sentdate` desc";
		$this->dbc->sql_query($sql);
		while($row=$this->dbc->sql_fetchrow()){
		};
		return $retVal;
	}

	function PMListing($ownerID,$direction="inbox",$thePage=1){
		$itemslist="";
		$PageSize=$this->prms["pm.listing.pagesize"]->Value;
		$PageNum=$thePage;
		$realdirection=($direction!="inbox")?"outbox":"inbox";
		$whereclause=($realdirection=="inbox")?"`pmt`.`deletedbyrecipient`<>1 and `pmt`.`to`=$ownerID":"`pmt`.`deletedbysender`<>1 and `pmt`.`from`=$ownerID";
		$listtemplate=$this->prms["pm.$realdirection.listing"]->Value;
		$itemtemplate=$this->prms["pm.$realdirection.listing.item"]->Value;
		$devidertemplate=$this->prms["pm.$realdirection.listing.devider"]->Value;
		$retVal=$listtemplate;
		$sql="select `pmt`.`id`, `pmt`.`from`, `pmt`.`to`, `pmt`.`sentdate`, `pmt`.`sent`, `pmt`.`readdate`, `pmt`.`readed`, `pmt`.`subject`, `pmt`.`message`, `pmt`.`senderip`, `pmt`.`recipientip`, `u1`.`displayname` as `sendername`, `u2`.`displayname` as `recipientname` from `$this->pmTable` as `pmt` inner join `$this->usersTable` as `u1` on `u1`.`id`=`pmt`.`from` inner join `$this->usersTable` as `u2` on `u2`.`id`=`pmt`.`to` where $whereclause order by `sentdate` desc";
		$this->dbc->sql_query($sql);
		$counter=0;
		$todelcount=0;
		while($row=$this->dbc->sql_fetchrow()){
			$counter++;
			$InPage=(($counter>(($PageNum-1)*$PageSize))&&($counter<=(($PageNum)*$PageSize)));
			if($InPage){
				$todelcount++;
				$oneitem=$itemtemplate;
				$oneitem=str_replace("--subject--",CutQuots($row["subject"]),$oneitem);
				$oneitem=str_replace("--from--",CutQuots($row["sendername"]),$oneitem);
				$oneitem=str_replace("--deletecheckbox--","<input type=hidden name=idtodelete$todelcount value=" . $row["id"] . "><input type=checkbox name=todelete$todelcount>",$oneitem);
				$oneitem=str_replace("--to--",CutQuots($row["recipientname"]),$oneitem);
				$oneitem=str_replace("--message--",str_replace("\r\n","<br>",CutQuots($row["message"])),$oneitem);
				$oneitem=str_replace("--readhref--",$this->interfaceScript . "?pm&$realdirection&page=$PageNum&details=" . $row["id"],$oneitem);
				$oneitem=str_replace("--deletehref--",$this->interfaceScript . "?pm&$realdirection&page=$PageNum&delete=" . $row["id"],$oneitem);
				$oneitem=str_replace("--date--",date($this->prms["DateFormat"]->Value,$row["sentdate"]),$oneitem);
				$itemslist.=$oneitem . $devidertemplate;
			};
		};
		if(strlen($itemslist)>strlen($devidertemplate))$itemslist=substr($itemslist,0,(strlen($itemslist)-strlen($devidertemplate)));
		$unreaded=0;
		if($realdirection=="inbox"){
			$sql="select count(*) as `ur` from `$this->pmTable` where $whereclause and `readed`<>1";
			$this->dbc->sql_query($sql);
			$unreaded=$this->dbc->sql_fetchfield("ur");
		};
		$pager=$this->MakePageChanger($this->interfaceScript . "?pm&$realdirection", $thePage, $counter);
		$bigform="<form method=post name=pmlistingform action=\"$this->interfaceScript?pm&$realdirection&page=$PageNum\"><input type=hidden name=action value=bulkdelete><input type=hidden name=totalitemscount value=$counter>";
		$retVal=str_replace("--itemslist--",$itemslist,$retVal);
		$retVal=str_replace("--unreaded--",$unreaded,$retVal);
		$retVal=str_replace("--total--",$counter,$retVal);
		$retVal=str_replace("--pager--",$pager,$retVal);
		$retVal=str_replace("--form--",$bigform,$retVal);
		$retVal=str_replace("--deleteitemslink--","javascript:document.forms['pmlistingform'].submit()",$retVal);
		return $retVal;
	}

	function MakePageChanger($theFormPrefix, $thePage, $allitemscount){
		$retVal="";
		$counter=$allitemscount;
		$PageSize=$this->prms["pm.listing.pagesize"]->Value;
		$PageNum=$thePage;
		$lastPage=($counter%$PageSize==0)?0:1;
		$Devider=$this->prms["pm.listing.pager.devider"]->Value;
		$lastPage+=($counter/$PageSize);
		if($lastPage<2)return "";
		for($fcounter=1;$fcounter<=$lastPage;$fcounter++){
			$PageHref=$theFormPrefix . "&page=" . $fcounter;
			$PageLink=($fcounter==$PageNum)?$this->prms["pm.listing.pager.active"]->Value:$this->prms["pm.listing.pager.inactive"]->Value;
			$PageLink=str_replace("--link--",$PageHref,$PageLink);
			$PageLink=str_replace("--pagenum--",$fcounter,$PageLink);
			$retVal.=$PageLink . $Devider;
		};
		if(strlen($retVal)>strlen($Devider))$retVal=substr($retVal,0,(strlen($retVal)-strlen($Devider)));
		if(strlen($retVal)>0)$retVal=str_replace("--pageslist--",$retVal,$this->prms["pm.listing.pager"]->Value);
		return $retVal;
	}

	function PMReadMessage($ownerID,$direction="inbox",$thePage=1,$messageID){
		$PageNum=$thePage;
		$whereclause="`pmt`.`id`=$messageID and (`pmt`.`from`=$ownerID or `pmt`.`to`=$ownerID)";
		$sql="select `pmt`.`id`, `pmt`.`from`, `pmt`.`to`, `pmt`.`sentdate`, `pmt`.`sent`, `pmt`.`readdate`, `pmt`.`readed`, `pmt`.`subject`, `pmt`.`message`, `pmt`.`senderip`, `pmt`.`recipientip`, `u1`.`displayname` as `sendername`, `u2`.`displayname` as `recipientname` from `$this->pmTable` as `pmt` inner join `$this->usersTable` as `u1` on `u1`.`id`=`pmt`.`from` inner join `$this->usersTable` as `u2` on `u2`.`id`=`pmt`.`to` where $whereclause";
		$this->dbc->sql_query($sql);
		if($this->dbc->sql_numrows()==0)return "";
		$row=$this->dbc->sql_fetchrow();
		$realdirection=($row["from"]==$ownerID)?"outbox":"inbox";
		$retVal=$this->prms["pm.$realdirection.message.read"]->Value;
		$retVal=str_replace("--subject--",CutQuots($row["subject"]),$retVal);
		$retVal=str_replace("--from--",CutQuots($row["sendername"]),$retVal);
		$retVal=str_replace("--to--",CutQuots($row["recipientname"]),$retVal);
		$retVal=str_replace("--message--",str_replace("\r\n","<br>",CutQuots($row["message"])),$retVal);
		$retVal=str_replace("--deletehref--",$this->interfaceScript . "?pm&$realdirection&page=$PageNum&delete=" . $row["id"],$retVal);
		$retVal=str_replace("--replyhref--",$this->interfaceScript . "?pm&$realdirection&page=$PageNum&reply=" . $row["id"],$retVal);
		$retVal=str_replace("--backhref--",$this->interfaceScript . "?pm&$realdirection&page=$PageNum",$retVal);
		$retVal=str_replace("--date--",date($this->prms["DateFormat"]->Value,$row["sentdate"]),$retVal);
		return $retVal;
	}

	function PMMarkToDelete($ownerID,$messageID){
		$sql="update `$this->pmTable` set `deletedbysender`=1 where `id`=$messageID and `from`=$ownerID";
		$this->dbc->sql_query($sql);
		$sql="update `$this->pmTable` set `deletedbyrecipient`=1 where `id`=$messageID and `to`=$ownerID";
		$this->dbc->sql_query($sql);
	}

	function GetInboxInfo(){
		global $SessionSettings;
		if(isset($this->inboxinfo["total"]))return $this->inboxinfo;
		$sql="select count(*) as `cc` from `$this->pmTable` where `to`=" . $SessionSettings["siteuserid"] . " and `deletedbyrecipient`<>1 and `readed`<>1";
		$this->dbc->sql_query($sql);
		$this->inboxinfo["unreaded"]=$this->dbc->sql_fetchfield("cc");
		$sql="select count(*) as `cc` from `$this->pmTable` where `to`=" . $SessionSettings["siteuserid"] . " and `deletedbyrecipient`<>1";
		$this->dbc->sql_query($sql);
		$this->inboxinfo["total"]=$this->dbc->sql_fetchfield("cc");
		return $this->inboxinfo;
	}
	
	function Install($reinstall=false){
		$installsql="DROP TABLE IF EXISTS `$this->enterLogTable`;
			CREATE TABLE `$this->enterLogTable` (
			  `logdate` int(11) NOT NULL default '0',
			  `eventtype` varchar(250) NOT NULL default '',
			  `description` varchar(250) NOT NULL default '',
			  `user` int(11) NOT NULL default '0',
			  `remoteip` varchar(20) NOT NULL default '',
			  KEY `logdate` (`logdate`)
			);
			DROP TABLE IF EXISTS `$this->usersNeedsTable`;
			CREATE TABLE `$this->usersNeedsTable` (
			  `id` int(11) NOT NULL auto_increment,
			  `fname` varchar(250) NOT NULL default '',
			  `dname` varchar(250) NOT NULL default '',
			  `description` text NOT NULL,
			  `req` int(11) NOT NULL default '0',
			  `datatype` varchar(50) NOT NULL default '',
			  `sort` int(11) NOT NULL default '0',
			  `changeable` int(11) NOT NULL default '0',
			  PRIMARY KEY  (`id`)
			);
			DROP TABLE IF EXISTS `$this->pmTable`;
			CREATE TABLE `$this->pmTable` (
			  `id` bigint(20) NOT NULL auto_increment,
			  `from` int(11) NOT NULL default '0',
			  `to` int(11) NOT NULL default '0',
			  `sentdate` int(11) NOT NULL default '0',
			  `sent` int(11) NOT NULL default '0',
			  `readdate` int(11) NOT NULL default '0',
			  `readed` int(11) NOT NULL default '0',
			  `subject` varchar(250) NOT NULL default '',
			  `message` text NOT NULL,
			  `senderip` varchar(20) NOT NULL default '',
			  `recipientip` varchar(20) NOT NULL default '',
			  `deletedbysender` int(11) NOT NULL default '0',
			  `deletedbyrecipient` int(11) NOT NULL default '0',
			  PRIMARY KEY  (`id`)
			);
			DROP TABLE IF EXISTS `$this->usersTable`;
			CREATE TABLE `$this->usersTable` (
			  `id` int(11) NOT NULL auto_increment,
			  `displayname` varchar(250) NOT NULL default '',
			  `email` varchar(250) NOT NULL default '',
			  `password` varchar(250) NOT NULL default '',
			  `regdate` int(11) NOT NULL default '0',
			  `regip` varchar(20) NOT NULL default '',
			  `enabled` int(11) NOT NULL default '0',
			  `adminnote` text NOT NULL,
			  `registered` int(11) NOT NULL default '0',
			  PRIMARY KEY  (`id`)
			);
			DROP TABLE IF EXISTS `$this->regdataTable`;
			CREATE TABLE `$this->regdataTable` (
			  `user` int(11) NOT NULL default '0',
			  `field` varchar(30) NOT NULL default '0',
			  `bool` int(11) NOT NULL default '0',
			  `memo` text NOT NULL,
			  `char` varchar(250) NOT NULL default '',
			  `datetime` int(11) NOT NULL default '0',
			  `int` int(11) NOT NULL default '0',
			  `float` float NOT NULL default '0'
			);";
		$splitedinstructions=split(";",$installsql);
		foreach($splitedinstructions as $oneinstruction)
			if($reinstall){
				$this->dbc->sql_query($oneinstruction);
			}else if(strstr($oneinstruction,"DROP TABLE")==FALSE){
					$this->dbc->sql_query($oneinstruction);
				};
	}
};

$theAuthModule=new clsAuthModule("auth","авторизация",$db);
$SAmodsArray["auth"]=$theAuthModule;
$SAmodsArray["auth"]->prms=MergeConfigs($SAmodsArray["auth"]->prms,GetConfig(0,"auth"));
?>
