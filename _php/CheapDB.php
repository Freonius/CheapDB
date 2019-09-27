<?php
/**
 * 
 * @author Freonius
 *
 */
class CheapDB{
	protected $table;
	/**
	 * 
	 * @var array
	 */
	protected $result=array();
	/**
	 * 
	 * @var array
	 */
	protected $limit=array(0,0);
	protected $where=null;
	protected $structure;
	protected $last_id=0;
	protected $values=array();
	protected $error=false;
	protected $json=null;
	protected $error_reporting=false;
	protected $name="";
	protected $folder="";
	/**
	 * If error reporting is set to true it writes the errors.
	 * @param instance $e
	 * @return NULL
	 */
	protected function err($e){
		if(!$this->error_reporting){
			return null;
		}
		echo "Error@: FILE-><strong>";
		echo $e->getFile();
		echo "</strong> LINE-><strong>";
		echo $e->getLine();
		echo "</strong> MESSAGE->";
		echo $e->getMessage();
		echo "<br />";
		return null;
	}
	/**
	 * 
	 * @param string $table
	 * @param array $structure
	 * @param boolean $error_reporting valid for all the instance
	 * @return boolean
	 */
	public function __construct($table,$structure,$folder="_json/",$error_reporting=false){
		$this->error_reporting=$error_reporting;
		$this->folder=$folder;
		try{
			$this->realconstruct($table, $structure);
		}catch(Exception $e){
			$this->err($e);
			$this->error=true;
			return false;
		}
		return true;
	}
	public function setWhere($field,$value){
		$this->where=array($field,$value);
	}
	protected function realconstruct($table,$structure){
		$table=trim($table);
		$this->name=$table;
		$temp=$table;
		$table=$this->folder.$table.".json";
		if(!file_exists($table)){
			throw new Exception("No table with the name ".$temp.".");
			return false;
		}
		if(!is_array($structure)){
			throw new Exception("The structure for ".$temp." must be an array.");
			return false;
		}
		$handle=fopen($table,"r");
		//$read=fread($hande,filesize($table));
		$read="";
		while(!feof($handle)){
			$read.=fgets($handle);
		}
		if($read==""){
			$this->last_id=0;
			$this->result="";
			$this->structure=$structure;
			$this->table=$table;
			return true;
		}
		$l=strlen("var ".$this->name."_json=");
		$read=substr($read, $l,strlen($read));
		$read=substr($read,0,strlen($read)-1);
		$res=json_decode($read,true);
		if(!$res){
			throw new Exception("Cannot read from table ".$temp.".");
			return false;
		}
		if(!is_array($res)){
			throw new Exception("Results given for ".$temp." is not an array.");
			return false;
		}
		end($res);
		$last_key=key($res);
		if(!is_array($res[$last_key])){
			throw new Exception("Result given from ".$temp." is a 2d array. 3d expected.");
			return false;
		}
		foreach($structure as $field){
			if(!array_key_exists($field, $res[$last_key])){
				throw new Exception("Structure for ".$temp." does not match the one give.");
				return false;
			}
		}
		$this->last_id=filter_var($last_key,FILTER_SANITIZE_NUMBER_INT);
		$this->result=$res;
		$this->structure=$structure;
		$this->table=$table;
		return true;
	}
	public function limit($from,$how_many){
		
	}
	/**
	 * Insert new value for Create, Update
	 * @param string $name Set the field name
	 * @param string $value Set the value to add
	 */
	public function setValue($name,$value=""){
		$this->values[$name]=$value;
		return null;
	}
	/**
	 * Append to file (AKA create a new entry)
	 * @return boolean
	 */
	public function append(){
		try{
			$this->r_append();
		}catch(Exception $e){
			$this->err($e);
			return false;
		}
		return true;
	}
	/**
	 * Update where id=id
	 * @param int $id
	 * @return boolean
	 */
	public function change($id){
		try{
			$this->r_change($id);
		}catch(Exception $e){
			$this->err($e);
			return false;
		}
		return true;
	}
	/**
	 * Delete from file where id=id
	 * @param int $id
	 * @return boolean
	 */
	public function delete($id){
		try{
			$this->r_delete($id);
		}catch(Exception $e){
			$this->err($e);
			return false;
		}
		return true;
	}
	protected function r_append(){
		if($this->error){
			throw new Exception("An error occurred before the append.");
			return false;
		}
		$id=$this->last_id;
		$id++;
		try{
			$this->reconstruct($id);
		}catch(Exception $e){
			$this->err($e);
			return false;
		}
		try{
			$this->commit();
		}catch(Exception $e){
			$this->err($e);
			return false;
		}
		return true;
	}
	protected function r_change($id){
		if($this->error){
			throw new Exception("An error occurred before the change.");
			return false;
		}
		$id=filter_var($id,FILTER_SANITIZE_NUMBER_INT);
		try{
			$this->reconstruct($id,"u");
		}catch(Exception $e){
			$this->err($e);
			return false;
		}
		try{
			$this->commit();
		}catch(Exception $e){
			$this->err($e);
			return false;
		}
		return true;
	}
	protected function r_delete($id){
		if($this->error){
			throw new Exception("An error occurred before the change.");
			return false;
		}
		$id=filter_var($id,FILTER_SANITIZE_NUMBER_INT);
		try{
			$this->reconstruct($id,"d");
		}catch(Exception $e){
			$this->err($e);
			return false;
		}
		try{
			$this->commit();
		}catch(Exception $e){
			$this->err($e);
			return false;
		}
		return true;
	}
	/**
	 * Select from file. If a json format is required, set $get_json to true.
	 * @param string $get_json
	 * @throws Exception
	 * @return boolean|string|multitype:|Ambigous <multitype:, unknown>
	 */
	public function get($get_json=false){
		if($this->error){
			throw new Exception("An error occurred before the get.");
			return false;
		}
		$res=$this->result;
		if(!is_array($this->where)){
			if($get_json){
				return json_encode($res);
			}
			return $res;
		}
		unset($res);
		$res=array();
		foreach($this->result as $id=>$array){
			if(!is_array($array)){
				throw new Exception("Result is not an array.");
				return false;
			}
			$is_id=false;
			if($this->where[0]=="id"){
				$is_id=true;
			}
			if(!array_key_exists($this->where[0], $array) && !$is_id){
				throw new Exception($this->where[0]." is not in the result array.");
				return false;
			}
			if($is_id){
				if($id==$this->where[1]){
					$res[$id]=$array;
				}
			}
			if($array[$this->where[0]]==$this->where[1]){
				$res[$id]=$array;
			}
		}
		if($get_json){
			return json_encode($res);
		}
		return $res;
	}
	protected function reconstruct($id,$mode="c"){
		$res=$this->result;
		if($mode=="d" || $mode=="u"){
			if(!array_key_exists($id, $res)){
				throw new Exception("ID ".$id." was not found.");
				return false;
			}
			if($mode=="d"){
				unset($res[$id]);
				$this->json=$res;
				return true;
			}
		}
		$res[$id]=array();
		foreach($this->structure as $field){
			if(!is_array($this->values)){
				throw new Exception("Values is not an array.");
				$this->error=true;
				return false;
			}
			if(!array_key_exists($field, $this->values)){
				$value="";
			}
			else{
				$value=$this->values[$field];
			}
			$value=preg_replace("/{ID}/", $id, $value);
			$res[$id][$field]=$value;
		}
		$this->json=$res;
		return true;
	}
	protected function commit(){
		if($this->error){
			throw new Exception("An error occurred before committing.");
			return false;
		}
		if($this->json==null){
			throw new Exception("JSON array has not been set.");
			$this->error=true;
			return false;
		}
		if(!is_array($this->json)){
			throw new Exception("JSON is not an array.");
			$this->error;
			return false;
		}
		$json="var ".$this->name."_json=";
		$json.=json_encode($this->json);
		$json.=";";
		$handle=fopen($this->table,"w");
		if(!fwrite($handle, $json)){
			throw new Exception("Could not write to file.");
			fclose($handle);
			$this->error=true;
			return false;
		}
		else{
			fclose($handle);
			return true;
		}
	}
	public function getScript(){
		return "\t<script src=\"".$this->table."\"></script><!--JSON table for ".$this->name."-->\n";
	}
}