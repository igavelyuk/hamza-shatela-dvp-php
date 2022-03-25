<?php
include 'functions.php';
$added_pages = get_added_pages();
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title> Sigma Vault </title>

    <link rel="stylesheet" href="assets/css/plugins/jquery-ui.min.css">
    <link rel="stylesheet" href="assets/css/plugins/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/plugins/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/plugins/toastr.min.css">
    <link rel="stylesheet" href="assets/css/plugins/magnific-popup.css">
    <link rel="stylesheet" href="assets/css/plugins/select2.min.css">

    <link rel="stylesheet" href="assets/css/main.css">
  </head>
  <body>

    <div class="section">
      <div class="container">

        <header class="sigma_card sigma_header">
          <ul>
            <li> <a class="active" href="index.php">Vault</a> </li>
            <li> <a href="tester.php">Test Sections</a> </li>
          </ul>
        </header>

        <h1>Build Sections (<?php echo count( get_sections_raw() ) ?> sections)</h1>

        <div class="row">
          <div class="col-lg-3">
            <div class="sigma_sticky">
              <div class="sigma_card sigma_blockable">
                <h5 class="sigma_collapse-trigger">Advanced Settings</h5>
                <div class="sigma_collapse-content">
                  <form id="advanced-settings" method="post">
                    <!-- <div class="form-group">
                      <label for="main-sheet">Author</label>
                      <select class="form-control" id="template-author" name="template-author">
                        <?php foreach( get_authors() as $key => $val ){ ?>
                        <option value="<?php echo $val['name'] ?>"><?php echo $val['name'] ?></option>
                        <?php } ?>
                      </select>
                    </div> -->
                    <div class="form-group">
                      <label for="main-sheet">Stylesheet</label>
                      <select class="form-control" id="main-sheet" name="main-sheet">
                        <?php foreach( get_main_stylesheets() as $key => $val ){ ?>
                        <option value="<?php echo $key ?>"><?php echo $val ?></option>
                        <?php } ?>
                      </select>
                    </div>
                    <div class="form-group">
                      <label for="sidebar-style">Sidebar Style</label>
                      <select class="form-control" id="sidebar-style" name="sidebar-style">
                        <?php foreach( get_sidebar_options( 'styles' ) as $key => $val ){ ?>
                        <option value="<?php echo $key ?>"><?php echo $val ?></option>
                        <?php } ?>
                      </select>
                    </div>
                    <div class="form-group">
                      <label for="btn-style">Buton Style</label>
                      <select class="form-control" id="btn-style" name="btn-style">
                        <?php foreach( get_btn_options( 'styles' ) as $key => $val ){ ?>
                        <option value="<?php echo $key ?>"><?php echo $val ?></option>
                        <?php } ?>
                      </select>
                    </div>
                    <div class="form-group">
                      <label for="btn-shape">Button Shape</label>
                      <select class="form-control" id="btn-shape" name="btn-shape">
                        <?php foreach( get_btn_options( 'shapes' ) as $key => $val ){ ?>
                        <option value="<?php echo $key ?>"><?php echo $val ?></option>
                        <?php } ?>
                      </select>
                    </div>
                    <!-- <div class="form-group">
                      <label for="font-style">Font Style</label>
                      <select class="form-control" id="font-style" name="font-style"> -->
                        <?php foreach( get_font_styles( ) as $key => $val ){ ?>
                        <!-- <option value="<?php echo $key ?>"><?php echo $val ?></option> -->
                        <?php } ?>
                      <!-- </select>
                    </div> -->
                    <div class="form-group mb-0">
                      <div data-toggle="tooltip" title="Keep the added sections selected after page creation" class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="keep-sections">
                        <label class="custom-control-label" for="keep-sections">Keep sections after creation</label>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
              <div class="sigma_card sigma_templates-result sigma_blockable">
                <h5 class="sigma_collapse-trigger">Templates</h5>
                <div class="sigma_collapse-content">
                  <div class="sigma_card" id="templates-area"></div>
                  <form id="templates-form" method="post">
                    <div class="form-group">
                      <input type="text" class="form-control" required id="template-name" name="template-name" placeholder="Template Name" value="">
                    </div>
                    <button type="submit" id="save-template" class="sigma_btn warning btn-block mt-3" name="button">Save Template</button>
                  </form>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-6">
            <form class="search-categories" method="post">
              <div class="form-group">
                <input type="text" class="form-control" placeholder="Search Categories" id="search-categories" name="search-categories" value="">
              </div>
              <div class="sigma_search-controls">
                <div class="form-group">
                  <select class="form-control" id="category-list"  name="category-list">
                    <option value="">Select Category</option>
                    <?php foreach( get_section_parents() as $parent ){ ?>
                      <option value="<?php echo $parent ?>"><?php echo $parent ?></option>
                    <?php } ?>
                  </select>
                </div>
                <div class="form-group">
                  <button type="button" data-toggle="tooltip" class="sigma_btn light toggle-selected" title="Show selected only" name="button"> <i class="far fa-eye"></i> </button>
                </div>
                <div class="form-group">
                  <button type="button" data-toggle="tooltip" class="sigma_btn light toggle-all" title="Select all" name="button"> <i class="far fa-ballot-check"></i> </button>
                </div>
              </div>
            </form>

            <div class="sigma_categories-wrap sigma_sortable" id="sortable-sections">
              <?php get_sections_html(); ?>
            </div>

          </div>
          <div class="col-lg-3">

            <div class="sigma_sections-result sigma_card sigma_blockable">
              <h5 class="sigma_collapse-trigger">Result</h5>
              <div class="sigma_collapse-content">
                <div id="result-area" class="sigma_card">
                  <p>No Sections added</p>
                </div>

                <form id="generate-html-form" method="post">
                  <div class="form-group">
                    <input type="text" class="form-control" required id="page-name" autocomplete="off" name="page-name" placeholder="Page Name (e.g. Blog Grid)" value="">
                  </div>
                  <button type="submit" name="button" class="sigma_btn btn-block mt-3" id="generate-html"> Generate Page </button>
                </form>
                <button type="button" name="button" class="sigma_btn success btn-block <?php if( count($added_pages) == 0 ){ echo 'd-none'; } ?> mt-3" id="download-project"> Download Project </button>
              </div>
            </div>
            <div class="sigma_card sigma_pages-result sigma_blockable">
              <div id="pages-area">
                <?php get_added_pages_html(); ?>
              </div>
            </div>
            <div class="sigma_card">
              <h5 class="sigma_collapse-trigger">Auto Generate</h5>
              <div class="sigma_collapse-content">
                <form id="auto-generate" method="post">
                  <div class="custom-control custom-checkbox my-3">
                    <input type="checkbox" name="one-page" class="custom-control-input" id="one-page">
                    <label class="custom-control-label" for="one-page">Make this one page</label>
                  </div>
                  <select class="form-control" id="auto-generate-topic" name="available-topics">
                    <?php get_topics() ?>
                  </select>
                  <button type="submit" class="sigma_btn success btn-block mt-3" name="button">Generate Template</button>
                </form>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>

    <script type="text/javascript" src="assets/js/plugins/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="assets/js/plugins/jquery-ui.min.js"></script>
    <script type="text/javascript" src="assets/js/plugins/popper.min.js"></script>
    <script type="text/javascript" src="assets/js/plugins/bootstrap.min.js"></script>
    <script type="text/javascript" src="assets/js/plugins/toastr.min.js"></script>
    <script type="text/javascript" src="assets/js/plugins/jquery.magnific-popup.min.js"></script>
    <script type="text/javascript" src="assets/js/plugins/jquery.nestable.js"></script>
    <script type="text/javascript" src="assets/js/plugins/select2.min.js"></script>

    <script type="text/javascript" src="assets/js/utils.js"></script>
    <script type="text/javascript" src="assets/js/templates.js"></script>
    <script type="text/javascript" src="assets/js/headerMapping.js"></script>
    <script type="text/javascript" src="assets/js/main.js"></script>

  </body>
</html>
