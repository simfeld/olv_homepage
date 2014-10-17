<?php
/**
 * Agenda Module
 * 
 * Author: Samuel Imfeld
 * ©2014 OLV Zug
 */
 
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
// Include the syndicate functions only once
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));
require_once( dirname(__FILE__).'/helper.php' );
 
$hello = modAgendaHelper::load( $params );
require( JModuleHelper::getLayoutPath( 'mod_agenda' ) );
?>
