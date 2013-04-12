<?php

namespace LoveThatFit\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * LoveThatFit\AdminBundle\Entity\SurveyAnswer
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class SurveyAnswer
{
    
    // ...

    /**
     * @ORM\ManyToOne(targetEntity="SurveyQuestion", inversedBy="surveyAnswers")
     * @ORM\JoinColumn(name="question_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $question;
    
    /**
     * @ORM\OneToMany(targetEntity="SurveyUser", mappedBy="answer")
     */
    protected $survey;
    
    public function __construct()
    {
        $this->survey = new ArrayCollection();
    }
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $answer
     *
     * @ORM\Column(name="answer", type="text")
     */
    private $answer;


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
     * Set answer
     *
     * @param string $answer
     * @return SurveyAnswer
     */
    public function setAnswer($answer)
    {
        $this->answer = $answer;
    
        return $this;
    }

    /**
     * Get answer
     *
     * @return string 
     */
    public function getAnswer()
    {
        return $this->answer;
    }

    /**
     * Set question
     *
     * @param LoveThatFit\AdminBundle\Entity\SurveyQuestion $question
     * @return SurveyAnswer
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
     * Add survey
     *
     * @param LoveThatFit\AdminBundle\Entity\SurveyUser $survey
     * @return SurveyAnswer
     */
    public function addSurvey(\LoveThatFit\AdminBundle\Entity\SurveyUser $survey)
    {
        $this->survey[] = $survey;
    
        return $this;
    }

    /**
     * Remove survey
     *
     * @param LoveThatFit\AdminBundle\Entity\SurveyUser $survey
     */
    public function removeSurvey(\LoveThatFit\AdminBundle\Entity\SurveyUser $survey)
    {
        $this->survey->removeElement($survey);
    }

    /**
     * Get survey
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getSurvey()
    {
        return $this->survey;
    }
}