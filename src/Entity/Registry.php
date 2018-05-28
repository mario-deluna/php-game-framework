<?php  

namespace PGF\Entity;

class Registry 
{	
	/**
	 * The index of entity ids.
	 *
	 * @var int
	 */
	protected $index = 0;

	/**
	 * An array of all entities inside this registry
	 *
	 * @var array[int => Entity]
	 */
	protected $entities = [];

	/**
	 * A map of the trait classes to entities
	 *
	 * @var array
	 */
	protected $traitmap = [];

	/**
	 * A map of entities to the trait mappings
	 *
	 * @var array
	 */
	protected $entitymap = [];

	/**
	 * Add a entity to the registry
	 *
	 * @param Entity 			$entity
	 */
	public function add(Entity $entity)
	{
		// get the next id
		$id = $this->index; $this->index++;

		// assign the id
		$entity->id = $id;

		// add to main list 
		$this->entities[$id] = $entity;

		// map the traits
		$traits = class_uses($entity); sort($traits);

		// single mapping
		$lastTrait = "";
		foreach($traits as $trait)
		{
			$this->entitymap[$id][] = $trait;
			$this->traitmap[$trait][$id] = $entity;

			// second layer
			foreach($traits as $trait2) {
				if ($trait === $trait2) {
					continue;
				}

				$this->entitymap[$id][] = $trait . $trait2;
				$this->traitmap[$trait . $trait2][$id] = $entity;

				// third layer
				foreach($traits as $trait3) {
					if ($trait === $trait3 || $trait2 === $trait3) {
						continue;
					}

					$this->entitymap[$id][] = $trait . $trait2 . $trait3;
					$this->traitmap[$trait . $trait2 . $trait3][$id] = $entity;
				}
			}
		}
	}

	/**
	 * Fetch entities by their traits
	 *
	 * @param ...array[string]
	 */
	public function fetch(...$traits) : array 
	{
		sort($traits); return $this->traitmap[implode("", $traits)] ?? [];
	}
}