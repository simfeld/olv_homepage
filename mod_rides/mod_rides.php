<?php
/**
 * Ride Arrangement Module
 * 
 * Author: Samuel Imfeld
 * ©2014 OLV Zug
 */
 
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// Include the syndicate functions only once
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));
require_once( dirname(__FILE__).'/helper.php' );

$layout = 'default';

// get POST request
$post = JRequest::get('post');
$type = JRequest::getVar('type', 'all');
$reset = JRequest::getVar('reset', null);

// get id
$id = null;
// data required for driver or passenger? -> get id
if ($type == 'driver') {
  $id = JRequest::getVar('driver', null);
} else if ($type == 'passenger') {
  $id = JRequest::getVar('passenger', null);
}

// register button pressed?
$register = JRequest::getVar('register', 'false');

// add button pressed?
$add = JRequest::getVar('add', 'false');

// delete button pressed?
$del = JRequest::getVar('del', 'false');

$hello = '';
$hello .= '<script type="text/javascript">document.baseurl="' . JURI::base() . '";</script>';

// need to update events?
if ($type == 'all' && $reset != 'true') {
  if(!modRidesHelper::updateEvents()) {
    $hello .= modRidesHelper::showerror();
  }
}

if ($register == 'true') {
  // register new driver or passenger
  // get register data
  $first = JRequest::getVar('first', null);
  $last = JRequest::getVar('last', null);
  $email = JRequest::getVar('email', null);
  // check if null, then execute
  if ($first!=null&&$last!=null&&$email!=null) {
    $hello .= modRidesHelper::register($type, $first, $last, $email);
  } else {
    $hello .= modRidesHelper::showerror();
  }
} else if ($add == 'true') {
  // add driver to event
  // get driver data
  $id = JRequest::getVar('ident', null);
  if ($type == 'driver') {
    $eventid = JRequest::getVar('eventid', null);
    $nr = JRequest::getVar('nr', null);
    $place = JRequest::getVar('place', null);
    $time = JRequest::getVar('time', null);
    // check if null, then execute
    if ($nr!=null&&$place!=null&&$time!=null&&$id!=null&&$eventid!=null) {
      $hello .= modRidesHelper::addDriver($id, $eventid, $nr, $place, $time);
    } else {
      $hello .= modRidesHelper::showerror();
    }
  } else if ($type == 'passenger') {
    $rid = JRequest::getVar('rideid', null);
    // check if null, then execute
    if ($id!=null&&$rid!=null) {
      $hello .= modRidesHelper::addPassenger($id, $rid);
    } else {
      $hello .= modRidesHelper::showerror();
    }
  }
} else if ($del == 'true') {
  // add driver to event
  // get driver data
  $id = JRequest::getVar('ident', null);
  $rid = JRequest::getVar('rideid', null);
  if ($type == 'driver') {
    // check if null, then execute
    if ($id!=null&&$rid!=null) {
      $hello .= modRidesHelper::deleteDriver($id, $rid);
    } else {
      $hello .= modRidesHelper::showerror();
    }
  } else if ($type == 'passenger') {
    // check if null, then execute
    if ($id!=null&&$rid!=null) {
      $hello .= modRidesHelper::deletePassenger($id, $rid);
    } else {
      $hello .= modRidesHelper::showerror();
    }
  }
} else {
  // normal data request, no register
  $hello .= modRidesHelper::load( $params, $type, $id );
}
require( JModuleHelper::getLayoutPath( 'mod_rides' , $layout) );
?>
