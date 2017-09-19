<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Traits\DateTrait;
use AppBundle\Entity\Traits\FileTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="news")
 */
class News extends BaseEntity
{
    use FileTrait,
        DateTrait
        ;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id")
     */
    private $author;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    protected $publishStartDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    protected $publishEndDate;

    /**
     * @var string
     * @ORM\Column(type="text", length=2000, nullable=true)
     */
    private $description;

    /**
     * @return mixed
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param mixed $author
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }

    /**
     * @return \DateTime
     */
    public function getPublishStartDate()
    {
        return $this->publishStartDate;
    }

    /**
     * @param \DateTime $publishStartDate
     * @return News
     */
    public function setPublishStartDate(\DateTime $publishStartDate)
    {
        $this->publishStartDate = $publishStartDate;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getPublishEndDate()
    {
        return $this->publishEndDate;
    }

    /**
     * @param \DateTime $publishEndDate
     * @return News
     */
    public function setPublishEndDate(\DateTime $publishEndDate)
    {
        $this->publishEndDate = $publishEndDate;
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
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
    }
}
