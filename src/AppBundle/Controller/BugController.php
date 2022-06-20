<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Bug;
use AppBundle\Form\BugType;

use Doctrine\ORM\Query;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
/**
 * Bug controller.
 *
 * @Route("bug")
 */
class BugController extends Controller
{
    /**
     * Lists all bug entities.
     *
     * @Route("/", name="bug_index")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $query = $this->getDoctrine()->getRepository('AppBundle:Bug')
            ->getRecentBugsArrayQuery();
        /** @var Query $query */


        $paginator = $this->get('knp_paginator');        
        /** @var SlidingPagination $pagination */
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1), // page number
            5  // limit per page
        );
        
        return $this->render('bug/index.html.twig', array(
            'pagination' => $pagination,
        ));
    }

    /**
     * Creates a new bug entity.
     *
     * @Route("/new", name="bug_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $bug = new Bug();
        $form = $this->createForm('AppBundle\Form\BugType', $bug);
        
        $form->handleRequest($request);
                
        if ($form->isSubmitted() && $form->isValid()) {
            $bug->setStatus(Bug::STATUS_OPEN);
            $bug->setCreated(new \DateTime("now"));

            $em = $this->getDoctrine()->getManager();
            $em->persist($bug);
            $em->flush();

            return $this->redirectToRoute('bug_show', array('id' => $bug->getId()));
        }

        return $this->render('bug/new.html.twig', array(
            'bug' => $bug,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a bug entity.
     *
     * @Route("/{id}", name="bug_show")
     * @Method("GET")
     */
    public function showAction(Bug $bug)
    {
        $deleteForm = $this->createDeleteForm($bug);
        dump(get_class($bug->getEngineer()));

        return $this->render('bug/show.html.twig', array(
            'bug' => $bug,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing bug entity.
     *
     * @Route("/{id}/edit", name="bug_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Bug $bug)
    {
        $deleteForm = $this->createDeleteForm($bug);
        $editForm = $this->createForm('AppBundle\Form\BugType', $bug);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('bug_index', array('id' => $bug->getId()));
        }

        return $this->render('bug/edit.html.twig', array(
            'bug' => $bug,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a bug entity.
     *
     * @Route("/{id}", name="bug_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Bug $bug)
    {
        $form = $this->createDeleteForm($bug);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($bug);
            $em->flush();
        }

        return $this->redirectToRoute('bug_index');
    }

    /**
     * Creates a form to delete a bug entity.
     *
     * @param Bug $bug The bug entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Bug $bug)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('bug_delete', array('id' => $bug->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    /**
     * @Route("/{id}/close", name="bug_close")
     * @Method("PUT")
     */

     public function closeAction(Request $request, $id)
     {
        $repository = $this->getDoctrine()->getRepository("AppBundle:Bug");
        $bug = $repository->find($id);
        if(!$bug){
            throw $this->createNotFoundException('No bug found for id '.$id);
        }
        if ($this->isCsrfTokenValid('close_bug', $request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $bug->close();
            $em->flush();
        }
        return $this->redirectToRoute("bug_show",["id"=>$bug->getId()]);
     }
}