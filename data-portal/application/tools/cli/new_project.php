<?php
setlocale(LC_ALL, "en_US");
require_once '../../../library/Jax/System/Admin.php';
define("APPLICATION_PATH", realpath("../../"));

$vars = array(
		"##NS##"=>array("Enter a namespace for your project.\r\nMust be alphabetic with no spaces and length of 2-5 letters"),
		"##APP_NAME##"=>array("Enter your project's name"),
		"##COMPANY##"=>array("Enter your company's name (your name if not applicable"),
		"##DB_HOST##"=>array("Enter MySQL server host (IP or servername"),
		"##DB_PORT##"=>array("Enter MySQL port. (3306 is default)"),
		"##DB_NAME##"=>array("Enter MySQL database name"),
		"##DB_USER##"=>array("Enter MySQL username"),
		"##DB_PASSWORD##"=>array("Enter MySQL password")
);

$appCfg = array();
$skipTo = null;
$loop = true;


while($loop){
	foreach ($vars as $tag=>$tcfg){
		if(!is_null($skipTo) && $tag != $skipTo) continue;
		
		print "\r\n".$tcfg[0]." :\r\n";
		$h = fopen("php://stdin","r");
		$idata = trim(fgets($h));
		
		switch ($tag){
			case "##NS##":
				$skipTo = null;
				
				$idata = ucfirst(strtolower(str_replace(" ", "", $idata)));
				if(!ctype_alpha($idata) || strlen($idata) < 2 || strlen($idata) > 5) {
					die("\r\nMust be 2-5 chars and consist of alphabetic chars only!");
				}
				
				if(!Jax_System_Admin::checkNS($idata)){
					print "\r\nNamespace is reserved or in use. Please try another.\r\n";
					$skipTo = $tag;
				}
				
				break;
				
			default:
				$skipTo = null;
				
				if(empty($idata)){
					$skipTo = $tag;
					break 2;
				}
				
				break;
		}
		
		if(is_null($skipTo)){
			print "Confirm? Y/N\r\n";
			$ch = fopen("php://stdin","r");
			$c = trim(fgets($ch));
			
			if(strtolower($c) == "y"){
				$appCfg[$tag] = $idata;
			} else {
				$skipTo = $tag;
				break;
			}
		}
		//Jax_System_Admin::new_project($appCfg);die("\r\n\r\nPEND");
	}
	if (is_null($skipTo)) $loop = false;
}

print "\r\nCreating project, please wait...\r\n";


$jax = Jax_System_Admin::new_project($appCfg);

if($jax->success){
	print "Project \"".$appCfg['##APP_NAME##']."\" created successfully!";
} else {
	die($jax->error);
}