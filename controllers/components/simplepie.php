<?php
/*
 * SimplePie CakePHP Component
 * Copyright (c) 2008 Matt Curry
 * www.PseudoCoder.com
 * http://github.com/mcurry/cakephp/tree/master/components/simplepie
 * http://sandbox2.pseudocoder.com/demo/simplepie
 *
 * Based on the work of Scott Sansoni (http://cakeforge.org/snippet/detail.php?type=snippet&id=53)
 *
 * @author      Matt Curry <matt@pseudocoder.com>
 * @license     MIT
 *
 */

class SimplepieComponent extends Object {
  var $cache;

  function __construct() {
    $this->cache = CACHE . 'rss' . DS;
  }

  function feed($feed_url, $options=array()) {
		$options = array_merge(array('start' => 0, 'length' => 10, 'cache' => true, 'fields' => array('title', 'permalink')), $options);
		
		if($options['cache']) {
			$items = Cache::read(md5($feed_url));
			if ($items !== false) {
				return $items;
			}
		}
    
    //make the cache dir if it doesn't exist
    if (!file_exists($this->cache)) {
      $folder = new Folder($this->cache, true);
    }

    //include the vendor class
    App::import('vendor', 'simplepie');

    //setup SimplePie
    $feed = new SimplePie();
    $feed->set_feed_url($feed_url);
    $feed->set_cache_location($this->cache);

    //retrieve the feed
    $feed->init();

    //get the feed items
    $items = $feed->get_items($options['start'], $options['length']);
		
		if($options['cache']) {
			$cache = array();
			foreach($items as $item) {
				$holder = array();
				foreach($options['fields'] as $field) {
					$holder[$field] = $item->{"get_$field"}();
				}
				$cache[] = $holder;
			}
			
			Cache::write(md5($feed_url), $cache);
			return $cache;
		}

    //return
    if ($items) {
      return $items;
    } else {
      return false;
    }
  }
}
?>
