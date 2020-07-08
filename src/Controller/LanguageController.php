<?php

namespace App\Controller;

use App\Dto\Language\LanguageDto;
use App\Entity\Language;
use App\Event\Language\LanguageEvent;
use App\Event\Language\LanguageViewEvent;
use App\Exception\EventException as EventExceptionAlias;
use App\Form\DeleteType;
use App\Form\LanguageType;
use App\Repository\LanguageRepository;
use App\Service\FlashBagService;
use App\Service\Language\LanguageService;
use App\Service\ViewService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LanguageController extends DefaultController
{
    /**
     * @Route("/languages", name="languages.list")
     *
     * @param LanguageRepository $languageRepository
     *
     * @return Response
     */
    public function languageList(LanguageRepository $languageRepository): Response
    {
        return $this->list($languageRepository, 'language');
    }

    /**
     * @Route(
     *     "/languages/{id}",
     *     name="languages.detail",
     *     requirements={"id"="\d+"}
     * )
     *
     * @param Language|null $language
     *
     * @return Response
     *
     * @throws EventExceptionAlias
     */
    public function languageDetail(?Language $language = null): Response
    {
        if (!$language) {
            return $this->languageNotFound();
        }

        return $this->detail($language, 'language');
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

        return $this->redirectToLanguageList();
    }

    /**
     * @return Response
     */
    public function redirectToLanguageList(): Response
    {
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
            ->setEntityName('language')
            ->setDto($languageDto);

        return $this->create($request, $languageService);
    }

    /**
     * @Route(
     *     "/languages/{id}/update",
     *     name="languages.update",
     *     requirements={"id"="\d+"}
     * )
     *
     * @param Request         $request
     * @param LanguageService $languageService
     * @param Language        $language
     *
     * @return Response
     */
    public function languageUpdate(Request $request, LanguageService $languageService, ?Language $language = null): Response
    {
        if (!$language) {
            return $this->languageNotFound();
        }

        $languageDto = $languageService->createLanguageDtoFromLanguage($language);

        $languageService
            ->setFormType(LanguageType::class)
            ->setEntityName('language')
            ->setDto($languageDto)
            ->setEntity($language);

        return $this->create($request, $languageService);
    }

    /**
     * @Route(
     *     "/languages/{id}/delete",
     *     name="languages.delete",
     *     requirements={"id"="\d+"}
     * )
     *
     * @param Request       $request
     * @param Language|null $language
     *
     * @return Response
     */
    public function delete(Request $request, ?Language $language = null): Response
    {
        if (!$language) {
            return $this->languageNotFound();
        }

        $form = $this->createForm(DeleteType::class, null, [
            'action' => $this->generateUrl('languages.delete', ['id' => $language->getId()]),
        ]);

        $this->dispatchEvent(LanguageEvent::PRE_DELETE, [
            'form' => $form,
            'language' => $language,
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->dispatchEvent(LanguageEvent::DELETE, ['language' => $language]);

            $this->addAndTransFlashMessage(
                FlashBagService::FLASH_SUCCESS,
                'Language',
                'Language successfully deleted.',
                'language'
            );

            return $this->redirectToLanguageList();
        }

        $this->getViewService()->setData('delete.html.twig', [
            'form' => $form->createView(),
            'message' => $this->trans('Are you sure you want to delete this language (%language%) ?', 'language', [
                'language' => $language->getIso6391(),
            ]),
        ]);

        $this->dispatchEvent(LanguageViewEvent::DELETE, [ViewService::NAME => $this->getViewService()]);

        return $this->getResponse();
    }
}
