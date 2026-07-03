<?php
// Minimal front controller to ensure root requests load the home page.
// Delegate to the main controller entry point instead of recursively requiring itself.
require_once __DIR__ . '/../controllers/indexcontroller.php';
