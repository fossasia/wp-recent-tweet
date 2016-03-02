<?php

// widget function
	class tp_widget_recent_tweets extends WP_Widget {
		
		public function __construct() {
			parent::__construct(
				'tp_widget_recent_tweets', // Base ID
				'* Recent Tweets', // Name
				array( 'description' => __( 'Display recent tweets', 'tp_tweets' ), ) // Args
			);
		}

		
		//widget output
			public function widget($args, $instance) {
				extract($args);
				if(!empty($instance['title'])){ $title = apply_filters( 'widget_title', $instance['title'] ); }
				
				echo $before_widget;				
				if ( ! empty( $title ) ){ echo $before_title . $title . $after_title; }

				
					//check settings and die if not set
						if(empty($instance['consumerkey']) || empty($instance['consumersecret']) || empty($instance['accesstoken']) || empty($instance['accesstokensecret']) || empty($instance['cachetime']) || empty($instance['username'])){
							echo '<strong>'.__('Please fill all widget settings!','tp_tweets').'</strong>' . $after_widget;
							return;
						}
										
					//check if cache needs update
						$tp_twitter_plugin_last_cache_time = get_option('tp_twitter_plugin_last_cache_time');
						$diff = time() - $tp_twitter_plugin_last_cache_time;
						$crt = $instance['cachetime'] * 3600;
						
					 //	yes, it needs update			
						if($diff >= $crt || empty($tp_twitter_plugin_last_cache_time)){
							
							if(!require_once('twitteroauth.php')){ 
								echo '<strong>'.__('Couldn\'t find twitteroauth.php!','tp_tweets').'</strong>' . $after_widget;
								return;
							}
														
							function getConnectionWithAccessToken($cons_key, $cons_secret, $oauth_token, $oauth_token_secret) {
							  $connection = new TwitterOAuth($cons_key, $cons_secret, $oauth_token, $oauth_token_secret);
							  return $connection;
							}
							  
							  							  
							$connection = getConnectionWithAccessToken($instance['consumerkey'], $instance['consumersecret'], $instance['accesstoken'], $instance['accesstokensecret']);
							$tweets = $connection->get("https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=".$instance['username']."&count=10&exclude_replies=".$instance['excludereplies']) or die('Couldn\'t retrieve tweets! Wrong username?');
							
														
							if(!empty($tweets->errors)){
								if($tweets->errors[0]->message == 'Invalid or expired token'){
									echo '<strong>'.$tweets->errors[0]->message.'!</strong><br />' . __('You\'ll need to regenerate it <a href="https://apps.twitter.com/" target="_blank">here</a>!','tp_tweets') . $after_widget;
								}else{
									echo '<strong>'.$tweets->errors[0]->message.'</strong>' . $after_widget;
								}
								return;
							}
							
							$tweets_array = array();
							for($i = 0;$i <= count($tweets); $i++){
								if(!empty($tweets[$i])){
									$tweets_array[$i]['created_at'] = $tweets[$i]->created_at;
									
										//clean tweet text
										$tweets_array[$i]['text'] = preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', $tweets[$i]->text);
									
									if(!empty($tweets[$i]->id_str)){
										$tweets_array[$i]['status_id'] = $tweets[$i]->id_str;			
									}
								}	
							}							
							
							//save tweets to wp option 		
								update_option('tp_twitter_plugin_tweets',serialize($tweets_array));							
								update_option('tp_twitter_plugin_last_cache_time',time());
								
							echo '<!-- twitter cache has been updated! -->';
						}
						
						
												
					$tp_twitter_plugin_tweets = maybe_unserialize(get_option('tp_twitter_plugin_tweets'));
					if(!empty($tp_twitter_plugin_tweets) && is_array($tp_twitter_plugin_tweets)){
						print '
						<div class="tp_recent_tweets">
							<ul>';
							$fctr = '1';
							foreach($tp_twitter_plugin_tweets as $tweet){					
								if(!empty($tweet['text'])){
									if(empty($tweet['status_id'])){ $tweet['status_id'] = ''; }
									if(empty($tweet['created_at'])){ $tweet['created_at'] = ''; }
								
									print '<li><span>'.tp_convert_links($tweet['text']).'</span><a class="twitter_time" target="_blank" href="http://twitter.com/'.$instance['username'].'/statuses/'.$tweet['status_id'].'">'.tp_relative_time($tweet['created_at']).'</a></li>';
									if($fctr == $instance['tweetstoshow']){ break; }
									$fctr++;
								}
							}
						
						print '
							</ul>';

							// If we're being supported display the link
							$tp_twitter_plugin_options = get_option('tp_twitter_plugin_options');

							if ($tp_twitter_plugin_options['support-us'] == 1) {
								print '<p><i>Check out the <a href="https://wordpress.org/plugins/sumome/" target="_blank">SumoMe</a> plugin</i></p>';
							}
						print '</div>';
					}else{
						print '
						<div class="tp_recent_tweets">
							' . __('<b>Error!</b> Couldn\'t retrieve tweets for some reason!','tp_tweets') . '
						</div>';
					}
				
				
				
				echo $after_widget;
			}
			
		
		//save widget settings 
			public function update($new_instance, $old_instance) {				
				$instance = array();
				$instance['title'] = strip_tags( $new_instance['title'] );
				$instance['consumerkey'] = strip_tags( $new_instance['consumerkey'] );
				$instance['consumersecret'] = strip_tags( $new_instance['consumersecret'] );
				$instance['accesstoken'] = strip_tags( $new_instance['accesstoken'] );
				$instance['accesstokensecret'] = strip_tags( $new_instance['accesstokensecret'] );
				$instance['cachetime'] = strip_tags( $new_instance['cachetime'] );
				$instance['username'] = strip_tags( $new_instance['username'] );
				$instance['tweetstoshow'] = strip_tags( $new_instance['tweetstoshow'] );
				$instance['excludereplies'] = strip_tags( $new_instance['excludereplies'] );

				if($old_instance['username'] != $new_instance['username']){
					delete_option('tp_twitter_plugin_last_cache_time');
				}
				
				return $instance;
			}
			
			
		//widget settings form	
			public function form($instance) {
				$defaults = array( 'title' => '', 'consumerkey' => '', 'consumersecret' => '', 'accesstoken' => '', 'accesstokensecret' => '', 'cachetime' => '', 'username' => '', 'tweetstoshow' => '' );
				$instance = wp_parse_args( (array) $instance, $defaults );
						
				echo '
				<p>Get your API keys &amp; tokens at:<br /><a href="https://apps.twitter.com/" target="_blank">https://apps.twitter.com/</a></p>
				<p><i>Check out our <a href="https://wordpress.org/plugins/sumome/" target="_blank">SumoMe</a> plugin</i></p>
				<p><label>' . __('Title:','tp_tweets') . '</label>
					<input type="text" name="'.$this->get_field_name( 'title' ).'" id="'.$this->get_field_id( 'title' ).'" value="'.esc_attr($instance['title']).'" class="widefat" /></p>
				<p><label>' . __('Consumer Key:','tp_tweets') . '</label>
					<input type="text" name="'.$this->get_field_name( 'consumerkey' ).'" id="'.$this->get_field_id( 'consumerkey' ).'" value="'.esc_attr($instance['consumerkey']).'" class="widefat" /></p>
				<p><label>' . __('Consumer Secret:','tp_tweets') . '</label>
					<input type="text" name="'.$this->get_field_name( 'consumersecret' ).'" id="'.$this->get_field_id( 'consumersecret' ).'" value="'.esc_attr($instance['consumersecret']).'" class="widefat" /></p>					
				<p><label>' . __('Access Token:','tp_tweets') . '</label>
					<input type="text" name="'.$this->get_field_name( 'accesstoken' ).'" id="'.$this->get_field_id( 'accesstoken' ).'" value="'.esc_attr($instance['accesstoken']).'" class="widefat" /></p>									
				<p><label>' . __('Access Token Secret:','tp_tweets') . '</label>		
					<input type="text" name="'.$this->get_field_name( 'accesstokensecret' ).'" id="'.$this->get_field_id( 'accesstokensecret' ).'" value="'.esc_attr($instance['accesstokensecret']).'" class="widefat" /></p>														
				<p><label>' . __('Cache Tweets in every:','tp_tweets') . '</label>
					<input type="text" name="'.$this->get_field_name( 'cachetime' ).'" id="'.$this->get_field_id( 'cachetime' ).'" value="'.esc_attr($instance['cachetime']).'" class="small-text" /> hours</p>																			
				<p><label>' . __('Twitter Username:','tp_tweets') . '</label>
					<input type="text" name="'.$this->get_field_name( 'username' ).'" id="'.$this->get_field_id( 'username' ).'" value="'.esc_attr($instance['username']).'" class="widefat" /></p>																			
				<p><label>' . __('Tweets to display:','tp_tweets') . '</label>
					<select type="text" name="'.$this->get_field_name( 'tweetstoshow' ).'" id="'.$this->get_field_id( 'tweetstoshow' ).'">';
					$i = 1;
					for($i; $i <= 10; $i++){
						echo '<option value="'.$i.'"'; if($instance['tweetstoshow'] == $i){ echo ' selected="selected"'; } echo '>'.$i.'</option>';						
					}
					echo '
					</select></p>
				<p><label>' . __('Exclude replies:','tp_tweets') . '</label>
					<input type="checkbox" name="'.$this->get_field_name( 'excludereplies' ).'" id="'.$this->get_field_id( 'excludereplies' ).'" value="true"'; 
					if(!empty($instance['excludereplies']) && esc_attr($instance['excludereplies']) == 'true'){
						print ' checked="checked"';
					}					
					print ' /></p>';		
			}
	}
	
	
	

										
					//convert links to clickable format
					if (!function_exists('tp_convert_links')) {
						function tp_convert_links($status,$targetBlank=true,$linkMaxLen=250){
						 
							// the target
								$target=$targetBlank ? " target=\"_blank\" " : "";
							 
							// convert link to url								
								$status = preg_replace('/\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[A-Z0-9+&@#\/%=~_|]/i', '<a href="\0" target="_blank">\0</a>', $status);
							 
							// convert @ to follow
								$status = preg_replace("/(@([_a-z0-9\-]+))/i","<a href=\"http://twitter.com/$2\" title=\"Follow $2\" $target >$1</a>",$status);
							 
							// convert # to search
								$status = preg_replace("/(#([_a-z0-9\-]+))/i","<a href=\"https://twitter.com/search?q=$2\" title=\"Search $1\" $target >$1</a>",$status);
							 
							// return the status
								return $status;
						}
					}
					
					
					//convert dates to readable format	
					if (!function_exists('tp_relative_time')) {
						function tp_relative_time($a) {
							//get current timestampt
							$b = strtotime('now'); 
							//get timestamp when tweet created
							$c = strtotime($a);
							//get difference
							$d = $b - $c;
							//calculate different time values
							$minute = 60;
							$hour = $minute * 60;
							$day = $hour * 24;
							$week = $day * 7;
								
							if(is_numeric($d) && $d > 0) {
								//if less then 3 seconds
								if($d < 3) return __('right now','tp_tweets');
								//if less then minute
								if($d < $minute) return floor($d) . __(' seconds ago','tp_tweets');
								//if less then 2 minutes
								if($d < $minute * 2) return __('about 1 minute ago','tp_tweets');
								//if less then hour
								if($d < $hour) return floor($d / $minute) . __(' minutes ago','tp_tweets');
								//if less then 2 hours
								if($d < $hour * 2) return __('about 1 hour ago','tp_tweets');
								//if less then day
								if($d < $day) return floor($d / $hour) . __(' hours ago','tp_tweets');
								//if more then day, but less then 2 days
								if($d > $day && $d < $day * 2) return __('yesterday','tp_tweets');
								//if less then year
								if($d < $day * 365) return floor($d / $day) . __(' days ago','tp_tweets');
								//else return more than a year
								return __('over a year ago','tp_tweets');
							}
						}	
					}	
	
	
	
// register	widget
	function register_tp_twitter_widget(){
		register_widget('tp_widget_recent_tweets');
	}
	add_action('widgets_init', 'register_tp_twitter_widget', 1)
	
?>