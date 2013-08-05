<?php

namespace Topxia\Service\Course\Dao\Impl;

use Topxia\Service\Common\BaseDao;
use Topxia\Service\Course\Dao\CourseMaterialDao;

class CourseMaterialDaoImpl extends BaseDao implements CourseMaterialDao
{
    protected $table = 'course_material';

    public function getMaterial($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = ? LIMIT 1";
        return $this->getConnection()->fetchAssoc($sql, array($id)) ? : null;
    }

    public function findMaterialsByCourseId($courseId, $start, $limit)
    {
        $sql ="SELECT * FROM {$this->table} WHERE courseId=? ORDER BY createdTime DESC LIMIT {$start}, {$limit}";
        return $this->getConnection()->fetchAll($sql, array($courseId)) ? : array();
    }

    public function findMaterialsByLessonId($lessonId, $start, $limit)
    {
        $sql ="SELECT * FROM {$this->table} WHERE lessonId=? ORDER BY createdTime DESC LIMIT {$start}, {$limit}";
        return $this->getConnection()->fetchAll($sql, array($lessonId)) ? : array();
    }

    public function getMaterialCountByCourseId($courseId)
    {
        $sql ="SELECT COUNT(*) FROM {$this->table} WHERE courseId = ?";
        return $this->getConnection()->fetchColumn($sql, array($courseId));
    }

    public function addMaterial($material)
    {
        $affected = $this->getConnection()->insert($this->table, $material);
        if ($affected <= 0) {
            throw $this->createDaoException('Insert learn error.');
        }
        return $this->getMaterial($this->getConnection()->lastInsertId());
    }

    public function deleteMaterial($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        return $this->getConnection()->executeUpdate($sql, array($id));
    }
}