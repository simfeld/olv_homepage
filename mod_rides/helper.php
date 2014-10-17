<?php
/**
 * Helper class for rides module
 */
class modRidesHelper
{
    private static $db = null;
    
    /**
     * 
     * @param array $params An object containing the module parameters
     * @access public
     */    
    public static function load( $params, $type, $id )
    {
		  $name = '';
			if ($type != 'all') {
			  $name = implode(' ',self::loadName($type, $id));
			}
			$html = self::writeLoginForm($type, $name) . self::writeRegister() . self::writeContentNew($type, $id) . self::writeRegistrationForm();
			return $html;
    }
    
    /**
    *
    * establish database connection
    */
    private static function getDB()
    {
      if (is_null(self::$db)) {
        $option = array(); //prevent problems
        $option['driver']   = 'mysql';        // Database driver name
        $option['host']     = 'localhost';    // Database host name
        $option['user']     = 'web407';       // User for database authentication
        $option['password'] = 'D7nuT$_36Pi6'; // Password for database authentication
        $option['database'] = 'usr_web407_3'; // Database name
        $option['prefix']   = '';             // Database prefix (may be empty)
        self::$db = JDatabase::getInstance( $option );
      }
      return self::$db;
    }
    
    // ================================================================================== //
    // content writing functions
    // ================================================================================== //
    
    /**
    *
    * write main content
    */
    private static function writeContentNew($type, $id)
    {
			// iterate over events
			// driver: if not registered for event -> register form
			//   if registered -> show passengers
			// passenger: if not registered for event -> show options (loadContent not suitable because these not shown)
			//   if registered -> show driver and other passengers
      $db = self::getDB();
      $query = $db->getQuery(true);
      $cond = array($db->quoteName('r.driver') . ' = ' . $db->quoteName('d.id'),
                     $db->quoteName('r.event') . ' = ' . $db->quoteName('e.id'));
      $from = $db->quoteName(array('driver', 'event', 'ride'), array('d', 'e', 'r'));
      if ($type != 'all') {
        if ($type == 'driver') {
          array_push($cond, $db->quoteName('d.id') . ' = ' . $db->quote($id));
        }
      }
      $query
        ->select($db->quoteName(array('e.id', 'e.name', 'e.date', 'd.id', 'd.firstname', 'd.lastname', 'r.place', 'r.time', 'r.n', 'r.id'),
                                array('eid', 'ename', 'edate', 'did', 'dfirst', 'dlast', 'place', 'time', 'n', 'rid')))
        ->from($from)
        ->where($cond)
        ->order(array($db->quoteName('e.date') . ' asc', $db->quoteName('d.id') . ' asc'));
      //JFactory::getApplication()->enqueueMessage(JText::_((string) $query), 'error');
      $db->setQuery($query);
      $content = $db->loadObjectList();
      $events = self::loadEvents();
      $ret = '<div id="accordion">';
			for ($i = 0; $i < count($events); ++$i) {
			  $event = $events[$i]->eid;
			  $ret .= '<table class="title"><tr class="title"><td class="date"><h2 class="date">' . self::toDate($events[$i]->edate) . '</h2></td>';
        $ret .= '<td class="title"><h2 class="title">' . $events[$i]->ename . '</h2></h2></tr></table>';
        $ret .= '<div class="content">';
        $table = '<table id="ride-content" border="1"><tr><td><strong>Fahrer</strong></td><td><strong>Passagiere</strong></td>';
        $table .= '<td><strong>Ort</strong></td><td><strong>Zeit</strong></td><td><strong>Anzahl Plätze</strong></td></tr>';
        $ridecnt = 0;
        $presentat = -1;
        $tblist = array();
        $full = array();
        $rideid = array();
        for ($j = 0; $j < count($content); ++$j) {
			    if ($content[$j]->eid != $event) {
			      continue;//only for the right events
			    }
			    ++$ridecnt;
          $line = '<td>' . $content[$j]->dfirst . ' ' . $content[$j]->dlast . '</td><td>';
          $query = $db->getQuery(true);
          $cond = array($db->quoteName('rding.ride') . ' = ' . $db->quoteName('r.id'),
                        $db->quoteName('rding.passenger') . ' = ' . $db->quoteName('p.id'),
                        $db->quoteName('r.id') . ' = ' . $db->quote($content[$j]->rid));
          $query
            ->select($db->quoteName(array('p.firstname', 'p.lastname', 'p.id'), array('pfirst', 'plast', 'pid')))
            ->from($db->quoteName(array('ride', 'passenger', 'riding'), array('r', 'p', 'rding')))
            ->where($cond);
          $db->setQuery($query);
          $passengers = $db->loadObjectList();
          $npass = count($passengers);
          for ($k = 0; $k < count($passengers); ++$k) {
            $line .= '' . $passengers[$k]->pfirst . ' ' . $passengers[$k]->plast . '<br />';
            if ($type == 'passenger' && $passengers[$k]->pid == $id) $presentat = $j;
          }
          $line .= '</td>';
			    $line .= '<td>' . $content[$j]->place . '</td>';
			    $line .= '<td>' . $content[$j]->time . '</td>';
			    $line .= '<td>' . $content[$j]->n . ', noch ' . ($content[$j]->n - $npass) . ' frei</td>';
			    $tblist[$j] = $line;
			    $full[$j] = (($content[$j]->n - $npass)<=0);
			    $rideid[$j] = $content[$j]->rid;
			  }
			  if ($type == 'driver') {
			    if ($ridecnt > 0) {
			      $impl = '<td>' . self::writeDeleteFormD($id, $event) . '</td></tr>';
			      $table .= '<tr>' . implode($impl . '<tr>',$tblist) . $impl;
			      $table .= '</table>';
			      $ret .= $table;
			    } else {
			      $ret .= self::writeAddFormD($id, $event);
			    }
			  } else if ($type == 'passenger') {
			    if ($presentat == -1) {
			      for ($k = 0; $k < count($content); ++$k) {
			        if ($tblist[$k] == '') continue;
			        if ($full[$k]) {
			          $table .= '<tr>' . $tblist[$k] . '</tr>';
			        } else {
			          $table .= '<tr>' . $tblist[$k] . '<td>' . self::writeAddFormP($id, $rideid[$k]) . '</td></tr>';
			        }
			      }
			      //$table .= '<tr>' . implode($impl . '<tr>',$tblist) . $impl;
			      $table .= '</table>';
			      $ret .= $table;
			    } else {
			      $table .= '<tr>' . $tblist[$presentat] . '<td>' . self::writeDeleteFormP($id, $rideid[$presentat]) . '</td></tr></table>';
			      $ret .= $table;
			    }
			  } else if ($type == 'all') {
			    $table .= '<tr>' . implode('</tr><tr>',$tblist) . '</tr>';
			    $table .= '</table>';
			    $ret .= $table;
			  }
			  $ret .= '</div>';
			}
			$ret .= '</div>';
			return $ret;
    }
    
    /**
    *
    * write login form
    */
    private static function writeLoginForm($type, $name)
    {
      $drivers = self::loadDrivers();
      $passengers = self::loadPassengers();
      $ret = '<div id="login">';
      if ($type == 'all') {
        $ret .= '<form action="/" id="loginForm">';
        $ret .= '<label class="left btn active" for="radio1">Fahrer</label>';
        $ret .= '<label class="right btn" for="radio2">Passagier</label>';
        $ret .= '<input id="radio1" type="radio" checked="checked" class="dselect hidden" name="type" value="driver">';//type[driver|passenger]
        $ret .= '<input id="radio2" type="radio" class="pselect hidden" name="type" value="passenger">';
        $ret .= '<select class="dselect" name="driver" size="1">';//driver[$id]
        for ($i = 0; $i < count($drivers); ++$i) {
          $ret .= '<option value="' . $drivers[$i]->did. '">' . $drivers[$i]->dfirst . ' ' . $drivers[$i]->dlast . '</option>';
        }
        $ret .= '</select>';
        $ret .= '<select class="pselect hidden" name="passenger" size="1">';//passenger[$id]
        for ($i = 0; $i < count($passengers); ++$i) {
          $ret .= '<option value="' . $passengers[$i]->pid. '">' . $passengers[$i]->pfirst . ' ' . $passengers[$i]->plast . '</option>';
        }
        $ret .= '<input type="submit" value="laden">';
        $ret .= '</select>';
        $ret .= '</form>';
      } else {
        $ret .=  '<form action="/" id="resetForm"><label>' . $name . '</label><input type="submit" value="reset"></form>';
      }
      $ret .= '</div>';
      return $ret;
    }
    
    /**
    *
    * write register button
    */
    private static function writeRegister()
    {
      return '<div id="register"><form action="/" id="popRegistration"><input type="submit" value="registrieren"></form></div>';
    }
    
    /**
    *
    * write back button
    */
    private static function writeBack()
    {
      return '<div id="back" class="hidden"><form action="/" id="goBack"><input type="submit" value="zurück"></form></div>';
    }
    
    /**
    *
    * write register button
    */
    private static function writeRegistrationForm()
    {
      $ret = '<div id="registration" class="hidden"><form class="form-validate" action="/" id="registrationForm">';
      $ret .= '<label>Registriere dich in der Mitfahrzentralen-Datenbank</label>';
      $ret .= '<table><tr><td><label class="left btn active" for="radio3">Fahrer</label>';
      $ret .= '<label class="right btn" for="radio4">Passagier</label>';
      $ret .= '<input id="radio3" type="radio" checked="checked" class="dselect hidden" name="type" value="driver">';//type[driver|passenger]
      $ret .= '<input id="radio4" type="radio" class="pselect hidden" name="type" value="passenger"></td></tr>';
      $ret .= '<tr><td><label>Vorname</label></td>';
      $ret .= '<td><input type="text" class="required" size="10" name="first"></td></tr>';
      $ret .= '<tr><td><label>Name</label></td>';
      $ret .= '<td><input class="required" type="text" size="10" name="last"></td></tr>';
      $ret .= '<tr><td><label>email</label></td>';
      $ret .= '<td><input class="validate-email required" type="text" size="15" name="email"></td></tr>';
      $ret .= '<tr><td><input type="submit" class="validate" value="registrieren"></td></tr></table></form></div>';
      $ret .= self::writeBack();
      return $ret;
    }
    
    /**
    *
    * write form to add driver to specified event
    */
    private static function writeAddFormD($id, $eventid)
    {
      $ret = '';
      $ret .= '<div id="addDriver">';
      $ret .= '<form class="form-validate" action="/" id="addDriverForm">';
      $ret .= '<input type="hidden" name="id" value="' . $id . '">';
      $ret .= '<input type="hidden" name="eventid" value="' . $eventid . '">';
      $ret .= '<table><tr><td><label>Anzahl Plätze:</label></td>';
      $ret .= '<td><input class="validate-numeric required" type="text" name="nr" style="width:2em;"></td></tr>';
      $ret .= '<tr><td><label>Ort:</label></td>';
      $ret .= '<td><input class="required" type="text" name = "place" size="10"></td></tr>';
      $ret .= '<tr><td><label>Zeit:</label></td>';
      $ret .= '<td><input type="text" class="validate-numeric required" name="timehr" style="width:2em;"><label>:</label><input type="text" class="validate-numeric required" name="timemin" style="width:2em;"></td></tr>';
      $ret .= '<tr><td><input type="submit" class="validate" value="eintragen"></td></tr></table></form></div>';
      return $ret;
    }
    
    /**
    *
    * write form to add passenger to specified ride
    */
    private static function writeAddFormP($id, $rid)
    {
      $ret = '';
      $ret .= '<div id="addPassenger">';
      $ret .= '<form action="/" id="addPassengerForm">';
      $ret .= '<input type="hidden" name="id" value="' . $id . '">';
      $ret .= '<input type="hidden" name="rid" value="' . $rid . '">';
      $ret .= '<input type="submit" value="eintragen"></form></div>';
      return $ret;
    }
    
    /**
    *
    * write form to delete passenger from specified ride
    */
    private static function writeDeleteFormP($id, $rid)
    {
      $ret = '';
      $ret .= '<div id="deletePassenger">';
      $ret .= '<form action="/" id="deletePassengerForm">';
      $ret .= '<input type="hidden" name="id" value="' . $id . '">';
      $ret .= '<input type="hidden" name="rid" value="' . $rid . '">';
      $ret .= '<input type="submit" value="Eintrag löschen"></form></div>';
      return $ret;
    }
    
    /**
    *
    * write form to delete driver from specified event
    */
    private static function writeDeleteFormD($id, $eventid)
    {
      $ret = '';
      $ret .= '<div id="deleteDriver">';
      $ret .= '<form action="/" id="deleteDriverForm">';
      $ret .= '<input type="hidden" name="id" value="' . $id . '">';
      $ret .= '<input type="hidden" name="eventid" value="' . $eventid . '">';
      $ret .= '<input type="hidden" name="rid" value="' . self::getRideId($id,$eventid) . '">';
      $ret .= '<input type="submit" value="Eintrag löschen"></form></div>';
      return $ret;
    }
    
    // ================================================================================== //
    // database getter queries
    // ================================================================================== //
    
    /**
    *
    * load driver list
    */
    private static function loadDrivers()
    {
      $db = self::getDB();
      $query = $db->getQuery(true);
      $query
        ->select($db->quoteName(array('d.id', 'd.firstname', 'd.lastname'), array('did', 'dfirst', 'dlast')))
        ->from($db->quoteName('driver', 'd'));
      $db->setQuery($query);
      return $db->loadObjectList();
    }
    
    /**
    *
    * check if Driver is registered in specified event
    */
    private static function checkDriverRegistered($did, $eid)
    {
      $db = self::getDB();
      $query = $db->getQuery(true);
      $cond = array($db->quoteName('r.driver') . ' = ' . $db->quote($did),
                     $db->quoteName('r.event') . ' = ' . $db->quote($eid),
                     $db->quoteName('r.event') . ' = ' . $db->quoteName('e.id'));
      $query
        ->select('COUNT(*) as n')
        ->from($db->quoteName(array('event', 'ride'), array('e', 'r')))
        ->where($cond);
      $db->setQuery($query);
      $result = $db->loadObjectList();
      return ($result[0]->n=='1');
    }
    
    /**
    *
    * get ride id corresponding to given driver and event
    */
    private static function getRideId($did, $eid)
    {
      $db = self::getDB();
      $query = $db->getQuery(true);
      $cond = array($db->quoteName('r.driver') . ' = ' . $db->quote($did),
                     $db->quoteName('r.event') . ' = ' . $db->quote($eid));
      $query
        ->select($db->quoteName('r.id','id'))
        ->from($db->quoteName(array('ride'), array('r')))
        ->where($cond);
      $db->setQuery($query);
      $result = $db->loadObjectList();
      return ($result[0]->id);
    }
    
    /**
    *
    * load name of driver or passenger
    */
    private static function loadName($type, $id)
    {
      $db = self::getDB();
      $query = $db->getQuery(true);
      if ($type == 'driver') {
        $query
          ->select($db->quoteName(array('d.firstname', 'd.lastname'), array('first', 'last')))
          ->from($db->quoteName('driver', 'd'))
          ->where($db->quoteName('d.id') . ' = ' . $db->quote($id));
      } else if ($type == 'passenger') {
        $query
          ->select($db->quoteName(array('p.firstname', 'p.lastname'), array('first', 'last')))
          ->from($db->quoteName('passenger', 'p'))
          ->where($db->quoteName('p.id') . ' = ' . $db->quote($id));
      }
      $db->setQuery($query);
      $result = $db->loadObjectList();
      return array($result[0]->first, $result[0]->last);
    }
    
    /**
    *
    * get email address
    */
    private static function getEmail($type, $id)
    {
      $db = self::getDB();
      $query = $db->getQuery(true);
      if ($type == 'driver') {
        $query
          ->select($db->quoteName('d.email', 'email'))
          ->from($db->quoteName('driver', 'd'))
          ->where($db->quoteName('d.id') . ' = ' . $db->quote($id));
      } else if ($type == 'passenger') {
        $query
          ->select($db->quoteName('p.email', 'email'))
          ->from($db->quoteName('passenger', 'p'))
          ->where($db->quoteName('p.id') . ' = ' . $db->quote($id));
      }
      $db->setQuery($query);
      $result = $db->loadObjectList();
      return $result[0]->email;
    }
    
    /**
    *
    * get ride email info
    */
    private static function getRideMail($ride, $name)
    {
      $db = self::getDB();
      $query = $db->getQuery(true);
      $query
          ->select($db->quoteName(array('e.name', 'e.date', 'd.firstname', 'd.email', 'r.place',  'r.time'), array('event', 'date', 'first', 'email', 'place', 'time')))
          ->from($db->quoteName(array('driver', 'event', 'ride'), array('d', 'e', 'r')))
          ->where(array($db->quoteName('r.id') . ' = ' . $db->quote($ride),
                        $db->quoteName('r.driver') . ' = ' . $db->quoteName('d.id'),
                        $db->quoteName('r.event') . ' = ' . $db->quoteName('e.id')));
      $db->setQuery($query);
      $result = $db->loadObjectList();
      $ret = "Hallo " . $result[0]->first . "\n\n";
      $ret .= $name . " hat sich für deine Fahrt eingetragen:\n\nAnlass:\t\t";
      $ret .= $result[0]->name . "\nDatum:\t\t";
      $ret .= $result[0]->date . "\nOrt:\t\t";
      $ret .= $result[0]->place . "\nZeit:\t\t";
      $ret .= $result[0]->time . "\n";
      $data = array($ret, $result[0]->email);
      return $data;
    }
    
    /**
    *
    * load event list
    */
    private static function loadEvents()
    {
      $db = self::getDB();
      $query = $db->getQuery(true);
      $query
        ->select($db->quoteName(array('e.id', 'e.name', 'e.date'), array('eid', 'ename', 'edate')))
        ->from($db->quoteName('event', 'e'))
        ->order($db->quoteName('e.date') . ' asc');
      $db->setQuery($query);
      return $db->loadObjectList();
    }
    
    /**
    *
    * load event id list
    */
    private static function loadEventIDs()
    {
      $db = self::getDB();
      $query = $db->getQuery(true);
      $query
        ->select($db->quoteName('e.agenda', 'id'))
        ->from($db->quoteName('event', 'e'));
      $db->setQuery($query);
      $result = $db->loadObjectList();
      $data = array();
      for ($i = 0; $i < count($result); ++$i) {
        $data[$i] = $result[$i]->id;
      }
      return $data;
    }
    
    /**
    *
    * update events
    * using agenda entries
    */
    public static function updateEvents()
    {
      $db = JFactory::getDbo();
      $query = $db->getQuery(true);
      $query
        ->select($db->quoteName(array('id', 'title', 'state', 'publish_down', 'metakey')))
        ->from($db->quoteName('#__content'))
        ->where($db->quoteName('catid') . ' = ' . $db->quote('10'))
        ->order($db->quoteName('publish_down') . ' asc');
      $db->setQuery($query);
      $query = $db->getQuery(true);
      $result = $db->loadObjectList();
      $ids = self::loadEventIDs();
      $db = self::getDB();
      $columns = array('agenda', 'name', 'date');
      $need = false;
      $query
        ->insert($db->quoteName('event'))
        ->columns($db->quoteName($columns));
      for ($i = 0; $i < count($result); ++$i) {
				$time = strtotime($result[$i]->publish_down);
			  if ($result[$i]->state == '1'&& time() < $time) {
			    $meta = explode(' ',$result[$i]->metakey);
			    if (in_array('ride',$meta) && (!in_array($result[$i]->id,$ids))) {
			      $values = array($db->quote($result[$i]->id), $db->quote($result[$i]->title), $db->quote($result[$i]->publish_down));
			      $query->values(implode(',', $values));
			      if (!$need) $need = true;
			    }
			  }
			}
      $db->setQuery($query);
      if ($need) {
        return $db->query();
      } else {
        return 'true';
      }
    }
    
    /**
    *
    * load passenger list
    */
    private static function loadPassengers()
    {
      $db = self::getDB();
      $query = $db->getQuery(true);
      $query
        ->select($db->quoteName(array('p.id', 'p.firstname', 'p.lastname'), array('pid', 'pfirst', 'plast')))
        ->from($db->quoteName('passenger', 'p'));
      $db->setQuery($query);
      return $db->loadObjectList();
    }
    
    /**
    *
    * load the main content
    * depending on what type is selected
    */
    private static function loadContent($type, $id)
    {
      $db = self::getDB();
      $query = $db->getQuery(true);
      $cond = array($db->quoteName('r.driver') . ' = ' . $db->quoteName('d.id'),
                     $db->quoteName('r.event') . ' = ' . $db->quoteName('e.id'),
                     $db->quoteName('rding.passenger') . ' = ' . $db->quoteName('p.id'),
                     $db->quoteName('rding.ride') . ' = ' . $db->quoteName('r.id'));
      if ($type != 'all') {
        if ($type == 'driver') {
        array_push($cond, $db->quoteName('d.id') . ' = ' . $db->quote($id));
        } else if ($type == 'passenger') {
        array_push($cond, $db->quoteName('p.id') . ' = ' . $db->quote($id));
        }
      }
      $query
        ->select($db->quoteName(array('e.id', 'e.name', 'e.date', 'd.id', 'd.firstname', 'd.lastname', 'p.firstname', 'p.lastname', 'r.place', 'r.time', 'r.n'),
                                array('eid', 'ename', 'edate', 'did', 'dfirst', 'dlast', 'pfirst', 'plast', 'place', 'time', 'n')))
        ->from($db->quoteName(array('driver', 'event', 'passenger', 'ride', 'riding'), array('d', 'e', 'p', 'r', 'rding')))
        ->where($cond)
        ->order(array($db->quoteName('e.date') . ' asc', $db->quoteName('d.id') . ' asc'));
      $db->setQuery($query);
      return $db->loadObjectList();
    }
    
    // ================================================================================== //
    // database setter queries
    // ================================================================================== //    
    
    /**
    *
    * register driver or passenger in database
    */
    public static function register($type, $first, $last, $email)
    {
      $db = self::getDB();
      $query = $db->getQuery(true);
      $columns = array('firstname', 'lastname', 'email');
      $values = array($db->quote($first), $db->quote($last), $db->quote($email));
      $query
        ->insert($db->quoteName($type))
        ->columns($db->quoteName($columns))
        ->values(implode(',', $values));
      $db->setQuery($query);
      $ret = '';
      if ($db->query()) {
        $ret .= self::load(null, 'all', null);
      } else {
        $ret .= self::showerror();
      }
      $subj = "Mitfahrzentrale Registrierung";
      $body = "Hallo " . $first . "\n\nDu wurdest erfolgreich in der Mitfahrzentralen-Datenbank regisriert.";
      self::sendMail($email, $subj, $body);
      return $ret;
    }
    
    /**
    *
    * add driver to event
    */
    public static function addDriver($id, $eventid, $nr, $place, $time)
    {
      $db = self::getDB();
      $query = $db->getQuery(true);
      $columns = array('event', 'driver', 'place', 'time', 'n');
      $values = array($db->quote($eventid), $db->quote($id), $db->quote($place), $db->quote($time), $db->quote($nr));
      $query
        ->insert($db->quoteName('ride'))
        ->columns($db->quoteName($columns))
        ->values(implode(',', $values));
      $db->setQuery($query);
      $ret = '';
      if ($db->query()) {
        $ret .= self::load(null, 'driver', $id);
      } else {
        $ret .= self::showerror();
      }
      return $ret;
    }
    
    /**
    *
    * add passenger to ride
    */
    public static function addPassenger($id, $ride)
    {
      $db = self::getDB();
      $query = $db->getQuery(true);
      $columns = array('ride', 'passenger');
      $values = array($db->quote($ride), $db->quote($id));
      $query
        ->insert($db->quoteName('riding'))
        ->columns($db->quoteName($columns))
        ->values(implode(',', $values));
      $db->setQuery($query);
      $ret = '';
      if ($db->query()) {
        $ret .= self::load(null, 'passenger', $id);
      } else {
        $ret .= self::showerror();
      }
      $subj = "Mitfahrzentrale";
      $name = implode(' ', self::loadName('passenger', $id));
      $data = self::getRideMail($ride, $name);
      self::sendMail($data[1], $subj, $data[0]);// send mail to corresponding driver
      return $ret;
    }
    
    /**
    *
    * delete driver from event
    */
    public static function deleteDriver($id, $ride)
    {
      $db = self::getDB();
      $query = $db->getQuery(true);
      $cond = array($db->quoteName('id') . ' = ' . $db->quote($ride));
      $query
        ->delete($db->quoteName('ride'))
        ->where($cond);
      $db->setQuery($query);
      $success = $db->query();
      $query = $db->getQuery(true);
      $cond = array($db->quoteName('ride') . ' = ' . $db->quote($ride));
      $query
        ->delete($db->quoteName('riding'))
        ->where($cond);
      $db->setQuery($query);
      $ret = '';
      if ($db->query()&&$success) {
        $ret .= self::load(null, 'driver', $id);
      } else {
        $ret .= self::showerror();
      }
      return $ret;
    }
    
    /**
    *
    * delete passenger from ride
    */
    public static function deletePassenger($id, $ride)
    {
      $db = self::getDB();
      $query = $db->getQuery(true);
      $cond = array($db->quoteName('ride') . ' = ' . $db->quote($ride),
                     $db->quoteName('passenger') . ' = ' . $db->quote($id));
      $query
        ->delete($db->quoteName('riding'))
        ->where($cond);
      $db->setQuery($query);
      $ret = '';
      if ($db->query()) {
        $ret .= self::load(null, 'passenger', $id);
      } else {
        $ret .= self::showerror();
      }
      return $ret;
    }
    
    // ================================================================================== //
    // helper functions
    // ================================================================================== //
    
    /**
    *
    * email sender
    */
    private static function sendMail($to, $subj, $body)
    {
      $mailer = JFactory::getMailer();
      $sender = array('mitfahrzentrale@olv-zug.ch', 'Mitfahrzentrale' );
      $mailer->setSender($sender);
      $mailer->addRecipient($to);
      $mailer->setSubject($subj);
      $mailer->setBody($body);
      return $mailer->Send();
    }
    
    /**
    *
    * date time conversion
    */
    private static function toDate($datetime)
    {
      return date('d.m.Y', strtotime($datetime));
    }
    
    /**
    *
    * check if date is in the past
    */
    private static function checkDate($time)
    {
      return (time() < strtotime($time));
    }
    
    /**
    *
    * error string
    */
    public static function showerror()
    {
      return 'Oops! An error occurred.';
    }
}
?>
