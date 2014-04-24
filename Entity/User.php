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
}
