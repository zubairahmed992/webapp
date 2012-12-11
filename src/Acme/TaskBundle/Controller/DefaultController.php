<?php

namespace Acme\TaskBundle\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Acme\TaskBundle\Entity\Task;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/hello/{name}")
     * @Template()
     */
    public function indexAction($name)
    {
        return array('name' => $name);
    }
     /**
     * @Route("task_new", name="task_new")
     * @Template()
     */
      public function newAction(Request $request)
    {
        // create a task and give it some dummy data for this example
      $task = new Task();

    $form = $this->createFormBuilder($task)
        ->add('task', 'text')
        ->add('dueDate', 'date')
        ->getForm(); 
    
    if ($request->isMethod('POST')) {
        $form->bind($request);

        if ($form->isValid()) {
            // perform some action, such as saving the task to the database

            return $this->redirect($this->generateUrl('task_create'));
        }
    }
     return $this->render('AcmeTaskBundle:Default:new.html.twig', array(
            'form' => $form->createView(),
        ));
    }
     /**
     * @Route("task_create", name="task_create")
     * @Template()
     */
      public function createAction()
    {
          $task = new Task();
          $task->setTask('robo dobo');
          
          
          return new Response('Form submitted');
      }
}
