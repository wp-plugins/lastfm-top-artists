<?php

/**
 * Plugin Name: LastFM Top 5 
 * Plugin URI: http://github.com/alairock/LastFM-Top-Artists-Plugin-for-WordPress
 * Description: Displays the top 5 LastFM artists for a particular user.
 * Version: 0.1
 * Author: alairock
 * Author URI: http://sixteenink.com
 * License: GPL2
 **/

class LastFMTop50Widget extends WP_Widget {
    function LastFMTop50Widget() {
        $widget_ops = array('classname' => 'LastFMTop50Widget', 'description' => 'Displays top X number of artists' );
        $this->WP_Widget('LastFMTop50Widget', 'Top LastFM Artists', $widget_ops);
    }
    function form($instance) {
    $instance = wp_parse_args( (array) $instance, array( 'username' => '', 'latestx' => '5' ) );
    $username = $instance['username'];
    $latestx = $instance['latestx'];
?>
        <p><label for="<?php echo $this->get_field_id('username'); ?>">Username: <input class="widefat" id="<?php echo $this->get_field_id('username'); ?>" name="<?php echo $this->get_field_name('username'); ?>" type="text" value="<?php echo attribute_escape($username); ?>" /></label></p>
        <p><label for="<?php echo $this->get_field_id('latestx'); ?>">Number of Artists to show: <input class="widefat" id="<?php echo $this->get_field_id('latestx'); ?>" name="<?php echo $this->get_field_name('latestx'); ?>" type="text" value="<?php echo attribute_escape($latestx); ?>" /></label></p>



<?php }

    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['username'] = $new_instance['username'];
        $instance['latestx'] = $new_instance['latestx'];
        return $instance;
    }   
    
    function widget($args, $instance) {
        extract($args, EXTR_SKIP);
        echo $before_widget;
        $username = empty($instance['username']) ? ' ' : apply_filters('widget_username', $instance['username']);
        $latestx = empty($instance['latestx']) ? ' ' : apply_filters('widget_latestx', $instance['latestx']);

                            
        echo "<h1>Top " . $latestx . " Artists</h1>";
        
$lfm = file_get_contents('http://ws.audioscrobbler.com/2.0/user/' . $username . '/topartists.xml?period=12month&limit=' . $latestx);

$lfmA = $this->xml2ary($lfm);
$lfmA = $lfmA['topartists']['_c']['artist'];
foreach($lfmA as $lfmV) { ?>
    <img height="23px" width="34px" src="<?php echo $lfmV['_c']['image']['0']['_v']; ?>"> <a href="<?php echo $lfmV['_c']['url']['_v']; ?>"><?php echo $lfmV['_c']['name']['_v']; ?> </a> [<?php echo $lfmV['_c']['playcount']['_v']; ?>]<br>
<?php } 

        echo $after_widget;
            }

// XML to Array
public function xml2ary(&$string) {
    $parser = xml_parser_create();
    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
    xml_parse_into_struct($parser, $string, $vals, $index);
    xml_parser_free($parser);

    $mnary=array();
    $ary=&$mnary;
    foreach ($vals as $r) {
        $t=$r['tag'];
        if ($r['type']=='open') {
            if (isset($ary[$t])) {
                if (isset($ary[$t][0])) $ary[$t][]=array(); else $ary[$t]=array($ary[$t], array());
                $cv=&$ary[$t][count($ary[$t])-1];
            } else $cv=&$ary[$t];
            if (isset($r['attributes'])) {foreach ($r['attributes'] as $k=>$v) $cv['_a'][$k]=$v;}
            $cv['_c']=array();
            $cv['_c']['_p']=&$ary;
            $ary=&$cv['_c'];

        } elseif ($r['type']=='complete') {
            if (isset($ary[$t])) { // same as open
                if (isset($ary[$t][0])) $ary[$t][]=array(); else $ary[$t]=array($ary[$t], array());
                $cv=&$ary[$t][count($ary[$t])-1];
            } else $cv=&$ary[$t];
            if (isset($r['attributes'])) {foreach ($r['attributes'] as $k=>$v) $cv['_a'][$k]=$v;}
            $cv['_v']=(isset($r['value']) ? $r['value'] : '');

        } elseif ($r['type']=='close') {
            $ary=&$ary['_p'];
        }
    }    
    
    $this->_del_p($mnary);
    return $mnary;
}

// _Internal: Remove recursion in result array
public function _del_p(&$ary) {
    foreach ($ary as $k=>$v) {
        if ($k==='_p') unset($ary[$k]);
        elseif (is_array($ary[$k])) $this->_del_p($ary[$k]);
    }
}

// Array to XML
public function ary2xml($cary, $d=0, $forcetag='') {
    $res=array();
    foreach ($cary as $tag=>$r) {
        if (isset($r[0])) {
            $res[]=ary2xml($r, $d, $tag);
        } else {
            if ($forcetag) $tag=$forcetag;
            $sp=str_repeat("\t", $d);
            $res[]="$sp<$tag";
            if (isset($r['_a'])) {foreach ($r['_a'] as $at=>$av) $res[]=" $at=\"$av\"";}
            $res[]=">".((isset($r['_c'])) ? "\n" : '');
            if (isset($r['_c'])) $res[]=ary2xml($r['_c'], $d+1);
            elseif (isset($r['_v'])) $res[]=$r['_v'];
            $res[]=(isset($r['_c']) ? $sp : '')."</$tag>\n";
        }
        
    }
    return implode('', $res);
}

// Insert element into array
public function ins2ary(&$ary, $element, $pos) {
    $ar1=array_slice($ary, 0, $pos); $ar1[]=$element;
    $ary=array_merge($ar1, array_slice($ary, $pos));
}

}

//add widget
add_action( 'widgets_init', create_function('', 'return register_widget("LastFMTop50Widget");') );?>