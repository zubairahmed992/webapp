<?php

namespace LoveThatFit\AdminBundle\Entity;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use \Symfony\Component\EventDispatcher\EventDispatcher;
use \Symfony\Component\EventDispatcher\Event;
use LoveThatFit\AdminBundle\Event\SizeChartEvent;



class SizeChartHelper{

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

    public function __construct(EventDispatcherInterface $dispatcher, EntityManager $em, $class)
    {
        $this->dispatcher = $dispatcher;
        $this->em = $em;
        $this->class = $class;
        $this->repo = $em->getRepository($class);
        
    }
 //---------------------------------------------------------------------   
    
    public function createNewSizeChart()
{
    $class = $this->class;
    $size_chart = new $class();
    return $size_chart;
}
//-------------------------------------------------------

public function saveSizeChart(SizeChart $size_chart)
{
    $this->em->persist($size_chart);
    $this->em->flush();  
}
//-------------------------------------------------------

public function find($id)
{
    return $this->repo->find($id);
}
#-------------------------Web Service for size chart for registration step two-------------------------------------------------#
public function sizeChartList($request_array)
{
     
        $gender=$request_array['gender'];
        $bodytype=$request_array['body_type'];
        $target_top=$request_array['target_top'];
        $top_size=$request_array['top_size'];
        $target_bottom=$request_array['target_bottom']; 
        $bottom_size=$request_array['bottom_size'];    
        $target_dress=$request_array['target_dress'];    
        $dress_size=$request_array['dress_size'];     
       
         $neck=0;
         $bust=0;
         $chest=0;
         $waist=0;
         $sleeve=0;
         $inseam=0;
         $hip=0;
          $measurement= array();   
         if($target_top)
         {   
          $sizechart_top = $this->repo->getSizeChartByBrandGenderBodyTypeTopSize($gender,$bodytype,$target_top,$top_size);
          if($sizechart_top){
          $bust=$sizechart_top[0]['top_bust'];
          $neck=$sizechart_top[0]['top_neck'];
          $chest=$sizechart_top[0]['top_chest'];
          $waist=$sizechart_top[0]['top_waist'];
          }
          
          
         }
         if($target_bottom)
         {     
         $sizechart_bottom = $this->repo->getSizeChartByBrandGenderBodyTypeBottomSize($gender,$bodytype,$target_bottom,$bottom_size);
                if($sizechart_bottom)
                { 
                $waist=$sizechart_bottom[0]['bottom_waist'];
                $hip=$sizechart_bottom[0]['bottom_hip'];
                $inseam=$sizechart_bottom[0]['bottom_inseam'];
                }
               
         }
         if($target_dress)
         {     
         $size_chart_dress = $this->repo->getSizeChartByBrandGenderBodyTypeDressSize($gender, $bodytype, $target_dress, $dress_size);
            
            $bust_dress=0;
            $waist_dress=0;
            $hip_dress=0;
            $sleeve_dress=0;
            if($size_chart_dress){
                
            $bust_dress = $sizechart_dress[0]['dress_bust'];
            $waist_dress = $sizechart_dress[0]['dress_waist'];
            $hip_dress = $sizechart_dress[0]['dress_hip'];
            $sleeve_dress = $sizechart_dress[0]['dress_sleeve'];
            }
            
            #-----------------BUST AVERAGE-----------------------------------------------#
            if ($bust_dress == 0 && $bust > 0) {
                $bust = $bust;
            }
            if ($bust == 0 && $bust_dress) {
                $bust = $bust_dress;
            }
            if ($bust_dress > 0 && $bust > 0) {
                $bust = ($bust_dress + $bust) / 2;
            }

            #----------------- WAIST AVERAGE-----------------------------------------------#
            if ($waist_dress == 0 && $waist > 0) {
                $wasit = $waist;
            }
            if ($waist_dress > 0 && $waist == 0) {
                $wasit = $waist_dress;
            }
            if ($waist_dress > 0 && $waist > 0) {
                $wasit = ($waist_dress + $wasit) / 2;
            }
            #-------------------------------SLEEVE________________________________________________# 
            if ($sleeve > 0 && $sleeve_dress == 0) {
                $sleeve = $sleeve;
            }
            if ($sleeve == 0 && $sleeve_dress > 0) {
                $sleeve = $sleeve_dress;
            }
            if ($sleeve > 0 && $sleeve_dress > 0) {
                $sleeve = ($sleeve + $sleeve_dress) / 2;
            }


            #--------------------------HIP-----------------#
            if ($hip > 0 && $hip_dress == 0) {
                $hip = $hip;
            }
            if ($hip == 0 && $hip_dress > 0) {
                $hip = $hip_dress;
            }
            if ($hip > 0 && $hip_dress > 0) {
                $hip = ($hip + $hip_dress) / 2;
            }
        }  
    
     
        $measurement['neck']=$neck;
        $measurement['bust']=$bust;
        $measurement['chest']=$chest;
        $measurement['waist']=$waist;
        $measurement['hip']=$hip;
        $measurement['sleeve']=$sleeve;
        $measurement['inseam']=$inseam;
        if($measurement)   
        {
            return   $measurement;
        }
        
        
}
}