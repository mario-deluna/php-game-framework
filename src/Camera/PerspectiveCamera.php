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
     * @var mat4
     */
    protected $projectionMatrix;

    /**
     * The Camera position evector
     *
     * @var vec3
     */
    public $position;

    /**
     * The up vector
     *
     * @var vec3
     */
    public $upVector;

    /**
     * The up vector
     *
     * @var vec3
     */
    public $worldUpVector;

    /**
     * The front vector
     *
     * @var vec3
     */
    public $frontVector;

    /**
     * The right vector
     *
     * @var vec3
     */
    public $rightVector;

    /**
     * Euler angles 
     */
    public $pitch = 0.0;
    public $yaw = -90.0;

    /**
     * Current screen dimensions
     *
     * @var int
     */
    private $screenWidth = 800;
    private $screenHeight = 600;

    /**
     * Construct
     */
    public function __construct(?vec3 $position = null)
    {
        $this->updateProjection();

        $this->position = $position ?? new vec3(0.0, 0.0, 0.0);
        $this->worldUpVector = new vec3(0.0, 1.0, 0.0);
        $this->frontVector = new vec3(0.0, 0.0, -1.0);

        $this->updateVectors();
    }

    public function getViewMatrix() : mat4
    {
        return \glm\lookAt($this->position, new vec3(
            $this->position->x + $this->frontVector->x, 
            $this->position->y + $this->frontVector->y,
            $this->position->z + $this->frontVector->z
        ), $this->upVector);
    }

    /**
     * recalculate the projection
     */
    public function updateProjection()
    {
        $this->projectionMatrix = \glm\perspective(45.0, (float)$this->screenWidth / (float)$this->screenHeight, 0.1, 2000.0);
    }

    /**
     * Set the euler angles (yaw & ptich)
     *
     * @param float             $yaw
     * @param float             $pitch
     */
    public function setAngel(float $yaw, float $pitch)
    {
        $this->yaw = $yaw;
        $this->pitch = $pitch;

        if ($this->pitch > 89.0) {
            $this->pitch = 89.0;
        } elseif ($this->pitch < -89.0) {
            $this->pitch = -89.0;
        }

        $this->updateVectors();
    }

    /**
     * Set the euler angles (yaw & ptich)
     *
     * @param float             $yoffset
     * @param float             $poffset
     */
    public function updateAngel(float $yoffset, float $poffset)
    {
        $this->setAngel($this->yaw + $yoffset, $this->pitch + $poffset);
    }

    /**
     * Move the camera backward
     */
    public function moveBackward(float $v = 1.0)
    {
        $this->position = new vec3(
            $this->position->x - ($this->frontVector->x * $v), 
            $this->position->y - ($this->frontVector->y * $v),
            $this->position->z - ($this->frontVector->z * $v)
        );
    }

    /**
     * Move the camera backward
     */
    public function moveForward(float $v = 1.0)
    {
        $this->position = new vec3(
            $this->position->x + ($this->frontVector->x * $v), 
            $this->position->y + ($this->frontVector->y * $v),
            $this->position->z + ($this->frontVector->z * $v)
        );
    }

    /**
     * Move the camera backward
     */
    public function moveRight(float $v = 1.0)
    {
        $this->position = new vec3(
            $this->position->x + ($this->rightVector->x * $v), 
            $this->position->y + ($this->rightVector->y * $v),
            $this->position->z + ($this->rightVector->z * $v)
        );
    }

    /**
     * Move the camera backward
     */
    public function moveLeft(float $v = 1.0)
    {
        $this->position = new vec3(
            $this->position->x - ($this->rightVector->x * $v), 
            $this->position->y - ($this->rightVector->y * $v),
            $this->position->z - ($this->rightVector->z * $v)
        );
    }

    /**
     * Update the camera vectors
     */
    public function updateVectors()
    {
        $front = new vec3();
        $front->x = cos(\glm\radians($this->yaw)) * cos(\glm\radians($this->pitch));
        $front->y = sin(\glm\radians($this->pitch));
        $front->z = sin(\glm\radians($this->yaw)) * cos(\glm\radians($this->pitch));

        $this->frontVector = \glm\normalize($front);
        $this->rightVector = \glm\normalize(\glm\cross($this->frontVector, $this->worldUpVector));
        $this->upVector = \glm\normalize(\glm\cross($this->rightVector, $this->frontVector));
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

    /**
     * Prints some camera information to the screen
     */
    public function printDebug()
    {
        echo "----\n";
        echo " Pitch: $this->pitch\n";
        echo " Yaw: $this->yaw\n";
        echo " postion: $this->position\n";
        echo " front: $this->frontVector\n";
    }
}