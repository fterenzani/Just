<?php
require_once dirname(__FILE__) . '/common.php';

$config->error_title = 'Page not fount';

set_status('404');
?>


<h1>Dho!</h1>
<p>Page not fount :-(</p>

<?php die ?>