<?php

class Karotz extends wizz_karotz {
		
	public static function getInteractiveId(){
		if(isset($_GET["interactiveid"])){
			return $_GET["interactiveid"];
		}
				
		$data = file_get_contents("php://input");
		if($data){
			try {
			  $voosMsg = simplexml_load_string($data);			
				return $voosMsg->interactiveId;
			  
			}catch (Exception $e){
	    	return null;
	    }
		}	    
			
		return null;		
	}	
	
	public static function sign($installid, $apikey, $secret){
      
      $parameter = array(
        "installid" => $installid,
        "apikey" => $apikey,
        "once" => rand ( 9999999999999, 99999999999999 ),
        "timestamp" => time());

      $items = array();
      foreach( $parameter as $key => $value){
          array_push($items, urlencode($key)."=".urlencode($value));
      }
      asort($items);
      
      $query = implode ( "&" , $items );
      
      $iv = hash_hmac ( "sha1" , $query, $secret, true );
      $signature = base64_encode($iv);      
      
      return $query."&signature=".urlencode($signature);      
      
  }

	public static function startAppDirectly($installid, $apikey, $secret)
	{
		
		$sUrl = "http://api.karotz.com/api/karotz/start?" . self::sign($installid, $apikey, $secret);
		
		//open connection
		$ch = curl_init();
				
		curl_setopt($ch,CURLOPT_URL,$sUrl);
		curl_setopt($ch,CURLOPT_POST,0);
		curl_setopt($ch,CURLOPT_POSTFIELDS,"");
		//curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1); 
		curl_setopt($ch, CURLOPT_HEADER      ,1); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
				
		//execute post
		$result = curl_exec($ch);

		//echo "Result:".$result;
		$start=strpos($result,"<interactiveId>");
		
		$interactiveId=substr($result,$start+15,36);
		//echo "interactiveId:".$interactiveId;
		 
			 		
		return $interactiveId;
		
		
	}
	
	
	public static function startApp($sUsername, $sPassword, $sApiKey){
		
		$urlLogin = 'http://www.karotz.com/login/j_spring_security_check';
		$urlStart = 'http://www.karotz.com/authentication/run/karotz/'.$sApiKey;
		
		$sTempCookieFile = "../_cookie.txt";
		
		//open connection
		$ch = curl_init();
				
		curl_setopt($ch,CURLOPT_URL,$urlLogin);
		curl_setopt($ch,CURLOPT_POST,2);
		curl_setopt($ch,CURLOPT_POSTFIELDS,'j_username='.$sUsername.'&j_password='.$sPassword );
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1); 
		curl_setopt($ch, CURLOPT_HEADER      ,0); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
		
		// IMITATE CLASSIC BROWSER'S BEHAVIOUR : HANDLE COOKIES
		curl_setopt ($ch, CURLOPT_COOKIEJAR, $sTempCookieFile);
		
		//execute post
		$result = curl_exec($ch);
				
		curl_setopt($ch,CURLOPT_URL,$urlStart);
		curl_setopt($ch,CURLOPT_POST,0);
		curl_setopt($ch,CURLOPT_POSTFIELDS,"");
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1); 
		curl_setopt($ch, CURLOPT_HEADER      ,1); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
		
		//execute post
		$result = curl_exec($ch);	
						
		$start=strpos($result,"interactiveid=");
		$end=strpos($result,"&",$start);
		$id=substr($result,$start+14,$end-$start-14);
						
		//close connection
		curl_close($ch);
		
		if(file_exists($sTempCookieFile))
			unlink($sTempCookieFile);
		
		return $id;
		
	}
	
	
	protected function checkInteractiveId(){
		if(!isset($this->liveid))
			return false;
			
		if($this->liveid == null)
			return false;
			
		return true;
	}
		
	public function isConnected(){		
		return $this->checkInteractiveId();		
	}	
	
	public function handleButton(){ 
		
		if(!empty($_GET["interactiveid"])){
			return false;
		}
			
		$data = file_get_contents("php://input");
		$voosMsg = null;
		if($data){
			try {
			  $voosMsg = simplexml_load_string($data);
			}catch (Exception $e) {
	    	return false;
	    }
		}
		   
    if($voosMsg){
    	$type = $voosMsg->buttonCallback->type;
    	$this->liveid = self::getInteractiveId();
    	
			$this->api_debug .= '<br />[Button Data]'.$this->liveid.'<br/>'.$type.'[/Button Data]';
  	
      if (isset($type) && isset($this->liveid)){
	      	
				if($this->debug)
				{
					$this->api_debug .= '<br />[Button Data]'.$this->liveid.'<br/>'.$type.'[/Button Data]';
				}
			
    	 if($type == "SIMPLE")
        {
					$this->quit();	
					return true;
        }
      }       	
    }
    
    return false;    
	}
	
	public function replaceColorIfNecessary($color){
		$aColors = array(
									"BLACK"  => "000000",
									"BLUE"   => "0000FF",
									"CYAN"   => "00FF9F", 
									"GREEN"  => "00FF00", 
									"ORANGE" => "FFA500", 
									"PINK"   => "FFCFAF",
									"PURPLE" => "9F00FF", 
									"RED"    => "FF0000",
									"YELLOW" => "75FF00", 
									"WHITE"  => "4FFF68"
									);
		if(isset($aColors[strtoupper($color)]))
		{
			return $aColors[strtoupper($color)];
		}
		
		return $color;
		
	}
	
	# LED
	public function led_pulse($color='FFFFFF', $period=3000, $pulse=500) {
		$color = $this->replaceColorIfNecessary($color);
		$return = parent::led_pulse($color,$period,$pulse);
	}
		
	public function led_fade($color='FFFFFF', $period=3000) {
		$color = $this->replaceColorIfNecessary($color);
		$return = parent::led_fade($color,$period);
	}
	public function led_light($color='FFFFFF') {
		$color = $this->replaceColorIfNecessary($color);
		$return = parent::led_light($color);
	}
	
	public function say($text=null, $lang=null, $break_strength = null, $prosody = null, $emotion = null) {
		
		//$text = ' Hello Jonathan <prosody rate="-50%">this is very slow text</prosody><break strength="medium" /><prosody rate="150%">this is very quick text</prosody>';
		//http://www.w3.org/TR/speech-synthesis/
		/*			
		- texte<break strength="weak|medium|strong" />texte
		- <prosody rate="+ou-taux%">texte</prosody>
		- <voice emotion='calm|happy|sad'>texte</voice>
		*/
		
		$text = str_replace(array("ä","ü","ö","ß"),array("ae","ue","oe","ss"),strtolower($text));		
		
		if(isset($break_strength)){
			switch($break_strength){
				case 'weak': $text = '<break strength="weak" />'.$text; break;
				case 'medium': $text = '<break strength="medium" />'.$text; break;
				case 'strong': $text = '<break strength="strong" />'.$text; break;
				default: break;
			}
		}
		
		if(isset($prosody)){
				$text = '<prosody rate="'.$prosody.'" >'.$text.'</prosody>';
		}
		
		if(isset($emotion)){
			switch($emotion){
				case 'calm': $text = '<voice emotion="calm" >'.$text.'</voice>'; break;
				case 'happy': $text = '<voice emotion="happy" >'.$text.'</voice>'; break;
				case 'sad': $text = '<voice emotion="sad" >'.$text.'</voice>'; break;
				default: break;
			}
		}
		
		$return = parent::say($text,$lang);
		sleep(strlen($text) / 7);
		return $return;
	}	
	
	public function config($name=null) {
		
		$this->rest_method = self::API_FCT_CONFIG;
		unset($this->api_params);
		$this->api_params[''] = 'none';
		$this->call_api();
		
		if(!isset($this->api_resp_array['config']))
			return "";
			
		$return = $this->api_resp_array['config'];
			
		
		$xml = @new SimpleXMLElement($this->api_response);
		if(isset($name) && $name != null){	
			if(isset($xml->config)){
				foreach ($xml->config->children() as $parent => $child) {
					if($parent == "params"){
						if($child->key == $name){
							return $child->value;
						}		
					}
				}
			}
		}
		
		return $return;
	}
	
	public function sayTTS($text=null, $voice="reiner") {
		
		$text = str_replace(array("ä","ü","ö","ß"),array("ae","ue","oe","ss"),strtolower($text));				
		$postdata = 'voice='.$voice.'&txt='.urlencode($text);  // POST VARIABLES TO BE SENT								
		
		$ch = curl_init("http://192.20.225.36/tts/cgi-bin/nph-talk");
		curl_setopt($ch, CURLOPT_POST      ,1);
		curl_setopt($ch, CURLOPT_POSTFIELDS    ,$postdata);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1); 
		curl_setopt($ch, CURLOPT_HEADER      ,1);  // DO NOT RETURN HTTP HEADERS 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
		$Rec_Data = curl_exec($ch);
		
		$curl_info = curl_getinfo($ch);				
		curl_close($ch);		
		$mediafile = $curl_info["url"];
		
		$this->rest_method = self::API_FCT_MULTIMEDIA;
		unset($this->api_params);
		$this->api_params['action'] = 'play';
		$this->api_params['url'] = $mediafile;
		$return = $this->call_api();
		sleep(strlen($text) / 7);
		
		$this->rest_method = self::API_FCT_MULTIMEDIA;
		unset($this->api_params);
		$this->api_params['action'] = 'stop';
		$this->call_api();
		
		return $return;
	}
	
	
}


?>