@extends('../../master_layout/admin_master')

@section('title')
<title>Buy & Sell App</title>
@endsection

@section('custom_js')
<script src="{{asset('public/assets/Myfile/item_js.js')}}"></script>

@endsection

@section('content_header')
 <a href="{{url('admin-user-post')}} "><i class="icon-home"></i> Ad</a>
@endsection

@section('custom_css')
 
@endsection

@section('content')
    <div class="widget-box">
        <div class="widget-title"> <span class="icon"> <i class="icon-info-sign"></i> </span>
            <h5>Edit Ad</h5>
        </div>
        <div class="widget-content nopadding">
            <form action="{{url('admin-edit-user-post-submit')}}" method="post" enctype="multipart/form-data"> 
            <div class="form-horizontal">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="edit_item_id" value="{{$product_details->id}}">
                <div class="control-group">
                    <label class="control-label">Ad Name</label>
                    <div class="controls">
                        <input type="text" value="@if(!empty($product_details->item_name)){{$product_details->item_name}} @endif" name="edit_item_name" />
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label">Price </label>
                    <div class="controls">
                        <input type="number" step="0.01" value="@if(!empty($product_details->price)){{$product_details->price}}@endif" name="edit_price" />
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">Ad Description</label>
					<div class="controls">
					<textarea name="description" id="description" class="form-control" rows="4">
					@if(!empty($product_details->description)){{$product_details->description}} @endif
					</textarea>
                    </div>  
                </div>
                <div class="control-group">
                    <label class="control-label">Ad Tag</label>
                    <div class="controls">
                        <input type="text" value="@if(!empty($product_details->item_tags)){{$product_details->item_tags}} @endif" name="edit_item_tags" />
                    </div>
                </div>
                 <div class="control-group">
                    <label class="control-label">Video</label>
                    <div class="controls">
                        <input type="file" name="video_link" />
						
					</div>
					<div class="control-group">
                    <label class="control-label"> </label>
					<div class="pull right">
					 @if($product_details->video_link!='')
							<video controls>
								@if(!empty($product_details->video_link))
									<source src="{{asset('public/assets/images/'.$product_details->video_link)}}" type="video/mp4">
								@endif
								Your browser does not support HTML5 video.
							</video>
						@endif
					</div>
					</div>
                </div>
                <div class="control-group">
                    <label class="control-label">Select category:</label>
                    <div class="controls " >  
                       
                        <select name="edit_category_id" id="edit_category_id">
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
                    <label class="control-label">Select sub-category:</label>
                    <div class="controls " >  
                        <select name="edit_subcategory_id" id="edit_subcategory_id">
                            <option value="" > Select Sub-Category </option>
                            
                        </select>   
                    </div>
                </div> 

                <div class="control-group">                    
                    <div class="controls">
                        <input type="submit" class="btn btn-default" name="item_edit_submit" value="Update"/>
                    </div>
                </div>

            </div>  
         </form>   
        </div>
    </div>
	
	
@if(!empty($product_details->item_images))
<div class="widget-box">
        <div class="widget-title"> <span class="icon"> <i class="icon-info-sign"></i> </span>
            <h5>Edit Emages</h5>
        </div>
    <div class="widget-content nopadding">     
        @foreach($product_details->item_images as $img)
         <div class="control-group" id="image_delete_id_{{$img->id}}">                    
                <div class="controls">
               <input type="hidden" id="image_id_{{$img->id}}" value="{{json_encode($img)}}">
               <img src="{{asset('public/assets/images/'.$img->item_image_name)}}" width="100" height="75">
                <input type="button" onclick="open_edit_modal({{$img->id}})" value="update">
				 <input type="button" onclick="delete_ad_images({{$img->id}})" value="Delete">
                </div>
         </div>
        @endforeach   
        
    </div></div>




<!-- Modal -->
<div id="image_edit_myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel"> Image </h3>
    </div>
    <div class="modal-body">
       
        <form class="form-horizontal" action="{{URL::to('admin-edit-user-post-image')}}" method="post" enctype="multipart/form-data">
              <input type="hidden" name="_token" value="{{ csrf_token() }}">
              <input type="hidden" name="item_images_id" id="item_images_id" value="">
            <div class="control-group">
                <label class="control-label">Image</label>
                <div class="controls">
                    <input type="file" name="item_image_name"/>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">Image</label>
                <div class="controls">                   
                     <img id="set_edit_image" height="42" width="42" class="hover-shadow cursor">
                </div>
            </div>
            
            <input type="submit" class="btn btn-primary" value="Update">
        </form>
        
    </div>
    <div class="modal-footer">
        
         
         
        
        <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
       
    </div>
</div>


@endif




	
	
@endsection

