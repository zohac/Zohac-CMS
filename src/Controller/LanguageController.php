<?php

namespace App\Controller;

use App\Dto\Language\LanguageDto;
use App\Entity\Language;
use App\Form\LanguageType;
use App\Repository\LanguageRepository;
use App\Service\FlashBagService;
use App\Service\Language\LanguageService;
use ReflectionException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LanguageController extends DefaultController
{
    /**
     * @Route("/languages", name="languages.list")
     *
     * @param LanguageRepository $languageRepository
     * @param LanguageService    $languageService
     *
     * @return Response
     */
    public function languageList(LanguageRepository $languageRepository, LanguageService $languageService): Response
    {
        return $this->list($languageRepository, $languageService);
    }

    /**
     * @Route(
     *     "/languages/{uuid}",
     *     name="languages.detail",
     *     requirements={"uuid"="[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}"}
     * )
     *
     * @param LanguageService $languageService
     * @param Language|null   $language
     *
     * @return Response
     */
    public function languageDetail(LanguageService $languageService, ?Language $language = null): Response
    {
        if (!$language) {
            return $this->languageNotFound();
        }

        return $this->detail($language, $languageService);
    }

    /**
     * @return Response
     */
    public function languageNotFound(): Response
    {
        $this->addAndTransFlashMessage(
            FlashBagService::FLASH_ERROR,
            'Language',
            'The language was not found.',
            'language'
        );

        return $this->redirectToRoute('languages.list');
    }

    /**
     * @Route("/languages/create", name="languages.create")
     *
     * @param Request         $request
     * @param LanguageDto     $languageDto
     * @param LanguageService $languageService
     *
     * @return Response
     */
    public function languageCreate(
        Request $request,
        LanguageDto $languageDto,
        LanguageService $languageService
    ): Response {
        $languageService
            ->setFormType(LanguageType::class)
            ->setDto($languageDto);

        return $this->create($request, $languageService);
    }

    /**
     * @Route(
     *     "/languages/{uuid}/update",
     *     name="languages.update",
     *     requirements={"uuid"="[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}"}
     * )
     *
     * @param Request         $request
     * @param LanguageService $languageService
     * @param Language|null   $language
     *
     * @return Response
     *
     * @throws ReflectionException
     */
    public function languageUpdate(
        Request $request,
        LanguageService $languageService,
        ?Language $language = null
    ): Response {
        if (!$language) {
            return $this->languageNotFound();
        }

        $languageDto = $languageService->createLanguageDtoFromLanguage($language);

        $languageService
            ->setFormType(LanguageType::class)
            ->setDto($languageDto)
            ->setEntity($language);

        return $this->update($request, $languageService);
    }

    /**
     * @Route(
     *     "/languages/{uuid}/delete",
     *     name="languages.delete",
     *     requirements={"uuid"="[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}"}
     * )
     *
     * @param Request         $request
     * @param LanguageService $languageService
     * @param Language|null   $language
     *
     * @return Response
     */
    public function languageDelete(
        Request $request,
        LanguageService $languageService,
        ?Language $language = null
    ): Response {
        if (!$language) {
            return $this->languageNotFound();
        }

        $languageService->setEntity($language);

        return $this->delete($request, $languageService);
    }
}
