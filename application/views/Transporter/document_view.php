<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
	<section class="content-header">
      <h1>
        Document View
        <small><?php 
		if(!empty($user)){
			echo ucwords($user['first_name'].' '.$user['last_name']).' ('.phoneno_format($user['phone_no']).')';
		}
		if(!empty($vehicle)){
			echo "Plate No.: ".$vehicle['plate_no']." Year : ".$vehicle['purchase_year'];
		}
		?></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?=base_url(BASE_FOLDER_TRANS.'dashboard')?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
		<?php
		if(isset($user['is_company'])){
			if($user['is_company']=='0'){
		?>
		<li><a href="<?=base_url(BASE_FOLDER_TRANS.'drivers')?>">Drivers</a></li>
        <li><a href="<?=base_url(BASE_FOLDER_TRANS.'drivers/documents/'.$user['user_id'])?>">Documents</a></li>
		<?php
			}
			else{
			?>
		<li><a href="<?=base_url(BASE_FOLDER_TRANS.'documents')?>">My Documents</a></li>	
			<?php
			}
		}
		if(isset($vehicle['vehicle_id'])){
		?>
		<li><a href="<?=base_url(BASE_FOLDER_TRANS.'vehicles')?>">Vehicles</a></li>
        <li><a href="<?=base_url(BASE_FOLDER_TRANS.'vehicles/documents/'.$vehicle['vehicle_id'])?>">Documents</a></li>
		<?php
		}
		?>
        <li class="active">Document View</li>
      </ol>
    </section>
	<section class="content">
		<div class="row">
			<div class="col-xs-12">
				<div class="box">
					<div class="box-header">
						Document Type : <?=(isset($document['document_title']))?$document['document_title']:''?><br/>
						Document Status : <?php 
							if($document['document_status']==1){
								echo "<span class='bg-green'>Approved</span>";
							}
							elseif($document['document_status']==2){
								echo "<span class='bg-red'>Rejected</span>";
							}
							else{
								echo "<span class='bg-yellow'>Pending</span>";
							}
						?>
					<div class="box-body">
						<div class="row margin-bottom">
							<div class="col-sm-8">
							  <img class="img-responsive" src="<?=$document['file_name']?>" alt="Photo">
							</div>
						</div>
					</div>
					<div class="box-footer">
						
					</div>
				</div>
			</div>
		</div>
	</section>