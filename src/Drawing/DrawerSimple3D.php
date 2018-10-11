<?php  

namespace PGF\Drawing;

use PGF\Exception;
use PGF\Window;
use PGF\Shader\{Program, Shader};

use PGF\Texture\Texture;
use PGF\Mesh\TexturedMesh;

use glm\mat4;
use glm\vec3;

/**
 * Simple basic 3D drawer
 */
class DrawerSimple3D
{
    /**
     * Current shader
     * 
     * @var Program
     */
    public $shader;

    /**
     * Construct
     */
    public function __construct()
    {
        // create the drawer shader
        $this->createDefaultShader();
    }

    /**
     * Create the default simple 3D Shader
     */
    private function createDefaultShader()
    {
        /**
         * Prepare Shaders
         */
        $vertexShader = new Shader(Shader::VERTEX, "
        #version 330 core
        layout (location = 0) in vec3 position;
        layout (location = 1) in vec3 normalv;
        layout (location = 2) in vec2 text_coords;

        uniform mat4 projection;
        uniform mat4 transform;
        uniform mat4 view;

        out vec3 FragPos;
        out vec3 FragNormals;
        out vec2 tCoords;
        out vec3 LightPos;

        void main()
        {
            vec3 lightPos = vec3(10, 10, 10);

            gl_Position = projection * view * transform * vec4(position, 1.0);
            FragPos = vec3(view * transform * vec4(position, 1.0));
            FragNormals = mat3(transpose(inverse(view * transform))) * normalv;
            LightPos = vec3(view * vec4(lightPos, 1.0));
            tCoords = text_coords;
        }
        ");

        $fragmentShader = new Shader(Shader::FRAGMENT, "
        #version 330 core

        out vec4 fragment_color;

        in vec3 FragPos;  
        in vec3 FragNormals;
        in vec2 tCoords;
        in vec3 LightPos;

        uniform sampler2D texture1;
        uniform float time;
          
        void main()
        {
            vec4 textcol = texture(texture1, tCoords);
            vec3 lightColor = vec3(1.0, 1.0, 0.9);
            vec3 objectColor = vec3(0.2, 0.2, 0.2);
            //vec3 objectColor = vec3(0.8, 0.8, 0.8);// * vec3(textcol.x, textcol.y, textcol.z);
            
            // ambient
            float ambientStrength = 0.6;
            vec3 ambient = ambientStrength * lightColor;    
            
             // diffuse 
            vec3 norm = normalize(FragNormals);
            vec3 lightDir = normalize(LightPos - FragPos);
            float diff = max(dot(norm, lightDir), 0.0);
            vec3 diffuse = diff * lightColor;
            
            // specular
            float specularStrength = 0.9;
            vec3 viewDir = normalize(-FragPos); // the viewer is always at (0,0,0) in view-space, so viewDir is (0,0,0) - Position => -Position
            vec3 reflectDir = reflect(-lightDir, norm);  
            float spec = pow(max(dot(viewDir, reflectDir), 0.0), 32);
            vec3 specular = specularStrength * spec * lightColor; 
            
            vec3 result = (ambient + diffuse + specular) * objectColor;
            fragment_color = vec4(result, 1.0);
        } 
        ");
        $shader = new Program($vertexShader, $fragmentShader);
        $shader->link();

        // we created the shader program so we can free
        // the sources
        unset($vertexShader, $fragmentShader);

        // assign the current shader
        $this->shader = $shader;
    }

    /**
     * Activate the current shader
     */
    public function activateShader()
    {
        $this->shader->use();
    }

    /**
     * Update the view matrix 
     */
    public function setViewMatrx(mat4 $matrix)
    {
        $this->shader->uniformMatrix4fv('view', $matrix);
    }

    /**
     * Update the projection matrix 
     */
    public function setProjectionMatrx(mat4 $matrix)
    {
        $this->shader->uniformMatrix4fv('projection', $matrix);
    }

    /**
     * Update the transformation matrix 
     */
    public function setTransformationMatrix(mat4 $matrix)
    {
        $this->shader->uniformMatrix4fv('transform', $matrix);
    }

    /**
     * Draw the damn thing
     */ 
    public function draw(Texture $texture, TexturedMesh $mesh)
    {
        // prepare the texture
        glActiveTexture(GL_TEXTURE0);
        $texture->bind();

        $mesh->draw();
    }
}