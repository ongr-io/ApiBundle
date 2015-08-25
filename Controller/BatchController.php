<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ApiBundle\Controller;

use ONGR\ApiBundle\Request\RestRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class BatchController extends AbstractRestController implements BatchControllerInterface
{
    /**
     * {@inheritdoc}
     */
    public function batchAction(RestRequest $restRequest)
    {
        try {
            return $this->renderRest(
                $this->get('ongr_api.batch_processor')->handle($restRequest),
                Response::HTTP_ACCEPTED
            );
        } catch (ResourceNotFoundException $e) {
            $error = [
                'message' => 'Could not resolve path!',
                'error'   => $e->getMessage()
            ];
        } catch (Exception $e) {
            $error = [
                'message' => 'Error',
                'error' => $e->getMessage()
            ];
        }

        return $this->renderRest($error, Response::HTTP_BAD_REQUEST);
    }
}
