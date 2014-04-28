<?php
namespace Madways\KommunalomatBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

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
     * @var \DateTime $time_first_online
     * @ORM\Column(type="datetime")
     */
    protected $time_first_online;

    /**
     * @var \DateTime $time_view_result
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $time_view_result;

    /**
     *
     * @ORM\OneToMany(targetEntity="UserAnswer", mappedBy="user")
     * 
     */
    private $answers;

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
     * Set time_first_online
     *
     * @param \DateTime $timeFirstOnline
     * @return User
     */
    public function setTimeFirstOnline($timeFirstOnline)
    {
        $this->time_first_online = $timeFirstOnline;

        return $this;
    }

    /**
     * Get time_first_online
     *
     * @return \DateTime 
     */
    public function getTimeFirstOnline()
    {
        return $this->time_first_online;
    }

    /**
     * Set time_view_result
     *
     * @param \DateTime $timeViewResult
     * @return User
     */
    public function setTimeViewResult($timeViewResult)
    {
        $this->time_view_result = $timeViewResult;

        return $this;
    }

    /**
     * Get time_view_result
     *
     * @return \DateTime 
     */
    public function getTimeViewResult()
    {
        return $this->time_view_result;
    }

    private static function cmp($a, $b) {
        return ($a->getWeight() > $b->getWeight())? 1 : -1;
    }

    public function getAnswersSorted()
    {
        $arr = $this->answers->toArray();
        usort($arr, array($this, 'cmp'));
        return $arr;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->answers = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add answers
     *
     * @param \Madways\KommunalomatBundle\Entity\UserAnswer $answers
     * @return User
     */
    public function addAnswer(\Madways\KommunalomatBundle\Entity\UserAnswer $answers)
    {
        $this->answers[] = $answers;

        return $this;
    }

    /**
     * Remove answers
     *
     * @param \Madways\KommunalomatBundle\Entity\UserAnswer $answers
     */
    public function removeAnswer(\Madways\KommunalomatBundle\Entity\UserAnswer $answers)
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
