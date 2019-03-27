@extends('layouts.app')
@section('content')
    <div>
        <button id="open-form">Open Form</button>
        <form id="formSubmitProblemTraffic" action="/client/complaints" method="post">
            @csrf
            Title <input type="text" name="title">
            <br>
            <select name="type" >
                <option value="jam">Tac duong</option>
                <option value="accident">Tai nan</option>
                <option value="disaster">Thien tai</option>
            </select>
            <input type="hidden" name="longitude">
            <input type="hidden" name="latitude">
            <input type="submit" value="Submit">
        </form>
        <br>
        <br>
        <br>
        <br>
        <div>
            <button onclick="myfunction()">
                SOS
            </button>
        </div>

    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script>
        function myfunction(){
            navigator.geolocation.getCurrentPosition(showPosition);
            function showPosition(position) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    'url': '/help',
                    'method': 'POST',
                    'data':{
                        'latitude':position.coords.latitude,
                        'longitude':position.coords.longitude
                    },
                    success: function (response) {
                        alert("ok")
                    },
                    error: function () {
                        alert("k ok")
                    }
                });
            }


        }
        $("#open-form").click(function () {
            navigator.geolocation.getCurrentPosition(showPosition);
            function showPosition(position) {
                $("input[name='longitude']").val(position.coords.longitude);
                $("input[name='latitude']").val(position.coords.latitude);
                alert ($("input[name='longitude']").val());
            }
        });
    </script>
@endsection