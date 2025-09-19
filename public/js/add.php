<?php
/*
    Automatyczne podlaczenie wszystkich JS z tego katalogu pod stron
*/

    //UWAGA, wymagany PHP5
    function ScriptsAdd(){
    $LIST="";
        foreach(new DirectoryIterator('public/js/') as $file){
            if( strpos($file->getFilename(),".")!=0 && $file->getFilename()!="add.php"){
                $tab[] = $file->getFilename();
                }
            }
            
        sort($tab);
        reset($tab);
            
        for ($x=0;$x<count($tab);$x++){
            $LIST.='<script type="text/javascript" src="public/js/'.$tab[$x].'"></script> '."\n";
        }
    return $LIST;
    }
    

?>