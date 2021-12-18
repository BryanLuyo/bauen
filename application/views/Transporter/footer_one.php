<!-- Bootstrap 3.3.6 -->
<script src="<?=base_url()?>assets/bootstrap/js/bootstrap.min.js"></script>
<!-- iCheck -->
<script src="<?=base_url()?>assets/plugins/iCheck/icheck.min.js"></script>
<!-- developer js -->
<script src="<?=base_url()?>assets/js/dev_script.js" type="text/javascript"></script>
<script type="text/javascript">
  $(function () {
    $('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%' // optional
    });
  });
</script>
<!-- preloader view -->
<div class="sitepreloader" style="width:100%; height:100%; background-color:cyan; z-index:9999; left:0px; top:0px; opacity:0.5; position:fixed; display:none;">
	<img src="<?=base_url('assets/Admin/dist/img/')?>avatar.png" style="position:absolute; top:50%; left:50%; margin-left:-108px; margin-top:-108px;" />
</div>
</body>
</html>