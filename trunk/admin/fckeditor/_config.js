FCKConfig.AutoDetectLanguage = false ;
FCKConfig.DefaultLanguage = "ru" ;
FCKConfig.StartupFocus = true ;
FCKConfig.EditorAreaCSS = '/style.php' ;
FCKConfig.LinkBrowser = false;
FCKConfig.BodyClass = 'body';

//FCKConfig.ImageBrowserURL = '/media/images/';
//FCKConfig.FlashBrowserURL = '/media/flashes/';
FCKConfig.ImageUpload = true ;

FCKConfig.SmileyPath = '/media/smiley/msn/' ;
FCKConfig.StylesXmlPath = '/style.xml'

FCKConfig.ToolbarStartExpanded = true ;

FCKConfig.ToolbarSets["delfinius-engine"] = [
['Source','DocProps','-','Save','NewPage','-','Templates'],
['Cut','Copy','Paste','PasteText','PasteWord', '-', 'Find', 'Replace'],
['Undo','Redo','-', 'Bold','Italic','Underline','StrikeThrough'],
['OrderedList','UnorderedList','-','Outdent','Indent'],
['Link','Unlink','Anchor'],
'/',
['FontFormat', '-' ,'Style', 'TextColor', 'BGColor'],
['Table','Image','Flash','Rule','SpecialChar','Smiley'],
['ShowBlocks','-','About']
] ;