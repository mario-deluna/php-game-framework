<?php  

namespace PGF\Camera;

use PGF\Exception;

use glm\mat4;
use glm\vec3;

class PerspectiveCamera
{
    /** 
     * The curren projection Matrix
     *
     * @var glm\mat4
     */
    protected $projectionMatrix;

    /**
     * The Camera position evector
     *
     * @var glm\vec3
     */
    protected $position;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->
    }

    /**
     * recalculate the projection
     */
    public function updateProjection()
    {
        $this->projectionMatrix = glm\perspective(45.0, (float)1000 / (float)1000, 0.1, 100.0);
    }

    /**
     * Retrieve the current projection matrix
     *
     * @return mat4
     */
    public function getProjectionMatrx() : mat4
    {
        return $this->projectionMatrix;
    }
}