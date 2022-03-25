<?php

// Functions responsible for utilities
include dirname(dirname(__FILE__)) . '/constants.php';
include dirname(__FILE__) . '/functions-options.php';

// Meta fields which will be added to the <head>
function get_meta_fields(){
  return $meta = array(
    array('name' => 'viewport', 'content' => 'width=device-width, initial-scale=1'),
    array('name' => 'description', 'content' => 'Description website'),
    array('name' => 'author', 'content' => 'Maksym Blank'),
    array('name' => 'keywords', 'content' => 'website, with, meta, tags'),
    array('name' => 'robots', 'content' => 'noindex, follow'),
    array('name' => 'revisit-after', 'content' => '5 month'),
    array('name' => 'image', 'content' => 'http://mywebsite.com/image.jpg'),
    array('name' => 'og:title', 'content' => 'Title website'),
    array('name' => 'og:description', 'content' => 'Description website'),
    array('name' => 'og:image', 'content' => 'http://mywebsite.com/image.jpg'),
    array('name' => 'og:site_name', 'content' => 'My Website'),
    array('name' => 'og:type', 'content' => 'website'),
    array('name' => 'twitter:card', 'content' => 'summary'),
    array('name' => 'twitter:title', 'content' => 'Title website'),
    array('name' => 'twitter:description', 'content' => 'Description website'),
  );
}

// CSS/JS which will be added to the <head> and before </body>
function get_dependency_resources(){
  $resources = file_get_contents( VAULT_PATH . "/data/resources.json");
  return json_decode($resources, true);
}

// Return list of one page templates
function get_opt_list(){
  $resources = file_get_contents( VAULT_PATH . "/data/onePage.json");
  return json_decode($resources, true);
}

// Vendor CSS/JS which will be added to the <head> and before </body>
function get_vendor_resources(){
  return array(
    'assets/fonts/font-awesome.min.css'
  );
}

// adds the niche css to the main stylesheet
function add_niche_resources( $pages, $style_sheet ){

  global $project_name;

  foreach( $pages as $page ){
    if( isset( $page['parent_name'] ) && !empty( $page['parent_name'] ) ){

      // add niche css to main stylesheet
      if( !file_exists( $project_name . '/assets/css/' . $page['parent_name'] . '.css' ) ){
        $niche_sheet = $page['parent_path'] . '/' . strtolower($page['parent_name']) . '.css';
        if( file_exists( $niche_sheet ) && $niche_sheet = file_get_contents( $niche_sheet ) ){
          // Put the content into the main stylesheet then mark that this niche has already been added (incase multiple sections of the same niche are selected)
          file_put_contents( $project_name . '/assets/css/' . $style_sheet , $niche_sheet.PHP_EOL , FILE_APPEND | LOCK_EX );
          copy( 'sections/'. $page['parent_name'] . '/' . $page['parent_name'] . '.css', $project_name . '/assets/css/' . $page['parent_name'] . '.css' );
        }else{
          echo json_encode( [ 'status'  => 'error', 'message' => 'Niche section selected, but no niche stylesheet found. ' . $page['name'] . ' skipped' ] );
          die();
        }
      }

      // add niche js to main js script
      if( !file_exists( $project_name . '/assets/js/' . $page['parent_name'] . '.js' ) ){
        $niche_script = $page['parent_path'] . '/' . strtolower($page['parent_name']) . '.js';
        if( file_exists( $niche_script ) && $niche_script = file_get_contents( $niche_script ) ){
          // Put the content into the js script then mark that this niche has already been added (incase multiple sections of the same niche are selected)
          file_put_contents( $project_name . '/assets/js/main.js' , $niche_script.PHP_EOL , FILE_APPEND | LOCK_EX );
          copy( 'sections/'. $page['parent_name'] . '/' . $page['parent_name'] . '.js', $project_name . '/assets/js/' . $page['parent_name'] . '.js' );
        }
      }

    }
  }

}

// deletes all the niche added files
function delete_niche_resources(){

  global $project_name;

  // Delete any niche css
  foreach (new DirectoryIterator( $project_name . '/assets/css' ) as $file) {
    if(!$file->isDot() && !$file->isDir()){

      if (strpos($file->getFilename(), 'style') === false)
        unlink($file->getPathname());

    }
  }

  // Delete any niche js
  foreach (new DirectoryIterator( $project_name . '/assets/js' ) as $file) {
    if(!$file->isDot() && !$file->isDir()){

      if (strpos($file->getFilename(), 'main') === false)
        unlink($file->getPathname());

    }
  }

}

// Create the base folder structure
function create_project( $style_sheet ){

  global $project_name;

  ini_set('max_execution_time', '300');

  // Does the project directory already exist?
  if( !is_dir( $project_name ) ){

    if( mkdir( $project_name, 0777 ) ){
      // Create our assets structure only once
      create_assets_structure();
    }else{
      echo json_encode( [ 'status'  => 'error', 'message' => 'Permission error: Failed to create project directory' ] );
      die();
    }

    // Create a text file to push the resources into (Image count, Icon count, files used)
    fopen($project_name ."/resources.txt", "w");

    // Start adding the files we need to the assets
    add_fontawesome();
    add_styles( $style_sheet );
    add_images();

    // Add functional contact form
    add_contact_form_assets();

  }

}

// Add content to the resources text file.
function write_to_resource_text_file( $pages, $page_name, $image_count, $icon_count ){

  global $project_name;

  file_put_contents(
    $project_name . '/resources.txt',
    $page_name . ' Page: ' . PHP_EOL .'**********' . PHP_EOL .
    'Image Count: ' . $image_count . PHP_EOL .
    'Icon Count: ' . $icon_count . PHP_EOL .
    'Pages: ' . implode( ' - ', array_column( $pages, 'name' ) ) . PHP_EOL . PHP_EOL,
    FILE_APPEND
  );

}

// Add the files needed for a functional contact form
function add_contact_form_assets(){

  global $project_name;

  copy( 'assets/functions/contact/mf_form.js', $project_name . '/assets/functions/mf_form.js' );
  copy( 'assets/functions/contact/sendmail.php', $project_name . '/sendmail.php' );

}

// Adds contact form references in the html file, and adjust the contents of mf_form.js to include the author recaptcha site_key
function add_contact_form( $template_author, $head, $body, $doc ){

  global $project_name;

  $template_author = strtolower( str_replace('-', '_', $template_author) );
  $authors = get_authors();
  $author = isset( $authors[$template_author] ) ? $authors[$template_author] : '';

  if( $author ){

    $captcha_secret_key = $author['secret_key'];
    $captcha_site_key = $author['site_key'];
    $existing_key = '';

    $node = $head->appendChild($doc->createElement('script'));
    $node->setAttribute('src', '//www.google.com/recaptcha/api.js?render='.$captcha_secret_key);

    $node = $body->appendChild($doc->createElement('script'));
    $node->setAttribute('src', 'assets/functions/mf_form.js');
    $node->setAttribute('type', 'text/javascript');

    // Replace the keys in mf_form.js
    $mf_form_path = $project_name . '/assets/functions/mf_form.js';
    $mf_form_content = file_get_contents($mf_form_path);
    $mf_form_content = str_replace( 'site_key_here', $captcha_secret_key, $mf_form_content );
    file_put_contents($mf_form_path, $mf_form_content);

    // Replace the keys in sendmail.php
    $sendmail_path = $project_name . '/sendmail.php';
    $sendmail_content = file_get_contents($sendmail_path);
    $sendmail_content = str_replace( 'site_key_here', $captcha_site_key, $sendmail_content );
    file_put_contents($sendmail_path, $sendmail_content);

  }

}

// Create all the asset folders
function create_assets_structure(){

  global $project_name;

  $assets = array(
    '/assets/css/plugins',
    '/assets/functions',
    '/assets/js/plugins',
    '/assets/img',
  );

  foreach($assets as $asset) {

    $dirs = explode('/' , $asset);
    $mkDir = $project_name . '/';

    foreach( $dirs as $folder ){
      $mkDir = $mkDir . $folder ."/";
      if(!is_dir($mkDir)) {
        mkdir($mkDir, 0777);
      }
    }

  }

}

// Adds all project assets to the assets directory
function add_styles( $style_sheet ){
  global $project_name;

  $resources = get_dependency_resources();

  foreach( $resources as $resource_type ){
    foreach( $resource_type as $resource ){
      // Copy from sections/assets/... to project/assets/css/...
      copy( 'sections/'.$resource, $project_name . '/' . $resource );
    }
  }

  // Copy the main stylesheet selected
  if( !empty($style_sheet) ){
    copy( 'sections/assets/css/'. $style_sheet, $project_name . '/assets/css/' . $style_sheet );
  }

}

// Adds all fontawesome files to the project
function add_fontawesome(){
  global $project_name;
  recursive_copy( 'vendor/font-awesome', $project_name . '/assets/fonts' );
}

// Adds all image files to the project
function add_images(){
  global $project_name;
  recursive_copy( 'sections/assets/img', $project_name . '/assets/img' );
}

// Copies all files and folders in a directory recursively
function recursive_copy( $src, $dst ){

  $dir = opendir($src);
  if( !is_dir($dst) ){
    mkdir($dst);
  }
  while(false !== ( $file = readdir($dir)) ) {
    if (( $file != '.' ) && ( $file != '..' )) {
      if ( is_dir($src . '/' . $file) ) {
        recursive_copy($src . '/' . $file,$dst . '/' . $file);
      } else {
        copy( $src . '/' . $file, $dst . '/' . $file );
      }
    }
  }
  closedir($dir);

}

// Delete files recursively
function delete_files($path) {

  if (!file_exists($path)) return false;

  if (is_file($path) || is_link($path)) {
    return unlink($path);
  }

  if (is_dir($path)) {
    $path = rtrim($path, '/') . '/';

    $dir = new DirectoryIterator($path);

    foreach ($dir as $file) {
      if (!$file->isDot()) {
        delete_files($path . $file->getFilename());
      }
    }

    rmdir($path);
  }

}

// Recursively add the map the menu to an html page
function importMenuNodes( array $menus, DomElement $node, DOMDocument $doc, bool $is_one_page = false ){

  if(empty($menus) || empty($node)){
    echo json_encode( [ 'status'  => 'error', 'message' => 'No headers / menus found' ] );
    die();
  }

  foreach($menus as $menu){

    // <li> tag
    $li = $node->appendChild( $doc->createElement('li') );
    $li->setAttribute('class', 'menu-item');

    // <a> tag
    $a = $li->appendChild( $doc->createElement('a') );
    if( $is_one_page ){
      $a->setAttribute('href', '#section-'.$menu['path']);
    }else{
      $a->setAttribute('href', $menu['path']);
    }
    $a->nodeValue = ucfirst(str_replace( '.html', '', str_replace('-', ' ', $menu['name']) ));

    if( isset($menu['children']) && !empty($menu['children']) ){
      $li->setAttribute('class', 'menu-item menu-item-has-children');

      // <ul> submenu tag
      $ul = $li->appendChild( $doc->createElement('ul') );
      $ul->setAttribute('class', 'sub-menu');

      importMenuNodes( $menu['children'], $ul, $doc );

    }

  }

}

// Recursively map the sections to the nav for one page template
function map_one_page_template_header( array $menu_items ){

}

// check if header exists in a file
function check_header_exists( $file ){

  $content = file_get_contents( $file );

  $doc = new DOMDocument();
  libxml_use_internal_errors(true);
  $doc->loadHTML($content); // Content of every file
  libxml_use_internal_errors(false);

  $classname = "navbar-nav";
  $xpath = new DomXPath($doc);
  $searchNode = $xpath->query("//*[contains(@class, '$classname')]"); // The navbar-nav items

  return $searchNode->length != 0;

}

// check if sigma-section exists in a file
function check_sigma_section_exists( $file ){

  $content = file_get_contents( $file );

  $doc = new DOMDocument();
  libxml_use_internal_errors(true);
  $doc->loadHTML($content); // Content of every file
  libxml_use_internal_errors(false);

  $searchNode = $doc->getElementsByTagName("sigma-section");

  return $searchNode->length != 0;

}

// Download the img from via.placeholder and replace the src in images.
function download_images( $image, $project_name, $page_slug, $is_img = true ){

  $img_url = $is_img == true ? $image->getAttribute('src') : str_replace( ["'", ")", ";"], '', $image->getAttribute('style') );

  if( !is_dir( $project_name . '/assets/img/' . $page_slug ) ){
    mkdir( $project_name . '/assets/img/' . $page_slug );
  }

  // Checks if the image is a logo first
  if( $is_img && $image->getAttribute('alt') == 'logo' ){
    $img_path = $project_name . '/assets/img/logo.jpg';
    save_remote_image( $img_url, $img_path );
    $image->setAttribute('src', 'assets/img/logo.jpg');
  }else{

    $img_path = 'assets/img/' . $page_slug . '/' . basename($img_url);
    $full_img = $project_name . '/' . $img_path . '.jpg';
    $count = 0;

    while(file_exists($full_img)){
      $img_path = 'assets/img/' . $page_slug . '/' . basename($img_url);
      $img_path = $img_path . '-' . $count;
      $full_img = $project_name . '/' . $img_path . '.jpg';
      $count++;
    }

    save_remote_image( $img_url, $full_img );

    if( $is_img ){
      $image->setAttribute('src', $img_path . '.jpg');
    }else{
      $image->setAttribute('style', 'background-image: url('.$img_path.'.jpg)');
    }

  }

}

// Create an image
function save_remote_image( $img_url, $path ){

  $img_dimensions = basename($img_url);
  $img_url = explode( "x", $img_dimensions );
  $width = is_array( $img_url ) ? $img_url[0] : 0;
  $height = is_array( $img_url ) && count( $img_url ) > 1 ? $img_url[1] : $img_url[0];

  if( $width && $height ){

    $img = imagecreate($width, $height);

    $bg_color = imagecolorallocate($img, 204, 204, 204);
    $font_color = imagecolorallocate($img, 255, 255, 255);
    $font_size = 5;

    $char_width = imagefontwidth($font_size) * strlen($img_dimensions);
    $char_height = imagefontheight($font_size);

    imagestring($img, $font_size, ($width/2)-($char_width/2), ($height/2)-($char_height/2), $img_dimensions, $font_color);
    header("Content-Type: image/jpg");

    imagejpeg($img, $path);
    imagedestroy($img);

  }

}

?>
