<?php

/**
 * @file
 * Contains \Drupal\wecron\Plugin\QueueWorker\DefaultQueueWorker.
 */

namespace Drupal\wecron\Plugin\QueueWorker;

use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Queue\QueueWorkerBase;
use Drupal\we_api\Plugin\QueueWorker\WereferenceController;

/**
 * Processes tasks for example module.
 *
 * @QueueWorker(
 *   id = "default_queue",
 *   title = @Translation("Example: Default worker"),
 *   cron = {"time" = 90}
 * )
 */
class DefaultQueueWorker extends QueueWorkerBase
{

    /**
     * {@inheritdoc}
     */
    public function processItem($id)
    {
        $wereference = new WereferenceController;
        /* Static node id's */
        $node_ids = [107, 170, 195, 243, 324, 355, 387, 304];
        foreach ($node_ids as $id) {
            $items = $wereference->we_get_view($id);
            /*
            * THIS CODE WILL RUN AT 7:00 AM , 12:00 AM , 19:00 PM
            */
            $current_time = \Drupal::time()->getCurrentTime();
            $fix_time = \Drupal::service('date.formatter')->format($current_time, 'custom', 'H');
            $int_time = (int)$fix_time;
            $time_string = '<!-- Time Run ' . $fix_time . ' -->';
            $min = 1;
            $max = 6;

            if (($int_time >= $min) && ($int_time <= $max)) {
                /* Loop through all the list and generate files. */
                foreach ($items as $item) {
                    $str = substr($item, 1);
                    $directories = explode("/", $str);
                    $directory_full_name = 'public://api/v1/html/' . $directories[0] . '/' . $directories[1];
                    /* Html request */
                    $url = '//' . $this->get_url() . '/api/' . $this->get_api_version() . '/html' . $item;
                    $client = \Drupal::httpClient();
                    $request = $client->get($url, ['connect_timeout' => 5]);
                    $content = $request->getBody()->getContents(); //You would like JSON decode the response.
                    if (\Drupal::service('file_system')->prepareDirectory($directory_full_name, FileSystemInterface::CREATE_DIRECTORY)) {
                        /** @var \Drupal\file\FileInterface $file */
                        $file = file_save_data($content . $time_string, $directory_full_name . '/' . $directories[2] . '.html', $replace = 1);
                    } else {
                        \Drupal::service('file_system')->prepareDirectory($directory_full_name, FileSystemInterface::CREATE_DIRECTORY);
                        /** @var \Drupal\file\FileInterface $file */
                        file_save_data($content . $time_string, $directory_full_name . '/' . $directories[2] . '.html', $replace = 1);
                    }
                }
            }
        }
    }

    public function get_url()
    {
        $host = \Drupal::request()->getHost();
        return $host;
    }

    public function get_api_version()
    {
        return 'v1';
    }
}
