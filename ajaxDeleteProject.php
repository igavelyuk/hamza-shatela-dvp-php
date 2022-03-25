<?php

include 'includes/functions-ajax.php';
include 'includes/functions-sections.php';

global $project_name;

delete_files( $project_name );

ob_start();
get_added_pages_html();
$pages = ob_get_clean();

echo json_encode( [ 'status'  => 'success', 'message' => 'Project Deleted', 'pages' => json_encode($pages)] );
die();
