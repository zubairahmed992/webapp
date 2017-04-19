<?php

namespace LoveThatFit\ProductIntakeBundle\Entity;


use Doctrine\ORM\EntityManager;
use Doctrine\Common\Collections\ArrayCollection;

class ServiceRepo
{

    private $em;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    #-------------------------------------------------------------------
    public function getProductSpecification($decoded)
    {
            $style_id_number = $decoded['result']['style_id_number'];   
            $style_name = $decoded['result']['style_name'];             
            $title = $decoded['result']['title'];  
            $query = $this->em
                ->createQueryBuilder()
                ->select('ps.specs_json')
                ->from('LoveThatFitProductIntakeBundle:ProductSpecification', 'ps')
                ->where("ps.style_id_number='$style_id_number'  OR  ps.style_name = '$style_name' OR  ps.title = '$title'")               
                ->getQuery();               
            try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
    
    #--------------------------------------------------------------
    {
        $query = $this->em
            ->createQuery("
                SELECT ct.id as clothing_type_id, b.id as brand_id, r.id as retailer_id,
                partial p.{id, name ,gender, styling_type, description, disabled, hem_length, neckline, sleeve_styling, rise, stretch_type, horizontal_stretch, vertical_stretch, fabric_weight, layering, structural_detail, fit_type, fit_priority, fabric_content, garment_detail, size_title_type, control_number, product_model_height }
                ,partial pc.{id, title, pattern, image}
                ,partial pz.{id, title, body_type, index_value}
                ,partial pi.{id, image, line_number, raw_image, sku, price}
                ,partial psm.{id, title, garment_measurement_flat, max_body_measurement, vertical_stretch, horizontal_stretch, stretch_type_percentage, ideal_body_size_high,	ideal_body_size_low, garment_measurement_stretch_fit, min_body_measurement, fit_model_measurement, grade_rule, min_calculated, max_calculated}
                  
                FROM LoveThatFitAdminBundle:Product p              
                JOIN p.product_colors pc
                JOIN p.clothing_type ct
                JOIN p.brand b            
                LEFT JOIN p.retailer r                
                JOIN p.product_sizes pz
                LEFT JOIN pz.product_items pi
                JOIN pz.product_size_measurements psm            
                WHERE p.id=:product_id 
                ")->setParameters(array('product_id' => $id));
     
        
        try {
            return $query->getArrayResult();

        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }

    }
 

}