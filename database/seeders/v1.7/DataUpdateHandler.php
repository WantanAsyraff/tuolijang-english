<?php

declare(strict_types=1);

use App\Constants\System\ConfigEnum;
use App\Http\Dao\Company\CompanyUserDao;
use App\Http\Model\Admin\Admin;
use App\Http\Model\Admin\AdminInfo;
use App\Http\Model\Approve\Approve;
use App\Http\Model\Approve\ApproveApply;
use App\Http\Model\Approve\ApproveContent;
use App\Http\Model\Approve\ApproveForm;
use App\Http\Model\Approve\ApproveProcess;
use App\Http\Model\Approve\ApproveReply;
use App\Http\Model\Approve\ApproveRule;
use App\Http\Model\Approve\ApproveUser;
use App\Http\Model\Cloud\CloudAuth;
use App\Http\Model\Cloud\CloudFile;
use App\Http\Model\Cloud\CloudShare;
use App\Http\Model\Cloud\CloudViewHistory;
use App\Http\Model\Company\UserChange;
use App\Http\Model\Company\UserEducation;
use App\Http\Model\Company\UserWork;
use App\Http\Model\Finance\Bill;
use App\Http\Model\News\News;
use App\Http\Model\Position\Job;
use App\Http\Model\Schedule\ScheduleType;
use App\Http\Model\Storage\StorageRecord;
use App\Http\Service\Company\CompanyUserCardService;
use App\Http\Service\Company\CompanyUserService;
use App\Http\Service\Config\SystemConfigService;

class DataUpdateHandler
{
    protected $cardModels = [
    ];

    public function __construct()
    {
        $this->exportAdmin();
        $this->reloadAdminInfo();
        $this->reloadCard2Uid();
        $this->reloadSystemConfig();
    }

    /**
     * 导入新员工数据.
     */
    private function exportAdmin(): void
    {
        $users = app()->get(CompanyUserDao::class)->select(with: ['user']) ?? [];
        foreach ($users as $user) {
            Admin::firstOrCreate(['id' => $user->id], [
                'id'          => $user->id,
                'uid'         => $user->uid,
                'account'     => $user->account ?? '',
                'password'    => $user->user?->password ?? '',
                'avatar'      => $user->avatar,
                'name'        => $user->name,
                'phone'       => $user->phone,
                'job'         => $user->job,
                'is_admin'    => (int) $user->ident,
                'roles'       => $user->roles,
                'last_ip'     => $user->user?->last_ip ?? '',
                'login_count' => $user->user?->login_count ?? 0,
                'status'      => $user->uid ? $user->status : 0,
                'is_init'     => $user->user?->is_init ?? 1,
                'mark'        => $user->user?->remark ?? '',
                'created_at'  => $user->created_at,
            ]);
        }
    }

    private function reloadAdminInfo(): void
    {
        $cards = app()->get(CompanyUserCardService::class)->select(['entid' => 1])?->toArray() ?? [];
        foreach ($cards as $card) {
            $uid = $card['uid'];
            if (! $uid) {
                $uid = Admin::where('phone', $card['phone'])->value('uid');
            }
            unset($card['entid'], $card['avatar'], $card['name'], $card['phone'], $card['position'], $card['uid'], $card['id'], $card['updated_at'], $card['status']);
            AdminInfo::where('uid', $uid)->update($card);
            if ($card['type'] == 4) {
                Admin::where('uid', $uid)->update(['status' => 2]);
            }
        }
    }

    private function reloadCard2Uid(): void
    {
        $cards = app()->get(CompanyUserService::class)->column(['entid' => 1], ['id', 'card_id', 'uid']);
        if ($cards) {
            $Approve                      = new Approve();
            $ApproveApply                 = new ApproveApply();
            $ApproveProcess               = new ApproveProcess();
            $ApproveForm                  = new ApproveForm();
            $ApproveContent               = new ApproveContent();
            $ApproveUser                  = new ApproveUser();
            $ApproveReply                 = new ApproveReply();
            $ApproveRule                  = new ApproveRule();
            $StorageRecord                = new StorageRecord();
            $news                         = new News();
            $userWork                     = new UserWork();
            $userEducation                = new UserEducation();
            $job                          = new Job();
            $folder                       = new CloudFile();
            $folderAuth                   = new CloudAuth();
            $folderHistory                = new CloudViewHistory();
            $folderShare                  = new CloudShare();
            $folderViewHitory             = new CloudViewHistory();
            $scheduleType                 = new ScheduleType();
            $userChange                   = new UserChange();
            $bill                         = new Bill();
            $Approve->timestamps          = false;
            $ApproveApply->timestamps     = false;
            $ApproveProcess->timestamps   = false;
            $ApproveForm->timestamps      = false;
            $ApproveRule->timestamps      = false;
            $ApproveContent->timestamps   = false;
            $ApproveUser->timestamps      = false;
            $ApproveReply->timestamps     = false;
            $StorageRecord->timestamps    = false;
            $news->timestamps             = false;
            $userWork->timestamps         = false;
            $userEducation->timestamps    = false;
            $job->timestamps              = false;
            $userChange->timestamps       = false;
            $folder->timestamps           = false;
            $folderAuth->timestamps       = false;
            $folderHistory->timestamps    = false;
            $folderShare->timestamps      = false;
            $folderViewHitory->timestamps = false;
            $scheduleType->timestamps     = false;
            $bill->timestamps             = false;
            foreach ($cards as $card) {
                $Approve->where('card_id', $card['card_id'])->update(['user_id' => $card['id']]);
                $ApproveApply->where('card_id', $card['card_id'])->update(['user_id' => $card['id']]);
                $ApproveProcess->where('card_id', $card['card_id'])->update(['user_id' => $card['id']]);
                $ApproveForm->where('card_id', $card['card_id'])->update(['user_id' => $card['id']]);
                $ApproveContent->where('card_id', $card['card_id'])->update(['user_id' => $card['id']]);
                $ApproveUser->where('card_id', $card['card_id'])->update(['user_id' => $card['id']]);
                $ApproveReply->where('card_id', $card['card_id'])->update(['user_id' => $card['id']]);
                $ApproveRule->where('card_id', $card['card_id'])->update(['user_id' => $card['id']]);
                $StorageRecord->where('card_id', $card['card_id'])->update(['user_id' => $card['id']]);
                $StorageRecord->where('creater', $card['card_id'])->update(['operator' => $card['id']]);
                $news->where('card_id', $card['card_id'])->update(['user_id' => $card['id']]);
                $userWork->where('card_id', $card['card_id'])->update(['user_id' => $card['id']]);
                $userEducation->where('card_id', $card['card_id'])->update(['user_id' => $card['id']]);
                $job->where('card_id', $card['card_id'])->update(['user_id' => $card['id']]);
                $userChange->where('card_id', $card['card_id'])->update(['uid' => $card['id']]);
                $folder->where('uid', $card['uid'])->update(['user_id' => $card['id']]);
                $folderAuth->where('uid', $card['uid'])->update(['user_id' => $card['id']]);
                $folderHistory->where('uid', $card['uid'])->update(['user_id' => $card['id']]);
                $folderShare->where('to_uid', $card['uid'])->update(['user_id' => $card['id']]);
                $folderViewHitory->where('uid', $card['uid'])->update(['user_id' => $card['id']]);
                $scheduleType->where('uid', $card['uid'])->update(['user_id' => $card['id']]);
                $bill->where('uid', $card['uid'])->update(['user_id' => $card['id']]);
            }
        }
    }

    private function reloadSystemConfig()
    {
        $service = app()->get(SystemConfigService::class);
        $list    = $service->select([], ['id', 'key', 'parameter'])?->toArray();
        foreach ($list as $item) {
            $key = match ($item['key']) {
                'ent_logint_error_count'  => 'LOGIN_ERROR_COUNT',
                'ent_logint_lock'         => 'LOGIN_LOCK',
                'jd_storageRegion'        => 'JD_STORAGE_REGION',
                'client_policy_switch'    => 'CLIENT_POLICY_SWITCH',
                'unsettled_client_number' => 'UNSETTLED_CLIENT_NUMBER',
                default                   => strtoupper(str_replace('ent_', '', $item['key'])),
            };
            if (ConfigEnum::isValidKey($key)) {
                $config = ConfigEnum::values()[$key]->getValue();
                $service->update($item['id'], [
                    'key'       => $config['key'],
                    'category'  => $config['category'],
                    'parameter' => $config['parameter'],
                ]);
            }
        }
    }
}
