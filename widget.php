<?php

// widget function
	class tp_widget_recent_tweets extends WP_Widget {
		
		public function __construct() {
			parent::__construct(
				'tp_widget_recent_tweets', // Base ID
				'* Recent Tweets', // Name
				array( 'description' => __( 'Display recent tweets', 'ingrid' ), ) // Args
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
							echo '<strong>Please fill all widget settings!</strong>' . $after_widget;
							return;
						}
					
										
					//check if cache needs update
						$tp_twitter_plugin_last_cache_time = get_option('tp_twitter_plugin_last_cache_time');
						$diff = time() - $tp_twitter_plugin_last_cache_time;
						$crt = $instance['cachetime'] * 3600;
						
					 //	yes, it needs update			
						if($diff >= $crt || empty($tp_twitter_plugin_last_cache_time)){
							
							if(!require_once('twitteroauth.php')){ 
								echo '<strong>Couldn\'t find twitteroauth.php!</strong>' . $after_widget;
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
									echo '<strong>'.$tweets->errors[0]->message.'!</strong><br />You\'ll need to regenerate it <a href="https://dev.twitter.com/apps" target="_blank">here</a>!' . $after_widget;
								}else{
									echo '<strong>'.$tweets->errors[0]->message.'</strong>' . $after_widget;
								}
								return;
							}
							
							for($i = 0;$i <= count($tweets); $i++){
								if(!empty($tweets[$i])){
									$tweets_array[$i]['created_at'] = $tweets[$i]->created_at;
									$tweets_array[$i]['text'] = $tweets[$i]->text;			
									$tweets_array[$i]['status_id'] = $tweets[$i]->id_str;			
								}	
							}							
							
							//save tweets to wp option 		
								update_option('tp_twitter_plugin_tweets',serialize($tweets_array));							
								update_option('tp_twitter_plugin_last_cache_time',time());
								
							echo '<!-- twitter cache has been updated! -->';
						}
						
						
												
					
					$tp_twitter_plugin_tweets = maybe_unserialize(get_option('tp_twitter_plugin_tweets'));
					if(!empty($tp_twitter_plugin_tweets)){
						print '
						<div class="tp_recent_tweets">
							<ul>';
							$fctr = '1';
							foreach($tp_twitter_plugin_tweets as $tweet){								
								print '<li><span>'.tp_convert_links($tweet['text']).'</span><br /><a class="twitter_time" target="_blank" href="http://twitter.com/'.$instance['username'].'/statuses/'.$tweet['status_id'].'">'.tp_relative_time($tweet['created_at']).'</a></li>';
								if($fctr == $instance['tweetstoshow']){ break; }
								$fctr++;
							}
						
						print '
							</ul>
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
				<p><label>Title:</label>
					<input type="text" name="'.$this->get_field_name( 'title' ).'" id="'.$this->get_field_id( 'title' ).'" value="'.esc_attr($instance['title']).'" class="widefat" /></p>
				<p><label>Consumer Key:</label>
					<input type="text" name="'.$this->get_field_name( 'consumerkey' ).'" id="'.$this->get_field_id( 'consumerkey' ).'" value="'.esc_attr($instance['consumerkey']).'" class="widefat" /></p>
				<p><label>Consumer Secret:</label>
					<input type="text" name="'.$this->get_field_name( 'consumersecret' ).'" id="'.$this->get_field_id( 'consumersecret' ).'" value="'.esc_attr($instance['consumersecret']).'" class="widefat" /></p>					
				<p><label>Access Token:</label>
					<input type="text" name="'.$this->get_field_name( 'accesstoken' ).'" id="'.$this->get_field_id( 'accesstoken' ).'" value="'.esc_attr($instance['accesstoken']).'" class="widefat" /></p>									
				<p><label>Access Token Secret:</label>		
					<input type="text" name="'.$this->get_field_name( 'accesstokensecret' ).'" id="'.$this->get_field_id( 'accesstokensecret' ).'" value="'.esc_attr($instance['accesstokensecret']).'" class="widefat" /></p>														
				<p><label>Cache Tweets in every:</label>
					<input type="text" name="'.$this->get_field_name( 'cachetime' ).'" id="'.$this->get_field_id( 'cachetime' ).'" value="'.esc_attr($instance['cachetime']).'" class="small-text" /> hours</p>																			
				<p><label>Twitter Username:</label>
					<input type="text" name="'.$this->get_field_name( 'username' ).'" id="'.$this->get_field_id( 'username' ).'" value="'.esc_attr($instance['username']).'" class="widefat" /></p>																			
				<p><label>Tweets to display:</label>
					<select type="text" name="'.$this->get_field_name( 'tweetstoshow' ).'" id="'.$this->get_field_id( 'tweetstoshow' ).'">';
					$i = 1;
					for(i; $i <= 10; $i++){
						echo '<option value="'.$i.'"'; if($instance['tweetstoshow'] == $i){ echo ' selected="selected"'; } echo '>'.$i.'</option>';						
					}
					echo '
					</select></p>
				<p><label>Exclude replies:</label>
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
								$status = preg_replace("/((http:\/\/|https:\/\/)[^ )
]+)/e", "'<a href=\"$1\" title=\"$1\" $target >'. ((strlen('$1')>=$linkMaxLen ? substr('$1',0,$linkMaxLen).'...':'$1')).'</a>'", $status);
							 
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
							$b = strtotime("now"); 
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
								if($d < 3) return "right now";
								//if less then minute
								if($d < $minute) return floor($d) . " seconds ago";
								//if less then 2 minutes
								if($d < $minute * 2) return "about 1 minute ago";
								//if less then hour
								if($d < $hour) return floor($d / $minute) . " minutes ago";
								//if less then 2 hours
								if($d < $hour * 2) return "about 1 hour ago";
								//if less then day
								if($d < $day) return floor($d / $hour) . " hours ago";
								//if more then day, but less then 2 days
								if($d > $day && $d < $day * 2) return "yesterday";
								//if less then year
								if($d < $day * 365) return floor($d / $day) . " days ago";
								//else return more than a year
								return "over a year ago";
							}
						}	
					}	
	
	
	
// register	widget
	function register_tp_twitter_widget(){
		register_widget('tp_widget_recent_tweets');
	}
	add_action('init', 'register_tp_twitter_widget', 1)
	
?>