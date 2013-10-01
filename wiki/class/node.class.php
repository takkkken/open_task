<?php
define("NEW_PAGE",-1);
define("NO_PATH",-2);

class Node{
    
    var $con;       // the sql connection
    var $histid;    // the id of the node for history purpose
    var $target;    // the node from where the action was initiated
    var $user;      //
    var $uid;       // the id of the user making the change
    var $lang;      // the language of the tree
    var $ip;        // the ip address of the change being made
    var $action;    // add, remove, rename
    var $comment;   // the comment on the change recorded in the history
    var $position;  // this is used for ordering the nodes
    var $name;      // the label of the node
    var $json;      // a response to send to the client
    var $root;      // language root
    var $registered_only;      // only registered users can edit
    
    /**
     * Consructor
     * 
     * @param link $con    The connection to the mysql database
     * @param string $lang The language
     */
    function Node($con,$lang){
        $this->con = $con;
        $this->lang = $lang;

        $this->root = 1;
        
    }

    /**
     * Sets the id
     * 
     * @param int   $id  The string to be converted (WikiWord)
     * @return      void
     */
    function setHistId($id) {$this->histid = is_numeric($id) ? $id : 0;}
    
    /**
     * Sets the id
     * 
     * @param int   $id  The string to be converted (WikiWord)
     * @return           void
     */
    function setUid($id) {$this->uid = is_numeric($id) ? $id : 0;}
    
    /**
     * Sets the id
     * 
     * @param int $id   The string to be converted (WikiWord)
     * @return          void
     */
    function setTarget($id) {$this->target = is_numeric($id) ? $id : 0;}

    /**
     * Converts a String to Wiki Word Format
     *
     * @param string  $str  The string to be converted (WikiWord)
     * @return string       The converted format (Wiki Word)
     */
    function WikiWord($str){
        $segs = explode("/",$str);
        $w = $segs[count($segs)-1];
        return preg_replace("/([a-z])([A-Z])/", "$1 $2", $w);
    }
    
    /**
     * Find the id of a node based on it's name and parent
     * 
     * @param  int $parent    The parent id
     * @param  string $label  The label of the node
     * @return int            The node id or -1 if not found
     */
    function parseNode($parent, $label){
        $label = $this->WikiWord($label);
        $sql = "SELECT * FROM node INNER JOIN page ON node.node_id=page.node_id ".
				"WHERE node.parent_id=$parent AND page.label='$label' AND page.language='$this->lang'";
        $result = mysql_query($sql,$this->con);
        
        if(mysql_num_rows($result) > 0){
            $id = mysql_result($result, 0, 'node.node_id');
        }else{
            $id = -1;
        }       
        return $id;
    }
    
    /**
     * Find the page name based on the path
     * 
     * @param string $path     The path of the node
     * @return string          The page name as a string
     */
    function PageFromPath($path){
        $chunks = explode("/", substr($path,1)); 
        return $this->WikiWord($chunks[count($chunks)-1]);
    }
    
    /**
     * Find the id of the parent node based on it's name and parent
     * 
     * @param string $path     The path of the node
     * @return int             The parent id
     */
    function ParentFromPath($path){
        $chunks = explode("/", substr($path,1)); 

        if(count($chunks) == 1){
            return $this->root;
        }else{
            $parentpath = '';
            for($i=0;$i<count($chunks)-1;$i++){
                $parentpath.= "/$chunks[$i]";
            }
            return $this->NodeFromPath($parentpath);
        }
    }
    
    /**
     * Find the id of a node from it's path
     * 
     * @param string $path    The path of the node
     * @return  int           The node id or NEW_PAGE if found at end of path.  NO_PATH if not found.
     */
    function NodeFromPath($path){
        $chunks = explode("/", substr($path,1)); // remove first slash and split
        
        $id = $this->root;
        
        $c = 1; // count chunks, only create page at end of path
        foreach($chunks as $chunk){
            $id = $this->parseNode($id, $chunk);
            if($id==-1){
                $redirect = $this->CheckRedirect($path);
                if($redirect > 0) return $redirect;
                if($c == count($chunks))
                    $id = NEW_PAGE;
                else
                    $id = NO_PATH;  
                break;
            }
            $c++;
        }
        return $id;
    }
    
    /**
     * email changes to subscribers
     * 
     * @param string $return_address   The From: part of the email
     * @param string $action           (add,remove,rename)
     */
    function Subscriptions($return_address, $action){
        if($return_address=='') return; // don't use subscription mail feature
        $sql = "SELECT subscribe,email FROM user WHERE subscribe=1";
        try{
            $result = mysql_query($sql, $this->con);
            
        }catch(Exception $e){
            $this->json="{'response':'Database Error - Unable to check subscription'}";
            return FALSE;
        }

	
        
        for($s=0;$s<mysql_num_rows($result);$s++){
            
            $nowe = date('Y-m-d H:i:s');
            $email = mysql_result($result,$s,'email');  
            $to      = $email;
            $subject = 'Wiki Web Help Update';
            $headers = "From: $return_address\r\n";
                
            $send = "Node ".$this->name." has been updated $nowe with action of $action.";
            
            try{
                $this->_mail_utf8($to, $subject, $send, $headers);
            
            }catch(Exception $e){
                $this->json="{'response':'Database Error - Unable to send subscription'}";
            }
        }
    }
    
	function _mail_utf8($to, $subject = '(No subject)', $message = '', $header = '') {
		$header_ = 'MIME-Version: 1.0' . "\r\n" . 'Content-type: text/plain; charset=UTF-8' . "\r\n";
		return mail($to, '=?UTF-8?B?'.base64_encode($subject).'?=', $message, $header_ . $header);
	}

    /**
     * Renames a node and adds a redirect from the old name
     * 
     * @return bool TRUE or FALSE
     */
    function Rename(){
    	if(!$this->isEditable($this->target)){
    		$this->json="{'response':'Page locked, unable to rename'}";
		    return FALSE;
    	}
			
        try{
			$sql = "SELECT node_id FROM page WHERE node_id=$this->target AND language='$this->lang'";
            $result = mysql_query($sql, $this->con);
			if(mysql_num_rows($result) == 0){
				$sql = "INSERT INTO page VALUES($this->target,'$this->lang','$this->name','=$this->name=',0)";
				$result = mysql_query($sql, $this->con);
			}else{
		
				// here we want to save the old name as a redirect.
				$this->AddRedirect($this->PathFromId($this->target),$this->target);
				$sql = "UPDATE page SET label='$this->name' WHERE node_id=$this->target AND language='$this->lang'";
				$result = mysql_query($sql, $this->con);
			}			
            
        }catch(Exception $e){
            $this->json="{'response':'Unable to rename'}";
            return FALSE;
        }
        $this->json="{'response':'ok','node':'".$this->target."'}";     
        $this->histid = $this->target;
        
        return TRUE;
        
    }
    
    /**
     * Records the changes
     * 
     * @param string $action   (add, remove, rename)
     * @return bool            TRUE or FALSE
     */
    function UpdateHistory($action){
        $now = date('YmdHis');
        if($action=="addpage" || $action == "addfolder") $action = "add";
        
        $sql = "INSERT INTO `node_revision` (revision_id,node_id,user_id,user_ip,language,action, comment,revision_time,label) ";
        $sql .= "VALUES ('',".$this->histid.",".$this->uid.",'".$this->ip."','".$this->lang."','$action', '".$this->comment."','$now','".$this->name."') ";

        try{
            $result = mysql_query($sql, $this->con);
            
        }catch(Exception $e){
            $this->json="{'response':'Error updating history'}";
            return FALSE;
        }
        return TRUE;
    }
	
    /**
     * Is a user blocked?
     * 
     * @return  bool TRUE or FALSE
     */
    function blocked(){
        $block = 1;
        
        try{
            $ip=$_SERVER['REMOTE_ADDR'];
            
            $sqlb = "SELECT ip_address FROM blocked WHERE ip_address='$ip'";
            $resultb = mysql_query($sqlb,$this->con);
            $block = mysql_num_rows ($resultb);
            
        }catch(Exception $e){
            $err = $e->getMessage();
            $this->json="{'response':'Database Error, $err'}";
            return FALSE;
        }
        if($block > 0)
            return TRUE;
            
        return FALSE;
    	
    }

    /**
     * Checks to see if a node is editable based on its locked status and blocked ips
     * 
     * @param int $node_id  The id of the node to be checked
     * @return bool         TRUE or FALSE
     */
	function isEditable($node_id){
        $sql = "SELECT locked FROM page WHERE node_id=".$node_id ." AND language='$this->lang'";
        $block = 1; $locked = 0;
        
        try{
            $result = mysql_query($sql, $this->con);
            if(mysql_num_rows($result) > 0)
				$locked = mysql_result($result, 0, 'locked');
            
        }catch(Exception $e){
            $err = $e->getMessage();
            $this->json="{'response':'Database Error, $err'}";
            return FALSE;
        }
		if($locked == 1 || $this->blocked() || ($this->registered_only && ($this->uid < 1)))
		    return FALSE;

		return TRUE;
	}
    
    /**
     * Removes a node
     * 
     * @param int      $node_id  The id of the node to be removed
     * @return bool    TRUE or FALSE
     */
    function RemoveNode($node_id){
        $this->histid = $node_id;
        $sql = "SELECT parent_id,node_position,locked,label FROM node WHERE node_id=".$node_id;
		
        try{
            $result = mysql_query($sql, $this->con);
            
        }catch(Exception $e){
        	$err = $e->getMessage();
            $this->json="{'response':'Database Error, $err'}";
            return FALSE;
        }
		
        if(!$this->isEditable($node_id)){
            $this->json="{'response':'Page locked.  Unlock page before deleting.'}";
            return FALSE;
        }

        $parent_id = mysql_result($result, 0, 'parent_id');
        $node_position = mysql_result($result, 0, 'node_position');
        $this->name = mysql_result($result, 0, 'label');
        
        // remove node by setting parent to 0 to preserve it
        $sql = "UPDATE node SET parent_id=0 WHERE node_id=".$node_id;
        try{
            $result = mysql_query($sql, $this->con);
            
        }catch(Exception $e){
            $this->json="{'response':'Database Error, Unable to update node positions'}";
            return FALSE;
        }
    
        // shift nodes down if they are above the current node
        $sql = "UPDATE node SET node_position=node_position-1 WHERE node_position>$node_position AND parent_id=$parent_id";
        try{
            $result = mysql_query($sql, $this->con);
            
        }catch(Exception $e){
            $this->json="{'response':'Database Error, Unable to update node positions'}";
            return FALSE;
        }
        $this->json="{'response':'ok','node':'-1'}";
        return TRUE; 
    }
    
    /**
     * Creates a new node based on the previously set member variables
     * 
     * @param int $clip This is used when pasting a node from the clipboard
     */
    function NewNode($clip = false){
    	if($this->blocked()){
    		$this->json="{'response':'You do not have the authority to add pages'}";
			return;
    	}
        $sql = "SELECT parent_id, node_position FROM node WHERE node_id=".$this->target;
        try{
            $result = mysql_query($sql, $this->con);
            
        }catch(Exception $e){
            $this->histid=-1; 
            $this->json="{'response':'Database Error, Invalid Target'}";
            return;
        }

        $parent_id = mysql_result($result, 0, 'parent_id');
        $node_position = mysql_result($result, 0, 'node_position');
        $newpos = 0;
    
        if($this->position == 'before'){
            $newpos = $node_position;
            $targetpos = $node_position + 1;
        }
        
        if($this->position == 'after'){
            $newpos = $node_position + 1;
            $targetpos = $node_position;        
        }
    
        if($this->position == 'in'){
            // insert in folder at the end
            $sql = "SELECT max(node_position) AS lastnode FROM node WHERE parent_id=".$this->target;
            try{
                $result = mysql_query($sql, $this->con);
                
            }catch(Exception $e){
                $this->histid=-1; 
                $this->json="{'response':'Database Error, Unable to retrieve position'}";
                return;
            }

            if(mysql_num_rows($result) > 0){
                $last = mysql_result($result, 0, 'lastnode')+1;     
                $newpos = $last;
            }
            
            $targetpos = $node_position;    
            $parent_id = $this->target; 
        }else{
            // shift nodes up before inserting and updating target
            $sql = "UPDATE node SET node_position=node_position+1 WHERE node_position>$node_position AND parent_id=$parent_id";
            try{
                $result = mysql_query($sql, $this->con);
                
            }catch(Exception $e){
                $this->histid=-1; 
                $this->json="{'response':'Database Error, Unable to update node position'}";
                return;
            }
        }
        
        if($targetpos != $node_position){
            $sql = "UPDATE node SET node_position=$targetpos WHERE node_id=".$this->target;
            try{
                $result = mysql_query($sql, $this->con);
                
            }catch(Exception $e){
                $this->histid=-1; 
                $this->json="{'response':'Database Error, Unable to update node position'}";
                return;
            }
        }
        
        if($clip){
            $sql = "UPDATE node SET node_position=$newpos, parent_id=$parent_id WHERE node_id=$clip";
            try{
                $result = mysql_query($sql, $this->con);
                
            }catch(Exception $e){
                $this->histid=-1; 
                $this->json="{'response':'Database Error, Unable to paste node'}";
                return;
            }
            $new_id = $clip;
        }else{
            $sql = "INSERT INTO node VALUES(NULL,$parent_id, '".$this->name."', $newpos, 0)";
            try{
                $result = mysql_query($sql, $this->con);
                
            }catch(Exception $e){
                $this->histid=-1; 
                $this->json="{'response':'Database Error, Unable to insert node'}";
                return ;
            }
            $new_id = mysql_insert_id();
            
            $sql = "INSERT INTO page VALUES($new_id, '$this->lang','$this->name','=$this->name=',0)";
            try{
                $result = mysql_query($sql, $this->con);
                
            }catch(Exception $e){
                $this->histid=-1; 
                $this->json="{'response':'Database Error, Unable to create page'}";
                return;
            }
        }
        
        $this->histid = $new_id;
        $this->json = "{'response':'ok','node':'$new_id'}"; 
    }
    
    /**
     * Finds the parent id and the label of a node
     * 
     * @param int $id   The id of the node
     * @return  array   array with the parent id and the label of the node
     */
    function ParentFromId($id){
        $sql = "SELECT parent_id,page.label FROM node INNER JOIN page ON node.node_id=page.node_id WHERE page.node_id=$id and page.language='$this->lang'";
        try{
            $result = mysql_query($sql, $this->con);
			if(mysql_num_rows($result) > 0){
				$pid = mysql_result($result, 0, 'parent_id');               
				$lab = mysql_result($result, 0, 'page.label');   
			}
        }catch(Exception $e){
            $this->json = "{'response':'".mysql_error()."'}";
            return array("pid"=>"", "label"=>"");
        }

        return array("pid"=>$pid, "label"=>$lab);
    }
    
    /**
     * Gets the full path of a node
     * 
     * @param int $id   The id of the node
     * @return string   The full path
     */
    function PathFromId($id){
        $chunks = array(); $cnt=0; $path="";
        while($id != $this->root){
            $info = $this->ParentFromId($id);
            $id = $info['pid'];
            if($id == 0)  // deleted page
                break;
            $chunks[] = $info['label'];
            $cnt++;
        }
        $chunks = array_reverse($chunks);
        
        foreach($chunks as $chunk){
            $path.="/$chunk";
        }

        return $path;
    }
    
    /**
     * Adds a redirect to the database that will be used to send to an id from the path
     * 
     * @param string $path The path to be inserted
     * @param int    $id   The id of the node to be inserted
     */
    function AddRedirect($path,$id){
        if($path == '') return;
        $sql = "INSERT INTO redirect VALUES('$path', '$this->lang', $id)";
        try{
            mysql_query($sql, $this->con);
        }catch(Exception $e){
            $this->json = "{'response':'".mysql_error()."'}";
        }
    }
    
    /**
     * Checks to see if the path is in the redirect table.  
     * This is only checked if path does not exist
     * 
     * @param string $path The path to be redirected
     * @return  int        The id to redirect to if found, if not -1
     */
    function CheckRedirect($path){
        $path = preg_replace("/([a-z])([A-Z])/", "$1 $2", $path); // wiki wordified path
        $sql = "SELECT * FROM `redirect` "
                ."INNER JOIN `node` ON node.node_id=redirect.redirect_id "
                ."WHERE redirect_path='$path' AND language='$this->lang'";
        try{
            $result = mysql_query($sql, $this->con); 
            if(mysql_num_rows($result)>0) {
                $id = mysql_result($result, 0, 'redirect_id');
                $pid = mysql_result($result, 0, 'parent_id');
                if($pid > 0) return $id;
            }
        }catch(Exception $e){
            $this->json = "{'response':'".mysql_error()."'}";
            return -1;
        }
        
        return -1;
    }
}
?>