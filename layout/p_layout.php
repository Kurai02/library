<?php
    function active($currect_page){
        $url = str_replace(".php","", basename($_SERVER['PHP_SELF'])); //name without .php
        if($currect_page == $url){
            echo 'active'; //class name in css
        }
    }

?>