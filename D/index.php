<?php

define("ROOT", dirname(__FILE__));

$debug= false;
if($debug){
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
}

//	admin

class Admin {
	public static $theme = "";
	public static $root = "";
	public static $user = null;
	public static $debug_sql = false;

	public static function includeCSS(){
		$path = "res/css/";
		$handle=opendir($path);
		if($handle){
			while (false !== ($entry = readdir($handle)))  {
				if($entry!="." && $entry!=".."){
					$fullpath = $path.$entry;
				if(!is_dir($fullpath)){
						echo "<link rel='stylesheet' type='text/css' href='".$fullpath."' />";

					}
				}
			}
		closedir($handle);
		}

	}

	public static function alert($text){
		echo "<script>alert('".$text."');</script>";
	}

	public static function redir($url){
		echo "<script>window.location='".$url."';</script>";
	}

	public static function includeJS(){
		$path = "res/js/";
		$handle=opendir($path);
		if($handle){
			while (false !== ($entry = readdir($handle)))  {
				if($entry!="." && $entry!=".."){
					$fullpath = $path.$entry;
				if(!is_dir($fullpath)){
						echo "<script type='text/javascript' src='".$fullpath."'></script>";

					}
				}
			}
		closedir($handle);
		}

	}

}


//	view



class View {
	
	public static function load($view){
		// Module::$module;
		if(!isset($_GET['view'])){
			if(Admin::$root==""){
				include "admin/".$view."-view.php";
			}else if(Admin::$root=="admin"){
				include "admin/".Admin::$theme."/view/".$view."-view.php";				
			}
		}else{


			if(View::isValid()){
				$url ="";
			if(Admin::$root==""){
			$url = "admin/".$_GET['view']."-view.php";
			}else if(Admin::$root=="admin"){
			$url = "admin/".Admin::$theme."/view/".$_GET['view']."-view.php";				
			}
				include $url;				
			}else{
				View::Error("<b>404 NOT FOUND</b> View <b>".$_GET['view']."");
			}



		}
	}

	
	public static function isValid(){
		$valid=false;
		if(isset($_GET["view"])){
			$url ="";
			if(Admin::$root==""){
			$url = "admin/".$_GET['view']."-view.php";
			}else if(Admin::$root=="admin"){
			$url = "admin/".Admin::$theme."/view/".$_GET['view']."-view.php";				
			}
			if(file_exists($file = $url)){
				$valid = true;
			}
		}
		return $valid;
	}

	public static function Error($message){
		print $message;
	}

}



//	module

class Module {

	public static function loadMenu(){
		if(Admin::$root==""){
		include "admin/menu.php";
		}else if(Admin::$root=="admin"){
		include "admin/".Admin::$theme."menu.php";
		}
	}


}

//	database

class Database {
	public static $banco;
	public static $conexao;
	function Database(){
		$this->user="root";$this->pass="";$this->host="localhost";$this->dbancob="0002050";
	}

	function connect(){
		$conexao = new mysqli($this->host,$this->user,$this->pass,$this->dbancob);
		$conexao->query("set sql_mode=''");
		return $conexao;
	}

	public static function getCon(){
		if(self::$conexao==null && self::$banco==null){
			self::$banco = new Database();
			self::$conexao = self::$banco->connect();
		}
		return self::$conexao;
	}
	
}

//executor

class Executor {

	public static function doit($sql){
		$conexao = Database::getCon();
		if(Admin::$debug_sql){
			print "<pre>".$sql."</pre>";
		}
		return array($conexao->query($sql),$conexao->insert_id);
	}
}


//	model

class Model {

	public static function exists($modelname){
		$fullpath = self::getFullpath($modelname);
		$found=false;
		if(file_exists($fullpath)){
			$found = true;
		}
		return $found;
	}

	public static function getFullpath($modelname){
		return Admin::$root."admin/".$modelname.".php";
	}

	public static function many($query,$aclass){
		$cnt = 0;
		$array = array();
		while($r = $query->fetch_array()){
			$array[$cnt] = new $aclass;
			$cnt2=1;
			foreach ($r as $key => $v) {
				if($cnt2>0 && $cnt2%2==0){ 
					$array[$cnt]->$key = $v;
				}
				$cnt2++;
			}
			$cnt++;
		}
		return $array;
	}
	
	public static function one($query,$aclass){
		$cnt = 0;
		$found = null;
		$data = new $aclass;
		while($r = $query->fetch_array()){
			$cnt=1;
			foreach ($r as $key => $v) {
				if($cnt>0 && $cnt%2==0){ 
					$data->$key = $v;
				}
				$cnt++;
			}

			$found = $data;
			break;
		}
		return $found;
	}

}

// SESSION


class Session{
	public static function setUID($uid){
		$_SESSION['ID_PERFUSU'] = $uid;
	}

	public static function unsetUID(){
		if(isset($_SESSION['ID_PERFUSU']))
			unset($_SESSION['ID_PERFUSU']);
	}

	public static function issetUID(){
		if(isset($_SESSION['ID_PERFUSU']))
			return true;
		else return false;
	}

	public static function getUID(){
		if(isset($_SESSION['ID_PERFUSU']))
			return $_SESSION['ID_PERFUSU'];
		else return false;
	}

}


//LB

class Lb {

	public function Lb(){
	}

	public function start(){
	function __autoload($modelname){
	if(Model::exists($modelname)){
		include Model::getFullPath($modelname);
	} 
}

if(isset($_SESSION["ID_PERFUSU"])){
	Admin::$user = UsuarioData::getById($_SESSION["ID_PERFUSU"]);
}

if(!isset($_GET["action"])){
	Module:: loadMenu("index");
}else{
	Action::load($_GET["action"]);
}
	}

}



ob_start();
session_start();
Admin::$root="";


$lb = new Lb();
$lb->start();

?>