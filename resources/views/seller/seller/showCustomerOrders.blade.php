@extends('layouts.sellerApp')

@section('content')
<div class="container mt-5">

    <customer-orders user_role="{{ auth()->user()->role }}"></customer-orders>

</div>
@endsection
