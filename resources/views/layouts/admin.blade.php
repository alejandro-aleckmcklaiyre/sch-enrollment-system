@extends('layouts.base')

@section('content')
<div class="d-flex" style="gap:20px; align-items:flex-start;">
	<aside class="sidebar" style="width:220px;">
		@include('layouts.admin-sidebar')
	</aside>
	<main class="main-content">
		<h2 class="mb-3">@yield('page-title', 'Admin Panel')</h2>
		@yield('page-content')
	</main>
</div>
@endsection