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

/**
 * Controller for handling batch requests.
 */
class BatchController extends AbstractRestController implements BatchControllerInterface
{
    /**
     * {@inheritdoc}
     */
    public function batchAction(RestRequest $restRequest)
    {
        $data = $this->get('ongr_api.batch_processor')->handle($restRequest);

        if ($data !== false) {
            return $this->renderRest($data, Response::HTTP_ACCEPTED);
        }

        return $this->renderRest(['message' => 'Deserialization error!'], Response::HTTP_BAD_REQUEST);
    }
}
