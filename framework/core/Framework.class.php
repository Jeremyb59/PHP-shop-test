<?php
    class Framework{
        public static function run(){
            static::init();
            static::autoload();
            static::dispath();
        }
        
        private static function init(){
            define("DS",DIRECTORY_SEPARATOR);
            define("ROOT",getcwd().DS);
            define("APP_PATH",ROOT."application".DS);
            define("FRAMEWORK_PATH",ROOT."framework".DS);
            define("PUBLIC_PATH",ROOT."public".DS);
            define("CONFIG_PATH",APP_PATH."config".DS);
            define("CONTROLLER_PATH",APP_PATH."controllers".DS);
            define("MODEL_PATH",APP_PATH."models".DS);
            define("VIEW_PATH",APP_PATH."views".DS);
            define("CORE_PATH",FRAMEWORK_PATH."core".DS);
            define("DB_PATH",FRAMEWORK_PATH."databases".DS);
            define("HELP_PATH",FRAMEWORK_PATH."helpers".DS);
            define("LIB_PATH",FRAMEWORK_PATH."libraries".DS);
            define("UPLOAD_PATH",PUBLIC_PATH."uploads".DS);
            define("PLATFORM",isset($_GET['p'])?$_GET['p']:"admin");
            define("CONTROLLER",isset($_GET['c'])?ucfirst($_GET['c']):"Index");
            define("ACTION",isset($_GET['a'])?$_GET['a']:"index");
            define("CUR_CONTROLLER_PATH",CONTROLLER_PATH.PLATFORM.DS);
            define("CUR_VIEW_PATH",VIEW_PATH.PLATFORM.DS);
            
            $GLOBALS['config'] = include CONFIG_PATH.'config.php';
            
            include CORE_PATH."Controller.class.php";
            include CORE_PATH."Model.class.php";
            include DB_PATH."Mysql.class.php";
            
            session_start();
        }
        
        private static function dispath(){
            $controller_name = CONTROLLER."Controller";
            $action_name = ACTION."Action";
            $controller = new $controller_name();
            $controller->$action_name(); 
        }
        
        private static function load($classname){
            if(substr($classname,-10) == "Controller"){
                include CUR_CONTROLLER_PATH."{$classname}.class.php";
            }elseif (substr($classname,-5) == "Model") {
                include MODEL_PATH."{$classname}.class.php";
            }
        }
        
        private static function autoload(){
            $arr = array(__CLASS__,"load");
            spl_autoload_register($arr);
        }
    }
