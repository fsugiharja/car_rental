<!-- Content Header (Page header) -->
<section class="content-header">
	<div class="col-md-6 col-md-6">
		<div class="row">
            <h1>
               <?php echo $this->lang->line("error_label_page"); ?>
            </h1>
            <ul class="breadcrumb">
                <li><a href="<?php echo base_url(); ?>"><?php echo $this->lang->line("menu_home"); ?></a></li>
                <li><a href=""><?php echo $this->lang->line("error_label_404"); ?></a></li>
            </ul>
		</div>
	</div>
	
</section>
<!-- Main content -->
<section class="content">
	<!-- Main row -->
	<div class="row">		
		<section class="col-lg-12 connectedSortable">
			<div class="col-md-12 panel-white box">			
				<div class="error-template text-center">
					<h1>
					   <?php echo $this->lang->line("error_label_404"); ?></h1>
					<div class="error-details">
					   <?php echo $this->lang->line("error_label_message"); ?>
					</div>
					<div class="error-actions">
						<a href="<?php echo base_url();?>" class="btn btn-primary-sequis"><span class="fa fa-home"></span>
							 <?php echo $this->lang->line("error_label_message_return"); ?> </a>
					</div>
				</div>	
				<div class="clearfix"></div>
			</div><!-- /.row (main col) -->
		</section><!-- /.row (main col) -->
	</div><!-- /.row (main row) -->
	
</section><!-- /.content -->
