<?php  

namespace PGF\System;

use PGF\Shader\Program;
use PGF\Mesh\TexturedMesh;
use PGF\Camera\PerspectiveCamera;
use PGF\Entity\Registry;
use PGF\Entity\Traits\{
	Drawable3D,
	Transform3D
};

class Draw3DSystem extends System
{
	/**
	 * The shader program
	 *
	 * @var Program
	 */
	protected $shader;

	/**
	 * The drawers camera
	 *
	 * @var PerspectiveCamera
	 */
	protected $camera;

	protected $tmpMesh;

	/**
	 * Construct
	 */
	public function __construct(Program $shader, PerspectiveCamera $camera)
	{
		$this->shader = $shader;
		$this->camera = $camera;

		$this->tmpMesh = new TexturedMesh(require PATH_RESOURCES . DS . 'meshes/pistol.php');

// 		$this->tmpMesh = new TexturedMesh([
//     1,          1,          -1,         0.577349,          0.577349,          -0.577349,         0,          0,
//     -1,         -1,         -1,         -0.577349,         -0.577349,         -0.577349,         1,          1,
//     -1,         1,          -1,         -0.577349,         0.577349,          -0.577349,         0,          1,
//     -1,         1,          1,          -0.577349,         0.577349,          0.577349,          1,          0,
//     1,          -1,         1,          0.577349,          -0.577349,         0.577349,          0,          1,
//     1,          1,          1,          0.577349,          0.577349,          0.577349,          0,          0,
//     1,          1,          1,          0.577349,          0.577349,          0.577349,          1,          0,
//     1,          -1,         -1,         0.577349,          -0.577349,         -0.577349,         0,          1,
//     1,          1,          -1,         0.577349,          0.577349,          -0.577349,         0,          0,
//     1,          -1,         1,          0.577349,          -0.577349,         0.577349,          1,          0,
//     -1,         -1,         -1,         -0.577349,         -0.577349,         -0.577349,         0,          1,
//     1,          -1,         -1,         0.577349,          -0.577349,         -0.577349,         0,          0,
//     -1,         -1,         -1,         -0.577349,         -0.577349,         -0.577349,         0,          0,
//     -1,         1,          1,          -0.577349,         0.577349,          0.577349,          1,          1,
//     -1,         1,          -1,         -0.577349,         0.577349,          -0.577349,         0,          1,
//     1,          1,          -1,         0.577349,          0.577349,          -0.577349,         1,          0,
//     -1,         1,          1,          -0.577349,         0.577349,          0.577349,          0,          1,
//     1,          1,          1,          0.577349,          0.577349,          0.577349,          0,          0,
//     1,          1,          -1,         0.577349,          0.577349,          -0.577349,         0,          0,
//     1,          -1,         -1,         0.577349,          -0.577349,         -0.577349,         1,          0,
//     -1,         -1,         -1,         -0.577349,         -0.577349,         -0.577349,         1,          1,
//     -1,         1,          1,          -0.577349,         0.577349,          0.577349,          1,          0,
//     -1,         -1,         1,          -0.577349,         -0.577349,         0.577349,          1,          1,
//     1,          -1,         1,          0.577349,          -0.577349,         0.577349,          0,          1,
//     1,          1,          1,          0.577349,          0.577349,          0.577349,          1,          0,
//     1,          -1,         1,          0.577349,          -0.577349,         0.577349,          1,          1,
//     1,          -1,         -1,         0.577349,          -0.577349,         -0.577349,         0,          1,
//     1,          -1,         1,          0.577349,          -0.577349,         0.577349,          1,          0,
//     -1,         -1,         1,          -0.577349,         -0.577349,         0.577349,          1,          1,
//     -1,         -1,         -1,         -0.577349,         -0.577349,         -0.577349,         0,          1,
//     -1,         -1,         -1,         -0.577349,         -0.577349,         -0.577349,         0,          0,
//     -1,         -1,         1,          -0.577349,         -0.577349,         0.577349,          1,          0,
//     -1,         1,          1,          -0.577349,         0.577349,          0.577349,          1,          1,
//     1,          1,          -1,         0.577349,          0.577349,          -0.577349,         1,          0,
//     -1,         1,          -1,         -0.577349,         0.577349,          -0.577349,         1,          1,
//     -1,         1,          1,          -0.577349,         0.577349,          0.577349,          0,          1,
// ]);
	}

	/**
	 * Update the given entities
	 *
	 * @param Registry 			$entities
	 */
	public function update(Registry $entities) {}

	/**
	 * Draw the given entities
	 *
	 * @param Registry 			$entities
	 */
	public function draw(Registry $entities)
	{
		$this->shader->use();

	    glUniformMatrix4fv(glGetUniformLocation($this->shader->id(), "view"), 1, false, \glm\value_ptr($this->camera->getViewMatrix()));
	    glUniformMatrix4fv(glGetUniformLocation($this->shader->id(), "projection"), 1, false, \glm\value_ptr($this->camera->getProjectionMatrx()));


		// set the time
		glUniform1f(glGetUniformLocation($this->shader->id(), "time"), glfwGetTime());

		foreach($entities->fetch(Drawable3D::class, Transform3D::class) as $entity)
		{
			// set the tranformation uniform
    		glUniformMatrix4fv(glGetUniformLocation($this->shader->id(), "transform"), 1, false, $entity->transform->getMatrix());

    		// load the mesh
    		$this->tmpMesh->draw();
		}
	}
}