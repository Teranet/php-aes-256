<?php

class Aes256{
	/*
	 *
	 * This Class makes API intergration easier by managing
	 * the Http requests
	 * and data encryption
	 */
	private $key;
	private $params;
	private $response;



	public function __construct($key){
		$this->key = $key;

	}	



	/*
	 * Request function manages all the calls to the api and returns
	 * the api response
	 * @JSON data - Data param is of type json, expected by the api
	 * @String endpoint - The api endpoint to be called
	 * @return json. We return the json data as documented on the api
	 */

	public function request($data,$endpoint){
		$requestData = $this->encrypt($data);
		$serverRes = $this->curlWrapper($requestData,$endpoint);
		$jsonRes = $this->decrypt($serverRes);
		return $jsonRes;


	}






	/*
	 *        Encryt data before sending it to the client.
	 *        PARAMS:
	 *        @data - Data to be encrypted. Often json data
	 *        @key  - Encryption key. saved in config file
	 *
	 */

	private function encrypt($data){
		$encryption_key = base64_decode($this->key);
		// Generate an initialization vector
		$iv = openssl_random_pseudo_bytes(
			openssl_cipher_iv_length('aes-256-cbc'));
		// Encrypt the data using AES 256 encryption in CBC mode
		// using our encryption key and initialization vector.
		$encrypted = openssl_encrypt(
			$data, 'aes-256-cbc', $encryption_key, 0, $iv);
		// The $iv is just as important as the key for decrypting, 
		// so save it with our encrypted data using
		// a unique separator (::)
		return base64_encode($encrypted . '::' . $iv);
	}







 /*
    Decrypt received data
    @Data -received data
    @key  Encryption key. saved in config file
  */

	private function decrypt($data){
		// Remove the base64 encoding from our key
		$encryption_key = base64_decode($this->key);
		// To decrypt, split the encrypted data from our IV - 
		// our unique separator used was "::"
		list($encrypted_data, $iv) = explode(
			'::', base64_decode($data), 2);
		return openssl_decrypt(
			$encrypted_data, 'aes-256-cbc',
		       	$encryption_key, 0, $iv);



	}


	/*
	 * Curl helper. This function makes the http/s 
	 * calls to the endpoints
	 * @String data - Encrypted data to the api
	 * @String endpoint - Http endpoint
	 * @return String - Encrypted response from the server
	 */
	private function curlWrapper($data,$url){
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_POST, 1);   //0 for a get request
		curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT ,3);
		curl_setopt($ch,CURLOPT_TIMEOUT, 20);
		$response = curl_exec($ch);
		curl_close ($ch);
		return $response;

	}

}
?>

