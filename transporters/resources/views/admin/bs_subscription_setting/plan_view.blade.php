@extends('../../master_layout/admin_master')

@section('title')
<title>Buy & Sell App</title>
@endsection

@section('content_header')
 <a href="{{url('admin-plan-list')}} "><i class="icon-home"></i>Subscription Plan</a>
@endsection

@section('custom_js')
<script src="{{asset('public/assets/Myfile/subscription_js.js')}}"></script>
@endsection

@section('content')

<div class="span12">
    @if(session('message'))
    <div class="alert alert-danger {{session('message.type')}}">
    {{session('message.text')}}
    </div>
    @endif

    <div class="widget-box">
        <div class="widget-title"> <span class="icon"> <i class="icon-th"></i> </span>
            <h5 class="pull-left">Ad Filter</h5>
        </div>
        <div class="widget-content nopadding">

            <form class="form-horizontal" method="GET" action="" >
                
                 <div class="control-group">
                    <label class="control-label"><b>Subscription Name:</b></label>
                    <div class="controls " >  
                       <input type="text" name="search_key" value="@if(!empty($search['search_key'])){{$search['search_key']}}@endif">
                    </div>
                </div>
                <div class="form-actions">
                    <input type="submit" value="Filter" class="btn btn-success">
                    <a href="{{URL::to('admin-plan-list')}}" class="btn btn-default">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="widget-box">
        <div class="widget-title"> <span class="icon"> <i class="icon-th"></i> </span>
            <h5 class="pull-left">Category list</h5>
            <a href="{{ URL::to('admin-plan-add')}}" class="btn btn-mini btn-primary pull-right button_class">Add New</a>
        </div>
       
        @if(!empty($plan_list))
         <?php $sl = 0 ; ?>
        <div class="widget-content nopadding">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>SL.No</th>
                        <th>Plan Name</th>
                        <th>Plan description</th>
                        <th>Plan validity</th>
                        <th>Plan amount</th>
                        <th>Status</th>
                        <th>Action</th>

                    </tr>
                </thead>
                <tbody>

                    @foreach ($plan_list as $plan) 
                     <?php $sl = $sl+1 ; ?>
                    <tr>
                        <td class="center">{{ $sl}} </td>
                        
                        <td class="center"> {{ ucwords($plan->plan_name)}}</td>
                        <td class="center"> {{ ucwords($plan->plan_description)}}</td>
                        <td class="center"> {{ $plan->plan_validity}} Days </td>
                        <td class="center"> {{ $plan->amount}}</td>
                        <td class="center"> @if ($plan->status==1) {{"Active"}} @else {{"Block"}}  @endif 
                        
                            
                        </td> 
                       
                        

                        <td class=" ">
                            <input type="hidden" id="plan_details_{{$plan->id}}" value="{{json_encode($plan)}}">
                            
                             @if ($plan->status==1)  
                             <a class="btn btn-mini mergin_one" onclick="chenge_plan_status('{{base64_encode(env('APP_KEY').'||'.$plan->id)}}')" >
                                <i class="icon-ban-circle"></i> {{"block"}} 
                            </a>
                             @else 
                             <a class="btn btn-mini mergin_one" onclick="chenge_plan_status('{{base64_encode(env('APP_KEY').'||'.$plan->id)}}')" >
                                <i class="icon-ban-circle"></i> {{"Active"}}      
                                </a>
                             @endif    
                            

                            &nbsp;&nbsp;
                            <a href="{{ URL::to('admin-plan-edit-'.base64_encode($plan->id.'||'.env('APP_KEY')))}}" class="btn btn-mini mergin_one" >
                                <i class="icon-edit"></i> Edit</a>
                            &nbsp;&nbsp;
                            <a href="{{ URL::to('admin-plan-delete-'.base64_encode($plan->id.'||'.env('APP_KEY'))) }}" class="btn btn-mini" style="margin:1px">
                                <i class="icon-trash"></i> Delete</a>
                            &nbsp;&nbsp;
                           

                        </td>

                    </tr>
                    @endforeach

                </tbody>
            </table>

        </div>
        <div class="pagination alternate pull-right">
            {!! $plan_list->appends(['search_key'=>Request::get('search_key')])->links() !!}
        </div>
        @endif
    </div>
</div>


@endsection
