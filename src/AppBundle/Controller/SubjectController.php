<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Subject;
use AppBundle\Form\SubjectSearchType;
use AppBundle\Form\SubjectType;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Subject controller.
 *
 * @Route("/subject")
 */
class SubjectController extends Controller {

    /**
     * Lists all Subject entities.
     *
     * @Route("/", name="subject_index", methods={"GET"})
     *
     * @Template()
     * @param Request $request
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $dql = 'SELECT e FROM AppBundle:Subject e ORDER BY e.id';
        $query = $em->createQuery($dql);
        $paginator = $this->get('knp_paginator');
        $subjects = $paginator->paginate($query, $request->query->getint('page', 1), 25);

        return array(
            'subjects' => $subjects,
        );
    }

    /**
     * Search for Subject entities.
     *
     * @Route("/search", name="subject_search", methods={"GET"})
     *
     * @Template()
     * @param Request $request
     */
    public function searchAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(SubjectSearchType::class);
        $subjects = array();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $repo = $em->getRepository(Subject::class);
            $query = $repo->searchQuery($form->getData());
            $paginator = $this->get('knp_paginator');
            $subjects = $paginator->paginate($query, $request->query->getInt('page', 1), 25);
        }

        return array(
            'search_form' => $form->createView(),
            'subjects' => $subjects,
        );
    }

    /**
     * Creates a new Subject entity.
     *
     * @Route("/new", name="subject_new", methods={"GET","POST"})
     *
     * @Template()
     * @Security("has_role('ROLE_CONTENT_EDITOR')")
     *
     * @param Request $request
     */
    public function newAction(Request $request) {
        $subject = new Subject();
        $form = $this->createForm(SubjectType::class, $subject);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($subject);
            $em->flush();

            $this->addFlash('success', 'The new subject was created.');
            return $this->redirectToRoute('subject_show', array('id' => $subject->getId()));
        }

        return array(
            'subject' => $subject,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a Subject entity.
     *
     * @Route("/{id}", name="subject_show", methods={"GET"})
     *
     * @Template()
     * @param Subject $subject
     */
    public function showAction(Subject $subject) {

        return array(
            'subject' => $subject,
        );
    }

    /**
     * Displays a form to edit an existing Subject entity.
     *
     * @Route("/{id}/edit", name="subject_edit", methods={"GET","POST"})
     *
     * @Template()
     * @Security("has_role('ROLE_CONTENT_EDITOR')")
     *
     * @param Request $request
     * @param Subject $subject
     */
    public function editAction(Request $request, Subject $subject) {
        $editForm = $this->createForm(SubjectType::class, $subject);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success', 'The subject has been updated.');
            return $this->redirectToRoute('subject_show', array('id' => $subject->getId()));
        }

        return array(
            'subject' => $subject,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a Subject entity.
     *
     * @Route("/{id}/delete", name="subject_delete", methods={"GET"})
     *
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     *
     * @param Request $request
     * @param Subject $subject
     */
    public function deleteAction(Request $request, Subject $subject) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($subject);
        $em->flush();
        $this->addFlash('success', 'The subject was deleted.');

        return $this->redirectToRoute('subject_index');
    }

}
