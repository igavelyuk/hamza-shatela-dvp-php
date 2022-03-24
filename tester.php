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
            <li> <a href="index.php">Vault</a> </li>
            <li> <a class="active" href="tester.php">Test Sections</a> </li>
          </ul>
        </header>

        <h1>Test Sections</h1>

        <div class="sigma_card sigma_blockable">

          <form id="section-test-form" enctype="multipart/form-data" method="post">
            <label for="file" class="sigma_input-file">
              <i class="far fa-cloud-upload"></i> Upload Files
            </label>
            <input multiple id="file" name="file[]" type="file"/>
            <button type="submit" class="sigma_btn d-block">Run Tests</button>
          </form>

          <p class="count-files"><b>0</b> files were added</p>

          <div class="mt-3" id="section-test-uploaded-files"> No Files uploaded yet. Once you upload the files, they will be added here. </div>

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
    <script type="text/javascript" src="assets/js/tester.js"></script>

  </body>
</html>
