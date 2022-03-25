<?php

// Remove the last occurence of a substring in a string
function str_lreplace($search, $replace, $subject){
  $pos = strrpos($subject, $search);

  if($pos !== false){
    $subject = substr_replace($subject, $replace, $pos, strlen($search));
  }

  return $subject;
}

// remove number and .html from file name
function normalize_name( $name ){
  $name = explode( '.', str_lreplace( '-', '.', $name ) );
  return isset($name[0]) ? strtolower( str_replace( '-', ' ', $name[0] ) ) : '';
}
