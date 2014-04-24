<?php

namespace Madways\KommunalomatBundle\Entity\Repository;
use Doctrine\ORM\EntityRepository;

class QuestionRepository extends EntityRepository
{
    public function findAll()
    {
        return $this->findBy(array(), array('weight' => 'ASC'));
    }
}
