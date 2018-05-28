<?php  

namespace PGF\Entity\Primitives;

use PGF\Entity\Entity;

use glm\vec3;

class Cube extends Entity
{
	use \PGF\Entity\Traits\Transform3D;
	use \PGF\Entity\Traits\Drawable3D;

	/**
	 * Construct the cube
	 */
	public function __construct(vec3 $position, vec3 $scale, vec3 $rotation)
	{
		$this->position = $position;
		$this->size = $scale;
		$this->rotation = $rotation;

		$this->mesh = 'cube';
	}
}