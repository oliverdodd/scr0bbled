<?php
/*
Plugin Name: scr0bbled
Plugin URI: http://wordpress.org/extend/plugins/scr0bbled/
Description: latest [available] album artwork from recently scrobbled tracks
Author: Oliver C Dodd
Version: 1.0.3
Author URI: http://01001111.net
  
  Copyright (c) 2009 Oliver C Dodd - http://01001111.net
  
  Much of the functionality is taken from the free 01001111 library
  
  *NOTE: you will need to apply for and supply your own Last.FM API Key
  
  Permission is hereby granted,free of charge,to any person obtaining a 
  copy of this software and associated documentation files (the "Software"),
  to deal in the Software without restriction,including without limitation
  the rights to use,copy,modify,merge,publish,distribute,sublicense,
  and/or sell copies of the Software,and to permit persons to whom the 
  Software is furnished to do so,subject to the following conditions:
  
  The above copyright notice and this permission notice shall be included in
  all copies or substantial portions of the Software.
  
  THE SOFTWARE IS PROVIDED "AS IS",WITHOUT WARRANTY OF ANY KIND,EXPRESS OR
  IMPLIED,INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
  FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL 
  THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,DAMAGES OR OTHER
  LIABILITY,WHETHER IN AN ACTION OF CONTRACT,TORT OR OTHERWISE,ARISING
  FROM,OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
  DEALINGS IN THE SOFTWARE.
*/
class scr0bbled
{
	/*-VARIABLES----------------------------------------------------------*/
	private $apiKey;
	private $user;
	private $nAlbums;
	private $imageSize;
	public $title;
	private $divid;
	
	public static $imageSizes = array('small','medium','large');
	
	/*-CONSTRUCT----------------------------------------------------------*/
	//public function __construct($k,$u,$n=10,$s='medium',$t="scr0bbled")
	public function scr0bbled($k,$u,$n=10,$s='medium',$t="scr0bbled",$d="")
	{
		$this->apiKey		= $k;
		$this->user		= $u;
		$this->nAlbums		= $n;
		$this->imageSize	= $s;
		$this->title		= $t;
		$this->divid		= $d;
	}
	
	/*-URL----------------------------------------------------------------*/
	private function url($method,$args=array())
	{
		$args = $this->queryString($args);
		if ($args) $args = "&$args";
		return "http://ws.audioscrobbler.com/2.0/".
			"?method=$method&api_key=$this->apiKey$args";
	}
	private function queryString($args)
	{
		if (!is_array($args))
			return $args;
		$pairs = "";
		foreach ($args as $k => $v)
			$pairs[] = "$k=$v";
		return implode('&',$pairs);
	}
	/*-GET RECENT TRACKS--------------------------------------------------*/
	private function recentTracks($limit=50)
	{
		$url = $this->url("user.getrecenttracks",
			"user=$this->user&limit=$limit");
		$recentTracksXML = @file_get_contents($url);
		return $this->parseRecentTracks($recentTracksXML);
	}
	/*-PARSE RECENT TRACKS------------------------------------------------*/
	private function parseRecentTracks($xml)
	{
		$doc = new DOMDocument();
		if (!$xml||!$doc->loadXML($xml)) return array();
		
		$tracks = array();
		foreach ($doc->getElementsByTagName("track") as $node) {
			$tracks[] = array(
				'track'		=> self::tagValue($node,'name'),
				'artist'	=> self::tagValue($node,'artist'),
				'album'		=> self::tagValue($node,'album'),
				'image'		=> self::tagValue($node,'image',
							array("size"=>$this->imageSize))
			);
		}
		return $tracks;
	}
	/*-XML PARSING SPECIFICS----------------------------------------------*/
	public static function tagValue($node,$tag,$attributes=array(),$valueIfNoChild=false)
	{
		if (!$attributes) {
			$children = $node->getElementsByTagName($tag);
			return $children->length
				? $children->item(0)->nodeValue
				: ($valueIfNoChild ? $node->nodeValue : "");
		}
		//get tags
		$tags = $node->getElementsByTagName($tag);
		//check attributes
		$element = false;
		for ($i = 0; $i < $tags->length; $i++) {
			$found = true;
			foreach ($attributes as $k => $v)
				$found &= strcasecmp($tags->item($i)->getAttribute($k),$v) == 0;
			if ($found) {
				$element = $tags->item($i);
				break;
			}
		}
		return $element ? $element->nodeValue : "";
	}
	/*-GET LATEST ALBUMS--------------------------------------------------*/
	public function latestAlbums()
	{
		if (!$this->apiKey||!$this->user) return "";
		$tracks = $this->recentTracks($this->nAlbums*10);
		
		$html = "";
		$albums = array();
		foreach($tracks as $track) {
			if (count($albums) >= $this->nAlbums) break;
			if (!$track['image']) continue;
			
			$artist		= self::a($track,'artist');
			$album		= self::a($track,'album');
			$src		= self::a($track,'image');
			
			$title = "$artist - $album";
			
			if (isset($albums[$title])) continue;
			$albums[$title] = $src;
			
			$html .= "<img	class='album'
					title='".str_replace("'","&#39;",$title)."'
					src='$src' /> ";
		}
		return $this->divid ? "<div id='$this->divid'>$html</div>" : $html;
	}
	
	/*-ARRAY ACCESSOR-----------------------------------------------------*/
	private static function a($a,$k,$d="")
	{
		if (!is_array($a)) return $d;
		if (!isset($a[$k])) {
			if ($set) $a[$k] = $d;
			return $d; }
		return $a[$k];
	}
	/*-GET OPTIONS--------------------------------------------------------*/
	public static function getOptions()
	{
		return !($options = get_option('scr0bbled'))
			? $options = array(
				'apiKey'	=> "",
				'user'		=> "",
				'nAlbums'	=> 10,
				'imageSize'	=> "medium",
				'title'		=> "scr0bbled",
				'divid'		=> "")
			: $options;
	}
	
	/*-MAKE OPTIONS-------------------------------------------------------*/
	public static function makeOptions($a,$s="")
	{
		$options = "";
		foreach ($a as $o) {
			$sel = $o == $s ? " selected='selected' " : "";
			$options .= "<option$sel>$o</o>";
		}
		return $options;
	}
}
/*-OPTIONS--------------------------------------------------------------------*/
function widget_scr0bbled_options()
{
	$options = scr0bbled::getOptions();
	if($_POST['scr0bbled-submit'])
	{
		$options = array(	'apiKey'	=> $_POST['scr0bbled-apiKey'],
					'user'		=> $_POST['scr0bbled-user'],
					'nAlbums'	=> $_POST['scr0bbled-nAlbums'],
					'imageSize'	=> $_POST['scr0bbled-imageSize'],
					'title'		=> $_POST['scr0bbled-title'],
					'divid'		=> $_POST['scr0bbled-divid']);
		update_option('scr0bbled',$options);
	}
	?>
	<p>	Last.FM API Key:
		<input	type="text"
			name="scr0bbled-apiKey"
			id="scr0bbled-apiKey"
			value="<?php echo $options['apiKey']; ?>"  />
	</p>
	<p>	Last.FM User:
		<input	type="text"
			name="scr0bbled-user"
			id="scr0bbled-user"
			value="<?php echo $options['user']; ?>"  />
	</p>
	<p>	Number of Albums to Display:
		<select	name="scr0bbled-nAlbums"
			id="scr0bbled-nAlbums">
			<?php echo scr0bbled::makeOptions(range(1,20),
				$options['nAlbums']); ?>
		</select>
	</p>
	<p>	Image Size:
		<select	name="scr0bbled-imageSize"
			id="scr0bbled-imageSize">
			<?php echo scr0bbled::makeOptions(scr0bbled::$imageSizes,
				$options['imageSize']); ?>
		</select>
	</p>
	<p>	Title:
		<input	type="text"
			name="scr0bbled-title"
			id="scr0bbled-title"
			value="<?php echo $options['title']; ?>"  />
	</p>
	<p>	Wrapper Div ID (blank for no div):
		<input	type="text"
			name="scr0bbled-divid"
			id="scr0bbled-divid"
			value="<?php echo $options['divid']; ?>"  />
	</p>
	<input type="hidden" id="scr0bbled-submit" name="scr0bbled-submit" value="1" />
	<?php
}
/*-WIDGETIZE------------------------------------------------------------------*/
function widget_scr0bbled_init()
{
	if (!function_exists('register_sidebar_widget')) { return; }
	function widget_scr0bbled($args)
	{
		extract($args);
		$options = scr0bbled::getOptions();
		$s = new scr0bbled(	$options['apiKey'],
					$options['user'],
					$options['nAlbums'],
					$options['imageSize'],
					$options['title'],
					$options['divid']);
		echo "	$before_widget
			$before_title $s->title $after_title
				{$s->latestAlbums()}
			$after_widget
		";
	}
	register_sidebar_widget('scr0bbled','widget_scr0bbled');
	register_widget_control('scr0bbled','widget_scr0bbled_options');
}
//add_action('init',"widget_scr0bbled_init");
add_action('plugins_loaded', 'widget_scr0bbled_init');
?>
