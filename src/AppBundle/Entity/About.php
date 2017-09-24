<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Traits\DateTrait;
use AppBundle\Entity\Traits\FileTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="about")
 * @ORM\HasLifecycleCallbacks
 */
class About extends BaseEntity
{
	use FileTrait,
		DateTrait;

	/**
	 * @var string
	 * @ORM\Column(type="text", length=2000, nullable=true)
	 */
	private $description;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $isEnabled;

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->isEnabled;
    }

    /**
     * @param bool $isEnabled
	 * @return About
	 */
    public function setEnabled(bool $isEnabled)
    {
        $this->isEnabled = $isEnabled;

        return $this;
    }

	/**
	 * @return string
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * @param string $description
	 * @return About
	 */
	public function setDescription(string $description)
	{
		$this->description = $description;
		return $this;
	}
}