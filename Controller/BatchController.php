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

use ONGR\ApiBundle\Request\RestRequestProxy;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class BatchController extends AbstractRestController implements BatchControllerInterface
{
    /**
     * @var OptionsResolver
     */
    private $resolver;

    public function __construct()
    {
        $this->resolver = new OptionsResolver();
        $this->resolver
            ->setRequired(['method', 'path', 'body'])
            ->setAllowedTypes(
                [
                    'method' => 'string',
                    'path' => 'string',
                    'body' => 'array'
                ]
            )
            ->setNormalizer('method', function ($method) {
                return strtoupper($method);
            });
    }

    /**
     * {@inheritdoc}
     *
     * TODO: REFACTOR SO THAT GET WOULD FIT TO.
     */
    public function batchAction(Request $request)
    {
        try {
            $data = $this
                ->get('serializer')
                ->deserialize($request->getContent(), 'array', $request->getContentType());
        } catch (\Exception $e) {
            return $this->renderRest(
                [
                    'message' => 'Deserialization error',
                    'error' => $e->getMessage()
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        $response = [];
        foreach ($data as $key => $action) {
            try {
                $options = $this->get('router')->getMatcher()->match($action['path']);
            } catch (ResourceNotFoundException $e) {
                $response[] = ['message' => 'Failed to match path!'];
                continue;
            }

            if ($action['method'] !== 'GET') {
                $manager = $this->get($options['manager']);

                if ($options['id'] !== null) {
                    $action['body']['_id'] = $options['id'];
                } elseif (isset($action['body']['id'])) {
                    $action['body']['_id'] = $action['body']['id'];
                }

                try {
                    $manager
                        ->getConnection()
                        ->bulk($this->resolveMethod($action['method']), $options['type'], $action['body']);
                    $manager->getConnection()->commit();
                    $response[$key] = ['message' => 'Success'];
                } catch (\Exception $e) {
                    $response[$key] = ['message' => 'Failed', 'error' => $e->getMessage()];
                }
            }
        }

        return $this->renderRest($response, Response::HTTP_ACCEPTED);
    }

    /**
     * @param $method
     *
     * @return string
     */
    protected function resolveMethod($method)
    {
        switch ($method) {
            case 'POST':
                return 'create';
            case 'DELETE':
                return 'delete';
            default:
                return 'index';
        }
    }
}
