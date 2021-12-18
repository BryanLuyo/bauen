@extends('../master_layout/admin_master')

@section('title')
<title>Bauen | Conectando cargas con transportistas homologados | Transporte de Carga Pesada </title>
@endsection

@section('content_header')
 <a href="{{url('admin-dashboard')}} "><i class="icon-home"></i> Home</a>
 <style>
     /* Style the tab */
div.tab {
    overflow: hidden;
    border: 1px solid #ccc;
    background-color: #f1f1f1;
}

/* Style the buttons inside the tab */
div.tab button {
    background-color: inherit;
    float: left;
    border: none;
    outline: none;
    cursor: pointer;
    padding: 14px 16px;
    transition: 0.3s;
}

/* Change background color of buttons on hover */
div.tab button:hover {
    background-color: #ddd;
}

/* Create an active/current tablink class */
div.tab button.active {
    background-color: #ccc;
}

/* Style the tab content */
.tabcontent {
    display: none;
    padding: 6px 12px;
    border: 1px solid #ccc;
    border-top: none;
}
 </style>
@endsection

@section('custom_js')
<script src="{{asset('public/assets/Myfile/item_js.js')}}"></script>
<script>    
    function openCity(evt, cityName) {
    // Declare all variables
    var i, tabcontent, tablinks;

    // Get all elements with class="tabcontent" and hide them
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }

    // Get all elements with class="tablinks" and remove the class "active"
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }

    // Show the current tab, and add an "active" class to the button that opened the tab
    document.getElementById(cityName).style.display = "block";
    evt.currentTarget.className += " active";
}
</script>
@endsection

@section('content')
<div class="tab">
  <button class="tablinks active" onclick="openCity(event, 'All')">All</button>
  <button class="tablinks" onclick="openCity(event, 'Today')">Today</button>
  <button class="tablinks" onclick="openCity(event, 'Month')">Month</button>
   <button class="tablinks" onclick="openCity(event, 'Year')">Year</button>
</div>

<div id="All" class="tabcontent active" style="display:block;height:150px">
  
   <div clas="span12" style="display:block; border: 0 1px 1px 1px solid #000;">  
        <div class="quick-actions_homepage">
            <ul class="quick-actions">
   <li class="bg_ly "><a href="javascript:void(0)" ><i class="icon-user"></i> <strong>{{$dashboard['all']['reg_user']}}</strong> <br><small>Total Registered Users</small></a></li>
    <li class="bg_lh "><a href="javascript:void(0)" ><i class="icon-building"></i> <strong>{{$dashboard['all']['add_posted']}}</strong><br> <small>Total Ads Posted</small></a></li>
    <li class="bg_lg "><a href="javascript:void(0)" ><i class="icon-money"></i> <strong>{{$dashboard['all']['sub_user']}}</strong><br> <small>Total Subscribed Users</small></a></li>
          </ul>
        </div>
    </div>
</div>

<div id="Today" class="tabcontent"  style="height:150px"> 
  
   <div clas="span12">  
        <div class="quick-actions_homepage">
            <ul class="quick-actions">
   <li class="bg_ly "><a href="javascript:void(0)" ><i class="icon-user"></i> <strong>{{$dashboard['today']['reg_user']}}</strong> <br><small>Total Registered Users</small></a></li>
    <li class="bg_lh "><a href="javascript:void(0)" ><i class="icon-building"></i> <strong>{{$dashboard['today']['add_posted']}}</strong><br> <small>Total Ads Posted</small></a></li>
    <li class="bg_lg "><a href="javascript:void(0)" ><i class="icon-money"></i> <strong>{{$dashboard['today']['sub_user']}}</strong><br> <small>Total Subscribed Users</small></a></li>
          </ul>
        </div>
    </div> 
</div>

<div id="Month" class="tabcontent" style="height:150px">
  
  <div clas="span12">  
        <div class="quick-actions_homepage">
            <ul class="quick-actions">
   <li class="bg_ly "><a href="javascript:void(0)" ><i class="icon-user"></i> <strong>{{$dashboard['month']['reg_user']}}</strong> <br><small>Total Registered Users</small></a></li>
    <li class="bg_lh "><a href="javascript:void(0)" ><i class="icon-building"></i> <strong>{{$dashboard['month']['add_posted']}}</strong><br> <small>Total Ads Posted</small></a></li>
    <li class="bg_lg "><a href="javascript:void(0)" ><i class="icon-money"></i> <strong>{{$dashboard['month']['sub_user']}}</strong><br> <small>Total Subscribed Users</small></a></li>
          </ul>
        </div>
    </div>
</div>
<div id="Year" class="tabcontent" style="height:150px">
  
  <div clas="span12">  
        <div class="quick-actions_homepage">
            <ul class="quick-actions">
   <li class="bg_ly "><a href="javascript:void(0)" ><i class="icon-user"></i> <strong>{{$dashboard['year']['reg_user']}}</strong> <br><small>Total Registered Users</small></a></li>
    <li class="bg_lh "><a href="javascript:void(0)" ><i class="icon-building"></i> <strong>{{$dashboard['year']['add_posted']}}</strong><br> <small>Total Ads Posted</small></a></li>
    <li class="bg_lg "><a href="javascript:void(0)" ><i class="icon-money"></i> <strong>{{$dashboard['year']['sub_user']}}</strong><br> <small>Total Subscribed Users</small></a></li>
          </ul>
        </div>
    </div>
</div>
<!--    <div clas="span12">  
        <div class="quick-actions_homepage">
            <ul class="quick-actions">
                <li class="bg_ly "><a href="{{URL::to('admin-user-view')}}"><i class="icon-user"></i> <strong><?php // {{//$user_count}} ?></strong> <br><small>New Users</small></a></li>
                <li class="bg_lh "><a href="{{URL::to('admin-company-view')}}"><i class="icon-building"></i> <strong><?php //{{//$company_count}} ?></strong><br> <small>New Company</small></a></li>
                <li class="bg_lg "><a href="{{URL::to('admin-order-view')}}"><i class="icon-money"></i> <strong><?php //{{//$money_count}} ?></strong><br> <small>Total Business</small></a></li>
                <li class="bg_lv "><a href="{{URL::to('admin-order-view')}}"><i class="icon-shopping-cart"></i> <strong><?php //{{//$order_count}} ?></strong><br> <small>New Orders</small></a></li>
                <li class="bg_lo "><a href="{{URL::to('admin-company-view')}}"><i class="icon-star"></i> <strong><?php //{{//$order_company_count}} ?></strong><br> <small>Trending Company</small></a></li>
                <li class="bg_lr "><a href="{{URL::to('admin-return-view')}}"><i class="icon-remove"></i> <strong><?php //{{//$return_count}} ?></strong><br> <small>New Returns</small></a></li>
           
            </ul>
        </div>
    </div>-->

@endsection