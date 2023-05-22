<?php
    class Controller{
        public function jump($url,$message,$wait=2){
            if($time == 0){
                header("Location:$url");
            }else{
                include CUR_VIEW_PATH."message.html";
            }
            die();
        }
        
        public function library($lib){
            include LIB_PATH."{$lib}.class.php";
        }
        
        public function helper($help){
            include HELP_PATH."{$help}.php";
        }
    }
