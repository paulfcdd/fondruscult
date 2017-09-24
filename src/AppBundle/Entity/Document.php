<?php

namespace AppBundle\Entity;


use AppBundle\Entity\Traits\DateTrait;
use AppBundle\Entity\Traits\FileTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="documents")
 * @ORM\Entity()
 */
class Document extends BaseEntity
{
	use FileTrait,
		DateTrait;
}