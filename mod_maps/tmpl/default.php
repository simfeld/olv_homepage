<?php // no direct access
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>
<script type="text/javascript">
	// <![CDATA[
	window.addEvent('domready', function() {
		var acc = new Fx.Accordion('#accordion h2', '#accordion .content');
	});
	// ]]>
	
</script>
<div class="custom<?php echo $moduleclass_sfx ?>" style="margin-top:5px;">
<?php echo $hello; ?>
</div>
