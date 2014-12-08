<?php
if (! class_exists('Hashids')) require 'Hashids.php';

class Bing_Crawler {

	var $result = NULL;

	public function __construct ($options = array('q' => '', 'method' => 'api', 'api_key' => '', 'count' => 20))
	{
		# required files

		# get user agent
		ob_start();
		include 'ua.txt';
		$user_agent = ob_get_clean();
		$user_agent = explode("\n", $user_agent);
		shuffle($user_agent);
		$user_agent = str_replace(array("\n", "\r", "\n\r"), '', end($user_agent));

		# set default response
		$response = array();

		if ($options['method'] === 'api')
		{
			$url = 'https://api.datamarket.azure.com/Bing/Search/Web?$format=json&Query=%27'. rawurlencode($options['q']) .'%27';
			$process = curl_init($url);
			curl_setopt($process, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($process, CURLOPT_USERPWD, $options['api_key'].':'.$options['api_key']);
			curl_setopt($process, CURLOPT_TIMEOUT, 30);
			curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($process, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($process, CURLOPT_USERAGENT, $user_agent);
			$results = curl_exec($process);
			curl_close($process);

			$results = json_decode($results, true);

			if (isset($results['d']) && isset($results['d']['results']) && count($results['d']['results']) > 0)
			{
				$response = $results['d']['results'];

				if (! empty($response))
				{
					$response = array_map(array($this, 'arr_filter_bing'), $response);
					$response = array_diff($response, array(''));
					$response = array_combine(range(1, count($response)), array_values($response));
				}
			}
		}
		else
		{
			$url = 'https://www.bing.com/search?q='. urlencode($options['q']) .'&go=&form=QBRE&qs=n&sk=&format=rss&count='. $options['count'];
			$process = curl_init();
			curl_setopt($process, CURLOPT_URL, $url);
			curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($process, CURLOPT_PROXY, "127.0.0.1:9050");
			curl_setopt($process, CURLOPT_PROXYTYPE, 7);
			curl_setopt($process, CURLOPT_HEADER, 0); 
			curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);
			//curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1); 
			curl_setopt($process, CURLOPT_VERBOSE, 1);
			curl_setopt($process, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($process, CURLOPT_USERAGENT, $user_agent);
			$results = curl_exec($process);
			curl_close($process);

			$xml = new SimpleXMLElement($results);
			$json = json_encode($xml);
			$results = json_decode($json, TRUE);

			if (isset($results['channel']) && isset($results['channel']['item']) && count($results['channel']['item']) > 0)
			{
				$response = $results['channel']['item'];

				if (! empty($response))
				{
					$response = array_map(array($this, 'arr_filter_bing_proxy'), $response);
					$response = array_diff($response, array(''));
					$response = array_combine(range(1, count($response)), array_values($response));
				}
			}
		}

		if (! empty($response))
		{
			$this->result = $response;
		}

		return $response;
	}

	function result ()
	{
		return $this->result;
	}

	function arr_filter_bing ($arr)
	{
		$url = $arr['Url'];
		$eurl = explode('.', $url);
		if (array_key_exists('Description', $arr) && array_key_exists('Title', $arr) && array_key_exists('Url', $arr) && end($eurl) === 'pdf')
		{
			$description = ucwords(clean_words(normalize(strip_tags($arr['Description']))));
			$title = ucwords(clean_words(normalize(strip_tags($arr['Title']))));

			if (! bad_words($title) && ! bad_words($description) && str_word_count($title) > 1)
			{
				$hash = new Hashids(md5($url), 15);
				$output = array(
					'id'			=> $hash->encrypt(1),
					'description'	=> $description,
					'title'			=> $title,
					'url'			=> $url,
				);
				
				return $output;
			}
		}
	}

	function arr_filter_bing_proxy ($arr)
	{
		$url = $arr['link'];
		$eurl = explode('.', $url);
		if (array_key_exists('description', $arr) && array_key_exists('title', $arr) && array_key_exists('link', $arr) && end($eurl) === 'pdf')
		{
			$description = ucwords(clean_words(normalize(strip_tags($arr['description']))));
			$title = ucwords(clean_words(normalize(strip_tags($arr['title']))));

			if (! bad_words($title) && ! bad_words($description) && str_word_count($title) > 1)
			{
				$hash = new Hashids(md5($url), 15);
				$output = array(
					'id'			=> $hash->encrypt(1),
					'description'	=> $description,
					'title'			=> $title,
					'url'			=> $url,
				);
				
				return $output;
			}
			
		}
	}
}
