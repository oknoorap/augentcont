<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" 
	xmlns:html="http://www.w3.org/TR/REC-html40"
	xmlns:sitemap="http://www.sitemaps.org/schemas/sitemap/0.9"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" version="1.0" encoding="UTF-8" indent="yes" />
<xsl:template match="/">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>XML Sitemap</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="robots" content="noindex,follow" />
		<style type="text/css">
			body {
				font-family: arial, sans-serif;
				font-size: 14px;
				padding: 10px;
			}

			h1 {
				font-size: 18px;
				margin: 0 0 10px;
			}
			
			td {
				font-size: 12px;
				padding: 0;
				padding-right: 10px;
			}
			
			th {
				text-align: left;
				font-size: 12px;
				padding: 0
			}
			
			tr.high {
				background-color:whitesmoke;
			}

			a {
				color: #3498db;
				text-decoration: none;
			}
			a:hover {
				text-decoration: underline;
			}
		</style>
	</head>
	<body>
		<xsl:apply-templates></xsl:apply-templates>
	</body>
</html>
</xsl:template>

<xsl:template match="sitemap:urlset">
	<h1>XML Sitemap</h1>
	<div id="content">
	<table cellpadding="5">
		<tr style="border-bottom:1px black solid;">
			<th>URL</th>
			<th>Priority</th>
			<th>Change frequency</th>
			<th>Last modified</th>
		</tr>
		<xsl:variable name="lower" select="'abcdefghijklmnopqrstuvwxyz'"/>
		<xsl:variable name="upper" select="'ABCDEFGHIJKLMNOPQRSTUVWXYZ'"/>
		<xsl:for-each select="./sitemap:url">
			<tr>
				<xsl:if test="position() mod 2 != 1">
					<xsl:attribute  name="class">high</xsl:attribute>
				</xsl:if>
				<td>
					<xsl:variable name="itemURL">
						<xsl:value-of select="sitemap:loc"/>
					</xsl:variable>
					<a href="{$itemURL}">
						<xsl:value-of select="sitemap:loc"/>
					</a>
				</td>
				<td>
					<xsl:value-of select="concat(sitemap:priority*100,'%')"/>
				</td>
				<td>
					<xsl:value-of select="concat(translate(substring(sitemap:changefreq, 1, 1),concat($lower, $upper),concat($upper, $lower)),substring(sitemap:changefreq, 2))"/>
				</td>
				<td>
					<xsl:value-of select="concat(substring(sitemap:lastmod,0,11),concat(' ', substring(sitemap:lastmod,12,5)))"/>
				</td>
			</tr>
		</xsl:for-each>
	</table>
	</div>
</xsl:template>


<xsl:template match="sitemap:sitemapindex">
	<h1>Sitemap Index</h1>
	<div id="content">
		<table cellpadding="5">
			<tr style="border-bottom:1px black solid;">
				<th>URL of sub-sitemap</th>
				<th>Last modified</th>
			</tr>
			<xsl:for-each select="./sitemap:sitemap">
				<tr>
					<xsl:if test="position() mod 2 != 1">
						<xsl:attribute  name="class">high</xsl:attribute>
					</xsl:if>
					<td>
						<xsl:variable name="itemURL">
							<xsl:value-of select="sitemap:loc"/>
						</xsl:variable>
						<a href="{$itemURL}">
							<xsl:value-of select="sitemap:loc"/>
						</a>
					</td>
					<td>
						<xsl:value-of select="concat(substring(sitemap:lastmod,0,11),concat(' ', substring(sitemap:lastmod,12,5)))"/>
					</td>
				</tr>
			</xsl:for-each>
		</table>
	</div>
</xsl:template>

</xsl:stylesheet>