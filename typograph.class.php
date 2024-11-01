<?php

class ATypograph
{
	protected $options	= array();
	
	function __construct( $options = array() )
	{
		$defaultOptions	= array(
			"apos"				=> "&#39;",
			"nbsp"				=> "&nbsp;",
			"ndash"				=> "&ndash;", # –
			"mdash"				=> "&mdash;", # —
			"hellip"			=> "…",
			# Abbr
			"wrapAbbr"			=> false,
			"lnowrap"			=> "<span style=\"white-space: nowrap;\">",
			"rnowrap"			=> "</span>",
			# Quotes
			"quoteType"			=> 2,
			// "laquo"				=> "&laquo;",
			"laquo"				=> "«",
			// "raquo"				=> "&raquo;",
			"raquo"				=> "»",
			"lquote2"			=> "„",
			"rquote2"			=> "“",
			// "hpspace"			=> "style=\"margin-right: 0.7em;\"",
			"hpspace"			=> "class=\"hpspace\"",
			// "hpquote"			=> "style=\"margin-left: -0.7em;\"",
			"hpquote"			=> "class=\"hpquote\"",
			# Marks
			"marksProcess"		=> true,
			"tradeMark"			=> "&trade;",
			"copyMark"			=> "&copy;",
			"regMark"			=> "&reg;",
			# Paragraphs
			"paragraphProcess"	=> true,
			"paragraphStart"	=> "<p>",
			"paragraphEnd"		=> "</p>",
		);
		
		$options = array_intersect_key($options, array_flip(array_keys($defaultOptions)));
		
		$this->options = array_merge($defaultOptions, $options);
	}
	
	public function getOption( $option )
	{
		if( !isset( $this->options[$option] ) )
			return null;
		
		return $this->options[$option];
	}
	
	public function setOption( $option, $value )
	{
		if( !isset( $this->options[$option] ) )
			return false;
		
		$this->options[$option] = $value;
		
		return true;
	}
	
	public function process( $input )
	{
		$html_tag = "(?:(?U)<[a-zA-Z0-9\/][^>]*>)";
		$abbr = "(?:ООО|ОАО|ЗАО|ЧП|ИП|НПФ|НИИ)";
		$prepos = "а|в|во|вне|и|или|к|о|с|у|о|со|об|обо|от|ото|то|на|не|ни|но|из|изо|за|уж|на|по|под|подо|пред|предо|про|над|надо|как|без|безо|что|да|для|до|там|ещё|их|или|ко|меж|между|перед|передо|около|через|сквозь|для|при|я";
		$metrics = "мм|см|м|км|г|кг|б|кб|мб|гб|dpi|px";
		$shortages = "г|гр|тов|пос|c|ул|д|пер|м|кв";
		$word = "[a-zA-Zа-яА-ЯрРхХюЮуУсСтТ_]";
		
		$phrase_begin = "(?:\.{3,5}|" . $word . "|\n)";
		$phrase_end   = "(?:[)!?.:;#*\]|\$|" . $word . "|" . $this->getOption("raquo") . "|" . $this->getOption("rquote2") . "|&quot;|\"|" . 
		$this->getOption("hellip") . "|" . $this->getOption("copyMark") . "|" . $this->getOption("tradeMark") . "|" . $this->getOption("apos") . "|" . $this->getOption("regMark") . "|')";
		
		$any_quote = "(?:" . $this->getOption("laquo") . "|" . $this->getOption("raquo") . "|" . $this->getOption("lquote2") . "|" . $this->getOption("rquote2") . "|\")";# &quot;
		
		# Quotes
		$input = preg_replace("~([^\"]\w+)\"(\w+)\"~", "\$1 \"\$2\"", $input);
		$input = preg_replace("~\"(\w+)\"(\w+)~", "\"\$1\" \$2", $input);
		
		$input = preg_replace("~(?<=\\s|^|[>(])(".$html_tag."*)(".$any_quote.")(".$html_tag."*".$phrase_begin.$html_tag."*)~", "\$1" . $this->getOption("laquo") . "\$3", $input);
		$input = preg_replace("~(".$html_tag."*(?:".$phrase_end."|[0-9]+)".$html_tag."*)(".$any_quote.")(".$html_tag."*".$phrase_end.$html_tag."*|\s|[,<-])~", "\$1" . $this->getOption("raquo") . "\$3", $input);
		
		# nbsp
		if( $this->getOption("wrapAbbr") )
			$input = preg_replace("~(" . $abbr . ")\s+" . $any_quote . "(.*)" . $any_quote . "~", $this->getOption("lnowrap") . "\$1" . $this->getOption("nbsp") . $this->getOption("laquo") . "\$2" . $this->getOption("raquo") . $this->getOption("rnowrap"), $input);
		else
			$input = preg_replace("~(" . $abbr . ")\s+" . $any_quote . "(.*)" . $any_quote . "~", "\$1" . $this->getOption("nbsp") . $this->getOption("laquo") . "\$2" . $this->getOption("raquo"), $input);
		
		$input = preg_replace("~(^|[^a-zA-Zа-яА-Я])(" . $shortages . ")\.\s?([А-Я0-9]+)~s", "\$1\$2" . $this->getOption("nbsp") . "\$3", $input);
		$input = preg_replace("~(стр|с|табл|рис|илл)\.?\s*(\d+)~si", "\$1" . $this->getOption("nbsp") . "\$2", $input);
		$input = preg_replace("~(?<=\s|^|\w)(" . $prepos . ")(\s+)~i", "\$1" . $this->getOption("nbsp"), $input);
		$input = preg_replace("~(?<=\S)(\s+)(ж|бы|б|же|ли|ль|либо|или)(?=" . $html_tag . "*[\s)!?.])~i", $this->getOption("nbsp") . "\$2", $input);
		
		# Dashes
		$input = preg_replace("~ +(?:--?|—|&mdash;)(?=\s)~", $this->getOption("nbsp") . $this->getOption("mdash"), $input);
		$input = preg_replace("~^(?:--?|&mdash;)(?=\s)~", $this->getOption("mdash"), $input);
		
		$input = preg_replace("/(?<=\d)-(?=\d)/", $this->getOption("ndash"), $input);
		
		# Hellips
		$input = preg_replace("~\.{3,5}~", $this->getOption("hellip"), $input);
		
		# Metrics
		$input = preg_replace("~([0-9]+)\s*(" . $metrics . ")~s", "\$1" . $this->getOption("nbsp") . "\$2", $input);
		$input = preg_replace("~(\s" . $metrics . ")(\d+)~", "\$1<sup>\$2</sup>", $input);
		
		$input = preg_replace_callback("~(<".$allblocks."[^>]*>)~", function($item){
			$item = str_replace("&nbsp;", " ", $item[0]);
			$item = str_replace("&ndash;", "-", $item);
			$item = str_replace($this->getOption("laquo"), "\"", $item);
			$item = str_replace($this->getOption("raquo"), "\"", $item);
			
			$item = preg_replace_callback("/(=\")([^\"]*)(\")(.[^=\"]*)(\")(\"|.[^\"])/", function($input){
				return $input[1].$input[2]."&quot;".$input[4]."&quot;".$input[6];
			}, $item);
			return $item;
		}, $input);
		
		if( $this->getOption("quoteType") == 2 )
		{
			$input = preg_replace("~(\s)" . $this->getOption("laquo") . "~", "<span " . $this->getOption("hpspace") . "> </span><span " . $this->getOption("hpquote") . ">" . $this->getOption("laquo") . "</span>", $input);
			$input = preg_replace("~[^>]" . $this->getOption("laquo") . "~", "<span " . $this->getOption("hpspace") . "> </span><span " . $this->getOption("hpquote") . ">" . $this->getOption("laquo") . "</span>", $input);
			$input = preg_replace("~^" . $this->getOption("laquo") . "~", "<span " . $this->getOption("hpquote") . ">" . $this->getOption("laquo") . "</span>", $input);
			$input = preg_replace("~(" . $prepos . ")\s<span " . $this->getOption("hpspace") . "> </span><span " . $this->getOption("hpquote") . ">" . $this->getOption("laquo") . "</span>~", "\$1 " . $this->getOption("laquo"), $input);
		}
		
		# Marks
		if( $this->getOption("marksProcess") )
		{
			$input = str_replace(array("(c)", "(C)"), $this->getOption("copyMark"), $input);
			$input = str_replace(array("(r)", "(R)"), $this->getOption("regMark"), $input);
			$input = str_replace(array("(tm)", "(TM)"), $this->getOption("tradeMark"), $input);
		}
		
		# Paragraphs
		if( $this->getOption("paragraphProcess") )
			$input = $this->paragraphs($input);
		
		# Br fix
		$allblocks = "(?:a|img|table|thead|tfoot|caption|col|iframe|colgroup|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|form|map|area|blockquote|address|math|style|p|h[1-6]|hr|fieldset|legend|section|article|aside|hgroup|header|footer|nav|figure|figcaption|details|menu|summary|script|pre)";
		
		/**
		 * TODO: do work
		 */
		$input = preg_replace("!<p>\s*(</?" . $allblocks . "[^>]*>)!", "\$1", $input);
		$input = preg_replace("!(</?" . $allblocks . "[^>]*>)\s*</p>!", "\$1", $input);
		
		$input = preg_replace("!(</?" . $allblocks . "[^>]*>)\s*<br />!", "\$1", $input);
		
		$input = preg_replace("~<script[^>]*>(.*?)(<br[^>]*>)(.*?)</script>~", "\$1\$3", $input);
		
		$input = preg_replace_callback("~(<(script|pre)[^>]*>)((\n|.|)*?)(</(script|pre)>)~", function($item){
			$item[3] = str_replace("&nbsp;", " ", $item[3]);
			$item[3] = str_replace("&ndash;", "-", $item[3]);
			$item[3] = str_replace($this->getOption("raquo"), "\"", $item[3]);
			
			return $item[1] . preg_replace("~(?:<br[^>]*>)~", "", $item[3]) . $item[5];
		}, $input);
		
		return $input;
	}
	
	protected function paragraphs( $input )
	{
		if( trim( $input ) == "" )
			return $input;
		
		$input .= "\n";
		$input = str_replace(array("\r\n", "\r"), "\n", $input);
		$input = preg_replace("/\n\n+/", "\n\n", $input);
		$inputs = preg_split("/\n\s*\n/", $input, -1, PREG_SPLIT_NO_EMPTY);
		
		$input = "";
		
		foreach( $inputs AS $paragraph )
			$input .= $this->getOption("paragraphStart") . trim($paragraph, "\n") . $this->getOption("paragraphEnd") . "\n";
		
		$input = preg_replace("|" . $this->getOption("paragraphStart") . "\s*" . $this->getOption("paragraphEnd") . "|", "", $input);
		$input = preg_replace("|(?<!<br />)\s*\n|", "<br />\n", $input);
		$input = preg_replace("!<br />(\s*</?(?:p|li|div|dl|dd|dt|th|pre|td|ul|ol)[^>]*>)!", "\$1", $input);
		$input = preg_replace("|\n" . $this->getOption("paragraphEnd") . "\$|", $this->getOption("paragraphEnd"), $input);
		
		return $input;
	}
}