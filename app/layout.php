<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title><?php echo isset($config->title)? $config->title: 'Just! framework - The simplest' ?></title>
<?php if (isset($config->description)): ?>
<meta name="description" content="<?php echo $config->description ?>">
<?php endif ?>
<?php foreach (array_unique($config->css) as $css): ?>
<link rel="stylesheet" href="<?php echo strpos($css, 'http') === 0? $css: $config->web . $css ?>">
<?php endforeach ?>
<script>var jq = []</script>
<meta http-equiv="X-UA-Compatible" content="chrome=1">
<?php if (isset($config->canonical)): ?>
<link rel="canonical" href="<?php echo $config->canonical ?>">
<?php endif ?>
</head>
<body>

<?php echo $content ?>

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