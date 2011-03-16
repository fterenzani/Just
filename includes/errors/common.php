<?php

require_once 'helpers.php';

$config->layout = dirname(__FILE__) . '/layout.php';

// Clean the previous buffer
ob_end_clean();
ob_start();
