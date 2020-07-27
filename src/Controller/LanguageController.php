<?php

namespace App\Controller;

use App\Dto\Language\LanguageDto;
use App\Entity\Language;
use App\Exception\DtoHandlerException;
use App\Exception\HydratorException;
use App\Form\LanguageType;
use App\Interfaces\ControllerInterface;
use App\Repository\LanguageRepository;
use App\Service\FlashBagService;
use App\Traits\ControllerTrait;
use ReflectionException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class LanguageController.
 *
 * @Route("/language")
 */
class LanguageController extends AbstractController implements ControllerInterface
{
    use ControllerTrait;

    /**
     * @Route("/", name="language.list", methods={"GET"})
     *
     * @param LanguageRepository $languageRepository
     *
     * @return Response
     *
     * @throws ReflectionException
     */
    public function languageIndex(LanguageRepository $languageRepository): Response
    {
        $repositoryOptions = [];

//        if ($soft) {
//            $repositoryOptions = ['archived' => false];
//        }

        return $this->index($languageRepository, Language::class, $repositoryOptions);
    }

    /**
     * @Route(
     *     "/{uuid}/",
     *     name="language.detail",
     *     requirements={"uuid"="[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}"},
     *     methods={"GET"}
     * )
     *
     * @param Language|null $language
     *
     * @return Response
     *
     * @throws ReflectionException
     */
    public function languageShow(?Language $language = null): Response
    {
        if (!$language) {
            return $this->languageNotFound();
        }

        return $this->show($language);
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

        return $this->redirectToRoute('language.list');
    }

    /**
     * @Route("/create/", name="language.create", methods={"GET", "POST"})
     *
     * @param Request     $request
     * @param LanguageDto $languageDto
     *
     * @return Response
     *
     * @throws ReflectionException
     */
    public function languageNew(Request $request, LanguageDto $languageDto): Response
    {
        return $this->new($request, $languageDto, Language::class, LanguageType::class);
    }

    /**
     * @Route(
     *     "/{uuid}/update/",
     *     name="language.update",
     *     requirements={"uuid"="[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}"},
     *     methods={"GET", "POST"}
     * )
     *
     * @param Request       $request
     * @param Language|null $language
     *
     * @return Response
     *
     * @throws ReflectionException
     * @throws DtoHandlerException
     * @throws HydratorException
     */
    public function languageEdit(Request $request, ?Language $language = null): Response
    {
        if (!$language) {
            return $this->languageNotFound();
        }

        return $this->edit($request, $language, LanguageType::class);
    }

    /**
     * @Route(
     *     "/{uuid}/delete/",
     *     name="language.delete",
     *     requirements={"uuid"="[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}"},
     *     methods={"GET", "POST"}
     * )
     *
     * @param Request       $request
     * @param Language|null $language
     *
     * @return Response
     *
     * @throws ReflectionException
     */
    public function languageDelete(Request $request, ?Language $language = null): Response
    {
        if (!$language) {
            return $this->languageNotFound();
        }

        return $this->delete($request, $language);
    }
}
