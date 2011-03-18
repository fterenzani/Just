<?php
$content = ob_get_clean();
?>
<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title><?php echo $config->title ?></title>
<style>
html, body {padding: 0; margin: 0}
body {font: .9em arial; text-align: center; background: #eee}
h1 {font-size: 1em}
.container {margin: auto; text-align: left; width: 450px; position: relative; top: 50px; background: #fff; border: 1px solid #ccc;padding: 10px;}
</style>
</head>
<div class="container">

	<?php echo $content ?>

</div>
</body>