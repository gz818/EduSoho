<?php

namespace Topxia\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Topxia\Service\Common\ServiceException;
use Topxia\Common\ArrayToolkit;
use Topxia\Common\Paginator;


class BlockController extends BaseController
{

    public function indexAction(Request $request)
    {
        $paginator = new Paginator(
            $this->get('request'),
            $this->getBlockService()->searchBlockCount(),
            20
        );

        $findedBlocks = $this->getBlockService()->searchBlocks($paginator->getOffsetCount(),
            $paginator->getPerPageCount());
        $users = $this->getUserService()->findUsersByIds(ArrayToolkit::column($findedBlocks, 'userId'));
        return $this->render('TopxiaAdminBundle:Block:index.html.twig', array(
            'blocks'=>$findedBlocks,
            'users'=>$users,
            'paginator' => $paginator
        ));
    }

    public function previewAction(Request $request, $id)
    {
        $blockHistory = $this->getBlockService()->getBlockHistory($id);
        return $this->render('TopxiaAdminBundle:Block:blockhistory-preview.html.twig', array(
            'blockHistory'=>$blockHistory
        ));
    }

    public function updateAction(Request $request, $block)
    {

        $block = $this->getBlockService()->getBlock($block);
        $paginator = new Paginator(
            $this->get('request'),
            $this->getBlockService()->findBlockHistoryCountByBlockId($block['id']),
            20
        );

        $form = $this->getUpdateForm($block);
        $blockHistorys = $this->getBlockService()->findBlockHistorysByBlockId(
            $block['id'], 
            $paginator->getOffsetCount(),
            $paginator->getPerPageCount());

        $historyUsers = $this->getUserService()->findUsersByIds(ArrayToolkit::column($blockHistorys, 'userId'));
        if ('POST' == $request->getMethod()) {
            $form->bind($request);
            if ($form->isValid()) {
                try {
                    $block = $this->getBlockService()->updateBlock($block['id'], $form->getData());
                    $users = $this->getUserService()->findUsersByIds(array($block['userId']));
                    $html = $this->renderView('TopxiaAdminBundle:Block:list-tr.html.twig', array(
                        'block' => $block, 'users'=>$users
                    ));
                    return $this->createJsonResponse(array('status' => 'ok', 'html' => $html));
                } catch (ServiceException $e) {
                    return $this->createJsonResponse(array('status' => 'error', 'error' => array('message' => $e->getMessage())));
                }
            }
        }

        return $this->render('TopxiaAdminBundle:Block:block-update-modal.html.twig', array(
            'form' => $form->createView(),
            'block' => $block,
            'blockHistorys'=>$blockHistorys,
            'historyUsers'=>$historyUsers,
            'paginator'=>$paginator
        ));
    }

    public function editAction(Request $request, $block)
    {
        $block = $this->getBlockService()->getBlock($block);
        $form = $this->getCreateForm($block);
        if ('POST' == $request->getMethod()) {
            $form->bind($request);
            if ($form->isValid()) {
                try {
                    $block = $this->getBlockService()->updateBlock($block['id'], $form->getData());
                    $users = $this->getUserService()->findUsersByIds(array($block['userId']));
                    $html = $this->renderView('TopxiaAdminBundle:Block:list-tr.html.twig', array(
                        'block' => $block, 'users'=>$users
                    ));
                    return $this->createJsonResponse(array('status' => 'ok', 'html' => $html));
                } catch (ServiceException $e) {
                    return $this->createJsonResponse(array('status' => 'error', 'error' => array('message' => $e->getMessage())));
                }
            }
        }
        return $this->render('TopxiaAdminBundle:Block:block-modal.html.twig', array(
            'form' => $form->createView(),
            'editBlock' => $block
        ));
    }

    public function createAction(Request $request)
    {
        $form = $this->getCreateForm();
        if ('POST' == $request->getMethod()) {
            $form->bind($request);
            if ($form->isValid()) {
                try {
                    $block = $this->getBlockService()->createBlock($form->getData());
                    $users = $this->getUserService()->findUsersByIds(array($block['userId']));
                    $html = $this->renderView('TopxiaAdminBundle:Block:list-tr.html.twig', array('block' => $block,'users'=>$users));
                    return $this->createJsonResponse(array('status' => 'ok', 'html' => $html));
                } catch (ServiceException $e) {
                    return $this->createJsonResponse(array('status' => 'error', 'error' => array('message' => $e->getMessage())));
                }
            }
        }

        return $this->render('TopxiaAdminBundle:Block:block-modal.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function deleteAction(Request $request, $id)
    {
        try {
            $this->getBlockService()->deleteBlock($id);
            return $this->createJsonResponse(array('status' => 'ok'));
        } catch (ServiceException $e) {
            return $this->createJsonResponse(array('status' => 'error'));
        }
    }

    public function checkBlockCodeForCreateAction(Request $request)
    {
        $code = $request->query->get('value');
        $blockByCode = $this->getBlockService()->getBlockByCode($code);
        if (empty($blockByCode)) {
            return $this->createJsonResponse(array('success' => true, 'message' => '此编码可以使用'));
        }
        return $this->createJsonResponse(array('success' => false, 'message' => '此编码已存在,不允许使用'));
    }

    public function checkBlockCodeForEditAction(Request $request, $id)
    {
        $code = $request->query->get('value');
        $blockByCode = $this->getBlockService()->getBlockByCode($code);
        if(empty($blockByCode)){
            return $this->createJsonResponse(array('success' => true, 'message' => 'ok'));
        } elseif ($id == $blockByCode['id']){
            return $this->createJsonResponse(array('success' => true, 'message' => 'ok'));
        } elseif ($id != $blockByCode['id']){
            return $this->createJsonResponse(array('success' => false, 'message' => '不允许设置为已存在的其他编码值'));
        }
    }

    private function getCreateForm($block = array())
    {
        $form = $this->createFormBuilder($block)
            ->add('code', 'text')
            ->add('title', 'text')
            ->getForm();
        return $form;
    }

    private function getUpdateForm($block = array())
    {
        $form = $this->createFormBuilder($block)
            ->add('content', 'textarea')
            ->getForm();
        return $form;
    }

    protected function getBlockService()
    {
        return $this->getServiceKernel()->createService('Content.BlockService');
    }

}