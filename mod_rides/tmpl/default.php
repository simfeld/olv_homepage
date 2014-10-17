<?php // no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
JHtml::_('behavior.framework');
JHtml::_('jquery.framework');
JHTML::_('behavior.formvalidation');
JHtml::_('bootstrap.framework');
$document = JFactory::getDocument();
$document->addStyleSheet(JURI::base() . '/modules/mod_rides/css/mod_rides.css');
$document->addScript(JURI::base() . '/modules/mod_rides/js/mod_rides_mootools.js');
$document->addScript(JURI::base() . '/modules/mod_rides/js/mod_rides_jquery.js');
$document->addScript(JURI::base() . '/modules/mod_rides/js/mod_rides_post.js');
?>
<div id="rides" class="custom<?php echo $moduleclass_sfx ?>" style="margin-top:5px;">
<?php echo $hello; ?>
</div>
