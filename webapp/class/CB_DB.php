<?php

/**
 * CB_DB.php
 *
 * @copyright  2004-2007 CYBRiDGE
 * @license    CYBRiDGE 1.0
 */

CLASS CB_DB{

    function CB_DB(){
        global $GLADIUS_DB_ROOT;
        $this->adodb = ADONewConnection(DB_TYPE);
        $result = $this->adodb->Connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

//        $this->adodb->debug = 1;
        if(!$result){
            if(DB_TYPE == "gladius" && @mkdir($GLADIUS_DB_ROOT . DB_NAME) && @mkdir(VAR_DIR . '/data/' . DB_NAME)){
                return $this->CB_DB();
            }
            $this->error("接続エラー");
        }
    }

    function Execute($sql){
	    $sql = preg_replace("/\"/is","'",$sql);
	    $query = explode(";",$sql);
	    if(!is_array($query)){
	        $query = array($sql);
	    }
	    foreach($query AS $val){
	        if(trim($val)){
	            $data[] = $this->adodb->execute($val.";");
	        }
	    }
        return $data;
    }

    
    function GetAll($sql){
        return $this->adodb->GetAll($sql);
    }

    function GetRow($sql){
        $rows = $this->GetAll($sql);
        return $rows[0];
    }
    
    function Insert($table,$data){
        foreach($data as $key=>$val){
            if(preg_match("/^_/is",$key)){
                unset($data[$key]);
            }
        }

        if(DB_TYPE == "gladius"){
            foreach($data as $key=>$val){
                $data[$key] = preg_replace(array("/\"/is","/'/is"),array("’","’"),$data[$key]);
                $data[$key] = preg_replace("/\\\/is","",$data[$key]);
            }
        }

        foreach($data as $key=>$val){
            $fields[] = $key;
			//エスケープ対応	2013/08/14
			$val=mysql_real_escape_string($val);
            $values[] = "'{$val}'";
        }
        $sql = "INSERT INTO {$table} (".implode(",", $fields).") VALUES (".implode(",", $values).")";
        @$this->Execute($sql);

        if(DB_TYPE == "gladius"){
  				$tables = $this->GetAll('SHOW TABLES');
				  foreach($tables as $value){
				    if(preg_match("/{$table}/is",$value["table"])){
				      return $value["top_insert_id"];
				    }
				  }
				}
				else{
				  $in_sql = 'SELECT LAST_INSERT_ID();';
				  $in_res = $this->GetRow($in_sql);
				  return $in_res[0];
				}
    }

    function Update($table,$data,$where){
	    foreach($data as $key=>$val){
	        if($val != null){
				//エスケープ対応	2013/08/14
				$val=mysql_real_escape_string($val);
	        	$values[] = "{$key} = '{$val}'";
	    	}else{
	        	$values[] = "{$key} = null";
	   		}
	    }
	    $sql = "UPDATE {$table} SET ".implode(",", $values)." WHERE {$where}";
//echo $sql;

	    @$this->Execute($sql);
    }

    function Delete($table,$where){
    $sql = "DELETE FROM {$table} WHERE {$where}";
    $this->Execute($sql);
    }

    function logicalDelete($table,$where){
    $sql = "UPDATE {$table} SET is_deleted=1 WHERE {$where}";
    $this->Execute($sql);
    }

    function Debug($flag = 1){
        $this->adodb->debug = $flag;
    }

    function Error($str){
        die($str);
    }
}