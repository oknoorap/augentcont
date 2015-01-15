<?php 
foreach(results() as $item)
{
	$the_title[]	= $item['title'];
	$the_content[]	= $item['description'];
	$the_ref[]		= $item['url'];
	$the_links[]	= $item['id'];
	$new_links[]    = generate_permalink_url($item['title'], get_category());
}

/**
* PDF Init
*/
$page_title = title();
$keyword = title(true);

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetHeaderMargin(5);
$pdf->SetMargins(10, 25, -1, true);
$pdf->SetHeaderData('', 10, $page_title, sprintf("Document hosted at %s", current_path_url()));
$pdf->SetPrintFooter(false);
$pdf->SetTitle($page_title);
$pdf->SetSubject('Download PDF '. $keyword);
$pdf->SetKeywords("free download {$keyword} pdf, {$keyword} pdf download, {$keyword} document download");
$pdf->SetCreator(base_url());


/**
* Add new Page
* 1st Page
*/
$pdf->SetFont('times', '', 12);
$pdf->AddPage();

$title = '';
foreach($the_title as $index => $value)
{
	$title .= '<tr style="font-size:15">'.
		'<td width="5%">'. ($index + 1) .'.</td>'.
		'<td width="95%"><a href="#2" style="color:#000000;">'. $value .'</a></td>'.
	'</tr>';
}

$html = '<h1 style="text-align:center">'. title_case($keyword) .'</h1><br />'.
'<h2 style="font-family:georgia;font-style:italic;text-align:center;">Table of Contents</h2>'.
'<div>'.
	'<table>'. $title .'</table>'.
'</div>';

$pdf->writeHTML($html, true, 0, true, 0);



/**
* Add new page
* 2nd Page
*/
$pdf->SetFont('helvetica', '', 12);
$pdf->AddPage();

$content = "<p>Full version of this PDF contains 10 attachment URLs, you also can download documents related with {$page_title}</p>";

foreach($the_content as $index => $value)
{
	$content_count = ($index + 1);
	$content .= "<p>{$value} <a href=\"". read_permalink($the_links[$index]) ."\">Continue Reading</a>";
	$content .= (config('boost.mode')) ? ", <a href=\"". $new_links[$index] ."\">Similiar</a>": '';
	$content .= " <a href=\"#3\"><sup>{$content_count}</sup></a></p>";
}

$pdf->writeHTML($content, true, 0, true, 0);


/**
* Add new page
* 3rd Page
*/
$pdf->SetFont('times', '', 12);
$pdf->AddPage();

$reference_list = '';
foreach($the_ref as $index => $value)
{
	$reference_list .= '<li>'.str_replace(array('http://', 'https://', 'www'), array('','','www '), $value).'</li>';
}
$html = "<h1 style=\"font-family:georgia;font-style:italic;text-align:center;\">Online References</h1><p>We use these documents or PDF to publish ". current_url() ." :</p> <ol>{$reference_list}</ol>";
$pdf->writeHTML($html, true, 0, true, 0);


/**
* Build pdf
*/
$pdf->Output($keyword . '.pdf', 'I');