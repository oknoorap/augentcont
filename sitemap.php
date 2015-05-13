<?php
# required functions
require 'includes/helpers.php';
$config = build_config('config.php');
require 'includes/DB_Driver.php';

# get variable from htaccess
$limit = 0xc350;
$offset = (empty($_GET['offset']))? 0: $_GET['offset'];
$format = (empty($_GET['format']))? 'xml': str_replace('.', '', $_GET['format']);

# if url is sitemap.xml.gz or sitemap1.xml redirects to homepage
if (($offset === 0 && $format !== 'xml') || ($offset > 0 && $format !== 'gz'))
{
	header("Location: ". base_url() .'sitemap.xml');
}

# connect to db
$db = new DB_Driver('localhost', config('database.name'), config('database.username'), config('database.password'));

# count keywords in db
$total = $db->query("SELECT COUNT(*) as `count` FROM `keywords`")->result();
$total = current($total);
$count = $total['count'];

if ($count > 0)
{
	$output = '<?xml version="1.0" encoding="UTF-8"?>';
	$output .= '<?xml-stylesheet type="text/xsl" href="'. base_url() .'sitemap.xsl"?>';

	if ($offset === 0)
	{
		# split sitemap
		$diff = floor($count / $limit) + 1;

		# split sitemap index
		header("Content-type: application/xml");
		$output .= '<sitemapindex xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/siteindex.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
		for ($i = 1; $i <= $diff; $i++):
			$output .= '<sitemap><loc>'. base_url() .'sitemap'. $i .'.xml.gz</loc><lastmod>'. date('Y-m-d') .'</lastmod></sitemap>';
		endfor;
		$output .= '</sitemapindex>';
		echo $output;
	}
	else
	{
		# get offset
		$offset = ($offset > 1) ? ($limit * ($offset - 1)) + 1: 0;

		# sitemap index
		$sitemap = $db->query("SELECT `keywords`.`keyword`, `keywords`.`time`, `cat`.`name` as `cat_name` FROM `keywords` LEFT JOIN `cat` ON `keywords`.`cat_id` = `cat`.`id` GROUP BY `keywords`.`keyword` ORDER BY `keywords`.`time` DESC LIMIT {$limit} OFFSET {$offset}")->result();

		if (count($sitemap) > 0)
		{
			$sitemap_name = 'sitemap'. $_GET['offset']. '.xml.gz';
			header('Content-Encoding: UTF-8');
			header('Content-type: application/x-gzip; charset=UTF-8');
			header('Content-Disposition: attachment; filename="'. $sitemap_name .'"');

			$output .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
			foreach ($sitemap as $permalink):
				$output .= '<url><loc>'. generate_permalink_url($permalink['keyword'], $permalink['cat_name']). '</loc><lastmod>'. date('Y-m-d', $permalink['time']) .'</lastmod><changefreq>monthly</changefreq><priority>0.8</priority></url>';
			endforeach;
			$output .= '</urlset>';
			echo gzencode("\xEF\xBB\xBF". $output);
		}
		else
		{
			header("Location: ". base_url());
		}
	}
}
else
{
	header("Location: ". base_url());
}