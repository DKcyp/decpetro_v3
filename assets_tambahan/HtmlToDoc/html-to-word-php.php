<?php
	//Loading class file
	include_once 'HtmlToDoc.class.php';

	//Initialize class
	$htd = new HTML_TO_DOC();

	$html = '<h1>Hello World</h1><br><h2>Hello World</h2><br><h3>Hello World</h3>';

	$htd->createDoc($html,'htmldoc',true);
?>