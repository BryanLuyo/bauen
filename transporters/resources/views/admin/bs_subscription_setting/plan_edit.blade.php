@extends('../../master_layout/admin_master')

@section('title')
<title>Buy & Sell App</title>
@endsection

@section('content_header')
 <a href="{{url('admin-plan-list')}} "><i class="icon-home"></i> Subscription Plan</a>
@endsection

@section('custom_js')
<script src="{{asset('public/assets/Myfile/subscription_js.js')}}"></script>
@endsection

@section('content')




 <div class="widget-box">
     @if(count($errors) > 0)
     <h1>ffffjjjjjjjjjjjjfffff</h1>
        <div class="alert alert-danger">
            <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
            </ul>
        </div>
        @endif
        
            @if(session('message'))
            <h1>fffffffff</h1>
                <div class="alert alert-{{session('message.type')}}">
                {{session('message.text')}}
                </div>
            @endif
            
            
            
            
        <div class="widget-title"> <span class="icon"> <i class="icon-info-sign"></i> </span>
            <h5>Subscription Edit</h5>
        </div>
     
      
        <div class="widget-content nopadding">
         @if(!empty($plan))
            <form class="form-horizontal" id="admin_plan_edit" name="admin_plan_edit" action="admin-plan-edit-post" method="post" enctype="multipart/form-data">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="plan_id" id="plan_id" value="{{$plan->id}}">
                <div class="control-group">
                    <label class="control-label">Plan Name </label>
                    <div class="controls">
                        <input type="text" name="plan_name" id="plan_name" value="{{$plan->plan_name}}" />
                    </div>
                </div>
               <div class="control-group">
                    <label class="control-label">Plan Description </label>
                    <div class="controls">
                        <input type="text" name="plan_description" id="plan_description" value="{{$plan->plan_description}}" />
                    </div>
                </div>
                
                <div class="control-group">
                    <label class="control-label">Plan Validity(Days) </label>
                    <div class="controls">
                        <input type="number" name="plan_validity" id="plan_validity" value="{{$plan->plan_validity}}" />
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">Plan price </label>
                    <div class="controls">
                        <input type="text" name="amount" id="amount" value="{{$plan->amount}}" />
                    </div>
                </div>
                <div class="control-group">
                    
                    <div class="controls">
                        <input type="submit" class="btn btn-default" name="plan_edit_submit" id="plan_edit_submit" value="Update"/>
                    </div>
                </div>
                
            </form>   
              
         @endif



        </div>
    </div>


@endsection
