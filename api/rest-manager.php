<?php
    const self = "main";
    $_modules = [
        "create" => function(string $name, float $version, array $successes = ["Success."], array $errors = ["Unknown error."]){
            global $_modules;
            // Attention, creating new module, that already contains in it, will be overwritten by latest.
            $_modules = array_merge($_modules, [$name=>["name"=>$name,"version"=>$version,"successes"=>$successes,"errors"=>$errors]]);
        },
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
                "Unknown method!"
            ],
            "successes" => ["Success."]
        ]
    ];
    $uri = $_SERVER["REQUEST_URI"];
    $levels = explode("/", $uri);
    $method = $levels[2];
    header("Content-Type: application/json");
    header("Access-Control-Allow-Origin: ${_modules[self]["name"]}");
    if(!file_exists(stripslashes($method).".php")) {
        api(false, 1, self, ["debug.method"=>stripslashes($method)]);
    }
    $levelOffset = 3;
    $levels = array_slice($levels, $levelOffset);
    switch($_SERVER["REQUEST_METHOD"]){
        case "GET":
            $_CLIENT = ["GET"=>$levels,"INPUT"=>json_decode(file_get_contents("php://input"))];
            break;
        case "POST":
            $_CLIENT = ["POST"=>&$_POST,"INPUT"=>json_decode(file_get_contents("php://input"))];
            break;
        case "PUT":
            $_CLIENT = ["INPUT","INPUT"=>json_decode(file_get_contents("php://input"))];
            break;
    }
    include stripslashes($method).".php";
    function api(bool $state = false, int $code = 0, string $method = "Undefined", array $data = []){
        global $_modules;
        $data = array_merge($data, ["debug.timestamp"=>time()]);
        $description = $_modules[$method][$state?"successes":"errors"][$code] || $_modules[$method][$state?"successes":"errors"][0];
        echo json_encode(array_merge(["state"=>$state,"code"=>$code,"description"=>$description,"module"=>$method], $data));
        exit;
    }
