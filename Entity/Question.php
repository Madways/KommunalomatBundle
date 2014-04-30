<?php

namespace Madways\KommunalomatBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Table(name="kommunalomat_question")
 * @ORM\Entity(repositoryClass="Madways\KommunalomatBundle\Entity\Repository\QuestionRepository")
 */
class Question
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="text")
     */
    protected $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $explanation;

    /**
     * @ORM\Column(type="integer")
     */
    protected $weight;

    /**
     *
     * @ORM\OneToMany(targetEntity="PartyAnswer", mappedBy="question")
     * 
     */
    private $party_answers;

    /**
     *
     * @ORM\OneToMany(targetEntity="UserAnswer", mappedBy="question")
     * 
     */
    private $user_answers;

    public function __toString() {
        return $this->title;
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
     * Set title
     *
     * @param string $title
     * @return Question
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set weight
     *
     * @param integer $weight
     * @return Question
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * Get weight
     *
     * @return integer 
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Set explanation
     *
     * @param string $explanation
     * @return Question
     */
    public function setExplanation($explanation)
    {
        $this->explanation = $explanation;

        return $this;
    }

    /**
     * Get explanation
     *
     * @return string 
     */
    public function getExplanation()
    {
        return $this->explanation;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->party_answers = new \Doctrine\Common\Collections\ArrayCollection();
        $this->user_answers = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add party_answers
     *
     * @param \Madways\KommunalomatBundle\Entity\PartyAnswer $partyAnswers
     * @return Question
     */
    public function addPartyAnswer(\Madways\KommunalomatBundle\Entity\PartyAnswer $partyAnswers)
    {
        $this->party_answers[] = $partyAnswers;

        return $this;
    }

    /**
     * Remove party_answers
     *
     * @param \Madways\KommunalomatBundle\Entity\PartyAnswer $partyAnswers
     */
    public function removePartyAnswer(\Madways\KommunalomatBundle\Entity\PartyAnswer $partyAnswers)
    {
        $this->party_answers->removeElement($partyAnswers);
    }

    /**
     * Get party_answers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPartyAnswers()
    {
        return $this->party_answers;
    }

    /**
     * Add user_answers
     *
     * @param \Madways\KommunalomatBundle\Entity\UserAnswer $userAnswers
     * @return Question
     */
    public function addUserAnswer(\Madways\KommunalomatBundle\Entity\UserAnswer $userAnswers)
    {
        $this->user_answers[] = $userAnswers;

        return $this;
    }

    /**
     * Remove user_answers
     *
     * @param \Madways\KommunalomatBundle\Entity\UserAnswer $userAnswers
     */
    public function removeUserAnswer(\Madways\KommunalomatBundle\Entity\UserAnswer $userAnswers)
    {
        $this->user_answers->removeElement($userAnswers);
    }

    /**
     * Get user_answers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUserAnswers()
    {
        return $this->user_answers;
    }
}
