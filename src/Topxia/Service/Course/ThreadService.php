<?php
namespace Topxia\Service\Course;

interface ThreadService
{
	public function getThread($courseId, $threadId);

	public function findThreadsByType($courseId, $type, $sort = 'latestCreated', $start, $limit);

	public function searchThreads($conditions, $sort, $start, $limit);

	public function searchThreadCount($conditions);

	/**
	 * 创建话题
	 */
	public function createThread($thread);

	public function deleteThread($courseId, $threadId);

	public function stickThread($courseId, $threadId);

	public function unstickThread($courseId, $threadId);

	public function eliteThread($courseId, $threadId);

	public function uneliteThread($courseId, $threadId);

	/**
	 * 点击查看话题
	 *
	 * 此方法，用来增加话题的查看数。
	 * 
	 * @param integer $courseId 课程ID
	 * @param integer $threadId 话题ID
	 * 
	 */
	public function hitThread($courseId, $threadId);

	/**
	 * 获得话题的回帖
	 * 
	 * @param integer  $courseId 话题的课程ID
	 * @param integer  $threadId 话题ID
	 * @param string  	$sort     排序方式： defalut按帖子的发表时间顺序；best按顶的次序排序。
	 * @param integer 	$start    开始行数
	 * @param integer 	$limit    获取数据的限制行数
	 * 
	 * @return array 获得的话题回帖列表。
	 */
	public function findThreadPosts($courseId, $threadId, $sort = 'default', $start, $limit);

	/**
	 * 获得话题回帖的数量
	 * @param  integer  $courseId 话题的课程ID
	 * @param  integer  $threadId 话题ID
	 * @return integer  话题回帖的数量
	 */
	public function getThreadPostCount($courseId, $threadId);

	/**
	 * 回复话题
	 */
	public function postThread($post);

	public function deletePost($courseId, $id);

	public function deleteThreadsByIds(array $ids=null);

}