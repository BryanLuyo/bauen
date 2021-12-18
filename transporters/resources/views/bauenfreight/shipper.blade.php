@extends('../master_layout/web_shipper')

@section('title')
<title>Bauen | Customer admin | Conectando cargas con transportistas homologados - Bauen Freight SAC - Transporte de Carga, Fletes, Carga de Pago</title>
@endsection

@section('custom_js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0/dist/Chart.min.js" integrity="sha256-Uv9BNBucvCPipKQ2NS9wYpJmi8DTOEfTA/nH2aoJALw=" crossorigin="anonymous"></script>
<script>
  /* Helpers */
  const getTrailerName = trailerId => {
    const trailers = {!! json_encode($trailers) !!};

    for(i = 0; i < trailers.length; i++) {
      if(trailers[i].trailer_id == parseInt(trailerId)) {
        return trailers[i].name;
      }
    }
  }

  const getMonthName = monthNumber => {
    const monthNumbers = ["01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12"];
    // const monthNames = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Setiembre', 'Octubre', 'Noviembre', 'Diciembre'];
    const monthNames = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Set', 'Oct', 'Nov', 'Dic'];

    if(monthNumber < 10 && monthNumber.toString().length == 1) {
      monthNumber = "0" + monthNumber;
    }

    const monthNumberIndex = monthNumbers.findIndex(item => item == monthNumber);
    return monthNames[monthNumberIndex];
  }
  /**/

  const user_id = "{{$user_data['user_id']}}";
  const device_type = 1;
  const device_unique_code = "DASH";
  let date = 'lastMonths'
  
  let data = {
    user_id: user_id,
    device_type: 1,
    device_unique_code: device_unique_code,
    date: date
  };

  function sortByDate (data) {
    if(data.constructor !== Array || data.length === 0 || !('date' in data[0])) {
      return data
    }

    const sanitized = []
    data.forEach(item => {
      let sanitizedDate = item.date.split('-')
      item.sanitizedDate = `${sanitizedDate[2]}${sanitizedDate[1]}${sanitizedDate[0]}`
      sanitized.push(item)
    })
    
    return sanitized.sort((a, b) => (a.sanitizedDate > b.sanitizedDate) ? 1 : -1)
  }

  /* Service level */
  const serviceLevelAPI = "{{url('../api/completed_requests_db')}}";

  const getServiceLevel = data => {
    data.tipo_dashboard = 1;
    let form_data = new FormData();

    for ( let key in data ) {
        form_data.append(key, data[key]);
    }

    return new Promise((resolve, reject) => {
      $.ajax({
        type: 'POST',
        url: serviceLevelAPI,
        data: form_data,
        processData : false,
        contentType : false,
        beforeSend: function() {
          console.log('Retrieving service level...')
        }
      })
        .done(function(response) {
          const data = JSON.parse(response);
          console.log('getServiceLevel.response: ', data);
          const orderedData = sortByDate(data.requests);
          console.log('sortedGetServiceLevel.response: ', orderedData);
          resolve(orderedData);
        });
    })
  }

  const formatServiceLevel = (requests, dateType) => {
    let payload = {
      labels: [],
      data: []
    };

    requests.forEach(item => {
      let total = parseInt(item.count);
      let ontime_total = parseInt(item.ontime_count);
      let percentage = (ontime_total/total)*100;

      if(dateType == 'lastMonths') {
        let date = item.date.split("-");
        payload.labels.push(getMonthName(date[1]));

        payload.data.push(percentage.toFixed(2));

      } else if(dateType == 'thisMonth' || dateType == 'thisWeek') {
        payload.labels.push(item.date);
        payload.data.push(percentage.toFixed(2));

      } else { // yesterday or today
        payload.labels.push(item.hour);
        payload.data.push(percentage.toFixed(2));
      }
    });

    console.log('formatServiceLevel');
    console.log(payload);
    return payload;
  }

  const initServiceLevelChart = async (restart = null) => {
    const serviceLevel = await getServiceLevel(data);
    const formattedServiceLevel = formatServiceLevel(serviceLevel, data.date);

    if(restart) {
      $('#serviceLevelChart').remove();
      $('#serviceLevelChartContainer').append('<canvas id="serviceLevelChart" width="600" height="150"><canvas>');
    }

    var serviceLevelChartx = document.getElementById('serviceLevelChart').getContext('2d');
    /**/
    Chart.defaults.LineWithLine = Chart.defaults.line;
    Chart.controllers.LineWithLine = Chart.controllers.line.extend({
      draw: function(ease) {
        Chart.controllers.line.prototype.draw.call(this, ease);

        if (this.chart.tooltip._active && this.chart.tooltip._active.length) {
          var activePoint = this.chart.tooltip._active[0],
              x = activePoint.tooltipPosition().x,
              topY = activePoint.tooltipPosition().y,
              bottomY = this.chart.scales['y-axis-0'].bottom;

          serviceLevelChartx.save();
          serviceLevelChartx.beginPath();
          serviceLevelChartx.moveTo(x, topY);
          serviceLevelChartx.lineTo(x, bottomY);
          serviceLevelChartx.lineWidth = 1;
          serviceLevelChartx.strokeStyle = getChartColor('yellow').border;
          serviceLevelChartx.stroke();
          serviceLevelChartx.restore();
        }
      }
    });
    /**/
    var serviceLevelChart = new Chart(serviceLevelChartx, {
        type: 'LineWithLine',
        data: {
            labels: formattedServiceLevel.labels,
            datasets: [{
                label: 'Nivel de servicio (%)',
                data: formattedServiceLevel.data,
                backgroundColor: getChartColor('yellow').background,
                borderColor: getChartColor('yellow').border,
                borderWidth: 2,
                fill: false,
                pointRadius: 5
            }]
        },
        options: Object.assign(chartGeneralOptions, {
          animation: {
            onComplete: function(animation) {
              var firstSet = animation.chart.config.data.datasets.length > 0 ? animation.chart.config.data.datasets[0].data : [];

              if (firstSet.length == 0) {
                document.getElementById('no-data-service-level').style.display = 'flex';
                document.getElementById('serviceLevelChart').style.display = 'none'
              } else {
                if(document.getElementById('no-data-service-level').style.display == 'flex') {
                  document.getElementById('no-data-service-level').style.display = 'none';
                  document.getElementById('serviceLevelChart').style.display = null
                }
              }
            }
          }
        })
    });
  }


  /* Completed Requests */
  const completedRequestsAPI = "{{url('../api/completed_requests_db')}}";

  const getCompletedRequests = data => {
    data.tipo_dashboard = 2;
    let form_data = new FormData();

    for ( let key in data ) {
        form_data.append(key, data[key]);
    }

    return new Promise((resolve, reject) => {
      $.ajax({
        type: 'POST',
        url: completedRequestsAPI,
        data: form_data,
        processData : false,
        contentType : false,
        beforeSend: function() {
          console.log('Retrieving completed requests...')
        }
      })
        .done(function(response) {
          const data = JSON.parse(response);
          console.log('getCompletedRequests.response: ', data)
          const orderedData = sortByDate(data.requests);
          resolve(orderedData);
        });
    })
  }

  const formatCompletedRequests = (requests, dateType) => {
    let payload = {
      labels: [],
      data: []
    };

    requests.forEach(item => {

      if(dateType == 'lastMonths') {
        let date = item.date.split("-");
        payload.labels.push(getMonthName(date[1]));
        payload.data.push(Object.values(item)[0]);
      } else if(dateType == 'thisMonth' || dateType == 'thisWeek') {
        payload.labels.push(item.date);
        payload.data.push(Object.values(item)[0]);
      } else {
        payload.labels.push(item.hour);
        payload.data.push(1);
      }

    });

    console.log('formatCompletedRequests');
    console.log(payload);
    return payload;
  }

  const setTotalCompletedRequests = (requests, dateType) => {
    let total = 0;

    if(requests.length > 0) {
      if(dateType == 'today' || dateType == 'yesterday') {
        total = requests.length;
      } else {
        requests.forEach(item => {
          let thisTotal = Object.values(item)[0];
          total = total + parseInt(thisTotal);
        });
      }
    }

    totalCompletedRequests.innerText = total;
  }

  const initCompletedRequestsChart = async (restart = null) => {
    const completedRequests = await getCompletedRequests(data);
    const formattedCompletedRequests = formatCompletedRequests(completedRequests, data.date);

    // setTotalCompletedRequests(completedRequests, data.date);

    if(restart) {
      $('#numberServicesChart').remove();
      $('#numberServicesChartContainer').append('<canvas id="numberServicesChart" width="600" height="150"><canvas>');
    }

    var numberServicesChartx = document.getElementById('numberServicesChart').getContext('2d');
    /**/
    Chart.defaults.LineWithLine = Chart.defaults.line;
    Chart.controllers.LineWithLine = Chart.controllers.line.extend({
      draw: function(ease) {
        Chart.controllers.line.prototype.draw.call(this, ease);

        if (this.chart.tooltip._active && this.chart.tooltip._active.length) {
          var activePoint = this.chart.tooltip._active[0],
              x = activePoint.tooltipPosition().x,
              topY = activePoint.tooltipPosition().y,
              bottomY = this.chart.scales['y-axis-0'].bottom;

          numberServicesChartx.save();
          numberServicesChartx.beginPath();
          numberServicesChartx.moveTo(x, topY);
          numberServicesChartx.lineTo(x, bottomY);
          numberServicesChartx.lineWidth = 1;
          numberServicesChartx.strokeStyle = getChartColor('yellow').border;
          numberServicesChartx.stroke();
          numberServicesChartx.restore();
        }
      }
    });
    /**/
    var numberServicesChart = new Chart(numberServicesChartx, {
        type: 'LineWithLine',
        data: {
            labels: formattedCompletedRequests.labels,
            datasets: [{
                label: 'Nro. de servicios',
                data: formattedCompletedRequests.data,
                backgroundColor: getChartColor('yellow').background,
                borderColor: getChartColor('yellow').border,
                borderWidth: 2,
                fill: false,
                pointRadius: 5
            }]
        },
        options: Object.assign(chartGeneralOptions, {
          animation: {
            onComplete: function(animation) {
              var firstSet = animation.chart.config.data.datasets.length > 0 ? animation.chart.config.data.datasets[0].data : [];

              if (firstSet.length == 0) {
                document.getElementById('no-data').style.display = 'flex';
                document.getElementById('numberServicesChart').style.display = 'none'
              } else {
                if(document.getElementById('no-data').style.display == 'flex') {
                  document.getElementById('no-data').style.display = 'none';
                  document.getElementById('numberServicesChart').style.display = null
                }
              }
            }
          }
        })
    });
  }

  /* Vehicle Requests */
  const getvehicleRequests = data => {
    data.tipo_dashboard = 3;
    let form_data = new FormData();

    for ( let key in data ) {
        form_data.append(key, data[key]);
    }

    return new Promise((resolve, reject) => {
      $.ajax({
        type: 'POST',
        url: completedRequestsAPI,
        data: form_data,
        processData : false,
        contentType : false,
        beforeSend: function() {
          console.log('Retrieving completed requests by vehicle...')
        }
      })
        .done(function(response) {
          const data = JSON.parse(response);
          console.log('getvehicleRequests.response: ', data)
          const orderedData = sortByDate(data.requests);
          resolve(orderedData);
        });
    })
  }

  const formatvehicleRequests = (requests, dateType) => {
    let payload = {
      labels: [],
      datasets: []
    };

    const months = [];
    const trailers = [];
    
    if(dateType == 'lastMonths') {
      requests.forEach(item => {
        /* Obtener labels que son los meses */
        let month = item.date.split("-");
        let monthName = getMonthName(month[1]);
        
        if(!months.some(e => e.name === monthName)) {
          months.push({number: month[1], name: monthName});
        }

        // Sort computed months
        const monthNames = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Setiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        months.sort((a,b) => { return monthNames.indexOf(a.name) - monthNames.indexOf(b.name)});

        /* Obtener TODOS los trailers */
        let trailerName = getTrailerName(item.trailer_id);
        if(!trailers.some(e => e.name === trailerName)) {
          trailers.push({id: item.trailer_id, name: trailerName});
        }
      });

      /* Build payload labels */
      months.forEach(month => payload.labels.push(month.name));

      /* Build payload dataset */
      trailers.forEach(trailer => {
        const chartColor = getChartColor();

        let currentData = {
          label: trailer.name,
          data: [],
          backgroundColor: chartColor.background,
          borderColor: chartColor.border,
          borderWidth: 1
        };

        months.forEach(month => {
          let request = requests.find(item => {
            let thisMonth = item.date.split("-")[1];
            if(thisMonth == month.number && item.trailer_id == trailer.id) {
              return item;
            }
          })

          const counter = (request) ? parseInt(request.count) : 0;
          currentData.data.push(counter);
        });

        payload.datasets.push(currentData);
      });
    } else {
      const labels = {
        'today': 'Hoy',
        'yesterday': 'Ayer',
        'thisWeek': 'Esta semana',
        'thisMonth': 'Este mes'
      }

      payload.datasets.push({
        label: labels[dateType],
        data: [],
        backgroundColor: [],
        borderColor: [],
        borderWidth: 1
      });

      requests.forEach(item => {
        const trailerName = getTrailerName(item.trailer_id);
        const chartColor = getChartColor();

        payload.labels.push(trailerName);
        payload.datasets[0].data.push(parseInt(item.count));
        payload.datasets[0].backgroundColor.push(chartColor.background);
        payload.datasets[0].borderColor.push(chartColor.border);
      })
    }
    
    console.log('formatvehicleRequests');
    console.log(payload);
    return payload;
  }

  const setTotalvehicleRequests = requests => {
    let total = 0;

    if(requests.length > 0) {
      requests.forEach(item => {
        let thisTotal = Object.values(item)[0];
        total = total + parseInt(thisTotal);
      });
    }

    totalVehicleRequests.innerText = total;
  }

  const initVehicleTypeChart = async (restart = null) => {
    const vehicleRequests = await getvehicleRequests(data);
    const formattedvehicleRequests = formatvehicleRequests(vehicleRequests, data.date);

    // setTotalvehicleRequests(vehicleRequests);

    if(restart) {
      $('#vehicleTypeChart').remove();
      $('#vehicleTypeChartContainer').append('<canvas id="vehicleTypeChart" width="600" height="150"><canvas>');
    }

    var vehicleTypeChartx = document.getElementById('vehicleTypeChart').getContext('2d');
    new Chart(vehicleTypeChartx, {
      data: formattedvehicleRequests,
      type: 'bar',
      options: Object.assign(chartGeneralOptions, {
          animation: {
            onComplete: function(animation) {
              var firstSet = animation.chart.config.data.datasets.length > 0 ? animation.chart.config.data.datasets[0].data : [];

              if (firstSet.length == 0) {
                document.getElementById('no-data-vehicle-type').style.display = 'flex';
                document.getElementById('vehicleTypeChart').style.display = 'none'
              } else {
                if(document.getElementById('no-data-vehicle-type').style.display == 'flex') {
                  document.getElementById('no-data-vehicle-type').style.display = 'none';
                  document.getElementById('vehicleTypeChart').style.display = null
                }
              }
            }
          }
        })
    });
  }

  /* Acceptation Ratio */
  const getAcceptationRatio = data => {
    data.tipo_dashboard = 4;
    let form_data = new FormData();

    for ( let key in data ) {
        form_data.append(key, data[key]);
    }

    return new Promise((resolve, reject) => {
      $.ajax({
        type: 'POST',
        url: completedRequestsAPI,
        data: form_data,
        processData : false,
        contentType : false,
        beforeSend: function() {
          console.log('Retrieving acceptation ratio data...')
        }
      })
        .done(function(response) {
          const data = JSON.parse(response);
          console.log('getAcceptationRatio.response: ', data)
          const orderedData = sortByDate(data.requests);
          console.log('getAcceptationRatio.orderedData: ', orderedData)
          resolve(orderedData);
        });
    })
  }

  const formatAcceptationRatio = (requests, dateType) => {
    let payload = {
      labels: [],
      datasets: [{
        label: 'Cotizados',
        data: [],
        backgroundColor: getChartColor('light-gray').background,
        borderColor: getChartColor('light-gray').border,
        borderWidth: 1
      },
      {
        label: 'Completados',
        data: [],
        backgroundColor: getChartColor('yellow').background,
        borderColor: getChartColor('yellow').border,
        borderWidth: 1,
      }]
    };

    const results = [];
    const addedMonths = [];

    if(requests.length > 0) {
      requests.forEach(item => {
        let currentMonth;

        if(dateType == 'today') {
          currentMonth = "<?php echo date('d-m-Y'); ?>";
        } else if(dateType == 'yesterday') {
          currentMonth = "<?php echo date('d.m.Y',strtotime('-1 days')); ?>";
        } else {
          currentMonth = item.date.split("-")[1];
        }
        // Verify if current item date already has a object in results array
        if(!addedMonths.includes(currentMonth)) {
          addedMonths.push(currentMonth)

          let splittedDate = ('date' in item) ? item.date.split("-") : null

          let firstData = {
            monthNumber: currentMonth,
            year: splittedDate ? splittedDate[2] : null,
            sortableDate: splittedDate ? `${splittedDate[2]}${splittedDate[1]}${splittedDate[0]}` : null,
            cotizados: 0,
            ganados: 0
          }
          results.push(firstData)
        }

        // Find object in result array by current item month and add the counter
        const resultItem = results.find(result => result.monthNumber == currentMonth)
        resultItem.cotizados = resultItem.cotizados + parseInt(item.count)

        if(item.request_status == 13) {
          resultItem.ganados = resultItem.ganados + parseInt(item.count)
        }
      })
    }

    results.sort((a, b) => {
      if(a.sortableDate) {
        return a.sortableDate > b.sortableDate ? 1 : -1
      }

      return a.monthNumber > b.monthNumber ? 1 : -1
    })

    results.forEach(result => {
      payload.labels.push(result.year ? getMonthName(result.monthNumber)  : getMonthName(result.monthNumber));
      payload.datasets[0].data.push(result.cotizados);
      payload.datasets[1].data.push(result.ganados);
    })

    console.log('formatAcceptationRatio');
    console.log(payload);
    return payload;
  }

  const setTotalAcceptationRatio = requests => {
    let total = 0;
    let cotizados = 0;
    let ganados = 0;

    if(requests.length > 0) {
      requests.forEach(item => {
        if(item.request_status == 13) {
          ganados = ganados + parseInt(item.count)
        } else {
          cotizados = cotizados + parseInt(item.count)
        }
      });
    }

    cotizados = cotizados + ganados;

    if(cotizados == 0) {
      totalAcceptationRatio.innerText = 0;
    } else {
      total = Math.floor(ganados/cotizados*100);
      totalAcceptationRatio.innerText = `${total}%`;
    }
  }

  const initAcceptationRatioChart = async (restart = null) => {
    const acceptationRatio = await getAcceptationRatio(data);
    const formattedAcceptationRatio = formatAcceptationRatio(acceptationRatio, data.date);

    // setTotalAcceptationRatio(acceptationRatio);

    if(restart) {
      $('#acceptationRatioChart').remove();
      $('#acceptationRatioChartContainer').append('<canvas id="acceptationRatioChart" width="600" height="150"><canvas>');
    }

    var acceptationRatioChartx = document.getElementById('acceptationRatioChart').getContext('2d');
    new Chart(acceptationRatioChartx, {
      type: 'bar',
      data: formattedAcceptationRatio,
      options: Object.assign(chartGeneralOptions, {
          animation: {
            onComplete: function(animation) {
              var firstSet = animation.chart.config.data.datasets.length > 0 ? animation.chart.config.data.datasets[0].data : [];

              if (firstSet.length == 0) {
                document.getElementById('no-data-acceptation-ratio').style.display = 'flex';
                document.getElementById('acceptationRatioChart').style.display = 'none'
              } else {
                if(document.getElementById('no-data-acceptation-ratio').style.display == 'flex') {
                  document.getElementById('no-data-acceptation-ratio').style.display = 'none';
                  document.getElementById('acceptationRatioChart').style.display = null
                }
              }
            }
          }
        })
    });
  }

  initServiceLevelChart();
  initCompletedRequestsChart();
  initVehicleTypeChart();
  initAcceptationRatioChart();

  const chartColors = [
    {
      color: 'red',
      background: 'rgba(189, 61, 82, .7)',
      border: 'rgba(189, 61, 82, .9)'
    },
    {
      color: 'yellow',
      background: 'rgba(254, 196, 14, .7)',
      border: 'rgba(254, 196, 14, .9)'
    },
    {
      color: 'gray',
      background: 'rgba(46, 45, 43, .7)',
      border: 'rgba(46, 45, 43, .9)'
    },
    {
      color: 'light-gray',
      background: 'rgba(225, 225, 225, .7)',
      border: 'rgba(225, 225, 225, .9)'
    }
  ];

  const chartGeneralOptions = {
    scales: {
      yAxes: [{
        ticks: {
            beginAtZero: true,
            fixedStepSize: 1,
            precision: 0,
            userCallback: function(label, index, labels) {
                // when the floored value is the same as the value we have a whole number
                if (Math.floor(label) === label) {
                    return label;
                }

            },
        },
        gridLines : {
            display: true,
            drawBorder: false,
        }
      }],
      xAxes : [{
        maxBarThickness: 30,
        gridLines : {
            display : false,
            drawBorder: false
        }
      }]
    },
    tooltips: {
      intersect: false
    },
    responsive: true, 
    maintainAspectRatio: false
  };
 
  const getChartColor = (color = null) => {
    if(color) {
      return chartColors.find(item => item.color == color)
    }

    const rndm = Math.floor(Math.random() * chartColors.length);
    return chartColors[rndm];
  }

  // Sin terminar
  // Chart.defaults.global.legend.display = false;
  Chart.defaults.global.legend.position = 'bottom';

  /* Filter */
  $('.badge').click(function() {
    if(!$(this).hasClass('active')) {
      $('.badge.active').removeClass('active');
      $(this).addClass('active');

      data.date = $(this).data('value');

      initServiceLevelChart(true);
      initCompletedRequestsChart(true);
      initVehicleTypeChart(true);
      initAcceptationRatioChart(true);
    }
  })
</script>
@endsection

@section('custom_css')
<style type="text/css">
  .no-data-chart {
    display:none;
    width:100%;
    height:150px;
    align-items:center;
    justify-content:center;
  }

  .text-secondary {
    color: #fec40e !important;
  }

  .bauen-chart {
    max-width: 600px;
    max-height: 150px;
  }

  @media screen and (min-width: 992px) {
    .bauen-chart {
      height: 150px;
    }
  }

  .card-body {
    padding: 1rem;
    box-shadow: 0 5px 10px 3px rgba(0, 0, 0, 0.1)
  }

  .badge {
    background-color: #f5f5f5;
    color: #3b3b3b;
    cursor: pointer;
  }
  .badge:hover {
    background-color: #e1e1e1;
  }
  .badge.active {
    background-color: #fec40e;
    color: #fff;
  }
  .badge.active:hover {
    background-color: #f7bc03;
  }
</style>
@endsection


@section('title')
<title>Bauenfreight</title>
@endsection



@section('banner')
@endsection

@section('main_container')
<div class="right-body">
   <div class="shipper-home wow fadeInUp">
      <div class="tab-content">
        <div id="list" class="tab-pane fade in active">
        <div class="transit-request request-quote" >
            @if(Session::has('message'))
            <div class="col-xs-12">
              <div class="alert alert-info">
                  <a class="close" data-dismiss="alert">×</a>
                 {{Session::get('message')}}
                  {{Session::forget('message')}}
              </div>
            </div>
            @endif
            
            <div class="col-xs-12">
              <?php
              $request_list_count=0;
              $transit_requests_count=0;
              $completed_requests_count=0;
              $expired_requests_count=0;

              $serviceLevelChartEnable = true;
              $completedRequestsChartEnable = true;
              $vehicleRequestsChartEnable = true;
              $acceptationRatioChartEnable = true;

              //0,1 = requests_list; notIn 0,10,4,14 = transit_requests; 13 = completed; 4,14 = expired_requests
              ?>
              @if(!empty($count_requests))
                @foreach($count_requests as $requests)
                  @if(in_array($requests->request_status, array(0, 1)) && $requests->is_deleted != 1)
                    <?php $request_list_count += $requests->total_row; ?>
                  @elseif(in_array($requests->request_status, array(2,3,5,6,7,8,9,10,11,12)) && $requests->is_deleted != 1)
                    <?php $transit_requests_count += $requests->total_row; ?>
                  @elseif($requests->request_status == 13 && $requests->is_deleted != 1)
                    <?php $completed_requests_count += $requests->total_row; ?>
                  @elseif(in_array($requests->request_status, array(4,14)))
                    <?php $expired_requests_count += $requests->total_row; ?>
                  @endif
                @endforeach
              @endif

                  <div class="container-fluid">
                    <div class="row">
                      <div class="col-xs-12">
                        <div class="container-fluid">
                          <div class="row p-0">
                            <div class="col-lg-3 col-xs-12 pr-xl-1">
                              <a href="{!!  url('/request-list') !!}" class="box-stats mb-3 pr-lg-5">
                                <span @if($request_list_count != 0) class="text-secondary" @endif>{{$request_list_count}}</span>Cotizaciones<br>en curso
                              </a>
                            </div>
                            <div class="col-lg-3 col-xs-12 px-xl-1">
                              <a href="{!!  url('/transit-requests') !!}" class="box-stats mb-3 pr-lg-5">
                                <span @if($transit_requests_count != 0) class="text-secondary" @endif>{{$transit_requests_count}}</span>Ordenes<br>en tránsito
                              </a>
                            </div>
                            <div class="col-lg-3 col-xs-12 px-xl-1">
                              <a href="{!!  url('/completed-requests') !!}" class="box-stats mb-3 pr-lg-5">
                                <span @if($completed_requests_count != 0) class="text-secondary" @endif>{{$completed_requests_count}}</span>Ordenes<br>completas
                              </a>
                            </div>
                            <div class="col-lg-3 col-xs-12 pl-xl-1">
                              <a href="{!!  url('/done-requests') !!}" class="box-stats mb-3 pr-lg-5">
                                <span @if($expired_requests_count != 0) class="text-secondary" @endif>{{$expired_requests_count}}</span>Ordenes<br>vencidas
                              </a>
                            </div>
                          </div>

                              <div class="row mb-3">
                                <div class="col-xs-12">
                                  <span class="badge" data-value="today">Hoy</span>
                                  <span class="badge" data-value="yesterday">Ayer</span>
                                  <span class="badge" data-value="thisWeek">Esta semana</span>
                                  <span class="badge" data-value="thisMonth">Este mes</span>
                                  <span class="badge active" data-value="lastMonths">Últimos meses</span>
                                </div>
                              </div>
                              <div class="row">
                                  @if($serviceLevelChartEnable)
                                  <div class="col-md-6 mb-4">
                                    <div class="card shadow">
                                      <div class="card-body text-center">
                                        <strong style="font-size:28px">NIVEL DE ATENCIÓN</strong><br>
                                        <span>Porcentaje de recojos a tiempo</span>
                                        {{-- <strong style="font-size:42px">58%</strong><br>
                                        <span class="lead">Nivel de Atención</span> --}}
                                        <div class="bauen-chart mt-3">
                                          <div id="serviceLevelChartContainer">
                                            <canvas id="serviceLevelChart" width="600" height="150"></canvas>
                                          </div>
                                          <div id="no-data-service-level" class="no-data-chart"><strong>No se encontró suficiente información.</strong></div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                  @endif
                                  
                                  @if($completedRequestsChartEnable)
                                  <div class="col-md-6 mb-4">
                                    <div class="card shadow">
                                      <div class="card-body text-center">
                                        <strong style="font-size:28px" id="totalCompletedRequests">SERVICIOS REALIZADOS</strong><br>
                                        <span>Número de servicios totales en el tiempo</span>
                                        {{-- <strong style="font-size:42px" id="totalCompletedRequests"></strong><br>
                                        <span class="lead">Número de Servicios</span> --}}
                                        <div class="bauen-chart mt-3">
                                          <div id="numberServicesChartContainer">
                                              <canvas id="numberServicesChart" width="600" height="150"></canvas>
                                          </div>
                                          <div id="no-data" class="no-data-chart"><strong>No se encontró suficiente información.</strong></div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                  @endif

                                  @if($vehicleRequestsChartEnable)
                                  <div class="col-md-6 mb-4">
                                    <div class="card shadow">
                                      <div class="card-body text-center">
                                        <strong style="font-size:28px" id="totalAcceptationRatio">TIPOS DE TRAILERS UTILIZADOS</strong><br>
                                        <span>Distribución por tipo de trailer</span>
                                        {{-- <strong style="font-size:42px" id="totalVehicleRequests"></strong><br>
                                        <span class="lead">Envíos por Tipo de Vehículo</span> --}}
                                        <div class="bauen-chart mt-3">
                                          <div id="vehicleTypeChartContainer">
                                            <canvas id="vehicleTypeChart" width="600" height="150"></canvas>
                                          </div>
                                          <div id="no-data-vehicle-type" class="no-data-chart"><strong>No se encontró suficiente información.</strong></div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                  @endif

                                  @if($acceptationRatioChartEnable)
                                  <div class="col-md-6 mb-4">
                                    <div class="card shadow">
                                      <div class="card-body text-center">
                                        <strong style="font-size:28px" id="totalVehicleRequests">RATIO DE ACEPTACIÓN</strong><br>
                                        <span>Cargas cotizadas vs. cargas realizadas</span>
                                        {{-- <strong style="font-size:42px" id="totalAcceptationRatio"></strong><br>
                                        <span class="lead">Ratio de Aceptación de Pedidos</span> --}}
                                        <div class="bauen-chart mt-3">
                                          <div id="acceptationRatioChartContainer">
                                            <canvas id="acceptationRatioChart" width="600" height="150"></canvas>
                                          </div>
                                          <div id="no-data-acceptation-ratio" class="no-data-chart"><strong>No se encontró suficiente información.</strong></div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                  @endif
                              </div>
                          </div>
                      </div>
                    </div>
                  </div>
                    
                    
                    
                    
              </div>
          </div>
        </div>
      </div>
    </div>
  
	  </div>
         
         </div>


@endsection