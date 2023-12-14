<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;

use OpenApi\Attributes as OA;

use App\Service\Pager;
use App\Service\Serializer\DTOSerializer;
use App\Entity\User;
use App\Request\UserRequest;

#[Route("/api", name: "api_")]
#[OA\Tag(name: 'User')]
class UserController extends AbstractController
{
    public function __construct(
        private DTOSerializer $serializer,
        private Pager $pager
    ) {
    }

    #[Route(
        path: "/user",
        name: "user_list",
        methods: ["GET"],
        format: 'json'
    )]
    #[OA\Response(
        response: 200,
        description: 'User list',
    )]
    #[OA\Parameter(
        name: 'offset',
        in: 'query',
        description: 'offset page',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Parameter(
        name: 'limit',
        in: 'query',
        description: 'limit page',
        schema: new OA\Schema(type: 'string')
    )]
    public function index(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        $items = $doctrine->getRepository(User::class)->getSliceItems(
            $request
        );
        $pageItems = [
            'pagination' => $this->pager->getMeta($request, \App\Entity\User::class),
            'items' => $items
        ];

        $responseContent = $this->serializer->serialize($pageItems, 'json');
        return new JsonResponse(data: $responseContent, status: Response::HTTP_OK, json: true);
    }

    #[Route("/user", name: "user_new", methods: ["POST"])]
    #[OA\Response(
        response: 200,
        description: 'Create User (sex: 0=female, 1=male)',
    )]
    #[OA\RequestBody(
        content: new OA\JsonContent(type: "object",
            example:'{
                "email": "email@server.ru",
                "name" : "user name",
                "sex" : 1,
                "birthday": "2000-10-10",
                "phone" : 89271234567
            }'
        )
    )]
    public function new(ManagerRegistry $doctrine, Request $request, UserRequest $userRequest): JsonResponse
    {
        $errors = $userRequest->validate();
        $User = $doctrine->getRepository(User::class)->create($request);
        return $this->json(
            [
                'status' => 'ok',
                'message' => 'User created success with id: ' . $User->getId()
            ],
            201
        );
    }

    #[Route("/user/{id}", name: "user_show", methods: ["GET"])]
    #[OA\Response(
        response: 200,
        description: 'Returns item User'
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        description: 'User ID',
        schema: new OA\Schema(type: 'integer')
    )]
    public function show(ManagerRegistry $doctrine, int $id): JsonResponse
    {
        $User = $doctrine->getRepository(User::class)->find($id);

        if (!$User) {
            return $this->json(
                [
                    'status' => 'failed',
                    'message' => 'No User found for id ' . $id
                ],
                404
            );
        }

        $responseContent = $this->serializer->serialize($User, 'json');
        return new JsonResponse(data: $responseContent, status: Response::HTTP_OK, json: true);
    }

    #[Route("/user/{id}", name: "user_edit", methods: ["PUT"])]
    #[OA\Response(
        response: 200,
        description: 'edit User',
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        description: 'User ID',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\RequestBody(
        content: new OA\JsonContent(type: "object",
            example:'{
                "birthday": "2000-12-10"
            }'
        )
    )]
    public function edit(ManagerRegistry $doctrine, Request $request, int $id, UserRequest $userRequest): JsonResponse
    {
        $User = $doctrine->getRepository(User::class)->find($id);

        if (!$User) {
            return $this->json(
                [
                    'status' => 'failed',
                    'message' => 'No User found for id ' . $id
                ],
                404
            );
        }

        $User = $doctrine->getRepository(User::class)->update($request, $User);

        return $this->show($doctrine, $User->getId());
    }

    #[Route("/user/{id}", name: "user_delete", methods: ["DELETE"])]
    #[OA\Response(
        response: 200,
        description: 'Delete User'
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        description: 'User ID',
        schema: new OA\Schema(type: 'integer')
    )]
    public function delete(ManagerRegistry $doctrine, int $id): JsonResponse
    {
        $User = $doctrine->getRepository(User::class)->find($id);

        if (!$User) {
            return $this->json(
                [
                    'status' => 'failed',
                    'message' => 'No User found for id ' . $id
                ],
                404
            );
        }

        $doctrine->getRepository(User::class)->remove($User, 1);
        return $this->json('User removed success with id: ' . $id, 204);
    }
}
