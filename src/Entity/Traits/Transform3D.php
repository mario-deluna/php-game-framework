<?php  

namespace PGF\Entity\Traits;

use glm\vec3;
use PGF\Component\Transform3D as TransformComponent;

trait Transform3D
{	
	/**
	 * Tranformation instance
	 *
	 * @var TransformComponent
	 */
	public $transform;

	/**
	 * Initialize the tranformation component
	 *
	 * @param vec3 				$position
	 * @param vec3 				$size
	 * @param vec3 				$rotation
	 */
	public function initializeTransform(vec3 $position, vec3 $size, vec3 $rotation)
	{
		$this->transform = new TransformComponent($position, $size, $rotation);
	}
}