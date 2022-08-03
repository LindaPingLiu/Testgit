<?php
/**
* 
* @author Cho <cho@capture.co.uk>
* @version 1.0
* @package core
*/

/**
* Licence Type class - A class for controlling licence type used on the system
*
* This class is responsible for holding basic data about different licence type used by the system
*
* @author Cho <cho@capture.co.uk>
* @version 1.0
* @package core
*/
class access_level extends Business_Bean{

	function access_level($id=0){
		parent::Business_Bean();
		$this->id=$id;
	}

	//---------------------
	// private functions
	//---------------------
	function _load(){
		$sql="SELECT access_level.*  FROM (access_level) where idaccess_level=".$this->id;
		$this->db->query($sql);
		$this->details=$this->db->getSingleRow();
		return true;
	}
	
	function _save(){
        if(!$this->details['level']){
            $this->details['level'] = $this->nextLevelNumber();
        }
		$query="update access_level set `name`='".$this->details['name']."', level_number='".$this->details['level']."' where idaccess_level=".$this->id;
		$this->db->query($query);
	}
	
	function _createNew($vars){
		$this->details=array_merge($this->details, $vars);
		if(!$this->details['level']){
            $this->details['level'] = $this->nextLevelNumber();
		}
		$query="insert into access_level (`name`,`level_number`) 
				values ('".addslashes($this->details['name'])."', '".addslashes($this->details['level'])."')";
		$this->db->query($query);	
		$this->id=$this->db->getID();		
	}
	function nextLevelNumber(){
        $query="select max(level_number) from access_level;";
        $current_level = $this->db->getSingleValue($query);
        $next_level = $current_level?$current_level+1:1;
        return $next_level;
    }
	
	function _update($vars){
		$this->details=array_merge($this->details, $vars);
		$this->_save();
	}	
	
	
	function _search(){
		$result=new access_levelList;
		$wheresql="";	//implement manually by calc where clause from contents of the bean details array
		$result->where=$wheresql;
		return $result;
	}
	
	function _delete(){
		//if ($this->id==1) return false; // licencetype
		$this->load();

		global $db;
		$query="delete from access_level where idaccess_level=".$this->id;
		$this->db->query($query);
		return true;
	}
	
}
/**
* access_levelList class - A class for controlling lists of access_level
*
* @package core
*/

class access_levelList extends Bean_Collection{

	function access_levelList ($page=1, $order="level_number"){
		parent::Bean_Collection();
		$this->rowsperpage=DEFAULT_ROWS;
		$this->page=$page;
		if ($order=="") $order="name";
		$this->order=" ORDER BY ".$order;
		$this->select="SELECT access_level.*";
		$this->from=" FROM (access_level)";
		$this->primarykey="idaccess_level";
		$this->loadDetail();			//unusally you can load all langs at instanteation because there should never be many
										// this behaviour saves code elsewhere since you need licencetype lists all over the system
	}
}
?>