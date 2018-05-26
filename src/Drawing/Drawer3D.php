<?php  

namespace PGF\Drawing;

use PGF\Exception;
use PGF\Shader\Program;

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
     * Construct
     *
     * @param Program           $shader 
     */
    public function __construct(Program $shader)
    {
        $this->shader = $shader;

        // now setup the buffers
        //---
        $verticies = [
             0.5,  0.5, 0.0,
             0.5, -0.5, 0.0,
            -0.5, -0.5, 0.0,
            -0.5,  0.5, 0.0,
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
    public function draw()
    {
        $this->shader->use();

        $coords = [
            1.0, 1.0, // top right
            1.0, 0.0, // bottom right
            0.0, 0.0, // bottom left
            0.0, 1.0  // top left
        ];

        glBindBuffer(GL_ARRAY_BUFFER, $this->TCBO);
        glBufferDataFloat(GL_ARRAY_BUFFER, $coords, GL_DYNAMIC_DRAW);

        glBindVertexArray($this->VAO);
        glDrawElements(GL_TRIANGLES, 6, GL_UNSIGNED_INT, 0);
    }
}