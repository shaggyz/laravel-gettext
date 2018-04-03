<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Hello World</title>
</head>
<body>
	<section>
		{{ _i("Hello %s, how are you ?", 'Test') }}
	</section>
	<footer>
		<?= _i("Are you having a good day ?") ?>
	</footer>
</body>
</html>