<?php
require_once '../_php/CheapDB.php';
$structure=array("song","artist");
$db=new CheapDB("songs", $structure,"../_json/",true);
if(!isset($_GET["role"])){
	$role="get";
}
else{
	$role=$_GET["role"];
}
if(!isset($_GET["id"])){
	$id=0;
}
else{
	$id=$_GET["id"];
}
if(!isset($_GET["song"])){
	$song="";
}
else{
	$song=$_GET["song"];
}
if(!isset($_GET["artist"])){
	$artist="";
}
else{
	$artist=$_GET["artist"];
}
switch($role){
	case "form":
		echo '<form method="get">';
		echo 'Artist: <input type="text" name="artist" /><br />';
		echo 'Song: <input type="text" name="song" /><br />';
		echo '<input type="hidden" name="role" value="add" />';
		echo '<input type="submit" value="submit" />';
		echo '</form>';
		break;
	case "add":
		$db->setValue("song",$song);
		$db->setValue("artist",$artist);
		if($db->append()){
			echo "Added!";
		}
		else{
			echo "Not added :(";
		}
		break;
	case "get":
		$i=1;
		$res=$db->get();
		foreach($res as $result){
			echo "Title: <strong>".$result["song"]."</strong><br />";
			echo "Artist: <strong>".$result["artist"]."</strong><br />";
			echo "<a href=\"?role=modform&id=".$i."\">Change</a><br />";
			echo "<a href=\"?role=del&id=".$i."\">Delete</a><hr />";
			$i++;
		}
		break;
	case "modform":
		$db->setWhere("id", $id);
		$res=$db->get();
		$res=$res[$id];
		echo '<form method="get">';
		echo 'Artist: <input type="text" name="artist" value="'.$res["artist"].'" /><br />';
		echo 'Song: <input type="text" name="song" value="'.$res["song"].'" /><br />';
		echo '<input type="hidden" name="role" value="mod" />';
		echo '<input type="hidden" name="id" value="'.$id.'" />';
		echo '<input type="submit" value="submit" />';
		echo '</form>';
		break;
	case "mod":
		$db->setValue("song",$song);
		$db->setValue("artist",$artist);
		if($db->change($id)){
			echo "Changed!";
		}
		else{
			echo "Not changed :(";
		}
		break;
	case "delete":
		if($db->delete($id)){
			echo "Deleteted!";
		}
		else{
			echo "Not deleted :(";
		}
		break;
	default:
		break;
	
}
echo '<hr />';
echo '<strong>LINKS</strong>:<br /><br />';
echo '<a href="?role=form">Add a new song!</a><br />';
echo '<a href="?role=get">View the songs!</a><br />';