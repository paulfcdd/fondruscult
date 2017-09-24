<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\File;
use AppBundle\Entity\News;
use AppBundle\Service\FileUploaderService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class AdminController extends Controller
{

	const ENTITY_NAMESPACE_PATTERN = 'AppBundle\\Entity\\';

	/**
     * @Route("/admin/dashboard", name="admin.index")
     */
    public function indexAction() {

        return $this->render(':default/admin:index.html.twig', [
        ]);
    }

    /**
     * @param string $entity
     *
     * @return Response
     * @Route("/admin/{entity}/list", name="admin.list")
     */
    public function listAction(string $entity) {

        $objects = $this->getEntityRepository($entity)->findBy(['removed' => false]);

        return $this->render(':default/admin:list.html.twig', [
            'objects' => $objects,
        ]);
    }

    /**
     * @param $entity
     * @param $id
     * @return Response
     * @Route("/admin/{entity}/manage/{id}", name="admin.manage")
     */
    public function manageAction(string $entity, int $id = null, Request $request) {

        $em = $this->getDoctrine()->getManager();

        $uploader = $this->get(FileUploaderService::class);

        $files = null;

        $className = ucfirst($entity);

        $class = self::ENTITY_NAMESPACE_PATTERN.$className;

        $object = new $class();

        if ($id) {
            $object = $this->getEntityRepository($entity)->findOneById($id);
        }

        $form = $this->entityFormBuilder($className, $object);


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $formData = $form
                ->getData();


            if ($formData instanceof News) {
                $formData->setAuthor($this->getUser());
            }

            $em->persist($formData);
            $em->flush();

            if (isset($form['files'])) {
                $attachedFiles = $form['files']->getData();

                if ($attachedFiles instanceof UploadedFile) {

                    $em->persist(
                        $this->photoUploader($uploader, $entity, $attachedFiles, $formData, $class)
                    );
                }

                if (!empty($attachedFiles)) {

                    foreach ($attachedFiles as $attachedFile) {

                        $file = new File();

                        $uploader
                            ->setDir($entity)
                            ->setFile($attachedFile);

                        $file
                            ->setForeignKey($formData->getId())
                            ->setMimeType(strtolower($uploader->getMimeType()))
                            ->setEntity($class)
                            ->setName($uploader->upload());

                        $em->persist($file);

                    }

                    $em->flush();
                }
            }

            return $this->redirectToRoute('admin.manage', [
                'entity' => $entity,
                'id' => $formData->getId()
            ]);
        }

        return $this->render(':default/admin:manage.html.twig', [
            'form' => $form->createView(),
            'object' => $object,
        ]);
    }

	/**
	 * @param string $entity
	 * @Route("/admin/{entity}/trash", name="admin.trash")
	 * @return Response
	 */
	public function trashObjectsListAction(string $entity) {

		return $this->render(':default/admin:list.html.twig', [
			'objects' => $this->getEntityRepository($entity)->findBy(['removed' => true]),
			'template' => 'trash_list',
		]);

	}


	protected function getEntityRepository(string $entity) {

		return $this->getDoctrine()->getRepository($this->getClassFQN($entity));

	}

	protected function getClassFQN(string $entity) {

		$className = ucfirst($entity);

		$class = 'AppBundle\\Entity\\'.$className;

		return $class;
	}

    /**
     * @param FileUploaderService $uploader
     * @param string $entity
     * @param UploadedFile $uploadedFile
     * @param $formData
     * @param string $class
     * @return File
     */
    private function photoUploader(FileUploaderService $uploader, string $entity, UploadedFile $uploadedFile, $formData, string $class) {
        $file = new File();

        $uploader
            ->setDir($entity)
            ->setFile($uploadedFile);

        $file
            ->setForeignKey($formData->getId())
            ->setMimeType($uploader->getMimeType())
            ->setEntity($class)
            ->setName($uploader->upload());

        return $file;
    }

    /**
     * @param $entity
     * @param $id
     * @return Response
     * @Route("/admin/{entity}/manage/{id}/files", name="admin.manage.files")
     */
    public function fileManagerAction(string $entity, int $id) {


        return $this->render(':default/admin:files.html.twig', [
            'files' => $this->fileLoader($this->getClassFQN($entity), $id),
            'imagesExt' => FileUploaderService::IMAGES,
            'videosExt' => FileUploaderService::VIDEOS,
        ]);
    }

    /**
     * @param $className
     * @param $object
     * @return Form
     */
    private function entityFormBuilder($className, $object) {

        $formName = 'AppBundle\Form\\'.$className.'Type';

        $form = $this->createForm($formName, $object);

        return $form;

    }

    /**
     * @param string $class
     * @param int $id
     * @return array
     */
    private function fileLoader(string $class, int $id) {

        $doctrine = $this->getDoctrine();

        $files = $doctrine->getRepository(File::class)->findBy(
            ['foreignKey' => $id, 'entity' => $class]
        );

        return $files;

    }
}