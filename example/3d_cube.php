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

use PGF\{
	Window, 

	Shader\Shader,
	Shader\Program,
    
    Texture\Texture
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
 * Create drawer
 */
$drawer = new Drawer2D($window);

/**
 * Create the texture
 */
$texture = new Texture(__DIR__ . '/images/test.png');

/**
 * Main loop
 */
while (!$window->shouldClose())
{
	$window->clearColor(0, 0, 0, 1);
	$window->clear(GL_COLOR_BUFFER_BIT);

    $drawer->draw(10, 10, 780, 580, $texture);

    // swap
    $window->swapBuffers();
    $window->pollEvents();
}

