<?php  

namespace PGF\Entity\Traits;

use glm\vec3;

trait Transform3D
{	
	/**
	 * The position vector
	 *
	 * @var vec3
	 */
	public $position;

	/**
	 * The size vector
	 *
	 * @var vec3
	 */
	public $size;

	/**
	 * The rotation vector
	 *
	 * @var vec3
	 */
	public $rotation;

	/**
	 * Returns the position vector
	 *
	 * @return vec3
	 */
	public function getPosition() : vec3
	{
		return $this->position;
	}

	/**
	 * Returns the size vector
	 *
	 * @return vec3
	 */
	public function getSize() : vec3
	{
		return $this->size;
	}

	/**
	 * Returns the rotation vector
	 *
	 * @return vec3
	 */
	public function getRotation() : vec3
	{
		return $this->rotation;
	}
}