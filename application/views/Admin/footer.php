<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$controller = strtolower($this->uri->segment(2));
$action = strtolower($this->uri->segment(3));
$action = (empty($action))?'index':$action;
?>
  </div>
  <!-- /.content-wrapper -->
  <footer class="main-footer">
    <div class="pull-right hidden-xs">
      <b>Version</b> 1.0
    </div>
    <strong>Copyright &copy; 2021 <a href="<?=base_url()?>">BAUEN FREIGHT</a>.</strong> All rights
    reserved.
  </footer>
</div>
<!-- ./wrapper -->
<script type="text/javascript">
	var contl = "<?=$controller?>";
	var acts = "<?=$action?>";
	$(document).ready(function(){
		dismissalert();
		menuselection();
	});
	
	function dismissalert(){
		var obj = $(document).find('.mr-alert');
		if($($(document).find('.mr-alert')).length>0){
			setTimeout(function(){
				$(obj).hide('slow',function(){
					$(this).html('');
				});
			},5000);
		}
		else{
			console.log("not found");
		}
	}
	
	function menuselection(){
		if(acts!=''){
			if($($(".sidebar-menu ."+contl+"").find("."+acts+"")).length){
				$($(".sidebar-menu ."+contl+"").find("."+acts+"")).addClass('active');
				$($($(".sidebar-menu ."+contl+"").find("."+acts+"")).parents('.treeview')).addClass('active');
			}
			else{
				if($($(".sidebar-menu").find("."+contl+"")).length){
					$($(".sidebar-menu").find("."+contl+"")).addClass('active');
				}
			}
		}
		else{
			if($($(".sidebar-menu").find("."+contl+"")).length){
				$($(".sidebar-menu").find("."+contl+"")).addClass('active');
			}
		}
	}
</script>

<!-- Sparkline -->
<script src="<?=base_url('assets/plugins/sparkline/jquery.sparkline.min.js')?>"></script>
<!-- FastClick -->
<script src="<?=base_url('assets/plugins/fastclick/fastclick.js')?>"></script>
<!-- AdminLTE App -->
<script src="<?=base_url('assets/dist/js/app.min.js')?>"></script>
<script src="<?=base_url('assets/js/dev_script.js')?>"></script>
<!-- preloader view -->
<div class="sitepreloader" style="width:100%; height:100%; background-color:gray; z-index:9999; left:0px; top:0px; opacity:0.5; position:fixed; display:none;">
	<img src="<?=base_url('assets/dist/img/')?>avatar.png" style="position:absolute; top:50%; left:50%; margin-left:-108px; margin-top:-108px;" />
</div>
</body>
</html>