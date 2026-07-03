<?php
// Redirect root requests to the application entry point
// This ensures visiting `/` serves the job site index
header('Location: ./ass2/jobs/index.php');
exit;
