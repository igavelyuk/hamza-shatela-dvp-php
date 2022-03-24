<?php
include 'includes/functions-ajax.php';
include 'includes/functions-sections.php';
include 'includes/classes/class-beautify-html.php';

global $project_name;

$content = $searchNode = '';

// Vars
$pages = isset( $_POST['pages'] ) ? $_POST['pages'] : '';
$topic = isset( $_POST['topic'] ) ? $_POST['topic'] : '';
$page_name = isset( $_POST['page_name'] ) ? htmlspecialchars($_POST['page_name']) : '';
$style_sheet = isset( $_POST['style_sheet'] ) ? htmlspecialchars($_POST['style_sheet']) : '';
$btn_style = isset( $_POST['btn_style'] ) ? htmlspecialchars($_POST['btn_style']) : 'btn-style-1';
$btn_shape = isset( $_POST['btn_shape'] ) ? htmlspecialchars($_POST['btn_shape']) : 'btn-rounded';
$sidebar_style = isset( $_POST['sidebar_style'] ) ? htmlspecialchars($_POST['sidebar_style']) : 'sidebar-style-1';
$template_author = isset( $_POST['template_author'] ) ? htmlspecialchars($_POST['template_author']) : 'slidesigma';
$is_one_page = htmlspecialchars($_POST['is_one_page']);

$image_count = 0;
$icon_count = 0;

if( !is_array( $pages ) && empty( $pages ) ){
  echo json_encode( [ 'status'  => 'error', 'message' => json_encode($pages) ] );
  die();
}
if( empty( $page_name ) ){
  echo json_encode( [ 'status'  => 'error', 'message' => 'Please specify a name for the page' ] );
  die();
}

$page_name = ucwords( $page_name );
$page_slug = strtolower( str_replace( ' ', '-', $page_name ) );

// Loop the added pages
foreach( $pages as $page ){
  $content .= file_get_contents( $page['path'] );
}

libxml_use_internal_errors(true);

$doc = new DOMDocument();
$doc->preserveWhiteSpace = false;
$doc->loadHTML($content);
$doc->formatOutput = true;

$searchNode = $doc->getElementsByTagName("sigma-section");

if( !empty( $searchNode ) ){

  $node_count = 0;

  $doc = new DOMDocument('1.0');
  $doc->preserveWhiteSpace = false;
  $doc->loadHTML('<!DOCTYPE HTML>');
  $doc->formatOutput = true;

  $html = $doc->appendChild($doc->createElement('html'));
  $head = $html->appendChild($doc->createElement('head'));

  $meta = get_meta_fields();
  $resources = get_dependency_resources();
  $vendor_resources = get_vendor_resources();

  // Add title tag
  $title = $head->appendChild($doc->createElement('title'));
  $title->nodeValue = $page_name;

  // Loop through the <meta> tags
  foreach ($meta as $attributes) {
    $node = $head->appendChild($doc->createElement('meta'));
    foreach ($attributes as $key => $value) {
      $node->setAttribute($key, $value);
    }
  }

  // Add the <link> tags to the head
  foreach ($resources['css'] as $key => $val) {
    $node = $head->appendChild($doc->createElement('link'));
    $node->setAttribute('href', $val);
    $node->setAttribute('rel', 'stylesheet');
  }

  // Assign the main stylesheet
  if( !empty($style_sheet) ){
    $node = $head->appendChild($doc->createElement('link'));
    $node->setAttribute('href', 'assets/css/' . $style_sheet);
    $node->setAttribute('rel', 'stylesheet');
  }

  // Add body tag
  $body = $html->appendChild($doc->createElement('body'));

  $body_classes = [ $btn_style, $btn_shape, $sidebar_style ];

  // Set utility styles here
  $body->setAttribute( 'class', implode( ' ', $body_classes ) );

  // Create all the assets directory
  create_project( $style_sheet );

  // Loop through the selected sections and add them to our DOM element
  foreach( $searchNode as $node ){

    // Fetch images from the section, change their URLs and upload the image to the project assets
    $node_images = $node->getElementsByTagName('img');
    foreach($node_images as $image) {
      download_images( $image, $project_name, $page_slug );
      $image_count++;
    }

    // Fetch images from the style attribute, change their URLs and upload the image to the project assets
    $node_divs = $node->getElementsByTagName('div');
    foreach($node_divs as $div) {
      if($div->hasAttribute('style')){
        download_images( $div, $project_name, $page_slug, false );
        $image_count++;
      }
    }

    $node_icons = $node->getElementsByTagName('i');
    foreach($node_icons as $icon) {
      $icon_classes = $icon->getAttribute('class');
      if( strpos($icon_classes, 'is_flaticon') !== false ){
        $icon_count++;
      }
    }

    // Add the node to the domdocument
    $section_id = substr($pages[$node_count]['name'], 0, strrpos( $pages[$node_count]['name'], '-'));

    $body->appendChild($doc->createComment( $section_id . ' Start' ));
    $wrapper_div = $body->appendChild($doc->createElement( 'div' ));
    $wrapper_div->setAttribute('id', 'section-' . $section_id);
    $wrapper_div->appendChild($doc->importNode($node, true));
    $wrapper_div->appendChild($doc->createComment( $section_id . ' End' ));

    $node_count++;

  }

  // add the <script> tags before the body closing tag
  foreach ($resources['js'] as $key => $val) {
    $node = $body->appendChild($doc->createElement('script'));
    $node->setAttribute('src', $val);
    $node->setAttribute('type', 'text/javascript');
  }

  // Push the Niche stylesheet content to main sheet (if any)
  add_niche_resources( $pages, $style_sheet );

  // Push the vendor files to the assets
  foreach ($vendor_resources as $resource) {
    $node = $head->appendChild($doc->createElement('link'));
    $node->setAttribute('href', $resource);
    $node->setAttribute('rel', 'stylesheet');
  }

  // Add contact form scripts to the contact page
  if ( strpos($page_slug, 'contact') !== false || $is_one_page == true ){
    add_contact_form( $template_author, $head, $body, $doc );
  }

  // Process one page template
  // if( $is_one_page == true ){
  //
  //   // Add one page functionality
  //   $one_page_scripts = file_get_contents( 'sections/assets/js/one-page.js' );
  //   file_put_contents( $project_name . '/assets/js/main.js' , $one_page_scripts.PHP_EOL , FILE_APPEND | LOCK_EX );
  //
  //   // Map the header
  //   $classname = "navbar-nav";
  //   $xpath = new DomXPath($doc);
  //   $headerNodes = $xpath->query("//*[contains(@class, '$classname')]"); // The navbar-nav items
  //
  //   foreach( $headerNodes as $header_node ){
  //
  //     // Empty up the navbar-navs
  //     while ($header_node->hasChildNodes()) {
  //       $header_node->removeChild($header_node->firstChild);
  //     }
  //
  //     // Refill the navbar-nav with the mapped content recursively
  //     $opt_list = get_opt_list();
  //
  //     $_menu_items = $opt_list[$topic][0]['menu_items'];
  //     $menu_items = [];
  //
  //     foreach($_menu_items as $_menu_item){
  //       $menu_items[] = [ 'name' => $_menu_item, 'path' => $_menu_item ];
  //     }
  //     importMenuNodes( $menu_items, $header_node, $doc, true );
  //
  //   }
  //
  // }

  // Replace subheader title with page name
  $classname = "sigma_subheader";
  $xpath = new DomXPath($doc);
  $subheader_nodes = $xpath->query("//*[contains(@class, '$classname')]"); // The sigma_subheader items

  if( $subheader_nodes->length != 0 ){
    foreach( $subheader_nodes as $subheader ){

      $headings = $subheader->getElementsByTagName('h1');

      // Replace heading title
      if($headings){
        foreach($headings as $heading){
          $heading->nodeValue = $page_name;
        }
      }

    }
  }

  // Replace breadcrumb active title with page name
  $classname = "breadcrumb";
  $xpath = new DomXPath($doc);
  $breadcrumb_nodes = $xpath->query("//*[contains(@class, '$classname')]"); // The breadcrumb items

  if( $breadcrumb_nodes->length != 0 ){
    foreach( $breadcrumb_nodes as $breadcrumb ){

      $breadcrumb_items = $breadcrumb->getElementsByTagName('li');

      // Replace heading title
      if($breadcrumb_items){
        foreach($breadcrumb_items as $key => $item){
          if( $key == count($breadcrumb_items) - 1 ){
            $item->nodeValue = $page_name;
          }
        }
      }

    }
  }

  // Strip <sigma-section>
  $final_html = str_replace( ['</sigma-section>', '<sigma-section>'], '', $doc->saveHTML() );

  $beautifier_options = array(
    'indent_size' => 2
  );

  $beautifier = new Beautify_Html( $beautifier_options );
  $final_html = $beautifier->beautify($final_html);

  $final_doc = new DOMDocument();
  $final_doc->preserveWhiteSpace = false;
  $final_doc->loadHTML($final_html);
  $final_doc->formatOutput = true;

  $final_doc->saveHTMLFile( $project_name . '/' . $page_slug.'.html' );

  libxml_use_internal_errors(false);

  write_to_resource_text_file( $pages, $page_name, $image_count, $icon_count );

  ob_start();
  get_added_pages_html();
  $pages = ob_get_clean();

  echo json_encode([
    'status'  => 'success',
    'message' => $page_slug . '.html generated succesfully',
    'relative_path' => $project_name . '/' . $page_slug . '.html',
    'pages' => json_encode($pages)
  ]);
  die();

}

?>
