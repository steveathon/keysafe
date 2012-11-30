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

	class phpKeySafe {
		private $_keyDir;
		private $_cache;
		
		public function __construct($KeyDir = NULL) {
			if ( !isset($KeyDir) ||  !is_dir($KeyDir) ) {
				throw new Exception('ERR: Not able to read/write to that file/directory' . $KeyDir);
			}
			else {
				$this->_keyDir = $KeyDir;
			}
		}
		
		public function getKey() {
			$Args = func_get_args();
			if ( is_array($Args) && @count($Args)>0) {
				$KeyRequest = @explode('.',$Args[0]);
				if ( file_exists($this->_keyDir . $KeyRequest[0] .'.key') ){
					if ( isset($this->_cache[$KeyRequest[0]]) ) {
						// Use the stored key.
						if ( isset($this->_cache[$KeyRequest[0]][$KeyRequest[1]] ) ) {
							return $this->_cache[$KeyRequest[0]][$KeyRequest[1]];
						}
						else {
							throw new Exception('ERR: Not able to find the key you reqested.');
						}
					}
					else {
						$Data = file_get_contents($this->_keyDir . $KeyRequest[0] .'.key');
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
					throw new Exception('ERR: The file you requested is not valid'.$this->_keyDir . $KeyRequest[0] .'.key');
				}
			}
			return false;
		}
		
	}