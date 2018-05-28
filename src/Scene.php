<?php

namespace PGF;

use PGF\Entity\Entity;
use PGF\Entity\Registry;
use PGF\System\System;

class Scene
{
	/**
	 * An entity registry
	 *
	 * @var Registry
	 */
	public $entities;

	/**
	 * An array of systems
	 *
	 * @var array[System]
	 */
	protected $systems = [];

	/**
	 * Scene constructor
	 */
	public function __construct()
	{
		$this->entities = new Registry;
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