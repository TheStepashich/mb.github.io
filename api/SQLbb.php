<?php
// SQLbb - lite class for working with SQL //
//       made by Fleisar in 11.08.20       //

// USING EXAMPLES
/*  --> SELECT
 *  $bb->select('example.table',["login"=>"User","password"=>"password"]);
 *  -> THIS REQUEST
 *  SELECT * FROM `example.table` WHERE `login`='User' AND `password`='password'
 *  --> SELECT (using LIKE)
 *  $bb->select('example.table',["login%LIKE%"=>"_ser%"],"&","`login`");
 *  -> THIS REQUEST
 *  SELECT `login` FROM `example.table` WHERE `login` LIKE '_ser%'
 *  --> INSERT
 *  $bb->insert('example.table',["login"=>"User","password"=>"password"]);
 *  -> THIS REQUEST
 *  INSERT INTO `example.table`(`login`,`password`) VALUES ('User','password');
 *  --> UPDATE
 *  $bb->update('example.table',["password"=>"newPassword"],["login"=>"User","password"=>"password"]);
 *  -> THIS REQUEST
 *  UPDATE `example.table` SET `password`='newPassword' WHERE `login`='User' AND `password`='password'
 */

class SQLbb {
    public $sql = null;
    public $debug = false;
    public function __construct($host, $username, $password, $dbname, $port=3306) {
        $this->sql = new mysqli($host, $username, $password, $dbname, $port);
        return $this->sql;
    }
    public function select($table,$search,$statements="&",$colums="*") {
        $select = "";
        $i = 0;
        foreach($search as $colum => $need){
            $operator = "=";
            if(substr($colum,-6,6) == "%LIKE%"){
                $colum = substr($colum, 0, -6);
                $operator = " LIKE ";
            }
            $select .= " `${colum}`${operator}'".$this->sql->real_escape_string($need)."' ";
            if($i<sizeof($search)-1){
                $select .= $statements[$i>strlen($statements)-1?strlen($statements)-1:$i]==="&"?" AND":" OR";
            }
            $i++;
        }
        if($this->debug) echo "SELECT ${colums} FROM `${table}` WHERE ${select}<br/>";
        return $this->sql->query("SELECT ${colums} FROM `${table}` WHERE ${select}");
    }
    public function insert($table, $colums){
        $col = "";
        $val = "";
        foreach($colums as $colum => $value){
            $col .= "`${colum}`,";
            $val .= "'".$this->sql->real_escape_string($value)."',";
        }
        $col = substr($col, 0, -1);
        $val = substr($val, 0, -1);
        if($this->debug) echo "INSERT INTO `${table}`(${col}) VALUES (${val})<br/>";
        return $this->sql->query("INSERT INTO `${table}`(${col}) VALUES (${val})");
    } // INSERT INTO $table($colums) VALUES ($values);
    public function update($table, $set, $search, $statements="&"){
        $setted = "";
        foreach($set as $key => $string){
            $setted .= "`${key}`='".$this->sql->real_escape_string($string)."', ";
        }
        $setted = substr($setted, 0, -2);
        $select = "";
        $i = 0;
        foreach($search as $key => $string){
            $select .= "`${key}`='".$this->sql->real_escape_string($string)."' ";
            if($i<sizeof($search)-1){
                $select .= $statements[$i>strlen($statements)-1?strlen($statements)-1:$i]==="&"?" AND":" OR";
            }
            $i++;
        }
        if($this->debug) echo "UPDATE `${table}` SET ${setted} WHERE ${select}<br/>";
        return $this->sql->query("UPDATE `${table}` SET ${setted} WHERE ${select}");
    } // UPDATE $table SET $setVar WHERE $statements
}
