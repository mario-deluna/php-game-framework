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

    Drawing\Drawer2D
};

$window = new Window;

// configure the window
$window->setHint(GLFW_CONTEXT_VERSION_MAJOR, 3);
$window->setHint(GLFW_CONTEXT_VERSION_MINOR, 3);
$window->setHint(GLFW_OPENGL_PROFILE, GLFW_OPENGL_CORE_PROFILE);
$window->setHint(GLFW_OPENGL_FORWARD_COMPAT, GL_TRUE);

// open it
$window->open('Simple 3D Example');

// enable vsync
$window->setSwapInterval(1);

/**
 * Prepare Shaders
 */
$vertexShader = new Shader(Shader::VERTEX, "
#version 330 core
layout (location = 0) in vec3 position;
layout (location = 1) in vec2 texture_coordinates;

out vec2 tcoords;

void main()
{
    gl_Position = vec4(position, 1.0f);
    tcoords = texture_coordinates;
}
");

$fragmentShader = new Shader(Shader::FRAGMENT, "
#version 330 core
out vec4 fragment_color;

in vec2 tcoords;

void main()
{
    fragment_color = vec4(1.0f, 1.0f, 1.0f, 1.0f);
}
");
$shader = new Program($vertexShader, $fragmentShader);
$shader->link();

// we created the shader program so we can free
// the sources
unset($vertexShader, $fragmentShader);

/**
 * Create drawer
 */
$drawer = new Drawer2D($shader);

/**
 * Main loop
 */
while (!$window->shouldClose())
{
	$window->clearColor(0, 0, 0, 1);
	$window->clear(GL_COLOR_BUFFER_BIT);

    $drawer->draw();

    // swap
    $window->swapBuffers();
    $window->pollEvents();
}

