<?php

namespace App\Controller;

use App\Entity\Idea;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class IdeaController extends Controller
{
    /**
     * @Route("/idea/{ideaId}", name="idea_show")
     * @param $ideaId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function detailedIdeaAction($ideaId)
    {
        $idea=$this->getDoctrine()->getRepository(Idea::class)->find($ideaId);

        return $this->render('frontend/detailedIdea.html.twig', [
            'idea' => $idea,
        ]);
    }
}