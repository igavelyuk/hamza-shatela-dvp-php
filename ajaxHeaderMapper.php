<?php
include 'includes/functions-ajax.php';
include 'includes/classes/class-beautify-html.php';

global $project_name;

$menus = $_POST['menus'];
$missing_menu_items = [];

if( empty($menus) ){
  echo json_encode( [ 'status'  => 'error', 'message' => 'No menus found' ] );
  die();
}

if (!file_exists($project_name)){
  echo json_encode( [ 'status'  => 'error', 'message' => 'Project does not exist yet' ] );
  die();
}

$dir = new DirectoryIterator($project_name);
foreach ($dir as $file) {
  if ( !$file->isDot() && $file->getExtension() == 'html' ) {

    libxml_use_internal_errors(true);
    $content = file_get_contents( $file->getPathname() );

    $doc = new DOMDocument();
    $doc->loadHTML($content); // Content of every file

    $classname = "navbar-nav";
    $xpath = new DomXPath($doc);
    $searchNode = $xpath->query("//*[contains(@class, '$classname')]"); // The navbar-nav items

    if( $searchNode->length == 0 ){
      $missing_menu_items[] = $file->getFilename();
    }

    foreach( $searchNode as $node ){

      // Empty up the navbar-navs
      while ($node->hasChildNodes()) {
        $node->removeChild($node->firstChild);
      }

      // Refill the navbar-nav with the mapped content recursively
      importMenuNodes( $menus, $node, $doc );

    }

    $final_html = $doc->saveHTML();

    $beautifier_options = array(
      'indent_size' => 2
    );

    $beautifier = new Beautify_Html( $beautifier_options );
    $final_html = $beautifier->beautify($final_html);

    $final_doc = new DOMDocument();
    $final_doc->preserveWhiteSpace = false;
    $final_doc->loadHTML($final_html);
    $final_doc->formatOutput = true;

    $final_doc->saveHTMLFile( $project_name . '/' . basename($file->getPathname()) );
    libxml_use_internal_errors(false);

  }
}

echo json_encode( [ 'status'  => 'success', 'message' => 'Headers mapped successfully', 'missing_items' => $missing_menu_items ] );
die();
