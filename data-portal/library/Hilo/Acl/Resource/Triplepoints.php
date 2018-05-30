<?php
class Hilo_Acl_Resource_Triplepoints extends Hilo_Acl_Resource
{
	protected $_resourceId = "Triplepoints";
		
	public function __construct(){
		$this->_config[self::DISPLAY_NAME] = 'Triple Points';
		$this->_config[self::ICON] = 'icon-cart-2';
	}
	
	public function listTcpPromos(){
		Jax_Utilities_ResourceAccessChecker::run($this->_resourceId,Jax_Acl_Constants::ACCESS_READ);
		
		$db = Jax_Data_Source::getInstance()->getDb();
		
		$tbls = $db->query("SHOW TABLES")->fetchAll();
		
		$tables = array();
		foreach($tbls as $tbl){
			foreach($tbl as $h=>$tnam){
				
				$tp = explode("_", $tnam);
				if(strtolower(substr($tp[0], 0,3)) == "tcp" && count($tp) == 2){
					$tables[] = $tnam;
				}
			}
		}
		
		sort($tables);
		
		return $tables;
	}
	
	protected function weeklyEmailProc(Zend_Controller_Request_Abstract $request){
		Jax_Utilities_ResourceAccessChecker::run($this->_resourceId,Jax_Acl_Constants::ACCESS_UPDATE);
		
		$sess = new Zend_Session_Namespace();
		$tbl = $sess->TCPTBL;
		
		$tfnam = ucfirst(strtolower(str_replace("_", "", $tbl)));
		
		$wtClass = "Hilo_Models_".$tfnam."w";
		
		$weTbl = new $wtClass();
		
		$data = Jax_Data_Source::getInstance()->getRecord($tfnam."f","`opt`='Y' AND `AcctNo` NOT IN (SELECT `AcctNo` FROM `".$weTbl->info('name')."`)",true);
	
		$emailsent = 0;
		
		$config = array('auth' => 'login',
				'username' => 'noreply@hilofoodstores.com',
				'password' => 'N0r3ply');
			
		$tr = new Zend_Mail_Transport_Smtp('127.0.0.1',$config);
		
		Zend_Mail::setDefaultTransport($tr);
		
		foreach($data as $row){
			extract($row);		

			$content = "";
			
			$mail = new Zend_Mail();
			
			//send email

			$mymail   = "noreply@hilofoodstores.com";
			$subject  = "3x Point Wednesdays Promotion Update";
			$content .= "<!DOCTYPE HTML>\n";
			$content .= "<html>\n";
			$content .= "<head>\n";
			$content .= "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">\n";
			$content .= "</head>\n";
			$content .= "<body>\n";
			$content .= "<table width=\"900\">\n";
			$content .= "  <tr>\n";
			$content .= "  	<td>\n";
			
			if(intval(date("m")) == 1 || intval(date("m")) == 12){
				$content .= "<p>Happy New Year $FirstName $LastName,</p>";
			} else {
				$content .= "<p>Hello $FirstName $LastName,</p>";
			}
			
			$content .= "<p>Thank you for registering for our latest Triple Points promotion. This e-mail provides an update of your spending to date as it relates to your targets to qualify for Triple Points every Wednesday in the month of March 2014.</p>";
			$content .= "<p align=\"center\">YOUR SPENDING TARGETS <br/> as at ".date("Y-m-d",strtotime($lupd))."</p>";
			$content .="<p align=\"center\">***************************************************</p>";
			
			$content .= '<div align="center"><table border="0" width="40%">
					<tr><td></td><td>Jan 2014</td><td>Feb 2014</td><td>Total</td></tr>
					<tr><td>Target:</td><td>$'.number_format($mth1t,2,'.','').'</td><td>$'.number_format($mth2t,2,'.','').'</td><td>$'.number_format(($mth1t+$mth2t),2,'.','').'</td></tr>
					<tr><td>Actual:</td><td>$'.number_format($mth1a,2,'.','').'</td><td>$'.number_format($mth2a,2,'.','').'</td><td>$'.number_format(($mth1a+$mth2a),2,'.','').'</td></tr>
					<tr><td>Balance:</td><td></td><td></td><td>$'.number_format((($mth1t+$mth2t)-($mth1a+$mth2a)),2,'.','').'</td></tr>
					</table></div>';
			
			//$content .="<p align=\"center\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Jan &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;       	  Feb &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;       	 Total    </p>";
			
			//$content .="<p align=\"center\">&nbsp; Target:&nbsp;&nbsp;  $". number_format($mth1t,2,'.','') . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;      $" . number_format($mth2t,2,'.','') . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;       $" . number_format(($mth1t+$mth2t),2,'.','') . "   </p>";
			
			//$content .="<p align=\"center\">Actual:&nbsp;&nbsp;&nbsp; $" . number_format($mth1a,2,'.','') . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;       $" . number_format($mth2a,2,'.','') . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;          $" . number_format(($mth1a+$mth2a),2,'.','') . "   </p>";
			
			//$content .="<p align=\"center\">&nbsp;Balance: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; $" . number_format((($mth1t+$mth2t)-($mth1a+$mth2a)),2,'.','') . "   </p>";
			
			$content .="<p align=\"center\">***************************************************</p>";
			$content .="<p>Continue shopping and use your Smart Shopper Card every time you shop at any of our Hi Lo Food Stores to work towards achieving your targets.</p>";
			$content .="<p>Remember Triple Points Every Wednesday in March 2014 could be yours.  You could earn 3 points for every $10 you spend on the 4 Wednesdays in March 2014.</p>";
			$content .="<p>What a fantastic benefit!</p>";
			$content .="<p>Our Family serving Your Family!</p>";
			$content .= "    <p>&nbsp;</p><p><strong>N.B.: Please do not reply to this email, as we are not able to respond to messages sent to this address.</strong></p>";
			$content .= "    </td>\n";
			$content .= "  </tr>\n";
			$content .= "</table>\n";
			$content .= "</body>\n";
			$content .= "</html>\n";
			$header   = "From: Hi-Lo Food Stores -  <noreply@hilofoodstores.com>\nReply-To:<noreply@hilofoodstores.com>\r\n";
			$header  .= "X-Mailer:PHP/".phpversion()."\r\n";
			$header  .= "Mime-Version: 1.0\r\n";
			$header  .= "Content-type: text/html; charset=iso-8859-1\r\n";
			
			/*
			 * PREVIOUS METHOD
			 * if(@mail($clientemail, $subject, $content, $header)){
				$errCount=0;
				$emailsent+=1;
			}else{
				$errCount=1;
			}
			$content = "";
			$header   = "";*/
			
			$mail
			->addTo($Email)
			->setFrom($mymail,"Hi-Lo Food Stores")
			->setBodyHtml($content)
			->setSubject($subject)
			->send();
			
			Jax_Data_Source::getInstance()->addRecord($tfnam."w",array('AcctNo'=>$AcctNo));
		
			$emailsent++;
		}
		Jax_System_Logger::log(new Jax_LogEntry("Notify",Hilo_Acl_Constants::RESOURCE_TRIPLEPOINTS,"Weekly emails sent for 3x promo",Jax_Acl_Constants::ACCESS_UPDATE),Jax_Auth::getAuthId());
		
		return Jax_Response::Valid($emailsent);
	}
	
	protected function weeklyEmailTestProc(Zend_Controller_Request_Abstract $request){
		Jax_Utilities_ResourceAccessChecker::run($this->_resourceId,Jax_Acl_Constants::ACCESS_UPDATE);
	
		$sess = new Zend_Session_Namespace();
		$tbl = $sess->TCPTBL;
		
		
	
		$tfnam = ucfirst(strtolower(str_replace("_", "", $tbl)));
		
		$accts = "(42000999892)";
		
		$wtClass = "Hilo_Models_".$tfnam."w";
		
		$weTbl = new $wtClass();
	
		$data = Jax_Data_Source::getInstance()->getRecord($tfnam."f","`AcctNo` IN $accts AND `AcctNo` NOT IN (SELECT `AcctNo` FROM `".$weTbl->info('name')."`)",true);
	
		$emailsent = 0;
		
		
		$config = array('auth' => 'login',
				'username' => 'noreply@hilofoodstores.com',
				'password' => 'N0r3ply');
		
		$tr = new Zend_Mail_Transport_Smtp('127.0.0.1',$config);
		
		Zend_Mail::setDefaultTransport($tr);
		
		
		foreach($data as $row){
			extract($row);
	
			$content = "";
				
			$mail = new Zend_Mail();
				
			//send email
	
			$mymail   = "noreply@hilofoodstores.com";
			$subject  = "3x Point Wednesdays Promotion Update";
			$content .= "<!DOCTYPE HTML>\n";
			$content .= "<html>\n";
			$content .= "<head>\n";
			$content .= "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">\n";
			$content .= "</head>\n";
			$content .= "<body>\n";
			$content .= "<table width=\"900\">\n";
			$content .= "  <tr>\n";
			$content .= "  	<td>\n";
				
			if(intval(date("m")) == 1 || intval(date("m")) == 12){
				$content .= "<p>Happy New Year $FirstName $LastName,</p>";
			} else {
				$content .= "<p>Hello $FirstName $LastName,</p>";
			}
				
			$content .= "<p>Thank you for registering for our latest Triple Points promotion. This e-mail provides an update of your spending to date as it relates to your targets to qualify for Triple Points every Wednesday in the month of March 2014.</p>";
			$content .= "<p align=\"center\">YOUR SPENDING TARGETS <br/> as at ".date("Y-m-d",strtotime($lupd))."</p>";
			$content .="<p align=\"center\">***************************************************</p>";
				
			$content .= '<div align="center"><table border="0" width="40%">
					<tr><td></td><td>Jan 2014</td><td>Feb 2014</td><td>Total</td></tr>
					<tr><td>Target:</td><td>$'.number_format($mth1t,2,'.','').'</td><td>$'.number_format($mth2t,2,'.','').'</td><td>$'.number_format(($mth1t+$mth2t),2,'.','').'</td></tr>
					<tr><td>Actual:</td><td>$'.number_format($mth1a,2,'.','').'</td><td>$'.number_format($mth2a,2,'.','').'</td><td>$'.number_format(($mth1a+$mth2a),2,'.','').'</td></tr>
					<tr><td>Balance:</td><td></td><td></td><td>$'.number_format((($mth1t+$mth2t)-($mth1a+$mth2a)),2,'.','').'</td></tr>
					</table></div>';
				
			//$content .="<p align=\"center\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Jan &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;       	  Feb &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;       	 Total    </p>";
				
			//$content .="<p align=\"center\">&nbsp; Target:&nbsp;&nbsp;  $". number_format($mth1t,2,'.','') . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;      $" . number_format($mth2t,2,'.','') . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;       $" . number_format(($mth1t+$mth2t),2,'.','') . "   </p>";
				
			//$content .="<p align=\"center\">Actual:&nbsp;&nbsp;&nbsp; $" . number_format($mth1a,2,'.','') . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;       $" . number_format($mth2a,2,'.','') . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;          $" . number_format(($mth1a+$mth2a),2,'.','') . "   </p>";
				
			//$content .="<p align=\"center\">&nbsp;Balance: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; $" . number_format((($mth1t+$mth2t)-($mth1a+$mth2a)),2,'.','') . "   </p>";
				
			$content .="<p align=\"center\">***************************************************</p>";
			$content .="<p>Continue shopping and use your Smart Shopper Card every time you shop at any of our Hi Lo Food Stores to work towards achieving your targets.</p>";
			$content .="<p>Remember Triple Points Every Wednesday in March 2014 could be yours.  You could earn 3 points for every $10 you spend on the 4 Wednesdays in March 2014.</p>";
			$content .="<p>What a fantastic benefit!</p>";
			$content .="<p>Our Family serving Your Family!</p>";
			$content .= "    <p>&nbsp;</p><p><strong>N.B.: Please do not reply to this email, as we are not able to respond to messages sent to this address.</strong></p>";
			$content .= "    </td>\n";
			$content .= "  </tr>\n";
			$content .= "</table>\n";
			$content .= "</body>\n";
			$content .= "</html>\n";
			$header   = "From: Hi-Lo Food Stores -  <noreply@hilofoodstores.com>\nReply-To:<noreply@hilofoodstores.com>\r\n";
			$header  .= "X-Mailer:PHP/".phpversion()."\r\n";
			$header  .= "Mime-Version: 1.0\r\n";
			$header  .= "Content-type: text/html; charset=iso-8859-1\r\n";
				
			/*
			 * PREVIOUS METHOD
			* if(@mail($clientemail, $subject, $content, $header)){
			$errCount=0;
			$emailsent+=1;
			}else{
			$errCount=1;
			}
			$content = "";
			$header   = "";*/
				
			$mail
			->addTo($Email)
			->setFrom($mymail,"Hi-Lo Food Stores")
			->setBodyHtml($content)
			->setSubject($subject)
			->send();
	
			Jax_Data_Source::getInstance()->addRecord($tfnam."w",array('AcctNo'=>$AcctNo));
			
			$emailsent++;
		}
		Jax_System_Logger::log(new Jax_LogEntry("Notify",Hilo_Acl_Constants::RESOURCE_TRIPLEPOINTS,"Weekly emails sent for 3x promo",Jax_Acl_Constants::ACCESS_UPDATE),Jax_Auth::getAuthId());
	
		return Jax_Response::Valid($emailsent);
	}
	
	protected function pExtractProc(Zend_Controller_Request_Abstract $request){
		
		Jax_Utilities_ResourceAccessChecker::run($this->_resourceId,Jax_Acl_Constants::ACCESS_UPDATE);
		
		$full = false;
		$params = $request->getParams();
		if(isset($params['all'])) $full = true;
		
		$sess = new Zend_Session_Namespace();
		$tbl = $sess->TCPTBL;
		
		$tfnam = ucfirst(strtolower(str_replace("_", "", $tbl)));
		
		if(!$full){
			$where = "`proc`='N'";
		} else {
			$where = null;
		}
		
		$data = Jax_Data_Source::getInstance()->getRecord($tfnam."e",$where,true);
		
		if(!$full) {
			Jax_Data_Source::getInstance()->updateRecord($tfnam."e","`proc`='N'",array('proc'=>'Y'));
			
			Jax_System_Logger::log(new Jax_LogEntry("Export",Hilo_Acl_Constants::RESOURCE_TRIPLEPOINTS,"Partial email extract processed.",Jax_Acl_Constants::ACCESS_UPDATE),Jax_Auth::getAuthId());
			
		} else {
			Jax_System_Logger::log(new Jax_LogEntry("Export",Hilo_Acl_Constants::RESOURCE_TRIPLEPOINTS,"Full email extract processed.",Jax_Acl_Constants::ACCESS_UPDATE),Jax_Auth::getAuthId());
		}
		
		
		
		header('Content-type: text/csv');
		header('Content-disposition: attachment;filename='.$tfnam.'_emails_'.date("YmdHis").'.csv');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	
		$header = false;
		foreach($data as $row){
				
			$head = "";
			$rd = "";
			foreach ($row as $h=>$v){
				
				if(!$header){
					$head .= "$h,";
					$rd .= "$v,";
				} else {
					$rd .= "$v,";
				}
			}
				
			if(!$header){
				echo substr($head, 0,strlen($head)-1)."\r\n";
				$header = true;
			}
				
			echo substr($rd, 0,strlen($rd)-1);

			
			echo "\r\n";
		}
		
		
		die();
	}
}