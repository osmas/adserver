<?php 
$current_dir =  str_replace($_SERVER['DOCUMENT_ROOT'], '', __DIR__ );

include_once(substr(dirname( __FILE__ ), 0,-strlen($current_dir) ) . '/202-config/connect.php'); 

AUTH::require_user();

//show the template
template_top('Hourly Overview',NULL,NULL,NULL);  ?>

<div class="row" style="margin-bottom: 15px;">
	<div class="col-xs-12">
		<h6>Week Parting <?php showHelp("weekparting"); ?></h6>
		<small>Here you can see what day of the week performs best.</small>
	</div>
</div>

<?php display_calendar(get_absolute_url().'tracking202/ajax/sort_weekly.php', true, true, true, false, true, true); ?>    

<script type="text/javascript">
   loadContent('<?php echo get_absolute_url();?>tracking202/ajax/sort_weekly.php',null);
</script>

<?php template_bottom();