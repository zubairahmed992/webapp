<?php

namespace LoveThatFit\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="LoveThatFit\AdminBundle\Entity\SurveyUserRepository")
 * @ORM\Table(name="surveyuser")
 * @ORM\HasLifecycleCallbacks()
 */
class SurveyUser
{
   
    /**
     * @ORM\ManyToOne(targetEntity="SurveyQuestion", inversedBy="SurveyUser")
     * @ORM\JoinColumn(name="question_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $question;
    
    /**
     * @ORM\ManyToOne(targetEntity="SurveyAnswer", inversedBy="SurveyUser")
     * @ORM\JoinColumn(name="answer_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $answer;
    
    /**
     * @ORM\ManyToOne(targetEntity="LoveThatFit\UserBundle\Entity\User", inversedBy="SurveyUser")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $user;
    
    
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $survey
     *
     * @ORM\Column(name="survey", type="text")
     */
    private $survey;


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
     * Set survey
     *
     * @param string $survey
     * @return SurveyUser
     */
    public function setSurvey($survey)
    {
        $this->survey = $survey;
    
        return $this;
    }

    /**
     * Get survey
     *
     * @return string 
     */
    public function getSurvey()
    {
        return $this->survey;
    }

    /**
     * Set question
     *
     * @param LoveThatFit\AdminBundle\Entity\SurveyQuestion $question
     * @return SurveyUser
     */
    public function setQuestion(\LoveThatFit\AdminBundle\Entity\SurveyQuestion $question = null)
    {
        $this->question = $question;
    
        return $this;
    }

    /**
     * Get question
     *
     * @return LoveThatFit\AdminBundle\Entity\SurveyQuestion 
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * Set answer
     *
     * @param LoveThatFit\AdminBundle\Entity\SurveyAnswer $answer
     * @return SurveyUser
     */
    public function setAnswer(\LoveThatFit\AdminBundle\Entity\SurveyAnswer $answer = null)
    {
        $this->answer = $answer;
    
        return $this;
    }

    /**
     * Get answer
     *
     * @return LoveThatFit\AdminBundle\Entity\SurveyAnswer 
     */
    public function getAnswer()
    {
        return $this->answer;
    }

    /**
     * Set user
     *
     * @param LoveThatFit\UserBundle\Entity\User $user
     * @return SurveyUser
     */
    public function setUser(\LoveThatFit\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return LoveThatFit\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }
}