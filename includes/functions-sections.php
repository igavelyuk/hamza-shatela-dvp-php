<?php

// Get list of all sections without any grouping
function get_sections_raw(){

  $sections = [];
  $path = VAULT_PATH . '/sections';

  foreach( new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $path ) ) as $file ) {

    $parent = basename(dirname($file->getPathname())) != 'sections' ? basename(dirname($file->getPathname())) : '';

    if( $parent == 'assets' || $parent == 'excludes' || $parent == 'partials' )
      continue;

    if( !$file->isDir() && $file->getExtension() == 'html' ){
      $sections[] = [
        'name'          => $file->getFilename(),
        'path'          => $file->getPathname(),
        'relative_path' => $parent != '' ? 'sections/' . $parent . '/' . $file->getFilename() : 'sections/' . $file->getFilename(),
        'parent_name'   => ( dirname( $file->getPathname() ) != $path ) ? $parent : '',
        'parent_path'   => ( dirname( $file->getPathname() ) != $path ) ? dirname( $file->getPathname() ) : '',
      ];
    }

  }

  return $sections;

}

// Check if a section has a parent
function has_parent( $section ){
  return !empty( $section['parent_name'] );
}

// Return the parent of the sections (Category)
function get_section_parents(){
  $section_raw = get_sections_raw();
  return array_filter(array_unique(array_column( $section_raw, 'parent_name' )));
}

// Group the raw sections and group them into categories
function get_sections(){
  $section_raw = get_sections_raw();
  $grouped_sections = [];

  foreach( $section_raw as $section ){

    $name = normalize_name( $section['name'] );

    if( $name ){
      $grouped_sections[ $name ][] = $section;
    }

  }

  return $grouped_sections;
}

// Return all the sections in HTML format
function get_sections_html(){
  $sections = get_sections();
  $i = 0;
  foreach( $sections as $key => $value ):
    ?>
    <div class="sigma_categories" data-order="<?php echo $i ?>" data-category="<?php echo str_replace( ' ', '-', $key ) ?>" data-parent="<?php echo $value[0]['parent_name'] ?>">
      <h4>
        <?php echo $key ?>
        <span>(<?php echo count($value) ?> sections)</span>
        <?php if( isset( $value[0] ) && has_parent( $value[0] ) ){ ?>
          <i> - in <?php echo $value[0]['parent_name'] ?> </i>
        <?php } ?>
      </h4>
      <span class="sigma_category-disable">
        <i class="fal fa-minus-hexagon"></i>
      </span>
      <div class="sigma_category-sections">
      <?php
      foreach( $value as $section ):
        ?>
          <span class="sigma_category-section" data-name="<?php echo $section['name'] ?>" data-section=<?php echo json_encode($section) ?>> <?php echo $section['name'] ?> <a href="<?php echo $section['relative_path'] ?>"> <i class="far fa-external-link"></i> </a>  </span>
        <?php
      endforeach;
      ?>
      </div>
    </div>
    <?php
    $i++;
  endforeach;
}

// Return all the added sections
function get_added_pages(){

  global $project_name;
  $sections = [];

  if( !file_exists( VAULT_PATH .'/' . $project_name ) ){
    return $sections;
  }

  $dir = new DirectoryIterator(VAULT_PATH .'/' . $project_name);

  foreach ($dir as $file) {
    if (!$file->isDot() && !$file->isDir() && $file->getExtension() == 'html') {
      $sections[] = [
        'name'          => $file->getFilename(),
        'path'          => $file->getPathname(),
        'relative_path' => $project_name . '/' . $file->getFilename(),
      ];
    }
  }

  return $sections;

}

// Return all the added sections in HTML format
function get_added_pages_html(){

  $added_pages = get_added_pages();
  $i = 0;

  ?>
  <h5 class="sigma_collapse-trigger">Pages Created (<span class="pages-added-count"><?php echo count($added_pages) ?></span>) </h5>
  <div class="sigma_collapse-content">
    <div class="sigma_card">
      <?php if( count($added_pages) > 0 ){ ?>
      <div class="dd" id="nestable_pages">
        <ol class="dd-list">
      <?php
      foreach( $added_pages as $key => $page ){
        if( file_exists($page['relative_path']) ){
        ?>
        <li class="dd-item sigma_section-added" title="<?php echo $page['name'] ?>" data-id="<?php echo $i ?>">
          <div class="dd-handle dd-burger-handle"></div>
          <div class="dd-content">
            <p><?php echo $page['name'] ?></p>
            <div class="sigma_section-added-controls">
              <a target="_blank" href="<?php echo $page['relative_path'] ?>"> <i class="far fa-external-link"></i> </a>
              <a target="_blank" class="delete-page" href="<?php echo $page['relative_path'] ?>"> <i class="far fa-trash"></i> </a>
            </div>
          </div>
        </li>
        <?php $i++; } } ?>
        </ol>
      </div>
      <?php }else{ ?>
        <p>No Pages Added</p>
      <?php } ?>
      <?php if(count($added_pages) > 0){ ?>
      <form method="post" id="add-custom-page">
        <div class="input-group mt-3">
          <input type="text" class="form-control" placeholder="Custom Page" id="custom-page">
          <div class="input-group-append">
            <button class="sigma_btn" type="button"> <i class="far fa-plus"></i> </button>
          </div>
        </div>
      </form>
      <?php } ?>
    </div>
    <?php if(count($added_pages) > 0){ ?>
    <button type="button" name="button" class="sigma_btn light btn-block mt-3" id="map-headers"> Map Headers </button>
    <button type="button" name="button" class="sigma_btn danger btn-block mt-3" id="clear-pages"> Clear Pages </button>
    <button type="button" name="button" class="sigma_btn danger btn-block mt-3" id="delete-project">⚠️ Delete Project ⚠️</button>
    <?php } ?>
  </div>

  <?php

}

function get_topics(){

  $categories = file_get_contents(  VAULT_PATH . "/data/pages.json");
  $categories = json_decode($categories, true);

  if( is_array($categories) && !empty($categories) ){

    foreach( array_keys( $categories ) as $category ){
      ?>
      <option value="<?php echo strtolower( $category ); ?>"><?php echo ucwords( str_replace( '_', ' ', $category ) ); ?></option>
      <?php
    }
  }

}

?>
