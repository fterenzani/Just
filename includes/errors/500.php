<?php
require_once dirname(__FILE__) . '/common.php';

$config->error_title = 'Internal server error';

set_status('500');
?>


<h1>Dho!</h1>
<p>Internal server error :-(</p>
<?php if ($config->env != 'prod'): ?>
  <p style="background: yellow"><?php echo get_class($exception) ?>: <?php echo $exception->getMessage() ?>
  <p><b>Stacktrace:</b>
  <pre><?php echo $exception->getTraceAsString() ?></pre>
<?php endif ?>


<?php die ?>