<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\File;
use AppBundle\Service\MailerService;
use Doctrine\DBAL\DBALException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ApiController
 * @package AppBundle\Controller\Admin
 * @Route("/admin/api")
 * @Method({"POST"})
 */
class ApiController extends AdminController
{
    /**
     * @param string|null $name
     * @return \Doctrine\Common\Persistence\ObjectManager|object
     */
    public function doctrineManager(string $name = null) {
        return $this->getDoctrine()->getManager($name);
    }

    /**
     * @return JsonResponse
     * @Route("/mark-as-unread/{id}/{entity}", name="admin.api.message_unread")
     */
    public function markAsUnreadAjaxAction($id, $entity) {

        $entityRepository = $this->getEntityRepository($entity)->findOneById($id);

        $entityRepository->setStatus(0);

        try {
            $this->doctrineManager()->flush();
            return JsonResponse::create('ok');
        } catch (DBALException $exception) {
            return JsonResponse::create('not ok', 500);
        }
    }

    /**
	 * @param Request $request
     * @Route("/object-delete", name="admin.api.object_delete")
	 * @return JsonResponse
     */
    public function deleteObjectAjaxAction(Request $request) {

    	$objectName = $request->request->get('objectName');

    	$id = $request->request->get('objectId');

        $objectFQN = self::ENTITY_NAMESPACE_PATTERN.ucfirst($objectName);

        $objectEntity = $this->doctrineManager()->getRepository($objectFQN)->findOneById($id);

        if ($objectEntity->isRemoved()) {
			if ($this->deleteObjectRelatedFiles($objectFQN, intval($id))) {
				$this->doctrineManager()->remove($objectEntity);
				$this->doctrineManager()->flush();
				return JsonResponse::create();
			}
		}

		$objectEntity
			->setDateRemoved(new \DateTime())
			->setRemoved(true);

		$this->doctrineManager()->flush();
		return JsonResponse::create();
	}

	/**
	 * @param Request $request
	 * @Route("/repair-object", name="admin.api.object_repair")
	 * @return JsonResponse
	 */
    public function repairObjectAction(Request $request) {

		$objectName = $request->request->get('objectName');

		$id = $request->request->get('objectId');

		$objectFQN = self::ENTITY_NAMESPACE_PATTERN.ucfirst($objectName);

		$objectEntity = $this->doctrineManager()->getRepository($objectFQN)->findOneById($id);

		$objectEntity
			->setRemoved(0);

		$this->doctrineManager()->flush();

		return JsonResponse::create();
	}

    /**
     * @param $entity
     * @param $id
     * @return JsonResponse
     * @Route("/message_delete/{entity}/{id}", name="admin.api.message_delete")
     */
    public function deleteMessageAjaxAction($entity, $id) {

        $entityRepository = $this->getEntityRepository($entity)->findOneById($id);

        $this->doctrineManager()->remove($entityRepository);

        try {
            $this->doctrineManager()->flush();
            return JsonResponse::create();
        } catch (DBALException $exception) {
            return JsonResponse::create('not ok', 500);
        }

    }

    /**
     * @param File $file
     * @return JsonResponse
     * @Route("/set_image_as_default/{file}", name="admin.api.set_as_default")
     *
     */
    public function setImageAsDefaultAjaxAction(File $file) {

        $doctrine = $this->getDoctrine();

        $resp = [
            'data' => null,
            'status' => null
        ];


        $objectFiles = $doctrine->getRepository(File::class)->findBy([
            'entity' => $file->getEntity(),
            'foreignKey' => $file->getForeignKey()
        ]);


        foreach ($objectFiles as $objectFile) {
            if ($objectFile->isIsDefault() == 1) {
                $objectFile->setIsDefault(0);
            }
        }

        $file->setIsDefault(1);

        try {
            $doctrine->getManager()->flush();
            $resp['data'] = 'ok';
            $resp['status'] = 200;
        } catch (\Exception $exception) {
            $resp['data'] = 'not ok';
            $resp['status'] = 500;
        }


        return JsonResponse::create($resp['data'], $resp['status']);
    }

    /**
     * @param File $file
     * @return JsonResponse
     * @Route("/file_delete/{file}", name="admin.api.file_delete")
     */
    public function deleteFileAjaxAction(File $file) {

        $finder = new Finder();

        $fileDir = $this->getParameter('upload_directory');

        $finder->name($file->getName());

        foreach ($finder->in($fileDir) as $item) {
            unlink($item);
        }

        $this->doctrineManager()->remove($file);

        $this->doctrineManager()->flush();

        return JsonResponse::create('ok');
    }

    /**
     * @param string $objectFQN
     * @param int $objectId
     * @return bool
     */
    private function deleteObjectRelatedFiles(string $objectFQN, int $objectId) {

        $objectFiles = $this->doctrineManager()->getRepository(File::class)->findBy([
            'entity' => $objectFQN,
            'foreignKey' => $objectId,
        ]);

        foreach ($objectFiles as $objectFile) {
            $this->deleteFileAjaxAction($objectFile);
        }

        return true;

    }
}