<?php

// widget function
	class fa_widget_recent_tweet extends WP_Widget {
		
		public function __construct() {
			parent::__construct(
				'fa_widget_recent_tweet', // Base ID
				'* Recent Tweet', // Name
				array( 'description' => __( 'Display recent tweets', 'fa_tweets' ), ) // Args
			);
		}

		
		//widget output
			public function widget($args, $instance) {
				extract($args);
				if(!empty($instance['title'])){ $title = apply_filters( 'widget_title', $instance['title'] ); }
				
				echo $before_widget;				
				if ( ! empty( $title ) ){ echo $before_title . $title . $after_title; }

				
					//check settings and die if not set
						if((empty($instance['consumerkey']) || empty($instance['consumersecret']) || empty($instance['accesstoken']) || empty($instance['accesstokensecret']) || empty($instance['username'])) && empty($instance['loklak_api'])){
							echo '<strong>'.__('Please fill all widget settings!','fa_tweets').'</strong>' . $after_widget;
							return;
						}

						if( !empty($instance['loklak_api']) && ( esc_attr($instance['loklak_api']  == 'true'))){
							if(!class_exists('Loklak'))
								require_once dirname(__FILE__).'/loklak_php_api/loklak.php';
                			$loklak = new Loklak();                			
						}

										
					//check if cache needs update
						$fa_twitter_plugin_last_cache_time = get_option('fa_twitter_plugin_last_cache_time');
						$diff = time() - $fa_twitter_plugin_last_cache_time;
						$crt = $instance['cachetime'] * 3600;
						
					 //	yes, it needs update			
						if($diff >= $crt || empty($fa_twitter_plugin_last_cache_time)){
							
							if( isset($loklak)){
					            $screen_name = explode('@', $instance['username'])[1];
					            $tweets = $loklak->search('', null, null, $screen_name, 10);					            
					            $tweets = json_decode($tweets, true);
					            if(!($tweets = json_decode($tweets['body'], false))){
					            	echo '<strong>'.__('Couldn\'t retrieve tweets from Loklak.org','fa_tweets').'</strong>' . $after_widget;
					            	return;
					            }

					            $tweets = $tweets->statuses;
					        }
							else{
								if(!require_once('twitteroauth.php')){ 
									echo '<strong>'.__('Couldn\'t find twitteroauth.php!','fa_tweets').'</strong>' . $after_widget;
									return;
								}
															
								function getConnectionWithAccessToken($cons_key, $cons_secret, $oauth_token, $oauth_token_secret) {
								  $connection = new TwitterOAuth($cons_key, $cons_secret, $oauth_token, $oauth_token_secret);
								  return $connection;
								}
																  							  
								$connection = getConnectionWithAccessToken($instance['consumerkey'], $instance['consumersecret'], $instance['accesstoken'], $instance['accesstokensecret']);
								$tweets = $connection->get("https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=".$instance['username']."&count=10&exclude_replies=".$instance['excludereplies']) or die('Couldn\'t retrieve tweets! Wrong username?');
							}					
							if(!empty($tweets->errors)){
								if($tweets->errors[0]->message == 'Invalid or expired token'){
									echo '<strong>'.$tweets->errors[0]->message.'!</strong><br />' . __('You\'ll need to regenerate it <a href="https://apps.twitter.com/" target="_blank">here</a>!','fa_tweets') . $after_widget;
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
								update_option('fa_twitter_plugin_tweets',serialize($tweets_array));							
								update_option('fa_twitter_plugin_last_cache_time',time());
								
							echo '<!-- twitter cache has been updated! -->';
						}
						
						
												
					$fa_twitter_plugin_tweets = maybe_unserialize(get_option('fa_twitter_plugin_tweets'));
					if(!empty($fa_twitter_plugin_tweets) && is_array($fa_twitter_plugin_tweets)){
						print '
						<div class="fa_recent_tweets">
							<ul>';
							$fctr = '1';
							foreach($fa_twitter_plugin_tweets as $tweet){					
								if(!empty($tweet['text'])){
									if(empty($tweet['status_id'])){ $tweet['status_id'] = ''; }
									if(empty($tweet['created_at'])){ $tweet['created_at'] = ''; }
								
									print '<li><span>'.fa_convert_links($tweet['text']).'</span><a class="twitter_time" target="_blank" href="http://twitter.com/'.$instance['username'].'/statuses/'.$tweet['status_id'].'">'.fa_relative_time($tweet['created_at']).'</a></li>';
									if($fctr == $instance['tweetstoshow']){ break; }
									$fctr++;
								}
							}
						
						print '
							</ul> 
						</div>';						
					}else{
						print '
						<div class="fa_recent_tweets">
							' . __('<b>Error!</b> Couldn\'t retrieve tweets for some reason!','tp_tweets') . '
						</div>';
					}
				
				
				
				echo $after_widget;
			}
			
		
		//save widget settings 
			public function update($new_instance, $old_instance) {				
				$instance = array();
				//var_dump($new_instance);
				//die();
				$instance['title'] = strip_tags( $new_instance['title'] );
				$instance['consumerkey'] = strip_tags( $new_instance['consumerkey'] );
				$instance['consumersecret'] = strip_tags( $new_instance['consumersecret'] );
				$instance['accesstoken'] = strip_tags( $new_instance['accesstoken'] );
				$instance['accesstokensecret'] = strip_tags( $new_instance['accesstokensecret'] );
				$instance['cachetime'] = strip_tags( $new_instance['cachetime'] );
				$instance['username'] = strip_tags( $new_instance['username'] );
				$instance['tweetstoshow'] = strip_tags( $new_instance['tweetstoshow'] );
				$instance['excludereplies'] = strip_tags( $new_instance['excludereplies'] );
				$instance['loklak_api'] = strip_tags(  $new_instance['loklak_api'] );

				if($old_instance['username'] != $new_instance['username']){
					delete_option('fa_twitter_plugin_last_cache_time');
				}
				
				return $instance;
			}
			
			
		//widget settings form	
			public function form($instance) {
				$defaults = array( 'title' => '', 'consumerkey' => '', 'consumersecret' => '', 'accesstoken' => '', 'accesstokensecret' => '', 'cachetime' => '', 'username' => '', 'tweetstoshow' => '' );
				$instance = wp_parse_args( (array) $instance, $defaults );
						
				echo '
				<p>Get your API keys &amp; tokens at:<br /><a href="https://apps.twitter.com/" target="_blank">https://apps.twitter.com/</a></p>';
				echo '
				<p>
					<input type="checkbox" name="'.$this->get_field_name( 'loklak_api' ).'" id="'.$this->get_field_id( 'loklak_api' ).'" value="true" class="rtw-loklak_api"'; 
					if(!empty($instance['loklak_api']) && esc_attr($instance['loklak_api']) == 'true'){
						print ' checked="checked"';
					}					
					print ' /><label>' . __('Use anonymous <a href="http://loklak.org/">loklak.org</a> API instead of Twitter. <a href="http://loklak.org/">Find out more</a>','fa_tweets') . '</label></p>';
				echo '
				<p><label>' . __('Title:','fa_tweets') . '</label>
					<input type="text" name="'.$this->get_field_name( 'title' ).'" id="'.$this->get_field_id( 'title' ).'" value="'.esc_attr($instance['title']).'" class="widefat rtw-title" /></p>
				<p><label>' . __('Consumer Key:','fa_tweets') . '</label>
					<input type="text" name="'.$this->get_field_name( 'consumerkey' ).'" id="'.$this->get_field_id( 'consumerkey' ).'" value="'.esc_attr($instance['consumerkey']).'" class="widefat rtw-consumerkey"';
					if(!empty($instance['loklak_api']) && esc_attr($instance['loklak_api']) == 'true'){
						print ' disabled="disabled"';
					}				
					print ' /></p>';
				echo '
				<p><label>' . __('Consumer Secret:','fa_tweets') . '</label>
					<input type="text" name="'.$this->get_field_name( 'consumersecret' ).'" id="'.$this->get_field_id( 'consumersecret' ).' " value="'.esc_attr($instance['consumersecret']).'" class="widefat rtw-consumersecret"';
					if(!empty($instance['loklak_api']) && esc_attr($instance['loklak_api']) == 'true'){
						print ' disabled="disabled"';
					}				
					print ' /></p>';					
				echo '
				<p><label>' . __('Access Token:','fa_tweets') . '</label>
					<input type="text" name="'.$this->get_field_name( 'accesstoken' ).'" id="'.$this->get_field_id( 'accesstoken' ).'" value="'.esc_attr($instance['accesstoken']).'" class="widefat rtw-accesstoken"';
					if(!empty($instance['loklak_api']) && esc_attr($instance['loklak_api']) == 'true'){
						print ' disabled="disabled"';
					}				
					print ' /></p>';
				echo '							
				<p><label>' . __('Access Token Secret:','fa_tweets') . '</label>		
					<input type="text" name="'.$this->get_field_name( 'accesstokensecret' ).'" id="'.$this->get_field_id( 'accesstokensecret' ).'"  value="'.esc_attr($instance['accesstokensecret']).'" class="widefat rtw-accesstokensecret"';
					if(!empty($instance['loklak_api']) && esc_attr($instance['loklak_api']) == 'true'){
						print ' disabled="disabled"';
					}				
					print ' /></p>';												
				echo '
				<p><label>' . __('Cache Tweets in every:','fa_tweets') . '</label>
					<input type="text" name="'.$this->get_field_name( 'cachetime' ).'" id="'.$this->get_field_id( 'cachetime' ).'" value="'.esc_attr($instance['cachetime']).'" class="small-text rtw-cachetime"/> hours</p>';

				echo '
				<p><label>' . __('Twitter Username:','fa_tweets') . '</label>
					<input type="text" name="'.$this->get_field_name( 'username' ).'" id="'.$this->get_field_id( 'username' ).'" value="'.esc_attr($instance['username']).'" class="widefat rtw-username" /></p>																			
				<p><label>' . __('Tweets to display:','fa_tweets') . '</label>
					<select type="text" name="'.$this->get_field_name( 'tweetstoshow' ).'" id="'.$this->get_field_id( 'tweetstoshow' ).'" class="rtw-tweetstoshow">';
					$i = 1;
					for($i; $i <= 10; $i++){
						echo '<option value="'.$i.'"'; if($instance['tweetstoshow'] == $i){ echo ' selected="selected"'; } echo '>'.$i.'</option>';			
					}
					echo '
					</select></p>
				<p><label>' . __('Exclude replies:','fa_tweets') . '</label>
					<input type="checkbox" name="'.$this->get_field_name( 'excludereplies' ).'" id="'.$this->get_field_id( 'excludereplies' ).'" class="rtw-excludereplies" value="true"'; 
					if(!empty($instance['excludereplies']) && esc_attr($instance['excludereplies']) == 'true'){
						print ' checked="checked"';
					}				
					print ' /></p>';
									
			}
	}
	
	
	

										
					//convert links to clickable format
					if (!function_exists('fa_convert_links')) {
						function fa_convert_links($status,$targetBlank=true,$linkMaxLen=250){
						 
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
					if (!function_exists('fa_relative_time')) {
						function fa_relative_time($a) {
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
								if($d < 3) return __('right now','fa_tweets');
								//if less then minute
								if($d < $minute) return floor($d) . __(' seconds ago','fa_tweets');
								//if less then 2 minutes
								if($d < $minute * 2) return __('about 1 minute ago','fa_tweets');
								//if less then hour
								if($d < $hour) return floor($d / $minute) . __(' minutes ago','fa_tweets');
								//if less then 2 hours
								if($d < $hour * 2) return __('about 1 hour ago','fa_tweets');
								//if less then day
								if($d < $day) return floor($d / $hour) . __(' hours ago','fa_tweets');
								//if more then day, but less then 2 days
								if($d > $day && $d < $day * 2) return __('yesterday','fa_tweets');
								//if less then year
								if($d < $day * 365) return floor($d / $day) . __(' days ago','fa_tweets');
								//else return more than a year
								return __('over a year ago','fa_tweets');
							}
						}	
					}	
	
	
	
// register	widget
	function register_fa_twitter_widget(){
		register_widget('fa_widget_recent_tweet');
	}
	function add_fa_twitter_plugin_script(){
		wp_register_script('test', plugin_dir_url( __FILE__ ).'assets/js/fa_twitter_plugin.js', array('jquery'));
		wp_enqueue_script('test');
	}
	add_action('admin_enqueue_scripts', 'add_fa_twitter_plugin_script' );
	add_action('widgets_init', 'register_fa_twitter_widget', 1);
	
?>