<?php

namespace Topxia\Service\User;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface UserService
{
    
	public function getUser($id);

    public function getUserByNickname($nickname);

    public function getUserByEmail($email);

    public function getUnreadNotificationNum($userId);

	public function findUsersByIds(array $ids);

    public function findUserProfilesByIds(array $ids);

    public function searchUsers(array $conditions, $start, $limit);

    public function searchUserCount(array $conditions);

    public function setEmailVerified($userId);

    public function waveUnreadNotification($userId, $diff = 1);
    
    public function changeEmail($userId, $email);

    public function changeAvatar($userId, UploadedFile $file);

    public function isNicknameAvaliable($nickname);

    public function isEmailAvaliable($email);

    /**
     * 变更密码
     * 
     * @param  [integer]    $id       用户ID
     * @param  [string]     $password 新密码
     */
    public function changePassword($id, $password);

    /**
     * 校验密码是否正确
     * 
     * @param  [integer]    $id       用户ID
     * @param  [string]     $password 密码
     * 
     * @return [boolean] 密码正确，返回true；错误，返回false。
     */
    public function verifyPassword($id, $password);

    /**
     * 用户注册
     *
     * 当type为default时，表示用户从自身网站注册。
     * 当type为weibo、qq、renren时，表示用户从第三方网站连接，允许注册信息没有密码。
     * 
     * @param  [type] $registration 用户注册信息
     * @param  string $type         注册类型
     * @return array 用户信息
     */
    public function register($registration, $type = 'default');

    public function updateUserProfile($id, $fields);

    public function updateLoginInfo($id,$loginInfo);

    public function getUserProfile($id);

    public function changeUserRoles($id, array $roles);

    public function increaseCoin ($userId, $coin, $action = null, $note = null);

    public function decreaseCoin ($userId, $coin, $action = null, $note = null);

    public function makeToken($type, $userId = null, $expiredTime = null, $data = null);

    public function getToken($type, $token);

    public function deleteToken($type, $token);

    public function lockUser($id);
    
    public function unlockUser($id);

    /**
     * 
     * 绑定第三方登录的帐号到系统中的用户帐号
     * 
     */
    public function bindUser($type, $fromId, $toId, $token);

    public function getUserBindByTypeAndFromId($type, $fromId);

    public function getUserBindByTypeAndUserId($type, $toId);

    public function findBindsByUserId($userId);
    
    public function unBindUserByTypeAndToId($type, $toId);

    /**
     * 用户之间相互关注
     */
    
    public function follow($fromId, $toId);

    public function unFollow($fromId, $toId);

    public function isFollowed($fromId, $toId);

    /**
     * 过滤得到用户关注中的用户ID列表
     *
     * 此方法用于给出一批用户ID($followingIds)，找出哪些用户ID，是已经被用户($userId)关注了的。
     * 
     * @param  integer $userId       关注者的用户ID
     * @param  array   $followingIds 被关注者的用户ID列表
     * @return array 用户关注中的用户ID列表。
     */
    public function filterFollowingIds($userId, array $followingIds);

}