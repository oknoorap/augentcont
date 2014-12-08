<?php

class Rssreader {
  var $xml_file;
  var $xml_node_types = array( "title", "description", "link" );
  var $is_parser_active = false;
  function read(){
    // Declare
    $xml_count        = 0;
    $xml_items        = array();
    $is_parser_active = $this->is_parser_active;
    $xml_node_types   = $this->xml_node_types;
    $xml_reader = new XMLReader();
    $xml_reader->open ($this->xml_file, null, LIBXML_NOBLANKS);
    
    if($xml_reader){
        while ($xml_reader->read ())
        {
            $xml_node_type = $xml_reader->nodeType;
            $xml_name      = $xml_reader->name;
            // Pertama dan terakhir
            if ($xml_node_type != XMLReader::ELEMENT && $xml_node_type != XMLReader::END_ELEMENT)
            {continue;}
            else if ($xml_reader->name == "item" or $xml_reader->name == "entry"){
                if (($xml_node_type == XMLReader::END_ELEMENT) && $is_parser_active)
                { $xml_count++;}
                $is_parser_active = ($xml_node_type != XMLReader::END_ELEMENT);

            }
            if (!$is_parser_active || $xml_node_type == XMLReader::END_ELEMENT){
              continue;
            }
            if (in_array ($xml_name, $xml_node_types)){
                // Skip to the text node
                $xml_reader->read();
                $xml_items[$xml_count][$xml_name] = $xml_reader->value;
            }else if($xml_name == "media:thumbnail"){
                $xml_items[$xml_count]['media:thumbnail'] = array (
                        "url"     => $xml_reader->getAttribute("url"),
                        "width"   => $xml_reader->getAttribute("width"),
                        "height"  => $xml_reader->getAttribute("height")
                );
            }
        }
        return $xml_items;
    }else{
        return false;
    }
  }
}
?>