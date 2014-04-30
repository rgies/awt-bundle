<?php

namespace RGies\AwtBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="home")
     * @Template()
     */
    public function indexAction()
    {
        $request = $this->container->get('request');
        $bundleName = $request->attributes->get('_template')->get('bundle');

        return array('bundleName' => $bundleName);
    }

    /**
     * Ticket number input form.
     *
     * @Route("/ticket", name="ticket")
     * @Template()
     */
    public function ticketAction(Request $request)
    {
        if ($request->get('issue')!== null)
        {
            $issue = mb_strtoupper($request->get('issue'));
            return $this->redirect($this->generateUrl('printTicket', array('issue' => $issue)));
        }

        return array();
    }

    /**
     * Ticket print view.
     *
     * @Route("/printTicket/{issue}", name="printTicket")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function printTicketAction($issue)
    {
        $config = $this->container->getParameter('awt_jira_connector');

        $http   = $config['protocol'];
        $url    = $config['url'];
        $user   = $config['login'];
        $pass   = $config['password'];
        //$this->get('knp_snappy.pdf')->generate('http://www.google.de', 'c:/image.pdf');

        // Jira REST Call
        $requestUrl = sprintf ('%s://%s:%s@%s/rest/api/2/issue/%s', $http, $user, $pass, $url, $issue);
        $issueResponse = file_get_contents($requestUrl);

        // Decode REST Response
        $ticket = json_decode($issueResponse);

        // Get ticket properties
        $key = $ticket->key;
        $params = array();
        $params['id']           = $ticket->id;
        $params['issue']        = $issue;
        $params['title']        = $ticket->fields->summary;
        $params['created']      = $this->_formatDate($ticket->fields->created);
        $params['reporter']     = $ticket->fields->reporter->name;
        $params['url']          = sprintf ('%s://%s/browse/%s', $http, $url, $key);
        $params['type']         = array(
            'name' => $ticket->fields->issuetype->name,
            'isBackgroundFilled' => false,
            'backgroundColor' => 'gray',
        );

        // Define issue type parameters
        switch(strtolower($params['type']['name']))
        {
            case 'story':
                $params['type']['backgroundColor'] = 'green';
                break;

            case 'bug':
                $params['type']['backgroundColor'] = 'red';
                break;

            case 'improvement':
                $params['type']['backgroundColor'] = 'blue';
                break;
        }

        //$html = 'Hello World';
        //$html = $this->renderView('AwgBundle:Default:printTicket.html.twig', $params);


        return $params;
    }

    /**
     * @Route("/ticketToPdf/{issue}", name="ticketToPdf")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function ticketToPdfAction($issue)
    {
        $pageUrl = $this->generateUrl('printTicket', array('issue' => $issue), true); // use absolute path!

        return new Response(
            $this->get('knp_snappy.pdf')->getOutput($pageUrl),
            200,
            array(
                'Content-Type'          => 'application/pdf',
                'Content-Disposition'   => 'attachment; filename="file.pdf"'
            )
        );
    }

    /**
     * Formats the given date string.
     *
     * @param string $date Date value
     * @return bool|string
     */
    private function _formatDate($date)
    {
        return date('Y-m-d', strtotime(substr($date, 0, 10)));
    }
}