<?php
	/*
	 * Currently there are two simple functions - set and get
	 * The data is stored in a flat text-file in the cache directory
	 * Currently only the describe function of the SQLQuery class uses this cache function.
	 */
	class Cache{
		function get($fileName){
			$fileName = ROOT.DS.'tmp'.DS.'cache'.DS.$fileName;
			if(file_exists($fileName)){
				$handle = fopen($fileName, 'rb'); // open the file; r = read only mode -- b = force binary mode, which will not translate your data
				/*
				 * fread = Binary-safe file read
				 * reads up to "length" bytes from the file pointer referenced by handle.
				 * Reading stops as soon as one of the following conditions is met:
				 * 		length bytes have been read
				 * 		EOF (end of file) is reached
				 * 		a packet becomes available or the socket timeout occurs
				 * 		if the stream is read buffered and it does not represent a plain file, at most one read of up to a number of
				 * 			bytes equal to the chunk size (usually 8192) is made; depending on the previously buffered data, the size
				 * 			of the returned data may be larger than the chunk size.
				 */
				$variable = fread($handle, filesize($fileName));
				fclose($handle);

				// base64_encode: encodes data using the base64 algorithm and returns the encoded data
				return unserialize(base64_decode($variable)); // Creates a PHP value from a stored representation
			}else{
				return null;
			}
		}

		function set($fileName,$variable){
			$fileName = ROOT.DS.'tmp'.DS.'cache'.DS.$fileName;
			$handle = fopen($fileName, 'a');
			// It turns out that if there's a ", ', :, or ; in any of the values the serialization gets corrupted

			// As a fix, you must use the "base64_encode" function
			// base64_encode: encodes data using the base64 algorithm and returns the encoded data
			fwrite($handle, base64_encode(serialize($variable)));
			fclose($handle);
		}
	}
