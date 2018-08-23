<?php  

namespace PGF\Component;

use glm\vec3;
use glm\mat4;

class Transform3D
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
	 * Does the tranformation matrix need an update?
	 *
	 * @var bool
	 */
	public $__transformDirty = true;

	/**
	 * The tranformation matrix cache of the entity.
	 *
	 * @var array
	 */
	public $__transformMatrix = null;

	/**
	 * Construct a new Tranformation
	 *
	 * @param vec3 				$position
	 * @param vec3 				$size
	 * @param vec3 				$rotation
	 */
	public function __construct(vec3 $position, vec3 $size, vec3 $rotation)
	{
		$this->position = $position;
		$this->size = $size;
		$this->rotation = $rotation;
	}

	public function getMatrix() : array
	{
		if ($this->__transformDirty || is_null($this->__transformMatrix)) 
		{
			$model = new \glm\mat4();
			$model = \glm\translate($model, $this->position);

			if ($this->rotation->z) {
				$model = \glm\rotate($model, $this->rotation->z, new \glm\vec3(0.0, 0.0, 1.0));
			} if ($this->rotation->y) {
				$model = \glm\rotate($model, $this->rotation->y, new \glm\vec3(0.0, 1.0, 0.0));
			} if ($this->rotation->x) {
				$model = \glm\rotate($model, $this->rotation->x, new \glm\vec3(1.0, 0.0, 0.0));
			}

			$model = \glm\scale($model, $this->size);
			$model = \glm\scale($model, \glm\vec3(1, 1, 1));

			$this->__transformMatrix = \glm\value_ptr($model);
			$this->__transformDirty = false;
		}

		return $this->__transformMatrix;
	}

	/**
	 * Calculates the current front vector 
	 *
	 * @return vec3
	 */
	public function calculateFrontVector() : vec3
	{
		$vec3 = new vec3(
			- (sin(\glm\radians($this->rotation->y)) * cos(\glm\radians($this->rotation->x))),
			sin(\glm\radians($this->rotation->x)),
			cos(\glm\radians($this->rotation->y)) * cos(\glm\radians($this->rotation->x))
		);

		// normalize
		$vec3->normalize();

		return $vec3;
	}

	/**
	 * Calculates the current front vector 
	 *
	 * @return vec3
	 */
	public function calculateRightVector() : vec3
	{
		$vec3 = \glm\cross($this->calculateFrontVector(), new vec3(0, 1, 0));

		// normalize
		$vec3->normalize();

		return $vec3;
	}

	/**
	 * Calculates the current front vector 
	 *
	 * @return vec3
	 */
	public function calculateUpVector() : vec3
	{
		$vec3 = \glm\cross($this->calculateRightVector(), $this->calculateFrontVector());

		// normalize
		$vec3->normalize();

		return $vec3;
	}

	/**
	 * Moves the object forward by the given value
	 *
	 * @param float 			$value
	 */
	public function moveForward(float $v)
	{
		$direction = $this->calculateFrontVector();

		$this->position->x -= ($direction->x * $v);
		$this->position->y -= ($direction->y * $v);
		$this->position->z -= ($direction->z * $v);
	}

	/**
	 * Moves the object forward by the given value
	 *
	 * @param float 			$value
	 */
	public function moveBackward(float $v)
	{
		$direction = $this->calculateFrontVector();

		$this->position->x += ($direction->x * $v);
		$this->position->y += ($direction->y * $v);
		$this->position->z += ($direction->z * $v);
	}

	/**
	 * Moves the object to the right of its direction
	 *
	 * @param float 			$value
	 */
	public function moveRight(float $v)
	{
		$direction = $this->calculateRightVector();

		$this->position->x -= ($direction->x * $v);
		$this->position->y -= ($direction->y * $v);
		$this->position->z -= ($direction->z * $v);
	}

	/**
	 * Moves the object to the right of its direction
	 *
	 * @param float 			$value
	 */
	public function moveLeft(float $v)
	{
		$direction = $this->calculateRightVector();

		$this->position->x += ($direction->x * $v);
		$this->position->y += ($direction->y * $v);
		$this->position->z += ($direction->z * $v);
	}

	/**
	 * Moves the object up of its direction
	 *
	 * @param float 			$value
	 */
	public function moveUp(float $v)
	{
		$direction = $this->calculateUpVector();

		$this->position->x += ($direction->x * $v);
		$this->position->y += ($direction->y * $v);
		$this->position->z += ($direction->z * $v);
	}

	/**
	 * Moves the object down of its direction
	 *
	 * @param float 			$value
	 */
	public function moveDown(float $v)
	{
		$direction = $this->calculateUpVector();

		$this->position->x -= ($direction->x * $v);
		$this->position->y -= ($direction->y * $v);
		$this->position->z -= ($direction->z * $v);
	}
}