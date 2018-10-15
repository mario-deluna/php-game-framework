<?php  

namespace PGF\Entity\Traits;

use glm\vec3;
use PGF\Texture\Texture;

trait Drawable3D
{	
	/**
	 * The name of the mesh to use
	 *
	 * @var string
	 */
	public $mesh;

	/**
	 * The name of the mesh to use
	 *
	 * @var Texture
	 */
	public $diffuseMap;

	/**
	 * The name of the mesh to use
	 *
	 * @var Texture
	 */
	public $sepcularMap;

	/**
	 * Scaling the texture on the object
	 */
	public $textureScaleX = 1.0;
	public $textureScaleY = 1.0;
}