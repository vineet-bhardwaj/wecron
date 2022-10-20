<?php

namespace Drupal\wecron\Plugin\QueueWorker;

use Drupal\Core\Controller\ControllerBase;

/**
 * Provides route responses api import.
 */
class WereferenceController extends ControllerBase
{
    /*
    * Implement function to get list of json view on page node wise.
    * @node_id : Get the node id and fetch the name of the view to generate html.
    */
    function we_get_view($node_id)
    {
        $list = array();
        $node = \Drupal\node\Entity\Node::load($node_id);
        if ($node) {
            // @field_sections : Name of the Paragraph
            foreach ($node->field_sections as $item_index => $item) {
                $paragraph = \Drupal\paragraphs\Entity\Paragraph::load($item->toArray()['target_id']);
                if ($paragraph->field_json_view_name) {
                    $json_view_val = $paragraph->field_json_view_name->value;
                    array_push($list, $json_view_val);
                }
            }
        }
        return $list;
    }
}
