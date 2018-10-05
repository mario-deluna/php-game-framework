<?php  

namespace PGF\Drawing;

use PGF\Exception;
use PGF\Window;
use PGF\Shader\{Program, Shader};

use PGF\Texture\Texture;

/**
 * Plain and simple 2D Texture drawer
 */
class Drawer2D
{
    /**
     * The buffers
     *
     * @var int
     */
    protected $VAO;
    protected $VBO;
    protected $EBO;
    protected $TCBO;

    /**
     * The shader used to draw 
     *
     * @param Program
     */
    protected $shader;

    /** 
     * The window needed to for dimensions
     *
     * @var Window
     */
    protected $window;

    /**
     * Construct
     */
    public function __construct(Window $window)
    {
        // assign the window
        $this->window = $window;

        // create the drawer shader
        $this->createDefaultShader();

        // now setup the buffers
        //---
        $verticies = [
             1,  1, 0.0,
             1,  0, 0.0,
             0,  0, 0.0,
             0,  1, 0.0,
        ];

        $indices = [
            0, 1, 3, // first triangle
            1, 2, 3  // second triangle
        ];
        
        // set texture coordinates
        $coords = [
            1.0, 1.0, // top right
            1.0, 0.0, // bottom right
            0.0, 0.0, // bottom left
            0.0, 1.0  // top left
        ];

        glGenVertexArrays(1, $this->VAO);
        glGenBuffers(1, $this->VBO);
        glGenBuffers(1, $this->EBO);
        glGenBuffers(1, $this->TCBO);
        
        glBindVertexArray($this->VAO);
        
        // vertex buffer
        glBindBuffer(GL_ARRAY_BUFFER, $this->VBO);
        glBufferDataFloat(GL_ARRAY_BUFFER, $verticies, GL_STATIC_DRAW);
        
        // index buffer
        glBindBuffer(GL_ELEMENT_ARRAY_BUFFER, $this->EBO);
        glBufferDataInt(GL_ELEMENT_ARRAY_BUFFER, $indices, GL_STATIC_DRAW);
        
        // position attribute
        glVertexAttribPointer(0, 3, GL_FLOAT, GL_FALSE, 3, 0);
        glEnableVertexAttribArray(0);
        
        // buffer the texture coordinates
        glBindBuffer(GL_ARRAY_BUFFER, $this->TCBO);
        glBufferDataFloat(GL_ARRAY_BUFFER, $coords, GL_STATIC_DRAW);
        
        // texture coord attribute
        glVertexAttribPointer(1, 2, GL_FLOAT, GL_FALSE, 2, 0);
        glEnableVertexAttribArray(1);

        // unbind
        glBindBuffer(GL_ARRAY_BUFFER, 0); 
        glBindVertexArray(0); 
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
        layout (location = 1) in vec2 texture_coordinates;

        out vec2 tcoords;

        uniform mat4 projection;
        uniform vec2 pos;
        uniform vec2 size;

        mat4 scale(float x, float y, float z){
            return mat4(
                vec4(x,   0.0, 0.0, 0.0),
                vec4(0.0, y,   0.0, 0.0),
                vec4(0.0, 0.0, z,   0.0),
                vec4(0.0, 0.0, 0.0, 1.0)
            );
        }

        mat4 translate(float x, float y, float z){
            return mat4(
                vec4(1.0, 0.0, 0.0, 0.0),
                vec4(0.0, 1.0, 0.0, 0.0),
                vec4(0.0, 0.0, 1.0, 0.0),
                vec4(x,   y,   z,   1.0)
            );
        }

        void main()
        {
            gl_Position = projection * translate(pos.x, pos.y, 0) * scale(size.x, size.y, 0) * vec4(position, 1.0f);
            tcoords = texture_coordinates;
        }
        ");

        $fragmentShader = new Shader(Shader::FRAGMENT, "
        #version 330 core
        out vec4 fragment_color;

        in vec2 tcoords;

        uniform sampler2D texture1;

        void main()
        {
            fragment_color = texture(texture1, tcoords);
            //fragment_color = vec4(1.0, 1.0, 1.0, 1.0);
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