@extends('../../master_layout/admin_master')

@section('title')
<title>Buy & Sell App</title>
@endsection

@section('custom_js')
<script src="{{asset('public/assets/Myfile/item_js.js')}}"></script>

<script src="{{asset('public/assets/Myfile/jquery.scrollify.js')}}"></script>

<script> 
		$(document).ready(function(){
			$('.scroller').scrollify(); 
		});
	</script> 
@endsection

@section('content_header')
 <a href="{{url('admin-user-post')}} "><i class="icon-home"></i> Ads</a>
@endsection

@section('custom_css')
	<link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.1.0/css/font-awesome.css">
	<link rel="stylesheet" href="{{asset('public/assets/Myfile/jquery-scrollify-style.css')}}" />
@endsection

@section('content')
    <div class="widget-box">
        <div class="widget-title"> <span class="icon"> <i class="icon-info-sign"></i> </span>
            <h5>Ad Details</h5>
        </div>
        <div class="widget-content nopadding">

            <form class="form-horizontal">
                <div class="control-group">
                    <label class="control-label">Ad Name</label>
                    <div class="controls">
                        <input type="text" value="{{$product_details->item_name}}" disabled />
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label">Price </label>
                    <div class="controls">
                        <input type="text" value="${{$product_details->price}}" disabled />
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">Ad posted by</label>
                    <div class="controls">
                        <input type="text" value="{{$product_details->user_name}}" disabled />
                    </div>
                </div>
                
                <div class="control-group">
                    <label class="control-label">Category Name</label>
                    <div class="controls">
                        <input type="text" value="{{$product_details->category_name}}" disabled />
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">Subcategory Name</label>
                    <div class="controls">
                        <input type="text" value="{{$product_details->subcategory_name}}" disabled />
                    </div>
                </div>
				 <div class="control-group">
                    <label class="control-label">Description</label>
					
					
					<div class="controls">
					<textarea name="description" id="description" class="form-control" disabled rows="4">
					@if(!empty($product_details->description)){{$product_details->description}} @endif
					</textarea>
                    </div>  
                    
                </div>
                <div class="control-group">
                    <label class="control-label">Post Tags</label>
                    <div class="controls">
                        <input type="text" value="{{$product_details->item_tags}}" disabled />
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label"> </label>  
					 @if($product_details->video_link!='')
                                    <video controls>
										@if(!empty($product_details->video_link))
											<source src="{{asset('public/assets/images/'.$product_details->video_link)}}" type="video/mp4">
										@endif
                                        Your browser does not support HTML5 video.
                                    </video>
				   @endif
                </div>
                <div class="control-group">
                    <label class="control-label">images</label>                    
                        @if(!empty($product_details->item_images))							
						<ul class="scroller"> 
						 @foreach($product_details->item_images as $img)   
								<li>
									<a href="{{asset('public/assets/images/'.$img->item_image_name)}}" data-title=""> 
										<img src="{{asset('public/assets/images/'.$img->item_image_name)}}"> 
									</a>
								</li>

						 @endforeach
						 </ul>	
						 @else
							<div class="widget-content nopadding">
							<h3>  No results found </h3>
							</div>
                        @endif  
                </div>
            </form>   
        </div>
    </div>
	
	
	
	
	
	
	
@endsection
