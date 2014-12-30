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

function config ($name = '')
{
	global $config;
	if ($name !== '') return $config[$name];

	return $config;
}

function theme_url ()
{
	return base_url() . CONTENTPATH . '/themes/' . config('theme') .'/';
}

function remove_empty_array ($arr)
{
	$arr = array_diff($arr, array(''));
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
	return normalize_path (str_replace('_'.config('type'), '', current_path()));
}

function get_category ($url = false)
{
	$category = normalize_path(first_path());;
	if ($url) {
		$is_pdf = (config('type') === 'pdf') ? '.pdf': '.html';
		$is_capitalize = (config('capitalize')) ? false: true;
		return url_title($category, config('separator'), $is_capitalize);
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
	return ucfirst(strtolower($str));
}

function normalize ($str, $ucwords = FALSE)
{
	$str = url_title($str, '_', true);
	$str = str_replace('-', ' ', $str);
	$str = humanize($str);
	$str = strtolower($str);
	$str = trim($str);
	$str = ($ucwords) ? ucwords($str): $str;

	return $str;
}

function humanize($str)
{
	return ucwords(preg_replace('/[_]+/', ' ', strtolower(trim($str))));
}

function url_title($str, $separator = '-', $lowercase = FALSE)
{
	if ($separator == '-')
	{
		$search		= '_';
		$replace	= '-';
	}
	else
	{
		$search		= '-';
		$replace	= '_';
	}

	$str = preg_replace('/\./msU', '-', $str);
	$trans = array(
		'&\#\d+?;' => '',
		'&\S+?;' => '',
		'\s+' => $replace,
		'[^a-z0-9\-\._]' => '',
		$replace.'+' => $replace,
		$replace.'$' => $replace,
		'^'.$replace => $replace,
		'\.+$' => ''
		);

	$str = strip_tags($str);

	if ($lowercase === FALSE)
	{
		$str = ucwords($str);
	}

	foreach ($trans as $key => $val)
	{
		$str = preg_replace("#".$key."#i", $val, $str);
	}

	if ($lowercase === TRUE)
	{
		$str = strtolower($str);
	}

	$str = rtrim($str, $separator);

	return trim(stripslashes($str));
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
				if ($only_keyword)
				{
					$title = ucwords($title);
				}
				else
				{
					$title = sprintf(str_replace('{keyword}', '%s', config('result.title')), ucwords($title));
				}
			break;

			case 'single':
			if ($only_keyword)
			{
				$title = ucwords(normalize(results('title')));
			}
			else
			{
				$search = array('{keyword}', '{title}');
				$replace = ucwords(normalize(results('title')));
				$title = str_replace($search, $replace, config('single.title'));
			}
			break;

			default:
			$title = config('index.title');
			break;
		}

		$title = str_replace('{category}', ucwords(get_category()), $title);
	}
	else
	{
		$title = config('index.title');
	}

	$title = replace_syntax($title);
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
			$description = str_replace('{keyword}', ucwords($description), config('result.title'));
			$description = str_replace('{description}', trim($result['description']), config('result.description'));
			break;

			case 'single':
			$title = ucwords(normalize(results('title')));
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
	$is_capitalize = (config('capitalize')) ? false: true;
	return base_url() . url_title($name, config('separator'), $is_capitalize) .'/';
}

function permalink ($link)
{
	$is_pdf = (config('type') === 'pdf') ? '.pdf': '.html';
	$is_capitalize = (config('capitalize')) ? false: true;

	# category
	if (isset($link['icon'])) {
		return base_url() . url_title($link['name'], config('separator'), $is_capitalize) .'/';
	} else {
		if (isset($link['keyword'])) {
			if (! location('result')) {
				$link = url_title($link['keyword'], config('separator'), $is_capitalize) . $is_pdf;
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
			return normalize(clean_words($search_phrase));
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
	$is_capitalize = (config('capitalize')) ? false: true;

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
					$category = url_title($result['category'], config('separator'), $is_capitalize);
					$link = $category .'/'. url_title($result['keyword'], config('separator'), $is_capitalize) . $is_pdf;
					$output .= '<'.$options['item'].'>';
					$output .= '<a href="'. base_url() . $link.'" title="'. ucwords($result['keyword']) .'">'. ucwords($result['keyword']) .'</a>';
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
	$is_capitalize = (config('capitalize')) ? false: true;

	if (! empty($results))
	{
		if ($options['type'] === 'html')
		{
			$parent_class = 'class="'.$options['parent_class'].'"';
			$output = '<'.$options['prefix'].' '.$parent_class.'>';
			$results = array_map("unserialize", array_unique(array_map("serialize", $results)));
			foreach($results as $result)
			{
				$category = url_title($result['category'], config('separator'), $is_capitalize);
				$link = $category .'/'. url_title($result['keyword'], config('separator'), $is_capitalize) . $is_pdf .'?'. config('single_var') .'='. $result['id'];
				$output .= '<'.$options['item'].'>';
				$output .= '<a href="'. base_url() . $link.'" title="'. ucwords($result['title']) .'" rel="nofollow">'. ucwords($result['title']) .'</a>';
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
	$separator = config('separator');
	$is_pdf = (config('type') === 'pdf') ? '.pdf': '.html';
	$is_capitalize = (config('capitalize')) ? false: true;
	$category = url_title($category, $separator, $is_capitalize);
	$keyword = url_title($keyword, $separator, $is_capitalize);
	$view = ($id !== '') ? '?'. config('single_var') .'='. $id: '';
	return base_url() . $category .'/'. $keyword . $is_pdf . $view;
}

function generate_permalink ($keyword, $category = '', $id = '')
{
	if ($category === '') $category = get_category();
	$separator = config('separator');
	$is_pdf = (config('type') === 'pdf') ? '.pdf': '.html';
	$is_capitalize = (config('capitalize')) ? false: true;
	$category = url_title($category, $separator, $is_capitalize);
	$keyword = url_title($keyword, $separator, $is_capitalize);
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
	$is_capitalize = (config('capitalize')) ? false: true;

	if (! empty($results))
	{
		if ($options['type'] === 'html')
		{
			$parent_class = 'class="'.$options['parent_class'].'"';
			$output = '<'.$options['prefix'].' itemprop="itemListElement" itemscope itemtype="http://schema.org/Thing" '.$parent_class.'>';
			foreach($results as $result)
			{
				if (! in_array($result['id'], $options['exclude'])):
				$category = url_title($result['category'], config('separator'), $is_capitalize);
				$link = base_url() . $category .'/'. url_title($result['keyword'], config('separator'), $is_capitalize) . $is_pdf .'?'. config('single_var') .'='. $result['id'];
				$new_keyword = url_title($result['title'], config('separator'), $is_capitalize);
				$output .= '<'.$options['item'].'>';
				$output .= '<h3 itemprop="name"><a href="'. generate_permalink($result['title'], $category) .'" title="'. ucwords($result['title']) .'" rel="nofollow">'. ucwords($result['title']) .'</a></h3>';
				$output .= '<a href="'. $link .'" title="'. ucwords($result['title']) .'" rel="nofollow" class="tiny button"><i class="fa fa-book"></i> Read</a>';
				$output .= '<div class="desc">'. $result['description'] .'</div>';
				$output .= '<a href="'. $link .'" title="'. ucwords($result['title']) .'" rel="nofollow" class="read"><i class="fa fa-external-link-square"></i> '. $link .'</a>';
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
	$is_capitalize = (config('capitalize')) ? false: true;

	if (! empty($results))
	{
		$output = '';
		foreach($results as $result)
		{
			if ($result['cat_id'] !== 'NULL')
			{
				$category = url_title($list['name'], config('separator'), $is_capitalize);
				$link = $category .'/'. url_title($result['keyword'], config('separator'), $is_capitalize) . $is_pdf;
				$output .= '<a href="'. base_url() . $link.'" title="'. ucwords($result['keyword']) .'" rel="nofollow">'. ucwords($result['keyword']) .'</a>, ';
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
	$output = '<a href="'.base_url().'" title="'.config('index.title').'">Home</a> '. $nav;

	$category = capitalize(get_category());
	$category_url = base_url() . get_category();

	$keyword = capitalize(get_keyword());
	$keyword_url = generate_permalink(get_keyword(), get_category());

	switch (location('', false)) {
		case 'category':
		$output .= '<span class="current-page">'. normalize($category, true).'</span>';
		break;

		case 'result':
		$output .= '<a href="'. $category_url .'">'. $category .'</a>'. $nav;
		$output .= '<span class="current-page">'. normalize(get_keyword(), true) .'</span>';
		break;

		case 'single':
		$output .= '<a href="'. $category_url .'">'. $category .'</a>'. $nav;
		$output .= '<a href="'. $keyword_url .'">'. $keyword .'</a>'. $nav;
		$output .= '<span class="current-page">'. normalize(title(true), true).'</span>';
		break;
	}

	return $output;
}

function clean_words ($str)
{
	$words = explode(',', config('clean.words'));

	if ($str !== '')
	{
		$str = explode(' ', strtolower($str));
		$output = array();
		foreach ($str as $arr)
		{
			if (! in_array($arr, $words))
			{
				array_push($output, $arr);
			}
		}

		$output = implode(' ', $output);
		return $output;
	}

	return $str;
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
		$description = '<meta name="description" content="'. description() .'">';
	endif;

	if(location('category') || location('result')):
		$microdata = '<meta itemprop="mainContentOfPage" content="true"><meta itemprop="itemListOrder" content="Ascending">';
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
	$title = ucwords($arr['name']);
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
	$keyword = ucwords($arr['keyword']);
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

/** EOF */