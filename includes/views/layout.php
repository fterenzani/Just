<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title><?php echo isset($config->title)? $config->title: 'Just! framework - The simplest' ?></title>
<?php foreach (array_unique($config->css) as $css): ?>
<link rel="stylesheet" href="<?php echo strpos($css, 'http') === 0? $css: $config->web . $css ?>">
<?php endforeach ?>
<script>var jq = []</script>
<meta http-equiv="X-UA-Compatible" content="chrome=1">
</head>
<body>

<?php echo ob_get_clean() ?>

<?php foreach (array_unique($config->js) as $js): ?>
<script src="<?php strpos($js, 'http') === 0? $js: $config->web . $js ?>"></script>
<?php endforeach ?>
<script>
(function(){
for (var i = 0, length = jq.length; i < length; ++i) {
 jq[i]();
}
jq.push = function(func) {func()};
})();
</script>
</body>