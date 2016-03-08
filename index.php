<?php
	############################################################
	####### Script made by Helder Rodrigues#####################
	####### a11027@alunos.ipca.pt###############################
	####### version 0.1#########################################
	####### Dont remove credits#################################
	############################################################
	
	
	ob_start();
	$location = '/bad'; // where to send bad people
	$decision = false;
	
	//Functions
	function get_client_ip() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
       $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
	}
	
	//Calculate IP RANGE
	function ip_in_range( $ip, $range ) {
	if ( strpos( $range, '/' ) == false ) {
		$range .= '/32';
	}
	
	// $range is in IP/CIDR format eg 127.0.0.1/24
	list( $range, $netmask ) = explode( '/', $range, 2 );
	$range_decimal = ip2long( $range );
	$ip_decimal = ip2long( $ip );
	$wildcard_decimal = pow( 2, ( 32 - $netmask ) ) - 1;
	$netmask_decimal = ~ $wildcard_decimal;
	return ( ( $ip_decimal & $netmask_decimal ) == ( $range_decimal & $netmask_decimal ) );
	}
	
	//ALL IPS
	function ipRange($ip){
		$handle = fopen("ips.txt", "r");
		if ($handle) {
			while (($line = fgets($handle)) !== false) {
				$ranger =  ip_in_range($ip, $line);
				if($ranger == 1){
					return true;
				}
			}

			fclose($handle);
		} 
		return false;
	}
	
	//rest of code
	/*$ports = array(8080,80,81,1080,6588,8000,3128,553,554,4480);
    foreach($ports as $port) {
         if (@fsockopen($_SERVER['REMOTE_ADDR'], $port, $errno, $errstr, 30)) {
              echo "You are using a proxy!";
         }
     }*/
	
	$remoteaddr=$_SERVER["REMOTE_ADDR"];
	$xforward= $_SERVER["HTTP_X_FORWARDED_FOR"];
	if (empty($xforward)) {
		//user is NOT using proxy
		echo "You are not using proxy, your real IP address is: $remoteaddr";
		}
		else {
		echo "You are using a proxy, your proxy server IP is $remoteaddr while your real IP address is $xforward";
		$decision = true;
		}
	 $hostname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
	 echo "$hostname";
	 $ip = get_client_ip();
	 if($hostname == $ip){
		 $decision = true;
	 }
	$homepage = file_get_contents('http://www.shroomery.org/ythan/proxycheck.php?ip=$ip');
	echo $homepage;
	//checking content of shroomery to check if it is proxy or not
	if($homepage == "Y"){
		$decision = true;
	}
	
	//checking IP range of list of IP's
	 $rangeIPS = ipRange($ip);
	 if($rangeIPS){
		 $decision = true;
	 }
	 
	 //checking where is supposed to send the visit of your website
	 if($decision == false)
	 {
		 $location = "/good";
	 }	
		header('HTTP/1.1 301 Moved Permanently');
		header('Location: '.$location);
	 ?>
	 
