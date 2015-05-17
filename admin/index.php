<?php
if (isset($_GET['cc']))
{
	exec('sh -x '. dirname(getcwd()) . '/cc.sh');
}

require '../includes/helpers.php';
$info = json_decode(read_file('engine.json'), TRUE);
$version = $info['version'];
$config = build_config('../config.php');

require '../includes/DB_Driver.php';
require '../includes/Hashids.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST'):
	if (is_login () && isset($_POST['type']))
	{
		switch ($_POST['type'])
		{
			case 'checkdb':
				$response = array('status' => 404);
				$mysql_connect = mysql_connect('localhost', $_POST['db_username'], $_POST['db_password']);

				if ($mysql_connect)
				{
					if (mysql_select_db($_POST['db_name']))
					{
						$response['status'] = 200;
					}
				}

				echo json_encode($response);
				die();
			break;
			case 'insert':
				$db = new DB_Driver('localhost', config('database.name'), config('database.username'), config('database.password'));
				$response = array('status' => 404);
				$time = time();
				$category = $_POST['category'];
				$index = intval($_POST['index']);
				$icon = $_POST['icon'];
				$q = clean_words($_POST['keyword']);
				$q = permalink_url($q, ' ');

				if (! bad_words($q) && ! empty($q))
				{
					$list = search_bing($q);
					if ($list !== NULL && ! empty($list))
					{
						$q = safe_strtolower($q);
						$keyword_id = new Hashids(md5($q), 10);
						$keyword_id = $keyword_id->encrypt(1);
						$category_id = new Hashids(md5($category), 10);
						$category_id = $category_id->encrypt(1);

						$db->query("INSERT INTO `cat` (`id`, `name`, `icon`, `time`) VALUES ('{$category_id}', '{$category}', '{$icon}', '{$time}') ON DUPLICATE KEY UPDATE `id` = `id`;");
						$db->query("INSERT INTO `keywords` (`id`, `keyword`, `cat_id`, `time`) VALUES ('{$keyword_id}', '{$q}', '{$category_id}', '{$time}') ON DUPLICATE KEY UPDATE `count` = `count` + 1;");

						$query = "INSERT INTO `index` (`id`, `keyword_id`, `title`, `description`, `url`, `time`) VALUES ";
						foreach ($list as $item)
						{
							$title	= safe_string_insert($db->escape_str($item['title']), 'title');
							$description = safe_string_insert($db->escape_str($item['description']), 'desc');
							$url	= $db->escape_str($item['url']);

							$query .= "('{$item['id']}', '{$keyword_id}', '{$title}', '{$description}', '{$url}', '{$time}'), ";
						}
						$query = rtrim($query, ", ");
						$db->query("$query ON DUPLICATE KEY UPDATE `id` = `id`;");

						$response['status'] = 200;
					}
				}

				header("Content-type: application/json");
				echo json_encode($response);
				die();
			break;

			case 'gen_keyword':
				if (empty($_POST['lang']))
				{
					$lang = array('en', 'sv', 'nl', 'de', 'fr', 'war', 'ru', 'ceb', 'it', 'es', 'vi', 'pl', 'ja', 'pt', 'zh', 'uk', 'ca', 'fa', 'no', 'sh', 'fi', 'id', 'ar', 'cs', 'sr', 'ro', 'ko', 'hu', 'ms', 'tr', 'min', 'eo', 'kk', 'eu', 'sk', 'da', 'bg', 'he', 'lt', 'hy', 'hr', 'sl', 'et', 'uz', 'gl', 'nn', 'vo', 'la', 'simple', 'el', 'hi', 'az', 'th', 'ka', 'oc', 'ce', 'be', 'mk', 'mg', 'new', 'ur', 'tt', 'ta', 'pms', 'cy', 'tl', 'lv', 'bs', 'te', 'be-x-old', 'br', 'ht', 'sq', 'jv', 'lb', 'mr', 'is', 'ml', 'zh-yue', 'bn', 'af', 'ba', 'ga', 'pnb', 'cv', 'fy', 'lmo', 'tg', 'my', 'yo', 'sco', 'an', 'ky', 'sw', 'io', 'ne', 'gu', 'scn', 'bpy', 'nds', 'ku', 'ast', 'qu', 'als', 'su', 'pa', 'kn', 'ckb', 'ia', 'mn', 'nap', 'bug', 'bat-smg', 'arz', 'wa', 'zh-min-nan', 'gd', 'am', 'map-bms', 'yi', 'mzn', 'si', 'fo', 'bar', 'vec', 'nah', 'sah', 'os', 'sa', 'roa-tara', 'li', 'hsb', 'or', 'pam', 'mrj', 'mhr', 'se', 'mi', 'ilo', 'hif', 'bcl', 'gan', 'rue', 'glk', 'nds-nl', 'bo', 'vls', 'ps', 'diq', 'fiu-vro', 'bh', 'xmf', 'tk', 'gv', 'sc', 'co', 'csb', 'hak', 'km', 'kv', 'zea', 'vep', 'crh', 'zh-classical', 'frr', 'eml', 'ay', 'wuu', 'stq', 'udm', 'nrm', 'kw', 'rm', 'szl', 'so', 'koi', 'as', 'lad', 'mt', 'fur', 'dv', 'gn', 'dsb', 'pcd', 'ie', 'cbk-zam', 'cdo', 'lij', 'ksh', 'ext', 'mwl', 'gag', 'ang', 'ug', 'ace', 'pi', 'pag', 'nv', 'sd', 'frp', 'sn', 'kab', 'lez', 'ln', 'pfl', 'xal', 'krc', 'myv', 'haw', 'rw', 'kaa', 'pdc', 'to', 'kl', 'arc', 'nov', 'kbd', 'av', 'bxr', 'lo', 'bjn', 'ha', 'tet', 'tpi', 'na', 'pap', 'lbe', 'jbo', 'ty', 'mdf', 'roa-rup', 'wo', 'tyv', 'ig', 'srn', 'nso', 'kg', 'ab', 'ltg', 'zu', 'om', 'chy', 'za', 'cu', 'rmy', 'tw', 'tn', 'chr', 'mai', 'pih', 'got', 'bi', 'xh', 'sm', 'ss', 'mo', 'rn', 'ki', 'pnt', 'bm', 'iu', 'ee', 'lg', 'ts', 'ak', 'fj', 'ik', 'sg', 'st', 'ff', 'dz', 'ny', 'ch', 'ti', 've', 'ks', 'cr', 'tum');

					shuffle($lang);
					$lang = array('en', 'en', 'en', end($lang));
					shuffle($lang);
					$lang = end($lang);
				}
				else
				{
					$lang = $_POST['lang'];
				}

				switch ($_POST['endpoint'])
				{
					case 'b':
						$endpoint = 'wikibooks.org';
						break;
					break;

					default:
						$endpoint = 'wikipedia.org';
						break;
				}

				$req = open_url("http://$lang.$endpoint/w/api.php?action=query&list=random&rnlimit=10&rnnamespace=0&format=json");

				$arr_title = array();

				if ($req)
				{
					$results = json_decode($req, TRUE);

					if (isset($results['query']) && isset($results['query']['random']))
					{
						foreach ($results['query']['random'] as $result)
						{
							array_push($arr_title, $result['title']);
						}
					}
				}

				$response['result'] = $arr_title;
				echo json_encode($response);
				die();
			break;

			case 'config':
				$post_config = $_POST['config'];
				$post_config['header.script'] = json_escape($post_config['header.script']);
				$post_config['footer.script'] = json_escape($post_config['footer.script']);
				$post_config['bing.api'] = json_escape($post_config['bing.api']);
				
				# upload logo
				if ($post_config['logo_tmp'] !== 'no') {
					$uploaddir = '../content/logo/';
					$filename = md5($post_config['logo_tmp']) .'_logo.'. $post_config['logo_ext'];

					recursive_remove_directory($uploaddir);
					write_file($uploaddir . $filename, base64_decode($post_config['logo_data']), 'w');

					$post_config['logo'] = $filename;

				} else {
					$post_config['logo'] = $post_config['logo_old'];
				}

				unset($post_config['logo_ext']);
				unset($post_config['logo_data']);
				unset($post_config['logo_tmp']);
				unset($post_config['logo_old']);

				# write config
				$config_str = '$config = <<<config'."\r\n". json_encode($post_config) ."\r\n". 'config;';
				$str = <<<php
<?php

$config_str

php;
				write_file('../config.php', $str, 'w');

				$response = array('status' => 404);

				if ($_POST['config']['password'] !== $_SESSION["passwd"]) {
					$response['status'] = 302;
				} else {
					$response['status'] = 200;
				}

				header("Content-type: application/json");
				echo json_encode($response);
				die();
			break;

			case 'page':
				$title = $_POST['title'];
				$content = $_POST['content'];
				$json = json_encode(array('title' => $title, 'content' => $content));
				write_file('../content/pages/'.$_POST['name'].'.txt', $json, 'w');
				header("Location: ./?page=1");
			break;

			case 'spinner':
				$content = $_POST['content'];
				$spinner_config = '../content/spinner/conf.json';
				$spinner = explode(',', read_file($spinner_config));
				$content_clean = strip_tags($content);
				$id = $_POST['name'];

				if (empty($content_clean))
				{
					$spinner = array_diff($spinner, array($id));
				}
				else
				{
					$spinner = array_merge($spinner, array($id));
				}

				$json = array('content' => $content);
				$json = json_encode($json);
				write_file('../content/spinner/'.$id.'.spinner', $json, 'w');

				$spinner = remove_empty_array(array_unique($spinner));
				sort($spinner);
				$spinner = implode(',', $spinner);
				write_file($spinner_config, $spinner, 'w');

				header("Location: ./?spinner=1");
			break;
		}
	}
	else
	{
		$user = $_POST['user'];
		$passwd = $_POST['passwd'];

		if((isset($user) && $user == 'admin') && (isset($passwd) && $passwd === config('password')) && isset($_POST['login_form']))
		{
			$_SESSION["login"] = 'yes';
			$_SESSION["passwd"] = $passwd;
		}
	}
endif;
?>
<!DOCTYPE html>
<html data-ng-app="App">
<head>
	<meta charset="utf-8">
	<meta name="robots" content="noindex,nofollow">
	<title>Admin</title>
	<link rel="stylesheet" href="./assets/css/normalize.css">
	<link rel="stylesheet" href="./assets/css/foundation.min.css">
	<link rel="stylesheet" href="./assets/css/font-awesome.min.css">
	<?php if (is_login()): ?>
	<link rel="stylesheet" href="./assets/css/w2ui.min.css">
	<?php endif; ?>
	<link rel="stylesheet" href="./assets/css/site.css">
</head>

<body <?php echo (! is_login())? 'class="nologin"': ''; ?> data-ng-controller="Main">

	<div class="row" id="<?php echo (! is_login())? 'login-box': 'admin-box'; ?>">
		<?php if (! is_login()): ?>
			<div class="large-6 columns">
				<div class="row">
					<div class="large-12 columns">
						<img src="./assets/img/login.jpg">
					</div>
				</div>
			</div>
			<div class="large-5 large-offset-1 columns">
				<form method="POST">
					<h2>Admin Login <?php echo $version; ?></h2>
					<p>This is secret area, please leave this page if you're not an administrator of this site</p>

					<div class="row">
						<div class="large-12 columns">
							<label>
								<input type="text" name="user" placeholder="Username" />
							</label>
						</div>
					</div>

					<div class="row">
						<div class="large-12 columns">
							<label>
								<input type="password" name="passwd" placeholder="Password" />
							</label>
						</div>
					</div>

					<div class="row">
						<div class="large-12 columns">
							<input type="hidden" name="login_form" value="1">
							<button class="tiny button"><i class="fa fa-sign-in"></i> Sign In</button>
						</div>
					</div>
				</form>
			</div>
		<?php else: ?>
			<div class="large-12 columns">
				<div class="row">
					<div class="large-12 columns">
						<a href="./" class="no-margin tiny secondary button"><i class="fa fa-home"></i> Home</a>
						<a href="?page=1" class="no-margin tiny secondary button"><i class="fa fa-file-text-o"></i> Page</a>
						<a  data-ng-show="usingSpinner" href="?spinner=1" class="no-margin tiny secondary button"><i class="fa fa-spinner"></i> Spinner</a>
						<a href="logout.php" class="no-margin tiny alert button"><i class="fa fa-sign-out"></i> Logout</a>
						<a target="_blank" class="right a-small" href="<?php echo dirname(base_url()); ?>"><i class="fa fa-share"></i> View Site</a>&nbsp;
						<a href="?cc=1" class="right a-small"><i class="fa fa-remove"></i> CC</a>
					</div>
				</div>

				<?php
				if (isset($_GET['page'])):
					include 'page.php';
				elseif(isset($_GET['spinner'])):
					include 'spinner.php';
				else:
					include 'settings.php';
				endif;
				?>
			</div>
		<?php endif; ?>
	</div>

	<div class="row">
		<div class="large-12">
			<p style="margin:40px 0 20px;font-size: 12px;text-align:center">&copy; <?php echo date('Y'); ?> - Copyrighted by <a href="http://fb.com/anonymousjapan" target="_blank">fb.com/anonymousjapan</a></p>
		</div>
	</div>

<?php if (is_login()): ?>
<script type="text/javascript" src="./assets/js/jquery.min.js"></script>
<script type="text/javascript" src="./assets/js/angular.min.js"></script>
<script type="text/javascript" src="./assets/js/string.min.js"></script>
<script type="text/javascript" src="./assets/js/w2ui.min.js"></script>
<script type="text/javascript" src="./assets/js/underscore.min.js"></script>
<script type="text/javascript" src="./assets/js/async.js"></script>
<script type="text/javascript" src="./assets/js/uslug.js"></script>
<?php if(isset($_GET['page']) || isset($_GET['spinner'])): ?>
<script type="text/javascript" src="./assets/js/ckeditor/ckeditor.js" charset="utf-8"></script>
<script type="text/javascript">
var fileEditor = './assets/js/fileman/index.php'; 
(function($){
	$(document).ready(function() {
		$('.editor').each(function (i, el) {
			CKEDITOR.replace($(el).get(0), {
				filebrowserBrowseUrl: fileEditor,
				filebrowserImageBrowseUrl: fileEditor + '?type=Images',
				removeDialogTabs: 'link:upload;image:upload',
				skin: 'moono'
			}); 
		});
	});
})(jQuery);
</script>
<?php endif; ?>
<script type="text/javascript" src="./assets/js/site.js"></script>
<?php endif; ?>
</body>
</html>