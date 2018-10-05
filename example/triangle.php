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
	Shader\Program
};

$window = new Window;

// configure the window
$window->setHint(GLFW_CONTEXT_VERSION_MAJOR, 3);
$window->setHint(GLFW_CONTEXT_VERSION_MINOR, 3);
$window->setHint(GLFW_OPENGL_PROFILE, GLFW_OPENGL_CORE_PROFILE);
$window->setHint(GLFW_OPENGL_FORWARD_COMPAT, GL_TRUE);

// open it
$window->open('PHP GLFW Triangle');

// enable vsync
$window->setSwapInterval(1);

/**
 * Prepare Shaders
 */
$vertexShader = new Shader(Shader::VERTEX, "
#version 330 core
layout (location = 0) in vec3 position;
layout (location = 1) in vec3 color;

out vec4 pcolor;

void main()
{
    pcolor = vec4(color, 1.0f);
    gl_Position = vec4(position, 1.0f);
}
");

$fragmentShader = new Shader(Shader::FRAGMENT, "
#version 330 core
out vec4 fragment_color;
in vec4 pcolor;

void main()
{
    fragment_color = pcolor;
} 
");
$shader = new Program($vertexShader, $fragmentShader);
$shader->link();

// we created the shader program so we can free
// the sources
unset($vertexShader, $fragmentShader);

/**
 * Vertex creation
 */
// Buffers
$VBO; $VAO; 

// verticies
$verticies = [ 
     // positions      // colors
    0.5, -0.5, 0.0,  1.0, 0.0, 0.0,  // bottom right
   -0.5, -0.5, 0.0,  0.0, 1.0, 0.0,  // bottom let
    0.0,  0.5, 0.0,  0.0, 0.0, 1.0   // top 
];

glGenVertexArrays(1, $VAO);
glGenBuffers(1, $VBO);

glBindVertexArray($VAO);

glBindBuffer(GL_ARRAY_BUFFER, $VBO);
glBufferDataFloat(GL_ARRAY_BUFFER, $verticies, GL_STATIC_DRAW);

// positions
glVertexAttribPointer(0, 3, GL_FLOAT, GL_FALSE, 6, 0);
glEnableVertexAttribArray(0);

// colors
glVertexAttribPointer(1, 3, GL_FLOAT, GL_FALSE, 6, 3);
glEnableVertexAttribArray(1);

// unbind
glBindBuffer(GL_ARRAY_BUFFER, 0); 
glBindVertexArray(0); 

/**
 * Main loop
 */
while (!$window->shouldClose())
{
	$window->clearColor(0, 0, 0, 1);
	$window->clear(GL_COLOR_BUFFER_BIT);

    // use the shader
    $shader->use();

    // draw our vertex array
    glBindVertexArray($VAO);
    glDrawArrays(GL_TRIANGLES, 0, 3);

    // swap
    $window->swapBuffers();
    $window->pollEvents();
}

// stop & cleanup
glDeleteVertexArrays(1, $VAO);
glDeleteBuffers(1, $VBO);


