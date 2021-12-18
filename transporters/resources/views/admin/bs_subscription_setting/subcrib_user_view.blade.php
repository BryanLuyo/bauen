@extends('../../master_layout/admin_master')

@section('title')
<title>Buy & Sell App</title>
@endsection

@section('custom_js')
<script src="{{asset('public/assets/Myfile/subscription_js.js')}}"></script>
@endsection

@section('content_header')
 <a href="{{url('admin-subscribe-user-list')}} "><i class="icon-home"></i>Subscribed User</a>
@endsection

@section('content')

<div class="span12">
    <?php if (Session::has('flash_errmessage')) { ?>
        <div class="alert alert-danger alert-dismissible margin-t-10" style="margin-bottom:15px;">
            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
            <p><i class="icon fa fa-warning"></i><strong>Sorry!</strong> {{Session::get('flash_errmessage')}}</p>
        </div>
    <?php } ?><!-- / Error Message -->
    <?php if (Session::has('flash_message')) { ?>
        <div class="alert alert-success alert-dismissible margin-t-10" style="margin-bottom:15px;">
            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
            <p><i class="icon fa fa-check"></i><strong>Success!</strong> {{Session::get('flash_message')}}</p>
        </div>
    <?php } ?>

    <div class="widget-box">
        <div class="widget-title"> <span class="icon"> <i class="icon-th"></i> </span>
            <h5 class="pull-left">Subscribed User Search</h5>
        </div>
        <div class="widget-content nopadding">

            <form class="form-horizontal" method="GET" action="" >
				<div class="control-group">
                    <label class="control-label"><b>Search by subscription plan:</b></label>
                    <div class="controls " >  
                       
                        <select name="plan_name" id="plan_name">
                            <option value="0">Select subscription plan</option>
                            @if(!empty($plan_list))
                            @foreach($plan_list as $plan)
                             <option value="{{$plan->id}}">{{$plan->plan_name}}</option>
                            @endforeach
                            @endif
                        </select>   
                    </div>
                </div>

            
                <div class="control-group">
                    <label class="control-label"><b> Search by subscription expiry date:</b></label>
                    <div class="controls " >  
                        <input type="date" name="subscription_end_date" value="@if(!empty($search['subscription_end_date'])){{$search['subscription_end_date']}}@endif">
                    </div>
                </div>
                <div class="form-actions">
                    <input type="submit" value="Filter" class="btn btn-success">
                    <a href="{{URL::to('admin-subscribe-user-list')}}" class="btn btn-default">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="widget-box">
        <div class="widget-title"> <span class="icon"> <i class="icon-th"></i> </span>
            <h5 class="pull-left">Subscribed user list</h5>

        </div>
        @if(!empty($user_list))
        <?php $sl = 0 ; ?>
        <div class="widget-content nopadding">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>SL.No</th>
                        <th>Plan Name</th>
                        <th>Price</th>
                        <th>Validity start date</th>
                        <th>Validity end date</th>
                        <th>User Name</th>
                        <th>User Image</th>
                        <th>User Email</th>
                        <th>Phon No</th>
                        <th>Status</th>
                       
                       

                    </tr>
                </thead>
                <tbody>

                    @foreach ($user_list as $user) 
                    <?php $sl= $sl+1 ; ?>
                    <tr>
                        <td class="center">{{ $sl}} </td>
                        <td class="center"> {{ ucwords($user->plan_name)}}</td>
                        <td class="center"> {{ ucwords($user->amount)}}</td>
                        <td class="center">  {{date("M d , Y",strtotime($user->subscription_start_date))}}</td>
                        <td class="center">  {{date("M d , Y",strtotime($user->subscription_end_date))}}</td>
                        
                        
                        <td class="center"> {{ ucwords($user->user_name)}}</td>
                        <td class="center"> <img src="{{asset('public/assets/images/'.$user->user_image)}}" alt="Smiley face" height="42" width="42"></td>
                        
                        <td class="center"> {{ ucwords($user->user_email)}}</td>
                        <td class="center"> {{ $user->user_phone_num}}</td>
                         
                        <td class="center"> @if ($user->status==1) {{"Active"}} @else {{"Block"}}  @endif 
                        
                            
                        </td> 
                       
                        
                    </tr>
                    @endforeach

                </tbody>
            </table>

        </div>
        <div class="pagination alternate pull-right">
            {!! $user_list->appends(['search_key'=>Request::get('search_key')
            
            ])->setPath('')->render() !!}
        </div>
        @endif
    </div>
</div>



@endsection
