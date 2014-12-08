<?php

class Crawler {
	var $user_agent;
	public function __construct($user_agent = '')
	{
        if ($user_agent == '')
        {
			ob_start();
			include('ua.txt');
			$user_agent = ob_get_clean();
			$user_agent = explode("\n", $user_agent);
			shuffle($user_agent);
			$this->user_agent = str_replace(array("\n", "\r", "\n\r"), '', $user_agent[0]);
        }
        else
        {
            $this->user_agent = $user_agent;
        }
	}

	public function run ($api_key, $query)
	{
        $url = 'https://api.datamarket.azure.com/Bing/Search/Web?$format=json&Query=%27'. rawurlencode($query) .'%27&WebFileType=%27PDF%27';
        echo $url;die();

		$process = curl_init($url);
		curl_setopt($process, CURLOPT_USERAGENT, $this->user_agent);
		curl_setopt($process, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($process, CURLOPT_USERPWD, "$api_key:$api_key");
		curl_setopt($process, CURLOPT_TIMEOUT, 30);
		curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($process, CURLOPT_SSL_VERIFYPEER, FALSE);
		$response = curl_exec($process);
		
		# Have a great day!
		curl_close($process);

		# Deliver
		return $response;
    }
}
?>