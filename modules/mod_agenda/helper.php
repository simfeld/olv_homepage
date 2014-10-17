<?php
/**
 * Helper class for agenda module
 */
class modAgendaHelper
{
    /**
     * Loads the upcoming events, displayed in an accordion component
     *
     * @param array $params An object containing the module parameters
     * @access public
     */    
    public static function load( $params )
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query
          ->select($db->quoteName(array('title', 'introtext', 'state', 'publish_down', 'metakey')))
          ->from($db->quoteName('#__content'))
          ->where($db->quoteName('catid') . ' = ' . $db->quote('10'))
          ->order($db->quoteName('publish_down') . ' asc');
        $db->setQuery($query);
        $result = $db->loadObjectList();
        if (empty($result))
				{
					JFactory::getApplication()->enqueueMessage( 'no agenda data found');  
				}
				$loechli = $params->get('loechli');
				//JFactory::getApplication()->enqueueMessage(date('Y-m-d H:i:s'));
				//JFactory::getApplication()->enqueueMessage(setlocale (LC_TIME, 'de_DE@euro', 'de_DE', 'de', 'ge'));
				$document = JFactory::getDocument();
				$document->addStyleSheet(JURI::root() . 'modules/mod_agenda/css/mod_agenda.css');
				$ret = '<div id="accordion">';
				for ($i = 0; $i < count($result); ++$i) {
				  $time = strtotime($result[$i]->publish_down);
				  if ($result[$i]->state == '1'&& time() < $time) {
				    $meta = explode(' ',$result[$i]->metakey);
				    if ($loechli == '1')
				    {
				      if (in_array('loechli',$meta) || in_array('all',$meta)) {
				        $ret .= '<table><tr><td class="date"><h2 class="date">' . date('d.m.Y', $time) . '</h2></td>';
                $ret .= '<td class="title"><h2 class="title">' . $result[$i]->title . '</h2></h2></tr></table>';
			          $ret .= '<div class="content">' . $result[$i]->introtext . '</div>';
				      }
				    } else {
				      if (in_array('olv',$meta) || in_array('all',$meta)) {
				        $ret .= '<table><tr><td  class="date"><h2 class="date">' . date('d.m.Y', $time) . '</h2></td>';
                $ret .= '<td class="title"><h2 class="title">' . $result[$i]->title . '</h2></h2></tr></table>';
			          $ret .= '<div class="content">' . $result[$i]->introtext . '</div>';
				      }
				    }
				  }
				}
				$ret .= '</div>';
        return $ret;
    }
}
?>
