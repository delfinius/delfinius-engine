<script>
function checkfloat(str){
	var testreg=/(^(\d)*(\.)?(\d)+$)/gi;
	var chs=String(str);
	if((chs!="")&&(chs.match(testreg)!=null)){
		return true;
	}else{
		return false;
	};
};

function commatodot(str){
	var chs=String(str);
	var rep=/,/gi;
	chs=chs.replace(rep,'.');
	return chs;
};


function checkint(str){
	var testreg=/(^(\d)*$)/gi;
	var chs=String(str);
	if((chs!="")&&(chs.match(testreg)!=null)){
		return true;
	}else{
		return false;
	};
};

function checkParamsForm(theForm){
	var ParamsCount=Number(theForm.paramscount.value);
	for(counter=1;counter<=ParamsCount;counter++)if(eval('theForm.override'+counter).checked){
		paramType=eval('theForm.paramtype'+counter).value;
		paramValue=eval('theForm.paramvalue'+counter);
		paramName=eval('theForm.paramname'+counter).value;
		switch(paramType){
			case 'int':{
				if(!checkint(paramValue.value)){
					alert('Ќеверный формат значени€ целочисленного параметра "'+paramName+'"!');
					paramValue.focus();
					return false;
				};
				break;
			};

			case 'float':{
				paramValue.value=commatodot(paramValue.value);
				if(!checkfloat(paramValue.value)){
					alert('Ќеверный формат значени€ параметра "'+paramName+'"!');
					paramValue.focus();
					return false;
				};
				break;
			};
			default:
		};
	};
	return true;
};
</script>
