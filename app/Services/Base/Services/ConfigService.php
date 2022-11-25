<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) XiaoTeng <616896861@qq.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Services\Base\Services;

use App\Constant\FrontendConstant;
use App\Services\Base\Model\AppConfig;
use App\Services\Base\Interfaces\ConfigServiceInterface;

class ConfigService implements ConfigServiceInterface
{

    /**
     * @return int
     */
    public function getWatchedVideoSceneCredit1(): int
    {
        return (int)config('meedu.member.credit1.watched_video');
    }

    /**
     * @return int
     */
    public function getWatchedCourseSceneCredit1(): int
    {
        return (int)config('meedu.member.credit1.watched_course');
    }

    /**
     * @return int
     */
    public function getPaidOrderSceneCredit1(): int
    {
        return (int)config('meedu.member.credit1.paid_order');
    }

    /**
     * @return int
     */
    public function getRegisterSceneCredit1(): int
    {
        return (int)config('meedu.member.credit1.register');
    }

    /**
     * @return int
     */
    public function getInviteSceneCredit1(): int
    {
        return (int)config('meedu.member.credit1.invite');
    }

    /**
     * 獲取服務配置
     *
     * @param string $app
     * @return array
     */
    public function getServiceConfig(string $app): array
    {
        return config('services.' . $app, []);
    }

    /**
     * 網站名
     *
     * @return string
     */
    public function getName(): string
    {
        return config('app.name');
    }

    /**
     * ICP
     *
     * @return string
     */
    public function getIcp(): string
    {
        return config('meedu.system.icp', '');
    }

    /**
     * 播放器封面
     *
     * @return string
     */
    public function getPlayerCover(): string
    {
        return config('meedu.system.player_thumb', '');
    }

    /**
     * 播放器配置
     *
     * @return array
     */
    public function getPlayer(): array
    {
        return config('meedu.system.player');
    }

    /**
     * 獲取logo
     *
     * @return array
     */
    public function getLogo(): array
    {
        return [
            'logo' => config('meedu.system.logo'),
            'white_logo' => config('meedu.system.white_logo'),
        ];
    }

    /**
     * 獲取用户協議
     * @return string
     */
    public function getMemberProtocol(): string
    {
        return config('meedu.member.protocol', '');
    }

    /**
     * 獲取用户隱私協議
     * @return string
     */
    public function getMemberPrivateProtocol(): string
    {
        return config('meedu.member.private_protocol', '');
    }

    /**
     * 關於我們
     * @return string
     */
    public function getAboutus(): string
    {
        return config('meedu.aboutus', '');
    }

    /**
     * 用户默認頭像
     * @return string
     */
    public function getMemberDefaultAvatar(): string
    {
        return config('meedu.member.default_avatar');
    }

    /**
     * 用户默認鎖定狀態
     * @return int
     */
    public function getMemberLockStatus(): int
    {
        return (int)config('meedu.member.is_lock_default');
    }

    /**
     * 用户默認激活狀態
     * @return int
     */
    public function getMemberActiveStatus(): int
    {
        return (int)config('meedu.member.is_active_default');
    }

    /**
     * 課程列表默認顯示條數
     * @return int
     */
    public function getCourseListPageSize(): int
    {
        return (int)config('meedu.other.course_list_page_size', 6);
    }

    /**
     * 課程列表頁面SEO
     * @return array
     */
    public function getSeoCourseListPage(): array
    {
        return config('meedu.seo.course_list');
    }

    /**
     * 視頻列表頁面顯示條數
     * @return int
     */
    public function getVideoListPageSize(): int
    {
        return (int)config('meedu.other.video_list_page_size', 10);
    }

    /**
     * 獲取默認的編輯器
     * @return string
     */
    public function getEditor(): string
    {
        return config('meedu.system.editor', 'html');
    }

    /**
     * 短信配置
     * @return array
     */
    public function getSms(): array
    {
        return config('sms');
    }

    /**
     * 會員界面SEO
     * @return array
     */
    public function getSeoRoleListPage(): array
    {
        return config('meedu.seo.role_list');
    }

    public function getSeoIndexPage(): array
    {
        return config('meedu.seo.index');
    }

    /**
     * 支付網關
     *
     * @return array
     */
    public function getPayments(): array
    {
        return config('meedu.payment');
    }

    /**
     * 微信支付配置
     *
     * @return array
     */
    public function getWechatPay(): array
    {
        return config('pay.wechat');
    }

    /**
     * 支付寶支付配置
     *
     * @return array
     */
    public function getAlipayPay(): array
    {
        return config('pay.alipay');
    }

    /**
     * 緩存狀態
     *
     * @return boolean
     */
    public function getCacheStatus(): bool
    {
        return (int)config('meedu.system.cache.status') === FrontendConstant::YES;
    }

    /**
     * 緩存時間
     *
     * @return integer
     */
    public function getCacheExpire(): int
    {
        return (int)config('meedu.system.cache.expire');
    }

    /**
     * 圖片存儲驅動
     *
     * @return string
     */
    public function getImageStorageDisk(): string
    {
        return config('meedu.upload.image.disk');
    }

    /**
     * 圖片存儲路徑
     *
     * @return string
     */
    public function getImageStoragePath(): string
    {
        return config('meedu.upload.image.path');
    }

    /**
     * 註冊短信模板ID
     *
     * @return string
     */
    public function getRegisterSmsTemplateId(): string
    {
        return $this->getTemplateId('register');
    }

    /**
     * 登錄短信模板ID
     *
     * @return string
     */
    public function getLoginSmsTemplateId(): string
    {
        return $this->getTemplateId('login');
    }

    /**
     * 密碼重置模板ID
     *
     * @return string
     */
    public function getPasswordResetSmsTemplateId(): string
    {
        return $this->getTemplateId('password_reset');
    }

    /**
     * 手機號綁定模板ID
     *
     * @return string
     */
    public function getMobileBindSmsTemplateId(): string
    {
        return $this->getTemplateId('mobile_bind');
    }

    /**
     * 獲取某個場景的短信模板id
     *
     * @param [type] $scene
     * @return string
     */
    protected function getTemplateId($scene): string
    {
        $supplier = config('meedu.system.sms');
        $gateways = config('sms.gateways');
        $supplierConfig = $gateways[$supplier] ?? [];
        return $supplierConfig['template'][$scene] ?? '';
    }

    /**
     * 手動支付詳情
     *
     * @return string
     */
    public function getHandPayIntroducation(): string
    {
        return config('meedu.payment.handPay.introduction') ?? '';
    }

    /**
     * 已開啟的社交登錄app
     *
     * @return array
     */
    public function getEnabledSocialiteApps(): array
    {
        $apps = config('meedu.member.socialite');
        $list = [];
        foreach ($apps as $app) {
            if ((int)($app['enabled'] ?? 0) !== 1) {
                continue;
            }
            $list[] = $app;
        }
        return $list;
    }

    /**
     * meedu系統配置
     *
     * @return array
     */
    public function getMeEduConfig(): array
    {
        return config('meedu');
    }

    /**
     * 獲取手機號强制綁定狀態開關
     *
     * @return integer
     */
    public function getEnabledMobileBindAlert(): int
    {
        return (int)config('meedu.member.enabled_mobile_bind_alert', 0);
    }

    /**
     * 會員邀請配置
     *
     * @return array
     */
    public function getMemberInviteConfig(): array
    {
        return config('meedu.member.invite');
    }

    /**
     * 騰訊雲VOD配置
     *
     * @return array
     */
    public function getTencentVodConfig(): array
    {
        return config('tencent.vod');
    }

    /**
     * 騰訊小程序配置
     *
     * @return array
     */
    public function getTencentWechatMiniConfig(): array
    {
        return config('tencent.wechat.mini');
    }

    /**
     * 阿里雲私密播放狀態
     *
     * @return bool
     */
    public function getAliyunPrivatePlayStatus(): bool
    {
        return (int)config('meedu.system.player.enabled_aliyun_private') === 1;
    }

    /**
     * 獲取所有配置
     * @return array
     */
    public function all(): array
    {
        return AppConfig::query()->orderBy('sort')->get()->toArray();
    }

    /**
     * 檢測配置是否存在
     * @param string $key
     * @return bool
     */
    public function isConfigExists(string $key): bool
    {
        return AppConfig::query()->where('key', $key)->exists();
    }

    /**
     * 寫入配置
     * @param array $config
     */
    public function setConfig(array $config): void
    {
        $data = array_column($this->all(), 'key');
        foreach ($config as $key => $value) {
            if (!in_array($key, $data)) {
                continue;
            }
            AppConfig::query()->where('key', $key)->update(['value' => $value]);
        }
    }

    /**
     * 獲取阿里雲VOD配置
     * @return array
     */
    public function getAliyunVodConfig(): array
    {
        return config('meedu.upload.video.aliyun');
    }

    /**
     * 登錄限制規則
     *
     * @return int
     */
    public function getLoginLimitRule(): int
    {
        return (int)config('meedu.system.login.limit.rule');
    }

    /**
     * 微信公眾號配置
     * @return array
     */
    public function getMpWechatConfig(): array
    {
        $config = config('meedu.mp_wechat');
        return $config ? $config : [];
    }

    /**
     * 獲取註冊送VIP的配置
     * @return array
     */
    public function getMemberRegisterSendVipConfig(): array
    {
        return config('meedu.member.register.vip') ?? [];
    }
}
