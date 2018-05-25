<?php  

namespace PGF\Drawing;

use PGF\Exception;
use PGF\Shader\Program;

/**
 * Super simple mesh drawer
 */
class SimpleMesh
{
    /**
     * The buffers
     *
     * @var int
     */
    protected $VAO;
    protected $VBO;

    /**
     * number of triangles to draw
     *
     * @var int
     */
    protected $triangleCount;
    
    /**
     * Construct
     *
     * @param Program           $shader 
     */
    public function __construct(array $verticies)
    {
        $this->triangleCount = count($verticies) / 6;

        glGenVertexArrays(1, $this->VAO);
        glGenBuffers(1, $this->VBO);
        
        glBindVertexArray($this->VAO);
        
        // vertex buffer
        glBindBuffer(GL_ARRAY_BUFFER, $this->VBO);
        glBufferDataFloat(GL_ARRAY_BUFFER, $verticies, GL_STATIC_DRAW);
        
        // position attribute
        glVertexAttribPointer(0, 3, GL_FLOAT, GL_FALSE, 6, 0);
        glEnableVertexAttribArray(0);

        glVertexAttribPointer(1, 3, GL_FLOAT, GL_FALSE, 6, 3);
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
    }

    /**
     * Draw the damn thing
     */ 
    public function draw()
    {
        glBindVertexArray($this->VAO);
        glDrawArrays(GL_TRIANGLES, 0, $this->triangleCount);
    }
}