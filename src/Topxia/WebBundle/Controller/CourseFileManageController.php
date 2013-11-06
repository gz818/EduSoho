<?php
namespace Topxia\WebBundle\Controller;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;

use Topxia\Service\Util\CloudClientFactory;
use Topxia\Common\StringToolkit;
use Topxia\Common\ArrayToolkit;
use Topxia\Common\FileToolkit;
use Topxia\Common\Paginator;


class CourseFileManageController extends BaseController
{

    public function indexAction(Request $request, $id)
    {
        $course = $this->getCourseService()->tryManageCourse($id);
        $conditions = array(
            'targetType'=>'courselesson', 
            'targetId'=>$course['id']
        );

        $paginator = new Paginator(
            $request,
            $this->getUploadFileService()->searchFileCount($conditions),
            20
        );

        $courseLessons = $this->getUploadFileService()->searchFiles(
            $conditions,
            'latestCreated',
            $paginator->getOffsetCount(),
            $paginator->getPerPageCount()
        );

        $updatedUsers = $this->getUserService()->findUsersByIds(ArrayToolkit::column($courseLessons, 'updatedUserId'));
        $createdUsers = $this->getUserService()->findUsersByIds(ArrayToolkit::column($courseLessons, 'createdUserId'));

        return $this->render('TopxiaWebBundle:CourseFileManage:index.html.twig', array(
            'course' => $course,
            'courseLessons' => $courseLessons,
            'updatedUsers' => $updatedUsers,
            'createdUsers' => $createdUsers,
            'paginator' => $paginator
        ));
    }

    public function materialAction(Request $request, $id)
    {
        
        $course = $this->getCourseService()->tryManageCourse($id);
        $conditions = array(
            'targetType'=>'coursematerial', 
            'targetId'=>$course['id']
        );

        $paginator = new Paginator(
            $request,
            $this->getUploadFileService()->searchFileCount($conditions),
            20
        );

        $courseMaterials = $this->getUploadFileService()->searchFiles(
            $conditions,
            'latestCreated',
            $paginator->getOffsetCount(),
            $paginator->getPerPageCount()
        );

        $updatedUsers = $this->getUserService()->findUsersByIds(ArrayToolkit::column($courseMaterials, 'updatedUserId'));
        $createdUsers = $this->getUserService()->findUsersByIds(ArrayToolkit::column($courseMaterials, 'createdUserId'));

        return $this->render('TopxiaWebBundle:CourseFileManage:materials.html.twig', array(
            'course' => $course,
            'courseMaterials' => $courseMaterials,
            'updatedUsers' => $updatedUsers,
            'createdUsers' => $createdUsers,
            'paginator' => $paginator
        ));
    }

    public function uploadCourseLessonAction(Request $request, $id)
    {
        $course = $this->getCourseService()->tryManageCourse($id);
        $storageSetting = $this->getSettingService()->get('storage', array());
        return $this->render('TopxiaWebBundle:CourseFileManage:modal-upload-course-lesson.html.twig', array(
            'course' => $course,
            'storageSetting' => $storageSetting,
            'targetType' => 'courselesson',
            'targetId'=>$course['id']
        ));
    }

    public function uploadCourseMaterialAction(Request $request, $id)
    {
        $course = $this->getCourseService()->tryManageCourse($id);
        $storageSetting = $this->getSettingService()->get('storage', array());
        return $this->render('TopxiaWebBundle:CourseFileManage:modal-upload-course-material.html.twig', array(
            'course' => $course,
            'storageSetting' => $storageSetting,
            'targetType' => 'coursematerial',
            'targetId'=>$course['id']
        ));
    }

    public function submitUploadCourseFilesAction(Request $request, $id, $fileType )
    {
        if($fileType == 'material'){
            return $this->redirect($this->generateUrl('course_manage_files_material',array('id'=>$id)));
        } elseif ($fileType == 'lesson'){
            return $this->redirect($this->generateUrl('course_manage_files',array('id'=>$id)));
        }
    }

    public function deleteCourseFilesAction(Request $request, $id, $type)
    {
        $ids = $request->request->get('ids', array());
        $course = $this->getCourseService()->tryManageCourse($id);
        return $this->createJsonResponse(true);
    }

    private function getCourseService()
    {
        return $this->getServiceKernel()->createService('Course.CourseService');
    }

    private function getUploadFileService()
    {
        return $this->getServiceKernel()->createService('File.UploadFileService');
    }

    private function getSettingService()
    {
        return $this->getServiceKernel()->createService('System.SettingService');
    }

}