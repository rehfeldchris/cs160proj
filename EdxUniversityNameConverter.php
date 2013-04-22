<?php

/**
 * Converts Edx short X names to normal university names
 * 
 * @author Tatiana Braginets
 */

require_once 'UnknownUniversityNameException.php';

class EdxUniversityNameConverter 
{
	private static $names = array (
			'MITx' => 'Massachusetts Institute of Technology',
			'HarvardX' => 'Harvard University',
			'BerkeleyX' => 'University of California, Berkeley',
			'UTx' => 'The University of Texas System',
			'McGillX' => 'McGill University',
			'ANUx' => 'Australian National University',
			'WellesleyX' => 'Wellesley College',
			'GeorgetownX' => 'Georgetown University',
			'University of TorontoX' => 'University of Toronto',
			'EPFLx' => 'École Polytechnique Fédérale de Lausanne',
			'DelftX' => 'Delft University of Technology',
			'RiceX' => 'Rice University',
			'UTAustinX' => 'University of Texas at Austin'
	);
	
	/**
	 * Converts edX short university name to full name
	 * 
	 * @param string edX short university name
	 * @return string full university name
	 * @throws UnknownUniversityNameException
	 */
	public static function convert($name) 
	{
		if(!key_exists($name, EdxUniversityNameConverter::$names)) {
			throw new UnknownUniversityNameException("unkown university name on edx");
		} else {
			return EdxUniversityNameConverter::$names[$name];
		}
	}
}



?>