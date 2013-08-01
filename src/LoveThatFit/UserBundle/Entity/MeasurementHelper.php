<?php

namespace LoveThatFit\UserBundle\Entity;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use \Symfony\Component\EventDispatcher\EventDispatcher;
use \Symfony\Component\EventDispatcher\Event;
use LoveThatFit\UserBundle\Event\UserEvent;
use LoveThatFit\UserBundle\Entity\User;
use LoveThatFit\AdminBundle\Entity\SizeChart;
use Symfony\Component\HttpFoundation\Request;

class MeasurementHelper {

    /**
     * Holds the Symfony2 event dispatcher service
     */
    protected $dispatcher;

    /**
     * Holds the Doctrine entity manager for database interaction
     * @var EntityManager 
     */
    protected $em;

    /**
     * Entity-specific repo, useful for finding entities, for example
     * @var EntityRepository
     */
    protected $repo;

    /**
     * The Fully-Qualified Class Name for our entity
     * @var string
     */
    protected $class;

    public function __construct(EventDispatcherInterface $dispatcher, EntityManager $em, $class) {
        $this->dispatcher = $dispatcher;
        $this->em = $em;
        $this->class = $class;
        $this->repo = $em->getRepository($class);
    }

    //---------------------------------------------------------------------   
//-------------------------------------------------------

    public function find($id) {
        return $this->repo->find($id);
    }

    //-------------------------------------------------------
    public function findOneByName($name) {
        return $this->repo->findOneByName($name);
    }

    public function findMaxUserId() {
        return $this->repo->findMaxUserId();
    }
    public function saveMeasurement(Measurement $measurement) {
        $this->em->persist($measurement);
        $this->em->flush();
    }
   #----------------------Code For Value Retaing ------------------------------------------------------------------------# 
    public function measurementRetain($measurement) {
        $retaining_array = array();

        $top_size_chart = $measurement->getTopFittingSizeChart();
        $bottom_size_chart = $measurement->getBottomFittingSizeChart();
        $dress_size_chart = $measurement->getDressFittingSizeChart();

        #---Getting the Top Size Chart --------#
        if ($top_size_chart) {

            $retaining_array['topSizeChartId'] = $top_size_chart->getId();
            $top_brand = $top_size_chart->getBrand();
            $retaining_array['top_brand_id'] = $top_brand->getId();
        } else {
            $retaining_array['top_brand_id'] = Null;
            $retaining_array['topSizeChartId'] = Null;
        }

        #---Getting The Bottom Size Chart--------#   
        if ($bottom_size_chart) {
            $retaining_array['bottomSizeChartId'] = $bottom_size_chart->getId();
            $bottom_brand = $bottom_size_chart->getBrand();
            $retaining_array['bottom_brand_id'] = $bottom_brand->getId();
        } else {
            $retaining_array['bottom_brand_id'] = Null;
            $retaining_array['bottomSizeChartId'] = Null;
        }

        #---Getting The Dress Size Chart-----------#
        if ($dress_size_chart) {
            $retaining_array['dressSizeChartId'] = $dress_size_chart->getID();
            $dress_brand = $dress_size_chart->getBrand();
            $retaining_array['dress_brand_id'] = $dress_brand->getId();
        } else {
            $retaining_array['dress_brand_id'] = Null;
            $retaining_array['dressSizeChartId'] = Null;
        }
        return $retaining_array;
    }

}