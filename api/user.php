<?php
    $self = "user";
    $sql = null;
    // run through main module
    if(!isset($_modules["main"])) exit;
    // register module
    mCreate("user",1.0,["Success."],[
        "Unknown error!",
        "SQL connection error.",
        "Invalid password.",
        "User not found.",
        "User already exists."
    ]);
    // check if config exists
    if(!file_exists("config.php")) api(false, 0, $self);
    require "config.php";
    switch($_SERVER["REQUEST_METHOD"]){
        case "GET":
            init();
            $user = $sql->select($config["base"]["authTable"],["login"=>$_CLIENT["GET"][0],"id"=>$_CLIENT["GET"][0]],"|", "`id`,`login`,`timestamp`");
            if($user->num_rows == 0){
                api(false,3,$self,[]);
            }
            $user = $user->fetch_assoc();
            api(true,0,$self,[
                "user_id" => (int) $user["id"],
                "user_login" => $user["login"],
                "user_timestamp" => $user["timestamp"]
            ]);
            break;
        case "POST":
            init();
            $login = htmlspecialchars($_CLIENT["POST"]["user_login"]);
            if(strlen($login) < 3 || strlen($login) > 16)
                api(false, 4, $self, ["info_loginLength"=>"3-16"]);
            if(!preg_match( "/\w{3,32}/", $_CLIENT["POST"]["user_password"]))
                api(false, 2, $self, ["info_regex"=>"/\w{3,32}/"]);
            $password = md5($_CLIENT["POST"]["user_password"]);
            $sUser = $sql->select($config["base"]["authTable"],["login"=>$login]);
            if($sUser->num_rows !== 0)
                api(false,4,$self);
            $nUser = $sql->insert($config["base"]["authTable"],["login"=>$login,"password"=>md5($password),"content"=>"[]"]);
            api(true,0,$self);
            break;
    }
    function init(){
        global $SELF, $sql, $config;
        require "SQLbb.php";
        // connecting to SQL
        $sql = new SQLbb(
            $config["mysqli"]["host"],
            $config["mysqli"]["login"],
            $config["mysqli"]["password"],
            $config["mysqli"]["database"],
            $config["mysqli"]["port"]
        );
        if($sql === false) api(false, 1, $SELF["module"]);
        if($sql->sql->query("SELECT * FROM `".$config["base"]["authTable"]."`") === false){
            // table not found, so creating new
            $sql->sql->query("
                CREATE TABLE `".$config["mysqli"]["database"]."`.`".$config["base"]["authTable"]."` ( 
                    `id` INT NOT NULL AUTO_INCREMENT, 
                    `login` TINYTEXT NOT NULL , 
                    `password` TINYTEXT NOT NULL , 
                    `content` JSON NOT NULL , 
                    `timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
                    PRIMARY KEY (`id`)
                ) ENGINE = InnoDB;
            ");
        }
        return true;
    }
