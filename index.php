<?php
	include("./define/common.php");
	header("Location: " . $contentscript . "?id=" . $coreParams["startnode"]->Value . "\n");
?>