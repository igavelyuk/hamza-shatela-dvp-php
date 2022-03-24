<?php

// Return button options
function get_btn_options( $option ){
  $buttons = file_get_contents(  VAULT_PATH . "/data/buttons.json");
  $button_options = json_decode($buttons, true);

  return isset( $button_options[$option] ) ? $button_options[$option] : [ '' => 'Invalid option' ];
}

// Return button options
function get_sidebar_options( $option ){
  $sidebars = file_get_contents(  VAULT_PATH . "/data/sidebar.json");
  $sidebar_options = json_decode($sidebars, true);

  return isset( $sidebar_options[$option] ) ? $sidebar_options[$option] : [ '' => 'Invalid option' ];
}

// Return author options
function get_authors(){
  $authors = file_get_contents(  VAULT_PATH . "/data/authors.json");
  $author_options = json_decode($authors, true);

  return !empty( $author_options ) ? $author_options : [ '' => 'Invalid option' ];
}

// Get all possible mains to select from
function get_main_stylesheets(){

  $dir = new DirectoryIterator(VAULT_PATH .'/sections/assets/css');
  $styles = [];

  foreach ($dir as $file) {
    if (!$file->isDot() && !$file->isDir()) {
      $styles[$file->getFilename()] = $file->getFilename();
    }
  }

  return array_reverse($styles);

}

// Return font options
function get_font_styles(){
  
  $dir = new DirectoryIterator(VAULT_PATH .'/sections/assets/fonts');
  $styles = [];

  foreach ($dir as $file) {
    if (!$file->isDot() && !$file->isDir()) {
      $styles[$file->getFilename()] = $file->getFilename();
    }
  }

  return array_reverse($styles);

}
