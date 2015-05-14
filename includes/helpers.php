<?php

function base_url()
{
	/* Get protocol information whether using secure or normal */
	$protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"], 0, 5)) == 'https://' ? 'https://' : 'http://';

	/* Call current script */
	$path = $_SERVER['PHP_SELF'];

	/**
	* returns arrays:
	* Array (
	*  [dirname] => /path/
	*  [basename] => script.php
	*  [extension] => php
	*  [filename] => script
	* )
	*/
	$path_parts = pathinfo($path);
	$directory = $path_parts['dirname'];

	/**
	* Replace backslash dirname,
	* If it's in main directory only return / if within folder return dirname/
	*/
	$directory = str_replace('\\', '', $directory);
	$directory = ($directory == "/") ? "/" : $directory .'/';

	/* return domain name (localhost / domain.com) */
	$host = $_SERVER['HTTP_HOST'];

	/* Final Output */
	return $protocol . $host . $directory;
}

function domain ()
{
	return parse_url(base_url(), PHP_URL_HOST);
}

function site_name ()
{
	$site_name = domain();
	$site_name = explode('.', $site_name);
	$site_name = array_slice($site_name, 0, count($site_name) - 1);
	$site_name = array_filter($site_name, 'remove_long_domain');
	$site_name = end($site_name);

	return $site_name;
}

function remove_long_domain ($val)
{
	if (strlen($val) > 4) return true;
}

function build_config ($config_file)
{
	require $config_file;
	$config = json_decode($config, true);
	$config['capitalize'] = ($config['capitalize'] === 'true') ? true: false;
	$config['boost.mode'] = ($config['boost.mode'] === 'true') ? true: false;
	$config['using.spinner'] = ($config['using.spinner'] === 'true') ? true: false;

	return $config;
}

function config ($name = '', $is_attr = false)
{
	global $config;
	if ($name !== '') return ($is_attr) ? htmlentities($config[$name]): $config[$name];

	return $config;
}

function theme_url ()
{
	return base_url() . CONTENTPATH . '/themes/' . config('theme') .'/';
}

function remove_empty_array ($arr)
{
	$arr = array_filter($arr);
	#$arr = array_diff($arr, array());
	$arr = array_combine(range(1, count($arr)), array_values($arr));
	return $arr;
}

function get_path ()
{
	global $path;
	return $path;
}

function first_path ()
{
	global $path;
	return $path[1];
}

function current_path ()
{
	global $path;
	return end($path);
}

function get_keyword ()
{
	$path = str_replace('_' . config('type'), '', current_path());
	return normalize_path($path);
}

function get_category ($url = false)
{
	$category = normalize_path(first_path());

	if ($url)
	{
		$is_pdf = (config('type') === 'pdf') ? '.pdf': '.html';
		return permalink_url($category);
	}

	return $category;
}

function normalize_path ($path)
{
	$path = normalize($path, false);
	return $path;
}

function capitalize ($str)
{
	return safe_ucfirst(safe_strtolower($str));
}

function normalize ($str, $ucwords = FALSE)
{
	$str = url_title($str, '_', false);
	$str = str_replace('-', ' ', $str);
	$str = humanize($str);
	$str = safe_strtolower($str);
	$str = trim($str);
	$str = ($ucwords) ? safe_ucwords($str): $str;

	return $str;
}

function humanize($str)
{
	return safe_ucwords(preg_replace('/[_]+/', ' ', safe_strtolower(trim($str))));
}

function utf8_uri_encode( $utf8_string, $length = 0 ) {
	$unicode = '';
	$values = array();
	$num_octets = 1;
	$unicode_length = 0;

	$string_length = strlen( $utf8_string );
	for ($i = 0; $i < $string_length; $i++ ) {

		$value = ord( $utf8_string[ $i ] );

		if ( $value < 128 ) {
			if ( $length && ( $unicode_length >= $length ) )
				break;
			$unicode .= chr($value);
			$unicode_length++;
		} else {
			if ( count( $values ) == 0 ) $num_octets = ( $value < 224 ) ? 2 : 3;

			$values[] = $value;

			if ( $length && ( $unicode_length + ($num_octets * 3) ) > $length )
				break;
			if ( count( $values ) == $num_octets ) {
				if ($num_octets == 3) {
					$unicode .= '%' . dechex($values[0]) . '%' . dechex($values[1]) . '%' . dechex($values[2]);
					$unicode_length += 9;
				} else {
					$unicode .= '%' . dechex($values[0]) . '%' . dechex($values[1]);
					$unicode_length += 6;
				}

				$values = array();
				$num_octets = 1;
			}
		}
	}

	return $unicode;
}

//taken from wordpress
function seems_utf8($str) {
	$length = strlen($str);
	for ($i=0; $i < $length; $i++) {
		$c = ord($str[$i]);
		if ($c < 0x80) $n = 0; # 0bbbbbbb
		elseif (($c & 0xE0) == 0xC0) $n=1; # 110bbbbb
		elseif (($c & 0xF0) == 0xE0) $n=2; # 1110bbbb
		elseif (($c & 0xF8) == 0xF0) $n=3; # 11110bbb
		elseif (($c & 0xFC) == 0xF8) $n=4; # 111110bb
		elseif (($c & 0xFE) == 0xFC) $n=5; # 1111110b
		else return false; # Does not match any model
		for ($j=0; $j<$n; $j++) { # n bytes matching 10bbbbbb follow ?
			if ((++$i == $length) || ((ord($str[$i]) & 0xC0) != 0x80))
				return false;
		}
	}
	return true;
}

function url_title($title, $separator = '-', $capitalize = FALSE)
{
	$title = strip_tags($title);
	$title = str_replace(array("\xe2\x80\x98", "\xe2\x80\x99", "\xe2\x80\x9c", "\xe2\x80\x9d", "\xe2\x80\x93", "\xe2\x80\x94", "\xe2\x80\xa6"), '', $title);
	// Preserve escaped octets.
	$title = preg_replace('|%([a-fA-F0-9][a-fA-F0-9])|', '---$1---', $title);
	// Remove percent signs that are not part of an octet.
	$title = str_replace('%', '', $title);
	// Restore octets.
	$title = preg_replace('|---([a-fA-F0-9][a-fA-F0-9])---|', '%$1', $title);

	if (seems_utf8($title)) {
		if (function_exists('mb_strtolower')) {
			$title = mb_strtolower($title, 'UTF-8');
		}
		$title = utf8_uri_encode($title, 200);
	}

	$title = preg_replace('/\`\~\!\@\#\$\%\^\&\*\(\)\_\+\-\=\<\>\?\:\"\{\}\|\,\.\/\;\[\]/', '', $title);
	$title = str_replace('.', $separator, $title);
	$title = preg_replace('/[^%a-z0-9 _-]/', '', $title);
	$title = ($capitalize) ? safe_ucwords($title): $title;
	$title = preg_replace('/\s+/', $separator, $title);
	$title = preg_replace('|-+|', $separator, $title);
	$title = rtrim($title, $separator);
	$title = trim($title);
	$title = stripslashes($title);
	$title = ($capitalize) ? $title: safe_strtolower($title);
	$title = urldecode($title);
	//$title = html_entity_decode($title, ENT_QUOTES, "UTF-8");
	return $title;
}

function permalink_url ($str, $replace_separator_to_space = false)
{
	$str = url_title($str, config('separator'), config('capitalize'));

	if ($replace_separator_to_space)
	{
		$str = str_replace(config('separator'), ' ', $str);
	}

	return $str;
}

function safe_string ($str)
{
	$str = url_title($str, '-');
	$str = str_replace('-', ' ', $str);
	return $str;
}

function safe_ucfirst($str)
{
	if (seems_utf8($str))
	{
		if (function_exists('mb_strtoupper'))
		{
			$encoding = "UTF-8";
			$strlen = mb_strlen($str, $encoding);
			$first_char = mb_substr($str, 0, 1, $encoding);
			$then = mb_substr($str, 1, $strlen - 1, $encoding);
			$str = mb_strtoupper($first_char, $encoding) . $then;
		}
	}
	else
	{
		$str = ucfirst($str);
	}

	return $str;
}

function safe_strtolower($str)
{
	if (seems_utf8($str))
	{
		if (function_exists('mb_convert_case'))
		{
			$str = mb_convert_case($str, MB_CASE_LOWER, "UTF-8");
		}
	}
	else
	{
		$str = strtolower($str);
	}

	return $str;
}

function safe_ucwords($str)
{
	if (seems_utf8($str))
	{
		if (function_exists('mb_convert_case'))
		{
			$str = mb_convert_case($str, MB_CASE_TITLE, "UTF-8");
		}
	}
	else
	{
		$str = ucwords($str);
	}

	return $str;
}

function safe_string_insert ($str, $type)
{
	$str = title_case(safe_string($str));

	switch ($type) {
		case 'title':
			if (strlen($str) < 3)
			{
				$str = "Untitled Document";
			}
			else if (strlen($str) > 3 && strlen($str) < 8)
			{
				$str = "Document " . $str;
			}
			break;
		case 'desc':
			if (strlen($str) < 3)
			{
				$str = "No Description";
			}
			else if (strlen($str) > 3 && strlen($str) < 8)
			{
				$str = "Document " . $str;
			}
			break;
	}

	return $str;
}


function get_header ()
{
	$file = CONTENTPATH .'/themes/'. config('theme') . '/header.php';
	if (file_exists($file))	include $file;
}

function get_footer ()
{
	$file = CONTENTPATH .'/themes/'. config('theme') . '/footer.php';
	if (file_exists($file))	include $file;
}

function get_sidebar ()
{
	$file = CONTENTPATH .'/themes/'. config('theme') . '/sidebar.php';
	if (file_exists($file))	include $file;
}

function get_file ($filename)
{
	$file = CONTENTPATH .'/themes/'. config('theme') . '/'. $filename .'.php';
	if (file_exists($file))	include $file;
}

function results ($key = '')
{
	global $results;

	if ($key !== '')
	{
		return $results[$key];
	}

	return $results;
}

function static_shuffle (&$items, $key)
{
	# Shuffle Active Spinner
	if (config('spinner.method') === 'static')
	{
		$ord = 0;
		$key = md5($key);

		foreach(str_split($key) as $str)
		{
			if(is_int($str))
			{
				$ord += intval($str);
			}
			else
			{
				$ord += intval(ord($str));
			}
		}

		mt_srand($ord);

		for ($i = count($items) - 1; $i > 0; $i--)
		{ 
			$j = @mt_rand(0, $i); 
			$tmp = $items[$i]; 
			$items[$i] = $items[$j]; 
			$items[$j] = $tmp; 
		}

	}
	else
	{
		shuffle($items);
	}

	return $items[0];
}

function spinner ($echo = true)
{
	if(location('result') && config('using.spinner'))
	{
		$spinner_config = 'content/spinner/conf.json';
		$spinner = explode(',', read_file($spinner_config));

		if (is_array($spinner) && empty($spinner))
		{
			return;
		}

		$title = title(true);
		$spin = static_shuffle($spinner, $title);
		$json = json_decode(read_file(CONTENTPATH . '/spinner/'. $spin . '.spinner'), true);
		$content = $json['content'];
		$content = replace_syntax($content);
		$content = str_replace('{keyword}', $title, $content);

		#randomize words
		$match_syntax = '/\{(.*?)\}/';

		/* Match all posibility */
		preg_match_all($match_syntax, $content, $matches, PREG_PATTERN_ORDER);
		$results = $matches[0];

		if(! empty($results))
		{
			foreach($results as $key => $result)
			{
				$sanitize_string = str_replace(array('{', '}'), '', $result);
				$arrays = explode('|', $sanitize_string);
				$permalink = false;
				if (in_array('generate_permalink', $arrays))
				{
					$arrays = array_diff($arrays, array('generate_permalink'));
					$arrays = remove_empty_array($arrays);
					$arrays = array_values($arrays);
					$permalink = true;
				}

				$random = static_shuffle($arrays, $title);

				if ($permalink)
				{
					$random = '<a href="'. generate_permalink($random, get_category()) .'">'. $random .'</a>';
				}
				$content = str_replace($result, $random, $content);
			}
		}

		if ($echo) echo $content;
		return $content;
	}
}

function related ()
{
	if (location('single')):
		global $related;
		$id = $related['id'];
		$keyword_id = $related['keyword_id'];
		$results = recent_document($keyword_id, array('type' => 'array', 'limit' => config('results'), 'exclude' => array($id)));
		return $results;
	endif;
}



function title ($only_keyword = FALSE)
{
	global $path;
	$title = '';

	if (! empty($path) && is_array($path))
	{
		$title = normalize(current_path());
		$title = str_replace(' '. config('type'), '', $title);

		switch (location('', false))
		{
			case 'category':
			if ($only_keyword == FALSE) {
				$title = sprintf(str_replace('{category}', '%s', config('category.title')), humanize(current_path()));
			}
			break;

			case 'sitemap':
			case 'page':
				$title = results('title');
			break;

			case 'result':
				if (! $only_keyword)
				{
					$title = sprintf(str_replace('{keyword}', '%s', config('result.title')), title_case($title));
				}
			break;

			case 'single':
			if ($only_keyword)
			{
				$title = normalize(results('title'));
			}
			else
			{
				$search = array('{keyword}', '{title}');
				$replace = normalize(results('title'));
				$title = str_replace($search, $replace, config('single.title'));
			}
			break;

			default:
			$title = config('index.title');
			break;
		}

		$title = str_replace('{category}', get_category(), $title);
	}
	else
	{
		$title = config('index.title');
	}

	$title = replace_syntax($title);
	$title = title_case ($title);
	return $title;
}

function ptitle ()
{
	$title = title(true);
	$title = title_case($title);
	return $title;
}

function description ()
{
	global $path;
	$title = '';

	if (! empty($path) && is_array($path))
	{
		$description = normalize(current_path());
		$description = str_replace(' '. config('type'), '', $title);

		switch (location('', false))
		{
			case 'category':
			$description = str_replace('{category}', humanize(get_category()), config('category.description'));
			break;

			case 'result':
			$result = results();
			mt_srand(make_seed());
			$rand_number = mt_rand(0, count($result) - 1);
			$result = $result[$rand_number];
			$description = str_replace('{keyword}', title_case($description), config('result.title'));
			$description = str_replace('{description}', trim($result['description']), config('result.description'));
			break;

			case 'single':
			$title = title_case(normalize(results('title')));
			$search = array('{keyword}', '{title}', '{description}');
			$replace = array($title, $title, results('description'));
			$description = str_replace($search, $replace, config('single.description'));
			break;

			case 'sitemap':
			case 'page':
			$description = '';
			break;

			default:
			$description = config('index.description');
			break;
		}

		$description = str_replace('{category}', humanize(get_category()), $description);
	}
	else
	{
		$description = config('index.description');
	}

	$description = str_replace(array(
		'{site_name}',
		'{domain}',
		'{site_url}'
		), array(
		site_name(),
		domain(),
		base_url()
		), $description);

	return $description;
}

function make_seed ()
{
	$str = current_url();
	$str = str_split($str);
	$ord = 0;
	foreach ($str as $arr)
	{
		$ord += ord($arr);
	}

	return $ord;
}

function get_cat_permalink ($name)
{
	return base_url() . permalink_url($name) .'/';
}

function permalink ($link)
{
	$is_pdf = (config('type') === 'pdf') ? '.pdf': '.html';

	# category
	if (isset($link['icon'])) {
		return base_url() . permalink_url($link['name']) .'/';
	} else {
		if (isset($link['keyword'])) {
			if (! location('result')) {
				$link = permalink_url($link['keyword']) . $is_pdf;
				$link = current_path_url() . $link;
				return $link;
			}
			elseif(! location ('single')) {
				$link = current_url() .'?'. config('single_var') .'=' . $link['id'];
				return $link;
			}
		}
	}
}

function location ($is = '', $check = true)
{
	global $path;
	$location = 'home';
	
	if (! empty($path) && is_array($path))
	{
		switch (count($path))
		{
			case 1:
			if (end($path) === 'search')
			{
				$location = 'search';
			}
			else if(end($path) === 'search_xml')
			{
				$location = 'opensearch';
			}
			else
			{
				$location = 'category';
			}
			break;

			case 2:
			if (first_path() === 'p')
			{
				$page = array ('about', 'copyrights', 'privacy', 'terms', 'contact', 'faq');
				if (in_array(end($path), $page))
				{
					$location = 'page';
				}
				else
				{
					$location = '404';
				}
			}
			else if (first_path() === 'sitemaps')
			{
				$sitemap = range('A', 'Z');
				array_push($sitemap, 'numeric');

				if (in_array(end($path), $sitemap))
				{
					$location = 'sitemap';
				}
				else
				{
					$location = '404';
				}
			}
			else
			{
				$url = array_keys($_GET);
				if (substr($url[0], -1) === '/' || end(explode('_',$url[0])) !== config('type'))
				{
					$location = '404';
				}
				else
				{
					if (isset($_GET[config('single_var')])) 
					{
						$location = 'single';
					}
					else
					{
						$location = 'result';
					}
				}
			}
			break;

			case 0:
			default:
			$location = '404';
			break;
		}
	}

	if ($check)
	{
		return $is === $location;
	}

	return $location;
}

function current_url ()
{
	return base_url() . first_path() .'/'. str_replace('_'. config('type'), '.'. config('type'), current_path());
}

function current_path_url ()
{
	return base_url() . first_path() .'/';
}

function get_search_term ()
{
	if (! array_key_exists('HTTP_REFERER', $_SERVER)) return;
	$referer = $_SERVER['HTTP_REFERER'];
	$domain = parse_url($referer, PHP_URL_HOST);
	$search_phrase = '';
	$engines = array(
		/*'localhost' => 'q=',*/
		'dmoz'	=> 'q=',
		'aol'	=> 'q=',
		'ask'	=> 'q=',
		'google'=> 'q=',
		'bing'	=> 'q=',
		'hotbot'=> 'q=',
		'teoma'	=> 'q=',
		'yahoo'	=> 'p=',
		'altavista'=> 'p=',
		'lycos'	=> 'query=',
		'kanoodle' => 'query='
	);

	foreach($engines as $engine => $query_param)
	{
		if (strpos($domain, $engine) !==  false && strpos($referer, $query_param) !==  false)
		{
			$referer .= "&";
			$pattern = "/[?&]{$query_param}(.*?)&/si";
			preg_match($pattern, $referer, $matches);
			$search_phrase = urldecode($matches[1]);
			# return array($engine, $search_phrase);
			$search_phrase = safe_string($search_phrase);
			$search_phrase = clean_words($search_phrase);
			$search_phrase = safe_strtolower($search_phrase);
			return $search_phrase;
		}
	}
	return;
}

function directory_map ($source_dir, $directory_depth = 0, $hidden = FALSE)
{
	if ($fp = @opendir($source_dir))
	{
		$filedata   = array();
		$new_depth  = $directory_depth - 1;
		$source_dir = rtrim($source_dir, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;

		while (FALSE !== ($file = readdir($fp)))
		{
			/* Remove '.', '..', and hidden files [optional] */
			if ( ! trim($file, '.') OR ($hidden == FALSE && $file[0] == '.'))
			{
				continue;
			}

			if (($directory_depth < 1 OR $new_depth > 0) && @is_dir($source_dir.$file))
			{
				$filedata[$file] = directory_map($source_dir.$file.DIRECTORY_SEPARATOR, $new_depth, $hidden);
			}
			else
			{
				$filedata[] = $file;
			}
		}

		closedir($fp);
		return $filedata;
	}

	return FALSE;
}

function write_file($path, $data, $mode = FOPEN_WRITE_CREATE_DESTRUCTIVE)
{
	if ( ! $fp = @fopen($path, $mode))
	{
		return FALSE;
	}

	flock($fp, LOCK_EX);
	fwrite($fp, $data);
	flock($fp, LOCK_UN);
	fclose($fp);

	return TRUE;
}

function sanitize_query ($q)
{
	$arr_q = explode('_', $q);
	if (end($arr_q) !== config('type'))
	{
		return NULL;
	}
	else
	{
		$q = str_replace('_' . config('type'), '', $q);
		$q = explode(config('separator'), $q);
		$q = implode(' ', $q);
		$q = normalize($q);
		return $q;
	}
}

function search_bing ($q)
{
	require 'bing_crawler.php';

	$q = sprintf(str_replace('{q}', '%s', config('search.query')), $q);

	if (config('method') === 'api')
	{
		/* random api key */
		$api_key = config('bing.api');
		$api_key = explode("\r\n", $api_key);
		shuffle($api_key);
		$api_key = end($api_key);

		$bing = new Bing_Crawler(array(
			'q'			=> $q,
			'method'	=> 'api',
			'api_key'	=> $api_key,
			'count'		=> config('results')
			));
		$result = $bing->result();
		return $result;
	}
	else {
		$bing = new Bing_Crawler(array(
			'q'			=> $q,
			'method'	=> 'proxy',
			'count'		=> config('results')
			));
		$result = $bing->result();
		return $result;
	}

	return NULL;
}

function widget ($query, $options)
{
	$options = array_extend(array('prefix' => 'ul', 'parent_class'=>'square', 'item' => 'li', 'echo' => true, 'type' => 'html'), $options);
	global $db;
	$results = $db->query($query)->result();
	$is_pdf = (config('type') === 'pdf') ? '.pdf': '.html';

	if (! empty($results))
	{
		if ($options['type'] === 'html')
		{
			$parent_class = 'class="'.$options['parent_class'].'"';
			$output = '<'.$options['prefix'].' '.$parent_class.'>';
			foreach($results as $result)
			{
				if ($result['cat_id'] !== 'NULL')
				{
					$category = permalink_url($result['category']);
					$link = $category .'/'. permalink_url($result['keyword']) . $is_pdf;
					$output .= '<'.$options['item'].'>';
					$output .= '<a href="'. base_url() . $link.'" title="'. title_case($result['keyword']) .'">'. title_case($result['keyword']) .'</a>';
					$output .= '</'.$options['item'].'>';
				}
			}
			$output .= '</'.$options['prefix'].'>';

			if ($options['echo']) {
				echo $output;
			} else {
				return $output;
			}
		}
		else
		{
			return $results;
		}
	}
}

function recent ($options = array('limit' => 15))
{
	return widget("SELECT `kw`.`id` as `id`, `kw`.`keyword` as `keyword`, `kw`.`time` as `time`, `kw`.`cat_id` as `cat_id`, `cat`.`name` as `category` FROM `keywords` as `kw` LEFT JOIN `cat` as `cat` ON `cat`.`id`= `kw`.`cat_id` ORDER BY `kw`.`time` DESC LIMIT 0, {$options['limit']}", $options);
}

function popular ($options = array('prefix' => 'ul', 'parent_class'=>'square', 'item' => 'li', 'echo' => true, 'type' => 'html'))
{
	widget("SELECT `kw`.`keyword` as `keyword`, `kw`.`count` as `count`, `kw`.`cat_id` as `cat_id`, `cat`.`name` as `category` FROM `keywords` as `kw` LEFT JOIN `cat` as `cat` ON `cat`.`id`= `kw`.`cat_id` ORDER BY `kw`.`count` DESC LIMIT 0, 15", $options);
}

function random ($options = array())
{
	$options = array_extend(array('prefix' => 'ul', 'parent_class'=>'square', 'item' => 'li', 'echo' => true, 'type' => 'html', 'limit' => 5), $options);
	global $db;
	$results = $db->query("SELECT `cat`.`name` as `category`, `kw`.`keyword` as `keyword`, `kw`.`id` as `keyword_id`, `i`.`id` as `id`, `kw`.`cat_id` as `cat_id`, `i`.`title` FROM `index` as `i` LEFT JOIN `keywords` as `kw` ON `i`.`keyword_id` = `kw`.`id` LEFT JOIN `cat` as `cat` ON `kw`.`cat_id` = `cat`.`id` WHERE `keyword_id` IN (SELECT `kw`.`id` as `keyword_id` FROM `keywords` as `kw` LEFT JOIN `cat` as `cat` ON `cat`.`id`= `kw`.`cat_id` ORDER BY `kw`.`count` DESC) ORDER BY rand() LIMIT 0, {$options['limit']}")->result();
	$is_pdf = (config('type') === 'pdf') ? '.pdf': '.html';

	if (! empty($results))
	{
		if ($options['type'] === 'html')
		{
			$parent_class = 'class="'.$options['parent_class'].'"';
			$output = '<'.$options['prefix'].' '.$parent_class.'>';
			$results = array_map("unserialize", array_unique(array_map("serialize", $results)));
			foreach($results as $result)
			{
				$category = permalink_url($result['category']);
				$link = $category .'/'. permalink_url($result['keyword']) . $is_pdf .'?'. config('single_var') .'='. $result['id'];
				$output .= '<'.$options['item'].'>';
				$output .= '<a href="'. base_url() . $link.'" title="'. title_case($result['title']) .'" rel="nofollow">'. title_case($result['title']) .'</a>';
				$output .= '</'.$options['item'].'>';
			}
			$output .= '</'.$options['prefix'].'>';

			if ($options['echo']) {
				echo $output;
			} else {
				return $output;
			}
		} else {
			return $results;
		}
	}
}

function generate_permalink_url ($keyword, $category = '', $id = '')
{
	if ($category === '') $category = get_category();
	$is_pdf = (config('type') === 'pdf') ? '.pdf': '.html';
	$category = permalink_url($category);
	$keyword = permalink_url($keyword);
	$view = ($id !== '') ? '?'. config('single_var') .'='. $id: '';
	return base_url() . $category .'/'. $keyword . $is_pdf . $view;
}

function generate_permalink ($keyword, $category = '', $id = '')
{
	if ($category === '') $category = get_category();
	$is_pdf = (config('type') === 'pdf') ? '.pdf': '.html';
	$category = permalink_url($category);
	$keyword = permalink_url($keyword);
	$view = ($id !== '') ? '?'. config('single_var') .'='. $id: '';

	if (config('boost.mode') || location('single') || location('sitemap') || $id !== '') {
		return base_url() . $category .'/'. $keyword . $is_pdf . $view;
	}
	return "#$keyword\" id=\"$keyword"; 
}

function recent_document ($keyword_id, $options = array())
{
	$options = array_extend(array('prefix' => 'ul', 'parent_class'=>'square', 'item' => 'li', 'echo' => true, 'type' => 'html', 'limit' => 3, 'exclude' => array()), $options);
	global $db;
	$results = $db->query("SELECT `cat`.`name` as `category`, `kw`.`keyword` as `keyword`, `kw`.`id` as `keyword_id`, `i`.`id` as `id`, `i`.`title`, `i`.`description`, `i`.`url`, `i`.`time` FROM `index` as `i` LEFT JOIN `keywords` as `kw` ON `i`.`keyword_id` = `kw`.`id` LEFT JOIN `cat` as `cat` ON `kw`.`cat_id` = `cat`.`id` WHERE `keyword_id` = '{$keyword_id}' LIMIT 0, {$options['limit']}")->result();
	$is_pdf = (config('type') === 'pdf') ? '.pdf': '.html';

	if (! empty($results))
	{
		if ($options['type'] === 'html')
		{
			$parent_class = 'class="'.$options['parent_class'].'"';
			$output = '<'.$options['prefix'].' itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem" '.$parent_class.'>';
			foreach($results as $result)
			{
				if (! in_array($result['id'], $options['exclude'])):
				$category = permalink_url($result['category']);
				$link = base_url() . $category .'/'. permalink_url($result['keyword']) . $is_pdf .'?'. config('single_var') .'='. $result['id'];
				$new_keyword = permalink_url($result['title']);
				$output .= '<'.$options['item'].' itemprop="item" itemscope itemtype="http://schema.org/Thing">';
				$output .= '<h3 itemprop="name"><a href="'. generate_permalink($result['title'], $category) .'" title="'. title_case($result['title']) .'" rel="nofollow">'. title_case($result['title']) .'</a></h3>';
				$output .= '<a itemprop="url" href="'. $link .'" title="'. title_case($result['title']) .'" rel="nofollow" class="tiny button"><i class="fa fa-book"></i> Read</a>';
				$output .= '<div itemprop="description" class="desc">'. $result['description'] .'</div>';
				$output .= '<a href="'. $link .'" title="'. title_case($result['title']) .'" rel="nofollow" class="read"><i class="fa fa-external-link-square"></i> '. $link .'</a>';
				$output .= '</'.$options['item'].'>';
				endif;
			}
			$output .= '</'.$options['prefix'].'>';

			if ($options['echo']) {
				echo $output;
			} else {
				return $output;
			}
		}
		else
		{
			$output = array();
			foreach($results as $result):
				if (! in_array($result['id'], $options['exclude'])):
					array_push($output, $result);
				endif;
			endforeach;
			return $output;
		}
	}
}


function show_item ($list)
{
	global $db;
	$count = config("results");
	$results = $db->query("SELECT * FROM `keywords` WHERE `cat_id` = '{$list['id']}' ORDER BY  `time` DESC  LIMIT 0,5")->result();
	$is_pdf = (config('type') === 'pdf') ? '.pdf': '.html';

	if (! empty($results))
	{
		$output = '';
		foreach($results as $result)
		{
			if ($result['cat_id'] !== 'NULL')
			{
				$category = permalink_url($list['name']);
				$link = $category .'/'. permalink_url($result['keyword']) . $is_pdf;
				$output .= '<a href="'. base_url() . $link.'" title="'. title_case($result['keyword']) .'" rel="nofollow">'. title_case($result['keyword']) .'</a>, ';
			}
		}
		$output = rtrim($output, ', ');

		return $output;
	}
}

function sitemaps ($options = array('prefix' => 'ul', 'parent_class'=>'square', 'item' => 'li', 'echo' => true))
{
	$parent_class = 'class="'.$options['parent_class'].'"';
	$output = '<'.$options['prefix'].' '.$parent_class.'>';

	$output .= '<'.$options['item'].'>';
	$output .= '<a href="'. base_url() . 'sitemaps/numeric" title="Sitemap order by numeric">0-9</a>';
	$output .= '</'.$options['item'].'>';

	foreach (range('A', 'Z') as $list)
	{
		$link = 'sitemaps/'. $list;
		$output .= '<'.$options['item'].'>';
		$output .= '<a href="'. base_url() . $link.'" title="Sitemap order by '. $list .'">'. $list .'</a>';
		$output .= '</'.$options['item'].'>';
	}
	$output .= '</'.$options['prefix'].'>';

	echo $output;
}

function json_escape ($value)
{
	$value = stripslashes($value);
	$escapers = array("\n", "\r", "\t", "\x08", "\x0c");
	$replacements = array("\\n", "\\r", "\\t",  "\\f",  "\\b");
	$result = str_replace($escapers, $replacements, $value);
	return $result;
}

function sort_by($field, &$array, $direction = 'asc')
{
	usort($array, create_function('$a, $b', '
		$a = $a["' . $field . '"];
		$b = $b["' . $field . '"];

		if ($a == $b)
		{
			return 0;
		}

		return ($a ' . ($direction == 'desc' ? '>' : '<') .' $b) ? -1 : 1;
		'));

	return true;
}

function array_extend($a, $b) {
	foreach($b as $k=>$v) {
		if( is_array($v) ) {
			if( !isset($a[$k]) ) {
				$a[$k] = $v;
			} else {
				$a[$k] = array_extend($a[$k], $v);
			}
		} else {
			$a[$k] = $v;
		}
	}
	return $a;
}

function random_color ($str = '')
{
	$color = ($str === '') ? title(true): $str;
	$color = str_split($color);
	shuffle($color);
	$color = end($color);
	$color = ord($color);
	$color = str_split($color);
	shuffle($color);
	$color = end($color);

	return $color;
}

function get_id ()
{
	$id = '';

	if (location("single"))
	{
		$id = config('single_var');
		$id = (isset($_GET[$id]))? $_GET[$id]: '';
	}

	return $id;
}

function read_permalink ($id, $keyword = '', $category = '')
{
	if ($keyword === '' && $category === '')
	{
		$url = current_url();
		$url .= '?'. config('single_var') .'=' . $id;
	}
	else
	{
		$url = generate_permalink($keyword, $category, $id);
	}
	

	return $url;
}

function breadcrumbs ($nav = '')
{
	global $path;
	$nav = ($nav === '')? ' <i class="fa fa-chevron-right"></i> ': ' '. $nav .' ';
	$output = '<span itemscope itemtype="http://data-vocabulary.org/Breadcrumb"><a itemprop="url" href="'.base_url().'" title="'.config('index.title').'"><span itemprop="title">Home</span></a></span> '. $nav;

	$category = title_case(get_category());
	$category_url = base_url() . get_category();

	$keyword = title_case(get_keyword());
	$keyword_url = generate_permalink_url(get_keyword());

	$nav_cat = '<span itemscope itemtype="http://data-vocabulary.org/Breadcrumb"%s><a itemprop="url" href="'. $category_url .'"><span itemprop="title">'. $category .'</span></a></span> ';
	$nav_kwd = '<span itemscope itemtype="http://data-vocabulary.org/Breadcrumb"%s><a itemprop="url" href="'. $keyword_url .'"><span itemprop="title">'. $keyword .'</span></a></span> ';

	switch (location('', false)) {
		case 'category':
			$output .= sprintf($nav_cat, ' class="current-page"');
		break;

		case 'result':
			$output .= sprintf($nav_cat, '') . $nav;
			$output .= sprintf($nav_kwd, ' class="current-page"');
		break;

		case 'single':
			$output .= sprintf($nav_cat, '') . $nav;
			$output .= sprintf($nav_kwd, '') . $nav;
			$output .= '<span itemscope itemtype="http://data-vocabulary.org/Breadcrumb" class="current-page"><a itemprop="url" href="'. generate_permalink(get_keyword(), get_category(), get_id()) .'"><span itemprop="title">'. ptitle() .'</span></a></span>';
		break;
	}

	$output = '<div itemprop="breadcrumb">'. $output.'</div>';

	return $output;
}

function clean_words ($str)
{
	$str = preg_replace('%(?:(?:https?|ftp)://)?(?:\S+(?::\S*)?@)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\x{00a1}-\x{ffff}0-9]-*)*[a-z\x{00a1}-\x{ffff}0-9]+)(?:\.(?:[a-z\x{00a1}-\x{ffff}0-9]-*)*[a-z\x{00a1}-\x{ffff}0-9]+)*(?:\.(?:[a-z\x{00a1}-\x{ffff}]{2,})))(?::\d{2,5})?(?:/\S*)?%u', '', $str);
	$rurl = '/(http(s)?(:\/\/)?)?(w{3}|www2|asset(s)?|files|cdn|blog)?\.(.[^\.]*)\.(.[^.]+)?/';
	$str = preg_replace($rurl, '$6', $str);
	$words = explode(',', config('clean.words'));
	$words = array_map('clean_wordsfn', $words);
	$words = implode($words, '|');
	$str = preg_replace("/$words/", "", $str);

	return $str;
}

function clean_wordsfn ($val)
{
	$val = preg_replace('/[^\w\s]/', '', $val);
	$val = '\b'.$val.'(s)?\b';
	return $val;
}

function bad_words ($str)
{
	$words = explode(',', config('bad.words'));

	if ($str !== '')
	{
		$str = explode(' ', $str);
		$output = array();
		foreach ($str as $arr)
		{
			if (in_array($arr, $words))
			{
				return true;
			}
		}
	}

	return false;
}

function recursive_remove_directory ($directory)
{
	foreach(glob("{$directory}/*") as $file)
	{
		if (strpos($file, 'index.html') === FALSE) {
			if(is_dir($file)) { 
				recursive_remove_directory($file);
			} else {
				unlink($file);
			}
		}
	}
}

function logo_url ($default = '')
{
	$logo = config('logo');

	if (! empty($logo))
	{
		return base_url() . 'content/logo/'. $logo;
	}

	return theme_url() . $default;
}

function head ()
{
	$domain = domain();
	$base_url = base_url();
	$header_script = config('header.script');
	$keyword = '';
	$description = '';
	$microdata = '';

	if(location('result')):
		$keyword = '<meta name="keywords" content="'. title(true) .'">';
	endif;

	if(location('home') ||location('category') ||location('result') || location('single')):
		$description = '<meta itemprop="description" name="description" content="'. description() .'">';
	endif;

	echo <<<SCRIPT
	<link rel="search" type="application/opensearchdescription+xml" title="{$domain} Search" href="{$base_url}search.xml" />
	{$keyword}{$microdata}{$description}{$header_script}
SCRIPT;
}

function footer ()
{
	$footer_script = config('footer.script');
	echo <<<SCRIPT
	{$footer_script}
SCRIPT;
}

function get_categories ()
{
	global $db;
	$result = $db->query("SELECT * FROM `cat` ORDER BY `name` ASC LIMIT 0, 100000")->result();
	return $result;
}

function get_categories_map ($arr)
{
	$title = title_case($arr['name']);
	$arr['name'] = $title;
	return $arr;
}

function get_keywords ($category_name)
{
	global $db;
	$result = $db->query("SELECT * FROM `keywords` WHERE `cat_id` = (SELECT `id` FROM `cat` WHERE LOWER(`name`) LIKE '%{$category_name}%' LIMIT 0,1) LIMIT 0, 100000")->result();
	return $result;
}

function get_keywords_map ($arr)
{
	$keyword = title_case($arr['keyword']);
	$arr['keyword'] = $keyword;
	return $arr;
}

function read_file ($file)
{
	if ( ! file_exists($file))
	{
		return FALSE;
	}

	if (function_exists('file_get_contents'))
	{
		return file_get_contents($file);
	}

	if ( ! $fp = @fopen($file, FOPEN_READ))
	{
		return FALSE;
	}

	flock($fp, LOCK_SH);

	$data = '';
	if (filesize($file) > 0)
	{
		$data =& fread($fp, filesize($file));
	}

	flock($fp, LOCK_UN);
	fclose($fp);

	return $data;
}

function is_login ()
{
	if (isset($_SESSION['login']) && $_SESSION['login'] === 'yes') return true;
	return false;
}

function replace_syntax ($content)
{
	$content = str_replace(array(
		'{site_name}',
		'{domain}',
		'{site_url}'
	), array(
		site_name(),
		domain(),
		base_url()
	), $content);

	return $content;
}

function get_count ($category_id = '')
{
	global $db;
	$query = "SELECT COUNT(*) as `count` FROM `index`";
	if (! empty($category_id))
	{
		$query = "SELECT COUNT(*) as `count` FROM `index` WHERE `keyword_id` IN (SELECT `id` FROM `keywords` WHERE `cat_id` = '{$category_id}')";
	}
	$result = $db->query($query)->result();

	if (count($result) > 0) {
		return $result[0]['count'];
	}
}

function get_keyword_count ()
{
	global $db;
	$query = "SELECT COUNT(*) as `count` FROM `keywords`";
	$result = $db->query($query)->result();

	if (count($result) > 0) {
		return $result[0]['count'];
	}
}

function title_case ($title)
{
	$regx = '/<(code|var)[^>]*>.*?<\/\1>|<[^>]+>|&\S+;/';
	preg_match_all($regx, $title, $html, PREG_OFFSET_CAPTURE);
	$title = preg_replace ($regx, '', $title);
	$q_left = chr(8216);
	$q_right = chr(8217);
	$double_q = chr(8220);

	preg_match_all ('/[\w\p{L}&`\''. $q_left . $q_right .'"'. $double_q .'\.@:\/\{\(\[<>_]+-? */u', $title, $m1, PREG_OFFSET_CAPTURE);
	foreach ($m1[0] as &$m2) {
		list ($m, $i) = $m2;
		$i = mb_strlen (substr ($title, 0, $i), 'UTF-8');
		
		$m = $i>0 && mb_substr ($title, max (0, $i-2), 1, 'UTF-8') !== ':' && 
			!preg_match ('/[\x{2014}\x{2013}] ?/u', mb_substr ($title, max (0, $i-2), 2, 'UTF-8')) && 
			 preg_match ('/^(a(nd?|s|t)?|b(ut|y)|en|for|i[fn]|o[fnr]|t(he|o)|vs?\.?|via)[ \-]/i', $m)
		? mb_strtolower ($m, 'UTF-8')
		: (	preg_match ('/[\'"_{(\['. $q_left . $double_q .']/u', mb_substr ($title, max (0, $i-1), 3, 'UTF-8'))
		? mb_substr ($m, 0, 1, 'UTF-8').
			mb_strtoupper (mb_substr ($m, 1, 1, 'UTF-8'), 'UTF-8').
			mb_substr ($m, 2, mb_strlen ($m, 'UTF-8')-2, 'UTF-8')
		: (	preg_match ('/[\])}]/', mb_substr ($title, max (0, $i-1), 3, 'UTF-8')) ||
			preg_match ('/[A-Z]+|&|\w+[._]\w+/u', mb_substr ($m, 1, mb_strlen ($m, 'UTF-8')-1, 'UTF-8'))
		? $m
		: mb_strtoupper (mb_substr ($m, 0, 1, 'UTF-8'), 'UTF-8').
			mb_substr ($m, 1, mb_strlen ($m, 'UTF-8'), 'UTF-8')
		));
		
		$title = mb_substr ($title, 0, $i, 'UTF-8').$m. mb_substr ($title, $i+mb_strlen ($m, 'UTF-8'), mb_strlen ($title, 'UTF-8'), 'UTF-8');
	}

	foreach ($html[0] as &$tag) $title = substr_replace ($title, $tag[0], $tag[1], 0);
	return $title;
}

function download_url ($title)
{
	return base_url() . 'download.php?file=' . url_title($title, '-', false) .'&format=pdf';
}

function open_url ($url, $user_agent = '', $referer = '')
{
	if (empty($user_agent))
	{
		ob_start();
		include 'ua.txt';
		$user_agent = ob_get_clean();
		$user_agent = explode("\n", $user_agent);
		shuffle($user_agent);
		$user_agent = ob_get_clean();
		$user_agent = explode("\n", $user_agent);
		shuffle($user_agent);
		$user_agent = str_replace(array("\n", "\r", "\n\r"), '', end($user_agent));
	}

	$process = curl_init($url);
	curl_setopt($process, CURLOPT_TIMEOUT, 30);
	curl_setopt($process, CURLOPT_VERBOSE, 0);
	curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($process, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);
	if (!empty($referer)) curl_setopt($process, CURLOPT_REFERER, $referer);
	curl_setopt($process, CURLOPT_USERAGENT, $user_agent);
	$results = curl_exec($process);
	curl_close($process);

	return $results;
}

function pathjoin()
{
	$args = func_get_args();
	$paths = array();
	foreach ($args as $arg) {
		$paths = array_merge($paths, (array)$arg);
	}

	$paths = array_map(create_function('$p', 'return trim($p, "/");'), $paths);
	$paths = array_filter($paths);
	return join('/', $paths);
}

/** EOF */