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

    Drawing\TexturedMesh
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
layout (location = 2) in vec2 text_coords;

uniform mat4 transform;
uniform mat4 view;
uniform mat4 projection;

out vec3 FragPos;
out vec3 FragNormals;
out vec2 tCoords;

void main()
{
    gl_Position = projection * view * transform * vec4(position, 1.0f);
    FragPos = vec3(transform * vec4(position, 1.0));
    FragNormals = mat3(transpose(inverse(transform))) * normalv;
    tCoords = text_coords;
}
");

$fragmentShader = new Shader(Shader::FRAGMENT, "
#version 330 core

out vec4 fragment_color;

in vec3 FragPos;  
in vec3 FragNormals;
in vec2 tCoords;

uniform sampler2D texture1;
  
void main()
{
    vec4 textcol = texture(texture1, tCoords);
    vec3 lightPos = vec3(13.0, 13.0, 13.0); 
    vec3 lightColor = vec3(1.0, 1.0, 0.9);
    vec3 objectColor = vec3(0.7, 0.7, 0.7) * vec3(textcol.x, textcol.y, textcol.z);
    vec3 cameraPos = vec3(0.0, 0.0, -10.0);

    float specularStrength = 0.5;

    // ambient
    float ambientStrength = 0.5;
    vec3 ambient = ambientStrength * lightColor;
    
    // diffuse 
    vec3 norm = normalize(FragNormals);
    vec3 lightDir = normalize(lightPos - FragPos * 0.1);
    float diff = max(dot(norm, lightDir), 0.0);
    vec3 diffuse = diff * lightColor;
            
    vec3 result = (ambient + diffuse) * objectColor;
    fragment_color = vec4(result, 1.0);
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
// $cube = new TexturedMesh([
//    1,          1,          -1,         0.577349,          0.577349,          -0.577349,         0,          0,
//    -1,         -1,         -1,         -0.577349,         -0.577349,         -0.577349,         1,          1,
//    -1,         1,          -1,         -0.577349,         0.577349,          -0.577349,         0,          1,
//    -1,         1,          1,          -0.577349,         0.577349,          0.577349,          1,          0,
//    0.999999,   -1.000001,  1,          0.577349,          -0.577349,         0.577349,          0,          1,
//    1,          0.999999,   1,          0.577349,          0.577349,          0.577349,          0,          0,
//    1,          0.999999,   1,          0.577349,          0.577349,          0.577349,          1,          0,
//    1,          -1,         -1,         0.577349,          -0.577349,         -0.577349,         0,          1,
//    1,          1,          -1,         0.577349,          0.577349,          -0.577349,         0,          0,
//    0.999999,   -1.000001,  1,          0.577349,          -0.577349,         0.577349,          1,          0,
//    -1,         -1,         -1,         -0.577349,         -0.577349,         -0.577349,         0,          1,
//    1,          -1,         -1,         0.577349,          -0.577349,         -0.577349,         0,          0,
//    -1,         -1,         -1,         -0.577349,         -0.577349,         -0.577349,         0,          0,
//    -1,         1,          1,          -0.577349,         0.577349,          0.577349,          1,          1,
//    -1,         1,          -1,         -0.577349,         0.577349,          -0.577349,         0,          1,
//    1,          1,          -1,         0.577349,          0.577349,          -0.577349,         1,          0,
//    -1,         1,          1,          -0.577349,         0.577349,          0.577349,          0,          1,
//    1,          0.999999,   1,          0.577349,          0.577349,          0.577349,          0,          0,
//    1,          1,          -1,         0.577349,          0.577349,          -0.577349,         0,          0,
//    1,          -1,         -1,         0.577349,          -0.577349,         -0.577349,         1,          0,
//    -1,         -1,         -1,         -0.577349,         -0.577349,         -0.577349,         1,          1,
//    -1,         1,          1,          -0.577349,         0.577349,          0.577349,          1,          0,
//    -1,         -1,         1,          -0.577349,         -0.577349,         0.577349,          1,          1,
//    0.999999,   -1.000001,  1,          0.577349,          -0.577349,         0.577349,          0,          1,
//    1,          0.999999,   1,          0.577349,          0.577349,          0.577349,          1,          0,
//    0.999999,   -1.000001,  1,          0.577349,          -0.577349,         0.577349,          1,          1,
//    1,          -1,         -1,         0.577349,          -0.577349,         -0.577349,         0,          1,
//    0.999999,   -1.000001,  1,          0.577349,          -0.577349,         0.577349,          1,          0,
//    -1,         -1,         1,          -0.577349,         -0.577349,         0.577349,          1,          1,
//    -1,         -1,         -1,         -0.577349,         -0.577349,         -0.577349,         0,          1,
//    -1,         -1,         -1,         -0.577349,         -0.577349,         -0.577349,         0,          0,
//    -1,         -1,         1,          -0.577349,         -0.577349,         0.577349,          1,          0,
//    -1,         1,          1,          -0.577349,         0.577349,          0.577349,          1,          1,
//    1,          1,          -1,         0.577349,          0.577349,          -0.577349,         1,          0,
//    -1,         1,          -1,         -0.577349,         0.577349,          -0.577349,         1,          1,
//    -1,         1,          1,          -0.577349,         0.577349,          0.577349,          0,          1,

// ]);

//$suzane = new TexturedMesh(require __DIR__ . '/meshes/suzane.php');

/** 
 * Texture 
 */
$texture;
glGenTextures(1, $texture);
glBindTexture(GL_TEXTURE_2D, $texture); // all upcoming GL_TEXTURE_2D operations now have effect on this texture object
// set the texture wrapping parameters
glTexParameteri(GL_TEXTURE_2D, GL_TEXTURE_WRAP_S, GL_REPEAT);   // set texture wrapping to GL_REPEAT (default wrapping method)
glTexParameteri(GL_TEXTURE_2D, GL_TEXTURE_WRAP_T, GL_REPEAT);
// set texture filtering parameters
glTexParameteri(GL_TEXTURE_2D, GL_TEXTURE_MIN_FILTER, GL_LINEAR);
glTexParameteri(GL_TEXTURE_2D, GL_TEXTURE_MAG_FILTER, GL_LINEAR);

var_dump($texture); die;

$data = stbi_load(__DIR__ . '/images/noiseb.png', $width, $height, $nrChannels, 0);
if ($data) {
    glTexImage2D(GL_TEXTURE_2D, 0, GL_RGB, $width, $height, 0, GL_RGB, GL_UNSIGNED_BYTE, $data);
    glGenerateMipmap(GL_TEXTURE_2D);
} else {
    die('Could not load texture.');
}

//glViewport(0, 0, 800, 600);

/**
 * Main loop
 */
while (!$window->shouldClose())
{
	$window->clearColor(0.7, 0.7, 0.7, 1);
	$window->clear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);

    // bind the texture
    glActiveTexture(GL_TEXTURE0);
    glBindTexture(GL_TEXTURE_2D, $texture);  

    $shader->use();

    // create transformations
    $model = new glm\mat4();
    $view = new glm\mat4();
    $projection = new glm\mat4();
    $cameraPos = new glm\vec3(0.0, 0.0, -10.0);

    $model = glm\translate($model, glm\vec3(0.0, 0.0, 0.0));
    $model = glm\rotate($model, (float)glfwGetTime() * 50, new glm\vec3(0.0, 1.0, 0.0));
    $model = glm\rotate($model, (float)glfwGetTime() * 20, new glm\vec3(0.0, 0.0, 1.0));
    $view  = glm\translate($view, $cameraPos);
    $projection = glm\perspective(45.0, (float)800 / (float)600, 0.1, 100.0);

    glUniformMatrix4fv(glGetUniformLocation($shader->id(), "transform"), 1, false, glm\value_ptr($model));
    glUniformMatrix4fv(glGetUniformLocation($shader->id(), "view"), 1, false, glm\value_ptr($view));
    glUniformMatrix4fv(glGetUniformLocation($shader->id(), "projection"), 1, false, glm\value_ptr($projection));

    $cube->draw();

    for ($i=0;$i<10;$i++)
    {
        $model = glm\translate($model, glm\vec3(3.0, 0.0, 0.0));
        glUniformMatrix4fv(glGetUniformLocation($shader->id(), "transform"), 1, false, glm\value_ptr($model));
        $suzane->draw();
    }
   

    

    // swap
    $window->swapBuffers();
    $window->pollEvents();
}

