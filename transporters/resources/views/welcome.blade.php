<!DOCTYPE html>
<html>
    <head>
        <title>Laravel</title>

        <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">
            <select class="form-control input-lg" id="languageSwitcher">

                            <option value="">--Lang--</option>
                            <option value= "en">English</option>
                            <option value="de">Other</option>
            </select>
    <input type="hidden" name="_Token" id="_token" value="{{ csrf_token() }}">
        <style>
            html, body {
                height: 100%;
            }

            body {
                margin: 0;
                padding: 0;
                width: 100%;
                display: table;
                font-weight: 100;
                font-family: 'Lato';
            }

            .container {
                text-align: center;
                display: table-cell;
                vertical-align: middle;
            }

            .content {
                text-align: center;
                display: inline-block;
            }

            .title {
                font-size: 96px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="content">
                <div class="title">Laravel 5</div>
                <p>{{trans('app.Home') }}</p>
                @if(Session::has('locale'))
                <p>  {{Session::get('locale')}}</p>
                @endif
                <p>{{Config::get('app.locale')}}</p>
            </div>
        </div>
         <script src="{{asset('public/assets/jquery.min.js')}}"></script>
          <script src="{{asset('public/assets/custom_js.js')}}"></script>
    </body>
</html>
