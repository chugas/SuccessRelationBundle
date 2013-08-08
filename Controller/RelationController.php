<?php

namespace Success\RelationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;

class RelationController extends Controller {
  
  protected $pager = false;
  
  public function buttonsAction($id) {
    $user = $this->getEntityUser($id);
    $response = $this->render('RelationBundle:Relations:_buttons.html.twig', array('user' => $user));
    return $response;
  }
    
  public function addContactAction($id) {
    $userLogged = $this->getUser();
    if(is_null($userLogged)) {
      throw new NotFoundHttpException('Entity 1 no encontrada.');
    } else {
      $userContact = $this->getEntityUser($id);
      if (!is_object($userContact)) {
        throw new NotFoundHttpException('Entity 2 no encontrada.');
      }
    }
    
    $manager = $this->container->get('success.relation.manager');
    $manager->create($userLogged, $userContact, 'follow');

    $response = new JsonResponse(array('status' => 200), 200, array());

    return $response;
  }
  
  public function removeContactAction($id) {
    $userLogged = $this->getUser();
    if(is_null($userLogged)) {
      throw new NotFoundHttpException('Entity 1 no encontrada.');
    } else {
      $userContact = $this->getEntityUser($id);
      if (!is_object($userContact)) {
        throw new NotFoundHttpException('Entity 2 no encontrada.');
      }
    }
    
    $manager = $this->container->get('success.relation.manager');
    $manager->remove($userLogged, $userContact);

    $response = new JsonResponse(array('status' => 200), 200, array());

    return $response;
  }
  
  public function contactsAction($id) {
    $owner = $this->getEntityUser($id);
    
    $manager = $this->container->get('success.relation.manager');
    $collection = $manager->getFollowings($owner, 5);

    $response = $this->render('RelationBundle:Relations:contacts.html.twig', array(
        'owner' => $owner,
        'collection' => $collection
    ));

    return $response;
  }

  public function myListAction() {
    $user = $this->getUser();
    $collection = $this->getPager($user);

    $response = $this->render('RelationBundle:Relations:list.html.twig', array(
        'owner' => $user, 
        'collection' => $collection, 
        'haveToPaginate' => $this->pager->hasNextPage(),
        'page' => ($this->pager->hasNextPage() ? $this->pager->getNextPage() : 1)
    ));
    
    $response->setMaxAge(5 * 60);
    return $response;
  }  
  
  public function listAction($id) {
    $user = $this->getEntityUser($id);
    $collection = $this->getPager($user);

    $response = $this->render('RelationBundle:Relations:list.html.twig', array(
        'owner' => $user, 
        'collection' => $collection, 
        'haveToPaginate' => $this->pager->hasNextPage(),
        'page' => ($this->pager->hasNextPage() ? $this->pager->getNextPage() : 1)
    ));

    $response->setMaxAge(5 * 60);

    return $response;
  }
  
  public function pageAction($id, $page = 1) {
    $user = $this->getEntityUser($id);
    $collection = $this->getPager($user, $page);
    
    if ($this->getRequest()->isXmlHttpRequest()) {

      $html = $this->renderView('RelationBundle:Relations/Pager:_pager.html.twig', array(
          'owner' => $user,
          'collection' => $collection
      ));
      
      $data = array(
          'responseCode' => 200, 
          'response' => $html, 
          'haveToPaginate' => $this->pager->hasNextPage(),
          'href' => $this->get('router')->generate('relation_list_pager', array('id' => $user->getId(), 'page' => ($this->pager->hasNextPage() ? $this->pager->getNextPage() : 1)))
      );
      $response = new JsonResponse($data, 200);

    } else {

      $response = $this->render('RelationBundle:Relations:list.html.twig', array(
          'owner' => $user,
          'collection' => $collection,
          'haveToPaginate' => $this->pager->hasNextPage(),
          'page' => ($this->pager->hasNextPage() ? $this->pager->getNextPage() : 1)
      ));

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
  
  public function getPager($entity, $page = 1){
    $manager = $this->container->get('success.relation.manager');
    $queryBuilder = $manager->getFollowingsQuery($entity);
    
    $pager = new Pagerfanta(new DoctrineORMAdapter($queryBuilder));
    $pager->setMaxPerPage(10);
    $pager->setCurrentPage($page);
    
    $relationsResults = $pager->getCurrentPageResults();

    $collection = array();
    foreach ($relationsResults as $relation) {
      $collection[$relation->getId()] = $relation->getEntity2();
    }
    
    $this->pager = $pager;
    
    return $collection;    
  }
  
}
