<?php

namespace Application\Migrations;

use DateTime;
use Doctrine\DBAL\Schema\Schema;
use MigrationsBundle\Migrations\AbstractMigration;
use UserBundle\Entity\Email;
use UserBundle\Entity\Password;
use UserBundle\Entity\User;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160215163308 extends AbstractMigration
{
    const PASSWORD = "abcde12345";
    
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        parent::up($schema);
        
        $userData = array(
            array(
                'firstName' => 'Adam',
                'lastName' => 'Raiden',
                'birthday' => '09/12/1986',
                'gender' => 'male',
                'username' => 'dubletar',
                'email' => 'dubletar@gmail.com'
            ),array(
                'firstName' => 'Test1',
                'lastName' => 'User',
                'birthday' => '09/12/1986',
                'gender' => 'male',
                'username' => 'test1',
                'email' => 'test1@test.com'
            ),array(
                'firstName' => 'Test2',
                'lastName' => 'User',
                'birthday' => '09/12/1986',
                'gender' => 'male',
                'username' => 'test2',
                'email' => 'test2@test.com'
            )
        );
        
        $encodedPassword = md5(self::PASSWORD);
        foreach ($userData as $user)
        {
            $account = new User();
            $account->setBirthDate(new DateTime($user['birthday']));
            $account->setFirstName($user['firstName']);
            $account->setGender($user['gender']);
            $account->setLastName($user['lastName']);
            $account->setUsername($user['username']);
            
            $email = new Email();
            $email->setEmailAddress($user['email']);
            $email->setUserId($account);
            
            $password = new Password();
            $password->setUserId($account);
            $password->setPassword($encodedPassword);
            
            $this->em->persist($account);
            $this->em->persist($email);
            $this->em->persist($password);
        }
        
        $this->em->flush();
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        parent::down($schema);
    }
}
