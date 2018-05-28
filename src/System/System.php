<?php  

namespace PGF\System;

use PGF\Entity\Registry;

abstract class System
{
	/**
	 * Update the given entities
	 *
	 * @param Registry 			$entities
	 */
	abstract public function update(Registry $entities);

	/**
	 * Draw the given entities
	 *
	 * @param Registry 			$entities
	 */
	abstract public function draw(Registry $entities);
}