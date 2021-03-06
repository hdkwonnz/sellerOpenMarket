@extends('layouts.sellerApp')

@section('content')
<div class="container mt-5">
    <div class="row no-gutters">
        <div class="col-md-12 col-sm-12">
            <div class="bg-warning" style="border: 2px solid black; min-height: 600px;">
                <div class="display-4 m-5">
                    You are in Seller's page.
                </div>
                @auth
                @if ((Auth::user()->role) == 'seller')
                <div class="display-4 m-5">
                    <span style="word-break: break-all;">
                        Welcome to H-Mart... <br>
                        Please select your menu.
                    </span>
                </div>
                @else
                @php
                 Auth::logout();
                @endphp
                @endif
                @endauth

                @guest
                <div class="display-4 m-5">
                    <span style="word-break: break-all;">
                        Please login in <br>
                        or <br>
                        You need to get seller's permissions after register.
                    </span>
                </div>
                @endguest
            </div>
        </div>
    </div>
</div>
@endsection
