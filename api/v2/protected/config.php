<?php
require "jData.php";
$jd = new jData("db.json",["username","password","additional","rank"]);
if($jd==false) exit("Error in protected area: Failed to load JSON");

