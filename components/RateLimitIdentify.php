<?php

namespace app\components;

use Yii;
use Exception;
use yii\base\Component;
use yii\filters\RateLimitInterface;

/**
 * 用户访问速率控制 组件
 */
class RateLimitIdentify extends Component implements RateLimitInterface
{
    // 限制单位（秒）
    const LIMT_UNIT = 2;
    // 最大速率值
    const RATE_LIMIT = 1;

    //速率记数信息
    private $_aRateLimit;

    /**
     * 请求初始化：获取速率信息
     */
    public function getIdentity() {
        $sIp = Yii::$app->request->getUserIP();
        $this->_aRateLimit = ['allowance' => self::RATE_LIMIT, 'allowance_updated_at' => time(), 'ip' => $sIp];
        // TODO
        /**
         * 取存储信息
         * Redis操作：
         * $this->_aRateLimit = Yii::$app->redis->get(md5($sIp));
         * if(!empty($this->_aRateLimit)) {
         *      $this->_aRateLimit = json_decode($this->_aRateLimit, true);
         * } else {
         *      $this->_aRateLimit['ip'] = $sIp;
         * }
         */

        return $this;
    }

    /**
     * 取限制单位和最大速率值
     * @param $oRequest
     * @param $oAction
     * @return array
     */
    public function getRateLimit($oRequest, $oAction) {
        /**
         * 可设置为动态数据
         * Redis操作: 
         * $limit_unit = Yii::$app->redis->get('limit_unit');
         */
        return [self::RATE_LIMIT, self::LIMT_UNIT];
    }

    /**
     * 取允许请求数和更新时间
     * @param $oRequest
     * @param $oAction
     * @return array
     */
    public function loadAllowance($oRequest, $oAction) {
        $iAllowance = $this->_aRateLimit['allowance'] ?? self::RATE_LIMIT;
        $iTimestamp = $this->_aRateLimit['allowance_updated_at'] ?? time();

        return [$iAllowance, $iTimestamp];
    }

    /**
     * 设置允许请求数和更新时间
     * @param $oRequest
     * @param $oAction
     * @param int $iAllowance 速率值
     * @param int $iTimestamp 时间戳
     * @throws Exception
     */
    public function saveAllowance($oRequest, $oAction, $iAllowance, $iTimestamp) {
        $this->_aRateLimit['allowance'] = $iAllowance;
        $this->_aRateLimit['allowance_updated_at'] = $iTimestamp;
        // TODO
        try {
            /**
             * 保存速率信息
             * Redis操作：
             * $sIp = Yii::$app->request->getUserIP();
             * Yii::$app->redis->setex(md5($sIp), 3600*24*3, json_encode($this->_aRateLimit))
             */
        } catch(Exception $e) {
            /**
             * 异常处理方式
             */
            throw new Exception($e->getMessage());
        }
    }
}