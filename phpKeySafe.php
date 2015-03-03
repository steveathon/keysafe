<?php
	
	/** phpKeySafe
	 * @author Steve King <steve@stevenking.com.au>
	 * @copyright 2012 Steven King
	 * 
	 * This library is free software; you can redistribute it and/or
	 * modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
	 * License as published by the Free Software Foundation; either
	 * version 3 of the License, or any later version.
	 *
	 * This library is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 * GNU AFFERO GENERAL PUBLIC LICENSE for more details.
	 *
	 * You should have received a copy of the GNU Affero General Public
	 * License along with this library.  If not, see <http://www.gnu.org/licenses/>.
	 *
	 * Requires: JSON
	 * 
	 * Purpose: Providing a safer way to store passwords in your application
	 * without the need to store passwords in your code. 
	 * 
	 * TODO: Add support for writing new keys via this method. Right now it only
	 * permits a read of any files previously written.
	 */

	final class phpKeySafe {
		private $_keyDir;
		private $_cache;
		
		
		/**
		 * Construct, this will give you the base object you need to operate with.
		 * 
		 * Recent changes to improve path detection and reduce risk of... breakage.
		 * 
		 * @param path $KeyDir
		 * @throws Exception
		 */
		
		public function __construct($KeyDir = NULL) {
			
			if ( !function_exists('json_decode') ) {
				throw new Exception('ERR: You do not appear to have function.json_decode activated in PHP ini');
			}
			
			if ( strlen($KeyDir) > 0 ) {
				$rootParts = explode('/',$KeyDir);
				// Just incase there is crap at the end.
				while ( !@end($rootParts) && @count($rootParts) > 0 ) {
					@array_pop($rootParts);
				}
				if ( strlen($rootParts[0]) > 0 ) {
					// Then we can assume that the first part was not a /, if it isn't, then it's relative.
					$curDir = cwd() . '/';
					foreach ( $rootParts as $rootPart ) {
						$theString = @ereg_replace("[^A-Za-z0-9\.\-\_]", "", $rootPart);
						$curDir .= $theString . '/';
					}
				}
				else {
					$curDir = '';
					foreach ( $rootParts as $rootPart ) {
						$theString = @ereg_replace("[^A-Za-z0-9\.\-\_]", "", $rootPart);
						$curDir .= $theString . '/';
					}
				}
				$cleanRoot = $curDir;
			}
			
			if ( strlen($cleanRoot) < 1 || $cleanRoot == FALSE || !isset($cleanRoot) ) {
				throw new Exception('ERR: Not able to read/write to that file/directory' . $cleanRoot);
			}
			elseif ( is_dir($cleanRoot) ) {
				$this->_keyRoot = $cleanRoot;
				return true;
			} throw new Exception('ERR: Not able to read/write to that file/directory' . $cleanRoot);
		}
		
		/**
		 * 
		 * getKey will go into the keyfile and fetch the pair you are looking for.
		 * 
		 * @throws Exception
		 * @return Varies
		 */
		public function getKey() {
			// It was proposed to add strict var definitions in the function. Contemplating it.
			$Args = func_get_args();
			if ( is_array($Args) && @count($Args)>0) {
				$KeyRequest = @explode('.',$Args[0]);
				// Check to see if the file exists in the keydir and that the file is readable (new!).
				if ( file_exists($this->_keyDir . $KeyRequest[0] .'.key' && is_readable($this->_keyDir . $KeyRequest[0] .'.key')) ){
					// Check to see if there is a cache of the request;
					if ( isset($this->_cache[$KeyRequest[0]]) ) {
						/* A cache exists in memory, so we should use that.
						 * 
						 * Perhaps here we should look at adding this check above the file_exists to save some 
						 * time in the lookup and load.
						 */
						if ( isset($this->_cache[$KeyRequest[0]][$KeyRequest[1]] ) ) {
							return $this->_cache[$KeyRequest[0]][$KeyRequest[1]];
						}
						else {
							throw new Exception('ERR: Not able to find the key you reqested.');
						}
					}
					else {
						// Get the contents of the key file and load it into memory.
						$Data = file_get_contents($this->_keyDir . $KeyRequest[0] .'.key');
						// We use the same var, so we destroy the loaded file and replace it with an object (memory)
						$Data = json_decode($Data,true);
						if ( is_array($Data) && @count($Data) > 0 ) {
							$this->_cache[$KeyRequest[0]] = $Data;
							if ( isset($this->_cache[$KeyRequest[0]][$KeyRequest[1]] ) ) {
								return $this->_cache[$KeyRequest[0]][$KeyRequest[1]];
							}
							else {
								throw new Exception('ERR: Not able to find the key you reqested.');
							}
						}
					}
				}
				else {
					throw new Exception('ERR: The file you requested is not valid '.$this->_keyDir . $KeyRequest[0] .'.key or is unreadable.');
				}
			}
			return false;
		}
		
	}