<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;

class Pager
{
    public function __construct(ManagerRegistry $doctrine, private ContainerBagInterface $params)
    {
        $this->doctrine = $doctrine;
        $this->params = $params;
    }

    public function getMeta(Request $request, $class, $total = null): array
    {
        if (is_null($total)) {
            $total = $this->doctrine->getRepository($class)->getTotal();
        }

        $meta = [
            'total' => $total,
            'limit' => $this->getLimit($request),
            'offset' => (int)$request->query->get('offset')
        ];
        return $meta;
    }

    private function getLimit(Request $request): int
    {
        $limit = (int)$request->query->get('limit');
        if (!$limit or is_null($limit)) {
            $limit = $this->params->get('paginator_limit');
        }
        return $limit;
    }
}
