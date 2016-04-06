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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller for /_all request
 */
class GetAllController extends AbstractRestController
{
    public function getAllAction(Request $request)
    {
        $repository = $this->getRequestRepository($request);
        $parameters = $request->query->all();

        try {
            $document = $this->getCrudService()->readAll($repository, $parameters);

            if ($document === null) {
                return $this->renderError($request, 'No documents found', Response::HTTP_NOT_FOUND);
            }
        } catch (\Exception $e) {
            return $this->renderError($request, $e->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        return $this->renderRest($request, $document, Response::HTTP_OK);
    }
}
