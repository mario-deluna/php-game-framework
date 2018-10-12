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
$camera = new PerspectiveCamera(new vec3(0.0, 0.0, 7.0));

/**
 * Create basic 3D Shader
 */
$shader = new Simple3DShader();
$shader->use();
$shader->setProjectionMatrx(\glm\value_ptr($camera->getProjectionMatrx()));
$shader->setViewMatrx(\glm\value_ptr($camera->getViewMatrix()));
$shader->setLightPosition(\glm\vec3(50, 50, 50));

/**
 * Get a cube
 */
$cube = (new MeshManager)->get('primitives.cube');

/**
 * create the transforms
 */
$transform1 = new Transform3D(
	new vec3(-1.7, 0.0, 0.0),
	new vec3(1.0, 1.0, 1.0),
	new vec3(0.0, 0.0, 0.0)
);

$transform2 = new Transform3D(
    new vec3(1.7, 0.0, 0.0),
    new vec3(1.0, 1.0, 1.0),
    new vec3(0.0, 0.0, 0.0)
);

// enable deph test
glEnable(GL_DEPTH_TEST);

// load the test textures
$texture1 = new Texture(__DIR__ . '/images/tiles_diff.jpg');
$texture1_specular = new Texture(__DIR__ . '/images/tiles_spec.jpg');

$texture2 = new Texture(__DIR__ . '/images/planks_diff.jpg');
$texture2_specular = new Texture(__DIR__ . '/images/planks_spec.jpg');

/**
 * Main loop
 */
while (!$window->shouldClose())
{
	$window->clearColor(0, 0, 0, 1);
	$window->clear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);

	// set the transformation matrix
    $transform1->rotation->y += 0.8;
    $transform1->rotation->x += 0.9;
    $transform1->__transformDirty = true; 
	$shader->setTransformationMatrix($transform1->getMatrix());

    if ($window->getKeyState(GLFW_KEY_S) === GLFW_PRESS) {
        $shader->setTexture($texture1, null);
    } else {
        $shader->setTexture($texture1, $texture1_specular);
    }

    $shader->uniform1f('shininess', 10);
    $shader->uniform1f('specular_strength', 0.4);

	// draw the cube 1
   	$cube->draw();

    // set the transformation matrix
    $transform2->rotation->y -= 0.8;
    $transform2->rotation->x -= 0.9;
    $transform2->__transformDirty = true; 
    $shader->setTransformationMatrix($transform2->getMatrix());

    if ($window->getKeyState(GLFW_KEY_S) === GLFW_PRESS) {
        $shader->setTexture($texture2, null);
    } else {
        $shader->setTexture($texture2, $texture2_specular);
    }

    $shader->uniform1f('shininess', 5);
    $shader->uniform1f('specular_strength', 1.2);

    // draw the cube 2
    $cube->draw();

    // swap
    $window->swapBuffers();
    $window->pollEvents();
}

