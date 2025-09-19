<?php
/*
    Automatyczne podlaczenie wszystkich css z tego katalogu pod stron
*/

    //UWAGA, wymagany PHP5
    function CssAdd(){
    $LIST="";
        foreach(new DirectoryIterator('public/css/') as $file)
            if( strpos($file->getFilename(),".")!=0  && $file->getFilename()!="add.php"){
                $LIST.='<link href="public/css/'.$file->getFilename().'" rel="stylesheet" type="text/css" media="all" />'."\n";
                }
    return $LIST;
    }
    

?>