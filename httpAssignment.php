<?PHP
error_reporting(E_ALL);
$host = $argv[1];
/* Get the port for the WWW service. */
$service_port = getservbyname('www', 'tcp');

/* Get the IP address for the target host. */
$address = gethostbyname($host);

/* Create a TCP/IP socket. */
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
	if ($socket === false) {
	    echo "socket_create() failed: reason: " . 
	         socket_strerror(socket_last_error()) . "\n";
	}

	echo "Attempting to connect to '$address' on port '$service_port'...\n";
	$result = socket_connect($socket, $address, $service_port);
		if ($result === false) {
		    echo "socket_connect() failed.\nReason: ($result) " . 
		          socket_strerror(socket_last_error($socket)) . "\n";
		}

		$in = "";
		$in .= "HEAD / HTTP/1.1\r\n";
		$in .= "Host: $host\r\n";
		$in .= "Connection: Close\r\n\r\n";
		
		$out = '';
		$response = '';

		echo "Sending HTTP HEAD request...";
		socket_write($socket, $in, strlen($in));
			echo "OK.\n";

			echo "Reading response:\n\n";
			while ($out = socket_read($socket, 2048)) {
				$response .= $out;
			}

			$seperated_response = explode(" ",$response,2);
			
			$html_version = $seperated_response[0];
			$rest = $seperated_response[1];
			
			$seperated_rest = explode("\n",$rest,2);
			$status_code = $seperated_rest[0];
			$response_message = explode("\n",$seperated_rest[1]);	

			$response = array();
			echo "\n\n$html_version\n $status_code\n\n";
			
			foreach ($response_message as $res) {
				$arr = explode(":",$res,2);
				if(sizeof($arr)>=2){
					$response[$arr[0]] = $arr[1];
				}
			}

			foreach($response as $key => $value){
				if($key!=NULL || $value!=NULL){
					echo "$key :  $value\n";
				}
			}

socket_close($socket);
?>