<?php  

namespace PGF\System;

use PGF\Shader\Program;

use PGF\Mesh\TexturedMesh;

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
	protected $shader = null;

	protected $tmpMesh;

	/**
	 * Construct
	 */
	public function __construct(Program $shader)
	{
		$this->shader = $shader;
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
	 * @param array 			$entities
	 */
	public function update(array $entities) {}

	/**
	 * Draw the given entities
	 *
	 * @param array 			$entities
	 */
	public function draw(array $entities)
	{
		$this->shader->use();

	    $view = new \glm\mat4();
	    $view  = \glm\translate($view, new \glm\vec3(0.0, 0.0, -10.0));

	    $projection = new \glm\mat4();
	    $projection = \glm\perspective(45.0, (float)800 / (float)600, 0.1, 100.0);

	    glUniformMatrix4fv(glGetUniformLocation($this->shader->id(), "view"), 1, false, \glm\value_ptr($view));
	    glUniformMatrix4fv(glGetUniformLocation($this->shader->id(), "projection"), 1, false, \glm\value_ptr($projection));

		foreach($entities as $entity)
		{
			if ($entity instanceof Drawable3D && $entity instanceof Transform3D) 
			{
				$model = new \glm\mat4();
				$model = \glm\translate($model, $entity->position);

				if ($entity->rotation->x) {
					$model = \glm\rotate($model, $entity->rotation->x, new \glm\vec3(1.0, 0.0, 0.0));
				} if ($entity->rotation->y) {
					$model = \glm\rotate($model, $entity->rotation->y, new \glm\vec3(0.0, 1.0, 0.0));
				} if ($entity->rotation->z) {
					$model = \glm\rotate($model, $entity->rotation->z, new \glm\vec3(0.0, 0.0, 1.0));
				}

				$model = \glm\scale($model, $entity->scale);

				// set the tranformation uniform
	    		glUniformMatrix4fv(glGetUniformLocation($this->shader->id(), "transform"), 1, false, \glm\value_ptr($model));

	    		// load the mesh
	    		$this->tmpMesh->draw();
			}
		}
	}
}