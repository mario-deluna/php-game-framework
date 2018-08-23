<?php  

namespace PGF\Drawing;

use PGF\Exception;
use PGF\Shader\Program;

use glm\vec3;

class LineDrawer
{
    /**
     * The buffers
     *
     * @var int
     */
    protected $VAO;
    protected $VBO;
    
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

        glGenVertexArrays(1, $this->VAO);
        glGenBuffers(1, $this->VBO);
        
        glBindVertexArray($this->VAO);
        
        // position attribute
        glVertexAttribPointer(0, 3, GL_FLOAT, GL_FALSE, 3, 0);
        glEnableVertexAttribArray(0);

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
    public function draw(vec3 $a, vec3 $b)
    {
        $this->shader->use();

        glBindBuffer(GL_ARRAY_BUFFER, $this->VBO);
        glBufferDataFloat(GL_ARRAY_BUFFER, [$a->x, $a->y, $a->z, $b->x, $b->y, $b->z], GL_STATIC_DRAW);

        glBindVertexArray($this->VAO);
        glDrawArrays(GL_LINE_STRIP, 0, 2);
    }
}