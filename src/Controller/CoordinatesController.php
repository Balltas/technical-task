<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\GeocoderStrategy\GeocoderStrategyInterface;
use App\ValueObject\Address;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CoordinatesController extends AbstractController
{
    private ValidatorInterface $validator;
    private GeocoderStrategyInterface $geocoderService;
    private SerializerInterface $serializer;

    public function __construct(
        ValidatorInterface $validator,
        SerializerInterface $serializer,
        GeocoderStrategyInterface $geocoderService
    ) {
        $this->validator = $validator;
        $this->serializer = $serializer;
        $this->geocoderService = $geocoderService;
    }

    /**
     * @Route(path="/coordinates", name="coordinates", methods="post")
     */
    public function geocodeAction(Request $request): Response
    {
        /** @var Address $address */
        $address = $this->serializer->deserialize(
            $request->getContent(),
            Address::class,
            JsonEncoder::FORMAT
        );

        $errors = $this->validator->validate($address);

        if (count($errors) > 0) {
            return new JsonResponse((string)$errors, 422);
        }

        $coordinates = $this->geocoderService->getCoordinates($address);

        if ($coordinates === null) {
            return new JsonResponse([], 404);
        }

        return new JsonResponse([
            'lat' => $coordinates->getLat(),
            'lng' => $coordinates->getLng()
        ]);
    }
}
