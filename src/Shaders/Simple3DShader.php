<?php  

namespace PGF\Shaders;

use PGF\Exception;
use PGF\Shader\{Program, Shader};

use PGF\Texture\Texture;

use glm\mat4;
use glm\vec3;

/**
 * Simple basic 3D drawer
 */
class Simple3DShader extends Program
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
    public function __construct(...$shaders)
    {  
        /**
         * Prepare Shaders
         *
         * Big thanks to learnopengl.org
         */
        $vertexShader = new Shader(Shader::VERTEX, "
        #version 330 core
        layout (location = 0) in vec3 position;
        layout (location = 1) in vec3 normal_vector;
        layout (location = 2) in vec2 textrure_coords;

        uniform mat4 projection;
        uniform mat4 transform;
        uniform mat4 view;

        uniform vec3 light_position;

        out vec3 fragment_position;
        out vec3 fragment_normals;
        out vec2 fragment_coords;

        out vec3 fragment_light_position;

        void main()
        {
            fragment_position = vec3(view * transform * vec4(position, 1.0));
            fragment_normals = mat3(transpose(inverse(view * transform))) * normal_vector;
            fragment_coords = vec2(textrure_coords.x, -textrure_coords.y);

            gl_Position = projection * view * transform * vec4(position, 1.0);

            fragment_light_position = vec3(view * vec4(light_position, 1.0));
        }
        ");

        $fragmentShader = new Shader(Shader::FRAGMENT, "
        #version 330 core

        out vec4 fragment_color;

        in vec3 fragment_position;  
        in vec3 fragment_normals;
        in vec2 fragment_coords;
        in vec3 fragment_light_position;

        uniform int has_diffuse_texture = 1;
        uniform int has_specular_texture = 1;
        uniform sampler2D diffuse_map;
        uniform sampler2D specular_map;
        uniform vec3 diffuse_color = vec3(1.0f, 1.0f, 1.0f);
        uniform vec3 specular_color = vec3(1.0f, 1.0f, 1.0f);

        uniform vec3 view_position;

        uniform float shininess = 32.0f;
        uniform float specular_strength = 1.0f;

        uniform int mode = 0;

        void draw_full()
        {
            vec3 light_ambient = vec3(1.0f, 1.0f, 1.0f) * 0.2;
            vec3 light_diffuse = vec3(1.0f, 1.0f, 1.0f) * 0.8;
            vec3 light_specular = vec3(1.0f, 1.0f, 1.0f) * specular_strength;

            vec3 diffuse_sample;
            if (has_diffuse_texture == 1) {
                diffuse_sample = texture(diffuse_map, fragment_coords).rgb;
            } else {
                diffuse_sample = diffuse_color;
            }

            vec3 specular_sample;
            if (has_specular_texture == 1) {
                specular_sample = texture(specular_map, fragment_coords).rgb;
            } else {
                specular_sample = specular_color;
            }

            // ambient
            vec3 ambient = light_ambient * diffuse_sample;
            
            // diffuse 
            vec3 norm = normalize(fragment_normals);
            vec3 lightDir = normalize(fragment_light_position - fragment_position);
            float diff = max(dot(norm, lightDir), 0.0);
            vec3 diffuse = light_diffuse * diff * diffuse_sample;  
            
            // specular
            vec3 viewDir = normalize(view_position - fragment_position);
            vec3 reflectDir = reflect(-lightDir, norm);  
            float spec = pow(max(dot(viewDir, reflectDir), 0.0), shininess);
            vec3 specular = light_specular * spec * specular_sample;  
                
            // add everything together
            vec3 result = ambient + diffuse + specular;

            fragment_color = vec4(result, 1.0);
        }

        void draw_diffuse()
        {
            fragment_color = texture(diffuse_map, fragment_coords);
        }

        void draw_specular()
        {
            fragment_color = texture(specular_map, fragment_coords);
        }

        void draw_color()
        {
            fragment_color = vec4(1.0f, 1.0f, 1.0f, 1.0f);
        }

        void main()
        {
            if (mode == 0) {
                draw_full();
            } else if (mode == 1) {
                draw_diffuse();
            } else if (mode == 2) {
                draw_specular();
            } else if (mode == 3) {
                draw_color();
            }
        } 
        ");

        parent::__construct($vertexShader, $fragmentShader);
        $this->link();

        // we created the shader program so we can free
        // the sources
        unset($vertexShader, $fragmentShader);
    }

    /**
     * Set the light position
     */
    public function setLightPosition(vec3 $position)
    {
        $this->uniform3f('light_position', $position->x, $position->y, $position->z);
    }

    /**
     * Update the view matrix 
     */
    public function setViewMatrx(array $matrix)
    {
        $this->uniformMatrix4fv('view', $matrix);
    }

    /**
     * Set the view position
     */
    public function setViewPosition(vec3 $position)
    {
        $this->uniform3f('view_position', $position->x, $position->y, $position->z);
    }

    /**
     * Update the projection matrix 
     */
    public function setProjectionMatrx(array $matrix)
    {
        $this->uniformMatrix4fv('projection', $matrix);
    }

    /**
     * Update the transformation matrix 
     */
    public function setTransformationMatrix(array $matrix)
    {
        $this->uniformMatrix4fv('transform', $matrix);
    }

    /**
     * Set the texture
     */
    public function setTexture(Texture $texture, ?Texture $specular = null)
    {
        $this->uniform1i('diffuse_map', 0);
        $this->uniform1i('specular_map', 1);

        glActiveTexture(GL_TEXTURE0);
        $texture->bind();

        glActiveTexture(GL_TEXTURE1);
        if ($specular) {
            $specular->bind();
        } else {
            glBindTexture(GL_TEXTURE_2D, 0);
        }
    }
}