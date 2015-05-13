<?php 
define('CONTENTPATH', 'content');
define('KEYWORDPATH', CONTENTPATH . '/keywords');
define('INCLUDESPATH', 'includes');

require INCLUDESPATH . '/helpers.php';
$config = build_config('config.php');

require INCLUDESPATH . '/DB_Driver.php';
require INCLUDESPATH . '/Hashids.php';

$path = '';
$results = array();
$related = array();

if (! empty($_GET))
{
	$path = array_keys($_GET);
	$path = explode('/', $path[0]);
	$path = array_filter($path);
	$path_arr = array();
	foreach(array_values($path) as $i => $p)
	{
		$path_arr[($i + 1)] = $p;
	}
	$path = $path_arr;
}

$db = new DB_Driver('localhost', config('database.name'), config('database.username'), config('database.password'));

class Engine {

	var $path;
	var $db;
	var $location;

	function __construct()
	{
		$this->location = location('', false);
	}

	function init ()
	{
		global $db;
		$this->db = $db;

		# record keyword
		$keyword = get_search_term();
		if (! empty($keyword) && (location('category') || location('result') || location('single')))
		{
			$category_id = $this->is_category_exists();
			$this->insert_keyword($keyword, $category_id);
		}

		$this->remove_header();
		$this->run();
	}

	function run ()
	{
		global $results;
		$this->path = get_path();
		$this->theme = $this->theme_path();

		switch($this->location)
		{
			case 'opensearch':
				include 'opensearch.php';
			break;
			case 'home':
				$categories = get_categories();
				if (is_array($categories) && count($categories) > 0)
				{
					$results = array_map('get_categories_map', $categories);
					$this->render('index');
				}
				else
				{
					die('Please insert keywords');
				}
			break;

			case 'search':
				if ($_SERVER['REQUEST_METHOD'] === 'POST')
				{
					$q = (isset($_GET['q']))? $_GET['q']: '';
				}
				else
				{
					$q = (isset($_GET['q']))? $_GET['q']: '';
				}

				$q = permalink_url($q, ' ');

				if (empty($q) || strlen($q) < 5)
				{
					header("Location: ". base_url());
				}
				else
				{
					$result = $this->db->query("SELECT `kw`.`keyword` as `keyword`, `kw`.`time` as `time`, `cat`.`name` as `category` FROM `keywords` as `kw` LEFT JOIN `cat` as `cat` ON `cat`.`id`= `kw`.`cat_id` WHERE LOWER(`kw`.`keyword`) LIKE '{$q}'")->result();

					if (empty($result))
					{
						if (isset($_POST['cat']))
						{
							header("Location: ". generate_permalink_url($q, normalize($_POST['cat'])));
						}
						else
						{
							header("Location: ". generate_permalink_url($q, 'others'));
						}
					}
					else
					{
						$result = $result[0];
						header("Location: ". generate_permalink_url($result['keyword'], $result['category']));
					}
				}

			break;

			case 'category':
				if ($category_id = $this->is_category_exists())
				{
					$category_name = normalize(get_category());
					$keywords = get_keywords($category_name);

					if(! empty($keywords) && is_array($keywords))
					{
						$results = array_map('get_keywords_map', $keywords);
						$this->render('category');
					}
					else
					{
						$this->not_found();
					}
				}
				else
				{
					$this->not_found();
				}
			break;

			case 'result':
			case 'single':
				if ($category_id = $this->is_category_exists())
				{
					# Get keyword
					$q = get_keyword();
					$is_single = isset($_GET[config('single_var')]);

					# Check is valid URL
					if (! $this->valid_url($q) ) {
						header("Location: ". base_url());
					}

					# is keyword exists
					$list = array();
					$is_results_exists = false;

					$keyword = $this->get_keyword_id($q);
					if (! empty($keyword))
					{
						$keyword_id = $keyword[0]['id'];
						$category_id = $keyword[0]['cat_id'];

						# if category_id = NULL redirect to home
						if ($category_id === 'NULL')
						{
							header("Location: " . base_url() . get_category(true));
							die();
						}

						# search results by keyword
						$list = $this->search_db($q);

						if (! empty($list))
						{
							$is_results_exists = true;
						}
					}

					# keyword not exists
					else
					{
						# search bing and insert to db
						$list = $this->insert_keyword($q, $category_id);

						# if results exists
						if (! empty($list))
						{
							$is_results_exists = true;
						}
					}

					if ($is_results_exists)
					{
						if ($is_single)
						{
							$results = $this->single($_GET[config('single_var')]);

							if ($results !== NULL)
							{
								global $related;
								$related = $results;
								$this->render('single');
							}
							else
							{
								header("Location: ". base_url());
							}
						}
						else
						{
							if (config('type') === 'pdf') require INCLUDESPATH . '/tcpdf/tcpdf.php';
							$results = array_splice($list, 0, config('results'));
							$this->render('result');
						}
					}
					else
					{
						header("Location: ". base_url() . get_category(true));
					}
				}
				else
				{
					header("Location: ". base_url());
				}
			break;

			case 'page':
				$json = file_get_contents(CONTENTPATH . '/pages/'. current_path() .'.txt');
				$json = json_decode($json, TRUE);
				$title = htmlentities($json['title']);
				
				$content = $json['content'];
				$content = replace_syntax($content);

				$results = array('title' => $title, 'content' => $content);
				$this->render('page');
			break;

			case 'sitemap':
				$alphabet = strtolower(current_path());
				$list = array();
				$query = '';

				if ($alphabet === 'numeric')
				{
					$query = "SELECT `kw`.`keyword` as `keyword`, `kw`.`cat_id` as `cat_id`, `kw`.`time` as `time`, `cat`.`name` as `category` FROM `keywords` as `kw` LEFT JOIN `cat` as `cat` ON `cat`.`id`= `kw`.`cat_id` WHERE `keyword`  REGEXP '^[0-9]' LIMIT 0, 100000";
				}
				else
				{
					$query = "SELECT `kw`.`keyword` as `keyword`, `kw`.`cat_id` as `cat_id`, `kw`.`time` as `time`, `cat`.`name` as `category` FROM `keywords` as `kw` LEFT JOIN `cat` as `cat` ON `cat`.`id`= `kw`.`cat_id` WHERE `keyword` LIKE '$alphabet%' LIMIT 0, 100000";
				}

				$db_result = $this->db->query($query)->result();
				if (! empty($db_result))
				{
					foreach ($db_result as $result)
					{
						if ($result['cat_id'] !== 'NULL')
						{
							array_push($list, $result);
						}
					}
				}

				$results = array('title' => 'Sitemap '. current_path(), 'list' => $list);
				$this->render('sitemap');
			break;

			default:
				$this->not_found();
			break;
		}
	}

	function is_category_exists ()
	{
		$category = get_category();
		$category_id = $this->get_category_id($category);

		if (! empty($category_id)) return $category_id[0]['id'];
		return false;
	}

	function cache_file ($q)
	{
		$file_hash = new Hashids(md5($q), 10);
		$file_hash_id = $file_hash->encrypt(1);

		return CONTENTPATH .'/caches/'. $file_hash_id;
	}

	function search_db ($q)
	{
		$hash = new Hashids(md5($q), 10);
		$hash = $hash->encrypt(1);

		$result = $this->db->query("SELECT * FROM `index` WHERE `keyword_id` = '{$hash}'")->result();
		return $result;
	}

	function get_category_id ($category_name)
	{
		$result = $this->db->query("SELECT `id` FROM `cat` WHERE LOWER(`name`) LIKE '%{$category_name}%' LIMIT 0,1")->result();
		return $result;
	}

	function get_keyword_id ($keyword)
	{
		$result = $this->db->query("SELECT `id`,`cat_id` FROM `keywords` WHERE LOWER(`keyword`) LIKE '{$keyword}' LIMIT 0,1")->result();
		return $result;
	}

	function add_db ($q, $list, $keyword_id, $is_new = false)
	{
		$time = time();

		$query = "INSERT INTO `index` (`id`, `keyword_id`, `title`, `description`, `url`, `time`) VALUES ";
		foreach ($list as $k => $item)
		{
			$title = safe_string_insert($this->db->escape_str($item['title']), 'title');
			$list[$k]['title'] = $title;

			$description = safe_string_insert($this->db->escape_str($item['description']), 'desc');
			$list[$k]['description'] = $description;

			$url = $this->db->escape_str($item['url']);

			$query .= "('{$item['id']}', '{$keyword_id}', '{$title}', '{$description}', '{$url}', '{$time}'), ";
		}
		$query = rtrim($query, ", ");
		$this->db->query("$query ON DUPLICATE KEY UPDATE `id` = `id`;");

		return $list;
	}

	function get_db ($q)
	{
		$hash = new Hashids(md5($q), 10);
		$hash = $hash->encrypt(1);

		$this->db->where('keyword_id', $hash);
		$results = $this->db->get('index', config('results'))->result();

		if (count($results) > 0) {
			return $results;
		}

		return NULL;
	}

	public function single ($id)
	{
		$this->db->where('id', $id);
		$result = $this->db->get('index')->result();

		if (! empty($result))
		{
			return $result[0];
		}

		return NULL;
	}

	public function theme_path ()
	{
		if (file_exists(CONTENTPATH . '/themes/'. config('theme')))
		{
			return CONTENTPATH . '/themes/'. config('theme') .'/';
		}
		
		die('theme Doesn\'t Exists');
	}

	public function valid_url ($q)
	{
		$ext = end(explode('_', end($this->path)));
		if (count($this->path) > 1 && $ext === config('type') && $q !== '') return true;
		return false;
	}

	function append_file ($file, $data)
	{
		$files = explode("\n", read_file($file));
		$text = remove_empty_array($files);

		if (count($text) > 0)
		{
			$text = array_filter($text, 'normalize');
			if (! in_array($data, $text) && ! empty($data))
			{
				file_put_contents($file, "\n$data", FILE_APPEND);
			}
		}
	}

	function remove_header ()
	{
		if (config('type') === 'pdf')
		{
			header('Content-Type: text/html;charset=UTF-8');
			if (function_exists('header_remove'))
			{
				header_remove('X-Powered-By');
			}
			else
			{
				@ini_set('expose_php', 'off');
			}
		}
	}

	function render ($page_name)
	{
		# Load functions before render
		if (file_exists($this->theme . 'functions.php'))
		{
			require $this->theme . 'functions.php';
		}

		ob_start();
		include $this->theme . $page_name . '.php';
		$buffer = ob_get_clean();

		$search = array(
			'/\>[^\S ]+/s', 
			'/[^\S ]+\</s', 
			'/(\s)+/s',
			'#(?://)?<!\[CDATA\[(.*?)(?://)?\]\]>#s'
		);

		$replace = array(
			'>',
			'<',
			'\\1',
			"//&lt;![CDATA[\n".'\1'."\n//]]>"
		);

		if (config('type') === 'html') $buffer = preg_replace($search, $replace, $buffer);
		$buffer = str_replace('<html>', '<html itemscope itemtype="http://schema.org/WebPage">', $buffer);
		$buffer = str_replace('<title>', '<title itemprop="name">', $buffer);
		$script = array('<script type="text/javascript">var delok="'. config('method') .'";</script><script type="text/javascript" src="'.base_url().'content/views.js">',
			'</script><script type="application/ld+json">{"@context": "http://schema.org", "@type": "WebSite","url": "'. base_url() .'","potentialAction": {"@type": "SearchAction", "target": "'. base_url() .'search?q={search_term_string}", "query-input": "required name=search_term_string"},"name" : "'. site_name() .'"}</script>');
		$buffer = str_replace('</body>', implode('', $script) . '</body>', $buffer);
		echo $buffer;
	}

	function not_found ()
	{
		header('HTTP/1.0 404 Not Found');
		$this->render('404');
	}

	function insert_keyword ($keyword, $cat_id = '')
	{
		$time = time();

		$keyword = strtolower(permalink_url($keyword, true));
		$cat_id = (empty($cat_id)) ? 'VoXl0m3N1q': $cat_id;
		$keyword_id = new Hashids(md5($keyword), 10);
		$keyword_id = $keyword_id->encrypt(1);
		$keyword_is = $this->db->query("SELECT count(*) as `exists` FROM `keywords` WHERE `id` = '{$keyword_id}'")->result();

		if (! empty($keyword_is) && $keyword_is[0]['exists'] === '0')
		{
			$list = search_bing($keyword);

			if ($list !== NULL)
			{
				$this->db->query("INSERT INTO `keywords` (`id`, `keyword`, `cat_id`, `time`) VALUES ('{$keyword_id}', '{$keyword}', '{$cat_id}', '{$time}') ON DUPLICATE KEY UPDATE `count` = `count` + 1;");
				$list = $this->add_db($keyword, $list, $keyword_id);
				return $list;
			}
			else
			{
				$this->db->query("INSERT INTO `keywords` (`id`, `keyword`, `cat_id`, `time`) VALUES ('{$keyword_id}', '{$keyword}', 'NULL', '{$time}') ON DUPLICATE KEY UPDATE `count` = `count` + 1;");
				return false;
			}
		}
		else
		{
			$list = $this->search_db($keyword);
			return $list;
		}
	}
}

$system = new Engine();
$system->init();
/* End of File */