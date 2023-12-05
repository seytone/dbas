<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
		@yield('styles')
	</head>
	<body>
		@yield('content')
		<script>
			$(function()
			{
				$('.selectize-create').selectize({
					create: true,
					sortField: 'text'
				});
			});
		</script>
		@yield('scripts')
	</body>
</html>