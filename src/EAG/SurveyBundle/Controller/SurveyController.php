<?php

namespace EAG\SurveyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use EAG\SurveyBundle\Document\Survey;
use EAG\SurveyBundle\Form\SurveyType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * Class SurveyController
 * @Route("/questionnaires")
 * @package EAG\SurveyBundle\Controller
 */
class SurveyController extends Controller
{

    /**
     * Tester Ajax
     * @Route("/ajax", name="testajax")
     */
    public function ajaxAction ()
    {
        return $this->render('EAGSurveyBundle:Survey:ajax.html.twig');
    }

    /**
     * Lister les questionnaires
     * @Route("/", name="eag_survey_list")
     */
    public function listAction()
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $listSurveys = $dm->getRepository('EAGSurveyBundle:Survey')->findAll();
        return $this->render('EAGSurveyBundle:Survey:index.html.twig',array(
            'listSurveys' => $listSurveys
        ));
    }

    /**
     * Creer un questionnaire
     * @Route("/create", name="eag_survey_create")
     */
    public function createAction(Request $request)
    {
        $survey = new Survey();

        $form   = $this->get('form.factory')->create(SurveyType::class, $survey);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $dm = $this->get('doctrine_mongodb')->getManager();
            $dm->persist($survey);
            $dm->flush();
            return $this->redirect($this->generateUrl('eag_survey_show', array('id' => $survey->getId())));
        }
        return $this->render('EAGSurveyBundle:Survey:create.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * Afficher un questionnaire
     * @Route("/{id}", name="eag_survey_show")
     */
    public function showAction(Survey $survey)
    {
        return $this->render('EAGSurveyBundle:Survey:show.html.twig', array(
            'survey' => $survey,
        ));
    }

    /**
     * Supprimer un questionnaire existant
     * @Route("/{id}/delete", name="eag_survey_delete")
     */
    public function deleteAction(Request $request, Survey $survey)
    {
        $form = $this->get('form.factory')->create();

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $dm = $this->get('doctrine_mongodb')->getManager();
            $dm->remove($survey);
            $dm->flush();
            return $this->redirect($this->generateUrl('eag_survey_list'));
        }
        return $this->render('EAGSurveyBundle:Survey:delete.html.twig', array(
            'survey' => $survey,
            'form'   => $form->createView(),
        ));
    }
}
