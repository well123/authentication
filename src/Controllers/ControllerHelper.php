<?php

namespace Wubin\Authentication\Controllers;

/**
 * @Author     : fang
 * @Date       : 2020-05-06 17:31
 * @Description:
 */
trait ControllerHelper {

    //业务名称
    protected $nameCN = '';

    //不能为空
    public function getCantEmptyMsg($nameCN) {
        return '【' . $nameCN . '】不能为空';
    }

    //新增成功
    public function getAddOkMsg() {
        return '新增【' . $this->nameCN . '】成功';
    }

    //新增失败
    public function getAddErrorMsg() {
        return '新增【' . $this->nameCN . '】失败';
    }

    //不允许编辑
    public function getCantModifyMsg($value = '') {
        return $this->nameCN . '【' . $value . '】不允许编辑';
    }

    //编辑成功
    public function getModifyOkMsg() {
        return '编辑【' . $this->nameCN . '】成功';
    }

    //编辑失败
    public function getModifyErrorMsg() {
        return '编辑【' . $this->nameCN . '】失败';
    }

    //删除成功
    public function getRemoveOkMsg() {
        return '删除【' . $this->nameCN . '】成功';
    }

    //删除失败
    public function getRemoveErrorMsg() {
        return '删除【' . $this->nameCN . '】失败';
    }

    //不允许删除
    public function getCantDeleteMsg($value = '') {
        return $this->nameCN . '【' . $value . '】不允许删除';
    }

    public function getRepeatMsg($value = '') {
        return $value . '已被使用';
    }

    //父级不存在
    public function getPidNotExistMsg() {
        return '【' . $this->nameCN . '】父级不存在';
    }

    //有子级数据，不能删除父级
    public function getCantRemoveHasChildrenMsg() {
        return '【' . $this->nameCN . '】有子级数据，不能删除';
    }

    //操作成功
    public function getOperateOkMsg($operate) {
        return $operate . '成功';
    }

    //操作失败
    public function getOperateErrorMsg($operate) {
        return $operate . '失败';
    }

    /**
     * @Author     : fang
     * @Description:已被使用
     * @param $fieldCN
     * @param $value
     * @return string
     */
    public function getUsedMsg($fieldCN = null, $value) {
        if ($fieldCN === null) {
            return $this->nameCN . '已被使用';
        }
        return $this->nameCN . $fieldCN . '【' . $value . '】已被使用';
    }

    /**
     * @Author     : fang
     * @Description:已被使用-在其他表
     * @param $atNameCN :其他表的名称
     * @param $name :数据行标识，名称等
     * @return string
     */
    public function getUsedAtTableRowMsg($atNameCN, $name) {
        return $this->nameCN . '已在' . $atNameCN . '【' . $name . '】使用';
    }

    //已被使用
    public function getUsedByMsg($nameCN) {
        return $this->nameCN . '已有' . $nameCN . '使用';
    }

    //不存在
    public function getNotExistMsg() {
        return '【' . $this->nameCN . '】不存在或已被删除';
    }

    //不存在-其他表信息
    public function getRelatedNotExistMsg($nameCN) {
        return '【' . $nameCN . '】不存在';
    }

    //重复出现
    public function getRepeatCountMsg($nameCN, $count) {
        return '【' . $nameCN . '】重复出现【' . $count . '】次';
    }

    //追加不能删除
    public function getAppendCantRemoveMsg() {
        return '，不能删除';
    }
}
