<?php
  include("./define/common.php");
  $news_node=7;
  $modsArray['news']->prms=MergeConfigs($modsArray['news']->prms,GetConfig(0,'news'));
  $modsArray['news']->prms=MergeConfigs($modsArray['news']->prms,GetConfig($news_node,'news'));
  $modOutPut=$modsArray['news']->MakeUserOuput($news_node,"$contentscript?id=" . $news_node . $hrefSuffix);

  if($SAmodsArray["counter"])$SAmodsArray["counter"]->WriteLog($node);
  include("./define/index_top.php");
  if($modsArray['news']){
    echo $modOutPut[0];
  };
  include("./define/bottom.php");
?>