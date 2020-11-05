<?php
    const self = "main";
    $_CLIENT = null;
    $_modules = [
        "Undefined" => [
            "name" => "Undefined",
            "version" => 0.0,
            "successes" => ["Unregistered module. [S]"],
            "errors" => ["Unregistered module. [E]"]
        ],
        "main" => [
            "server" => "https://127.0.0.1/",
            "name" => "main",
            "version" => 1.0,
            "errors" => [
                "Undefined error!",
                "Unknown method!",
                "Invalid method"
            ],
            "successes" => ["Success."]
        ]
    ];
    $uri = $_SERVER["REQUEST_URI"];
    $levels = explode("/", $uri);
    $method = $levels[2];
    header("Content-Type: application/json");
    header("Access-Control-Allow-Origin: ".$_modules[self]["name"]);
    if(!file_exists(stripslashes($method).".php")) {
        api(false, 1, self, ["debug_method"=>stripslashes($method)]);
    }
    $levelOffset = 3;
    $levels = array_slice($levels, $levelOffset);
    switch($_SERVER["REQUEST_METHOD"]){
        case "PUT":
        case "OPTION":
        case "DELETE":
        case "GET":
            $_CLIENT = ["GET"=>$levels,"INPUT"=>json_decode(file_get_contents("php://input"))];
            break;
        case "POST":
            $_CLIENT = ["GET"=>$levels,"POST"=>&$_POST,"INPUT"=>json_decode(file_get_contents("php://input"))];
            break;
        default:
            api(false,2,self,["debug_method"=>$_SERVER["REQUEST_METHOD"]]);
    }
    require stripslashes($method).".php";
    function api(bool $state = false, int $code = 0, string $method = "Undefined", array $data = []){
        global $_modules;
        $data = array_merge($data, ["debug_timestamp"=>time()]);
        $des = $_modules[$method][$state?"successes":"errors"][$code];
        $description = $des!==""?$des:$_modules[$method][$state?"successes":"errors"][0];
        echo json_encode(array_merge(["state"=>$state,"code"=>$code,"description"=>$description,"module"=>$method], $data));
        exit;
    }
    function mCreate(string $name, float $version, array $successes = ["Success."], array $errors = ["Unknown error."]){
        global $_modules;
        // Attention, creating new module, that already contains in it, will be overwritten by latest.
        $_modules = array_merge($_modules, [$name=>["name"=>$name,"version"=>$version,"successes"=>$successes,"errors"=>$errors]]);
    }
