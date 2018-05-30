<?php
set_include_path(implode(PATH_SEPARATOR, array(
realpath('../../../library'),
realpath('../../../../../ZendFramework/1.12.0/'),
realpath(APPLICATION_PATH . '/../library'),
realpath(APPLICATION_PATH.'/../../../../ZendFramework/1.12.0/'),
realpath(APPLICATION_PATH.'/../../../ZendFramework/1.12.0/'),
get_include_path()
)));
require_once 'Zend/Barcode.php';

// Only the text to draw is required
$barcodeOptions = $_REQUEST['options'];
if(!is_array($barcodeOptions)) $barcodeOptions = array('text'=>'MISSING OPTIONS');

$rendererOptions = @$_REQUEST['render'];
if(!is_array($rendererOptions)) $rendererOptions = array();

$barcodeOptions['drawText'] = false;

$type = @$_REQUEST['type'];
if(!is_array($type)) $type = "Upca";

Zend_Barcode::factory(
$type, 'image', $barcodeOptions, $rendererOptions
)->render();