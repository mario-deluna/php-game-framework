<?php  

namespace PGF\Mesh;

use PGF\Exception;
use PGF\Shader\Program;
use PGF\Texture\Texture;

class TexturedMesh
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
     * The meshes shader
     *
     * @var Program
     */
    protected $shader;

    /**
     * The meshes texture
     *
     * @var Texture
     */
    protected $texture;

    /**
     * Construct
     *
     * @param Program           $shader 
     * @param Texture           $texture
     * @param array[float]      $verticies
     */
    public function __construct(Program $shader, Texture $texture, array $verticies)
    {
        // assign shader & texture
        $this->shader = $shader;
        $this->texture = $texture;

        // count the triangles
        $this->triangleCount = count($verticies) / 8;

        // generate the buffers.
        glGenVertexArrays(1, $this->VAO);
        glGenBuffers(1, $this->VBO);
        
        glBindVertexArray($this->VAO);
        
        // vertex buffer
        glBindBuffer(GL_ARRAY_BUFFER, $this->VBO);
        glBufferDataFloat(GL_ARRAY_BUFFER, $verticies, GL_STATIC_DRAW);
        
        // position attribute
        glVertexAttribPointer(0, 3, GL_FLOAT, GL_FALSE, 8, 0);
        glEnableVertexAttribArray(0);

        // normal
        glVertexAttribPointer(1, 3, GL_FLOAT, GL_FALSE, 8, 3);
        glEnableVertexAttribArray(1);

        // texture coordinates
        glVertexAttribPointer(2, 2, GL_FLOAT, GL_FALSE, 8, 6);
        glEnableVertexAttribArray(2);

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