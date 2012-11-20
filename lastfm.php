<?php

/**
 * Plugin Name: LastFM Top Artists
 * Plugin URI: http://wordpress.org/extend/plugins/lastfm-top-artists/
 * Description: Displays the top LastFM artists for a particular user.
 * Version: 0.5.0
 * Author: alairock
 * Author URI: http://sixteenink.com
 * License: GPL2
 **/

class LastFMTopArtistsWidget extends WP_Widget {

	public function LastFMTopArtistsWidget() {
		$widget_ops = array('classname' => 'LastFMTopArtistsWidget', 'description' => 'Displays the top LastFM artists for a particular user.' );
		$this->WP_Widget('LastFMTopArtistsWidget', 'Top LastFM Artists', $widget_ops);
	}

	public function form($instance) {
		$instance = wp_parse_args( (array) $instance, array( 'username' => '', 'latestx' => '5', 'typeof' => 'artists', 'timeframe' => 'overall', 'author' => 'yes' ) );
		$username = $instance['username'];
		$latestx = $instance['latestx'];
		$typeof = $instance['typeof'];
		$timeframe = $instance['timeframe'];
		$author = $instance['author'];
		?>
		<p><label for="<?php echo $this->get_field_id('username'); ?>">Username: <input class="widefat" id="<?php echo $this->get_field_id('username'); ?>" name="<?php echo $this->get_field_name('username'); ?>" type="text" value="<?php echo attribute_escape($username); ?>" /></label></p>
		<p><label for="<?php echo $this->get_field_id('latestx'); ?>">Number of Artists to show: <input class="widefat" id="<?php echo $this->get_field_id('latestx'); ?>" name="<?php echo $this->get_field_name('latestx'); ?>" type="text" value="<?php echo attribute_escape($latestx); ?>" /></label></p>
		<p><label>Top Artists/Tracks/Albums</label><select class="widefat" id="<?php echo $this->get_field_id('typeof'); ?>" name="<?php echo $this->get_field_name('typeof'); ?>">
			<option value="<?php echo attribute_escape($typeof); ?>"><?php echo attribute_escape($typeof); ?></option>
			<option value="track">Top Tracks</option>
			<option value="artist">Top Artists</option>
			<option value="album">Top Albums</option>
		</select>
		</p>

		<p><label>Timeframe:</label><select class="widefat" id="<?php echo $this->get_field_id('timeframe'); ?>" name="<?php echo $this->get_field_name('timeframe'); ?>">
			<option value="<?php echo attribute_escape($timeframe); ?>"><?php echo attribute_escape($timeframe); ?></option>
			<option value="overall">overall</option>
			<option value="12month">Last 12 Months</option>
			<option value="6month">Last 6 Months</option>
			<option value="3month">Last 3 Months</option>
			<option value="7day">Last 7 Days</option>
		</select>
		</p>
		<p><label for="<?php echo $this->get_field_id('author'); ?>">Display Widget Author: (yes/no)<input class="widefat" id="<?php echo $this->get_field_id('author'); ?>" name="<?php echo $this->get_field_name('author'); ?>" type="text" value="<?php echo attribute_escape($author); ?>" /></label></p>

	<?php }

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['username'] = $new_instance['username'];
		$instance['latestx'] = $new_instance['latestx'];
		$instance['typeof'] = $new_instance['typeof'];
		$instance['timeframe'] = $new_instance['timeframe'];
		$instance['author'] = $new_instance['author'];
		return $instance;
	}

	function widget($args, $instance) {
		extract($args, EXTR_SKIP);
		$username = empty($instance['username']) ? ' ' : apply_filters('widget_username', $instance['username']);
		$latestx = empty($instance['latestx']) ? ' ' : apply_filters('widget_latestx', $instance['latestx']);
		$typeof = empty($instance['typeof']) ? ' ' : apply_filters('widget_typeof', $instance['typeof']);
		$timeframe = empty($instance['timeframe']) ? ' ' : apply_filters('widget_timeframe', $instance['timeframe']);
		$author = empty($instance['author']) ? ' ' : apply_filters('widget_author', $instance['author']);
		$key = '1f3af5debd5cc5c3d3c5ea0019385474';
		echo $before_widget;

		$fromCache = json_decode(file_get_contents('lastfmcache.txt'), true);
		$lfm = $fromCache['data'];
		if ($fromCache['date'] <= time() - 10800 || $fromCache['type'] != $typeof || $fromCache['latestx'] != $latestx || $fromCache['username'] != $username || $fromCache['timeframe'] != $timeframe) {
			$lfm = json_decode(file_get_contents('http://ws.audioscrobbler.com/2.0/?method=user.gettop' . $typeof . 's&user=' . $username . '&api_key=' . $key . '&format=json&limit=' . $latestx . '&period=' . $timeframe . ''), true);
			$saveThis = json_encode(array('date' => time(),'type' => $typeof, 'username' => $username, 'timeframe' => $timeframe, 'latestx' => $latestx, 'data' => $lfm));
			file_put_contents('lastfmcache.txt', $saveThis);
		}
		echo "<h1>Top " . $latestx . " " . $typeof . "s</h1>";


		//Artists/Tracks/Albums
		$lfmA = $lfm['top' . $typeof . 's'][$typeof];
		foreach ($lfmA as $key => $value) {
			echo '<img src="' . $value['image']['0']['#text'] . '"> <a href="' . $value['url'] . '">' . $value['name'] . '</a> [' . $value['playcount'] . ']<br>';
		}

		if ($author == 'no') {
			$display = 'style="display: none"';
		} else {
			$display = '';
		}
		echo '<span ' . $display . '> Widget by <a href="http://sixteenink.com">alairock</a></span>';

		echo $after_widget;
	}

}

//add widget
add_action( 'widgets_init', create_function('', 'return register_widget("LastFMTopArtistsWidget");') );?>