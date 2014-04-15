<?php
namespace Madways\KommunalomatBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="kommunalomat_user")
 */
class User
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $session;

    /**
     * @ORM\OneToMany(targetEntity="UserAnswer", mappedBy="user")
     */
    protected $answers;

    public function __construct()
    {
        //$this->answers = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set session
     *
     * @param string $session
     * @return User
     */
    public function setSession($session)
    {
        $this->session = $session;

        return $this;
    }

    /**
     * Get session
     *
     * @return string 
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * Add answers
     *
     * @param \Madways\KommunalomatBundle\Entity\QuestionAnswer $answers
     * @return User
     */
    public function addAnswer(\Madways\KommunalomatBundle\Entity\QuestionAnswer $answers)
    {
        $this->answers[] = $answers;

        return $this;
    }

    /**
     * Remove answers
     *
     * @param \Madways\KommunalomatBundle\Entity\QuestionAnswer $answers
     */
    public function removeAnswer(\Madways\KommunalomatBundle\Entity\QuestionAnswer $answers)
    {
        $this->answers->removeElement($answers);
    }

    /**
     * Get answers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAnswers()
    {
        return $this->answers;
    }
}
