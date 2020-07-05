<?php

namespace App\Controller;

use App\Dto\Language\LanguageDto;
use App\Entity\Language;
use App\Event\Language\LanguageEvent;
use App\Event\Language\LanguageViewEvent;
use App\Form\DeleteType;
use App\Form\LanguageType;
use App\Repository\LanguageRepository;
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
    public function list(LanguageRepository $languageRepository): Response
    {
        $languages = $languageRepository->findAll();

        $this->getViewService()->setData('language/index.html.twig', ['languages' => $languages]);

        $this->dispatchEvent(LanguageViewEvent::LIST, [ViewService::NAME => $this->getViewService()]);

        return $this->getResponse();
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
     */
    public function detail(?Language $language = null): Response
    {
        if (!$language) {
            return $this->languageNotFound();
        }

        $this->getViewService()->setData('language/detail.html.twig', ['language' => $language]);

        $this->dispatchEvent(LanguageViewEvent::DETAIL, [ViewService::NAME => $this->getViewService()]);

        return $this->getResponse();
    }

    /**
     * @return Response
     */
    public function languageNotFound(): Response
    {
        $this->addAndTransFlashMessage(self::FLASH_ERROR, 'Language', 'The language was not found.', 'language');

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
     * @param Request     $request
     * @param LanguageDto $languageDto
     *
     * @return Response
     */
    public function create(Request $request, LanguageDto $languageDto): Response
    {
        $form = $this->createForm(LanguageType::class, $languageDto, [
            'action' => $this->generateUrl('languages.create'),
        ]);

        $this->dispatchEvent(LanguageEvent::PRE_CREATE, [
            'form' => $form,
            'languageDto' => $languageDto,
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->dispatchEvent(LanguageEvent::CREATE, ['languageDto' => $languageDto]);

            $this->addAndTransFlashMessage(self::FLASH_SUCCESS, 'Language', 'Language successfully created.', 'language');

            return $this->redirectToLanguageList();
        }

        $this->getViewService()->setData('language/type.html.twig', ['form' => $form->createView()]);

        $this->dispatchEvent(LanguageViewEvent::CREATE, [ViewService::NAME => $this->getViewService()]);

        return $this->getResponse();
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
    public function update(Request $request, LanguageService $languageService, ?Language $language = null): Response
    {
        if (!$language) {
            return $this->languageNotFound();
        }

        $languageDto = $languageService->createLanguageDtoFromLanguage($language);
        $form = $this->createForm(LanguageType::class, $languageDto, [
            'action' => $this->generateUrl('languages.update', ['id' => $language->getId()]),
        ]);

        $this->dispatchEvent(LanguageEvent::PRE_UPDATE, [
            'form' => $form,
            'languageDto' => $languageDto,
            'language' => $language,
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->dispatchEvent(LanguageEvent::UPDATE, [
                'languageDto' => $languageDto,
                'language' => $language,
            ]);

            $this->addAndTransFlashMessage(self::FLASH_SUCCESS, 'Language', 'Language successfully updated.', 'language');

            return $this->redirectToLanguageList();
        }

        $this->getViewService()->setData('language/type.html.twig', ['form' => $form->createView()]);

        $this->dispatchEvent(LanguageViewEvent::UPDATE, [ViewService::NAME => $this->getViewService()]);

        return $this->getResponse();
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

            $this->addAndTransFlashMessage(self::FLASH_SUCCESS, 'Language', 'Language successfully deleted.', 'language');

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
