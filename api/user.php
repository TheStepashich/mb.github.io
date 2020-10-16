<?php
    $self = "user";
    // run through main module
    if(!isset($_MODULES["main"])) exit;
    // register module
    $_modules->{"create"}("user",1.0,["Success."],["Unknown error!","SQL connection error."]);
    // check if config exists
    if(!file_exists("config.php")) api(false, 0, $self);
    require "config.php";
    function init(){
        global $SELF, $_MODULES, $sql, $config;
        // connecting to SQL
        $sql = new SQLbb(
            $config["mysqli"]["host"],
            $config["mysqli"]["login"],
            $config["mysqli"]["password"],
            $config["mysqli"]["database"],
            $config["mysqli"]["port"]
        );
        if($sql === false) api(false, 1, $SELF["module"]);
        if(!$sql->sql->query("SELECT * FROM `${config["base"]["authTable"]}`") === false){
            // table not found, so creating new
            $sql->sql->query("
                CREATE TABLE `${config["mysqli"]["database"]}`.`${config["base"]["authTable"]}` ( 
                    `id` INT NOT NULL , 
                    `login` TINYTEXT NOT NULL , 
                    `password` TINYTEXT NOT NULL , 
                    `content` JSON NOT NULL DEFAULT '[]' , 
                    `timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP 
                ) ENGINE = InnoDB;
            ");
        }
    }
