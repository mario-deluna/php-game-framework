<?php  

namespace PGF\Texture;

class Texture
{
	/**
	 * The texture id 
	 *
	 * @var int
	 */
	private $id;

	/**
	 * The texture dimensions
	 *
	 * @var int 
	 */
	private $width;
	private $height;

	/**
	 * The number of channels
	 *
	 * @var int
	 */
	private $nrChannels;

	/**
	 * Construct
	 */
	public function __construct(string $path)
	{
		glEnable(GL_BLEND);
		glBlendFunc(GL_SRC_ALPHA, GL_ONE_MINUS_SRC_ALPHA);

		glGenTextures(1, $this->id);
		glBindTexture(GL_TEXTURE_2D, $this->id);

		// set the texture wrapping parameters
		glTexParameteri(GL_TEXTURE_2D, GL_TEXTURE_WRAP_S, GL_REPEAT);  
		glTexParameteri(GL_TEXTURE_2D, GL_TEXTURE_WRAP_T, GL_REPEAT);

		// set texture filtering parameters
		glTexParameteri(GL_TEXTURE_2D, GL_TEXTURE_MIN_FILTER, GL_LINEAR);
		glTexParameteri(GL_TEXTURE_2D, GL_TEXTURE_MAG_FILTER, GL_LINEAR);

		// load the texture
		if (!$data = stbi_load($path, $this->width, $this->height, $this->nrChannels, 0)) {
		    throw new \Exception("Could not load texture: $path");
		}

		// set the data
		if ($this->nrChannels === 3) {
			glTexImage2D(GL_TEXTURE_2D, 0, GL_RGB, $this->width, $this->height, 0, GL_RGB, GL_UNSIGNED_BYTE, $data);
		} elseif ($this->nrChannels === 4) {
			glTexImage2D(GL_TEXTURE_2D, 0, GL_RGBA, $this->width, $this->height, 0, GL_RGBA, GL_UNSIGNED_BYTE, $data);
		} elseif ($this->nrChannels === 1) {
			glTexImage2D(GL_TEXTURE_2D, 0, GL_RED, $this->width, $this->height, 0, GL_RED, GL_UNSIGNED_BYTE, $data);
		} else {
			throw new \Exception("Unknown number of color channels ({$this->nrChannels}) for texture: $path");
		}

		// create mipmap
		glGenerateMipmap(GL_TEXTURE_2D);

		// lets hope $data is released here
	}

	/**
	 * Bind the current texture
	 */
	public function bind()
	{
		glBindTexture(GL_TEXTURE_2D, $this->id);  
	}
}