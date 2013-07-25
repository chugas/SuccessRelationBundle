Installation is a quick (I promise!) 2 step process:

1- Setting up the bundle
2- Create your RelationFollow class


1- 
A) Download and install RelationFollowBundle

B) Enable the bundle

Enable the required bundles in the kernel:

<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...

        new Success\RelationBundle\SuccessRelationBundle(),
    );
}

2-

Setup Doctrine ORM mapping
The ORM implementation does not provide a concrete RelationFollow class for your use, you must create one. This can be done by extending the abstract entities provided by the bundle and creating the appropriate mappings.

For example:

<?php
// src/MyProject/MyBundle/Entity/RelationUserFollow.php

namespace MyProject\MyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Success\RelationBundle\Entity\Relation as BaseRelation;

/**
 * @ORM\Entity
 */
class RelationUser extends BaseRelation
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * User one of the relation
     *
     * @var UserInterface
     * @ORM\ManyToOne(targetEntity="Application\Success\UsuarioBundle\Entity\User")
     */
    protected $entity1;

    /**
     * User two of the relation
     *
     * @var UserInterface
     * @ORM\ManyToOne(targetEntity="Application\Success\UsuarioBundle\Entity\User")
     */
    protected $entity2;
}

Configure your application

# app/config/config.yml

relation:
    class: Application\Success\UsuarioBundle\Entity\RelationUserFollow

Ejemplo de controlador

<?php

// src/MyProject/MyBundle/Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;

class RelationController extends Controller {

  public function buttonsAction($id) {
    $user = $this->getEntityUser($id);
    $response = $this->render('UsuarioBundle:User:_buttons.html.twig', array('user' => $user));
    return $response;
  }
    
  public function addContactAction($id) {
    $userLogged = $this->getUser();
    if(is_null($userLogged)) {
      throw new NotFoundHttpException('Usuario 1 no encontrado.');
    } else {
      $userContact = $this->getEntityUser($id);
      if (!is_object($userContact)) {
        throw new NotFoundHttpException('Usuario 2 no encontrado.');
      }
    }
    
    $manager = $this->container->get('success.relation.manager');
    $manager->create($userLogged, $userContact, 'follow');

    $response = new JsonResponse(array('status' => 200), 200, array());

    return $response;
  }
  
  public function contactsAction() {
    $user = $this->getUser();
    
    $manager = $this->container->get('success.relation.manager');
    $users = $manager->getFollowings($user, 5);

    $response = $this->render('UsuarioBundle:Relations:contacts.html.twig', array('users' => $users));

    return $response;
  }
  
  public function listAction() {
    $pager = $this->getPager();
    $relationsResults = $pager->getCurrentPageResults();

    $users = array();
    foreach ($relationsResults as $relation) {
      $users[$relation->getId()] = $relation->getEntity2();
    }

    $response = $this->render('UsuarioBundle:Relations:list.html.twig', array('users' => $users, 'haveToPaginate' => $pager->hasNextPage()));
    $response->setMaxAge(5 * 60);
    return $response;
  }
  
  public function pageAction($page = 1) {
    $pager = $this->getPager($page);
    $relationsResults = $pager->getCurrentPageResults();
    $users = array();
    foreach ($relationsResults as $relation) {
      $users[$relation->getId()] = $relation->getEntity2();
    }
    
    if ($this->getRequest()->isXmlHttpRequest()) {
      $html = $this->renderView('UsuarioBundle:Relations/Pager:_pager.html.twig', array('users' => $users));
      $data = array('responseCode' => 200, 'response' => $html, 'haveToPaginate' => $pager->hasNextPage());
      $response = new JsonResponse($data, 200);
    } else {
      $response = $this->render('UsuarioBundle:Relations:list.html.twig', array('users' => $users, 'haveToPaginate' => $pager->hasNextPage()));
    }
    $response->setMaxAge(30 * 60);
    return $response;
  }
  
  public function getEntityUser($id){
    $em = $this->getManager();
    $user = $em->getRepository('UsuarioBundle:User')->find($id);
      
    if (!$user) {
      throw new NotFoundHttpException("Usuario '" . $id . "' no encontrado");
    }
    
    return $user;
  }
  
  public function getManager() {
    return $this->getDoctrine()->getManager();
  }
  
  public function getPager($page = 1){
    $user = $this->getUser();
    $manager = $this->container->get('success.relation.manager');
    $queryBuilder = $manager->getFollowingsQuery($user);
    
    $pager = new Pagerfanta(new DoctrineORMAdapter($queryBuilder));
    $pager->setMaxPerPage(10);
    $pager->setCurrentPage($page);    
    return $pager;
  }
  
}

