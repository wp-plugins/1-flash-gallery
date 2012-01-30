<?php

/*
 * All XML tools will be here
 */

/**
 * Creates new DOMDocument
 * 
 * @return DOMDocument 
 */
function fgallery_create_document() {
    if (FGALLERY_PHP4_MODE) {
        $document = domxml_new_doc('1.0');
    } else {
        /* create a dom document with encoding utf8 */
        $document = new DOMDocument('1.0', 'UTF-8');
    }
    return $document;
}

/**
 * Displays the XML document
 * 
 * @param DOMDocument $document 
 */
function fgallery_show_document($document) {
    if (FGALLERY_PHP4_MODE) {
        echo $document->dump_mem(true);
    } else {
        echo $document->saveXML();
    }
}

/**
 * Create Element
 * 
 * @param string $name
 * @param string $value
 * @param DOMDocument $document
 * @return DOMElement 
 */
function fgallery_create_element($name, $value, &$document) {
    if (FGALLERY_PHP4_MODE) {
        $element = $document->create_element($name, $value);
    } else {
        $element = $document->createElement($name, $value);
    }
    return $element;
}

/**
 * Create CDATA section 
 * @param string $value
 * @param DOMDocument $document
 * @return DOMCDATASection 
 */
function fgallery_create_cdata_section($value, &$document){
    if (FGALLERY_PHP4_MODE) {
        $cdata = $document->create_cdata_section($value);
    } else {
        $cdata = $document->createCDATASection($value);
    }
    return $cdata;
}

/**
 * Append Child
 * 
 * @param DOMElement $parent
 * @param DOMElement $child
 * @return DOMElement parent 
 */
function fgallery_append_child(&$parent, $child) {
    if (FGALLERY_PHP4_MODE) {
        $parent->append_child($child);
    } else {
        $parent->appendChild($child);
    }
    return $parent;
}

/**
 * Removes child 
 * @param DOMElement $parent
 * @param DOMElement $child
 * @return DOMElement child 
 */
function fgallery_remove_child(&$parent, $child) {
    if (FGALLERY_PHP4_MODE) {
        $result = $parent->remove_child($child);
    } else {
        $result = $parent->removeChild($child);
    }
    return $result;
}

/**
 * Function to create attribute to image
 * 
 * @param string $name
 * @param string $value
 * @param DOMElement $parent
 * @param DOMDocument $imagesXML
 * @return DOMAttribute 
 */
function fgallery_create_attribute($name, $value, &$parent, &$document) {
    if (FGALLERY_PHP4_MODE) {
        $attribute = $document->create_attribute($name, $value);
        $parent->append_child($attribute);
    } else {
        $attribute = $document->createAttribute($name);
        $attribute->value = $value;
        $parent->appendChild($attribute);
    }
    return $attribute;
}

?>
