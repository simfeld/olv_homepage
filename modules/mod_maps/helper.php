<?php
/**
 * Helper class for maps module
 */
class modMapsHelper
{
    /**
     * Loads the upcoming events, displayed in an accordion component
     *
     * @param array $params An object containing the module parameters
     * @access public
     */    
    public static function load( $params )
    {
        $option = array(); //prevent problems
 
        $option['driver']   = 'mysql';            // Database driver name
        $option['host']     = 'localhost';    // Database host name
        $option['user']     = 'web407';       // User for database authentication
        $option['password'] = 'D7nuT$_36Pi6';   // Password for database authentication
        $option['database'] = 'usr_web407_2';      // Database name
        $option['prefix']   = '';             // Database prefix (may be empty)
 
        $db = JDatabase::getInstance( $option );
        $query = $db->getQuery(true);
        $query
          ->select('name, scale, equidistance, last_updated, area_full, area, access, description, img_link, X(coordinates) AS x, Y(coordinates) AS y')
          ->from($db->quoteName('maps'))
          ->order($db->quoteName('type') . ' asc');
        $db->setQuery($query);
        $result = $db->loadObjectList();
        if (empty($result))
				{
					JFactory::getApplication()->enqueueMessage( 'no maps data found');  
				}
				$document = JFactory::getDocument();
				$document->addStyleSheet(JURI::root() . 'modules/mod_maps/css/mod_maps.css');
				$ret = '<div id="accordion">';
				for ($i = 0; $i < count($result); ++$i) {
			    $ret .= '<h2>' . $result[$i]->name .'</h2>';
			    $ret .= '<div class="content"><table><tr><td><table border="1">';
			    $ret .= '<tr><td>Massstab</td><td>1:' . $result[$i]->scale . '</td></tr>';
			    $ret .= '<tr><td>Äquidistanz</td><td>' . $result[$i]->equidistance . ' m</td></tr>';
			    $ret .= '<tr><td>Koordinaten</td><td>' . $result[$i]->x . ' / ' . $result[$i]->y . '</td></tr>';
			    if ($result[$i]->area_full != '') {
			      $ret .= '<tr><td>Fläche</td><td>Gesamt: ' . $result[$i]->area_full . ' km&#178;<br />Lauffläche: ' . $result[$i]->area . ' km&#178;</td></tr>';
			    }
			    $ret .= '<tr><td>Erreichbarkeit</td><td>' . $result[$i]->access . '</td></tr>';
			    $ret .= '<tr><td>Beschreibung</td><td>' . $result[$i]->description . '</td></tr>';
			    $ret .= '</table></td><td><img src="' . JURI::root() . $result[$i]->img_link . '"></td></tr></table></div>';
				}
				$ret .= '</div>';
        return $ret;
    }
}
?>
