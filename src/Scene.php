<?php

namespace PGF;

use PGF\Entity\Entity;
use PGF\System\System;

class Scene
{
	/**
	 * An array of entities in this scene
	 *
	 * @var array[Entity]
	 */
	protected $entities = [];

	/**
	 * An array of systems
	 *
	 * @var array[System]
	 */
	protected $systems = [];

	/**
	 * Adds the given entity to the scene
	 *
	 * @param Entity 			$entity
	 */
	public function addEntity(Entity $entity)
	{
		$this->entities[] = $entity;
	}

	/**
	 * Add a system
	 *
	 * @param System 			$system
	 */
	public function addSystem(System $system)
	{
		$this->systems[] = $system;
	}

	/**
	 * Update all entiteies
	 */
	public function update()
	{
		foreach($this->systems as $system)
		{
			$system->update($this->entities);
		}
	}

	/**
	 * Draw all entities
	 */
	public function draw()
	{
		foreach($this->systems as $system)
		{
			$system->draw($this->entities);
		}
	}
}