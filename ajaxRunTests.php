<?php
include 'includes/functions-ajax.php';

global $project_name;

$files = isset($_FILES['file']) ? $_FILES['file'] : '';

ini_set('max_file_uploads', 100);

if( !empty($files) && count($files) > 99 ){
  echo json_encode( [ 'status'  => 'error', 'message' => 'Max files in a run allowed is 100' ] );
  die();
}

if( $files ){
  $errors = [];
  $i = 0;
  foreach( $files as $file ){

    if( $files['type'][$i] != 'text/html' ){
      echo json_encode( [ 'status'  => 'error', 'message' => 'One of the files uploaded is not an html file.' ] );
      die();
    }

    if( strpos($files['name'][$i], 'header') !== false ){
      if( !check_header_exists( $files['tmp_name'][$i] ) ){
        $errors[$i][] = 'Missing navbar-nav class, so the this file can\'t be mapped to a header.';
      }
    }

    if( !check_sigma_section_exists( $files['tmp_name'][$i] ) ){
      $errors[$i][] = 'Missing sigma-section, so this file can\'t be imported';
    }

    $i++;
  }
}

echo json_encode( [ 'status'  => 'success', 'message' => 'Files Checked', 'file' => $files, 'errors' => $errors ] );
die();
