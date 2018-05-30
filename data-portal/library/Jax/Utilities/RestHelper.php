<?php
class Jax_Utilities_RestHelper implements Jax_Utilities_Interface
{
	/* (non-PHPdoc)
	 * @see Jax_Utilities_Interface::run()
	 */
	public static function run() {
		$fparams = func_get_args();
		$url = $fparams[0];
		
		$params = @$fparams[1]; // null
		if (!isset($fparams[1])) $params = NULL;
		
		$verb = @$fparams[2]; // GET
		if (!isset($fparams[2])) $verb = "GET";
		
		$format = @$fparams[3]; // json
		if (!isset($fparams[3])) $format = "json";

		$cparams = array(
			'http' => array(
			    'method' => $verb,
			    'ignore_errors' => true
			)
		);
		  
		if ($params !== null) {
			$params = http_build_query($params);
		    if ($verb == 'POST') {
		    	$cparams['http']['content'] = $params;
		    } else {
		    	$url .= '?' . $params;
		    }
		}
		
		if ($format == "redirect") header("Location: $url");
		
		$context = stream_context_create($cparams);
		
		$fp = @fopen($url, 'rb', false, $context);
		if (!$fp) {
			$res = false;
		} else {
		    // If you're trying to troubleshoot problems, try uncommenting the
		    // next two lines; it will show you the HTTP response headers across
		    // all the redirects:
		    
		     //$meta = stream_get_meta_data($fp);
		     //var_dump($meta['wrapper_data']);
		    $res = stream_get_contents($fp);
		}
		
		if ($res === false) {
			throw new Exception("$verb $url failed: ".@$php_errormsg);
		}
		
		switch ($format) {
			case 'json':
			    $r = json_decode($res);
			    if ($r === null) {
			    	throw new Exception("failed to decode $res as json");
			    }
		    return $r;
		
		    case 'xml':
		    	$r = simplexml_load_string($res);
		    	if ($r === null) {
		        	throw new Exception("failed to decode $res as xml");
		      	}
		    return $r;
		    
		}
		
		return $res;
	}
}