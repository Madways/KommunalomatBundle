<?php
namespace Madways\KommunalomatBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="kommunalomat_party_answer")
 * 
 * This table maps the selected value and the explanation for an answer to the pary 
 */
class PartyAnswer
{

    /** 
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="Party") 
     * @ORM\JoinColumn(name="party_id", referencedColumnName="id", nullable=false) 
     */
    protected $party;

    /** 
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="Question") 
     * @ORM\JoinColumn(name="question_id", referencedColumnName="id", nullable=false) 
     */
    protected $question;

    /**
     * @ORM\Column(type="integer")
     */
    protected $answer;

    /**
     * @ORM\Column(type="text")
     */
    protected $explanation;

    /**
     * Set answer
     *
     * @param integer $answer
     * @return PartyAnswer
     */
    public function setAnswer($answer)
    {
        $this->answer = $answer;

        return $this;
    }

    /**
     * Get answer
     *
     * @return integer 
     */
    public function getAnswer()
    {
        return $this->answer;
    }

    /**
     * Set explanation
     *
     * @param string $explanation
     * @return PartyAnswer
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
     * Set party
     *
     * @param User $party
     * @return PartyAnswer
     */
    public function setParty(Party $party)
    {
        $this->party = $party;

        return $this;
    }

    /**
     * Get party
     *
     * @return User 
     */
    public function getParty()
    {
        return $this->party;
    }

    /**
     * Set question
     *
     * @param Question $question
     * @return PartyAnswer
     */
    public function setQuestion(Question $question)
    {
        $this->question = $question;

        return $this;
    }

    /**
     * Get question
     *
     * @return Question 
     */
    public function getQuestion()
    {
        return $this->question;
    }
}
