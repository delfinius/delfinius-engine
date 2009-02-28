<?php
// Класс для работы с таблицей в виде дерева

class DbTree{

    function DbTree($tablename, $databasecconnector){
	$this->cTreeTable=$tablename;
	$this->dbc=$databasecconnector;
	$this->cTableFields=array();
	$this->cKeyField='';
	$this->cParentField='';
	$this->cSortField='';
	$this->cNameField='';
	return $this;
    }

    // Вспомогательная функция для возвращения дерева (возвращается двумерный массив. поля записей - dbs_deep; все перечисленные в cTableFields)
    function GetHelper($theParent, $theDeep, $theSelectCondition = ''){
	    $localSelectCondition='';
	    if($theSelectCondition!='')$localSelectCondition=' and ' . $theSelectCondition;
	    $tmpLocalReturnArray=array();
	    $lastpos=0;
	    $selectCommand='select ';
	    for($lcounter=0;$lcounter<count($this->cTableFields);$lcounter++){
		$selectCommand.=$this->cTableFields[$lcounter];
		if($lcounter!=(count($this->cTableFields)-1))$selectCommand.=', ';
	    };
	    $selectCommand.=' from ' . $this->cTreeTable . ' where ' . $this->cParentField . '=' . $theParent . $localSelectCondition . ' order by ' . $this->cSortField;
//	    echo $selectCommand;
	    
	    if(!$this->dbc->sql_query($selectCommand)){
		$sqlerror=$this->dbc->sql_error();
		die($sqlerror['message']);
	    };
	    $lcounter=0;
	    while($row=$this->dbc->sql_fetchrow()){
		$tmpLocalReturnArray[$lcounter]=array();
		$tmpLocalReturnArray[$lcounter]["dbs_deep"]=$theDeep;
		foreach($this->cTableFields as $aKey => $aField)
		    $tmpLocalReturnArray[$lcounter][$this->cTableFields[$aKey]]=$row[$this->cTableFields[$aKey]];
		$lcounter++;
	    };
	    $ReturnArray=array();
	    for($lcounter=0;$lcounter<count($tmpLocalReturnArray);$lcounter++){
		$lastpos=count($ReturnArray);
		$ReturnArray[$lastpos]=$tmpLocalReturnArray[$lcounter];
		$ReturnArray=array_merge($ReturnArray,$this->GetHelper($tmpLocalReturnArray[$lcounter][$this->cKeyField], $theDeep+1, $theSelectCondition));
	    };
	    return $ReturnArray;
    }


    // Функция генерации дерева. Возвращает двумерный массив.
    function GetTree($theParent,$theSelectCondition=''){
	return $this->GetHelper($theParent,1,$theSelectCondition);
    }

	
    // Функция построения пути до узла. Двумерный массив. Поля данных - все перечисленные в cTableFields
    function GetNodePath($theNode,$theWithRoot=true){
	$cParent=$theNode;
	$ReturnArray=array();
	$llcounter=0;
	while($cParent!=0){
		$selectCommand='select ';
		for($lcounter=0;$lcounter<count($this->cTableFields);$lcounter++){
			$selectCommand.=$this->cTableFields[$lcounter];
			if($lcounter!=(count($this->cTableFields)-1))$selectCommand.=', ';
		};
		$selectCommand.=' from ' . $this->cTreeTable . ' where ' . $this->cKeyField . '=' . $cParent;
		if(!$this->dbc->sql_query($selectCommand)){
		    $sqlerror=$this->dbc->sql_error();
		    die($sqlerror['message']);
	        };
		if($row=$this->dbc->sql_fetchrow()){
			if($row[$this->cParentField]>=0){
				$ReturnArray[$llcounter]=array();
				foreach($this->cTableFields as $aKey => $aField)
					$ReturnArray[$llcounter][$aField]=$row[$aField];
				$cParent=$row[$this->cParentField];
				$llcounter++;
			}else{
				$cParent=0;
			};
		}else{
			$cParent=0;
		};
	};
	if($theWithRoot){
		$ReturnArray[$llcounter]=array();
		$ReturnArray[$llcounter][$this->cKeyField]=0;
		$ReturnArray[$llcounter][$this->cNameField]='root';
	};
	return array_reverse($ReturnArray);
    }


    // Функция возвращающая открытое до определённого узла дерево. Возвращаемые данные как в dbTreeGetHelper + поле dbs_opened
    function GetNodeTreeOpenedToNode($theNode,$theSelectCondition = '',$theWithRoot=true){
	$localAllTree=$this->GetTree(0,$theSelectCondition);
	$localNodePath=$this->GetNodePath($theNode,$theWithRoot);
	$ReturnArray=array();
	foreach($localAllTree as $laKey1 => $aTreeRec1){
		$curlen=count($ReturnArray);
		foreach($localNodePath as $laKey2 => $aTreeRec2){
			if(($localAllTree[$laKey1][$this->cKeyField]==$localNodePath[$laKey2][$this->cKeyField])||($localAllTree[$laKey1][$this->cParentField]==$localNodePath[$laKey2][$this->cKeyField])){
				$ReturnArray[$curlen]=$localAllTree[$laKey1];
				$ReturnArray[$curlen]["dbs_opened"]=($localAllTree[$laKey1][$this->cKeyField]==$localNodePath[$laKey2][$this->cKeyField]);
			};
		};
	};
	return $ReturnArray;
    }
	


    function GetTreeAsOptions($theParent,$theCurrent,$theExclude=false){
	$returnString='';
	$localExcludes=array();
	if($theExclude){
		$localExcludes=$this->GetTree($theExclude);
	};
	$localTree=$this->GetTree(0);
	foreach($localTree as $laKey1 => $aTreeRec1){
		$DeepStr='';
		$localDoExclude=($localTree[$laKey1][$this->cKeyField]==$theExclude);
		foreach($localExcludes as $laKey2 => $aTreeRec)
			if($localTree[$laKey1][$this->cKeyField]==$localExcludes[$laKey2][$this->cKeyField])$localDoExclude=true;

		for($laKey2=0;$laKey2<=$localTree[$laKey1]['dbs_deep'];$laKey2++)$DeepStr.='&nbsp;&nbsp;';
		$localSelected=($localTree[$laKey1][$this->cKeyField]==$theCurrent)?' selected':'';
		if(!$localDoExclude)$returnString.='<option value="' . $localTree[$laKey1][$this->cKeyField] . '"' . $localSelected . '>' . $DeepStr . CutQuots($localTree[$laKey1][$this->cNameField]);
	};
	return $returnString;
    }


    // Возвращается один развёрнутый уровень дерева (возвращается двумерный массив. поля записей - все перечисленные в cTableFields)
    function GetExpandedLevel($theParent,$theSelectCondition=''){
	$localSelectCondition='';
	if($theSelectCondition!='')$localSelectCondition=' and ' . $theSelectCondition;
	$tmpLocalReturnArray=array();
	$selectCommand='select ';
	for($lcounter=0;$lcounter<count($this->cTableFields);$lcounter++){
		$selectCommand.=$this->cTableFields[$lcounter];
		if($lcounter!=(count($this->cTableFields)-1))$selectCommand.=', ';
	};
	$selectCommand.=' from ' . $this->cTreeTable . ' where ' . $this->cParentField . '=' . $theParent . $localSelectCondition . ' order by ' . $this->cSortField;

	if(!$this->dbc->sql_query($selectCommand)){
	    $sqlerror=$this->dbc->sql_error();
	    die($sqlerror['message']);
        };
	while($row=$this->dbc->sql_fetchrow()){
	    $tmpLocalReturnArray[$lcounter]=array();
	    foreach($this->cTableFields as $aKey => $aField)
		$tmpLocalReturnArray[$lcounter][$this->cTableFields[$aKey]]=$row[$this->cTableFields[$aKey]];
	    $lcounter++;
	};
	return $tmpLocalReturnArray;
    }
	
};

// Создание объектов. Из названий переменных всё понятно
$SiteTree=new DbTree("sitetree",$db);
$SiteTree->cTableFields=array(0 =>"id", 1=>"parent", 2=>"name", 3=>"visible", 4=>"type", 5=>"sort", 6=> "symbol", 7=> "icon", 8=> "needlogged", 9 => "nltext", 10=> "ableinmenu");
$SiteTree->cKeyField="id";
$SiteTree->cParentField="parent";
$SiteTree->cSortField="sort";
$SiteTree->cNameField="name";
?>