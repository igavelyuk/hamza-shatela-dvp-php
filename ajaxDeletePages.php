<?php

include 'includes/functions-ajax.php';
include 'includes/functions-sections.php';

global $project_name;

$pages = $_POST['pages'];

if( empty($pages) || !is_array($pages) ){
  echo json_encode( [ 'status'  => 'error', 'message' => $pages] );
  die();
}

// Delete the file if it already exists before starting the process.
foreach( $pages as $page ){
  if( file_exists($page) ){
    delete_files( $page );
  }
}

ob_start();
get_added_pages_html();
$pages = ob_get_clean();

echo json_encode( [ 'status'  => 'success', 'message' => 'File(s) deleted', 'pages' => json_encode($pages)] );
die();
