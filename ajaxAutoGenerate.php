<?php
include 'includes/functions-ajax.php';

global $project_name;

delete_niche_resources();

echo json_encode( [ 'status'  => 'success', 'message' => 'Project generated successfully' ] );
die();
