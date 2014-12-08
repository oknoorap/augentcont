<?php
session_start();

function is_login ()
{
	if (isset($_SESSION['login']) && $_SESSION['login'] === 'yes') return true;
	return false;
}

require '../config.php';
require '../includes/DB_Driver.php';
require '../includes/Hashids.php';
require '../includes/helpers.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST'):
	if (is_login () && isset($_POST['type']))
	{
		switch ($_POST['type'])
		{
			case 'installdb':
				$db = new DB_Driver('localhost', $_POST['db_name'], $_POST['db_username'], $_POST['db_password']);
				$db->query("CREATE TABLE IF NOT EXISTS `cat` (`id` varchar(255) NOT NULL, `name` text NOT NULL, `icon` varchar(50) NOT NULL DEFAULT 'ellipsis-h', `time` int(11) NOT NULL, PRIMARY KEY (`id`), UNIQUE KEY `id` (`id`)) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

				$db->query("CREATE TABLE IF NOT EXISTS `index` ( `id` varchar(255) NOT NULL, `keyword_id` varchar(255) DEFAULT NULL, `title` text NOT NULL, `description` text NOT NULL, `url` text NOT NULL, `time` int(11) NOT NULL, PRIMARY KEY (`id`), UNIQUE KEY `id` (`id`)) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

				$db->query("CREATE TABLE IF NOT EXISTS `keywords` (`id` varchar(255) NOT NULL, `keyword` text NOT NULL,`cat_id` varchar(255) NOT NULL,`count` bigint(20) unsigned NOT NULL, `time` int(11) NOT NULL,PRIMARY KEY (`id`),UNIQUE KEY `id` (`id`)) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

				$db->query("INSERT INTO `cat` (`id`, `name`, `icon`, `time`) VALUES ('VoXl0m3N1q', 'Others', 'ellipsis-h', 1415098244);");

				$str = str_replace('"installed":"0"', '"installed":"1"', file_get_contents('../config.php'));
				write_file('../config.php', $str, 'w');
				
				$response['status'] = 200;

				echo json_encode($response);
				die();
			break;
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

				if (! bad_words($q))
				{
					$list = search_bing($q);
					if ($list !== NULL)
					{
						$keyword_id = new Hashids(md5($q), 10);
						$keyword_id = $keyword_id->encrypt(1);
						$category_id = new Hashids(md5($category), 10);
						$category_id = $category_id->encrypt(1);

						$db->query("INSERT INTO `cat` (`id`, `name`, `icon`, `time`) VALUES ('{$category_id}', '{$category}', '{$icon}', '{$time}') ON DUPLICATE KEY UPDATE `id` = `id`;");
						$db->query("INSERT INTO `keywords` (`id`, `keyword`, `cat_id`, `time`) VALUES ('{$keyword_id}', '{$q}', '{$category_id}', '{$time}') ON DUPLICATE KEY UPDATE `count` = `count` + 1;");

						$query = "INSERT INTO `index` (`id`, `keyword_id`, `title`, `description`, `url`, `time`) VALUES ";
						foreach ($list as $item)
						{
							$title	= $db->escape_str($item['title']);
							$description = $db->escape_str($item['description']);
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

			case 'config':
				$post_config = $_POST['config'];
				$post_config['bing.api'] = json_escape($post_config['bing.api']);
				$config_str = '$config = <<<config'."\r\n". json_encode($post_config) ."\r\n". 'config;';
				$str = <<<php
<?php

$config_str

php;
				write_file('../config.php', $str, 'w');

				if ($_POST['config']['password'] !== $_SESSION["passwd"]) {
					header("Location: ./logout.php");
				} else {
					header("Location: ./");
				}
			break;

			case 'page':
				$title = $_POST['title'];
				$content = $_POST['content'];
				$json = json_encode(array('title' => $title, 'content' => $content));
				write_file('../content/pages/'.$_POST['name'].'.txt', $json, 'w');
				header("Location: ./?page=1");
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
	<link rel="stylesheet" href="./assets/normalize.css">
	<link rel="stylesheet" href="./assets/foundation.min.css">
	<link rel="stylesheet" href="./assets/font-awesome.min.css">
	<?php if (is_login()): ?>
	<link rel="stylesheet" href="./assets/w2ui.min.css">
	<?php endif; ?>
	<link rel="stylesheet" href="./assets/site.css">
</head>

<body <?php echo (! is_login())? 'class="nologin"': ''; ?> data-ng-controller="Main">

	<div class="row" id="<?php echo (! is_login())? 'login-box': 'admin-box'; ?>">
		<?php if (! is_login()): ?>
			<div class="large-6 columns">
				<div class="row">
					<div class="large-12 columns">
						<img src="./assets/login.jpg">
					</div>
				</div>
			</div>
			<div class="large-5 large-offset-1 columns">
				<form method="POST">
					<h2>Admin Login</h2>
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
						<?php if(! isset($_GET['page'])):?><a href="?page=1" class="no-margin tiny secondary button"><i class="fa fa-file-text-o"></i> Page</a><?php endif; ?>
						<?php if(isset($_GET['page'])):?><a href="./" class="no-margin tiny secondary button"><i class="fa fa-home"></i> Home</a><?php endif; ?>
						<a href="logout.php" class="no-margin tiny alert button"><i class="fa fa-sign-out"></i> Logout</a>
					</div>
				</div>

				<?php
				if (isset($_GET['page'])):
					include 'page.php';
				else:
					include 'settings.php';
				endif;
				?>
			</div>
		<?php endif; ?>
	</div>

<?php if (is_login()): ?>
<script type="text/javascript" src="./assets/jquery.min.js"></script>
<script type="text/javascript" src="./assets/angular.min.js"></script>
<script type="text/javascript" src="./assets/string.min.js"></script>
<script type="text/javascript" src="./assets/w2ui.min.js"></script>
<script type="text/javascript" src="./assets/underscore.min.js"></script>
<script type="text/javascript" src="./assets/async.js"></script>
<?php if(isset($_GET['page'])): ?>
<script type="text/javascript" src="./assets/ckeditor/ckeditor.js" charset="utf-8"></script>
<?php endif; ?>
<script type="text/javascript" src="./assets/site.js"></script>
<?php endif; ?>
</body>
</html>