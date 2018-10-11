<?php 
if (!defined('DS')) { define('DS', DIRECTORY_SEPARATOR); }
/**
 *---------------------------------------------------------------
 * Autoloader / Compser
 *---------------------------------------------------------------
 *
 * We need to access our dependencies & autloader..
 */
require __DIR__ . DS . '..' . DS . 'vendor' . DS . 'autoload.php';

use glm\vec3;

use PGF\{
	Window, 
    Texture\Texture,
    Mesh\MeshManager,
    Shaders\Simple3DShader,
    Camera\PerspectiveCamera,

    Component\Transform3D
};

$window = new Window;

// configure the window
$window->setHint(GLFW_CONTEXT_VERSION_MAJOR, 3);
$window->setHint(GLFW_CONTEXT_VERSION_MINOR, 3);
$window->setHint(GLFW_OPENGL_PROFILE, GLFW_OPENGL_CORE_PROFILE);
$window->setHint(GLFW_OPENGL_FORWARD_COMPAT, GL_TRUE);

// open it
$window->open('3D Cube');

// enable vsync
$window->setSwapInterval(1);

/**
 * Create a camera
 */
$camera = new PerspectiveCamera(new vec3(0.0, 0.0, 5.0));

/**
 * Create basic 3D Shader
 */
$shader = new Simple3DShader();
$shader->use();
$shader->setProjectionMatrx(\glm\value_ptr($camera->getProjectionMatrx()));
$shader->setViewMatrx(\glm\value_ptr($camera->getViewMatrix()));

/**
 * Get a cube
 */
$cube = (new MeshManager)->get('primitives.cube');

/**
 * create a transform 
 */
$transform = new Transform3D(
	new vec3(0.0, 0.0, 0.0),
	new vec3(1.0, 1.0, 1.0),
	new vec3(0.0, 0.0, 0.0)
);

// enable deph test
glEnable(GL_DEPTH_TEST);

// load the test texture
$texture = new Texture(__DIR__ . '/images/test.png');

/**
 * Main loop
 */
while (!$window->shouldClose())
{
	$window->clearColor(0, 0, 0, 1);
	$window->clear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);

	// set the transformation matrix
    $transform->rotation->y += 0.8;
    $transform->rotation->x += 0.9;
    $transform->__transformDirty = true; 
	$shader->setTransformationMatrix($transform->getMatrix());

    $shader->setTexture($texture);

	// draw the cube
   	$cube->draw();

    // swap
    $window->swapBuffers();
    $window->pollEvents();
}

