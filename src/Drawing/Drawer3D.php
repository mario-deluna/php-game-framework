<?php  

namespace PGF\Drawing;

use PGF\Exception;
use PGF\Window;
use PGF\Shader\{Program, Shader};

use PGF\Texture\Texture;

/**
 * Simple basic 3D drawer
 */
class Drawer3D
{
    /**
     * Construct
     */
    public function __construct()
    {
        // create the drawer shader
        $this->createDefaultShader();
    }

    /**
     * Create the defrault 2D draw shader
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
     * Cleanup 
     */
    public function __destruct()
    {
        glDeleteVertexArrays(1, $this->VAO);
        glDeleteBuffers(1, $this->VBO);
        glDeleteBuffers(1, $this->EBO);
        glDeleteBuffers(1, $this->TCBO);
    }

    /**
     * Draw the damn thing
     */ 
    public function draw(int $x, int $y, int $width, int $height, Texture $texture)
    {
        // prepare the texture
        glActiveTexture(GL_TEXTURE0);
        $texture->bind();

        $this->shader->use();

        $windowWidth = $this->window->getWidth();
        $windowHeight = $this->window->getHeight();

        // set the projection matrix
        $this->shader->uniformMatrix4fv('projection', [
            2.0 / ($windowWidth - 0), 0, 0, 0,
            0, 2.0 / (0 - $windowHeight), 0, 0,
            0, 0, -2.0 / (-100 - 100), 0,
            -($windowWidth + 0) / ($windowWidth - 0), -(0 + $windowHeight) / (0 - $windowHeight), -(-100 + 100) / (-100 - 100), 1
        ]);

        // set the position
        $this->shader->uniform2f('pos', $x, $y);
        $this->shader->uniform2f('size', $width, $height);

        // draw
        glBindVertexArray($this->VAO);
        glDrawElements(GL_TRIANGLES, 6, GL_UNSIGNED_INT, 0);
    }
}