<?php
/*
Plugin Name: Wordpress Karotz
Plugin URI: http://www.christophs-blog.de
Description: Allows your readers to send a text message directly to your Karotz.
Author: Christoph Lang
Version: 1.0
Author URI: http://www.christophs-blog.de
*/
		
class WP_Karotz extends WP_Widget {
	
	function WP_Karotz() {
		$widget_ops = array('classname' => 'karotz', 'description' => __( 'Lass den Hasen labern.') );
		parent::WP_Widget('karotz', __('Karotz'), $widget_ops);
      
	}
	

   function update($new_instance, $old_instance) {
	   $instance = $old_instance;
		 $instance['title'] = strip_tags($new_instance['title']);
		 $instance['ansage'] = strip_tags($new_instance['ansage']);
		 $instance['installid'] = strip_tags($new_instance['installid']);
		 $instance['infourl'] = strip_tags($new_instance['infourl']);
		 $instance['lang'] = strip_tags($new_instance['lang']);
     return $instance;

	
   }

   function form($instance) {
   	
   	if(!isset($instance['title']))
   	{
   		$instance['title'] = "";
   	}
   	
   	if(!isset($instance['ansage']))
   	{
   		$instance['ansage'] = "";
   	}
   	if(!isset($instance['installid']))
   	{
   		$instance['installid'] = "A-B-C-D";
   	}
   	if(!isset($instance['infourl']))
   	{
   		$instance['infourl'] = "";
   	}
	if(!isset($instance['lang']))
   	{
   		$instance['lang'] = "FR";
   	}
   	
   	$title = esc_attr($instance['title']);
   	$ansage = esc_attr($instance['ansage']);
	$installid = esc_attr($instance['installid']);
   	$infourl = esc_attr($instance['infourl']);
	$lang = esc_attr($instance['lang']);
    ?>
     <p>
      <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
      <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
      
      <label for="<?php echo $this->get_field_id('ansage'); ?>"><?php _e('Ansage:'); ?></label> 
      <input class="widefat" id="<?php echo $this->get_field_id('ansage'); ?>" name="<?php echo $this->get_field_name('ansage'); ?>" type="text" value="<?php echo $ansage; ?>" />

	  <label for="<?php echo $this->get_field_id('installid'); ?>"><?php _e('Install-ID:'); ?></label> 
      <input class="widefat" id="<?php echo $this->get_field_id('installid'); ?>" name="<?php echo $this->get_field_name('installid'); ?>" type="text" value="<?php echo $installid; ?>" />

	  <label for="<?php echo $this->get_field_id('lang'); ?>"><?php _e('Language:'); ?></label> 
      <input class="widefat" id="<?php echo $this->get_field_id('lang'); ?>" name="<?php echo $this->get_field_name('lang'); ?>" type="text" value="<?php echo $lang; ?>" />
      
      <label for="<?php echo $this->get_field_id('infourl'); ?>"><?php _e('More Information-URL:'); ?></label> 
      <input class="widefat" id="<?php echo $this->get_field_id('infourl'); ?>" name="<?php echo $this->get_field_name('infourl'); ?>" type="text" value="<?php echo $infourl; ?>" />
    </p>
    <?php 

		
   }

	static function sendToKarotz($instance, $text)
	{
		define('MODE_DEBUG', true);
		#require('include.php');
		include('wizz.cc_karotz_class.php');
		include('karotz.php');

		$apiKey = "c2f05ab2-d347-4551-94e7-28bbe1fb52d1";
		$installid = "3e5ddfa0-9294-4d7c-8e67-0550f7353a8e"; # READ THE INSTALLID FROM CONFIG!
		$secret = "7f6a1601-f79b-4498-a73d-cf04ea416074";

	   	if(!isset($instance['installid']))
		{
			$instance['installid'] = "3e5ddfa0-9294-4d7c-8e67-0550f7353a8e";
		}
		$installid = esc_attr($instance['installid']);
		
		if(!isset($instance['lang']))
		{
			$instance['lang'] = "FR";
		}
		$lang = esc_attr($instance['lang']);
		
		$filename = getKarotzLogfile();

		$text = "Hallo. Jemand hat das Skript aufgerufen, aber keinen Text eingegeben. Seltsam.";

		if(isset($_POST["text"])){
			$text = $_POST["text"];
			$text = str_replace("\r\n"," ",$text);
			$text = str_replace("\n"," ",$text);
		}
		
        $text = str_replace(array("ä","ü","ö","ß"),array("ae","ue","oe","ss"),strtolower($text));	
		
		$filecontent = file_get_contents($filename);
		$filecontent = date("Y-m-d")."|-|".$text . "\n".$filecontent;

		file_put_contents($filename,$filecontent);

 		$interID = Karotz::startAppDirectly($installid,$apiKey,$secret);   
		
		 
		/* $filecontent = date("Y-m-d")."|interid|".$interID . "\n".$filecontent;
		file_put_contents($filename,$filecontent); */


		$karotz = new Karotz($interID, true); # true for debug mode
        
		$karotz->say($text, $lang);
		

		sleep(2);
		$karotz->quit();
	}
	
	function widget( $args, $instance ) {
		extract( $args );
		
		$title = __( $instance['title'] );	
		if(empty($title))
			$title = "Karotz Widget";
			
		$text = null;
		if(isset($_POST["text"]))
		{
			$text = $_POST["text"];
		}	

		if(isset($text) && !empty($text)){
				
			$posttext = $instance['ansage'];
			$posttext .= $text;
			self::sendToKarotz($instance, $posttext);
			$title .= '<br/><span style="color: green;">(Vielen Dank)</span>';
			
		}
		
		if(isset($text) && $text == ""){
			$title .= '<br/><span style="color: red;">(Bitte erst einen Text eingeben)</span>';
			
		}
		$ansage = "Lasse meinen Karotz sprechen. Einfach eine beliebige Nachricht eintippen und abschicken."; 
		if(isset($instance['ansage']))
		{
    		$ansage = esc_attr($instance['ansage']);
		}
		
		$infourl = '';
		if ( isset($instance['infourl']))  {
			$infourl = ' <a href="'.$instance['infourl'].'">(Weitere Informationen)</a>';
		}
		echo "{$before_widget}{$before_title}" . $title . "{$after_title}";
		   	
		echo '<p><b>'.$ansage.'</b>'.$infourl.'</p>';
		echo '<div style="height: 100px;">';
		echo '<form method="post">';
		echo '<textarea style="width:90%;margin:10px;height:50px;" name="text">' . (isset($text) ? $text : '').'</textarea>';		
		echo '<input type="submit" value="Karotz sprechen lassen" style="float:right; padding: 0px 20px 0px 20px; margin-right: 11px;">';
		echo '</form>';		
		
		echo '</div>';
		echo $after_widget;
	}
}


add_action( 'widgets_init', 'wp_karotz_widget_init' );

add_filter ('comment_post', 'new_comment_added' );
	
function new_comment_added($commentID)
{
                   
	// how often did the bot already post
	$comment = get_comment($commentID);
	$content = $comment->comment_content;
	$author = $comment->comment_author;
	
	$post = get_post($comment->comment_post_ID);
	$title = $post->post_title;
	
	$text = 'Neuer Kommentar auf Deinem Blog von '.$author.' bei Artikel '.$title.': '.$content;
	
	WP_Karotz::sendToKarotz($instance, $text);
	
		
}

function wp_karotz_widget_init() {
	register_widget('WP_Karotz');        
}


 /**
 * use hook, to integrate new widget
 */
add_action('admin_menu', 'karotz_widget_menu');

function karotz_widget_menu() {
	add_dashboard_page('Karotz Statistic', 'Karotz Statistic', 9, basename(__FILE__), 'karotz_widget_statistic'); 
	add_action('wp_dashboard_setup', 'karotz_wp_dashboard_setup');
	if(function_exists('register_setting')){
      register_setting('karotz_statistic_options', 'aDeleteLog');
      register_setting('karotz_statistic_options', 'sSelectedTexts');
      
  }
}

function karotz_wp_dashboard_setup() {
  wp_add_dashboard_widget( 'karotz_statistic_dashboard', __( 'Karotz Statistic' ), 'karotz_statistic_dashboard' );
}


function karotz_statistic_dashboard(){
	$filename = getKarotzLogFile();
	
	$filecontent = "";
	if(file_exists($filename))
	{
		$filecontent = file_get_contents($filename);
	} else {
		echo '<p>Datei nicht gefunden!'.$filename.'</p>';	
	}
	
	$data = getKarotzTexts($filecontent);
	$keys = array_keys($data);
	
	if(count($keys) <= 0){		
		echo "<p>Momentan leider keine Nachricht vorhanden...</p>";		
	}else{	
		$text = $data[$keys[0]][0];
		echo "<p><b>".$keys[0].":</b> ".replace_text($text)."</p>";
	}
}

function replace_text($text)
{	
		$text = str_replace(array("  ","\\'","\\\""),array(" ","'","\""),$text);
		return $text;
}

function getKarotzDelete($filename){
	
	echo '<form method="post" action="options.php">';

  settings_fields('karotz_statistic_options');
	$delete = get_option('aDeleteLog');
	$selectedTexts = get_option('sSelectedTexts');
	
	
	update_option('aDeleteLog','');
  
	echo '<input type="hidden" id="aDeleteLog" name="aDeleteLog" value="delete" size="10" />';
	echo '<input type="hidden" id="sSelectedTexts" name="sSelectedTexts" value="" size="100" />';
  echo '<p class="submit"><input type="submit" name="Submit" value="Ausgewählte Texte löschen" class="button-primary" /></p>';

	echo '</form>';
	
	if($delete == "delete"){
		
		$md5texts = explode(",",$selectedTexts);
		$bReturn = deleteTextsFromFile($filename,$md5texts);
		
		if($bReturn)
			echo '<span class="error">Ausgewählte Texte wurden entfernt.</span>';		
		else
			echo '<span class="error">Es wurden keine Texte ausgewählt.</span>';		
		
	}
}


function deleteTextsFromFile($filename, $md5texts)
{
	$bReturn = false;
	$filecontent = "";
	if(file_exists($filename))
	{
		$filecontent = file_get_contents($filename);
	}
	
	if(trim($filecontent) == ""){		
		return $bReturn;
	}
	
	$lines = explode("\n",$filecontent);
	foreach($lines as $key => $line){
		
		$linedata = explode("|-|",$line);
		if(!isset($linedata[1]))
			continue;
			
		if(in_array(md5($linedata[1]),$md5texts))
		{
			unset($lines[$key]);
			$bReturn = true;
		}
		
	}
	file_put_contents($filename,implode("\n",$lines));
	return $bReturn;
	
}

function getKarotzTexts($filecontent)
{
	
	$lines = explode("\n",$filecontent);
	$data = array();
	foreach($lines as $line){
		$linedata = explode("|-|",$line);
		if(empty($linedata[0]))
			continue;
		if(!isset($data[$linedata[0]]))
			$data[$linedata[0]] = array();
		$data[$linedata[0]][] = $linedata[1];
		
	}
	
	krsort($data);
	
	return $data;
	
}

function getKarotzLogFile(){
	return "messages.log";
}

function karotz_widget_statistic() {
		
	$filename = getKarotzLogFile();
		
	echo getKarotzWidgetCSS();
	echo getKarotzWidgetJavascript();
	
  echo '<h1>Karotz Widget Statistic</h1>'; 
	echo getKarotzDelete($filename);
	
	
	$box = '<div class="postbox-container" style="width:97%; margin-top: 0px; margin-left: 15px;">
						<div class="metabox-holder">	
							<div class="meta-box-sortables" style="min-height: 0">
								<div class="postbox">
									<h3 class="hndle" style="cursor:normal"><span>TITLE</span></h3>
									<div class="inside">CONTENT</div>
								</div>
							</div>
						</div>
					</div>';
	
	$filecontent = "";
	if(file_exists($filename))
	{
		$filecontent = file_get_contents($filename);
	} else {
		echo '<p>Datei nicht gefunden!'.$filename.'</p>';	
	}
	if(trim($filecontent) == ""){		
		echo str_replace(array("TITLE","CONTENT"),array("Nothing to do!","Sorry, currently there is no text to display..."),$box);
		return;
	}
	
	$data = getKarotzTexts($filecontent);
	
	foreach($data as $datum => $entrys){
		$content = "";
		
		$i=0;
		foreach($entrys as $entry){
			if(!empty($entry)){
				$content .= "<input type=\"checkbox\" onclick=\"handle(this);\" value=\"".md5($entry)."\" /><span>".replace_text($entry)."</span><br/>";	
				$i++;		
			}
		}
		
		if(!empty($content))
			echo str_replace(array("TITLE","CONTENT"),array($datum." (".$i.")",$content),$box);
	}
	
	
	
}

function getKarotzWidgetCSS(){
	return "<style type=\"text/css\">
		
    h1{
   	 	color: #464646;
   	 	font: italic 24px/35px Georgia,\"Times New Roman\",\"Bitstream Charter\",Times,serif;
	    margin: 0;
	    padding: 14px 15px 3px 15px;
	    text-shadow: 0 1px 0 #FFFFFF;
    }
        
    .inside span{
      font-weight:bold;
      font-size:10px;
      padding: 0px 0px 0px 10px;
      clear:both;
    
    }
    p.submit{
      padding-left: 20px;    
    }
    span.error{
      font-weight:bold;
      font-size:20px;
    	padding: 0px 0px 0px 20px;
   	 	color: red;
    }
    </style>\n";
}

function getKarotzWidgetJavascript(){
	
	$sReturn = '';
	$sReturn .= "<script type='text/javascript'>\n";
	$sReturn .= "
							function handle(element)
							{										
								if (element.checked) {									
									karotz_widget_add(element.value);									
								} else {
									karotz_widget_remove(element.value);
								}
							}
														
							function karotz_widget_remove(index)
							{
								var vals = (jQuery)(\"#sSelectedTexts\").val();								
								data = vals.split(\",\");
								
								for (i = 0; i < data.length; i++) {
								
									if(data[i] == index){
										data.splice(i,1);
									}
								}
										
								(jQuery)(\"#sSelectedTexts\").val(data.join(','));
							}
							
							function karotz_widget_add(index){
								var vals = (jQuery)(\"#sSelectedTexts\").val();
								data = vals.split(\",\");
								
								for (i = 0; i < data.length; i++) {
									
									if(data[i] == index){
										return;				
									}
									
									if(data[i] == ''){
										data.splice(i,1);
									}
								}
							
								data.push(index);
								(jQuery)(\"#sSelectedTexts\").val(data.join(','));
	
							}							
							";
	$sReturn .= "</script>\n";
	
	return $sReturn;
	
	
}

?>