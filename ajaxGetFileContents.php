<?php
$categories = !empty($_POST) && isset( $_POST['categories'] ) ? $_POST['categories'] : '';

if( $categories && is_array( $categories ) ){

  $pages = [];
  $content = '';

  // Loop categories
  foreach( $categories as $key => $value ){

    // Loop sections inside each category
    foreach( $value as $section ){

      // Add all pages of the sections in an array
      $pages[] = $section;

    }

  }

  echo json_encode($pages);

}
