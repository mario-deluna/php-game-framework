<?php  

namespace PGF\System;

abstract class System
{
	/**
	 * Update the given entities
	 *
	 * @param array 			$entities
	 */
	abstract public function update(array $entities);

	/**
	 * Draw the given entities
	 *
	 * @param array 			$entities
	 */
	abstract public function draw(array $entities);
}