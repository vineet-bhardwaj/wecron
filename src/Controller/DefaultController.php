<?php

/**
 * @file
 * Contains \Drupal\wecron\Controller\DefaultController.
 */

namespace Drupal\wecron\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Drupal\views\Views;

class DefaultController extends ControllerBase
{
    protected $account;

    public function __construct()
    {

    }

    /*
     * Fetch current url.
     */
    public function get_url()
    {
        $host = \Drupal::request()->getHost();
        return $host;
    }

    /*
     * Current api version.
     */
    public function get_api_version()
    {
        return '/api/v1/json/';
    }

    /*
     * Controller calls this function to generate html file.
     */
    public function get_render_html($viewname1,$viewname2,$pagename)
    {
        $url = '//' . $this->get_url() . $this->get_api_version() . $viewname1.'/'.$viewname2.'/'.$pagename;
        $view_name = 'view-'.$viewname1.'-'.$viewname2.'-'.$pagename;
        /* Parameter for $url and $view_name */
        $rendered = $this->get_render($url,$view_name);
        return new Response($rendered);
    }

    /*
     * Common render function.
     */
    function get_render($url, $view_name)
    {
        $client = \Drupal::httpClient();
        $request = $client->get($url);
        $response = $request->getBody()->getContents();
        $items = json_decode($response, true);
        $renderable = [
            '#theme' => $view_name,
            '#items' => $items,
            '#title' => $this->t('Items'),
        ];
        $rendered = \Drupal::service('renderer')->renderPlain($renderable);
        return $rendered;
    }



}
