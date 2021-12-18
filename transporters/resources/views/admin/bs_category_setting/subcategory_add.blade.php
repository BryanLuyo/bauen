@extends('../../master_layout/admin_master')

@section('title')
<title>Buy & Sell App</title>
@endsection

@section('content_header')
 <a href="{{url('admin-subcategory-list')}} "><i class="icon-home"></i> Subcategory</a>
@endsection

@section('custom_js')
<script src="{{asset('public/assets/Myfile/category_js.js')}}"></script>
@endsection

@section('content')




 <div class="widget-box">
        <div class="widget-title"> <span class="icon"> <i class="icon-info-sign"></i> </span>
            <h5>Subcategory Add</h5>
        </div>
     
      
        <div class="widget-content nopadding">
            
            @if(count($errors) > 0)
        <div class="alert alert-danger">
            <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
            </ul>
        </div>
        @endif
            <form class="form-horizontal" id="admin_subcategory_add" name="admin_subcategory_add" action="admin-subcategory-add-post" method="post" enctype="multipart/form-data">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="control-group">
                    <label class="control-label">Category:</label>
                    <div class="controls " >  
                       
                        <select name="category_id" id="category_id">
                            <option value="0">Select Category</option>
                            @if(!empty($category))
                            @foreach($category as $cat)
                             <option value="{{$cat->id}}">{{$cat->category_name}}</option>
                            @endforeach
                            @endif
                        </select>   
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">Subcategory Name</label>
                    <div class="controls">
                        <input type="text" name="subcategory_name" />
                    </div>
                </div>
<!--                <div class="control-group">
                    <label class="control-label">Subcategory Logo </label>
                    <div class="controls">
                        <input type="file" name="subcategory_image"/>
                    </div>
                </div>
                <div class="control-group">                
                    <label class="control-label">Subcategory Description </label>
                    <div class="controls">
                        <input type="text" name="subcategory_description"  />
                    </div>
                </div>-->
                <div class="control-group">
                    
                    <div class="controls">
                        <input type="submit" class="btn btn-default" name="subcategory_submit" id="subcategory_submit" value="submit"/>
                    </div>
                </div>
                
            </form>   
              




        </div>
    </div>


@endsection
