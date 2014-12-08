<?php header("Content-type: application/xml"); ?>
<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/" xmlns:moz="http://www.mozilla.org/2006/browser/search/"> 
  <ShortName><?php echo domain(); ?></ShortName> 
  <Description><?php echo config('index.description'); ?></Description> 
  <Image height="16" width="16" type="image/x-icon"><?php echo theme_url(); ?>favicon.ico</Image>
  <Url xmlns:parameters="http://a9.com/-/spec/opensearch/extensions/parameters/1.0/"
      type="text/html"
      template="<?php echo base_url(); ?>search"
      parameters:method="POST"
      parameters:enctype="application/x-www-form-urlencoded">
   <parameters:Parameter name="q" value="{searchTerms}"/>
  </Url>
  <InputEncoding>UTF-8</InputEncoding> 
  <AdultContent>false</AdultContent> 
  <Url type="application/opensearchdescription+xml" rel="self" template="<?php echo base_url(); ?>search.xml" /> 
</OpenSearchDescription> 