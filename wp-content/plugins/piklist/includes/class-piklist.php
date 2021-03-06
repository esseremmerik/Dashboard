<?php

class PikList
{      
  public static $paths = array();
  
  public static $plurals = array(
    'plural' => array(
      '/(quiz)$/i' => "$1zes"
      ,'/^(ox)$/i' => "$1en"
      ,'/([m|l])ouse$/i' => "$1ice"
      ,'/(matr|vert|ind)ix|ex$/i' => "$1ices"
      ,'/(x|ch|ss|sh)$/i' => "$1es"
      ,'/([^aeiouy]|qu)y$/i' => "$1ies"
      ,'/(hive)$/i' => "$1s"
      ,'/(?:([^f])fe|([lr])f)$/i' => "$1$2ves"
      ,'/(shea|lea|loa|thie)f$/i' => "$1ves"
      ,'/sis$/i' => "ses"
      ,'/([ti])um$/i' => "$1a"
      ,'/(tomat|potat|ech|her|vet)o$/i' => "$1oes"
      ,'/(bu)s$/i' => "$1ses"
      ,'/(alias)$/i' => "$1es"
      ,'/(octop)us$/i' => "$1i"
      ,'/(ax|test)is$/i' => "$1es"
      ,'/(us)$/i' => "$1es"
      ,'/s$/i' => "s"
      ,'/$/' => "s"
    )
    ,'singular' => array(
      '/(quiz)zes$/i' => "$1"
      ,'/(matr)ices$/i' => "$1ix"
      ,'/(vert|ind)ices$/i'  => "$1ex"
      ,'/^(ox)en$/i' => "$1"
      ,'/(alias)es$/i' => "$1"
      ,'/(octop|vir)i$/i' => "$1us"
      ,'/(cris|ax|test)es$/i' => "$1is"
      ,'/(shoe)s$/i' => "$1"
      ,'/(o)es$/i' => "$1"
      ,'/(bus)es$/i' => "$1"
      ,'/([m|l])ice$/i' => "$1ouse"
      ,'/(x|ch|ss|sh)es$/i' => "$1"
      ,'/(m)ovies$/i' => "$1ovie"
      ,'/(s)eries$/i' => "$1eries"
      ,'/([^aeiouy]|qu)ies$/i' => "$1y"
      ,'/([lr])ves$/i' => "$1f"
      ,'/(tive)s$/i' => "$1"
      ,'/(hive)s$/i' => "$1"
      ,'/(li|wi|kni)ves$/i' => "$1fe"
      ,'/(shea|loa|lea|thie)ves$/i' => "$1f"
      ,'/(^analy)ses$/i' => "$1sis"
      ,'/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i' => "$1$2sis"
      ,'/([ti])a$/i' => "$1um"
      ,'/(n)ews$/i' => "$1ews"
      ,'/(h|bl)ouses$/i' => "$1ouse"
      ,'/(corpse)s$/i' => "$1"
      ,'/(us)es$/i' => "$1"
      //,'/s$/i' => ""
    )
    ,'irregular' => array(
      'move' => 'moves'
      ,'foot' => 'feet'
      ,'goose' => 'geese'
      ,'sex' => 'sexes'
      ,'child' => 'children'
      ,'man' => 'men'
      ,'tooth' => 'teeth'
      ,'person' => 'people'
    )
    ,'ignore' => array(
      'sheep'
      ,'fish'
      ,'deer'
      ,'series'
      ,'species'
      ,'money'
      ,'rice'
      ,'information'
      ,'equipment'
      ,'media'
      ,'documentation'
    )
  );
  
  public static $prefix = 'pik_';
  
  public static function load()
  {    
    self::$paths['plugin'] = dirname(dirname(__FILE__));

    load_plugin_textdomain( 'piklist', false, dirname(dirname(plugin_basename(__FILE__ ))) . '/languages' );
    
    register_activation_hook('piklist/piklist.php', array('piklist', 'install'));
   
    self::auto_load();
  }
  
  public static function auto_load()
  {
    $includes = self::get_directory_list(self::$paths['plugin'] . '/includes');
    foreach ($includes as $include)
    {
      $class_name = str_replace(array('.php', 'class_'), array('', ''), self::slug($include));
      if ($include != __FILE__)
      {
        include_once self::$paths['plugin'] . '/includes/' . $include;
     
        if (class_exists($class_name) && method_exists($class_name, '_construct') && !is_subclass_of($class_name, 'WP_Widget'))
        {
          call_user_func(array($class_name, '_construct'));
        }
      }
    }
  }
  
  public static function install()
  {
    do_action('piklist_install');
  }
  
  public static function render($view, $arguments = array(), $return = false) 
  {
    global $post, $posts, $post_id, $current_user, $wpdb, $wp_query;
    
    if ($return)
    {
      ob_start();
    }

    foreach (self::$paths as $_display => $_path)
    {
      $_file = (path_is_absolute($view) ? $view : self::$paths[$_display] . '/parts/' . $view) . (strstr($view, '.php') ? '' : '.php');

      if (file_exists($_file))
      {      
        foreach ($arguments as $_key => $_value) 
        {
          $$_key = $_value;
        }
        
        include $_file;
        
        $_included = true;
        
        break;
      }
    }    

    if (!$_included)
    {
      self::pre('File Not Found: ' . $_file);
    }
    
    if ($return)
    {
      $output = ob_get_contents();
      
      ob_end_clean();

      return $output;
    }
  }

  public static function view_exists($view, $paths = array())
  {
    if (empty($paths))
    {
      return false;
    }
    
    foreach ($paths as $type => $path)
    {
      if (file_exists($path . '/parts/' . $view . '.php'))
      {
        return $type;
      }
    }
    
    return false;
  }
  
  public static function process_views($folder, $callback, $path = false, $prefix = '', $suffix = '.php')
  { 
    $paths = $path ? $path : self::$paths;

    foreach ($paths as $display => $path)
    {   
      $files = self::get_directory_list($path . '/parts/' . $folder);
      foreach ($files as $part)
      {
        $file_prefix = substr($part, 0, strlen($prefix));
        $file_suffix = substr($part, strlen($part) - strlen($suffix));
        if ($file_prefix == $prefix && $file_suffix == $suffix)
        {
          call_user_func_array($callback, array(array(
            'folder' => $folder
            ,'part' => $part
            ,'prefix' => $prefix
            ,'add_on' => $display
            ,'path' => $path
          )));
        }
      }
    }
  }
  
  public static function pre($output)
  {
    echo "<pre>";
    
    print_r($output);
  
    echo "</pre>\r\n";
  }
  
  public static function get_prefixed_post_types($prefix)
  {
    $post_types = get_post_types('', 'names');
    
    foreach ($post_types as $key => $post_type) 
    {
      if (substr($post_type, 0, strlen($prefix)) != $prefix)
      {
        unset($post_types[$key]);
      }
    }
    
    return $post_types;
  }
  
  public static function get_directory_list($start = '.', $path = false, $extension = false) 
  {
    $files = array();

    if (is_dir($start)) 
    {
      $file_handle = opendir($start);

      while (($file = readdir($file_handle)) !== false) 
      {
        if ($file != '.' && $file != '..' && strlen($file) > 2) 
        {
          if (strcmp($file, '.') == 0 || strcmp($file, '..') == 0) 
          {
            continue;
          }

          if ($file[0] != '.' && $file[0] != '_')
          {
            $file_parts = explode('.', $file);
            $_file = $extension ? $file : $file_parts[0];
            $file_path = $path ? $start . '/' . $_file : $_file;

            if (is_dir($file_path)) 
            {
              $files = array_merge($files, self::get_directory_list($file_path));
            } 
            else 
            {
              array_push($files, $file);
            }
          }
        }
      }

      closedir($file_handle);
    } 
    else 
    {
      $files = array();
    }

    return $files;
  }
   
  public static function dashes($string)
  {  
    return str_replace(array('_', ' '), '-', preg_replace('/[^a-z0-9]+/i', '-', str_replace('.php', '', strtolower($string))));
  }
  
  public static function slug($string)
  {
    return str_replace('.php', '', str_replace(array('-', ' '), '_', strtolower($string)));
  }
  
  public static function create_table($table_name, $columns) 
  {
    global $wpdb;
    
    $settings = $wpdb->has_cap('collation') ? (!empty($wpdb->charset) ? 'DEFAULT CHARACTER SET ' . $wpdb->charset : null) . (!empty($wpdb->collate) ? ' COLLATE ' . $wpdb->collate : null) : null;

    $wpdb->query('CREATE TABLE IF NOT EXISTS ' . $wpdb->prefix . $table_name . ' (' . $columns . ') ' . $settings . ';');
  }

  public static function delete_table($table_name) 
  {
    global $wpdb;

    $wpdb->query('DROP TABLE IF EXISTS ' . $wpdb->prefix . $table_name);
  }
  
  public static function post_type_labels($label)
  {
    return array(
      'name' => __(self::pluralize($label), 'piklist')
      ,'singular_name' => __(self::singularize($label), 'piklist')
      ,'add_new' => __('Add New ' . self::singularize($label), 'piklist')
      ,'add_new_item' => __('Add New ' . self::singularize($label), 'piklist')
      ,'edit_item' => __('Edit ' . self::singularize($label), 'piklist')
      ,'new_item' => __('Add New ' . self::singularize($label), 'piklist')
      ,'view_item' => __('View ' . self::singularize($label), 'piklist')
      ,'search_items' => __('Search ' . self::pluralize($label), 'piklist')
      ,'not_found' => __('No ' . self::pluralize(strtolower($label)) . ' found', 'piklist')
      ,'not_found_in_trash' => __('No ' . self::pluralize(strtolower($label)) . ' found in trash', 'piklist')
    );
  }
  
  public static function taxonomy_labels($label)
  {
    return array(
      'name' => __(self::singularize($label), 'piklist')
      ,'singular_name' => __(self::singularize($label), 'piklist')
      ,'search_items' =>  __('Search ' . self::pluralize($label), 'piklist')
      ,'all_items' => __('All ' . self::pluralize($label), 'piklist')
      ,'parent_item' => __('Parent'  . self::pluralize($label), 'piklist')
      ,'parent_item_colon' => __('Parent ' . self::pluralize($label) . ':', 'piklist')
      ,'edit_item' => __('Edit ' . self::singularize($label), 'piklist')
      ,'update_item' => __('Update ' . self::singularize($label), 'piklist')
      ,'add_new_item' => __('Add New ' . self::singularize($label), 'piklist')
      ,'new_item_name' => __('New ' . self::singularize($label) . ' Name', 'piklist')
      ,'menu_name' => __(self::pluralize($label), 'piklist')
    );
  }
  
  public static function pluralize($string)
  {
    if ((in_array(strtolower($string), self::$plurals['ignore'])) || (strrpos($string, ' ') && in_array(strtolower(substr($string, strrpos($string, ' ') + 1, strlen($string) - strrpos($string, ' ') + 1)), self::$plurals['ignore'])))
    {
      return $string;
    }
    
    foreach (self::$plurals['irregular'] as $pattern => $result)
    {
      $pattern = '/' . $pattern . '$/i';
      if (preg_match($pattern, $string))
      {
        return preg_replace($pattern, $result, $string);
      }
    }

    foreach (self::$plurals['plural'] as $pattern => $result)
    {
      if (preg_match($pattern, $string))
      {
        return preg_replace($pattern, $result, $string);
      }
    }

    return $string;
  }
  
  public static function singularize($string)
  {
    if (in_array(strtolower($string), self::$plurals['ignore']))
    {
      return $string;
    }
    
    foreach (self::$plurals['irregular'] as $pattern => $result)
    {
      $pattern = '/' . $pattern . '$/i';
      if (preg_match($pattern, $string))
      {
        return preg_replace($pattern, $result, $string);
      }
    }
    
    foreach (self::$plurals['singular'] as $pattern => $result)
    {
      if (preg_match($pattern, $string))
      {
        return preg_replace($pattern, $result, $string);
      }
    }

    return $string;
  }
  
  // Special Thanks to MikeSchinkel
  public static function add_admin_menu_separator($position) 
  {
    global $menu;
  
    if (isset($menu) && !empty($menu))
    {
      $index = 0;
    
      foreach ($menu as $offset => $section) 
      {
        if (substr($section[2], 0, 9) == 'separator')
        {
          $index++;
        }
      
        if ($offset >= $position) 
        {
          $menu[$position] = array(
            ''
            ,'read'
            ,'separator' . $index
            ,''
            ,'wp-menu-separator'
          );
        
          ksort($menu);
        
          break;
        }
      }
    }
  }
  
  public static function key_path($array, $find, $map = null)
  {
    $path = array();
    
    if (array_key_exists($find, $array))
    {
      return $map ? array("{$map[count($path)]}" => $find) : array($find);
    }
    else
    {
      foreach ($array as $key => $data)
      {
        if (is_array($data))
        {
          if ($path = self::key_path($data, $find, $map))
          {
            $path[($map ? $map[count($path)] : null)] = $key;
            
            return $path;
          }
        }
      }
    }

    return null;
  }
  
  public static function xml_to_array($xml) 
  {
    libxml_use_internal_errors(true);

    $xml_document = new DOMDocument();
    $xml_document->loadXML($xml);

    return self::dom_node_to_array($xml_document->documentElement);
  }

  public static function dom_node_to_array($node) 
  {
    $output = array();
    switch ($node->nodeType) 
    {
      case XML_CDATA_SECTION_NODE:
      case XML_TEXT_NODE:
        $output = trim($node->textContent);
      break;

      case XML_ELEMENT_NODE:
      for ($x = 0, $y = $node->childNodes->length; $x < $y; $x++) 
      {
        $child = $node->childNodes->item($x);

        $value = self::dom_node_to_array($child);

        if (isset($child->tagName)) 
        {
          $tag = $child->tagName;
          if (!isset($output[$tag])) 
          {
            $output[$tag] = array();
          }
          $output[$tag][] = $value;
        }
        elseif ($value) 
        {
          $output = (string) $value;
        }
      }

      if (is_array($output)) 
      {
        if ($node->attributes->length) 
        {
          $attributes = array();
          foreach($node->attributes as $key => $attribute_node) 
          {
            $attributes[$key] = (string) $attribute_node->value;
          }
          $output['@attributes'] = $attributes;
        }

        foreach ($output as $key => $value) 
        {
          if (is_array($value) && count($value) == 1 && $key != '@attributes') 
          {
            $output[$key] = $value[0];
          }
        }
      }

      break;
    }

    return $output;
  }
  
  public static function directory_empty($path)
  {
    if (is_dir($path))
    {
      $files = @scandir($path);
      return count($files) > 2 ? false : true;
    }
    
    return true;
  }

  public static function object_id($object)
  {
    if (!is_object($object)) 
    {
      return null;
    }
    
    if (!isset($object->__unique)) 
    {
      $object->__unique = microtime(true);
      usleep(1);
    }
    
    return spl_object_hash($object) . $object->__unique;
  }
  
  public static function get_settings($option, $setting)
  {
    $options = get_option($option);

    return isset($options[$setting]) ? $options[$setting] : array();
  }
  
  public static function check_in($needle, $haystack)
  {
    return (is_array($needle) && in_array($haystack, $needle)) || (is_string($needle) && $needle == $haystack);
  }
  
  public static function sort_by_order($a, $b) 
  {
    return $a['order'] - $b['order'];
  }
  
  public static function sort_by_args_order($a, $b) 
  {
    if (!isset($a['args']['order']) && !isset($b['args']['order']))
    {
      return 1;
    }

    return $a['args']['order'] - $b['args']['order'];
  }
  
  public static function array_next($array, $needle)
  {
    $keys = array_keys($array);
    $position = array_search($needle, $keys);

    if (isset($keys[$position + 1])) 
    {
      return $keys[$position + 1];
    }
    
    return $needle;
  }
    
  public static function object_custom($type, $id)
  {
    $meta = get_metadata($type, $id);

    foreach ($meta as $key => $value)
    {
      if (count($value) == 1) 
      {
        $meta[$key] = is_array($value[0]) ? array(maybe_unserialize($value[0])) : maybe_unserialize($value[0]);
      }
    }
    
    return $meta;
  }
  
  public static function performance()
  {
    if (!ini_get('safe_mode'))
    { 
      ini_set('max_execution_time', -1);
      ini_set('memory_limit', -1);
    }
  }
}

/*
 * Helper Function
 */
function piklist($option, $arguments = array())
{
  if (!is_array($arguments) && strstr($arguments, '='))
  {
    parse_str($arguments, $arguments);
  }

  if (is_array($option) || is_object($option))
  {
    $list = array();
    $arguments = is_array($arguments) ? $arguments : array($arguments);
    foreach ($option as $key => $value) 
    {
      if (count($arguments) > 1)
      {
        if (in_array('_key', $arguments))
        {
          $_value = $arguments[1];
          $list[$key] = is_object($value) ? $value->$_value : $value[$_value];
        }
        else
        {
          $__key = $arguments[0];
          $_key = is_object($value) ? $value->$__key : $value[$__key];

          $_value = $arguments[1];
          $list[$_key] = is_object($value) ? $value->$_value : $value[$_value];
        }
      }
      else
      {
        $_value = $arguments[0];
        array_push($list, is_object($value) && isset($value->$_value) ? $value->$_value : (isset($value[$_value]) ? $value[$_value] : null));
      }
    }

    return $list;
  }
  else
  {
    switch ($option)
    {
      case 'field':
        
        if (piklist_setting::get('active_section'))
        {
          piklist_setting::register_setting($arguments);
        }
        else
        {
          piklist_form::render_field($arguments, isset($arguments['return']) ? $arguments['return'] : false);
        }
        
      break;
      
      case 'post_type_labels':

        return piklist::post_type_labels($arguments);
        
      break;
      
      case 'taxonomy_labels':
      
        return piklist::taxonomy_labels($arguments);
        
      break;
      
      case 'post_custom':
      case 'get_post_custom':
      case 'get_user_custom':
      case 'get_term_custom':
        
        switch ($option)
        {
          case 'get_user_custom':
          
            $type = 'user';
            
          break;
          
          case 'get_term_custom':
          
            $type = 'term';
            
          break;
          
          default: 
          
            $type = 'post';
            
          break;
        }
        
        return piklist::object_custom($type, $arguments);
        
      break;
      
      case 'dashes':
      
        return piklist::dashes($arguments);
        
      break;
      
      case 'slug':
      
        return piklist::slug($arguments);
        
      break;
      
      case 'performance':
        
        piklist::performance();
        
      break;
      
      default:
        
        return piklist::render($option, $arguments, isset($arguments['return']) ? $arguments['return'] : false); 
        
      break;
    }
  }
}

?>