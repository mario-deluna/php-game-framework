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

    Drawing\SimpleMesh
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

// enable depth testing
glEnable(GL_DEPTH_TEST);

/**
 * Prepare Shaders
 */
$vertexShader = new Shader(Shader::VERTEX, "
#version 330 core
layout (location = 0) in vec3 position;
layout (location = 1) in vec3 normalv;

uniform mat4 transform;
uniform mat4 view;
uniform mat4 projection;

out vec3 FragPos;
out vec3 FragNormals;

void main()
{
    gl_Position = projection * view * transform * vec4(position, 1.0f);
    FragPos = vec3(transform * vec4(position, 1.0));
    FragNormals = mat3(transpose(inverse(transform))) * normalv;
}
");

$fragmentShader = new Shader(Shader::FRAGMENT, "
#version 330 core

out vec4 fragment_color;

in vec3 FragPos;  
in vec3 FragNormals;  
  
void main()
{
    vec3 lightPos = vec3(13.0, 13.0, 13.0); 
    vec3 lightColor = vec3(1.0, 1.0, 0.9);
    vec3 objectColor = vec3(0.7, 0.7, 0.7);
    vec3 cameraPos = vec3(0.0, 0.0, -10.0);

    float specularStrength = 0.5;

    // ambient
    float ambientStrength = 0.1;
    vec3 ambient = ambientStrength * lightColor;
    
    // diffuse 
    vec3 norm = normalize(FragNormals);
    vec3 lightDir = normalize(lightPos - FragPos * 0.1);
    float diff = max(dot(norm, lightDir), 0.0);
    vec3 diffuse = diff * lightColor;
            
    vec3 result = (ambient + diffuse) * objectColor;
    fragment_color = vec4(result, 1.0);

    if (FragPos.x > 0) {
        fragment_color = vec4(objectColor, 1.0);
    }
} 
");
$shader = new Program($vertexShader, $fragmentShader);
$shader->link();

// we created the shader program so we can free
// the sources
unset($vertexShader, $fragmentShader);

/**
 * Create the mesh 
 */
$mesh = new SimpleMesh([
   1,          1,          -1,         0.577349,          0.577349,          -0.577349,
   -1,         -1,         -1,         -0.577349,         -0.577349,         -0.577349,
   -1,         1,          -1,         -0.577349,         0.577349,          -0.577349,
   -1,         1,          1,          -0.577349,         0.577349,          0.577349,
   0.999999,   -1.000001,  1,          0.577349,          -0.577349,         0.577349,
   1,          0.999999,   1,          0.577349,          0.577349,          0.577349,
   1,          0.999999,   1,          0.577349,          0.577349,          0.577349,
   1,          -1,         -1,         0.577349,          -0.577349,         -0.577349,
   1,          1,          -1,         0.577349,          0.577349,          -0.577349,
   0.999999,   -1.000001,  1,          0.577349,          -0.577349,         0.577349,
   -1,         -1,         -1,         -0.577349,         -0.577349,         -0.577349,
   1,          -1,         -1,         0.577349,          -0.577349,         -0.577349,
   -1,         -1,         -1,         -0.577349,         -0.577349,         -0.577349,
   -1,         1,          1,          -0.577349,         0.577349,          0.577349,
   -1,         1,          -1,         -0.577349,         0.577349,          -0.577349,
   1,          1,          -1,         0.577349,          0.577349,          -0.577349,
   -1,         1,          1,          -0.577349,         0.577349,          0.577349,
   1,          0.999999,   1,          0.577349,          0.577349,          0.577349,
   1,          1,          -1,         0.577349,          0.577349,          -0.577349,
   1,          -1,         -1,         0.577349,          -0.577349,         -0.577349,
   -1,         -1,         -1,         -0.577349,         -0.577349,         -0.577349,
   -1,         1,          1,          -0.577349,         0.577349,          0.577349,
   -1,         -1,         1,          -0.577349,         -0.577349,         0.577349,
   0.999999,   -1.000001,  1,          0.577349,          -0.577349,         0.577349,
   1,          0.999999,   1,          0.577349,          0.577349,          0.577349,
   0.999999,   -1.000001,  1,          0.577349,          -0.577349,         0.577349,
   1,          -1,         -1,         0.577349,          -0.577349,         -0.577349,
   0.999999,   -1.000001,  1,          0.577349,          -0.577349,         0.577349,
   -1,         -1,         1,          -0.577349,         -0.577349,         0.577349,
   -1,         -1,         -1,         -0.577349,         -0.577349,         -0.577349,
   -1,         -1,         -1,         -0.577349,         -0.577349,         -0.577349,
   -1,         -1,         1,          -0.577349,         -0.577349,         0.577349,
   -1,         1,          1,          -0.577349,         0.577349,          0.577349,
   1,          1,          -1,         0.577349,          0.577349,          -0.577349,
   -1,         1,          -1,         -0.577349,         0.577349,          -0.577349,
   -1,         1,          1,          -0.577349,         0.577349,          0.577349,

]);

/**
 * Main loop
 */
while (!$window->shouldClose())
{
	$window->clearColor(0, 0, 0, 1);
	$window->clear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);

    $shader->use();

    // create transformations
    $model = new glm\mat4();
    $view = new glm\mat4();
    $projection = new glm\mat4();
    $cameraPos = new glm\vec3(0.0, 0.0, -10.0);

    $model = glm\translate($model, glm\vec3(0.0, 0.0, 0.0));
    $model = glm\rotate($model, (float)glfwGetTime() * 20, new glm\vec3(0.0, 1.0, 0.0));
    $model = glm\rotate($model, (float)glfwGetTime() * 20, new glm\vec3(0.0, 0.0, 1.0));
    $view  = glm\translate($view, $cameraPos);
    $projection = glm\perspective(45.0, (float)1000 / (float)1000, 0.1, 100.0);

    glUniformMatrix4fv(glGetUniformLocation($shader->id(), "transform"), 1, false, glm\value_ptr($model));
    glUniformMatrix4fv(glGetUniformLocation($shader->id(), "view"), 1, false, glm\value_ptr($view));
    glUniformMatrix4fv(glGetUniformLocation($shader->id(), "projection"), 1, false, glm\value_ptr($projection));

    $mesh->draw();

    // swap
    $window->swapBuffers();
    $window->pollEvents();
}

