<?php
namespace Madways\KommunalomatBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="kommunalomat_user_answer")
 * 
 * This table maps the selected value for an answer to the user 
 */
class UserAnswer
{

    /** 
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="User", inversedBy="answers") 
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false) 
     */
    protected $user;

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
     * Set answer
     *
     * @param integer $answer
     * @return UserAnswer
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
     * Set user
     *
     * @param User $user
     * @return UserAnswer
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set question
     *
     * @param Question $question
     * @return UserAnswer
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
