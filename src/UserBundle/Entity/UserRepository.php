<?php

namespace UserBundle\Entity;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    /**
     * @param $email string
     * @return array|bool
     */
    public function findUserByEmail($email)
    {
        /* @var \UserBundle\Entity\Email */
        $emailEntity = $this->getEntityManager()->getRepository("UserBundle:Email")
            ->findOneBy(array("emailAddress" => $email, "current" => 1));

        if (!$emailEntity) {
            return false;
        }

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select("u")
            ->from("UserBundle:User", "u")
            ->where($qb->expr()->eq("id", ":userId"))
            ->setParameter("userId", $emailEntity->getId());

        return $qb->getQuery()->getResult();
    }
}

