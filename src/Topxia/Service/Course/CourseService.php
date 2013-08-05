<?php

namespace Topxia\Service\Course;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface CourseService
{

	/**
	 * 每个课程可添加的最大的老师人数
	 */
	const MAX_TEACHER = 100;

	/**
	 * Course API
	 */

	public function getCourse($id);

	public function findCoursesByIds(array $ids);

	public function searchCourses($conditions, $sort = 'latest', $start, $limit);

	public function searchCourseCount($conditions);

	public function createCourse($course);

	public function updateCourse($id, $fields);

	public function updateCourseCounter($id, $counter);

	public function changeCoursePicture ($id, UploadedFile $picture);

	public function getUserLeaningCoursesCount($userId);
	
	public function getUserLeanedCoursesCount($userId);

	public function getUserTeachingCoursesCount($userId);
	
	public function findUserLeaningCourses($userId, $start, $limit);

	public function findUserLeanedCourses($userId, $start, $limit);
	
	public function findUserTeachingCourses($userId, $start, $limit);

	/**
	 * 删除课程
	 */
	public function deleteCourse($id);

	public function publishCourse($id);

	public function closeCourse($id);

	public function joinCourse($userId, $courseId, array $infos = array());

	public function exitCourse($userId, $courseId);


	/**
	 * Lesson API
	 */
	public function findLessonsByIds(array $ids);

	public function getCourseLesson($courseId, $lessonId);
	
	public function getCourseLessons($courseId);

	public function createLesson($lesson);

	public function updateLesson($courseId, $lessonId, $fields);

	public function deleteLesson($courseId, $lessonId);

	public function publishLesson($courseId, $lessonId);

	public function unpublishLesson($courseId, $lessonId);

	public function getNextLessonNumber($courseId);

	public function startLearnLesson($courseId, $lessonId);

	public function finishLearnLesson($courseId, $lessonId);

	public function cancelLearnLesson($courseId, $lessonId);

	public function getUserLearnLessonStatus($userId, $courseId, $lessonId);

	public function getUserLearnLessonStatuses($userId, $courseId);

	public function getUserNextLearnLesson($userId, $courseId);

	/**
	 * Chapter API
	 */
	
	public function getChapter($courseId, $chapterId);

	public function getCourseChapters($courseId);

	public function createChapter($chapter);

	public function updateChapter($courseId, $chapterId, $fields);

	public function deleteChapter($courseId, $chapterId);

	public function getNextChapterNumber($courseId);

	/**
	 * 获得课程的目录项
	 * 
	 * 目录项包含，章节、课时、测验
	 * 
	 */
	public function getCourseItems($courseId);

	public function sortCourseItems($courseId, array $itemIds);

	/**
	 * Member API
	 */
	public function getCourseMember($courseId, $userId);

	public function findCourseStudents($courseId, $start, $limit);

	public function getCourseStudentCount($courseId);

	public function findCourseTeachers($courseId);

	public function setCourseTeachers($courseId, $teachers);
	
	/**
	 * 尝试管理课程, 无权限则抛出异常
	 * 例如：编辑、上传资料...
	 * 
	 * @param  Integer $courseId 课程ID
	 * @return array 课程信息
	 */
	public function tryManageCourse($courseId);

	/**
	 * 是否可以管理课程
	 * @param  [mixed] $course ID或Course数组。
	 * @return [Boolean] 可以管理返回true，否则返回false
	 */
	public function canManageCourse($course);

	/**
	 * 尝试使用课程
	 * 例如：查看收费课时、提问、下载资料...
	 * 
	 * @param  Integer $courseId 课程ID
	 * @return array 课程信息
	 */
	public function tryTakeCourse($courseId);

	/**
	 * 尝试学习课程
	 * 
	 * 只有是课程的学员/老师，才可以学习。
	 * 
	 * @param  [type] $courseId 课程ID
	 * @return array
	 */
	public function tryLearnCourse($courseId);

	public function favoriteCourse($courseId);

	public function unFavoriteCourse($courseId);

	public function hasFavoritedCourse($courseId);

	public function findUserFavoriteCourses($userId, $start, $limit);

	public function getUserFavoriteCourseCount($userId);



	/*announcement*/
	public function createAnnouncement($courseId, $fields);

	public function getCourseAnnouncement($courseId, $id);

	public function deleteCourseAnnouncement($courseId, $id);

	public function findAnnouncements($courseId, $start, $limit);

	public function updateAnnouncement($courseId, $id, $fields);

}