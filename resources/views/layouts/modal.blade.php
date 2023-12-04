<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<meta name="mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-capable" content="yes">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
	<meta content="#eee423" name="theme-color">
	<meta content="#eee423" name="msapplication-TileColor">
	<meta content="{{ asset('img/app-icon.png') }}" name="msapplication-TileImage">
	<link href="{{ asset('img/app-icon.png') }}" sizes="200x200" rel="apple-touch-icon">
	<link href="{{ asset('img/app-icon.png') }}" sizes="200x200" type="image/png" rel="icon">
    <link href="{{ asset('favicon.ico') }}" rel="shortcut icon" type="image/x-icon" />
	<link href="{{ asset('manifest.json') }}" rel="manifest">
	
	<title>{{ env('APP_NAME', 'LICENCIAS BIT') }}</title>

    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" rel="stylesheet" />
    <link href="https://use.fontawesome.com/releases/v5.2.0/css/all.css" rel="stylesheet" />
    <link href="https://unpkg.com/@coreui/coreui@2.1.16/dist/css/coreui.min.css" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.min.css" rel="stylesheet" />
	{{-- Selectize --}}
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.15.2/css/selectize.default.min.css" integrity="sha512-pTaEn+6gF1IeWv3W1+7X7eM60TFu/agjgoHmYhAfLEU8Phuf6JKiiE8YmsNC0aCgQv4192s4Vai8YZ6VNM6vyQ==" crossorigin="anonymous" referrerpolicy="no-referrer"/>
    {{-- Custom Styles --}}
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet" />
    @yield('styles')
</head>

<body>
	@yield('content')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
    <script src="https://unpkg.com/@coreui/coreui@2.1.16/dist/js/coreui.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.full.min.js"></script>
    <!-- Core JS File. The JS code needed to make eventCalendar works -->
    <script src="{{ asset('plugins/vissitCalendar/jquery.eventCalendar.min.js') }}" type="text/javascript"></script>
    <!-- Bootstrap Maxlength -->
    <script src="{{ asset('plugins/bootstrapMaxlength/src/bootstrap-maxlength.js') }}" type="text/javascript"></script>
	{{-- Sweet Alerts --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>
	{{-- Selectize --}}
	<script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.15.2/js/selectize.min.js" integrity="sha512-IOebNkvA/HZjMM7MxL0NYeLYEalloZ8ckak+NDtOViP7oiYzG5vn6WVXyrJDiJPhl4yRdmNAG49iuLmhkUdVsQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	{{-- Number Spinner --}}
	<script src="https://cdn.jsdelivr.net/npm/bootstrap-input-spinner@3.3.3/src/bootstrap-input-spinner.min.js"></script>
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