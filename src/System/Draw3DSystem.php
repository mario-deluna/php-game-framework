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

		$this->tmpMesh = new TexturedMesh([
    1,          1,          -1,         0.577349,          0.577349,          -0.577349,         0,          0,
    -1,         -1,         -1,         -0.577349,         -0.577349,         -0.577349,         1,          1,
    -1,         1,          -1,         -0.577349,         0.577349,          -0.577349,         0,          1,
    -1,         1,          1,          -0.577349,         0.577349,          0.577349,          1,          0,
    1,          -1,         1,          0.577349,          -0.577349,         0.577349,          0,          1,
    1,          1,          1,          0.577349,          0.577349,          0.577349,          0,          0,
    1,          1,          1,          0.577349,          0.577349,          0.577349,          1,          0,
    1,          -1,         -1,         0.577349,          -0.577349,         -0.577349,         0,          1,
    1,          1,          -1,         0.577349,          0.577349,          -0.577349,         0,          0,
    1,          -1,         1,          0.577349,          -0.577349,         0.577349,          1,          0,
    -1,         -1,         -1,         -0.577349,         -0.577349,         -0.577349,         0,          1,
    1,          -1,         -1,         0.577349,          -0.577349,         -0.577349,         0,          0,
    -1,         -1,         -1,         -0.577349,         -0.577349,         -0.577349,         0,          0,
    -1,         1,          1,          -0.577349,         0.577349,          0.577349,          1,          1,
    -1,         1,          -1,         -0.577349,         0.577349,          -0.577349,         0,          1,
    1,          1,          -1,         0.577349,          0.577349,          -0.577349,         1,          0,
    -1,         1,          1,          -0.577349,         0.577349,          0.577349,          0,          1,
    1,          1,          1,          0.577349,          0.577349,          0.577349,          0,          0,
    1,          1,          -1,         0.577349,          0.577349,          -0.577349,         0,          0,
    1,          -1,         -1,         0.577349,          -0.577349,         -0.577349,         1,          0,
    -1,         -1,         -1,         -0.577349,         -0.577349,         -0.577349,         1,          1,
    -1,         1,          1,          -0.577349,         0.577349,          0.577349,          1,          0,
    -1,         -1,         1,          -0.577349,         -0.577349,         0.577349,          1,          1,
    1,          -1,         1,          0.577349,          -0.577349,         0.577349,          0,          1,
    1,          1,          1,          0.577349,          0.577349,          0.577349,          1,          0,
    1,          -1,         1,          0.577349,          -0.577349,         0.577349,          1,          1,
    1,          -1,         -1,         0.577349,          -0.577349,         -0.577349,         0,          1,
    1,          -1,         1,          0.577349,          -0.577349,         0.577349,          1,          0,
    -1,         -1,         1,          -0.577349,         -0.577349,         0.577349,          1,          1,
    -1,         -1,         -1,         -0.577349,         -0.577349,         -0.577349,         0,          1,
    -1,         -1,         -1,         -0.577349,         -0.577349,         -0.577349,         0,          0,
    -1,         -1,         1,          -0.577349,         -0.577349,         0.577349,          1,          0,
    -1,         1,          1,          -0.577349,         0.577349,          0.577349,          1,          1,
    1,          1,          -1,         0.577349,          0.577349,          -0.577349,         1,          0,
    -1,         1,          -1,         -0.577349,         0.577349,          -0.577349,         1,          1,
    -1,         1,          1,          -0.577349,         0.577349,          0.577349,          0,          1,
]);
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

		foreach($entities->fetch(Drawable3D::class, Transform3D::class) as $entity)
		{
			// $entity->rotation->x += 1;
			// $entity->rotation->y += 1;

			$model = new \glm\mat4();
			$model = \glm\translate($model, $entity->position);

			if ($entity->rotation->x) {
				$model = \glm\rotate($model, $entity->rotation->x, new \glm\vec3(1.0, 0.0, 0.0));
			} if ($entity->rotation->y) {
				$model = \glm\rotate($model, $entity->rotation->y, new \glm\vec3(0.0, 1.0, 0.0));
			} if ($entity->rotation->z) {
				$model = \glm\rotate($model, $entity->rotation->z, new \glm\vec3(0.0, 0.0, 1.0));
			}

			$model = \glm\scale($model, $entity->size);

			// set the tranformation uniform
    		glUniformMatrix4fv(glGetUniformLocation($this->shader->id(), "transform"), 1, false, \glm\value_ptr($model));

    		// load the mesh
    		$this->tmpMesh->draw();
		}
	}
}