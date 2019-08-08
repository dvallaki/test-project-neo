<!DOCTYPE html>
@php use Illuminate\Support\Facades\Input; @endphp
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Test Project</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

        <!-- Styles -->

        <link href="css/bootstrap.min.css" rel="stylesheet">
        <link href="css/bootstrap-datepicker.css" rel="stylesheet">
        
    </head>
    <body>
        @if($errors->any())
        <div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>
            {{$errors->first()}}
        </div>
        @endif
        <form method="post" style="margin: 0 auto;width: 50%;">
            @csrf
            Start Date: <input type="text" id="start_date" name="start_date" value="@php echo old('start_date')?old('start_date'):Input::get('start_date') @endphp">
            End Date: <input type="text" id="end_date" name="end_date" value="@php echo old('end_date')?old('end_date'):Input::get('end_date') @endphp">
            <input type="submit" name="submit" id="submit" value="Submit">
        </form>

        <canvas id="myChart" width="400" height="150"></canvas> <br/>

        @if(count($labels)>0)
        <div style="margin: 0 auto;width: 50%;">
            <b>Fastest asteroid ID:</b> {{ $fastestId }} <b>Speed (km/h):</b> {{ $maxSpeed }} <br/>
            <b>Closest asteroid ID:</b> {{ $closestId }} <b>Distance from Earth (km):</b> {{ $minDistance }} <br/>
            <b>Average size of asteroids (km):</b> {{ $averageSize }}<br/>
        </div>
        @endif

        <script src="js/jquery.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <script src="js/bootstrap-datepicker.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
        <script>
            $('#start_date').datepicker({format: 'dd-mm-yyyy', autoclose: true});
            $('#end_date').datepicker({format: 'dd-mm-yyyy', autoclose: true});

            var ctx = document.getElementById('myChart');
            var myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @php echo json_encode($labels) @endphp,
                    datasets: [{
                        label: '# of Asteroids',
                        data: @php echo json_encode($numOfAsteroids) @endphp,
                        borderWidth: 1,
                        fill:false,
                        borderColor:"rgb(75, 192, 192)",
                        lineTension:0.1
                    }]
                },
                options: {}
            });
        </script>
    </body>
</html>
