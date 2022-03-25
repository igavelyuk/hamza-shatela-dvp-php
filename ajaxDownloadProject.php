<?php

include 'includes/functions-ajax.php';
include 'includes/functions-sections.php';

global $project_name;

$dir = $project_name;
$zip_file = $project_name . '.zip';

// Delete the file if it already exists before starting the process.
if( file_exists($zip_file) ){
  delete_files( $zip_file );
}

if( !file_exists( $project_name ) ){
  echo json_encode( [ 'status'  => 'error', 'message' => 'The directory ' . $project_name . ' does not exist'] );
  die();
}

// Get real path for our folder
$rootPath = realpath($dir);

// Initialize archive object
$zip = new ZipArchive();
$zip->open($zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE);

// Create recursive directory iterator
/** @var SplFileInfo[] $files */
$files = new RecursiveIteratorIterator( new RecursiveDirectoryIterator($rootPath), RecursiveIteratorIterator::LEAVES_ONLY);

foreach ($files as $name => $file){
  // Skip directories (they would be added automatically)
  if (!$file->isDir()){
    // Get real and relative path for current file
    $filePath = $file->getRealPath();
    $relativePath = substr($filePath, strlen($rootPath) + 1);

    // Add current file to archive
    $zip->addFile($filePath, $relativePath);
  }
}

// Zip archive will be created only after closing object
if( $zip->close() ){
  delete_files( $project_name );

  ob_start();
  get_added_pages_html();
  $pages = ob_get_clean();

  echo json_encode( [ 'status'  => 'success', 'message' => 'Project Downloaded Successfully', 'url' => basename($zip_file), 'pages' => json_encode($pages) ] );
  die();
}else{
  echo json_encode( [ 'status'  => 'error', 'message' => 'Could not create zip file'] );
  die();
}
