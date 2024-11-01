<?php
/*
Plugin Name: Typographer
Plugin URI: http://typographer.axaple.com/
Description: Типограф для Wordpress автоматически приводит в порядок тексты для публикации сайте. Неразрывные пробелы для предлогов, союзов и сокращений, висячая пунктуация и замена символов.
Version: 1.1.5
Author: Axaple
Author URI: http://axaple.ru/
*/
/*  Copyright 2016 Axaple (email: axaple@axaple.com)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

require_once "typograph.class.php";

if( !class_exists("AxapleTypograph") )
{
	class AxapleTypograph
	{
		private $typograph	= null;
		
		function __construct()
		{
			# Init hooks
			$this->initHooks();
		}
		
		public function initHooks()
		{
			add_filter("the_title", array($this, "process"), 1, 1);
			add_filter("the_title", array($this, "processTwo"), 10, 1);
			add_filter("the_content", array($this, "process"), 1, 1);
			add_filter("the_content", array($this, "processTwo"), 10, 1);
		}
		
		public function process( $input )
		{
			$options	= array(
				"quoteType"			=> 2,
				"nbspProcess"		=> false,
				"paragraphProcess"	=> false,
			);
			$options = apply_filters("axaple_telegraph_options", $options, "process");
			
			return $this->getTypograph($options)->process($input);
		}
		
		public function processTwo( $input )
		{
			$options	= array(
				"quoteType"			=> 0,
				"nbspProcess"		=> true,
				"paragraphProcess"	=> false,
			);
			$options = apply_filters("axaple_telegraph_options", $options, "processTwo");
			
			return $this->getTypograph($options)->process($input);
		}
		
		private function getTypograph( $options )
		{
			if( $this->typograph == null )
				$this->typograph = new ATypograph();
			
			if( is_array( $options ) AND !empty( $options ) )
				foreach( $options AS $optionKey => $optionValue )
					$this->typograph->setOption($optionKey, $optionValue);
			
			return $this->typograph;
		}
	}
}

$GLOBALS["atypograph"] = new AxapleTypograph();