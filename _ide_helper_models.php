<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Http\Model\Admin{
/**
 * 用户表.
 *
 * @property int $id
 * @property string $uid
 * @property string $account 用户账号
 * @property string $password 用户密码
 * @property string $avatar 用户头像
 * @property string $name 用户姓名
 * @property string $phone 手机号
 * @property Job|null $job 职位ID
 * @property int $is_admin 是否为超级管理员
 * @property string $roles 角色权限
 * @property int $uni_online 移动端登录状态
 * @property string $client_id 连接通道ID
 * @property string|null $scan_key 扫码登录参数
 * @property string|null $last_ip 访问ip
 * @property int $login_count 登陆次数
 * @property int $status 状态：0、锁定；1、正常；
 * @property int $is_init 是否为初始密码
 * @property string $language 语言
 * @property string $mark
 * @property int $work_member_id 企业微信成功id
 * @property string $mcp_key MCP工具调用唯一值
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int $e_sign 是否认证电子签
 * @property string $e_userid 电子签用户ID
 * @property string $e_openid 电子签用户标识
 * @property-read \App\Http\Model\Admin\AdminInfo|null $card
 * @property-read \Illuminate\Database\Eloquent\Collection<int, UserEducation> $educations
 * @property-read int|null $educations_count
 * @property-read Frame|null $frame
 * @property-read \Illuminate\Database\Eloquent\Collection<int, FrameAssist> $frameIds
 * @property-read int|null $frame_ids_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Frame> $frames
 * @property-read int|null $frames_count
 * @property-read \App\Http\Model\Admin\AdminInfo|null $info
 * @property-read FrameAssist|null $isAdmin
 * @property-read UserJobAnalysis|null $jobAnalysis
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Frame> $manage_frames
 * @property-read int|null $manage_frames_count
 * @property-read WorkMember|null $member
 * @property-read \Illuminate\Database\Eloquent\Collection<int, UserPosition> $positions
 * @property-read int|null $positions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Frame> $scope
 * @property-read int|null $scope_count
 * @property-read Admin|null $super
 * @property-read \App\Http\Model\Admin\AdminInfo|null $user_card
 * @property-read WorkMember|null $work
 * @property-read \Illuminate\Database\Eloquent\Collection<int, UserWork> $works
 * @property-read int|null $works_count
 * @method static Builder|Admin id($value)
 * @method static Builder|Admin isWork($value)
 * @method static Builder|Admin name($value)
 * @method static Builder|Admin nameEq($value)
 * @method static Builder|Admin nameLike($value)
 * @method static Builder|Admin newModelQuery()
 * @method static Builder|Admin newQuery()
 * @method static Builder|Admin notId($value)
 * @method static Builder|Admin notUid($value)
 * @method static Builder|Admin onlyTrashed()
 * @method static Builder|Admin phone($value)
 * @method static Builder|Admin phoneLike($value)
 * @method static Builder|Admin query()
 * @method static Builder|Admin sex($value)
 * @method static Builder|Admin status($value)
 * @method static Builder|Admin time($value)
 * @method static Builder|Admin uid($value)
 * @method static Builder|Admin whereAccount($value)
 * @method static Builder|Admin whereAvatar($value)
 * @method static Builder|Admin whereClientId($value)
 * @method static Builder|Admin whereCreatedAt($value)
 * @method static Builder|Admin whereDeletedAt($value)
 * @method static Builder|Admin whereEOpenid($value)
 * @method static Builder|Admin whereESign($value)
 * @method static Builder|Admin whereEUserid($value)
 * @method static Builder|Admin whereId($value)
 * @method static Builder|Admin whereIsAdmin($value)
 * @method static Builder|Admin whereIsInit($value)
 * @method static Builder|Admin whereJob($value)
 * @method static Builder|Admin whereLanguage($value)
 * @method static Builder|Admin whereLastIp($value)
 * @method static Builder|Admin whereLoginCount($value)
 * @method static Builder|Admin whereMark($value)
 * @method static Builder|Admin whereMcpKey($value)
 * @method static Builder|Admin whereName($value)
 * @method static Builder|Admin wherePassword($value)
 * @method static Builder|Admin wherePhone($value)
 * @method static Builder|Admin whereRoles($value)
 * @method static Builder|Admin whereScanKey($value)
 * @method static Builder|Admin whereStatus($value)
 * @method static Builder|Admin whereUid($value)
 * @method static Builder|Admin whereUniOnline($value)
 * @method static Builder|Admin whereUpdatedAt($value)
 * @method static Builder|Admin whereWorkMemberId($value)
 * @method static Builder|Admin withTrashed()
 * @method static Builder|Admin withoutTrashed()
 * @mixin \Eloquent
 */
	class Admin extends \Eloquent implements \Tymon\JWTAuth\Contracts\JWTSubject, \crmeb\interfaces\TimeDataInterface {}
}

namespace App\Http\Model\Admin{
/**
 * App\Http\Model\Admin\AdminInfo
 *
 * @property int $id
 * @property string $uid
 * @property string|null $letter 姓氏首字母
 * @property string $city
 * @property string $area
 * @property string $card_id 身份证号
 * @property string $province
 * @property string $birthday 员工生日
 * @property string $nation 员工种族
 * @property string $politic 政治面貌
 * @property string $education 学历
 * @property string $education_image 学历证书
 * @property string $acad 学位
 * @property string $acad_image 学位证书
 * @property string $native 籍贯
 * @property string $address 居住地
 * @property int $sex 性别: 0、未知；1、男；2、女；3、其他；
 * @property int|null $age 员工年龄
 * @property int $marriage 婚姻状况:0、未婚；1、已婚；
 * @property int $type 员工状态:0、未入职；1、正式;2、使用;3、实习;4、离职；
 * @property int $work_years 工作经验（年）
 * @property string $spare_name 紧急联系人
 * @property string $spare_tel 紧急联系电话
 * @property string $email 邮箱
 * @property string $social_num 社保账户
 * @property string $fund_num 公积金账户
 * @property string $bank_num 银行卡账户
 * @property string $bank_name 开户行
 * @property string $graduate_name 毕业院校
 * @property string $graduate_date 毕业时间
 * @property string $interview_date 面试时间
 * @property string $interview_position 面试职位
 * @property int $is_part 是否兼职
 * @property string $photo 员工照片
 * @property string $card_front 身份证正面
 * @property string $card_both 身份证背面
 * @property string|null $work_time 入职时间
 * @property string|null $trial_time 试用时间
 * @property string|null $formal_time 转正时间
 * @property string|null $treaty_time 合同到期时间
 * @property string|null $quit_time 离职时间
 * @property int $sort 排序
 * @property \Illuminate\Support\Carbon|null $deleted_at 删除时间
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|AdminInfo id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminInfo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminInfo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminInfo time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminInfo type($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminInfo whereAcad($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminInfo whereAcadImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminInfo whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminInfo whereAge($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminInfo whereArea($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminInfo whereBankName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminInfo whereBankNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminInfo whereBirthday($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminInfo whereCardBoth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminInfo whereCardFront($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminInfo whereCardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminInfo whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminInfo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminInfo whereEducation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminInfo whereEducationImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminInfo whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminInfo whereFormalTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminInfo whereFundNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminInfo whereGraduateDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminInfo whereGraduateName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminInfo whereInterviewDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminInfo whereInterviewPosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminInfo whereIsPart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminInfo whereLetter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminInfo whereMarriage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminInfo whereNation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminInfo whereNative($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminInfo wherePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminInfo wherePolitic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminInfo whereProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminInfo whereQuitTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminInfo whereSex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminInfo whereSocialNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminInfo whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminInfo whereSpareName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminInfo whereSpareTel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminInfo whereTreatyTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminInfo whereTrialTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminInfo whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminInfo whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminInfo whereWorkTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminInfo whereWorkYears($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminInfo withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminInfo withoutTrashed()
 * @mixin \Eloquent
 */
	class AdminInfo extends \Eloquent {}
}

namespace App\Http\Model\Approve{
/**
 * 审核流程表
 * Class Approve.
 *
 * @property int $id 自增id
 * @property int $user_id 用户id
 * @property int $card_id
 * @property int $entid
 * @property string $name 审批名称
 * @property string $icon 审批图标
 * @property string $color 审批图标颜色
 * @property string $info 审批说明
 * @property int $types 审批类型：见枚举；
 * @property int $examine 是否需要审核
 * @property array|mixed $config 表单配置详情
 * @property int $status 状态：0、关闭；1、开启；
 * @property int $sort 排序
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read Admin|null $card
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\Approve\ApproveForm> $form
 * @property-read int|null $form_count
 * @property-read \App\Http\Model\Approve\ApproveProcess|null $process
 * @property-read \App\Http\Model\Approve\ApproveRule|null $rule
 * @method static \Illuminate\Database\Eloquent\Builder|Approve entid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Approve id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Approve nameLike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Approve newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Approve newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Approve notTypes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Approve notid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Approve onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Approve query()
 * @method static \Illuminate\Database\Eloquent\Builder|Approve status($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Approve types($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Approve whereCardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Approve whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Approve whereConfig($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Approve whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Approve whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Approve whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Approve whereExamine($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Approve whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Approve whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Approve whereInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Approve whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Approve whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Approve whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Approve whereTypes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Approve whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Approve whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Approve withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Approve withoutTrashed()
 * @mixin \Eloquent
 */
	class Approve extends \Eloquent {}
}

namespace App\Http\Model\Approve{
/**
 * 申请记录表
 * Class ApproveApply.
 *
 * @property int $id 自增id
 * @property int $user_id 用户id
 * @property int $entid 企业ID
 * @property int $card_id 创建用户名片ID
 * @property int $approve_id
 * @property string $node_id 当前节点ID
 * @property int $examine 是否需要审批：0、无须审批；1、需要审批；
 * @property int $status 申请状态：-1、撤回；0、待审批；1、已通过；2、已拒绝；
 * @property string $info 说明
 * @property string $number 编号
 * @property int $crud_id 关联实体ID
 * @property int $link_id 实体数据ID
 * @property int $apply_id 关联审批ID
 * @property int $is_recall 是否为撤销审批
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Http\Model\Approve\Approve|null $approve
 * @property-read Admin|null $card
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\Approve\ApproveContent> $content
 * @property-read int|null $content_count
 * @property-read SystemCrudApprove|null $crudApprove
 * @property-read SystemCrudApproveRule|null $crud_rules
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\Approve\ApproveForm> $form
 * @property-read int|null $form_count
 * @property-read Frame|null $frame
 * @property-read \App\Http\Model\Approve\Approve|\App\Http\Model\Crud\SystemCrudApprove|null $approve_config
 * @property-read ApproveApply|null $recall
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\Approve\ApproveReply> $reply
 * @property-read int|null $reply_count
 * @property-read \App\Http\Model\Approve\ApproveRule|null $rules
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\Approve\ApproveUser> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveApply approveId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveApply cardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveApply entid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveApply id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveApply nameLike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveApply newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveApply newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveApply nodeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveApply notCardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveApply notStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveApply notid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveApply onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveApply query()
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveApply status($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveApply time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveApply userId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveApply whereApplyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveApply whereApproveId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveApply whereCardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveApply whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveApply whereCrudId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveApply whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveApply whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveApply whereExamine($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveApply whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveApply whereInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveApply whereIsRecall($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveApply whereLinkId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveApply whereNodeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveApply whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveApply whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveApply whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveApply whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveApply withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveApply withoutTrashed()
 * @mixin \Eloquent
 */
	class ApproveApply extends \Eloquent {}
}

namespace App\Http\Model\Approve{
/**
 * 申请内容表
 * Class ApproveContent.
 *
 * @property int $id 自增id
 * @property int $user_id 用户id
 * @property int $card_id 创建用户名片ID
 * @property int $approve_id
 * @property int $apply_id
 * @property string $title 表单名称
 * @property array|mixed $info 表单提示
 * @property false|string $value 表单默认值
 * @property int $required 是否必选
 * @property string $types 表单类型
 * @property string $symbol 字段标识
 * @property array|mixed $content 表单详情
 * @property array|mixed $props 限制条件
 * @property array|mixed $options 表单配置信息
 * @property array|mixed $config 表单配置信息
 * @property string $uniqued 表单唯一值
 * @property int $sort 排序
 * @property-read \App\Http\Model\Approve\Approve|null $approve
 * @property-read Admin|null $card
 * @property array|mixed $condition_list
 * @property array|mixed $dep_head
 * @property array|mixed $user_list
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveContent applyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveContent cateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveContent entid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveContent id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveContent nameLike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveContent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveContent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveContent notUniqued($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveContent notid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveContent query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveContent valueLike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveContent whereApplyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveContent whereApproveId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveContent whereCardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveContent whereConfig($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveContent whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveContent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveContent whereInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveContent whereOptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveContent whereProps($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveContent whereRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveContent whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveContent whereSymbol($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveContent whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveContent whereTypes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveContent whereUniqued($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveContent whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveContent whereValue($value)
 * @mixin \Eloquent
 */
	class ApproveContent extends \Eloquent {}
}

namespace App\Http\Model\Approve{
/**
 * 审核流程表单
 * Class ApproveForm.
 *
 * @property int $id 自增id
 * @property int $user_id 用户id
 * @property int $card_id 创建用户名片ID
 * @property int $approve_id
 * @property string $title 表单名称
 * @property string $info 表单提示
 * @property string $value 表单默认值
 * @property int $required 是否必选
 * @property string $types 表单类型
 * @property string $symbol 字段标识
 * @property mixed $content 表单详情
 * @property mixed $props 限制条件
 * @property mixed $options 表单配置信息
 * @property mixed $config 表单配置信息
 * @property mixed $uniqued 表单唯一值
 * @property int $sort 排序
 * @property-read \App\Http\Model\Approve\Approve|null $approve
 * @property-read Admin|null $card
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveForm approveId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveForm entid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveForm id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveForm nameLike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveForm newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveForm newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveForm notUniqued($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveForm notid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveForm query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveForm uniqued($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveForm whereApproveId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveForm whereCardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveForm whereConfig($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveForm whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveForm whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveForm whereInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveForm whereOptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveForm whereProps($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveForm whereRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveForm whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveForm whereSymbol($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveForm whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveForm whereTypes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveForm whereUniqued($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveForm whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveForm whereValue($value)
 * @mixin \Eloquent
 */
	class ApproveForm extends \Eloquent {}
}

namespace App\Http\Model\Approve{
/**
 * 假期类型表
 * Class ApproveHolidayType.
 *
 * @property int $id 自增ID
 * @property string $name 假期类型
 * @property int $new_employee_limit 新员工请假限制：0、不限制；1、限制；
 * @property int $new_employee_limit_month 新员工请假月时限制
 * @property int $duration_type 请假时长类型：0、天；1、小时；
 * @property int $duration_calc_type 时长计算类型：0、自然日；1、工作日；
 * @property int $sort 排序
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveHolidayType filterNormal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveHolidayType id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveHolidayType nameLike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveHolidayType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveHolidayType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveHolidayType notId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveHolidayType onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveHolidayType query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveHolidayType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveHolidayType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveHolidayType whereDurationCalcType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveHolidayType whereDurationType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveHolidayType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveHolidayType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveHolidayType whereNewEmployeeLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveHolidayType whereNewEmployeeLimitMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveHolidayType whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveHolidayType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveHolidayType withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveHolidayType withoutTrashed()
 * @mixin \Eloquent
 */
	class ApproveHolidayType extends \Eloquent {}
}

namespace App\Http\Model\Approve{
/**
 * 审核流程树
 * Class ApproveProcess.
 *
 * @property int $id 自增id
 * @property int $user_id 用户id
 * @property int $entid 创建企业ID
 * @property int $card_id 创建用户名片ID
 * @property int $approve_id
 * @property int $level 流程级别
 * @property int $groups 分组ID
 * @property string $name 节点名称
 * @property int $types 节点类型：0、申请人；1、审批人；2、抄送人；3、条件；4、路由；
 * @property string $uniqued 节点唯一值
 * @property int $settype 审核人类型：1、指定成员；2、指定部门主管；7、连续多部门；5、申请人自己；4、申请人自选；(0、无此条件)
 * @property int $director_order 指定层级顺序：0、从上至下；1、从下至上；(-1、无此条件)
 * @property int $director_level 指定主管层级/指定终点层级：1-10；(0、无此条件)
 * @property int $no_hander 当前部门无负责人时：1、上级部门负责人审批；2、为空时跳过；(0、无此条件)
 * @property array|mixed $dep_head 指定部门负责人
 * @property int $self_select 是否允许自选抄送人
 * @property int $select_range 可选范围：1、不限范围；2、指定成员；(0、无此条件)
 * @property array|mixed $user_list 指定的成员列表
 * @property int $select_mode 选人方式：1、单选；2、多选；(0、无此条件)
 * @property int $examine_mode 多人审批方式：1、或签；2、会签；3、依次审批；(0、无此条件)
 * @property int $priority 条件优先级
 * @property string $parent 节点父级唯一值
 * @property int $is_child 是否存在子节点
 * @property int $is_condition 是否存在条件
 * @property array|mixed $condition_list 条件详情
 * @property int $is_initial 是否为初始数据
 * @property array|mixed $info 数据详情
 * @property int $pass_ratio 通过比例(%) 0=关闭（使用原逻辑）
 * @property-read \App\Http\Model\Approve\Approve|null $approve
 * @property-read Admin|null $card
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveProcess approveId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveProcess entid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveProcess groups($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveProcess id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveProcess isInitial($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveProcess levelLt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveProcess nameLike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveProcess newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveProcess newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveProcess noTypes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveProcess notUniqued($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveProcess notid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveProcess parent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveProcess query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveProcess types($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveProcess uniqued($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveProcess whereApproveId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveProcess whereCardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveProcess whereConditionList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveProcess whereDepHead($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveProcess whereDirectorLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveProcess whereDirectorOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveProcess whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveProcess whereExamineMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveProcess whereGroups($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveProcess whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveProcess whereInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveProcess whereIsChild($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveProcess whereIsCondition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveProcess whereIsInitial($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveProcess whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveProcess whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveProcess whereNoHander($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveProcess whereParent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveProcess wherePassRatio($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveProcess wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveProcess whereSelectMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveProcess whereSelectRange($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveProcess whereSelfSelect($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveProcess whereSettype($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveProcess whereTypes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveProcess whereUniqued($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveProcess whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveProcess whereUserList($value)
 * @mixin \Eloquent
 */
	class ApproveProcess extends \Eloquent {}
}

namespace App\Http\Model\Approve{
/**
 * 审核留言表
 * Class ApproveReply.
 *
 * @property int $id 自增id
 * @property int $user_id 用户id
 * @property int $card_id 创建用户名片ID
 * @property int $apply_id
 * @property string $content 回复内容
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Http\Model\Approve\Approve|null $approve
 * @property-read Admin|null $card
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Attach> $files
 * @property-read int|null $files_count
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveReply cateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveReply entid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveReply id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveReply nameLike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveReply newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveReply newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveReply notid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveReply query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveReply whereApplyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveReply whereCardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveReply whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveReply whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveReply whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveReply whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveReply whereUserId($value)
 * @mixin \Eloquent
 */
	class ApproveReply extends \Eloquent {}
}

namespace App\Http\Model\Approve{
/**
 * 审核规则表
 * Class ApproveRule.
 *
 * @property int $id 自增id
 * @property int $user_id 用户id
 * @property int $card_id 创建用户名片ID
 * @property int $approve_id
 * @property string $range 可见范围
 * @property int $abnormal 异常处理：0、自动同意；指定处理人ID；
 * @property string $auto 自动审批：0、首个节点处理，其他自动同意；1、连续审批自动同意；2、每个节点都需审批；
 * @property mixed $edit 修改权限：0、员工不可修改固定人员；1、不可删除固定抄送人；
 * @property int $recall 异常处理：1、审批通过后允许撤销；
 * @property int $is_transfer 是否可转审
 * @property int $is_sign 是否可加签
 * @property string|null $refuse 被拒绝后：0、返回初始，所有人重新审批；1、跳过已通过层级；
 * @property-read Admin|null $abCard
 * @property-read \App\Http\Model\Approve\Approve|null $approve
 * @property-read Admin|null $card
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveRule approveId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveRule cateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveRule entid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveRule id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveRule nameLike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveRule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveRule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveRule notid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveRule query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveRule whereAbnormal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveRule whereApproveId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveRule whereAuto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveRule whereCardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveRule whereEdit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveRule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveRule whereIsSign($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveRule whereIsTransfer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveRule whereRange($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveRule whereRecall($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveRule whereRefuse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveRule whereUserId($value)
 * @mixin \Eloquent
 */
	class ApproveRule extends \Eloquent {}
}

namespace App\Http\Model\Approve{
/**
 * 申请内容表
 * Class ApproveUser.
 *
 * @property int $id 自增id
 * @property int $user_id
 * @property int $card_id 相关用户名片ID
 * @property int $approve_id
 * @property int $apply_id
 * @property string $node_id 审核节点ID(唯一值)
 * @property int $level 级别
 * @property int $sort 审批顺序
 * @property int $verify 操作状态：0、自动；1、手动；
 * @property int $status 审批状态：-1、无需审批；0、待审批；1、已通过；2、已拒绝；
 * @property int $is_sign 是否为加签
 * @property int $is_transfer 是否为转审：0、正常节点；1、已转审；2、被转审；
 * @property int $parent 转审人ID
 * @property int $types 类型：1、审核人；2、抄送人；
 * @property mixed $info 人员详情
 * @property mixed $process_info 流程节点详情
 * @property string $content 人员说明
 * @property Carbon|null $created_at
 * @property mixed $updated_at
 * @property-read \App\Http\Model\Approve\Approve|null $approve
 * @property-read Admin|null $card
 * @property-read \App\Http\Model\Approve\ApproveProcess|null $process
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveUser applyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveUser approveId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveUser cardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveUser id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveUser isSign($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveUser isTransfer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveUser level($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveUser levelGt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveUser nodeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveUser nodeIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveUser notCard($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveUser notStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveUser query()
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveUser sort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveUser status($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveUser types($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveUser userId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveUser whereApplyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveUser whereApproveId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveUser whereCardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveUser whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveUser whereInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveUser whereIsSign($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveUser whereIsTransfer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveUser whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveUser whereNodeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveUser whereParent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveUser whereProcessInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveUser whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveUser whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveUser whereTypes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveUser whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApproveUser whereVerify($value)
 * @mixin \Eloquent
 */
	class ApproveUser extends \Eloquent {}
}

namespace App\Http\Model\Assess{
/**
 * Class AssessPlan.
 *
 * @property int $id 自增id
 * @property int $entid
 * @property int $create_time 星期:1-7/或者几号1-31
 * @property int $create_month 月
 * @property int $assess_type 被考核人类型：0=人员添加,1=部门添加
 * @property array|null $test_frame 考核部门ID列表
 * @property array|null $test_user 考核人员ID列表
 * @property int $period 周期:1=周;2=月;3=年;5=季度;4=半年
 * @property string $make_type 目标制定时间类型：考核开始前、考核开始后
 * @property int $make_day 目标制定天数
 * @property string $eval_type 上级评价时间类型：考核结束前、考核结束后
 * @property int $eval_day 上级评价天数
 * @property string $verify_type 审核时间类型：评价结束前、评价结束后
 * @property int $verify_day 绩效审核天数
 * @property int $status 状态:0=禁用;1=开启
 * @property string $uniqued 任务唯一值
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|AssessPlan id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessPlan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AssessPlan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AssessPlan query()
 * @method static \Illuminate\Database\Eloquent\Builder|AssessPlan time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessPlan whereAssessType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessPlan whereCreateMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessPlan whereCreateTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessPlan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessPlan whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessPlan whereEvalDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessPlan whereEvalType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessPlan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessPlan whereMakeDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessPlan whereMakeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessPlan wherePeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessPlan whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessPlan whereTestFrame($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessPlan whereTestUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessPlan whereUniqued($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessPlan whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessPlan whereVerifyDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessPlan whereVerifyType($value)
 * @mixin \Eloquent
 */
	class AssessPlan extends \Eloquent {}
}

namespace App\Http\Model\Assess{
/**
 * Class AssessReply.
 *
 * @property int $id 自增id
 * @property int $assessid
 * @property int $entid 企业ID
 * @property int $user_id 企业用户ID
 * @property string $content 内容
 * @property int $is_own 自身可见：0、否；1、是
 * @property int $types 类型：0、评价；1、申诉
 * @property int $status 申诉状态：0、评价；1、已处理；2、已拒绝；
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Admin|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|AssessReply id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessReply isOwn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessReply newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AssessReply newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AssessReply query()
 * @method static \Illuminate\Database\Eloquent\Builder|AssessReply time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessReply whereAssessid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessReply whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessReply whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessReply whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessReply whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessReply whereIsOwn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessReply whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessReply whereTypes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessReply whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessReply whereUserId($value)
 * @mixin \Eloquent
 */
	class AssessReply extends \Eloquent {}
}

namespace App\Http\Model\Assess{
/**
 * 企业绩效考核方案
 * Class AssessScheme.
 *
 * @property int $id 自增id
 * @property int $entid
 * @property string $name 名称
 * @property int $period 周期:1=周;2=月;3=年
 * @property string $create_type 生成绩效日期类型
 * @property int $create_month 生成绩效月份
 * @property int $create_day 生成绩效日期
 * @property string $create_time 生成绩效时间
 * @property string $own_appraise_period 自评结束时间类型
 * @property int $own_appraise_month 自评结束月份
 * @property int $own_appraise_day 自评结束日期
 * @property string $own_appraise_time 自评结束时间
 * @property string $leader_appraise_period 上级评分结束时间类型
 * @property int $leader_appraise_month 上级评分结束月份
 * @property int $leader_appraise_day 上级评分结束日期
 * @property string $leader_appraise_time 上级评分结束时间
 * @property int $user_id 企业成员ID(user_enterprise主键ID)
 * @property int $user_count 被考核人数
 * @property string $file_id 文件标识
 * @property int $status 状态:0=禁用;1=开启
 * @property \Illuminate\Support\Carbon|null $delete 是否删除
 * @property false|string[] $other 其他数据
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Admin|null $userEnt
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\Assess\AssessUser> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|AssessScheme nameLike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessScheme newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AssessScheme newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AssessScheme onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AssessScheme query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessScheme whereCreateDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessScheme whereCreateMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessScheme whereCreateTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessScheme whereCreateType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessScheme whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessScheme whereDelete($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessScheme whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessScheme whereFileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessScheme whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessScheme whereLeaderAppraiseDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessScheme whereLeaderAppraiseMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessScheme whereLeaderAppraisePeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessScheme whereLeaderAppraiseTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessScheme whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessScheme whereOther($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessScheme whereOwnAppraiseDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessScheme whereOwnAppraiseMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessScheme whereOwnAppraisePeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessScheme whereOwnAppraiseTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessScheme wherePeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessScheme whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessScheme whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessScheme whereUserCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessScheme whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessScheme withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AssessScheme withoutTrashed()
 * @mixin \Eloquent
 */
	class AssessScheme extends \Eloquent {}
}

namespace App\Http\Model\Assess{
/**
 * Class AssessScore.
 *
 * @property int $id 自增id
 * @property int $entid
 * @property int $user_id 用户关联企业表(user_enterprise主键)ID
 * @property string $name 等级名称
 * @property int $min 分数最小值
 * @property int $max 分数最大值
 * @property int $level 级别
 * @property string $mark 说明
 * @method static \Illuminate\Database\Eloquent\Builder|AssessScore entid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessScore level($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessScore newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AssessScore newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AssessScore query()
 * @method static \Illuminate\Database\Eloquent\Builder|AssessScore score($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessScore whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessScore whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessScore whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessScore whereMark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessScore whereMax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessScore whereMin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessScore whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessScore whereUserId($value)
 * @mixin \Eloquent
 */
	class AssessScore extends \Eloquent {}
}

namespace App\Http\Model\Assess{
/**
 * Class AssessSpace.
 *
 * @property int $id 自增id
 * @property int $entid
 * @property int $assessid 考核列表ID
 * @property int $targetid 考核模板ID
 * @property string $name 维度名称
 * @property int $ratio 维度占比
 * @property int $sort 排序
 * @property \Illuminate\Support\Carbon|null $deleted_at 删除时间
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\Assess\AssessTarget> $target
 * @property-read int|null $target_count
 * @method static \Illuminate\Database\Eloquent\Builder|AssessSpace assessid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessSpace id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessSpace newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AssessSpace newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AssessSpace onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AssessSpace query()
 * @method static \Illuminate\Database\Eloquent\Builder|AssessSpace targetid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessSpace time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessSpace whereAssessid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessSpace whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessSpace whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessSpace whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessSpace whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessSpace whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessSpace whereRatio($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessSpace whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessSpace whereTargetid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessSpace whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessSpace withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AssessSpace withoutTrashed()
 * @mixin \Eloquent
 */
	class AssessSpace extends \Eloquent {}
}

namespace App\Http\Model\Assess{
/**
 * 指标模板
 * Class AssessTarget.
 *
 * @property int $id 自增id
 * @property int $spaceid 维度ID
 * @property int $ratio 权重占比
 * @property int $sort 排序
 * @property string $name 指标名称
 * @property string $content 指标内容
 * @property string|null $info 评分等级
 * @property string $finish_info 完成情况
 * @property int $finish_ratio 完成百分比
 * @property string|null $check_info 上级评价
 * @property int $max 最高得分
 * @property int $score 评价得分
 * @property \Illuminate\Support\Carbon|null $deleted_at 删除时间
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Http\Model\Assess\AssessTargetCategory|null $cate
 * @property-read Admin|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|AssessTarget id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessTarget name($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessTarget newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AssessTarget newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AssessTarget notId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessTarget onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AssessTarget query()
 * @method static \Illuminate\Database\Eloquent\Builder|AssessTarget spaceid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessTarget time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessTarget whereCheckInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessTarget whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessTarget whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessTarget whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessTarget whereFinishInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessTarget whereFinishRatio($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessTarget whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessTarget whereInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessTarget whereMax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessTarget whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessTarget whereRatio($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessTarget whereScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessTarget whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessTarget whereSpaceid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessTarget whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessTarget withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AssessTarget withoutTrashed()
 * @mixin \Eloquent
 */
	class AssessTarget extends \Eloquent {}
}

namespace App\Http\Model\Assess{
/**
 * 指标模板分类
 * Class AssessTargetCategory.
 *
 * @property int $id 自增id
 * @property int $entid
 * @property false|string[] $path 路径
 * @property string $name 职级类别名称
 * @property int $pid 上级ID
 * @property int $types 类型：0、指标分类；1、指标模板分类；
 * @property int $status 开放状态：0、不开放；1、开放；
 * @property-read AssessTargetCategory|null $cate
 * @method static \Illuminate\Database\Eloquent\Builder|AssessTargetCategory entid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessTargetCategory nameLike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessTargetCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AssessTargetCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AssessTargetCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessTargetCategory types($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessTargetCategory whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessTargetCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessTargetCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessTargetCategory wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessTargetCategory wherePid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessTargetCategory whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessTargetCategory whereTypes($value)
 * @mixin \Eloquent
 */
	class AssessTargetCategory extends \Eloquent {}
}

namespace App\Http\Model\Assess{
/**
 * 绩效考核人员关联
 * Class AssessUser.
 *
 * @property int $id 自增id
 * @property int $scheme_id
 * @property int $user_id 用户关联企业表(user_enterprise主键)ID
 * @property-read FrameAssist|null $frame
 * @property-read Admin|null $userent
 * @method static \Illuminate\Database\Eloquent\Builder|AssessUser checkUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AssessUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AssessUser planid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessUser query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessUser whereSchemeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessUser whereUserId($value)
 * @mixin \Eloquent
 */
	class AssessUser extends \Eloquent {}
}

namespace App\Http\Model\Assess{
/**
 * Class EnterpriseTemplate.
 *
 * @property int $id 自增id
 * @property int $entid 企业ID
 * @property int $user_id 企业用户ID
 * @property int $cate_id 模板分类ID
 * @property string $name 模板名称
 * @property string $info 模板简介
 * @property string $cover 封面图
 * @property string $color 默认字体颜色
 * @property int $status 开放状态：0、不开放；1、开放；
 * @property int $types 记分类型：0，加权评分；1，加和评分
 * @property int $way 来源：0、企业端；
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Http\Model\Assess\AssessTargetCategory|null $cate
 * @property-read \App\Http\Model\Assess\TemplateCollect|null $collect
 * @property-read Admin|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|EnterpriseTemplate cateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnterpriseTemplate id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnterpriseTemplate name($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnterpriseTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EnterpriseTemplate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EnterpriseTemplate query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnterpriseTemplate types($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnterpriseTemplate whereCateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnterpriseTemplate whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnterpriseTemplate whereCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnterpriseTemplate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnterpriseTemplate whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnterpriseTemplate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnterpriseTemplate whereInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnterpriseTemplate whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnterpriseTemplate whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnterpriseTemplate whereTypes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnterpriseTemplate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnterpriseTemplate whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnterpriseTemplate whereWay($value)
 * @mixin \Eloquent
 */
	class EnterpriseTemplate extends \Eloquent {}
}

namespace App\Http\Model\Assess{
/**
 * Class TemplateCollect.
 *
 * @property int $id 自增id
 * @property int $entid 企业ID
 * @property int $user_id 企业用户ID
 * @property int $temp_id 考核模板ID
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateCollect newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateCollect newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateCollect query()
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateCollect tempId($val)
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateCollect userId($val)
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateCollect whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateCollect whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateCollect whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateCollect whereTempId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateCollect whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateCollect whereUserId($value)
 * @mixin \Eloquent
 */
	class TemplateCollect extends \Eloquent {}
}

namespace App\Http\Model\Attach{
/**
 * Class SystemAttach.
 *
 * @property int $id 附件ID
 * @property int $entid 分后台ID
 * @property string $uid 上传用户uid
 * @property string $name 附件名称
 * @property string $real_name 附件原始名称
 * @property string $att_dir 附件路径
 * @property string $thumb_dir 附件压缩路径
 * @property string $att_size 附件大小
 * @property string $att_type 附件类型
 * @property string $file_ext 文件扩展名
 * @property int $cid 分类ID
 * @property int $up_type 上传方式：1、本地；2、七牛云；3、OSS；4、COS。
 * @property int $way 来源：1、总后台；2、分后台；3、用户。
 * @property int $relation_type 模块:1、汇报；
 * @property int $relation_id 模块ID
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Admin|null $card
 * @property-read string $src
 * @property-read string $url
 * @method static \Illuminate\Database\Eloquent\Builder|SystemAttach cid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemAttach entid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemAttach entids($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemAttach fileExt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemAttach id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemAttach name($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemAttach newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemAttach newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemAttach query()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemAttach relationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemAttach relationType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemAttach time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemAttach whereAttDir($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemAttach whereAttSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemAttach whereAttType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemAttach whereCid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemAttach whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemAttach whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemAttach whereFileExt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemAttach whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemAttach whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemAttach whereRealName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemAttach whereRelationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemAttach whereRelationType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemAttach whereThumbDir($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemAttach whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemAttach whereUpType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemAttach whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemAttach whereWay($value)
 * @mixin \Eloquent
 */
	class SystemAttach extends \Eloquent {}
}

namespace App\Http\Model\Attendance{
/**
 * 审批记录.
 *
 * @property int $id 自增ID
 * @property int $uid 申请人
 * @property int $apply_type 审批申请类型：1：请假；2：补卡；3：加班；4：外出；5：出差；
 * @property string $type_unique 类型/异常标识
 * @property int $date_type 日期类型：1：工作日；2：休息日；3：节假日；
 * @property string $time_type 工时类型：day：天；hour：小时；minute：分钟；
 * @property int $calc_type 核算方式：1：调休；2：加班费；
 * @property float $work_hours 加班时长
 * @property int $apply_id 申请记录ID
 * @property \Illuminate\Support\Carbon|null $start_time
 * @property \Illuminate\Support\Carbon|null $end_time
 * @property array|mixed $others 其他标识
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read ApproveApply|null $approveApply
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceApplyRecord applyType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceApplyRecord calcType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceApplyRecord date($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceApplyRecord dateType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceApplyRecord id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceApplyRecord month($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceApplyRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceApplyRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceApplyRecord query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceApplyRecord typeUnique($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceApplyRecord uId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceApplyRecord whereApplyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceApplyRecord whereApplyType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceApplyRecord whereCalcType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceApplyRecord whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceApplyRecord whereDateType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceApplyRecord whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceApplyRecord whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceApplyRecord whereOthers($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceApplyRecord whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceApplyRecord whereTimeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceApplyRecord whereTypeUnique($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceApplyRecord whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceApplyRecord whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceApplyRecord whereWorkHours($value)
 * @mixin \Eloquent
 */
	class AttendanceApplyRecord extends \Eloquent {}
}

namespace App\Http\Model\Attendance{
/**
 * 考勤排班.
 *
 * @property int $id 自增ID
 * @property int $group_id 考勤组ID
 * @property int $uid 业务员ID
 * @property string $date 考勤时间
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Http\Model\Attendance\AttendanceGroup|null $group
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceArrange attendDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceArrange date($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceArrange month($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceArrange newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceArrange newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceArrange onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceArrange query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceArrange uId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceArrange whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceArrange whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceArrange whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceArrange whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceArrange whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceArrange whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceArrange whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceArrange withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceArrange withoutTrashed()
 * @mixin \Eloquent
 */
	class AttendanceArrange extends \Eloquent {}
}

namespace App\Http\Model\Attendance{
/**
 * 排班数据.
 *
 * @property int $id 自增ID
 * @property int $arrange_id 排班ID
 * @property int $group_id 考勤组ID
 * @property int $uid 业务员ID
 * @property int $shift_id 班次ID
 * @property string $date 排班日期
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read Admin|null $card
 * @property-read \App\Http\Model\Attendance\AttendanceShift|null $shift
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceArrangeRecord date($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceArrangeRecord dateGt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceArrangeRecord gtDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceArrangeRecord id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceArrangeRecord month($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceArrangeRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceArrangeRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceArrangeRecord onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceArrangeRecord query()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceArrangeRecord shiftIdGt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceArrangeRecord uid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceArrangeRecord whereArrangeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceArrangeRecord whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceArrangeRecord whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceArrangeRecord whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceArrangeRecord whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceArrangeRecord whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceArrangeRecord whereShiftId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceArrangeRecord whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceArrangeRecord whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceArrangeRecord withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceArrangeRecord withoutTrashed()
 * @mixin \Eloquent
 */
	class AttendanceArrangeRecord extends \Eloquent {}
}

namespace App\Http\Model\Attendance{
/**
 * 考勤打卡.
 *
 * @property int $id 自增ID
 * @property int $frame_id 部门ID
 * @property int $group_id 考勤组ID
 * @property string $group 考勤组名称
 * @property int $shift_id 考勤班次ID
 * @property mixed|null $shift_data 班次数据
 * @property string $address 打卡地址
 * @property string $lat 纬度
 * @property string $lng 经度
 * @property string $remark 备注
 * @property mixed|null $image 图片
 * @property int $uid 考勤人员ID
 * @property int $is_external 外勤打卡:0、考勤打卡；1、外勤打卡；
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int $clock_type 打卡方式：0、位置；1、Wifi
 * @property string $mac Wi-Fi打卡Mac地址
 * @property-read Admin|null $card
 * @property-read Frame|null $frame
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceClockRecord date($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceClockRecord id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceClockRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceClockRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceClockRecord onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceClockRecord query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceClockRecord uid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceClockRecord whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceClockRecord whereClockType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceClockRecord whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceClockRecord whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceClockRecord whereFrameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceClockRecord whereGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceClockRecord whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceClockRecord whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceClockRecord whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceClockRecord whereIsExternal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceClockRecord whereLat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceClockRecord whereLng($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceClockRecord whereMac($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceClockRecord whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceClockRecord whereShiftData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceClockRecord whereShiftId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceClockRecord whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceClockRecord whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceClockRecord withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceClockRecord withoutTrashed()
 * @mixin \Eloquent
 */
	class AttendanceClockRecord extends \Eloquent {}
}

namespace App\Http\Model\Attendance{
/**
 * 考勤组.
 *
 * @property int $id 自增ID
 * @property string $name 考勤组名称
 * @property int $type 考勤类型:0、人员；1、部门；
 * @property string $address 详细地址
 * @property string $lat 纬度
 * @property string $lng 经度
 * @property int $effective_range 有效范围
 * @property string $location_name 考勤地点名称
 * @property int $repair_allowed 允许补卡
 * @property int[] $repair_type 补卡类型:1、缺卡;2、迟到;3、严重迟到;4、早退；
 * @property int $is_limit_time 补卡时间限制:0、不限制；1、限制；
 * @property int $limit_time 补卡时间
 * @property int $is_limit_number 补卡次数限制:0、不限制；1、限制；
 * @property int $limit_number 补卡次数
 * @property int $is_photo 拍照打卡:0、不限制；1、限制；
 * @property int $is_external 外勤打卡:0、不限制；1、限制；
 * @property int $is_external_note 外勤打卡备注:0、不限制；1、限制；
 * @property int $is_external_photo 外勤打卡拍照:0、不限制；1、限制；
 * @property int $uid 业务员ID
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int $is_map 地图位置打卡
 * @property int $is_wifi Wi-Fi打卡
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Admin> $admin
 * @property-read int|null $admin_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Admin> $admins
 * @property-read int|null $admins_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Admin> $filters
 * @property-read int|null $filters_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Admin> $members
 * @property-read int|null $members_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\Attendance\AttendanceShift> $shifts
 * @property-read int|null $shifts_count
 * @property-read Admin|null $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\Attendance\AttendanceWifi> $wifi
 * @property-read int|null $wifi_count
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceGroup authUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceGroup id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceGroup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceGroup notId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceGroup onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceGroup whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceGroup whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceGroup whereEffectiveRange($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceGroup whereIsExternal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceGroup whereIsExternalNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceGroup whereIsExternalPhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceGroup whereIsLimitNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceGroup whereIsLimitTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceGroup whereIsMap($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceGroup whereIsPhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceGroup whereIsWifi($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceGroup whereLat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceGroup whereLimitNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceGroup whereLimitTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceGroup whereLng($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceGroup whereLocationName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceGroup whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceGroup whereRepairAllowed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceGroup whereRepairType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceGroup whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceGroup whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceGroup whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceGroup withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceGroup withoutTrashed()
 * @mixin \Eloquent
 */
	class AttendanceGroup extends \Eloquent {}
}

namespace App\Http\Model\Attendance{
/**
 * 考勤组人员.
 *
 * @property int $id 自增ID
 * @property int $group_id 考勤组ID
 * @property int $member 考勤类型ID
 * @property int $type 考勤成员类型:0、考勤人员；1、无需考勤人员；2、考勤组负责人；3、考勤部门；
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int $entid 企业ID
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceGroupMember id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceGroupMember member($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceGroupMember newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceGroupMember newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceGroupMember notGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceGroupMember onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceGroupMember query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceGroupMember type($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceGroupMember whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceGroupMember whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceGroupMember whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceGroupMember whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceGroupMember whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceGroupMember whereMember($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceGroupMember whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceGroupMember whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceGroupMember withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceGroupMember withoutTrashed()
 * @mixin \Eloquent
 */
	class AttendanceGroupMember extends \Eloquent {}
}

namespace App\Http\Model\Attendance{
/**
 * 考勤组班次.
 *
 * @property int $id 自增ID
 * @property int $group_id 考勤组ID
 * @property int $shift_id 班次表ID
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceGroupShift id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceGroupShift newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceGroupShift newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceGroupShift onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceGroupShift query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceGroupShift whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceGroupShift whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceGroupShift whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceGroupShift whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceGroupShift whereShiftId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceGroupShift whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceGroupShift withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceGroupShift withoutTrashed()
 * @mixin \Eloquent
 */
	class AttendanceGroupShift extends \Eloquent {}
}

namespace App\Http\Model\Attendance{
/**
 * 处理记录.
 *
 * @property int $id 自增ID
 * @property int $statistics_id 统计ID
 * @property int $shift_number 班次编号
 * @property int $before_status 修改前状态
 * @property int $before_location_status 修改前外勤状态
 * @property int $after_status 修改后状态
 * @property int $after_location_status 修改后外勤状态
 * @property string $result 打卡结果
 * @property string $remark 备注
 * @property int $source 来源：0、手动修改；1、补卡申请；
 * @property int $uid 操作人
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Admin|null $card
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceHandleRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceHandleRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceHandleRecord query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceHandleRecord whereAfterLocationStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceHandleRecord whereAfterStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceHandleRecord whereBeforeLocationStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceHandleRecord whereBeforeStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceHandleRecord whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceHandleRecord whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceHandleRecord whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceHandleRecord whereResult($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceHandleRecord whereShiftNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceHandleRecord whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceHandleRecord whereStatisticsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceHandleRecord whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceHandleRecord whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class AttendanceHandleRecord extends \Eloquent {}
}

namespace App\Http\Model\Attendance{
/**
 * 考勤提醒.
 *
 * @property int $id 自增ID
 * @property int $shift_id 班次ID
 * @property int $shift_num 打卡班次数量
 * @property mixed|null $one_shift_time 一班次上班时间
 * @property mixed|null $one_shift_remind 一班次上班提醒
 * @property int $one_shift_remind_push 一班次上班是否推送
 * @property mixed|null $one_shift_remind_short 一班次上班缺卡提醒
 * @property mixed|null $two_shift_time 一班次下班时间
 * @property mixed|null $two_shift_remind 一班次下班提醒
 * @property int $two_shift_remind_push 一班次下班是否推送
 * @property mixed|null $two_shift_remind_short 一班次下班缺卡提醒
 * @property mixed|null $three_shift_time 二班次上班时间
 * @property mixed|null $three_shift_remind 二班次上班提醒
 * @property int $three_shift_remind_push 二班次上班是否推送
 * @property mixed|null $three_shift_remind_short 二班次上班缺卡提醒
 * @property mixed|null $four_shift_time 二班次下班时间
 * @property mixed|null $four_shift_remind 二班次下班提醒
 * @property int $four_shift_remind_push 二班次下班是否推送
 * @property mixed|null $four_shift_remind_short 二班次下班缺卡提醒
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $date
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceRemind date($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceRemind id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceRemind newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceRemind newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceRemind query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceRemind toBePushed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceRemind whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceRemind whereFourShiftRemind($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceRemind whereFourShiftRemindPush($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceRemind whereFourShiftRemindShort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceRemind whereFourShiftTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceRemind whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceRemind whereOneShiftRemind($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceRemind whereOneShiftRemindPush($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceRemind whereOneShiftRemindShort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceRemind whereOneShiftTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceRemind whereShiftId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceRemind whereShiftNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceRemind whereThreeShiftRemind($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceRemind whereThreeShiftRemindPush($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceRemind whereThreeShiftRemindShort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceRemind whereThreeShiftTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceRemind whereTwoShiftRemind($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceRemind whereTwoShiftRemindPush($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceRemind whereTwoShiftRemindShort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceRemind whereTwoShiftTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceRemind whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class AttendanceRemind extends \Eloquent {}
}

namespace App\Http\Model\Attendance{
/**
 * 考勤班次.
 *
 * @property int $id 自增ID
 * @property string $name 班次名称
 * @property int $number 上下班次数 0、休息；1、1次上下班；2、2次上下班；
 * @property int $rest_time 中途休息：1、开启；0、关闭；
 * @property string $rest_start 休息开始时间
 * @property string $rest_end 休息结束时间
 * @property int $rest_start_after 休息开始规则 0、当日；1、次日；
 * @property int $rest_end_after 休息结束规则 0、当日；1、次日；
 * @property int $overtime 加班起算时间
 * @property string $work_time 工作时长
 * @property string $color 颜色标识
 * @property int $sort 排序
 * @property int $uid 业务员ID
 * @property int $types 班次类型:0、自定义;2、默认班次;1、休息;
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read Admin|null $card
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\Attendance\AttendanceShiftRule> $rules
 * @property-read int|null $rules_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\Attendance\AttendanceShiftRule> $times
 * @property-read int|null $times_count
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceShift idGt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceShift newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceShift newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceShift notId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceShift onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceShift query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceShift whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceShift whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceShift whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceShift whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceShift whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceShift whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceShift whereOvertime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceShift whereRestEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceShift whereRestEndAfter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceShift whereRestStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceShift whereRestStartAfter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceShift whereRestTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceShift whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceShift whereTypes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceShift whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceShift whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceShift whereWorkTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceShift withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceShift withoutTrashed()
 * @mixin \Eloquent
 */
	class AttendanceShift extends \Eloquent {}
}

namespace App\Http\Model\Attendance{
/**
 * 考勤班次规则.
 *
 * @property int $id 自增ID
 * @property int $shift_id 班次表ID
 * @property int $number 次数 1、1次上下班；2、2次上下班；
 * @property int $first_day_after 上班当日次数 0、当日；1、次日；
 * @property int $second_day_after 下班当日次数 0、当日；1、次日；
 * @property string $work_hours 上班时间
 * @property int $late 迟到
 * @property int $extreme_late 严重迟到
 * @property int $late_lack_card 晚到缺卡
 * @property int $early_card 提前打卡
 * @property string $off_hours 下班时间
 * @property int $early_leave 早退
 * @property int $early_lack_card 提前缺卡
 * @property int $delay_card 延后打卡
 * @property int $free_clock 下班可免打卡
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceShiftRule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceShiftRule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceShiftRule onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceShiftRule query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceShiftRule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceShiftRule whereDelayCard($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceShiftRule whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceShiftRule whereEarlyCard($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceShiftRule whereEarlyLackCard($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceShiftRule whereEarlyLeave($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceShiftRule whereExtremeLate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceShiftRule whereFirstDayAfter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceShiftRule whereFreeClock($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceShiftRule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceShiftRule whereLate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceShiftRule whereLateLackCard($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceShiftRule whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceShiftRule whereOffHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceShiftRule whereSecondDayAfter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceShiftRule whereShiftId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceShiftRule whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceShiftRule whereWorkHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceShiftRule withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceShiftRule withoutTrashed()
 * @mixin \Eloquent
 */
	class AttendanceShiftRule extends \Eloquent {}
}

namespace App\Http\Model\Attendance{
/**
 * 考勤缺卡提醒.
 *
 * @property int $id 自增ID
 * @property int $shift_id 班次ID
 * @property int $uid 员工ID
 * @property int $short_type 提醒类型：0、上班；1、下班；
 * @property mixed|null $work_time 上班时间
 * @property mixed|null $remind_time 推送时间
 * @property int $is_push 是否推送
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceShortRemind newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceShortRemind newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceShortRemind query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceShortRemind whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceShortRemind whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceShortRemind whereIsPush($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceShortRemind whereRemindTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceShortRemind whereShiftId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceShortRemind whereShortType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceShortRemind whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceShortRemind whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceShortRemind whereWorkTime($value)
 * @mixin \Eloquent
 */
	class AttendanceShortRemind extends \Eloquent {}
}

namespace App\Http\Model\Attendance{
/**
 * 考勤统计.
 *
 * @property int $id 自增ID
 * @property int $uid 考勤人员ID
 * @property int $frame_id 部门ID
 * @property int $group_id 考勤组ID
 * @property \App\Http\Model\Attendance\AttendanceGroup|null $group 考勤组名称
 * @property int $shift_id 考勤班次ID
 * @property array $shift_data 班次数据
 * @property mixed|null $one_shift_time 一班次上班打卡时间
 * @property int $one_shift_is_after 当日次数：0、当日；1、次日；
 * @property int $one_shift_status 打卡状态：0、无需打卡；1、正常；2、迟到；3、严重迟到；4、早退；5、缺卡；
 * @property int $one_shift_location_status 地点状态:0、正常；1、外勤；2、地点异常；
 * @property int $one_shift_record_id 打卡记录ID
 * @property mixed|null $two_shift_time 一班次下班打卡时间
 * @property int $two_shift_is_after
 * @property int $two_shift_status
 * @property int $two_shift_location_status
 * @property int $two_shift_record_id
 * @property mixed|null $three_shift_time 二班次上班打卡时间
 * @property int $three_shift_is_after
 * @property int $three_shift_status
 * @property int $three_shift_location_status
 * @property int $three_shift_record_id
 * @property mixed|null $four_shift_time 二班次下班打卡时间
 * @property int $four_shift_is_after
 * @property int $four_shift_status
 * @property int $four_shift_location_status
 * @property int $four_shift_record_id
 * @property string $required_work_hours 应出勤工时
 * @property string $actual_work_hours 实际出勤工时
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read Admin|null $card
 * @property-read Frame|null $frame
 * @property-read string $date
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceStatistics abnormalStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceStatistics date($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceStatistics gtDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceStatistics id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceStatistics locationStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceStatistics locationStatusGt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceStatistics locationStatusLt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceStatistics month($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceStatistics newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceStatistics newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceStatistics onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceStatistics query()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceStatistics shiftIdGt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceStatistics status($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceStatistics statusGt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceStatistics statusLt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceStatistics uid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceStatistics whereActualWorkHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceStatistics whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceStatistics whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceStatistics whereFourShiftIsAfter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceStatistics whereFourShiftLocationStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceStatistics whereFourShiftRecordId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceStatistics whereFourShiftStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceStatistics whereFourShiftTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceStatistics whereFrameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceStatistics whereGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceStatistics whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceStatistics whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceStatistics whereOneShiftIsAfter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceStatistics whereOneShiftLocationStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceStatistics whereOneShiftRecordId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceStatistics whereOneShiftStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceStatistics whereOneShiftTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceStatistics whereRequiredWorkHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceStatistics whereShiftData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceStatistics whereShiftId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceStatistics whereThreeShiftIsAfter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceStatistics whereThreeShiftLocationStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceStatistics whereThreeShiftRecordId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceStatistics whereThreeShiftStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceStatistics whereThreeShiftTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceStatistics whereTwoShiftIsAfter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceStatistics whereTwoShiftLocationStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceStatistics whereTwoShiftRecordId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceStatistics whereTwoShiftStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceStatistics whereTwoShiftTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceStatistics whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceStatistics whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceStatistics withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceStatistics withoutTrashed()
 * @mixin \Eloquent
 */
	class AttendanceStatistics extends \Eloquent {}
}

namespace App\Http\Model\Attendance{
/**
 * 请假工时.
 *
 * @property int $id 自增ID
 * @property int $statistics_id 考勤记录ID
 * @property int $apply_record_id 申请记录ID
 * @property int $uid 考勤人员ID
 * @property string $type_unique 请假类型
 * @property string $leave_duration 请假工时
 * @property int $holiday_type_id 假期类型ID
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Http\Model\Attendance\AttendanceApplyRecord|null $applyRecord
 * @property-read string $date
 * @property-read \App\Http\Model\Attendance\AttendanceStatistics|null $statistics
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceStatisticsLeave applyRecordId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceStatisticsLeave date($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceStatisticsLeave month($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceStatisticsLeave newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceStatisticsLeave newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceStatisticsLeave query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceStatisticsLeave uid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceStatisticsLeave whereApplyRecordId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceStatisticsLeave whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceStatisticsLeave whereHolidayTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceStatisticsLeave whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceStatisticsLeave whereLeaveDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceStatisticsLeave whereStatisticsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceStatisticsLeave whereTypeUnique($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceStatisticsLeave whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceStatisticsLeave whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class AttendanceStatisticsLeave extends \Eloquent {}
}

namespace App\Http\Model\Attendance{
/**
 * 考勤白名单.
 *
 * @property int $id 自增ID
 * @property int $uid 业务员ID
 * @property int $type 人员类型:0、人员；1、管理员；
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property mixed|null $deleted_at
 * @property-read Admin|null $card
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceWhitelist newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceWhitelist newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceWhitelist query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceWhitelist uid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceWhitelist whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceWhitelist whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceWhitelist whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceWhitelist whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceWhitelist whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceWhitelist whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class AttendanceWhitelist extends \Eloquent {}
}

namespace App\Http\Model\Attendance{
/**
 * 考勤组wifi配置.
 *
 * @property int $id
 * @property int $entid 企业ID
 * @property int $group_id 考勤组ID
 * @property string $name wifi名称
 * @property string $mac wifi地址
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceWifi mac($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceWifi newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceWifi newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceWifi query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceWifi whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceWifi whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceWifi whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceWifi whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceWifi whereMac($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceWifi whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceWifi whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class AttendanceWifi extends \Eloquent {}
}

namespace App\Http\Model\Attendance{
/**
 * 日历设置.
 *
 * @property int $id
 * @property string $day 日期
 * @property int $is_rest 是否休息 0、上班；1、休息；
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|CalendarConfig day($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CalendarConfig id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CalendarConfig month($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CalendarConfig newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CalendarConfig newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CalendarConfig query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CalendarConfig whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CalendarConfig whereDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CalendarConfig whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CalendarConfig whereIsRest($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CalendarConfig whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CalendarConfig year($value)
 * @mixin \Eloquent
 */
	class CalendarConfig extends \Eloquent {}
}

namespace App\Http\Model\Attendance{
/**
 * 打卡记录.
 *
 * @method static \Illuminate\Database\Eloquent\Builder|ClockRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClockRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClockRecord onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ClockRecord query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClockRecord withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ClockRecord withoutTrashed()
 * @mixin \Eloquent
 */
	class ClockRecord extends \Eloquent {}
}

namespace App\Http\Model\Attendance{
/**
 * 排班周期.
 *
 * @property int $id 自增ID
 * @property int $group_id 考勤组ID
 * @property string $name 周期名称
 * @property int $cycle 周期
 * @property int $uid 业务员ID
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at 删除时间
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\Attendance\AttendanceShift> $shifts
 * @property-read int|null $shifts_count
 * @method static \Illuminate\Database\Eloquent\Builder|RosterCycle id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RosterCycle newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RosterCycle newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RosterCycle onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|RosterCycle query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RosterCycle whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RosterCycle whereCycle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RosterCycle whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RosterCycle whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RosterCycle whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RosterCycle whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RosterCycle whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RosterCycle whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RosterCycle withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|RosterCycle withoutTrashed()
 * @mixin \Eloquent
 */
	class RosterCycle extends \Eloquent {}
}

namespace App\Http\Model\Attendance{
/**
 * 排班周期.
 *
 * @property int $id 自增ID
 * @property int $cycle_id 周期ID
 * @property int $shift_id 班次ID
 * @property int $number 周期数
 * @property int $uid 业务员ID
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|RosterCycleShift cycleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RosterCycleShift newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RosterCycleShift newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RosterCycleShift onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|RosterCycleShift query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RosterCycleShift whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RosterCycleShift whereCycleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RosterCycleShift whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RosterCycleShift whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RosterCycleShift whereShiftId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RosterCycleShift whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RosterCycleShift whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RosterCycleShift withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|RosterCycleShift withoutTrashed()
 * @mixin \Eloquent
 */
	class RosterCycleShift extends \Eloquent {}
}

namespace App\Http\Model\Auth{
/**
 * 企业角色
 * Class Role.
 *
 * @property int $id 角色自增id
 * @property string $role_name 角色名称
 * @property string|null $types 角色类型，null为用户自己添加
 * @property int $user_count 用户数量
 * @property int $entid 企业ID
 * @property int $data_level 数据范围：见枚举；
 * @property int $directly 是否包含直属下级；
 * @property mixed $frame_id 指定部门ID；
 * @property array|null $module_permissions 内置模块数据权限配置
 * @property mixed $rules 身份管理权限(system_menus主键ID)
 * @property mixed $rule_unique 菜单标识
 * @property mixed $apis 身份管理接口权限(system_menus主键ID)
 * @property mixed $api_unique 接口标识
 * @property int $status 状态
 * @method static \Illuminate\Database\Eloquent\Builder|Role ids($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Role notEntids($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role notId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role query()
 * @method static \Illuminate\Database\Eloquent\Builder|Role ruleApi($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereApiUnique($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereApis($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereDataLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereDirectly($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereFrameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereModulePermissions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereRoleName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereRuleUnique($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereRules($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereTypes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereUserCount($value)
 * @mixin \Eloquent
 */
	class Role extends \Eloquent {}
}

namespace App\Http\Model\Auth{
/**
 * 角色用户
 * Class RoleUser.
 *
 * @property int $id 自增id
 * @property int $entid 企业ID
 * @property int $role_id 角色(enterprise_role主键)iD
 * @property int $user_id 用户关联企业表(user_enterprise主键)ID
 * @property int $status 状态1=开启;0=关闭
 * @property-read Admin|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|RoleUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RoleUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RoleUser query()
 * @method static \Illuminate\Database\Eloquent\Builder|RoleUser roleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoleUser roleIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoleUser userId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoleUser userIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoleUser whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoleUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoleUser whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoleUser whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoleUser whereUserId($value)
 * @mixin \Eloquent
 */
	class RoleUser extends \Eloquent {}
}

namespace App\Http\Model\Auth{
/**
 * 企业成员角色权限
 * Class UserRole.
 *
 * @property int $id 权限自增id
 * @property int $user_id 企业成员ID(user_enterprise主键ID)
 * @property mixed $rules 身份管理权限(system_menus主键ID)
 * @property mixed $apis 身份管理接口权限(system_menus主键ID)
 * @method static \Illuminate\Database\Eloquent\Builder|UserRole newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserRole newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserRole query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserRole userId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserRole whereApis($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserRole whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserRole whereRules($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserRole whereUserId($value)
 * @mixin \Eloquent
 */
	class UserRole extends \Eloquent {}
}

namespace App\Http\Model\Category{
/**
 * 分类
 * Class Category.
 *
 * @property int $id 分类自增id
 * @property int $pid 父级ID
 * @property string $cate_name 分类名称
 * @property false|string[] $path 路径
 * @property int $sort 排序
 * @property string $pic 图标
 * @property int $is_show 是否显示
 * @property int $level 等级
 * @property string $type 分类类型
 * @property string $keyword 标记词
 * @property int $entid 平台编号；0、总后台；
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static Builder|Category cateName($value)
 * @method static Builder|Category entid($value)
 * @method static Builder|Category eqCateName($value)
 * @method static Builder|Category isShow($value)
 * @method static Builder|Category keyword($value)
 * @method static Builder|Category ltLevel($value)
 * @method static Builder|Category newModelQuery()
 * @method static Builder|Category newQuery()
 * @method static Builder|Category notId($value)
 * @method static Builder|Category pid($value)
 * @method static Builder|Category query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static Builder|Category type($value)
 * @method static Builder|Category whereCateName($value)
 * @method static Builder|Category whereCreatedAt($value)
 * @method static Builder|Category whereEntid($value)
 * @method static Builder|Category whereId($value)
 * @method static Builder|Category whereIsShow($value)
 * @method static Builder|Category whereKeyword($value)
 * @method static Builder|Category whereLevel($value)
 * @method static Builder|Category wherePath($value)
 * @method static Builder|Category wherePic($value)
 * @method static Builder|Category wherePid($value)
 * @method static Builder|Category whereSort($value)
 * @method static Builder|Category whereType($value)
 * @method static Builder|Category whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class Category extends \Eloquent {}
}

namespace App\Http\Model\Category{
/**
 * 消息分类
 * Class MessageCategory.
 *
 * @property int $id 自增id
 * @property int $pid 父级ID
 * @property string $cate_name 分类名称
 * @property false|string[] $path 路径
 * @property int $sort 排序
 * @property string $pic 图标
 * @property int $is_show 是否显示
 * @property int $uni_show 移动端是否显示
 * @property int $level 等级
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static Builder|MessageCategory cateName($value)
 * @method static Builder|MessageCategory eqCateName($value)
 * @method static Builder|MessageCategory isShow($value)
 * @method static Builder|MessageCategory ltLevel($value)
 * @method static Builder|MessageCategory newModelQuery()
 * @method static Builder|MessageCategory newQuery()
 * @method static Builder|MessageCategory notId($value)
 * @method static Builder|MessageCategory pid($value)
 * @method static Builder|MessageCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static Builder|MessageCategory whereCateName($value)
 * @method static Builder|MessageCategory whereCreatedAt($value)
 * @method static Builder|MessageCategory whereId($value)
 * @method static Builder|MessageCategory whereIsShow($value)
 * @method static Builder|MessageCategory whereLevel($value)
 * @method static Builder|MessageCategory wherePath($value)
 * @method static Builder|MessageCategory wherePic($value)
 * @method static Builder|MessageCategory wherePid($value)
 * @method static Builder|MessageCategory whereSort($value)
 * @method static Builder|MessageCategory whereUniShow($value)
 * @method static Builder|MessageCategory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class MessageCategory extends \Eloquent {}
}

namespace App\Http\Model\Chat{
/**
 * App\Http\Model\Chat\ChatAppAuth
 *
 * @property int $id
 * @property int $app_id 创建用户ID
 * @property int $user_id 用户ID
 * @property-read Admin|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|ChatAppAuth newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatAppAuth newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatAppAuth query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatAppAuth whereAppId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatAppAuth whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatAppAuth whereUserId($value)
 * @mixin \Eloquent
 */
	class ChatAppAuth extends \Eloquent {}
}

namespace App\Http\Model\Chat{
/**
 * Class ChatAppMcpService.
 *
 * @property int $id
 * @property string $name 服务名称
 * @property string $info 简介
 * @property string $type 连接类型：sse/stdio
 * @property string $service_url MCP服务地址
 * @property array $headers 自定义请求头（JSON键值对）
 * @property array|null $config_json MCP配置JSON（transport/url/headers/timeout）
 * @property int $status 状态：0=禁用，1=启用
 * @property int $is_default 是否为默认服务：0=否，1=是
 * @property int $sort 排序
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ChatAppMcpService newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatAppMcpService newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatAppMcpService query()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatAppMcpService time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatAppMcpService whereConfigJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatAppMcpService whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatAppMcpService whereHeaders($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatAppMcpService whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatAppMcpService whereInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatAppMcpService whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatAppMcpService whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatAppMcpService whereServiceUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatAppMcpService whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatAppMcpService whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatAppMcpService whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatAppMcpService whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class ChatAppMcpService extends \Eloquent {}
}

namespace App\Http\Model\Chat{
/**
 * Class ClientBill.
 *
 * @property int $id
 * @property string $name 名称
 * @property string $pic
 * @property string $info 简介
 * @property mixed $edit 编辑权限
 * @property int $uid 创建用户ID
 * @property int $status 状态
 * @property string $auth_ids 成员ID
 * @property int $use_limit 使用频次
 * @property int $sort 排序
 * @property int $models_id 模型ID
 * @property int $count_number 对话轮数
 * @property string $tables 数据库表名
 * @property string $content 数据库内容
 * @property string $tooltip_text 提示词
 * @property string $prologue_text 开场白
 * @property string $prologue_list 开场白问题
 * @property string $json 高级设置
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int $is_table
 * @property int $source_type 数据源类型：0=标准，1=MCP
 * @property string $keyword 关键字
 * @property string $data_arrange_text 整理数据规格
 * @property string|null $mcp_json MCP配置JSON
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\Chat\ChatAppAuth> $auth
 * @property-read int|null $auth_count
 * @property-read Admin|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|ChatApplications newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatApplications newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatApplications onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatApplications query()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatApplications time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatApplications whereAuthIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatApplications whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatApplications whereCountNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatApplications whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatApplications whereDataArrangeText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatApplications whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatApplications whereEdit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatApplications whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatApplications whereInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatApplications whereIsTable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatApplications whereJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatApplications whereKeyword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatApplications whereMcpJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatApplications whereModelsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatApplications whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatApplications wherePic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatApplications wherePrologueList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatApplications wherePrologueText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatApplications whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatApplications whereSourceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatApplications whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatApplications whereTables($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatApplications whereTooltipText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatApplications whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatApplications whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatApplications whereUseLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatApplications withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatApplications withoutTrashed()
 * @mixin \Eloquent
 */
	class ChatApplications extends \Eloquent {}
}

namespace App\Http\Model\Chat{
/**
 * Class ChatHistory.
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property int $chat_application_id 应用id
 * @property string $title 标题
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property mixed|null $top_up
 * @property int $is_show
 * @method static \Illuminate\Database\Eloquent\Builder|ChatHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatHistory notId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatHistory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatHistory time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatHistory whereChatApplicationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatHistory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatHistory whereIsShow($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatHistory whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatHistory whereTopUp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatHistory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatHistory whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatHistory withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatHistory withoutTrashed()
 * @mixin \Eloquent
 */
	class ChatHistory extends \Eloquent {}
}

namespace App\Http\Model\Chat{
/**
 * Class ClientBill.
 *
 * @property int $id
 * @property int $uid 创建用户ID
 * @property string $name 模型名称
 * @property string $pic
 * @property int $provider 供应商类型
 * @property string $models_type 模型类型
 * @property string $is_model 基础模型
 * @property string $url API URL
 * @property string $key API KEY
 * @property string $json 高级设置
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\Chat\ChatApplications> $applications
 * @property-read int|null $applications_count
 * @property-read Admin|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|ChatModels nameLike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatModels newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatModels newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatModels onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatModels query()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatModels time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatModels whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatModels whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatModels whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatModels whereIsModel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatModels whereJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatModels whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatModels whereModelsType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatModels whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatModels wherePic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatModels whereProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatModels whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatModels whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatModels whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatModels withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatModels withoutTrashed()
 * @mixin \Eloquent
 */
	class ChatModels extends \Eloquent {}
}

namespace App\Http\Model\Chat{
/**
 * App\Http\Model\Chat\ChatRecord
 *
 * @property int $id
 * @property string $chat_record_uuid uuid
 * @property int $chat_history_id 记录对话历史主键id
 * @property int $vote_status 赞扬状态
 * @property string $problem_text 提问内容
 * @property string $answer_text 回答内容
 * @property string $sql_text sql内容
 * @property int $prompt_tokens 问题tokens数
 * @property int $completion_tokens 回答tokens数
 * @property int $tokens 总tokens数
 * @property string $details 详情
 * @property int $run_time 运行时间记录
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int $uid 用户id
 * @property int $is_show 是否展示1=展示
 * @property int $chat_applications_id chat_applications_id
 * @method static \Illuminate\Database\Eloquent\Builder|ChatRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatRecord onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatRecord query()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatRecord time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatRecord whereAnswerText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatRecord whereChatApplicationsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatRecord whereChatHistoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatRecord whereChatRecordUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatRecord whereCompletionTokens($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatRecord whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatRecord whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatRecord whereDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatRecord whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatRecord whereIsShow($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatRecord whereProblemText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatRecord wherePromptTokens($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatRecord whereRunTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatRecord whereSqlText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatRecord whereTokens($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatRecord whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatRecord whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatRecord whereVoteStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatRecord withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatRecord withoutTrashed()
 * @mixin \Eloquent
 */
	class ChatRecord extends \Eloquent {}
}

namespace App\Http\Model\Client{
/**
 * 客户订单分类
 * Class ClientContractCategory.
 *
 * @property int $id 自增id
 * @property int $pid 上级ID
 * @property false|string[] $path 路径
 * @property int $level 级别
 * @property int $entid
 * @property int $bill_cate_id 账目分类ID
 * @property false|string[] $bill_cate_path 账目分类路径
 * @property string $name 分类名称
 * @property string $cate_no 分类编号
 * @property int $sort 排序
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Http\Model\Finance\BillCategory|null $billCategory
 * @method static \Illuminate\Database\Eloquent\Builder|ClientContractCategory billCateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientContractCategory id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientContractCategory ltLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientContractCategory nameLike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientContractCategory names($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientContractCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClientContractCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClientContractCategory notid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientContractCategory path($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientContractCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientContractCategory whereBillCateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientContractCategory whereBillCatePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientContractCategory whereCateNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientContractCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientContractCategory whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientContractCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientContractCategory whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientContractCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientContractCategory wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientContractCategory wherePid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientContractCategory whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientContractCategory whereUpdatedAt($value)
 */
	class ClientContractCategory extends \Eloquent {}
}

namespace App\Http\Model\Client{
/**
 * 客户文件列表
 * Class ClientFile.
 *
 * @property int $id 附件ID
 * @property int $eid 客户ID
 * @property int $cid 合同ID
 * @property int $fid 跟进记录ID
 * @property int $vid 发票申请ID
 * @property int $uid 上传用户ID
 * @property string $name 附件名称
 * @property string $real_name 附件原始名称
 * @property string $att_dir 附件路径
 * @property string $thumb_dir 附件压缩路径
 * @property string $att_size 附件大小
 * @property string $att_type 附件类型
 * @property int $entid 分后台ID
 * @property int $up_type 上传方式：1、本地；2、七牛云；3、OSS；4、COS。
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Http\Model\Admin\Admin|null $card
 * @method static \Illuminate\Database\Eloquent\Builder|ClientFile cid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientFile eid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientFile entid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientFile entidName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientFile entids($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientFile fid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientFile id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientFile name($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientFile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClientFile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClientFile query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientFile whereAttDir($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientFile whereAttSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientFile whereAttType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientFile whereCid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientFile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientFile whereEid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientFile whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientFile whereFid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientFile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientFile whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientFile whereRealName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientFile whereThumbDir($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientFile whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientFile whereUpType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientFile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientFile whereVid($value)
 */
	class ClientFile extends \Eloquent {}
}

namespace App\Http\Model\Client{
/**
 * 客户订单发票类目
 * Class ClientInvoiceCategory.
 *
 * @property int $id 自增ID
 * @property int $entid
 * @property string $name 类目名称
 * @property int $sort 排序
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ClientInvoiceCategory nameLike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientInvoiceCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClientInvoiceCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClientInvoiceCategory notid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientInvoiceCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|ClientInvoiceCategory time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientInvoiceCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientInvoiceCategory whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientInvoiceCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientInvoiceCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientInvoiceCategory whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientInvoiceCategory whereUpdatedAt($value)
 */
	class ClientInvoiceCategory extends \Eloquent {}
}

namespace App\Http\Model\Client{
/**
 * 客户发票操作日志.
 *
 * @property int $id
 * @property int $entid 企业ID
 * @property int $invoice_id 发票ID
 * @property int $uid 用户ID
 * @property int $type 操作类型
 * @property mixed $operation 日志内容
 * @property \Illuminate\Support\Carbon|null $created_at 创建时间
 * @property \Illuminate\Support\Carbon|null $updated_at 修改时间
 * @property-read \App\Http\Model\Admin\Admin|null $card
 * @method static \Illuminate\Database\Eloquent\Builder|ClientInvoiceLog cid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientInvoiceLog id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientInvoiceLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClientInvoiceLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClientInvoiceLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientInvoiceLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientInvoiceLog whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientInvoiceLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientInvoiceLog whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientInvoiceLog whereOperation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientInvoiceLog whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientInvoiceLog whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientInvoiceLog whereUpdatedAt($value)
 */
	class ClientInvoiceLog extends \Eloquent {}
}

namespace App\Http\Model\Client{
/**
 * 客户标签关联表
 * Class ClientLabels.
 *
 * @property int $id 自增id
 * @property int $eid 客户ID
 * @property int $label_id 标签ID
 * @property int $link_type 业务类型：1、客户；4、线索；
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ClientLabels eid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientLabels labelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientLabels newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClientLabels newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClientLabels query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientLabels whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientLabels whereEid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientLabels whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientLabels whereLabelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientLabels whereLinkType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientLabels whereUpdatedAt($value)
 */
	class ClientLabels extends \Eloquent {}
}

namespace App\Http\Model\Cloud{
/**
 * 云盘权限.
 *
 * @property int $id 文件权限 id
 * @property int $user_id 用户id
 * @property int $folder_id 文件 id
 * @property string $uid 用户 id
 * @property int $create 目录管理权限
 * @property int $read 查看权限
 * @property int $update 编辑权限
 * @property int $download 下载权限
 * @property int $delete 删除权限
 * @property \Illuminate\Support\Carbon $created_at
 * @method static \Illuminate\Database\Eloquent\Builder|CloudAuth folderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CloudAuth newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CloudAuth newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CloudAuth notUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CloudAuth query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CloudAuth userId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CloudAuth whereCreate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CloudAuth whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CloudAuth whereDelete($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CloudAuth whereDownload($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CloudAuth whereFolderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CloudAuth whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CloudAuth whereRead($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CloudAuth whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CloudAuth whereUpdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CloudAuth whereUserId($value)
 */
	class CloudAuth extends \Eloquent {}
}

namespace App\Http\Model\Cloud{
/**
 * 云端文件.
 *
 * @property int $id 文件 id
 * @property int $user_id 用户id
 * @property int $type 0:文件 1:目录
 * @property string $name 文件名称
 * @property string $path 文件路径
 * @property int $pid 父级文件 id
 * @property string $uid 用户 id
 * @property string|null $file_name 文件真实名称
 * @property string|null $file_ext 文件后缀
 * @property string|null $file_url 文件 url
 * @property string|null $file_sn 文件编号
 * @property string|null $file_size 文件大小
 * @property string|null $file_type 文件类型
 * @property string|null $upload_type
 * @property int|null $entid 企业 id
 * @property int|null $download_count 下载次数
 * @property int|null $version 文件版本
 * @property int|null $is_temp 临时文件
 * @property int|null $is_share 是否共享
 * @property int|null $is_collect 是否收藏
 * @property int|null $is_shortcut 是否常用
 * @property int|null $is_del 是否删除
 * @property int $del_uid 删除用户id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Http\Model\Admin\Admin|null $del_user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\Cloud\CloudShare> $share
 * @property-read int|null $share_count
 * @property-read \App\Http\Model\Admin\Admin|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|CloudFile allId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CloudFile fid($fid)
 * @method static \Illuminate\Database\Eloquent\Builder|CloudFile fileType($val)
 * @method static \Illuminate\Database\Eloquent\Builder|CloudFile id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CloudFile keyword($val)
 * @method static \Illuminate\Database\Eloquent\Builder|CloudFile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CloudFile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CloudFile notPid($pid)
 * @method static \Illuminate\Database\Eloquent\Builder|CloudFile onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|CloudFile path($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CloudFile pid($pid)
 * @method static \Illuminate\Database\Eloquent\Builder|CloudFile query()
 * @method static \Illuminate\Database\Eloquent\Builder|CloudFile time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CloudFile type($type)
 * @method static \Illuminate\Database\Eloquent\Builder|CloudFile uid($uid)
 * @method static \Illuminate\Database\Eloquent\Builder|CloudFile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CloudFile whereDelUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CloudFile whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CloudFile whereDownloadCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CloudFile whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CloudFile whereFileExt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CloudFile whereFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CloudFile whereFileSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CloudFile whereFileSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CloudFile whereFileType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CloudFile whereFileUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CloudFile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CloudFile whereIsCollect($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CloudFile whereIsDel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CloudFile whereIsShare($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CloudFile whereIsShortcut($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CloudFile whereIsTemp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CloudFile whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CloudFile wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CloudFile wherePid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CloudFile whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CloudFile whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CloudFile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CloudFile whereUploadType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CloudFile whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CloudFile whereVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CloudFile withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|CloudFile withoutTrashed()
 */
	class CloudFile extends \Eloquent {}
}

namespace App\Http\Model\Cloud{
/**
 * 云盘文件分享.
 *
 * @property int $id 共享 id
 * @property int $user_id 用户id
 * @property int $folder_id 文件 id
 * @property int $auth_id 权限 id
 * @property string $to_uid 共享用户
 * @property int|null $entid 企业 id/用户 id
 * @property \Illuminate\Support\Carbon $created_at 共享时间
 * @property-read \App\Http\Model\Cloud\CloudAuth|null $auth
 * @property-read \App\Http\Model\Admin\Admin|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|CloudShare newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CloudShare newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CloudShare query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CloudShare whereAuthId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CloudShare whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CloudShare whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CloudShare whereFolderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CloudShare whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CloudShare whereToUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CloudShare whereUserId($value)
 */
	class CloudShare extends \Eloquent {}
}

namespace App\Http\Model\Cloud{
/**
 * App\Http\Model\Cloud\CloudViewHistory
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property string $uid 修改用户
 * @property int $folder_id 文件 id
 * @property string $file_name 文件真实名称
 * @property string $file_url 文件 url
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|CloudViewHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CloudViewHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CloudViewHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CloudViewHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CloudViewHistory whereFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CloudViewHistory whereFileUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CloudViewHistory whereFolderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CloudViewHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CloudViewHistory whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CloudViewHistory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CloudViewHistory whereUserId($value)
 */
	class CloudViewHistory extends \Eloquent {}
}

namespace App\Http\Model\Company{
/**
 * 辅助表
 * Class Assist.
 *
 * @property int $id 辅助表自增id
 * @property int $main_id 主表ID
 * @property int $aux_id 副表ID
 * @property string $type 类型,可用其他表名区分
 * @property string $other 其他数据
 * @property \Illuminate\Support\Carbon|null $created_at 添加时间
 * @property-read \App\Http\Model\Admin\Admin|null $frame
 * @property-read \App\Http\Model\Frame\Frame|null $hasFrame
 * @property-read \App\Http\Model\Admin\Admin|null $users
 * @method static \Illuminate\Database\Eloquent\Builder|Assist frameIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Assist mainId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Assist newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Assist newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Assist query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Assist whereAuxId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Assist whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Assist whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Assist whereMainId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Assist whereOther($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Assist whereType($value)
 */
	class Assist extends \Eloquent {}
}

namespace App\Http\Model\Company{
/**
 * 企业管理.
 *
 * @property int $id 企业表自增id
 * @property string $logo 公司logo
 * @property string $title 管理后台标题
 * @property string $enterprise_name 公司名称
 * @property string $short_name 公司简称
 * @property string $enterprise_number 公司编号
 * @property string $enterprise_name_en 公司名称英文
 * @property string $lead 法人代表
 * @property string $telephone 电话号
 * @property string $phone 手机号
 * @property string $province 所在省
 * @property string $city 所在城市
 * @property string $area 所在区
 * @property string $address 详细地址
 * @property string $synopsis 简介
 * @property string $fax 传真
 * @property string $business_license 营业执照
 * @property string $remark 备注
 * @property string $disable_remark 禁用备注
 * @property string $introduction 简介
 * @property mixed $other 其他
 * @property string $uid 所属用户
 * @property int $scale 公司规模
 * @property int $type 企业类型
 * @property int $level 企业等级
 * @property int $sort 排序
 * @property int $verify 0=审核,1=审核通过,-1=不通过
 * @property int $remind 提醒状态：0、未读；1、已读；
 * @property string $uniqued 企业唯一值
 * @property int $init_data 是否已加载默认数据
 * @property int $disk_size 已使用云盘空间
 * @property int $status 0=禁用,1=正常,2=待缴费,3=已过期
 * @property \Illuminate\Support\Carbon|null $delete 是否删除
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Http\Model\Frame\Frame|null $frame
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\Frame\Frame> $frames
 * @property-read int|null $frames_count
 * @property-read \App\Http\Model\Admin\Admin|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Company enterpriseName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company level($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company name($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Company newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Company onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Company query()
 * @method static \Illuminate\Database\Eloquent\Builder|Company scale($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company status($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company type($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company uid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company uniqued($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company verifys($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereArea($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereBusinessLicense($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereDelete($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereDisableRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereDiskSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereEnterpriseName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereEnterpriseNameEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereEnterpriseNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereFax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereInitData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereIntroduction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereLead($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereOther($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereRemind($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereScale($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereShortName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereSynopsis($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereTelephone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereUniqued($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereVerify($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Company withoutTrashed()
 */
	class Company extends \Eloquent {}
}

namespace App\Http\Model\Company{
/**
 * 企业邀请用户加入申请表.
 *
 * @property int $id 自增id
 * @property int $entid 发送人或者企业ID
 * @property string $send_uid
 * @property string $uid 送达人id或者企业
 * @property int $frame_id
 * @property int $status -1=待处理,0=拒绝;1=同意
 * @property int $verify 审核状态：0、待审核；1、已通过；-1、拒绝；
 * @property string|null $perfect_key 邀请完善信息记录关联
 * @property \Illuminate\Support\Carbon|null $created_at 申请时间
 * @property-read \App\Http\Model\Company\Company|null $enterprise
 * @property-read \App\Http\Model\User\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyApply entid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyApply entids($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyApply id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyApply newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyApply newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyApply query()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyApply status($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyApply statusApply($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyApply verify($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyApply whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyApply whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyApply whereFrameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyApply whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyApply wherePerfectKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyApply whereSendUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyApply whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyApply whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyApply whereVerify($value)
 */
	class CompanyApply extends \Eloquent {}
}

namespace App\Http\Model\Company{
/**
 * Class CompanyConfig.
 *
 * @property int $id 自增id
 * @property string $key 配置字段
 * @property string $key_name 配置名称
 * @property string $type 类型(文本框,单选按钮...)
 * @property string $input_type 表单类型
 * @property string $category 配置分类:assess、绩效考核
 * @property string $parameter 规则 单选框和多选框
 * @property int $upload_type 上传文件格式1单图2多图3文件
 * @property string $required 规则
 * @property int $width 多行文本框的宽度
 * @property int $high 多行文框的高度
 * @property mixed $value 默认值
 * @property string $desc 配置简介
 * @property int $sort 排序
 * @property int $entid
 * @property int $is_show 是否隐藏
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyConfig category($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyConfig entid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyConfig key($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyConfig newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyConfig newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyConfig query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyConfig whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyConfig whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyConfig whereDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyConfig whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyConfig whereHigh($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyConfig whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyConfig whereInputType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyConfig whereIsShow($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyConfig whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyConfig whereKeyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyConfig whereParameter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyConfig whereRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyConfig whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyConfig whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyConfig whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyConfig whereUploadType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyConfig whereValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyConfig whereWidth($value)
 */
	class CompanyConfig extends \Eloquent {}
}

namespace App\Http\Model\Company{
/**
 * 企业邀请.
 *
 * @property int $id 自增id
 * @property int $entid 企业ID
 * @property string $send_uid 生成邀请码用户uuid
 * @property int $frame_id 组织架构ID
 * @property int $is_verify 是否需要企业审核：1、是；0、否；
 * @property string $uniqued 链接唯一值
 * @property string|null $perfect_key 邀请完善信息记录标识
 * @property mixed|null $fail_time 失效时间
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyInvite entid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyInvite frameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyInvite newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyInvite newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyInvite query()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyInvite status($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyInvite whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyInvite whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyInvite whereFailTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyInvite whereFrameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyInvite whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyInvite whereIsVerify($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyInvite wherePerfectKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyInvite whereSendUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyInvite whereUniqued($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyInvite whereUpdatedAt($value)
 */
	class CompanyInvite extends \Eloquent {}
}

namespace App\Http\Model\Company{
/**
 * 企业日志.
 *
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyLog time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyLog userName($value)
 */
	class CompanyLog extends \Eloquent {}
}

namespace App\Http\Model\Company{
/**
 * 日报汇报人
 * Class DailyReportMember.
 *
 * @property int $id 自增ID
 * @property int $daily_id 汇报ID
 * @property int $member 汇报人ID(user_enterprise自增ID)
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|DailyReportMember member($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DailyReportMember newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DailyReportMember newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DailyReportMember query()
 * @method static \Illuminate\Database\Eloquent\Builder|DailyReportMember time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DailyReportMember whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DailyReportMember whereDailyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DailyReportMember whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DailyReportMember whereMember($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DailyReportMember whereUpdatedAt($value)
 */
	class DailyReportMember extends \Eloquent {}
}

namespace App\Http\Model\Company{
/**
 * 员工培训.
 *
 * @property int $id 自增ID
 * @property string $type 培训类型
 * @property string $content 数据详情
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|EmployeeTrain newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EmployeeTrain newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EmployeeTrain query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmployeeTrain whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmployeeTrain whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmployeeTrain whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmployeeTrain whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmployeeTrain whereUpdatedAt($value)
 */
	class EmployeeTrain extends \Eloquent {}
}

namespace App\Http\Model\Company{
/**
 * 评估表.
 *
 * @property int $id 自增ID
 * @property string $name 评估表名称
 * @property int $uid 业务员ID
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Http\Model\Admin\Admin|null $card
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\Position\Job> $positions
 * @property-read int|null $positions_count
 * @method static \Illuminate\Database\Eloquent\Builder|HayGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HayGroup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HayGroup onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|HayGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HayGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HayGroup whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HayGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HayGroup whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HayGroup whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HayGroup whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HayGroup withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|HayGroup withoutTrashed()
 */
	class HayGroup extends \Eloquent {}
}

namespace App\Http\Model\Company{
/**
 * 评估表数据.
 *
 * @property int $id 自增ID
 * @property int $group_id 评估表ID
 * @property int $uid 业务员ID
 * @property int $col1 职位
 * @property string[] $col2 专业知识水平
 * @property string[] $col3 管理诀窍
 * @property string[] $col4 人际关系技巧
 * @property string[] $col5 评分
 * @property string[] $col6 思维环境
 * @property string[] $col7 思维难度
 * @property string[] $col8 评分
 * @property string[] $col9 行动自由度
 * @property string[] $col10 职务责任
 * @property string[] $col11 职务影响结果
 * @property string[] $col12 评分
 * @property string $col13 α
 * @property string $col14 β
 * @property string $col15 岗位分数
 * @property string $col16 岗位系数
 * @property int $sort 排序
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Http\Model\Admin\Admin|null $card
 * @property-read \App\Http\Model\Position\Job|null $position
 * @method static \Illuminate\Database\Eloquent\Builder|HayGroupData newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HayGroupData newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HayGroupData query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HayGroupData whereCol1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HayGroupData whereCol10($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HayGroupData whereCol11($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HayGroupData whereCol12($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HayGroupData whereCol13($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HayGroupData whereCol14($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HayGroupData whereCol15($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HayGroupData whereCol16($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HayGroupData whereCol2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HayGroupData whereCol3($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HayGroupData whereCol4($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HayGroupData whereCol5($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HayGroupData whereCol6($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HayGroupData whereCol7($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HayGroupData whereCol8($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HayGroupData whereCol9($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HayGroupData whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HayGroupData whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HayGroupData whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HayGroupData whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HayGroupData whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HayGroupData whereUpdatedAt($value)
 */
	class HayGroupData extends \Eloquent {}
}

namespace App\Http\Model\Company{
/**
 * 晋升表.
 *
 * @property int $id 自增ID
 * @property string $name 晋升名称
 * @property int $sort 排序
 * @property int $status 1、展示; 0、关闭
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Promotion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Promotion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Promotion onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Promotion query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promotion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promotion whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promotion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promotion whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promotion whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promotion whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promotion whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promotion withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Promotion withoutTrashed()
 */
	class Promotion extends \Eloquent {}
}

namespace App\Http\Model\Company{
/**
 * 晋升数据.
 *
 * @property int $id 自增ID
 * @property int $promotion_id 晋升表ID
 * @property string $rank 职级
 * @property string $position 职位
 * @property string $total 合计
 * @property string $benefit 效益工资
 * @property string $standard 标准
 * @property int $sort 排序
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|PromotionData newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PromotionData newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PromotionData onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|PromotionData query()
 * @method static \Illuminate\Database\Eloquent\Builder|PromotionData status($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromotionData whereBenefit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromotionData whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromotionData whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromotionData whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromotionData wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromotionData wherePromotionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromotionData whereRank($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromotionData whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromotionData whereStandard($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromotionData whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromotionData whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromotionData withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|PromotionData withoutTrashed()
 */
	class PromotionData extends \Eloquent {}
}

namespace App\Http\Model\Company{
/**
 * 考核记录
 * Class UserAssess.
 *
 * @property int $id 自增id
 * @property int $entid 企业ID
 * @property string $name 名称
 * @property int $period 周期:1=周;2=月;3=年
 * @property int $planid
 * @property int $frame_id 组织架构ID
 * @property int $number 考核批次ID
 * @property int $check_uid 考核用户信息表ID
 * @property int $test_uid 被考核用户信息表ID
 * @property mixed|null $start_time 考核开始时间
 * @property mixed|null $make_time 目标制定时间结束时间
 * @property int $make_status 目标制定状态：0、未制定；1、已启用；2、草稿。
 * @property mixed|null $end_time 考核结束时间
 * @property int $test_status 自评状态：0、未评价；1、已评价；2、草稿；
 * @property mixed|null $check_end 上级评价结束时间
 * @property int $check_status 上级评价状态：0、未评价；1、已评价；2、草稿。
 * @property mixed|null $verify_time 审核结束时间
 * @property int $verify_status 审核状态：0、未审核；1、已审核；
 * @property string $score 考核得分
 * @property string $total 最高分
 * @property int $grade 考核等级
 * @property int $status 考核状态：0、目标制定；1、自评期；2、上级评价；3、审核期；4、结束；
 * @property int $types 评分方式：0、加权评分；1、加和评分
 * @property int $intact 完整性：1、是；0、否
 * @property int $is_show 是否启用：0、未启用；1、已启用；
 * @property string $self_reply 自评
 * @property string $reply 上级评价
 * @property string $hide_reply 上级评价(仅上级可见)
 * @property \Illuminate\Support\Carbon|null $delete 删除时间
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Http\Model\Admin\Admin|null $check
 * @property-read \App\Http\Model\Frame\Frame|null $frame
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\Assess\AssessTarget> $info
 * @property-read int|null $info_count
 * @property-read \App\Http\Model\Assess\AssessPlan|null $plan
 * @property-read \App\Http\Model\Assess\AssessPlan|null $planInfo
 * @property-read \App\Http\Model\Admin\Admin|null $test
 * @property-read \App\Http\Model\Admin\Admin|null $userEnt
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssess checkStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssess checkUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssess createdAtYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssess endTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssess entid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssess execTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssess frameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssess grade($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssess newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssess newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssess notStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssess number($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssess onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssess planid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssess query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssess startTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssess status($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssess testUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssess time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssess whereCheckEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssess whereCheckStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssess whereCheckUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssess whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssess whereDelete($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssess whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssess whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssess whereFrameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssess whereGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssess whereHideReply($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssess whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssess whereIntact($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssess whereIsShow($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssess whereMakeStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssess whereMakeTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssess whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssess whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssess wherePeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssess wherePlanid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssess whereReply($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssess whereScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssess whereSelfReply($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssess whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssess whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssess whereTestStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssess whereTestUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssess whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssess whereTypes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssess whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssess whereVerifyStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssess whereVerifyTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssess withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssess withoutTrashed()
 */
	class UserAssess extends \Eloquent {}
}

namespace App\Http\Model\Company{
/**
 * 考核评分记录
 * Class UserAssess.
 *
 * @property int $id 自增id
 * @property int $entid 企业ID
 * @property int $assessid 考核记录ID
 * @property int $userid 操作人ID
 * @property int $check_uid 考核人ID
 * @property int $test_uid 被考核人ID
 * @property string $score 考核得分
 * @property string $total 最高分
 * @property int|null $grade 考核等级
 * @property string $info 变更说明
 * @property string $mark 备注信息
 * @property int $types 操作类型：0、评分；1、删除绩效；
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Http\Model\Admin\Admin|null $card
 * @property-read \App\Http\Model\Admin\Admin|null $check
 * @property-read \App\Http\Model\Admin\Admin|null $test
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssessScore assessid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssessScore newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssessScore newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssessScore query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssessScore time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssessScore types($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssessScore userid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssessScore whereAssessid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssessScore whereCheckUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssessScore whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssessScore whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssessScore whereGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssessScore whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssessScore whereInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssessScore whereMark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssessScore whereScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssessScore whereTestUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssessScore whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssessScore whereTypes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssessScore whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAssessScore whereUserid($value)
 */
	class UserAssessScore extends \Eloquent {}
}

namespace App\Http\Model\Company{
/**
 * 职位变动表
 * Class UserChange.
 *
 * @property int $id 自增ID
 * @property int $uid 人员ID
 * @property int $entid 企业ID
 * @property int $card_id 企业用户名片ID
 * @property int $types 变动类型：0、入职；1、转正；2、调岗；3、离职；
 * @property string|null $date 变动时间
 * @property int $new_frame 新部门ID
 * @property int $old_frame 原部门ID
 * @property int $new_position 新职位ID
 * @property int $old_position 原职位ID
 * @property string $info 原因说明
 * @property string $mark 备注信息
 * @property int $link_id 关联申请单ID
 * @property int $user_id 转移人员ID
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Http\Model\Admin\Admin|null $card
 * @property-read \App\Http\Model\Frame\Frame|null $nFrame
 * @property-read \App\Http\Model\Position\Job|null $nPosition
 * @property-read \App\Http\Model\Frame\Frame|null $oFrame
 * @property-read \App\Http\Model\Position\Job|null $oPosition
 * @method static \Illuminate\Database\Eloquent\Builder|UserChange newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserChange newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserChange query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserChange whereCardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserChange whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserChange whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserChange whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserChange whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserChange whereInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserChange whereLinkId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserChange whereMark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserChange whereNewFrame($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserChange whereNewPosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserChange whereOldFrame($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserChange whereOldPosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserChange whereTypes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserChange whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserChange whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserChange whereUserId($value)
 */
	class UserChange extends \Eloquent {}
}

namespace App\Http\Model\Company{
/**
 * 用户日报回复记录
 * Class UserDaily.
 *
 * @property int $id 自增id
 * @property int $pid
 * @property int $daily_id
 * @property string $uid
 * @property string $content 回复内容
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property mixed|null $deleted_at
 * @property-read \App\Http\Model\Admin\Admin|null $card
 * @property-read UserDailyReply|null $paentUser
 * @method static \Illuminate\Database\Eloquent\Builder|UserDailyReply ids($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDailyReply newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserDailyReply newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserDailyReply query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDailyReply whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDailyReply whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDailyReply whereDailyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDailyReply whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDailyReply whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDailyReply wherePid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDailyReply whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDailyReply whereUpdatedAt($value)
 */
	class UserDailyReply extends \Eloquent {}
}

namespace App\Http\Model\Company{
/**
 * 教育经历
 * Class UserEducation.
 *
 * @property int $id 自增id
 * @property int $user_id 用户id
 * @property int $card_id 企业用户信息(enterprise_user_card)ID
 * @property \Illuminate\Support\Carbon|null $start_time 开始时间
 * @property \Illuminate\Support\Carbon|null $end_time 结束时间
 * @property string $school_name 学校名称
 * @property string $major 所学专业
 * @property string $education 学历
 * @property string $academic 学位
 * @property string $remark 备注
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|UserEducation id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEducation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserEducation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserEducation query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEducation whereAcademic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEducation whereCardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEducation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEducation whereEducation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEducation whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEducation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEducation whereMajor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEducation whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEducation whereSchoolName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEducation whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEducation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEducation whereUserId($value)
 */
	class UserEducation extends \Eloquent {}
}

namespace App\Http\Model\Company{
/**
 * 工作分析
 * Class UserJobAnalysis.
 *
 * @property int $id 自增ID
 * @property int $entid 企业ID
 * @property int $uid 用户ID
 * @property string $data 分析内容
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|UserJobAnalysis newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserJobAnalysis newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserJobAnalysis query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserJobAnalysis whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserJobAnalysis whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserJobAnalysis whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserJobAnalysis whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserJobAnalysis whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserJobAnalysis whereUpdatedAt($value)
 */
	class UserJobAnalysis extends \Eloquent {}
}

namespace App\Http\Model\Company{
/**
 * 任职经历
 * Class UserPosition.
 *
 * @property int $id 自增id
 * @property int $card_id 企业用户信息(enterprise_user_card)ID
 * @property \Illuminate\Support\Carbon|null $start_time 开始时间
 * @property \Illuminate\Support\Carbon|null $end_time 结束时间
 * @property string $position 职位
 * @property string $department 部门
 * @property int $is_admin 身份0=普通员工;1=主管
 * @property int $status 任职状态0=离职;1=任职
 * @property string $remark 备注
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|UserPosition id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPosition newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserPosition newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserPosition query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPosition whereCardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPosition whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPosition whereDepartment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPosition whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPosition whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPosition whereIsAdmin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPosition wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPosition whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPosition whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPosition whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPosition whereUpdatedAt($value)
 */
	class UserPosition extends \Eloquent {}
}

namespace App\Http\Model\Company{
/**
 * 调薪记录
 * Class UserSalary.
 *
 * @property int $id 自增ID
 * @property int $entid 企业ID
 * @property int $card_id 企业用户名片ID
 * @property string $total 变更内容
 * @property string|null $take_date 生效时间
 * @property string $content 变更内容
 * @property string $mark 变更原因
 * @property int $link_id 关联申请单ID
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|UserSalary cardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSalary entid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSalary newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserSalary newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserSalary query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSalary whereCardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSalary whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSalary whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSalary whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSalary whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSalary whereLinkId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSalary whereMark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSalary whereTakeDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSalary whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSalary whereUpdatedAt($value)
 */
	class UserSalary extends \Eloquent {}
}

namespace App\Http\Model\Company{
/**
 * 工作经历
 * Class UserWork.
 *
 * @property int $id 自增id
 * @property int $user_id 用户id
 * @property int $card_id 企业用户信息(enterprise_user_card)ID
 * @property \Illuminate\Support\Carbon|null $start_time 开始时间
 * @property \Illuminate\Support\Carbon|null $end_time 结束时间
 * @property string $company 所在公司
 * @property string $position 职位
 * @property string $describe 工作描述
 * @property string $quit_reason 离职原因
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|UserWork id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserWork newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserWork newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserWork query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserWork whereCardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserWork whereCompany($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserWork whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserWork whereDescribe($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserWork whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserWork whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserWork wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserWork whereQuitReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserWork whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserWork whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserWork whereUserId($value)
 */
	class UserWork extends \Eloquent {}
}

namespace App\Http\Model\Config{
/**
 * App\Http\Model\Config\Agreement
 *
 * @property int $id 自增ID
 * @property string $ident 协议标识
 * @property string $title 协议标题
 * @property string $content 协议内容
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Agreement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Agreement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Agreement query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agreement whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agreement whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agreement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agreement whereIdent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agreement whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agreement whereUpdatedAt($value)
 */
	class Agreement extends \Eloquent {}
}

namespace App\Http\Model\Config{
/**
 * 省市区
 * Class SystemBackup.
 *
 * @property int $id 自增id
 * @property int $entid 企业ID
 * @property string $path 文件路径
 * @property string|null $uid 创建用户ID
 * @property string|null $version 版本号
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Backup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Backup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Backup query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Backup version($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Backup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Backup whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Backup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Backup wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Backup whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Backup whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Backup whereVersion($value)
 */
	class Backup extends \Eloquent {}
}

namespace App\Http\Model\Config{
/**
 * 省市区
 * Class City.
 *
 * @property int $id 自增id
 * @property int $city_id 城市ID
 * @property int $level 省市级别
 * @property int $parent_id 父级id
 * @property string $area_code 区号
 * @property string $name 名称
 * @property string $merger_name 合并名称
 * @property string $lng 经度
 * @property string $lat 纬度
 * @property int $is_show 是否展示
 * @property-read \Illuminate\Database\Eloquent\Collection<int, City> $children
 * @property-read int|null $children_count
 * @property-read string $hierarchy_name
 * @property-read City|null $parent
 * @method static \Illuminate\Database\Eloquent\Builder|City cityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City nameLike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|City newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|City query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereAreaCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereIsShow($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereLat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereLng($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereMergerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereParentId($value)
 */
	class City extends \Eloquent {}
}

namespace App\Http\Model\Config{
/**
 * Class Config.
 *
 * @property int $id 自增id
 * @property string $category 配置分类
 * @property string $key 配置字段
 * @property string $key_name 配置名称
 * @property string $type 类型(文本框,单选按钮...)
 * @property string $input_type 表单类型
 * @property int $cate_id 配置分类id
 * @property false|string[] $path 配置分类路径
 * @property string $parameter 规则 单选框和多选框
 * @property int $upload_type 上传文件格式1单图2多图3文件
 * @property string $required 规则
 * @property int $width 多行文本框的宽度
 * @property int $high 多行文框的高度
 * @property array|int|mixed $value 默认值
 * @property string $desc 配置简介
 * @property int $sort 排序
 * @property int $entid 0=总后台,1=分后台
 * @property string $ent_key 分后台
 * @property int $is_show 是否隐藏
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-write mixed $pid
 * @method static \Illuminate\Database\Eloquent\Builder|Config cateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Config key($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Config newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Config newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Config query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Config whereCateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Config whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Config whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Config whereDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Config whereEntKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Config whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Config whereHigh($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Config whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Config whereInputType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Config whereIsShow($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Config whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Config whereKeyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Config whereParameter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Config wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Config whereRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Config whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Config whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Config whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Config whereUploadType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Config whereValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Config whereWidth($value)
 */
	class Config extends \Eloquent {}
}

namespace App\Http\Model\Config{
/**
 * 字典数据.
 *
 * @property int $id
 * @property string|null $name 数据名称
 * @property string|null $value 数据值
 * @property string $pid 上级数据值
 * @property int $type_id 字典类型ID
 * @property string|null $type_name 字典类型名称
 * @property int $level 数据层级
 * @property string $color 标识颜色
 * @property int $sort 排序
 * @property int $status 状态：1、开启；0、关闭；
 * @property int $is_default 是否默认：1、是；0、否；
 * @property string|null $mark 备注信息
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property mixed|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|DictData dictValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DictData id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DictData isCityShow($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DictData level($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DictData levelLt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DictData nameEq($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DictData nameLike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DictData newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DictData newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DictData notId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DictData notValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DictData pid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DictData pidGt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DictData query()
 * @method static \Illuminate\Database\Eloquent\Builder|DictData status($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DictData typeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DictData typeName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DictData values($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DictData whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DictData whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DictData whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DictData whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DictData whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DictData whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DictData whereMark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DictData whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DictData wherePid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DictData whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DictData whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DictData whereTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DictData whereTypeName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DictData whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DictData whereValue($value)
 */
	class DictData extends \Eloquent {}
}

namespace App\Http\Model\Config{
/**
 * 字典类型.
 *
 * @property int $id
 * @property string|null $name 字典名称
 * @property string|null $ident 字典标识
 * @property string $link_type 关联业务
 * @property int $level 数据最大层级
 * @property int $status 状态：1、开启；0、关闭；
 * @property int $is_default 是否默认：1、是；0、否；
 * @property string|null $mark 备注信息
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $crud_id
 * @property int $field_id
 * @method static \Illuminate\Database\Eloquent\Builder|DictType cateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DictType crudId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DictType formValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DictType id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DictType ident($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DictType level($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DictType linkType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DictType nameLike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DictType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DictType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DictType notId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DictType query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DictType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DictType whereCrudId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DictType whereFieldId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DictType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DictType whereIdent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DictType whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DictType whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DictType whereLinkType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DictType whereMark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DictType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DictType whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DictType whereUpdatedAt($value)
 */
	class DictType extends \Eloquent {}
}

namespace App\Http\Model\Config{
/**
 * 自定义表单分组.
 *
 * @property int $id
 * @property string $title 分组名称
 * @property int $sort 分组排序
 * @property int $types 分组类型：1、客户；2、合同；3、联系人；
 * @property int $status 状态：1、显示；0、隐藏；
 * @property string $ident 标识
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\Config\FormData> $data
 * @property-read int|null $data_count
 * @method static \Illuminate\Database\Eloquent\Builder|FormCate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FormCate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FormCate onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|FormCate query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormCate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormCate whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormCate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormCate whereIdent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormCate whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormCate whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormCate whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormCate whereTypes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormCate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormCate withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|FormCate withoutTrashed()
 */
	class FormCate extends \Eloquent {}
}

namespace App\Http\Model\Config{
/**
 * 自定义表单内容.
 *
 * @property int $id
 * @property string $key 字段唯一值
 * @property string $key_name 字段名称
 * @property string $type 类型(文本框,单选按钮...)
 * @property string $input_type 表单类型
 * @property int $cate_id 配置分类id
 * @property string $param 规则 单选框和多选框
 * @property int $decimal_place 数字字段小数位数
 * @property int $upload_type 上传文件格式1单图2多图3文件
 * @property int $required 是否必填：1、必填；0、非必填；
 * @property string $placeholder 提示文字
 * @property int $max 最大边界值
 * @property int $min 最小边界值
 * @property string $dict_ident 字典标识
 * @property mixed|null $value 默认值
 * @property int $uniqued 是否校验唯一
 * @property string $desc 配置简介
 * @property int $link_type 关联类型：1、客户；2、合同；3、发票；4、线索；5、商机；6、产品；
 * @property string $link_field 关联字段
 * @property int $sort 排序
 * @property int $status 状态：1、显示；2、隐藏；
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\Config\DictData> $dictData
 * @property-read int|null $dict_data_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\Config\DictData> $options
 * @property-read int|null $options_count
 * @method static \Illuminate\Database\Eloquent\Builder|FormData cateExists($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormData cateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormData id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormData key($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormData linkField($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormData newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FormData newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FormData onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|FormData query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormData whereCateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormData whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormData whereDecimalPlace($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormData whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormData whereDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormData whereDictIdent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormData whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormData whereInputType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormData whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormData whereKeyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormData whereLinkField($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormData whereLinkType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormData whereMax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormData whereMin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormData whereParam($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormData wherePlaceholder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormData whereRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormData whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormData whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormData whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormData whereUniqued($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormData whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormData whereUploadType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormData whereValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormData withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|FormData withoutTrashed()
 */
	class FormData extends \Eloquent {}
}

namespace App\Http\Model\Config{
/**
 * 组合数据
 * Class Group.
 *
 * @property int $id 自增id
 * @property int $cate_id 分类id
 * @property string $group_key 数据字段英文名
 * @property string $group_name 数据字段中文名称
 * @property string $group_info 数据字段提示
 * @property false|string[] $fields 数据组字段以及类型（json数据）
 * @property int $entid 商家ID：0、总平台
 * @property int $sort 排序
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Group entid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group groupKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Group newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Group query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereCateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereFields($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereGroupInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereGroupKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereGroupName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereUpdatedAt($value)
 */
	class Group extends \Eloquent {}
}

namespace App\Http\Model\Config{
/**
 * Class GroupData.
 *
 * @property int $id 组合数据自增id
 * @property int $group_id 组合数据数组ID(关联system_group表id)
 * @property mixed $value 数据组对应的数据值（json数据）
 * @property int $sort 数据排序
 * @property int $status 状态 1=开启,0=关闭
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|GroupData groupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupData id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupData newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GroupData newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GroupData query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupData whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupData whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupData whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupData whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupData whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupData whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupData whereValue($value)
 */
	class GroupData extends \Eloquent {}
}

namespace App\Http\Model\Config{
/**
 * 财务支付方式.
 *
 * @property int $id 自增id
 * @property string $name 支付方式名称
 * @property string $ident 支付方式标识
 * @property string $info 简介
 * @property int $status 是否可用：1、是；0、否；
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Paytype nameLike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Paytype newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Paytype newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Paytype query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Paytype whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Paytype whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Paytype whereIdent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Paytype whereInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Paytype whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Paytype whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Paytype whereUpdatedAt($value)
 */
	class Paytype extends \Eloquent {}
}

namespace App\Http\Model\Config{
/**
 * 业务自定义数据.
 *
 * @property int $id
 * @property int $uid 用户ID
 * @property string $custom_type 类型
 * @property mixed|null $field_list 自定义数据
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|SalesmanCustom customType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesmanCustom id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesmanCustom newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SalesmanCustom newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SalesmanCustom query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesmanCustom whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesmanCustom whereCustomType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesmanCustom whereFieldList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesmanCustom whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesmanCustom whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesmanCustom whereUpdatedAt($value)
 */
	class SalesmanCustom extends \Eloquent {}
}

namespace App\Http\Model\Config{
/**
 * 云存储
 * Class SystemStorage.
 *
 * @property int $id 自增ID
 * @property string $access_key access_key
 * @property int $type 1=本地存储,2=七牛,3=oss,4=cos
 * @property string $name 空间名
 * @property string $region 地域
 * @property string $acl 权限
 * @property string $domain 空间域名
 * @property string $cdn CDN加速域名
 * @property string $cname CNAME值
 * @property int $is_ssl 0=http,1=https
 * @property int $status 状态
 * @property int $is_delete 是否删除
 * @property int $add_time 添加事件
 * @property int $update_time 更新事件
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|SystemStorage nameAttr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemStorage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemStorage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemStorage query()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemStorage statusAttr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemStorage typeAttr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemStorage whereAccessKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemStorage whereAcl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemStorage whereAddTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemStorage whereCdn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemStorage whereCname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemStorage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemStorage whereDomain($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemStorage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemStorage whereIsDelete($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemStorage whereIsSsl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemStorage whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemStorage whereRegion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemStorage whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemStorage whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemStorage whereUpdateTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemStorage whereUpdatedAt($value)
 */
	class SystemStorage extends \Eloquent {}
}

namespace App\Http\Model\Crud{
/**
 * Class SystemCrud.
 *
 * @email 136327134@qq.com
 * @date 2024/2/26
 * @property int $id 自增id
 * @property string $table_name 表中文名
 * @property string $table_name_en 表英文名
 * @property array $cate_ids 分类IDS
 * @property string $info 说明
 * @property int $crud_id 主表CRUD_ID；为空为主表
 * @property int $user_id 创建者ID
 * @property mixed $form_fields 当前form选择中的字段集合
 * @property int $list_type 0=默认；1=树形
 * @property int $is_update_form 是否允许修改表单
 * @property int $is_update_table 是否允许修改表格
 * @property int $show_log 是否展示日志
 * @property string $comment_title 评论标题
 * @property int $show_comment 是否展示评论
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int $is_form_table 是否存在表格
 * @property mixed $table_field 表格提交字段和展示字段
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\Crud\SystemCrudApprove> $approve
 * @property-read int|null $approve_count
 * @property-read SystemCrud|null $child
 * @property-read \Illuminate\Database\Eloquent\Collection<int, SystemCrud> $children
 * @property-read int|null $children_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\Crud\SystemCrudEvent> $event
 * @property-read int|null $event_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\Crud\SystemCrudField> $field
 * @property-read int|null $field_count
 * @property-read \App\Http\Model\Crud\SystemCrudForm|null $form
 * @property-read \App\Http\Model\System\Menus|null $menu
 * @property-read \App\Http\Model\Crud\SystemCrudRole|null $role
 * @property-read \App\Http\Model\Crud\SystemCrudTable|null $table
 * @property-read \App\Http\Model\Admin\Admin|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrud newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrud newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrud notName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrud onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrud query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrud whereCateIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrud whereCommentTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrud whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrud whereCrudId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrud whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrud whereFormFields($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrud whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrud whereInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrud whereIsFormTable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrud whereIsUpdateForm($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrud whereIsUpdateTable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrud whereListType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrud whereShowComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrud whereShowLog($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrud whereTableField($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrud whereTableName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrud whereTableNameEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrud whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrud whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrud withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrud withoutTrashed()
 */
	class SystemCrud extends \Eloquent {}
}

namespace App\Http\Model\Crud{
/**
 * Class SystemCrudApprove.
 *
 * @email 136327134@qq.com
 * @date 2024/2/28
 * @property int $id 自增id
 * @property int $types 审批类型
 * @property int $crud_id 关联CRUD_ID
 * @property int $user_id 创建用户ID
 * @property string $name 审批名称
 * @property string $icon 审批图标
 * @property string $color 审批图标颜色
 * @property string $info 审批说明
 * @property int $status 状态：0、关闭；1、开启；
 * @property int $sort
 * @property int $is_transfer
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Http\Model\Admin\Admin|null $card
 * @property-read \App\Http\Model\Crud\SystemCrud|null $crud
 * @property-read \App\Http\Model\Crud\SystemCrudApproveProcess|null $process
 * @property-read \App\Http\Model\Crud\SystemCrudApproveRule|null $rule
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApprove cateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApprove id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApprove nameLike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApprove newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApprove newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApprove notid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApprove onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApprove query()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApprove status($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApprove whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApprove whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApprove whereCrudId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApprove whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApprove whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApprove whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApprove whereInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApprove whereIsTransfer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApprove whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApprove whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApprove whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApprove whereTypes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApprove whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApprove whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApprove withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApprove withoutTrashed()
 */
	class SystemCrudApprove extends \Eloquent {}
}

namespace App\Http\Model\Crud{
/**
 * App\Http\Model\Crud\SystemCrudApproveProcess
 *
 * @property int $id 自增id
 * @property int $user_id 创建用户名片ID
 * @property int $approve_id 关联流程ID
 * @property int $level 流程级别
 * @property int $groups 分组ID
 * @property string $name 节点名称
 * @property int $types 节点类型：0、申请人；1、审批人；2、抄送人；3、条件；4、路由；
 * @property string $uniqued 节点唯一值
 * @property int $settype 审核人类型：1、指定成员；2、指定部门主管；7、连续多部门；5、申请人自己；4、申请人自选；(0、无此条件)
 * @property int $director_order 指定层级顺序：0、从上至下；1、从下至上；(-1、无此条件)
 * @property int $director_level 指定主管层级/指定终点层级：1-10；(0、无此条件)
 * @property int $no_hander 当前部门无负责人时：1、上级部门负责人审批；2、为空时跳过；(0、无此条件)
 * @property array|mixed $dep_head 指定部门负责人
 * @property int $self_select 是否允许自选抄送人
 * @property int $select_range 可选范围：1、不限范围；2、指定成员；(0、无此条件)
 * @property array|mixed $user_list 指定的成员列表
 * @property int $select_mode 选人方式：1、单选；2、多选；(0、无此条件)
 * @property int $examine_mode 多人审批方式：1、或签；2、会签；3、依次审批；(0、无此条件)
 * @property int $priority 条件优先级
 * @property string $parent 节点父级唯一值
 * @property int $is_child 是否存在子节点
 * @property int $is_condition 是否存在条件
 * @property array|mixed $condition_list 条件详情
 * @property int $is_initial 是否为初始数据
 * @property array|mixed $info 数据详情
 * @property int $pass_ratio 通过比例(%) 0=关闭（使用原逻辑）
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApproveProcess approveId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApproveProcess groups($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApproveProcess id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApproveProcess isInitial($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApproveProcess levelLt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApproveProcess nameLike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApproveProcess newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApproveProcess newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApproveProcess noTypes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApproveProcess notUniqued($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApproveProcess notid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApproveProcess parent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApproveProcess query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApproveProcess types($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApproveProcess uniqued($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApproveProcess whereApproveId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApproveProcess whereConditionList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApproveProcess whereDepHead($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApproveProcess whereDirectorLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApproveProcess whereDirectorOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApproveProcess whereExamineMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApproveProcess whereGroups($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApproveProcess whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApproveProcess whereInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApproveProcess whereIsChild($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApproveProcess whereIsCondition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApproveProcess whereIsInitial($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApproveProcess whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApproveProcess whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApproveProcess whereNoHander($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApproveProcess whereParent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApproveProcess wherePassRatio($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApproveProcess wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApproveProcess whereSelectMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApproveProcess whereSelectRange($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApproveProcess whereSelfSelect($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApproveProcess whereSettype($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApproveProcess whereTypes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApproveProcess whereUniqued($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApproveProcess whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApproveProcess whereUserList($value)
 */
	class SystemCrudApproveProcess extends \Eloquent {}
}

namespace App\Http\Model\Crud{
/**
 * 低代码审批数据记录.
 *
 * class SystemCrudApproveRecord
 *
 * @property int $id
 * @property int $approve_id 审批申请表的主键id
 * @property int $crud_id crud的主键id
 * @property int $data_id 实体表主键id
 * @property string $event 触发动作：create、update、delete
 * @property string $approve_event 审批动作：revoke、撤销，reject、驳回;
 * @property string $table_name crud的表名
 * @property string $data 实体表数据
 * @property string $schedule_data 实体附表数据
 * @property string $original_data 原来实体表数据
 * @property string $original_schedule_data 原来实体附表数据
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApproveRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApproveRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApproveRecord query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApproveRecord whereApproveEvent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApproveRecord whereApproveId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApproveRecord whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApproveRecord whereCrudId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApproveRecord whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApproveRecord whereDataId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApproveRecord whereEvent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApproveRecord whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApproveRecord whereOriginalData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApproveRecord whereOriginalScheduleData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApproveRecord whereScheduleData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApproveRecord whereTableName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApproveRecord whereUpdatedAt($value)
 */
	class SystemCrudApproveRecord extends \Eloquent {}
}

namespace App\Http\Model\Crud{
/**
 * App\Http\Model\Crud\SystemCrudApproveRule
 *
 * @property int $id 自增id
 * @property int $user_id 创建用户ID
 * @property int $approve_id
 * @property string $range 可见范围
 * @property int $abnormal 异常处理：0、自动同意；指定处理人ID；
 * @property string $auto 自动审批：0、首个节点处理，其他自动同意；1、连续审批自动同意；2、每个节点都需审批；
 * @property int $is_sign
 * @property int $is_transfer
 * @property mixed $edit 修改权限：0、员工不可修改固定人员；1、不可删除固定抄送人；
 * @property int $recall 异常处理：1、审批通过后允许撤销；
 * @property string|null $refuse 被拒绝后：0、返回初始，所有人重新审批；1、跳过已通过层级；
 * @property-read \App\Http\Model\Admin\Admin|null $abCard
 * @property-read \App\Http\Model\Crud\SystemCrudApprove|null $approve
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApproveRule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApproveRule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApproveRule query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApproveRule whereAbnormal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApproveRule whereApproveId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApproveRule whereAuto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApproveRule whereEdit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApproveRule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApproveRule whereIsSign($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApproveRule whereIsTransfer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApproveRule whereRange($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApproveRule whereRecall($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApproveRule whereRefuse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudApproveRule whereUserId($value)
 */
	class SystemCrudApproveRule extends \Eloquent {}
}

namespace App\Http\Model\Crud{
/**
 * App\Http\Model\Crud\SystemCrudCate
 *
 * @property int $id 自增id
 * @property string $name 分类名称
 * @property int $sort 排序
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $info 应用简介
 * @property-read \App\Http\Model\System\Menus|null $menu
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudCate ids($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudCate name($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudCate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudCate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudCate query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudCate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudCate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudCate whereInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudCate whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudCate whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudCate whereUpdatedAt($value)
 */
	class SystemCrudCate extends \Eloquent {}
}

namespace App\Http\Model\Crud{
/**
 * 评论
 *
 * @property int $id
 * @property int $uid 创建用户ID
 * @property int $crud_id crud的主键id
 * @property int $data_id crud的表的自增id
 * @property int $pid 评论父级id
 * @property string $comment 评论内容
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, SystemCrudComment> $reply
 * @property-read int|null $reply_count
 * @property-read \App\Http\Model\Admin\Admin|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudComment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudComment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudComment query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudComment whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudComment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudComment whereCrudId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudComment whereDataId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudComment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudComment wherePid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudComment whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudComment whereUpdatedAt($value)
 */
	class SystemCrudComment extends \Eloquent {}
}

namespace App\Http\Model\Crud{
/**
 * curl.
 *
 * @property int $id 自增id
 * @property string $title 接口标题
 * @property int $is_pre 0=直接请求，1=前置请求
 * @property string $pre_url 前置请求地址
 * @property string $pre_method 前置请求method
 * @property mixed $pre_headers 前置请求header
 * @property mixed $pre_data 前置请求data
 * @property int $pre_cache_time 前置请求缓存时间
 * @property string $url 请求地址
 * @property string $method 请求method
 * @property mixed $headers 请求header
 * @property mixed $data 请求data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudCurl method($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudCurl newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudCurl newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudCurl onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudCurl query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudCurl title($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudCurl whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudCurl whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudCurl whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudCurl whereHeaders($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudCurl whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudCurl whereIsPre($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudCurl whereMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudCurl wherePreCacheTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudCurl wherePreData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudCurl wherePreHeaders($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudCurl wherePreMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudCurl wherePreUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudCurl whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudCurl whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudCurl whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudCurl withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudCurl withoutTrashed()
 */
	class SystemCrudCurl extends \Eloquent {}
}

namespace App\Http\Model\Crud{
/**
 * Class SystemCrudDashboard.
 *
 * @property int $id 自增id
 * @property int $user_id 创建用户ID
 * @property int $update_user_id 修改用户ID
 * @property string $name 名称
 * @property array $configure 布局
 * @property int $status 状态：0、关闭；1、开启；
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Http\Model\System\Menus|null $menu
 * @property-read \App\Http\Model\Admin\Admin|null $updateUser
 * @property-read \App\Http\Model\Admin\Admin|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudDashboard nameLike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudDashboard newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudDashboard newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudDashboard notId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudDashboard onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudDashboard query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudDashboard whereConfigure($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudDashboard whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudDashboard whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudDashboard whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudDashboard whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudDashboard whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudDashboard whereUpdateUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudDashboard whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudDashboard whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudDashboard withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudDashboard withoutTrashed()
 */
	class SystemCrudDashboard extends \Eloquent {}
}

namespace App\Http\Model\Crud{
/**
 * 数据共享模型
 *
 * @property int $id
 * @property int $share_id 数据共享ID
 * @property int $crud_id crud的主键id
 * @property int $data_id crud的表的自增id
 * @property int $user_id 用户表的id
 * @property int $is_show 可查看
 * @property int $is_update 可修改
 * @property int $is_delete 可删除
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudDataShare newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudDataShare newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudDataShare query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudDataShare whereCrudId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudDataShare whereDataId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudDataShare whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudDataShare whereIsDelete($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudDataShare whereIsShow($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudDataShare whereIsUpdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudDataShare whereShareId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudDataShare whereUserId($value)
 */
	class SystemCrudDataShare extends \Eloquent {}
}

namespace App\Http\Model\Crud{
/**
 * Class SystemCrudEvent.
 *
 * @email 136327134@qq.com
 * @date 2024/2/28
 * @property int $id 自增id
 * @property int $crud_id 关联CRUD_ID
 * @property string $name 事件名称
 * @property string $event 事件类型
 * @property mixed $action 触发动作
 * @property int $sort 优先级
 * @property int $timer 定时任务执行周期
 * @property int $timer_type 周期类型:0=间隔秒数；1=间隔n分；2=间隔n小时；3=间隔n天；4=每天；5=每星期；6=每年
 * @property int $target_crud_id 目标实体
 * @property int $crud_approve_id 实体内的审核ID
 * @property int $curl_id 接口管理id
 * @property int $send_type 发送用户类型:0=内部;1=外部
 * @property mixed $send_user 发送用户
 * @property mixed $notify_type 通知类型
 * @property mixed $additional_search 附加搜索视图信息
 * @property int $additional_search_boolean 附加搜索条件：0=符合其一 1= 符合全部
 * @property string $template 模板内容
 * @property mixed $field_options 字段信息
 * @property mixed $aggregate_target_search 聚合目标搜索
 * @property int $aggregate_target_search_boolean 聚合目标搜索：0=符合其一 1= 符合全部
 * @property mixed $aggregate_data_search 聚合数据搜索
 * @property int $aggregate_data_search_boolean 聚合数据搜索：0=符合其一 1= 符合全部
 * @property mixed $aggregate_data_field 分组字段关联
 * @property mixed $aggregate_field_rule 聚合字段规则
 * @property string $sms_template_id 短信模板id
 * @property string $work_webhook_url 企业微信bot webhook地址
 * @property string $ding_webhook_url 钉钉机器人webhook地址
 * @property string $other_webhook_url 其他bot webhook地址
 * @property mixed $update_field_options 更新字段相关数据
 * @property int $other_webhook_status 其他bot 状态
 * @property int $ding_webhook_status 钉钉机器人webhook状态
 * @property int $work_webhook_status 企业微信bot webhook状态
 * @property int $sms_status 短信状态
 * @property int $system_status 系统消息状态
 * @property mixed $options 其他信息
 * @property mixed $timer_options 执行周期配置详情
 * @property int $status 状态:0=关闭;1=开启
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Http\Model\Crud\SystemCrud|null $crud
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudEvent cateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudEvent crudId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudEvent name($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudEvent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudEvent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudEvent onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudEvent query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudEvent whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudEvent whereAdditionalSearch($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudEvent whereAdditionalSearchBoolean($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudEvent whereAggregateDataField($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudEvent whereAggregateDataSearch($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudEvent whereAggregateDataSearchBoolean($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudEvent whereAggregateFieldRule($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudEvent whereAggregateTargetSearch($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudEvent whereAggregateTargetSearchBoolean($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudEvent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudEvent whereCrudApproveId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudEvent whereCrudId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudEvent whereCurlId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudEvent whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudEvent whereDingWebhookStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudEvent whereDingWebhookUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudEvent whereEvent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudEvent whereFieldOptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudEvent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudEvent whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudEvent whereNotifyType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudEvent whereOptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudEvent whereOtherWebhookStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudEvent whereOtherWebhookUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudEvent whereSendType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudEvent whereSendUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudEvent whereSmsStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudEvent whereSmsTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudEvent whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudEvent whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudEvent whereSystemStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudEvent whereTargetCrudId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudEvent whereTemplate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudEvent whereTimer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudEvent whereTimerOptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudEvent whereTimerType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudEvent whereUpdateFieldOptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudEvent whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudEvent whereWorkWebhookStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudEvent whereWorkWebhookUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudEvent withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudEvent withoutTrashed()
 */
	class SystemCrudEvent extends \Eloquent {}
}

namespace App\Http\Model\Crud{
/**
 * Class SystemCrudEventLog.
 *
 * @email 136327134@qq.com
 * @date 2024/3/14
 * @property int $id 自增id
 * @property int $crud_id 关联CRUD_ID
 * @property int $event_id 触发器ID
 * @property string $action 触发类型
 * @property string $result 触发结果
 * @property mixed $parameter 出发参数
 * @property mixed $log 日志内容
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Http\Model\Crud\SystemCrud|null $crud
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudEventLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudEventLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudEventLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudEventLog whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudEventLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudEventLog whereCrudId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudEventLog whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudEventLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudEventLog whereLog($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudEventLog whereParameter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudEventLog whereResult($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudEventLog whereUpdatedAt($value)
 */
	class SystemCrudEventLog extends \Eloquent {}
}

namespace App\Http\Model\Crud{
/**
 * App\Http\Model\Crud\SystemCrudField
 *
 * @property int $id 自增id
 * @property int $crud_id 关联CRUD_ID
 * @property string $field_name 字段中文名
 * @property string $field_name_en 字段英文名
 * @property string $form_value 表单值类型
 * @property string $field_type 字段类型
 * @property int $is_default_value_not_null 是否允许空值
 * @property int $is_table_show_row 是否在列表中默认显示
 * @property string $comment 字段说明
 * @property string $prev_field 前一个字段英文名
 * @property int $data_dict_id 数据字典ID
 * @property int $association_crud_id 一对一关联CRUD_ID
 * @property int $is_main 主展示字段
 * @property int $is_form 是否展示在表单中
 * @property string $form_field_uniqid 表单字段唯一值
 * @property mixed $association_field_names 一对一关联字段展示
 * @property mixed $options 其他表单信息
 * @property int $create_modify 是否创建时可以修改
 * @property int $update_modify 是否修改时可以修改
 * @property int $is_default 是否默认字段
 * @property int $data_type 0=数据字典；1=静态数据；3=数据接口
 * @property string $customize_items 静态数据
 * @property int $association_show_type 0=下拉，1=弹窗
 * @property int $is_uniqid 0=不唯一，1=唯一
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Http\Model\Crud\SystemCrud|null $association
 * @property-read \Illuminate\Database\Eloquent\Collection<int, SystemCrudField> $associationField
 * @property-read int|null $association_field_count
 * @property-read \App\Http\Model\Crud\SystemCrud|null $crud
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudField fieldName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudField newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudField newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudField notField($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudField notLowerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudField onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudField query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudField whereAssociationCrudId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudField whereAssociationFieldNames($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudField whereAssociationShowType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudField whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudField whereCreateModify($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudField whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudField whereCrudId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudField whereCustomizeItems($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudField whereDataDictId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudField whereDataType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudField whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudField whereFieldName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudField whereFieldNameEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudField whereFieldType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudField whereFormFieldUniqid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudField whereFormValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudField whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudField whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudField whereIsDefaultValueNotNull($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudField whereIsForm($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudField whereIsMain($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudField whereIsTableShowRow($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudField whereIsUniqid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudField whereOptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudField wherePrevField($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudField whereUpdateModify($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudField whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudField withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudField withoutTrashed()
 */
	class SystemCrudField extends \Eloquent {}
}

namespace App\Http\Model\Crud{
/**
 * Class SystemCrudForm.
 *
 * @email 136327134@qq.com
 * @date 2024/2/26
 * @property int $id 自增id
 * @property int $crud_id 关联CRUD_ID
 * @property int $version 版本号
 * @property string $name 表单名称
 * @property mixed $options 表单信息
 * @property array|mixed $fields 表单字段信息
 * @property mixed $global_options 表单公共信息
 * @property int $is_index 是否主表单
 * @property int $is_master 是否主要的表单
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Http\Model\Crud\SystemCrud|null $crud
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudForm newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudForm newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudForm onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudForm query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudForm whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudForm whereCrudId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudForm whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudForm whereFields($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudForm whereGlobalOptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudForm whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudForm whereIsIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudForm whereIsMaster($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudForm whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudForm whereOptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudForm whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudForm whereVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudForm withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudForm withoutTrashed()
 */
	class SystemCrudForm extends \Eloquent {}
}

namespace App\Http\Model\Crud{
/**
 * 操作日志
 *
 * @property int $id
 * @property int $uid 创建用户ID
 * @property int $crud_id crud的主键id
 * @property int $data_crud_id 数据的crud的主键id
 * @property int $data_id crud的表的自增id
 * @property string $log_type 状态：create=创建；update=更新；
 * @property string $change_field_name_en 修改的字段名称，可以为空
 * @property string $before_value 修改之前的值
 * @property string $after_value 修改之后的值
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Http\Model\Admin\Admin|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudLog whereAfterValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudLog whereBeforeValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudLog whereChangeFieldNameEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudLog whereCrudId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudLog whereDataCrudId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudLog whereDataId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudLog whereLogType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudLog whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudLog whereUpdatedAt($value)
 */
	class SystemCrudLog extends \Eloquent {}
}

namespace App\Http\Model\Crud{
/**
 * 操作按钮模型
 *
 * @property int $id
 * @property int $crud_id crud的主键id
 * @property string $name 操作名称
 * @property string $operate 操作唯一值
 * @property int $sort 排序
 * @property int $system_crud_form_id 选择的表单ID
 * @property int $operate_type 0=列表头部，1=列表中
 * @property int $status 状态
 * @property int $action_type 0=新增，1=编辑
 * @property string $popup_name 弹窗标题
 * @property mixed $use_rule 启用规则
 * @property mixed $options 参数设置
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudOperate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudOperate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudOperate query()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudOperate status($value = '')
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudOperate whereActionType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudOperate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudOperate whereCrudId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudOperate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudOperate whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudOperate whereOperate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudOperate whereOperateType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudOperate whereOptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudOperate wherePopupName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudOperate whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudOperate whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudOperate whereSystemCrudFormId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudOperate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudOperate whereUseRule($value)
 */
	class SystemCrudOperate extends \Eloquent {}
}

namespace App\Http\Model\Crud{
/**
 * 问卷调查
 *
 * @property int $id
 * @property string $url 问卷调查地址
 * @property string $unique 唯一值
 * @property int $crud_id 实体的id
 * @property int $user_id 创建人的id
 * @property int $role_type 0=仅企业员工可见，1=所有人
 * @property string $invalid_time 失效时间
 * @property int $status 0=关闭；1=开启
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Http\Model\Crud\SystemCrud|null $crud
 * @property-read \App\Http\Model\Admin\Admin|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudQuestionnaire newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudQuestionnaire newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudQuestionnaire query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudQuestionnaire whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudQuestionnaire whereCrudId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudQuestionnaire whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudQuestionnaire whereInvalidTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudQuestionnaire whereRoleType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudQuestionnaire whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudQuestionnaire whereUnique($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudQuestionnaire whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudQuestionnaire whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudQuestionnaire whereUserId($value)
 */
	class SystemCrudQuestionnaire extends \Eloquent {}
}

namespace App\Http\Model\Crud{
/**
 * 实体数据权限.
 *
 * @property int $id
 * @property int $role_id 关联角色ID
 * @property int $crud_id 关联实体ID
 * @property string|null $crud_name 关联实体名称
 * @property int $created 新增权限
 * @property int $reade 查看权限:4、全部.3、指定部门.2、当前部门.1、仅本人.0、不允许
 * @property string|null $reade_frame 查看部门
 * @property int $updated 修改权限:4、全部.3、指定部门.2、当前部门.1、仅本人.0、不允许
 * @property string|null $updated_frame 可修改部门
 * @property int $deleted 删除权限:4、全部.3、指定部门.2、当前部门.1、仅本人.0、不允许
 * @property string|null $deleted_frame 可删除部门
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $transfer 查看权限:4、全部.3、指定部门.2、当前部门.1、仅本人.0、不允许
 * @property string|null $transfer_frame 查看部门
 * @property int $share 查看权限:4、全部.3、指定部门.2、当前部门.1、仅本人.0、不允许
 * @property string|null $share_frame 查看部门
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudRole newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudRole newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudRole query()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudRole roleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudRole whereCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudRole whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudRole whereCrudId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudRole whereCrudName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudRole whereDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudRole whereDeletedFrame($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudRole whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudRole whereReade($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudRole whereReadeFrame($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudRole whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudRole whereShare($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudRole whereShareFrame($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudRole whereTransfer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudRole whereTransferFrame($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudRole whereUpdated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudRole whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudRole whereUpdatedFrame($value)
 */
	class SystemCrudRole extends \Eloquent {}
}

namespace App\Http\Model\Crud{
/**
 * App\Http\Model\Crud\SystemCrudSeniorSearch
 *
 * @property int $id 自增id
 * @property int $crud_id 关联CRUD_ID
 * @property int $user_id 关联USER_ID
 * @property int $sort 排序
 * @property string $senior_title 高级搜索标题
 * @property string $senior_search 高级搜索
 * @property int $senior_type 0=个人，1=系统
 * @property int $search_boolean 0=or，1=and
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudSeniorSearch newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudSeniorSearch newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudSeniorSearch onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudSeniorSearch query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudSeniorSearch whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudSeniorSearch whereCrudId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudSeniorSearch whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudSeniorSearch whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudSeniorSearch whereSearchBoolean($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudSeniorSearch whereSeniorSearch($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudSeniorSearch whereSeniorTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudSeniorSearch whereSeniorType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudSeniorSearch whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudSeniorSearch whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudSeniorSearch whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudSeniorSearch withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudSeniorSearch withoutTrashed()
 */
	class SystemCrudSeniorSearch extends \Eloquent {}
}

namespace App\Http\Model\Crud{
/**
 * 数据共享记录
 *
 * @property int $id
 * @property int $crud_id crud的主键id
 * @property int $user_id 用户表的id
 * @property int $role_type 0=查看，1=可查看，可编辑，2=可查看，可编辑，可删除
 * @property int $operate_user_id 操作人的id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Http\Model\Admin\Admin|null $operate
 * @property-read \App\Http\Model\Admin\Admin|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudShare ids($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudShare newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudShare newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudShare query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudShare whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudShare whereCrudId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudShare whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudShare whereOperateUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudShare whereRoleType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudShare whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudShare whereUserId($value)
 */
	class SystemCrudShare extends \Eloquent {}
}

namespace App\Http\Model\Crud{
/**
 * App\Http\Model\Crud\SystemCrudTable
 *
 * @property int $id 自增id
 * @property int $crud_id 关联CRUD_ID
 * @property int $version 版本号
 * @property mixed $senior_search 高级搜索
 * @property mixed $view_search 视图搜索
 * @property mixed $show_field 默认展示字段搜索
 * @property mixed $options 其他表单信息
 * @property int $is_index 是否主配置
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudTable newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudTable newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudTable onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudTable query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudTable whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudTable whereCrudId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudTable whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudTable whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudTable whereIsIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudTable whereOptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudTable whereSeniorSearch($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudTable whereShowField($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudTable whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudTable whereVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudTable whereViewSearch($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudTable withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudTable withoutTrashed()
 */
	class SystemCrudTable extends \Eloquent {}
}

namespace App\Http\Model\Crud{
/**
 * Class SystemCrudTableUser.
 *
 * @email 136327134@qq.com
 * @date 2024/3/9
 * @property int $id 自增id
 * @property int $crud_id 关联CRUD_ID
 * @property int $user_id 关联USER_ID
 * @property mixed $senior_search 高级搜索
 * @property mixed $show_field 字段信息
 * @property mixed $options 其他信息
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudTableUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudTableUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudTableUser onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudTableUser query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudTableUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudTableUser whereCrudId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudTableUser whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudTableUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudTableUser whereOptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudTableUser whereSeniorSearch($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudTableUser whereShowField($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudTableUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudTableUser whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudTableUser withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemCrudTableUser withoutTrashed()
 */
	class SystemCrudTableUser extends \Eloquent {}
}

namespace App\Http\Model\Customer{
/**
 * 合同签约
 * Class Contract.
 *
 * @property int $id
 * @property int $uid 经办人ID
 * @property int $eid 关联客户ID
 * @property array|null $cid 关联订单ID
 * @property array|null $oid 关联商机ID
 * @property int $link_type 关联类型:2.订单 5.商机
 * @property string $doc_name 合同名称
 * @property string $doc_no 合同编号
 * @property int $status 合同状态
 * @property int $sign_type 签约方式:1.纸质签约 2.电子签约
 * @property int $term_type 期限类型:0.无期限 1.固定期限 2.签约日起算
 * @property int $date_count 期限时长
 * @property \Illuminate\Support\Carbon|null $start_date 合同开始日期
 * @property \Illuminate\Support\Carbon|null $end_date 合同结束日期
 * @property int $sign_status 签约状态
 * @property \Illuminate\Support\Carbon|null $sign_date 签约日期
 * @property string|null $signature_sn 电子签订单号
 * @property array|null $sign_file 签署前文件
 * @property string|null $file_id 临时文件ID
 * @property string|null $app_url 签署小程序链接
 * @property string|null $pc_url 签署pc链接
 * @property string|null $sign_url 签署后文件地址
 * @property string|null $sign_result 签署后本地文件地址
 * @property int $approve_id 关联审批ID
 * @property int $is_verify 是否需要审核
 * @property mixed|null $fail_time 合同截止日期
 * @property string|null $mark 备注信息
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Http\Model\Admin\Admin|null $admin
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\System\Attach> $attach
 * @property-read int|null $attach_count
 * @property-read \App\Http\Model\Customer\Customer|null $customer
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\Customer\ProductAssist> $products
 * @property-read int|null $products_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\System\Attach> $result
 * @property-read int|null $result_count
 * @property-read \App\Http\Model\Approve\ApproveRule|null $rules
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\Customer\ContractSignatory> $signatory
 * @property-read int|null $signatory_count
 * @method static \Illuminate\Database\Eloquent\Builder|Contract cid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract eid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract endDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract failStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract nameLike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Contract newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Contract notId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract oid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Contract query()
 * @method static \Illuminate\Database\Eloquent\Builder|Contract signStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract signTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract startDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract status($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract uid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereAppUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereApproveId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereCid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereDateCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereDocName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereDocNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereEid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereFailTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereFileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereIsVerify($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereLinkType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereMark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereOid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract wherePcUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereSignDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereSignFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereSignResult($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereSignStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereSignType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereSignUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereSignatureSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereTermType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Contract withoutTrashed()
 */
	class Contract extends \Eloquent {}
}

namespace App\Http\Model\Customer{
/**
 * 合同签署方
 * Class ContractSignatory.
 *
 * @property int $id
 * @property int $cid 合同id
 * @property int $user_id 用户id
 * @property string $name 经办人姓名
 * @property string $company_name 企业名称
 * @property string $phone 电话
 * @property int $types 人员类型:0、发起人 1、签署人 2、签署企业
 * @property string $e_userid 电子签用户id
 * @property string $e_openid 电子签用户标识
 * @property mixed|null $sign_time 签约时间
 * @property int $sign_status 签约状态
 * @property string|null $remark 备注
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Http\Model\Admin\Admin|null $admin
 * @method static \Illuminate\Database\Eloquent\Builder|ContractSignatory cid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractSignatory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractSignatory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractSignatory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractSignatory query()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractSignatory time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractSignatory whereCid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractSignatory whereCompanyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractSignatory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractSignatory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractSignatory whereEOpenid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractSignatory whereEUserid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractSignatory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractSignatory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractSignatory wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractSignatory whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractSignatory whereSignStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractSignatory whereSignTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractSignatory whereTypes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractSignatory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractSignatory whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractSignatory withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractSignatory withoutTrashed()
 */
	class ContractSignatory extends \Eloquent {}
}

namespace App\Http\Model\Customer{
/**
 * 客户
 * Class Customer.
 *
 * @property int $id
 * @property int $uid 业务员ID
 * @property string $userid 企微用户ID
 * @property string $external_userid 企微客户ID
 * @property int $before_uid 前业务员ID
 * @property int $creator_uid 创建人ID
 * @property string|null $customer_name 客户名称
 * @property array|null $customer_label 客户标签
 * @property string $customer_no 客户编号
 * @property string|null $customer_way 客户来源
 * @property int $un_followed_days 未跟进天数
 * @property string $amount_recorded 已入账金额
 * @property string $amount_expend 已支出+金额
 * @property string $invoiced_amount 已开票金额
 * @property int $contract_num 合同数量
 * @property int $invoice_num 发票数量
 * @property int $attachment_num 附件数量
 * @property int $return_num 退回次数
 * @property string|null $customer_followed 是否关注
 * @property string|null $customer_status 客户状态
 * @property array|null $area_cascade 省市区
 * @property array|null $member 协作者
 * @property string $b37a3f36 备注
 * @property string|null $customer_tel 企业电话
 * @property string|null $9bfe77e4 详细地址
 * @property string $7763f80f 客户附件
 * @property mixed|null $last_follow_up_time 最后跟进时间
 * @property mixed|null $collect_time 领取时间
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null $c839a357 备注
 * @property string|null $c3c44e85 添加时间
 * @property string|null $cf4bb8ff 客户类型
 * @property string $c5d01f85 线索
 * @property string $c9d33076 企微客户
 * @property string|null $clue_id 关联线索
 * @property string|null $c0dbd2e5 演示人
 * @property-read \App\Http\Model\Admin\Admin|null $card
 * @property-read \App\Http\Model\Config\GroupData|null $cate
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\Customer\FollowUp> $follows
 * @property-read int|null $follows_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\Customer\Liaison> $liaison
 * @property-read int|null $liaison_count
 * @property-read \App\Http\Model\Admin\Admin|null $salesman
 * @property-read \App\Http\Model\Customer\FollowUp|null $track
 * @property-read \App\Http\Model\Config\GroupData|null $way
 * @method static \Illuminate\Database\Eloquent\Builder|Customer cid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer clientNoLike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer customerLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer customerStatusLt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer emailLike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer incompleteScheduleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer involved($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer labelLike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer nameEq($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer nameLike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Customer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Customer notId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer notUId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Customer phoneLike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer query()
 * @method static \Illuminate\Database\Eloquent\Builder|Customer status($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer uid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer where7763f80f($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer where9bfe77e4($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereAmountExpend($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereAmountRecorded($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereAreaCascade($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereAttachmentNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereB37a3f36($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereBeforeUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereC0dbd2e5($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereC3c44e85($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereC5d01f85($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereC839a357($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereC9d33076($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereCf4bb8ff($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereClueId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereCollectTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereContractNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereCreatorUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereCustomerFollowed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereCustomerLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereCustomerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereCustomerNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereCustomerStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereCustomerTel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereCustomerWay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereExternalUserid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereInvoiceNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereInvoicedAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereLastFollowUpTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereMember($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereReturnNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereUnFollowedDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereUserid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Customer withoutTrashed()
 */
	class Customer extends \Eloquent {}
}

namespace App\Http\Model\Customer{
/**
 * 客户文件列表
 * Class ClientFile.
 *
 * @property int $id 附件ID
 * @property int $eid 客户ID
 * @property int $cid 合同ID
 * @property int $fid 跟进记录ID
 * @property int $vid 发票申请ID
 * @property int $uid 上传用户ID
 * @property string $name 附件名称
 * @property string $real_name 附件原始名称
 * @property string $att_dir 附件路径
 * @property string $thumb_dir 附件压缩路径
 * @property string $att_size 附件大小
 * @property string $att_type 附件类型
 * @property int $entid 分后台ID
 * @property int $up_type 上传方式：1、本地；2、七牛云；3、OSS；4、COS。
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Http\Model\Admin\Admin|null $card
 * @method static \Illuminate\Database\Eloquent\Builder|File cid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File eid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File entid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File entidName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File entids($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File fid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File name($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|File newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|File query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereAttDir($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereAttSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereAttType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereCid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereEid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereFid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereRealName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereThumbDir($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereUpType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereVid($value)
 */
	class File extends \Eloquent {}
}

namespace App\Http\Model\Customer{
/**
 * Class FollowUp.
 *
 * @property int $id 自增id
 * @property int $eid 客户ID
 * @property string $link_type 关联业务:customer:客户,contract:合同,clue:线索,odds:商机
 * @property int $user_id 用户ID
 * @property string $content 说明内容
 * @property int $types 类型：0，说明；1，提醒；
 * @property mixed|null $time 提醒时间
 * @property string $uniqued 定时任务唯一值
 * @property int $status 状态：0、待处理；1、放弃；2、已完成；
 * @property \Illuminate\Support\Carbon|null $deleted_at 删除时间
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $follow_version 跟进版本
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\System\Attach> $attachs
 * @property-read int|null $attachs_count
 * @property-read \App\Http\Model\Admin\Admin|null $card
 * @property-read \App\Http\Model\Customer\Customer|null $client
 * @property-read \App\Http\Model\Customer\Lead|null $clue
 * @property-read \App\Http\Model\Customer\Customer|null $customer
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\Client\ClientFile> $file
 * @property-read int|null $file_count
 * @property mixed $files
 * @property-read FollowUp|null $latest
 * @property-read \App\Http\Model\Customer\Opportunity|null $odds
 * @method static \Illuminate\Database\Eloquent\Builder|FollowUp eid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FollowUp exist($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FollowUp id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FollowUp nameLike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FollowUp newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FollowUp newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FollowUp onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|FollowUp query()
 * @method static \Illuminate\Database\Eloquent\Builder|FollowUp time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FollowUp timeLt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FollowUp types($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FollowUp uniqued($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FollowUp userId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FollowUp whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FollowUp whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FollowUp whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FollowUp whereEid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FollowUp whereFollowVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FollowUp whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FollowUp whereLinkType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FollowUp whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FollowUp whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FollowUp whereTypes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FollowUp whereUniqued($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FollowUp whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FollowUp whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FollowUp withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|FollowUp withoutTrashed()
 */
	class FollowUp extends \Eloquent {}
}

namespace App\Http\Model\Customer{
/**
 * 客户发票
 * Class ClientInvoice.
 *
 * @property int $id 自增id
 * @property int $entid 企业ID
 * @property string $unique 唯一值
 * @property string $serial_number 发票流水号
 * @property string $uid 业务员ID
 * @property int $eid 客户ID
 * @property int $cid 合同ID
 * @property int $category_id 发票类目ID
 * @property string $name 发票名称
 * @property string|null $num 发票编号
 * @property string $price 合同金额
 * @property string $amount 发票金额
 * @property string $types 发票类型
 * @property string $title 发票抬头
 * @property string $ident 纳税人识别号
 * @property string $bank 开户行
 * @property string $account 开户账号
 * @property string $address 开票地址
 * @property string $tel 电话
 * @property string $collect_name 邮寄联系人
 * @property string $collect_tel 邮寄联系电话
 * @property string $collect_type 邮寄方式
 * @property string $collect_email 邮寄邮箱
 * @property string $mail_address 邮寄地址
 * @property string|null $invoice_type 开票方式
 * @property string|null $invoice_address 开票地址
 * @property int $status 发票状态 -1：开票撤回；0：待开票；1：已开票；2:已拒绝；3：申请作废；4:同意作废；5：拒绝作废；6：作废撤回；
 * @property int $invalid 作废状态: 0，默认；-1，撤回；1，待审核；2，审核通过；3，审核未通过
 * @property string|null $bill_date 开票日期
 * @property string|null $real_date 实际开票日期
 * @property string $mark 备注内容
 * @property string $remark 开票备注
 * @property string $card_remark 业务员备注
 * @property string $finance_remark 财务备注
 * @property string $creator 创建人ID
 * @property int $link_id 关联审批ID
 * @property int $revoke_id 撤销申请ID
 * @property string $link_bill 关联付款单ID
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\Attach\SystemAttach> $attachs
 * @property-read int|null $attachs_count
 * @property-read \App\Http\Model\Admin\Admin|null $card
 * @property-read \App\Http\Model\Client\ClientInvoiceCategory|null $category
 * @property-read \App\Http\Model\Customer\Customer|null $client
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\Customer\Payment> $clientBill
 * @property-read int|null $client_bill_count
 * @property-read \App\Http\Model\Customer\Customer|null $customer
 * @property-read \App\Http\Model\Admin\Admin|null $enterprise
 * @property-read \App\Http\Model\Customer\Order|null $treaty
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice billDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice cid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice createdAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice eid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice isAbnormal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice nameLike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice noStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice query()
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice realDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice status($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice uid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice uids($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereAccount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereBank($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereBillDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereCardRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereCid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereCollectEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereCollectName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereCollectTel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereCollectType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereCreator($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereEid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereFinanceRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereIdent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereInvalid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereInvoiceAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereInvoiceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereLinkBill($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereLinkId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereMailAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereMark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereRealDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereRevokeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereSerialNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereTel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereTypes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereUnique($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereUpdatedAt($value)
 */
	class Invoice extends \Eloquent {}
}

namespace App\Http\Model\Customer{
/**
 * 客户发票操作日志.
 *
 * @property int $id
 * @property int $entid 企业ID
 * @property int $invoice_id 发票ID
 * @property int $uid 用户ID
 * @property int $type 操作类型
 * @property mixed $operation 日志内容
 * @property \Illuminate\Support\Carbon|null $created_at 创建时间
 * @property \Illuminate\Support\Carbon|null $updated_at 修改时间
 * @property-read \App\Http\Model\Admin\Admin|null $card
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceLog cid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceLog id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceLog whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceLog whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceLog whereOperation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceLog whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceLog whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceLog whereUpdatedAt($value)
 */
	class InvoiceLog extends \Eloquent {}
}

namespace App\Http\Model\Customer{
/**
 * 客户标签
 * Class ClientLabel.
 *
 * @property int $id 自增id
 * @property int $entid
 * @property string $name 标签名称
 * @property int $sort 排序
 * @property int $pid 上级ID
 * @property int $is_work 0=本地标签，1=企业微信标签
 * @property string $work_group_id 企业微信标签分组ID
 * @property string $work_tag_id 企业微信标签ID
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Label> $children
 * @property-read int|null $children_count
 * @property-read Label|null $parent
 * @method static \Illuminate\Database\Eloquent\Builder|Label id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Label nameEq($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Label nameLike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Label newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Label newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Label notId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Label notPid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Label pid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Label query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Label whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Label whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Label whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Label whereIsWork($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Label whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Label wherePid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Label whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Label whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Label whereWorkGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Label whereWorkTagId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Label workGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Label workTagId($value)
 */
	class Label extends \Eloquent {}
}

namespace App\Http\Model\Customer{
/**
 * 线索模型.
 *
 * @property int $id id
 * @property int $uid 业务员用户ID
 * @property int $before_uid 前业务员用户ID
 * @property int $creator_uid 创建用户ID
 * @property string|null $name 线索名称
 * @property string|null $source 线索来源
 * @property string|null $pool
 * @property string|null $phone 联系电话
 * @property string|null $status 线索状态
 * @property string|null $followed 是否关注
 * @property int $return_num 退回次数
 * @property mixed|null $claim_time 领取时间
 * @property string|null $mark 备注
 * @property string|null $userid
 * @property string|null $external_userid
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property array|null $area_cascade 省市区
 * @property string|null $address 详细地址
 * @property array|null $customer_label 客户标签
 * @property mixed|null $createtime 线索时间
 * @property string $unionid unionid
 * @property-read \App\Http\Model\Admin\Admin|null $admin
 * @property-read \App\Http\Model\Customer\Customer|null $customer
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\Customer\FollowUp> $follows
 * @property-read int|null $follows_count
 * @method static \Illuminate\Database\Eloquent\Builder|Lead customerLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lead id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lead isWork($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lead nameLike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lead newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Lead newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Lead notId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lead notUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lead onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Lead query()
 * @method static \Illuminate\Database\Eloquent\Builder|Lead time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lead whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lead whereAreaCascade($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lead whereBeforeUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lead whereClaimTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lead whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lead whereCreatetime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lead whereCreatorUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lead whereCustomerLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lead whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lead whereExternalUserid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lead whereFollowed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lead whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lead whereMark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lead whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lead wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lead wherePool($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lead whereReturnNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lead whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lead whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lead whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lead whereUnionid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lead whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lead whereUserid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lead withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Lead withoutTrashed()
 */
	class Lead extends \Eloquent {}
}

namespace App\Http\Model\Customer{
/**
 * 联系人
 * Class Liaison.
 *
 * @property int $id
 * @property int $uid 业务员ID
 * @property string|null $eid 客户名称
 * @property int $creator_uid 创建人ID
 * @property string|null $liaison_name 联系人姓名
 * @property string|null $liaison_tel 联系电话
 * @property string|null $liaison_job 联系人职位
 * @property string $userid
 * @property string $external_userid
 * @property string|null $e06d7153 性别
 * @property string|null $e06d7152 联系人邮箱
 * @property string|null $e06d7159 联系人微信
 * @property string|null $l753bf282 备注
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null $cdc4d06a 联系人QQ
 * @property string $odds_customer 客户名称
 * @method static \Illuminate\Database\Eloquent\Builder|Liaison eid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Liaison id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Liaison liaisonName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Liaison liaisonTel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Liaison newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Liaison newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Liaison notId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Liaison onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Liaison query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Liaison uid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Liaison whereCdc4d06a($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Liaison whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Liaison whereCreatorUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Liaison whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Liaison whereE06d7152($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Liaison whereE06d7153($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Liaison whereE06d7159($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Liaison whereEid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Liaison whereExternalUserid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Liaison whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Liaison whereL753bf282($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Liaison whereLiaisonJob($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Liaison whereLiaisonName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Liaison whereLiaisonTel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Liaison whereOddsCustomer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Liaison whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Liaison whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Liaison whereUserid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Liaison withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Liaison withoutTrashed()
 */
	class Liaison extends \Eloquent {}
}

namespace App\Http\Model\Customer{
/**
 * 商机.
 *
 * @property int $id id
 * @property int $uid 业务员用户ID
 * @property string $userid 企微用户ID
 * @property string $external_userid 企微客户ID
 * @property int $before_uid 前业务员用户ID
 * @property int $creator_uid 创建用户ID
 * @property string|null $name 商机名称
 * @property string|null $odds_no 商机编号
 * @property string|null $eid 客户名称
 * @property int $cid 关联订单ID
 * @property string $source 商机类型：1、线索；0、客户；
 * @property string|null $types 商机类型
 * @property string|null $status 商机状态
 * @property string|null $followed 是否关注
 * @property string|null $description 商机描述
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null $c3e2adbb 附件
 * @property string $pid 客户名称
 * @property string $c5a5ade1 rt
 * @property string|null $odds_customer 客户名称
 * @property-read \App\Http\Model\Customer\Customer|null $customer
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\Customer\FollowUp> $follows
 * @property-read int|null $follows_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\Customer\ProductAssist> $product
 * @property-read int|null $product_count
 * @method static \Illuminate\Database\Eloquent\Builder|Opportunity eid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Opportunity id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Opportunity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Opportunity newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Opportunity onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Opportunity query()
 * @method static \Illuminate\Database\Eloquent\Builder|Opportunity time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Opportunity whereBeforeUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Opportunity whereC3e2adbb($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Opportunity whereC5a5ade1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Opportunity whereCid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Opportunity whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Opportunity whereCreatorUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Opportunity whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Opportunity whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Opportunity whereEid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Opportunity whereExternalUserid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Opportunity whereFollowed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Opportunity whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Opportunity whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Opportunity whereOddsCustomer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Opportunity whereOddsNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Opportunity wherePid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Opportunity whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Opportunity whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Opportunity whereTypes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Opportunity whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Opportunity whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Opportunity whereUserid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Opportunity withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Opportunity withoutTrashed()
 */
	class Opportunity extends \Eloquent {}
}

namespace App\Http\Model\Customer{
/**
 * 合同订单
 * Class Order.
 *
 * @property int $id
 * @property int $uid 业务员ID
 * @property int $eid 客户ID
 * @property string|null $oid 商机名称
 * @property int $creator_uid 创建人ID
 * @property string|null $contract_name 订单名称
 * @property string|null $contract_no 订单编号
 * @property string|null $contract_price 订单金额(元)
 * @property string $received 回款金额
 * @property string $surplus 尾款金额
 * @property string|null $contract_followed 是否关注
 * @property string|null $contract_status 订单状态
 * @property int $renew 是否有续费：0、否；1、是；
 * @property string|null $start_date 开始时间
 * @property string|null $end_date 结束时间
 * @property string|null $signing_status 签约状态
 * @property string|null $b3733f36 备注
 * @property array|null $contract_category 订单分类
 * @property string $contract_cate 合同分类copy
 * @property int $is_abnormal 是否异常：1、是；0、否；
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string $c26a5038 附件
 * @property string $c68b3d0f 图片
 * @property string|null $cde49fea 回访状态
 * @property string|null $ce77ea31 联系电话
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\Customer\Payment> $bills
 * @property-read int|null $bills_count
 * @property-read \App\Http\Model\Customer\Customer|null $client
 * @property-read \App\Http\Model\Customer\Customer|null $customer
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\Customer\ProductAssist> $product
 * @property-read int|null $product_count
 * @method static \Illuminate\Database\Eloquent\Builder|Order abnormal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order categoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order contractCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order contractStatusLt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order createdAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order eid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order endDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order endDateGt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order endDateLt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order nameLike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order notId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Order payStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order query()
 * @method static \Illuminate\Database\Eloquent\Builder|Order renew($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order signingStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order signingStatusLt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order startDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order startDateGt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order status($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order uid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereB3733f36($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereC26a5038($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereC68b3d0f($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCde49fea($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCe77ea31($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereContractCate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereContractCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereContractFollowed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereContractName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereContractNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereContractPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereContractStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCreatorUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereEid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereIsAbnormal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereOid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereReceived($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereRenew($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereSigningStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereSurplus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Order withoutTrashed()
 */
	class Order extends \Eloquent {}
}

namespace App\Http\Model\Customer{
/**
 * Class Payment.
 *
 * 回款/续费
 *
 * @property int $id 自增id
 * @property int $entid 企业ID
 * @property int $eid 客户ID
 * @property int $cid 合同ID
 * @property int $cate_id 续费类型ID
 * @property int $bill_cate_id 续费类型ID
 * @property int $bill_types 类型:0,支出;1,收入
 * @property string $uid 用户ID
 * @property int $invoice_id 发票ID
 * @property string $num 金额
 * @property string|null $mark 备注
 * @property int $types 类型：0，合同；1，续费；
 * @property int $type_id 支付方式ID
 * @property string $pay_type 支付方式名称
 * @property mixed|null $date 收款日期
 * @property string $end_date 续费结束日期
 * @property string $bill_no 付款单号
 * @property int $apply_id 关联申请审批ID
 * @property int $status 类型：0，待审核；1，已通过；2，未通过
 * @property string $fail_msg 失败原因
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Http\Model\Approve\ApproveRule|null $approveRule
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\Attach\SystemAttach> $attachs
 * @property-read int|null $attachs_count
 * @property-read \App\Http\Model\Admin\Admin|null $card
 * @property-read \App\Http\Model\Finance\BillCategory|null $cate
 * @property-read \App\Http\Model\Customer\Customer|null $client
 * @property-read \App\Http\Model\Customer\Order|null $contract
 * @property-read \App\Http\Model\Frame\Frame|null $frame
 * @property-read \App\Http\Model\Customer\Invoice|null $invoice
 * @property-read \App\Http\Model\Config\GroupData|null $renew
 * @property-read \App\Http\Model\Admin\Admin|null $salesman
 * @property-read \App\Http\Model\Customer\Order|null $treaty
 * @method static \Illuminate\Database\Eloquent\Builder|Payment applyBt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment billNoLike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment cid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment createdAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment date($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment eid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment endDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment endDateGt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment endDateIt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment entid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment invoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment markLike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment nameLike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment noWithdraw($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment notId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment query()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment status($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment typeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment types($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment uid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment updatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereApplyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereBillCateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereBillNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereBillTypes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereCateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereCid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereEid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereFailMsg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereMark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment wherePayType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereTypes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereUpdatedAt($value)
 */
	class Payment extends \Eloquent {}
}

namespace App\Http\Model\Customer{
/**
 * 商机.
 *
 * @property int $id id
 * @property int $uid 用户ID
 * @property string $name 产品名称
 * @property int $pid 产品分类
 * @property string $path 产品分类
 * @property string $unit_name 单位名
 * @property string $types 产品类型
 * @property string $number 产品编号
 * @property int $sort 排序
 * @property string $description 产品简介
 * @property int $spec_type 产品规格：0、单规格；1、多规格；
 * @property string $is_show 产品状态
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\Customer\ProductAttr> $attr
 * @property-read int|null $attr_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\Customer\ProductAttrValue> $attrValue
 * @property-read int|null $attr_value_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\Customer\ProductCategory> $category
 * @property-read int|null $category_count
 * @method static \Illuminate\Database\Eloquent\Builder|Product id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product nameLike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Product onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Product query()
 * @method static \Illuminate\Database\Eloquent\Builder|Product time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereIsShow($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product wherePid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereSpecType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereTypes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereUnitName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Product withoutTrashed()
 */
	class Product extends \Eloquent {}
}

namespace App\Http\Model\Customer{
/**
 * 商机.
 *
 * @property int $id ID
 * @property int $product_id 产品ID
 * @property string $product_name 产品名
 * @property string $sku 产品规格
 * @property string $image 产品图片
 * @property string $price 售价
 * @property string $ot_price 原价
 * @property string $total_price 总价
 * @property int $count 数量
 * @property int $discount 折扣百分比
 * @property string $remark 备注
 * @property string $unique 商品属性唯一值
 * @property int $link_id 关联ID
 * @property int $link_type 关联业务类型:1、 客户；2、合同；3、联系人；4、线索；5、商机；6、产品；
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAssist newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAssist newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAssist query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAssist unique($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAssist whereCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAssist whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAssist whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAssist whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAssist whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAssist whereLinkId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAssist whereLinkType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAssist whereOtPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAssist wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAssist whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAssist whereProductName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAssist whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAssist whereSku($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAssist whereTotalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAssist whereUnique($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAssist whereUpdatedAt($value)
 */
	class ProductAssist extends \Eloquent {}
}

namespace App\Http\Model\Customer{
/**
 * 商机.
 *
 * @property int $id 主键ID
 * @property int $product_id 产品ID
 * @property string $attr_name 属性名
 * @property array|mixed $attr_values 属性值
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttr newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttr newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttr query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttr whereAttrName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttr whereAttrValues($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttr whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttr whereProductId($value)
 */
	class ProductAttr extends \Eloquent {}
}

namespace App\Http\Model\Customer{
/**
 * 产品属性值.
 *
 * @property int $id ID
 * @property int $product_id 商品ID
 * @property array|mixed $detail 商品属性详情
 * @property string $sku 商品属性索引值
 * @property string $ot_price 原价
 * @property string $price 售价
 * @property string $cost 成本价
 * @property string $image 图片
 * @property string $bar_code 产品条码
 * @property string $unique 唯一值
 * @property-read \App\Http\Model\Customer\Product|null $product
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttrValue attrLike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttrValue newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttrValue newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttrValue productId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttrValue query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttrValue unique($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttrValue whereBarCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttrValue whereCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttrValue whereDetail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttrValue whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttrValue whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttrValue whereOtPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttrValue wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttrValue whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttrValue whereSku($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttrValue whereUnique($value)
 */
	class ProductAttrValue extends \Eloquent {}
}

namespace App\Http\Model\Customer{
/**
 * 产品分类.
 *
 * @property int $id id
 * @property int $uid 用户ID
 * @property int $pid 父id
 * @property string $path 路径
 * @property string $name 分类名称
 * @property int $level 等级
 * @property int $status 状态
 * @property int $sort 排序
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ProductCategory id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductCategory levelLt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductCategory nameLike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductCategory notId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductCategory notPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductCategory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductCategory pid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductCategory time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductCategory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductCategory whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductCategory wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductCategory wherePid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductCategory whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductCategory whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductCategory whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductCategory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductCategory withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductCategory withoutTrashed()
 */
	class ProductCategory extends \Eloquent {}
}

namespace App\Http\Model\Customer{
/**
 * 客户记录
 * Class Record.
 *
 * @property int $id
 * @property int $eid 关联业务ID
 * @property int $type 记录类型 1、退回公海；2、领取；3、流失；4、取消流失；5、移交同事；
 * @property string $link_type 关联业务:customer:客户,contract:合同,invoice:发票,clue:线索,odds:商机,
 * @property int $uid 业务员ID
 * @property int $creator_uid 创建人ID
 * @property int $record_version 记录版本
 * @property string|null $reason 原因
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Http\Model\Admin\Admin|null $creator
 * @property-read \App\Http\Model\Customer\FollowUp|null $follow
 * @property-read Record|null $latest
 * @method static \Illuminate\Database\Eloquent\Builder|Record eid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Record newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Record newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Record query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Record type($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Record whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Record whereCreatorUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Record whereEid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Record whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Record whereLinkType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Record whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Record whereRecordVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Record whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Record whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Record whereUpdatedAt($value)
 */
	class Record extends \Eloquent {}
}

namespace App\Http\Model\Customer{
/**
 * Class Remind.
 *
 * @property int $id 自增id
 * @property int $entid 企业ID
 * @property int $eid 客户ID
 * @property int $cid 合同ID
 * @property int $cate_id 续费类型ID
 * @property int $user_id 用户ID
 * @property int $bill_id 付款单ID
 * @property string $num 金额
 * @property string $mark 备注
 * @property mixed|null $time 提醒时间
 * @property mixed|null $this_period 本期时间
 * @property mixed|null $next_period 下期时间
 * @property string|null $uniqued 定时任务唯一值
 * @property int $rate 重复频率
 * @property int $period 重复周期：0、天；1、周；2、月；3、年
 * @property int $types 类型：0、回款；1、续费；
 * @property int $status 状态：0、正常；1、放弃；
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Http\Model\Admin\Admin|null $card
 * @property-read \App\Http\Model\Customer\Customer|null $client
 * @property-read \App\Http\Model\Customer\Order|null $contract
 * @property-read \App\Http\Model\Config\GroupData|null $renew
 * @property-read \App\Http\Model\Customer\Order|null $treaty
 * @method static \Illuminate\Database\Eloquent\Builder|Remind billId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Remind cid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Remind eid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Remind newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Remind newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Remind nextPeriodIt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Remind onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Remind query()
 * @method static \Illuminate\Database\Eloquent\Builder|Remind time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Remind timeLt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Remind types($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Remind uniqued($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Remind userId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Remind whereBillId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Remind whereCateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Remind whereCid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Remind whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Remind whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Remind whereEid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Remind whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Remind whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Remind whereMark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Remind whereNextPeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Remind whereNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Remind wherePeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Remind whereRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Remind whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Remind whereThisPeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Remind whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Remind whereTypes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Remind whereUniqued($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Remind whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Remind whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Remind withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Remind withoutTrashed()
 */
	class Remind extends \Eloquent {}
}

namespace App\Http\Model\Customer{
/**
 * 订单附件
 * Class Resource.
 *
 * @property int $id
 * @property int $entid 企业ID
 * @property int $eid 客户ID
 * @property int $cid 合同ID
 * @property int $uid 副表(user_enterprise)ID
 * @property string $content 备注内容
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\Attach\SystemAttach> $attachs
 * @property-read int|null $attachs_count
 * @property-read \App\Http\Model\Admin\Admin|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Resource newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Resource newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Resource query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Resource whereCid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Resource whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Resource whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Resource whereEid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Resource whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Resource whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Resource whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Resource whereUpdatedAt($value)
 */
	class Resource extends \Eloquent {}
}

namespace App\Http\Model\Customer{
/**
 * 客户关注
 * Class ClientSubscribe.
 *
 * @property int $id 自增id
 * @property int $entid 企业ID
 * @property int $uid 用户ID
 * @property int $eid 关联客户ID
 * @property int $types 类型：0、客户；1、线索；2、商机；
 * @property int $subscribe_status 关注状态：0、取消关注；1、已关注；
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Subscribe eid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscribe newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscribe newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscribe query()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscribe time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscribe uid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscribe whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscribe whereEid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscribe whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscribe whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscribe whereSubscribeStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscribe whereTypes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscribe whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscribe whereUpdatedAt($value)
 */
	class Subscribe extends \Eloquent {}
}

namespace App\Http\Model\Customer{
/**
 * 业绩目标.
 *
 * @property int $id id
 * @property int $uid 用户ID
 * @property int $link_id 用户/部门ID
 * @property string $amount 目标额
 * @property int|null $year 年份
 * @property int|null $month 月份
 * @property int $types 类型：0、人员；1、部门；
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Target linkId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Target newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Target newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Target query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Target whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Target whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Target whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Target whereLinkId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Target whereMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Target whereTypes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Target whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Target whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Target whereYear($value)
 */
	class Target extends \Eloquent {}
}

namespace App\Http\Model\Finance{
/**
 * 资金流水
 * Class Bill.
 *
 * @property int $id 自增id
 * @property int $entid
 * @property int $user_id
 * @property string $uid 创建成员ID
 * @property int $cate_id 财务流水分类ID
 * @property string $num 变动金额
 * @property mixed|null $edit_time 变动时间
 * @property int $types 变动类型:1=收入,0=支出
 * @property int $type_id 支付方式ID
 * @property string $pay_type 支付方式名称
 * @property string $mark 备注信息
 * @property string $file_id 附件ID
 * @property int $link_id 关联ID
 * @property int|null $order_id
 * @property string $link_cate 关联类型
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\System\Attach> $attachs
 * @property-read int|null $attachs_count
 * @property-read \App\Http\Model\Finance\BillCategory|null $cate
 * @property-read \App\Http\Model\Customer\Customer|null $client
 * @property-read \App\Http\Model\Customer\Payment|null $clientBill
 * @property-read \App\Http\Model\Customer\Order|null $contract
 * @property-read \App\Http\Model\System\Attach|null $file
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\System\Attach> $files
 * @property-read int|null $files_count
 * @property-read \App\Http\Model\Admin\Admin|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Bill cateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill nameLike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Bill newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Bill query()
 * @method static \Illuminate\Database\Eloquent\Builder|Bill time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill typeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill types($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill uid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill userId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereCateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereEditTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereFileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereLinkCate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereLinkId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereMark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill wherePayType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereTypes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereUserId($value)
 */
	class Bill extends \Eloquent {}
}

namespace App\Http\Model\Finance{
/**
 * 资金流水分类
 * Class BillCategory.
 *
 * @property int $id 自增id
 * @property int $entid
 * @property false|string[] $path 路径
 * @property int $level 级别
 * @property string $name 分类名称
 * @property string $cate_no 分类编号
 * @property int $sort 排序
 * @property int $pid 上级ID
 * @property int $types 类型:0,支出;1,收入
 * @property int $contact id
 * @property-read BillCategory|null $cate
 * @method static \Illuminate\Database\Eloquent\Builder|BillCategory gtLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillCategory idIt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillCategory ids($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillCategory level($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillCategory levelLt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillCategory name($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillCategory nameLike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BillCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BillCategory notPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillCategory notid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillCategory path($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillCategory pid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillCategory typesLike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillCategory whereCateNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillCategory whereContactId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillCategory whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillCategory whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillCategory wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillCategory wherePid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillCategory whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillCategory whereTypes($value)
 */
	class BillCategory extends \Eloquent {}
}

namespace App\Http\Model\Finance{
/**
 * 收支记账操作日志
 * Class BillLog.
 *
 * @property int $id 自增ID
 * @property int $entid 企业ID
 * @property int $bill_list_id 付款流水ID
 * @property int $uid 用户ID
 * @property int $type 操作类型
 * @property mixed $operation 日志内容
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Http\Model\Admin\Admin|null $card
 * @method static \Illuminate\Database\Eloquent\Builder|BillLog id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BillLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BillLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillLog whereBillListId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillLog whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillLog whereOperation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillLog whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillLog whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillLog whereUpdatedAt($value)
 */
	class BillLog extends \Eloquent {}
}

namespace App\Http\Model\Finance{
/**
 * 财务支付方式
 * Class Paytype.
 *
 * @property int $id 自增id
 * @property int $entid 企业ID
 * @property int $type_id 支付方式ID
 * @property string $name 支付方式名称
 * @property string $ident 支付方式标识
 * @property \App\Http\Model\Config\Paytype|null $info 简介
 * @property int $status 是否可用：1、是；0、否；
 * @property int $sort 排序
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Paytype entid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Paytype name($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Paytype newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Paytype newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Paytype query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Paytype typeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Paytype types($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Paytype whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Paytype whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Paytype whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Paytype whereIdent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Paytype whereInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Paytype whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Paytype whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Paytype whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Paytype whereTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Paytype whereUpdatedAt($value)
 */
	class Paytype extends \Eloquent {}
}

namespace App\Http\Model\Frame{
/**
 * Class Frame.
 *
 * @property int $id 自增ID
 * @property int $user_id 部门主管ID
 * @property int $entid
 * @property int $pid 父级ID
 * @property int $role_id 角色ID
 * @property string $name 部门名称
 * @property false|string[] $path 路径
 * @property string $introduce 部门介绍
 * @property int $sort 排序
 * @property-read int|null $user_count 用户数量
 * @property int $user_single_count 单个部门总人数
 * @property int $is_show 是否显示
 * @property int $level 等级
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Http\Model\Admin\Admin|null $super
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\Admin\Admin> $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\Admin\Admin> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|Frame entid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Frame frameIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Frame ids($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Frame nameLike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Frame newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Frame newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Frame notId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Frame notPid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Frame path($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Frame pid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Frame pids($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Frame query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Frame userIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Frame whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Frame whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Frame whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Frame whereIntroduce($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Frame whereIsShow($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Frame whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Frame whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Frame wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Frame wherePid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Frame whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Frame whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Frame whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Frame whereUserCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Frame whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Frame whereUserSingleCount($value)
 */
	class Frame extends \Eloquent {}
}

namespace App\Http\Model\Frame{
/**
 * 组织架构关联企业用户id
 * Class FrameAssist.
 *
 * @property int $id 辅助表自增id
 * @property int $entid 企业ID
 * @property int $frame_id 主表(enterprise_frame)ID
 * @property int $user_id 副表(user_enterprise)ID
 * @property int $is_mastart 是否为主部门
 * @property int $is_admin 是否为该部门的主管
 * @property int $superior_uid 上级主管用户ID
 * @property \Illuminate\Support\Carbon|null $created_at 添加时间
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Http\Model\Admin\Admin|null $admin
 * @property-read \App\Http\Model\Admin\AdminInfo|null $card
 * @property-read \App\Http\Model\Frame\Frame|null $frame
 * @property-read \App\Http\Model\Frame\Frame|null $framename
 * @property-read \App\Http\Model\Admin\Admin|null $super
 * @method static \Illuminate\Database\Eloquent\Builder|FrameAssist frameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FrameAssist frameIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FrameAssist isAdmin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FrameAssist isMastart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FrameAssist newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FrameAssist newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FrameAssist notFrameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FrameAssist notSuperiorUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FrameAssist notUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FrameAssist onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|FrameAssist query()
 * @method static \Illuminate\Database\Eloquent\Builder|FrameAssist superiorUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FrameAssist userId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FrameAssist userIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FrameAssist whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FrameAssist whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FrameAssist whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FrameAssist whereFrameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FrameAssist whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FrameAssist whereIsAdmin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FrameAssist whereIsMastart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FrameAssist whereSuperiorUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FrameAssist whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FrameAssist whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FrameAssist withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|FrameAssist withoutTrashed()
 */
	class FrameAssist extends \Eloquent {}
}

namespace App\Http\Model\Frame{
/**
 * Class FrameScope.
 *
 * @property int $id 自增id
 * @property int $uid 用户ID
 * @property int $entid 企业ID
 * @property int $link_id 关联ID
 * @property int $types 0、组织架构；1、用户；
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property mixed|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\Admin\Admin> $cards
 * @property-read int|null $cards_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\Frame\Frame> $frames
 * @property-read int|null $frames_count
 * @method static \Illuminate\Database\Eloquent\Builder|FrameScope entid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FrameScope newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FrameScope newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FrameScope query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FrameScope types($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FrameScope uid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FrameScope whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FrameScope whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FrameScope whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FrameScope whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FrameScope whereLinkId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FrameScope whereTypes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FrameScope whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FrameScope whereUpdatedAt($value)
 */
	class FrameScope extends \Eloquent {}
}

namespace App\Http\Model\Message{
/**
 * 消息
 * Class Message.
 *
 * @property int $id 自增id
 * @property int $entid 企业id
 * @property int $relation_id 总平台ID
 * @property int $cate_id 类型ID
 * @property string $cate_name
 * @property string $template_type 关联通知类型
 * @property string|null $template_var
 * @property int $template_time
 * @property string $title 消息标题
 * @property string $content 消息内容
 * @property string $remind_time 提醒时间
 * @property int $crud_id 实体id
 * @property int $event_id 实体的触发器id
 * @property int $user_sub 用户可取消订阅
 * @property mixed|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\Message\MessageTemplate> $messageTemplate
 * @property-read int|null $message_template_count
 * @property-read \App\Http\Model\Message\MessageTemplate|null $messageTemplateOne
 * @method static \Illuminate\Database\Eloquent\Builder|Message cateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message ids($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Message newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Message query()
 * @method static \Illuminate\Database\Eloquent\Builder|Message templateType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message title($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereCateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereCateName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereCrudId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereRelationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereRemindTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereTemplateTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereTemplateType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereTemplateVar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereUserSub($value)
 */
	class Message extends \Eloquent {}
}

namespace App\Http\Model\Message{
/**
 * 企业消息
 * Class MessageNotice.
 *
 * @property int $id 自增id
 * @property int $entid
 * @property int $send_id 发送人或者企业ID
 * @property string $to_uid
 * @property string $url 跳转链接
 * @property string $uni_url uni跳转路径
 * @property string|null $image 图片
 * @property string $title 消息标题
 * @property string $message 消息内容
 * @property int $type 消息类型:1=系统消息;0=个人消息;3=企业站内消息
 * @property int $cate_id 消息类型
 * @property int $message_id 消息模板ID
 * @property string $cate_name
 * @property int $is_read 是否已读:1=已读;0=未读
 * @property int $is_handle 是否已处理
 * @property int $is_show 是否显示
 * @property string $template_type 消息类型
 * @property array|mixed $button_template 消息类型
 * @property array|mixed $other 其他附加消息内容
 * @property int $link_id 关联记录ID
 * @property int $link_status 关联记录状态
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Http\Model\Company\Company|null $enterprise
 * @property-read \App\Http\Model\Message\MessageTemplate|null $template
 * @property-read \App\Http\Model\Admin\Admin|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|MessageNotice cateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageNotice ids($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageNotice linkId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageNotice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MessageNotice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MessageNotice otherId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageNotice query()
 * @method static \Illuminate\Database\Eloquent\Builder|MessageNotice templateType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageNotice time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageNotice toUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageNotice uid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageNotice whereButtonTemplate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageNotice whereCateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageNotice whereCateName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageNotice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageNotice whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageNotice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageNotice whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageNotice whereIsHandle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageNotice whereIsRead($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageNotice whereIsShow($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageNotice whereLinkId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageNotice whereLinkStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageNotice whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageNotice whereMessageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageNotice whereOther($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageNotice whereSendId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageNotice whereTemplateType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageNotice whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageNotice whereToUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageNotice whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageNotice whereUniUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageNotice whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageNotice whereUrl($value)
 */
	class MessageNotice extends \Eloquent {}
}

namespace App\Http\Model\Message{
/**
 * App\Http\Models\message\MessageSubscribe.
 *
 * @property int $id
 * @property int $user_id 企业用户ID
 * @property string|null $message_id 消息ID
 * @property int $is_subscribe 订阅/取消订阅
 * @method static \Illuminate\Database\Eloquent\Builder|MessageSubscribe newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MessageSubscribe newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MessageSubscribe query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageSubscribe whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageSubscribe whereIsSubscribe($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageSubscribe whereMessageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageSubscribe whereUserId($value)
 */
	class MessageSubscribe extends \Eloquent {}
}

namespace App\Http\Model\Message{
/**
 * 消息模板
 * Class MessageTemplate.
 *
 * @property int $id 自增id
 * @property int $relation_id 总平台ID
 * @property int $message_id 系统消息id
 * @property int $type 类型:0=系统消息;1=短信消息
 * @property string $template_id 模板id,可以为短信模板
 * @property string $message_title 消息标题
 * @property string $image 消息图片
 * @property string $url 跳转标题
 * @property string|null $uni_url 移动端跳转链接
 * @property int $status 开启状态:0=关闭;1=开启
 * @property string $webhook_url bot webhook地址
 * @property int $crud_event_id 实体内的触发器id，为0是系统的消息
 * @property int $relation_status 系统消息状态
 * @property string $content_template 内容模板
 * @property string $button_template 按钮模板
 * @property int $push_rule 推送规则:0=即时推送;1=延迟推送
 * @property int $minute 几分钟后推送
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|MessageTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MessageTemplate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MessageTemplate onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|MessageTemplate query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageTemplate whereButtonTemplate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageTemplate whereContentTemplate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageTemplate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageTemplate whereCrudEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageTemplate whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageTemplate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageTemplate whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageTemplate whereMessageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageTemplate whereMessageTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageTemplate whereMinute($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageTemplate wherePushRule($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageTemplate whereRelationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageTemplate whereRelationStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageTemplate whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageTemplate whereTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageTemplate whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageTemplate whereUniUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageTemplate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageTemplate whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageTemplate whereWebhookUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageTemplate withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|MessageTemplate withoutTrashed()
 */
	class MessageTemplate extends \Eloquent {}
}

namespace App\Http\Model\News{
/**
 * Class News.
 *
 * @property int $id 自增id
 * @property int $user_id 用户id
 * @property int $cate_id 分类ID
 * @property int $card_id
 * @property int $entid
 * @property string $title 通知标题
 * @property string $cover 封面图
 * @property string $info 通知简介
 * @property string $content 内容详情
 * @property int $is_top 是否置顶
 * @property int $push_type 发布类型：0、立即；1、定时；
 * @property mixed $push_time 发布时间
 * @property int $status 是否显示
 * @property int $sort 排序
 * @property int $visit 浏览量
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property mixed|null $deleted_at
 * @property-read \App\Http\Model\Admin\Admin|null $card
 * @property-read \App\Http\Model\News\NewsVisit|null $isVisit
 * @method static \Illuminate\Database\Eloquent\Builder|News cardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|News cateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|News day($value)
 * @method static \Illuminate\Database\Eloquent\Builder|News entid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|News equalPushTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|News id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|News isPush($value)
 * @method static \Illuminate\Database\Eloquent\Builder|News isVisit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|News newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|News newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|News notId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|News notVisit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|News pushTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|News pushType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|News query()
 * @method static \Illuminate\Database\Eloquent\Builder|News status($value)
 * @method static \Illuminate\Database\Eloquent\Builder|News time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|News titleLike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|News whereCardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|News whereCateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|News whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|News whereCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|News whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|News whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|News whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|News whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|News whereInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|News whereIsTop($value)
 * @method static \Illuminate\Database\Eloquent\Builder|News wherePushTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|News wherePushType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|News whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|News whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|News whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|News whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|News whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|News whereVisit($value)
 */
	class News extends \Eloquent {}
}

namespace App\Http\Model\News{
/**
 * Class NewsVisit.
 *
 * @property int $id 自增id
 * @property string $user_id 创建用户ID
 * @property int $notice_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|NewsVisit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NewsVisit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NewsVisit noticeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewsVisit query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewsVisit uuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewsVisit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewsVisit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewsVisit whereNoticeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewsVisit whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewsVisit whereUserId($value)
 */
	class NewsVisit extends \Eloquent {}
}

namespace App\Http\Model\Notepad{
/**
 * 笔记.
 *
 * @property int $id 自增id
 * @property string $uid 用户ID
 * @property string $title 标题名称
 * @property string $content 内容
 * @property int $pid 分类ID
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Http\Model\Notepad\NotepadCategory|null $cate
 * @property-read \App\Http\Model\Notepad\NotepadCategory|null $parent
 * @property-read \App\Http\Model\Admin\Admin|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Notepad cateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notepad newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Notepad newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Notepad pid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notepad query()
 * @method static \Illuminate\Database\Eloquent\Builder|Notepad time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notepad title($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notepad uid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notepad updatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notepad whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notepad whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notepad whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notepad wherePid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notepad whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notepad whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notepad whereUpdatedAt($value)
 */
	class Notepad extends \Eloquent {}
}

namespace App\Http\Model\Notepad{
/**
 * 笔记分类.
 *
 * @property int $id 自增id
 * @property string $uid 用户ID
 * @property false|string[] $path 路径
 * @property string $name 分类名称
 * @property int $pid 上级ID
 * @property int $types 类型：0、默认；1、用户添加
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read NotepadCategory|null $cate
 * @property-read NotepadCategory|null $parent
 * @method static \Illuminate\Database\Eloquent\Builder|NotepadCategory id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotepadCategory name($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotepadCategory nameLike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotepadCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NotepadCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NotepadCategory notId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotepadCategory path($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotepadCategory pid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotepadCategory pidLike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotepadCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotepadCategory uid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotepadCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotepadCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotepadCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotepadCategory wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotepadCategory wherePid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotepadCategory whereTypes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotepadCategory whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotepadCategory whereUpdatedAt($value)
 */
	class NotepadCategory extends \Eloquent {}
}

namespace App\Http\Model\Open{
/**
 * App\Http\Model\Open\OpenApiKey
 *
 * @property int $id
 * @property int $uid 创建用户ID
 * @property string $title 对外接口名称
 * @property string $ak 对外接口AK
 * @property string $sk 对外接口SK
 * @property string $info 描述
 * @property int $status 状态：1、启用；0、禁用；
 * @property mixed|null $last_time 最近登录时间
 * @property string $last_ip 最近登录IP
 * @property string $auth 接口权限ID
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property mixed $crud_auth
 * @method static \Illuminate\Database\Eloquent\Builder|OpenApiKey ak($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OpenApiKey nameLike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OpenApiKey newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OpenApiKey newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OpenApiKey onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|OpenApiKey query()
 * @method static \Illuminate\Database\Eloquent\Builder|OpenApiKey time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OpenApiKey whereAk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OpenApiKey whereAuth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OpenApiKey whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OpenApiKey whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OpenApiKey whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OpenApiKey whereInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OpenApiKey whereLastIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OpenApiKey whereLastTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OpenApiKey whereSk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OpenApiKey whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OpenApiKey whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OpenApiKey whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OpenApiKey whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OpenApiKey withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|OpenApiKey withoutTrashed()
 */
	class OpenApiKey extends \Eloquent implements \Tymon\JWTAuth\Contracts\JWTSubject, \crmeb\interfaces\TimeDataInterface {}
}

namespace App\Http\Model\Open{
/**
 * App\Http\Model\Open\OpenapiRule
 *
 * @property int $id
 * @property int $pid 上级id
 * @property string $name 权限名称
 * @property int $type 0=分类，1=接口
 * @property int $crud_id 实体id
 * @property string $method 请求方式
 * @property mixed $url 请求地址
 * @property string $path_prams 请求参数
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $get_prams
 * @property string|null $post_prams
 * @property string|null $request_data
 * @property string|null $response_data
 * @method static \Illuminate\Database\Eloquent\Builder|OpenapiRule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OpenapiRule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OpenapiRule query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OpenapiRule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OpenapiRule whereCrudId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OpenapiRule whereGetPrams($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OpenapiRule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OpenapiRule whereMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OpenapiRule whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OpenapiRule wherePathPrams($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OpenapiRule wherePid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OpenapiRule wherePostPrams($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OpenapiRule whereRequestData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OpenapiRule whereResponseData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OpenapiRule whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OpenapiRule whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OpenapiRule whereUrl($value)
 */
	class OpenapiRule extends \Eloquent {}
}

namespace App\Http\Model\Other{
/**
 * 导出记录.
 *
 * @property int $id
 * @property int $uid 用户ID
 * @property string $name 文件名
 * @property int $success_count 成功数量
 * @property int $fail_count 失败数量
 * @property int $status 状态:0、待处理,1、成功,2、失败
 * @property int $types 类型:0、导出;1、导入;
 * @property string $file_path 文件路径
 * @property int $file_status 文件状态:0、正常,1、删除
 * @property string|null $fail_msg 失败原因
 * @property string|null $module 关联业务模块
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Http\Model\Admin\Admin|null $admin
 * @property-read \App\Http\Model\System\Attach|null $attach
 * @method static \Illuminate\Database\Eloquent\Builder|ExportRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExportRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExportRecord query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExportRecord uid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExportRecord whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExportRecord whereFailCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExportRecord whereFailMsg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExportRecord whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExportRecord whereFileStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExportRecord whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExportRecord whereModule($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExportRecord whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExportRecord whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExportRecord whereSuccessCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExportRecord whereTypes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExportRecord whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExportRecord whereUpdatedAt($value)
 */
	class ExportRecord extends \Eloquent {}
}

namespace App\Http\Model\Other{
/**
 * 任务
 * Class Task.
 *
 * @property int $id 自增id
 * @property int $entid 企业id，0=全局任务
 * @property string $name 任务名称
 * @property string $period 任务执行类型
 * @property int $persist 是否永久执行
 * @property int $run_count 执行次数最少1次
 * @property int $exe_count 已经执行次数
 * @property string $class_name 执行任务类名
 * @property string $action 执行任务方法名
 * @property mixed $interval 执行时间,一般为json格式
 * @property string|null $end_time 结束时间
 * @property int $rate 间隔时间：n天、n月、n年、n周
 * @property mixed $parameter 执行参数一般为json格式
 * @property string $uniqued 任务唯一值
 * @property \Illuminate\Support\Carbon|null $delete 是否删除
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read array $event
 * @method static \Illuminate\Database\Eloquent\Builder|Task newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Task newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Task onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Task query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task uniqued($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereClassName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereDelete($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereExeCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereInterval($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereParameter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task wherePeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task wherePersist($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereRunCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereUniqued($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Task withoutTrashed()
 */
	class Task extends \Eloquent {}
}

namespace App\Http\Model\Other{
/**
 * 任务执行记录
 * Class TaskRunRecord.
 *
 * @property int $id 自增id
 * @property int $task_id
 * @property string $message 错误提示
 * @property int $line 错误行数
 * @property string $files 错误文件
 * @property int $status 1=执行成功;0=执行失败;-1=未执行
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|TaskRunRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TaskRunRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TaskRunRecord query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskRunRecord whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskRunRecord whereFiles($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskRunRecord whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskRunRecord whereLine($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskRunRecord whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskRunRecord whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskRunRecord whereTaskId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskRunRecord whereUpdatedAt($value)
 */
	class TaskRunRecord extends \Eloquent {}
}

namespace App\Http\Model\Other{
/**
 * 视图搜索.
 *
 * @property int $id id
 * @property int $uid 关联用户ID
 * @property string $title 视图名称
 * @property string $content 视图内容
 * @property string $category 视图分类(参考枚举类目)
 * @property int $types 视图类型：0-系统 1-个人
 * @property int $is_public 是否公开：0-否 1-是
 * @property int $sort 排序
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Http\Model\Admin\Admin|null $admin
 * @method static \Illuminate\Database\Eloquent\Builder|ViewSearch newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ViewSearch newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ViewSearch query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ViewSearch title($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ViewSearch whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ViewSearch whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ViewSearch whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ViewSearch whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ViewSearch whereIsPublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ViewSearch whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ViewSearch whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ViewSearch whereTypes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ViewSearch whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ViewSearch whereUpdatedAt($value)
 */
	class ViewSearch extends \Eloquent {}
}

namespace App\Http\Model\Package{
/**
 * App\Http\Model\Package\Upgrade
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Upgrade newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Upgrade newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Upgrade query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 */
	class Upgrade extends \Eloquent {}
}

namespace App\Http\Model\Package{
/**
 * App\Http\Model\Package\UpgradeRecord
 *
 * @method static \Illuminate\Database\Eloquent\Builder|UpgradeRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UpgradeRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UpgradeRecord query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 */
	class UpgradeRecord extends \Eloquent {}
}

namespace App\Http\Model\Position{
/**
 * 职级类别
 * Class Category.
 *
 * @property int $id 自增id
 * @property int $entid
 * @property string $name 职级类别名称
 * @property int $number 职级数
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Category|null $cate
 * @property false|string[] $path
 * @method static \Illuminate\Database\Eloquent\Builder|Category entid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category nameLike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category notId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereUpdatedAt($value)
 */
	class Category extends \Eloquent {}
}

namespace App\Http\Model\Position{
/**
 * 企业岗位
 * Class Job.
 *
 * @property int $id 自增id
 * @property int $user_id 用户id
 * @property int $entid
 * @property string $name 职位名称
 * @property int $cate_id
 * @property int $rank_id
 * @property int $card_id
 * @property int $job_count 岗位人数
 * @property string $describe 岗位描述
 * @property string $duty 岗位职责
 * @property int $status 状态:0=关闭;1=开启
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Http\Model\Admin\Admin|null $card
 * @property-read \App\Http\Model\Position\Category|null $cate
 * @property false|string[] $path
 * @property-read \App\Http\Model\Position\Position|null $rank
 * @method static \Illuminate\Database\Eloquent\Builder|Job cateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Job entid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Job id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Job nameLike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Job newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Job newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Job query()
 * @method static \Illuminate\Database\Eloquent\Builder|Job rankId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Job whereCardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Job whereCateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Job whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Job whereDescribe($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Job whereDuty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Job whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Job whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Job whereJobCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Job whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Job whereRankId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Job whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Job whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Job whereUserId($value)
 */
	class Job extends \Eloquent {}
}

namespace App\Http\Model\Position{
/**
 * 职级等级
 * Class Level.
 *
 * @property int $id 自增id
 * @property int $entid
 * @property string $salary 薪资范围
 * @property int $min_level 职等最小值
 * @property int $max_level 职等最大值
 * @method static \Illuminate\Database\Eloquent\Builder|Level entid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Level id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Level newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Level newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Level query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Level whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Level whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Level whereMaxLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Level whereMinLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Level whereSalary($value)
 */
	class Level extends \Eloquent {}
}

namespace App\Http\Model\Position{
/**
 * 企业职级
 * Class Position.
 *
 * @property int $id 自增id
 * @property string $name 职级名称
 * @property int $entid
 * @property int $cate_id
 * @property int $card_id
 * @property string $alias 职级别名
 * @property string $info 职级描述
 * @property int $number 职级人数
 * @property int $status 状态:1=开启,0=关闭
 * @property int $sort 排序
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Http\Model\Admin\Admin|null $card
 * @property-read \App\Http\Model\Position\Category|null $cate
 * @method static \Illuminate\Database\Eloquent\Builder|Position cateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Position entid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Position id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Position nameLike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Position newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Position newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Position notid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Position query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Position whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Position whereCardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Position whereCateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Position whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Position whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Position whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Position whereInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Position whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Position whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Position whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Position whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Position whereUpdatedAt($value)
 */
	class Position extends \Eloquent {}
}

namespace App\Http\Model\Position{
/**
 * 职级类型
 * Class Relation.
 *
 * @property int $id 自增id
 * @property int $entid 企业ID
 * @property int $level_id
 * @property int $cate_id
 * @property int $rank_id
 * @property int $number 职级数
 * @property int $status 状态:1=开启,0=关闭
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\Position\Job> $job
 * @property-read int|null $job_count
 * @property-read \App\Http\Model\Position\Position|null $rank
 * @method static \Illuminate\Database\Eloquent\Builder|Relation cateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Relation entid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Relation levelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Relation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Relation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Relation query()
 * @method static \Illuminate\Database\Eloquent\Builder|Relation rankId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Relation whereCateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Relation whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Relation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Relation whereLevelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Relation whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Relation whereRankId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Relation whereStatus($value)
 */
	class Relation extends \Eloquent {}
}

namespace App\Http\Model\Program{
/**
 * 项目
 * Class Program.
 *
 * @property int $id id
 * @property string $name 名称
 * @property string $ident 编号
 * @property int $uid 负责人
 * @property int $eid 关联客户
 * @property int $cid 关联合同
 * @property int $creator_uid 创建人ID
 * @property string|null $start_date 开始时间
 * @property string|null $end_date 结束时间
 * @property int $status 项目状态：0：正常；1：暂停；2：关闭；
 * @property string $describe 项目描述
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\Admin\Admin> $admins
 * @property-read int|null $admins_count
 * @property-read \App\Http\Model\Customer\Order|null $contract
 * @property-read \App\Http\Model\Customer\Customer|null $customer
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\Admin\Admin> $members
 * @property-read int|null $members_count
 * @method static \Illuminate\Database\Eloquent\Builder|Program adminUids($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Program cid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Program eid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Program id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Program nameLike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Program newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Program newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Program onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Program query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Program uid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Program uidOrCreatorUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Program whereCid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Program whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Program whereCreatorUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Program whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Program whereDescribe($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Program whereEid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Program whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Program whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Program whereIdent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Program whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Program whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Program whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Program whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Program whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Program withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Program withoutTrashed()
 */
	class Program extends \Eloquent {}
}

namespace App\Http\Model\Program{
/**
 * 项目动态
 * Class ProgramDynamic.
 *
 * @property int $id 自增id
 * @property int $types 动态类型 1：项目；2：任务；
 * @property int $uid 操作人ID
 * @property string $operator 操作人姓名
 * @property int $relation_id 操作ID
 * @property int $action_type 动作类型 1：创建；2：修改；
 * @property string $title 操作说明
 * @property string[] $describe 描述
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Http\Model\Program\ProgramTask|null $task
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramDynamic id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramDynamic newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramDynamic newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramDynamic programId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramDynamic query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramDynamic time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramDynamic uid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramDynamic whereActionType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramDynamic whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramDynamic whereDescribe($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramDynamic whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramDynamic whereOperator($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramDynamic whereRelationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramDynamic whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramDynamic whereTypes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramDynamic whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramDynamic whereUpdatedAt($value)
 */
	class ProgramDynamic extends \Eloquent {}
}

namespace App\Http\Model\Program{
/**
 * 项目成员
 * Class ProgramMember.
 *
 * @property int $id id
 * @property int $program_id 项目ID
 * @property int $uid 项目成员
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramMember id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramMember newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramMember newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramMember onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramMember programId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramMember query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramMember uid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramMember whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramMember whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramMember whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramMember whereProgramId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramMember whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramMember whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramMember withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramMember withoutTrashed()
 */
	class ProgramMember extends \Eloquent {}
}

namespace App\Http\Model\Program{
/**
 * 项目任务
 * Class ProgramTask.
 *
 * @property int $id 自增id
 * @property string $name 任务名称
 * @property string $ident 任务编号
 * @property int $pid 父级ID
 * @property string[] $path 路径
 * @property int $top_id 顶级ID
 * @property int $level 级别
 * @property int $program_id 项目ID
 * @property int $version_id 版本ID
 * @property int $creator_uid 创建人ID
 * @property int $uid 负责人
 * @property int $status 项目状态：0：未处理；1：进行中；2：已解决；3：已验收；4：已拒绝；
 * @property int $priority 优先级：1：紧急；2：高；3：中；4：低；
 * @property string|null $plan_start 计划开始
 * @property string|null $plan_end 计划结束
 * @property int $sort 排序
 * @property string $describe 任务描述
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\Admin\Admin> $admins
 * @property-read int|null $admins_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\Admin\Admin> $creator
 * @property-read int|null $creator_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\Admin\Admin> $members
 * @property-read int|null $members_count
 * @property-read ProgramTask|null $parent
 * @property-read \App\Http\Model\Program\Program|null $program
 * @property-read \App\Http\Model\Program\ProgramVersion|null $version
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTask admins($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTask authUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTask createdAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTask id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTask nameLike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTask newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTask newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTask onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTask path($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTask pid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTask pidNot($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTask planEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTask planEndNotOrNull($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTask planStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTask planStartNotOrNull($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTask priority($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTask programId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTask programIdNot($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTask query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTask status($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTask statusNot($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTask topId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTask uid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTask uidNot($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTask uidOrCreatorUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTask versionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTask versionIdNot($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTask whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTask whereCreatorUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTask whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTask whereDescribe($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTask whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTask whereIdent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTask whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTask whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTask wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTask wherePid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTask wherePlanEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTask wherePlanStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTask wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTask whereProgramId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTask whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTask whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTask whereTopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTask whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTask whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTask whereVersionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTask withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTask withoutTrashed()
 */
	class ProgramTask extends \Eloquent {}
}

namespace App\Http\Model\Program{
/**
 * 项目任务评论
 * Class ProgramTaskComment.
 *
 * @property int $id 自增id
 * @property int $task_id 任务ID
 * @property int $pid 父级ID
 * @property int $reply_uid 回复评论人ID
 * @property int $uid 评论人ID
 * @property string $describe 描述
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Http\Model\Admin\Admin|null $member
 * @property-read \App\Http\Model\Admin\Admin|null $reply_member
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTaskComment id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTaskComment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTaskComment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTaskComment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTaskComment pid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTaskComment query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTaskComment taskId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTaskComment uid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTaskComment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTaskComment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTaskComment whereDescribe($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTaskComment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTaskComment wherePid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTaskComment whereReplyUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTaskComment whereTaskId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTaskComment whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTaskComment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTaskComment withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTaskComment withoutTrashed()
 */
	class ProgramTaskComment extends \Eloquent {}
}

namespace App\Http\Model\Program{
/**
 * 项目任务成员
 * Class ProgramTaskMember.
 *
 * @property int $id id
 * @property int $task_id 任务ID
 * @property int $uid 项目成员
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTaskMember id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTaskMember newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTaskMember newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTaskMember onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTaskMember query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTaskMember taskId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTaskMember uid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTaskMember whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTaskMember whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTaskMember whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTaskMember whereTaskId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTaskMember whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTaskMember whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTaskMember withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramTaskMember withoutTrashed()
 */
	class ProgramTaskMember extends \Eloquent {}
}

namespace App\Http\Model\Program{
/**
 * 项目版本
 * Class ProgramVersion.
 *
 * @property int $id 自增id
 * @property int $program_id 项目ID
 * @property string $name 版本名称
 * @property int $creator_uid 创建人ID
 * @property int $sort 排序
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramVersion creatorUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramVersion id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramVersion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramVersion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramVersion onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramVersion programId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramVersion query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramVersion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramVersion whereCreatorUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramVersion whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramVersion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramVersion whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramVersion whereProgramId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramVersion whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramVersion whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramVersion withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramVersion withoutTrashed()
 */
	class ProgramVersion extends \Eloquent {}
}

namespace App\Http\Model\Schedule{
/**
 * 日程表.
 *
 * @property int $id 自增id
 * @property int $uid 企业用户ID
 * @property int $cid 日程分类ID
 * @property string $color 日程分类颜色
 * @property string $title 日程标题
 * @property string $content 日程内容
 * @property int $all_day 是否全天：1、是；0、否；
 * @property mixed|null $start_time 开始时间
 * @property mixed|null $end_time 结束时间
 * @property int $period 重复周期：0、不重复；1、日；2、月；3、年；
 * @property int $rate 重复频率
 * @property array $days 重复星期/日期
 * @property \App\Http\Model\Schedule\ScheduleRemind|null $remind 是否提醒：1、是；0、否；
 * @property mixed|null $fail_time 结束时间
 * @property int $pid 关联日程ID
 * @property int $link_id 关联业务ID
 * @property int $status 日程状态：0、待定；1、接受；2、拒绝；3、完成
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Http\Model\Admin\Admin|null $master
 * @property-read \App\Http\Model\Schedule\ScheduleRemind|null $remindData
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\Schedule\ScheduleUser> $scheduleUser
 * @property-read int|null $schedule_user_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\Schedule\ScheduleUser> $schedule_user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\Schedule\ScheduleTask> $task
 * @property-read int|null $task_count
 * @property-read \App\Http\Model\Schedule\ScheduleTask|null $taskOne
 * @property-read \App\Http\Model\Schedule\ScheduleType|null $type
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\Admin\Admin> $user
 * @property-read int|null $user_count
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule cid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule existsUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule linkId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule query()
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule status($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereAllDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereCid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereFailTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereLinkId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule wherePeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule wherePid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereRemind($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereUpdatedAt($value)
 */
	class Schedule extends \Eloquent {}
}

namespace App\Http\Model\Schedule{
/**
 * 日程提醒记录表.
 *
 * @property int $id 自增id
 * @property string $uid 用户ID
 * @property int $schedule_id 提醒ID
 * @property int $status 完成状态：1、是；0、否；
 * @property string|null $remind_day 提醒日期
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleRecord query()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleRecord time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleRecord whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleRecord whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleRecord whereRemindDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleRecord whereScheduleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleRecord whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleRecord whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleRecord whereUpdatedAt($value)
 */
	class ScheduleRecord extends \Eloquent {}
}

namespace App\Http\Model\Schedule{
/**
 * 日程提醒表.
 *
 * @property int $id 自增id
 * @property int $sid 关联日程ID
 * @property string $uid 用户ID
 * @property int $entid 企业ID
 * @property string $types 类型：user、用户；assess、考核；
 * @property string $content 待办内容
 * @property string $mark 备注信息
 * @property int $period 重复周期：0、不重复；1、日；2、月；3、年；
 * @property int $rate 重复频率
 * @property string|null $remind_day 提醒日期
 * @property string|null $remind_time 提醒时间
 * @property mixed $days 重复星期/天
 * @property mixed|null $end_time 结束日期：0、永不结束；
 * @property string $uniqued 定时任务唯一值
 * @property mixed|null $last_time 上次提醒日期
 * @property int $is_remind 是否提醒过0=无，1=有
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property mixed|null $deleted_at
 * @property-read \App\Http\Model\Schedule\Schedule|null $schedule
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleRemind content($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleRemind endTimeNot($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleRemind entid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleRemind newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleRemind newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleRemind periodNot($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleRemind query()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleRemind remind($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleRemind sid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleRemind time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleRemind types($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleRemind uid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleRemind whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleRemind whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleRemind whereDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleRemind whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleRemind whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleRemind whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleRemind whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleRemind whereIsRemind($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleRemind whereLastTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleRemind whereMark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleRemind wherePeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleRemind whereRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleRemind whereRemindDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleRemind whereRemindTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleRemind whereSid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleRemind whereTypes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleRemind whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleRemind whereUniqued($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleRemind whereUpdatedAt($value)
 */
	class ScheduleRemind extends \Eloquent {}
}

namespace App\Http\Model\Schedule{
/**
 * 日程表.
 *
 * @property int $id 主键ID
 * @property int $uid 企业用户ID
 * @property int $pid 关联日程ID
 * @property int $reply_id 关联评论ID
 * @property int $to_uid 回复指定人员ID
 * @property mixed|null $start_time 任务开始时间
 * @property mixed|null $end_time 任务结束时间
 * @property string $content 评论内容
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\System\Attach> $files
 * @property-read int|null $files_count
 * @property-read \App\Http\Model\Admin\Admin|null $from_user
 * @property-read \App\Http\Model\Admin\Admin|null $to_user
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleReply newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleReply newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleReply pid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleReply query()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleReply replyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleReply time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleReply timeZone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleReply whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleReply whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleReply whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleReply whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleReply wherePid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleReply whereReplyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleReply whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleReply whereToUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleReply whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleReply whereUpdatedAt($value)
 */
	class ScheduleReply extends \Eloquent {}
}

namespace App\Http\Model\Schedule{
/**
 * 日程表.
 *
 * @property int $id 主键ID
 * @property int $uid 企业用户ID
 * @property int $pid 关联日程ID
 * @property mixed|null $start_time 任务开始时间
 * @property mixed|null $end_time 任务结束时间
 * @property int $status 日程状态：0、待定；1、接受；2、拒绝；3、完成；
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleTask newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleTask newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleTask query()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleTask time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleTask whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleTask whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleTask whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleTask wherePid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleTask whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleTask whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleTask whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleTask whereUpdatedAt($value)
 */
	class ScheduleTask extends \Eloquent {}
}

namespace App\Http\Model\Schedule{
/**
 * 日程类型表.
 *
 * @property int $id 自增id
 * @property int $user_id 用户id
 * @property string $uid 用户UID
 * @property int $entid 企业ID
 * @property string $name 分类名称
 * @property int $sort 排序
 * @property string $color 日程分类颜色
 * @property string $info 分类简介
 * @property int $is_public 是否公共分类
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleType entLike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleType query()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleType time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleType uidLike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleType useridLike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleType whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleType whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleType whereInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleType whereIsPublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleType whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleType whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleType whereUserId($value)
 */
	class ScheduleType extends \Eloquent {}
}

namespace App\Http\Model\Schedule{
/**
 * 日程表.
 *
 * @property int $id 主键ID
 * @property int $uid 企业用户ID
 * @property int $schedule_id 关联日程ID
 * @property int $is_master 是否为组织人0=否，1=是
 * @property-read \App\Http\Model\Schedule\Schedule|null $schedule
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleUser query()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleUser scheduleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleUser time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleUser todo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleUser uid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleUser whereIsMaster($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleUser whereScheduleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleUser whereUid($value)
 */
	class ScheduleUser extends \Eloquent {}
}

namespace App\Http\Model\Storage{
/**
 * 物资管理.
 *
 * @property int $id 自增id
 * @property int $cid
 * @property int $entid 企业ID
 * @property string $creater 创建用户ID
 * @property string $name 物资名称
 * @property string $specs 物资规格
 * @property string $factory 生产厂家
 * @property string $units 计量单位
 * @property string $mark 备注
 * @property string $remark 重要信息
 * @property int $stock 库存
 * @property int $used 领用数量
 * @property string $number 物资编号
 * @property int $types 物资类型：0、消耗物资；1、固定物资；
 * @property int $status 物资状态：0、正常；1、已领用；3、维修中；4、已报废；
 * @property int $link_id 关联记录ID
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Http\Model\Storage\StorageCategory|null $cate
 * @property-read \App\Http\Model\Frame\Frame|null $receiveFrame
 * @property-read \App\Http\Model\Admin\Admin|null $receiveUser
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\Storage\StorageRecord> $record
 * @property-read int|null $record_count
 * @method static \Illuminate\Database\Eloquent\Builder|Storage cid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Storage distinct($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Storage entid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Storage id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Storage ids($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Storage nameLike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Storage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Storage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Storage onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Storage query()
 * @method static \Illuminate\Database\Eloquent\Builder|Storage status($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Storage stock($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Storage time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Storage types($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Storage whereCid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Storage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Storage whereCreater($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Storage whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Storage whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Storage whereFactory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Storage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Storage whereLinkId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Storage whereMark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Storage whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Storage whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Storage whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Storage whereSpecs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Storage whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Storage whereStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Storage whereTypes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Storage whereUnits($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Storage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Storage whereUsed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Storage withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Storage withoutTrashed()
 */
	class Storage extends \Eloquent {}
}

namespace App\Http\Model\Storage{
/**
 * 物资分类.
 *
 * Class StorageCategory.
 *
 * @property int $id 分类自增id
 * @property int $pid 父级ID
 * @property string $cate_name 分类名称
 * @property false|string[] $path 路径
 * @property int $sort 排序
 * @property int $level 等级
 * @property int $type 分类类型:0、消耗物资；1、固定物资；
 * @property int $entid 企业ID
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|StorageCategory cateName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StorageCategory entid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StorageCategory nameLike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StorageCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StorageCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StorageCategory notId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StorageCategory pid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StorageCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StorageCategory type($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StorageCategory whereCateName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StorageCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StorageCategory whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StorageCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StorageCategory whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StorageCategory wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StorageCategory wherePid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StorageCategory whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StorageCategory whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StorageCategory whereUpdatedAt($value)
 */
	class StorageCategory extends \Eloquent {}
}

namespace App\Http\Model\Storage{
/**
 * 物资记录.
 *
 * Class StorageRecord.
 *
 * @property int $id 自增id
 * @property int $user_id 用户id
 * @property int $operator 操作用户id
 * @property int $storage_id
 * @property int|null $storage_type 物资类型
 * @property int $entid 企业ID
 * @property \App\Http\Model\Admin\Admin|null $creater 创建用户ID
 * @property int $card_id 关联用户ID
 * @property int $frame_id 关联组织架构ID
 * @property string $info 操作说明
 * @property string $mark 备注信息
 * @property string $price 单价
 * @property string $total 总价
 * @property int $num 物资数量
 * @property int $types 操作类型：0、入库；1、领用；2、归还；3、维修；4、报废；5、维修完成；
 * @property int $status 当前物资状态
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Http\Model\Admin\Admin|null $card
 * @property-read \App\Http\Model\Frame\Frame|null $frame
 * @property-read \App\Http\Model\Storage\Storage|null $storage
 * @method static \Illuminate\Database\Eloquent\Builder|StorageRecord cardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StorageRecord creater($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StorageRecord entid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StorageRecord frameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StorageRecord ids($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StorageRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StorageRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StorageRecord onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|StorageRecord query()
 * @method static \Illuminate\Database\Eloquent\Builder|StorageRecord status($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StorageRecord storageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StorageRecord storageType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StorageRecord time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StorageRecord types($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StorageRecord whereCardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StorageRecord whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StorageRecord whereCreater($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StorageRecord whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StorageRecord whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StorageRecord whereFrameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StorageRecord whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StorageRecord whereInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StorageRecord whereMark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StorageRecord whereNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StorageRecord whereOperator($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StorageRecord wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StorageRecord whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StorageRecord whereStorageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StorageRecord whereStorageType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StorageRecord whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StorageRecord whereTypes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StorageRecord whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StorageRecord whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StorageRecord withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|StorageRecord withoutTrashed()
 */
	class StorageRecord extends \Eloquent {}
}

namespace App\Http\Model\System{
/**
 * Class Attach.
 *
 * @property int $id 附件ID
 * @property int $entid 分后台ID
 * @property string $uid 上传用户uid
 * @property string $name 附件名称
 * @property string $real_name 附件原始名称
 * @property string $att_dir 附件路径
 * @property string $thumb_dir 附件压缩路径
 * @property string $att_size 附件大小
 * @property string $att_type 附件类型
 * @property string $file_ext 文件扩展名
 * @property int $cid 分类ID
 * @property int $up_type 上传方式：1、本地；2、七牛云；3、OSS；4、COS。
 * @property int $way 来源：1、总后台；2、分后台；3、用户。
 * @property int $relation_type 模块:1、汇报；
 * @property int $relation_id 模块ID
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Http\Model\Admin\Admin|null $card
 * @method static \Illuminate\Database\Eloquent\Builder|Attach cid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attach entid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attach entids($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attach id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attach name($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attach newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Attach newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Attach query()
 * @method static \Illuminate\Database\Eloquent\Builder|Attach relationType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attach whereAttDir($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attach whereAttSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attach whereAttType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attach whereCid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attach whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attach whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attach whereFileExt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attach whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attach whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attach whereRealName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attach whereRelationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attach whereRelationType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attach whereThumbDir($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attach whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attach whereUpType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attach whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attach whereWay($value)
 */
	class Attach extends \Eloquent {}
}

namespace App\Http\Model\System{
/**
 * 分类.
 *
 * @property int $id 分类自增id
 * @property int $pid 父级ID
 * @property string $cate_name 分类名称
 * @property false|string[] $path 路径
 * @property int $sort 排序
 * @property string $pic 图标
 * @property int $is_show 是否显示
 * @property int $level 等级
 * @property string $type 分类类型
 * @property string $keyword 标记词
 * @property int $entid 平台编号；0、总后台；
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Category cateName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category entid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category eqCateName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category isShow($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category keyword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category ltLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category notId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category pid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category type($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereCateName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereIsShow($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereKeyword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category wherePic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category wherePid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereUpdatedAt($value)
 */
	class Category extends \Eloquent {}
}

namespace App\Http\Model\System{
/**
 * 省市区.
 *
 * @property int $id 自增id
 * @property int $city_id 城市ID
 * @property int $level 省市级别
 * @property int $parent_id 父级id
 * @property string $area_code 区号
 * @property string $name 名称
 * @property string $merger_name 合并名称
 * @property string $lng 经度
 * @property string $lat 纬度
 * @property int $is_show 是否展示
 * @property-read \Illuminate\Database\Eloquent\Collection<int, City> $children
 * @property-read int|null $children_count
 * @method static \Illuminate\Database\Eloquent\Builder|City nameLike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|City newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|City query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereAreaCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereIsShow($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereLat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereLng($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereMergerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereParentId($value)
 */
	class City extends \Eloquent {}
}

namespace App\Http\Model\System{
/**
 * Class Log.
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Log newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Log newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Log query()
 * @method static \Illuminate\Database\Eloquent\Builder|Log time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Log userName($value)
 */
	class Log extends \Eloquent {}
}

namespace App\Http\Model\System{
/**
 * 菜单模型
 * Class Menus.
 *
 * @property int $id 菜单自增id
 * @property int $pid 上级菜单ID
 * @property string $icon 按钮图标
 * @property mixed $menu_name 按钮名
 * @property string $api api请求地址
 * @property string $methods 请求方式POST GET PUT DELETE
 * @property string $unique_auth 前台唯一标识
 * @property string $menu_path 前端路由路径
 * @property int $menu_type 路由类型：0、系统；1、实体；
 * @property int $crud_id 实体id
 * @property string $uni_path 移动端路径
 * @property string $uni_img 移动端图标
 * @property int|null $position 菜单位置 0=侧方1=顶部
 * @property false|string[] $paths 路径
 * @property string $component 前端路径
 * @property int $level
 * @property mixed $other 其他参数
 * @property int $sort 排序
 * @property int $entid 菜单归属 0=总后台
 * @property string $type 类型：M、菜单；B、按钮；A、接口；
 * @property int $is_show 是否为隐藏菜单供前台使用
 * @property int $status 菜单状态 1=开启,0=关闭
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property mixed|null $deleted_at
 * @property string|null $uniqued 菜单唯一标识
 * @property string|null $parent_uniqued 父菜单唯一标识
 * @property int $crud_app_id 低代码的应用id
 * @property int $crud_dashboard_id 低代码图表的id
 * @property-read \Staudenmeir\LaravelAdjacencyList\Eloquent\Collection<int, Menus> $children
 * @property-read int|null $children_count
 * @property-read mixed $id_path
 * @property-read Menus|null $parent
 * @method static \Staudenmeir\LaravelAdjacencyList\Eloquent\Collection<int, static> all($columns = ['*'])
 * @method static \Staudenmeir\LaravelAdjacencyList\Eloquent\Builder|Menus api($value)
 * @method static \Staudenmeir\LaravelAdjacencyList\Eloquent\Builder|Menus apiLike($value)
 * @method static \Staudenmeir\LaravelAdjacencyList\Eloquent\Builder|Menus breadthFirst()
 * @method static \Staudenmeir\LaravelAdjacencyList\Eloquent\Builder|Menus crudIds($value)
 * @method static \Staudenmeir\LaravelAdjacencyList\Eloquent\Builder|Menus depthFirst()
 * @method static \Staudenmeir\LaravelAdjacencyList\Eloquent\Builder|Menus entid($value)
 * @method static \Staudenmeir\LaravelAdjacencyList\Eloquent\Collection<int, static> get($columns = ['*'])
 * @method static \Staudenmeir\LaravelAdjacencyList\Eloquent\Builder|Menus getExpressionGrammar()
 * @method static \Staudenmeir\LaravelAdjacencyList\Eloquent\Builder|Menus hasChildren()
 * @method static \Staudenmeir\LaravelAdjacencyList\Eloquent\Builder|Menus hasParent()
 * @method static \Staudenmeir\LaravelAdjacencyList\Eloquent\Builder|Menus id($value)
 * @method static \Staudenmeir\LaravelAdjacencyList\Eloquent\Builder|Menus ids($value)
 * @method static \Staudenmeir\LaravelAdjacencyList\Eloquent\Builder|Menus isLeaf()
 * @method static \Staudenmeir\LaravelAdjacencyList\Eloquent\Builder|Menus isRoot()
 * @method static \Staudenmeir\LaravelAdjacencyList\Eloquent\Builder|Menus menuName($value)
 * @method static \Staudenmeir\LaravelAdjacencyList\Eloquent\Builder|Menus menuPath($value)
 * @method static \Staudenmeir\LaravelAdjacencyList\Eloquent\Builder|Menus nameLike($value)
 * @method static \Staudenmeir\LaravelAdjacencyList\Eloquent\Builder|Menus newModelQuery()
 * @method static \Staudenmeir\LaravelAdjacencyList\Eloquent\Builder|Menus newQuery()
 * @method static \Staudenmeir\LaravelAdjacencyList\Eloquent\Builder|Menus notId($value)
 * @method static \Staudenmeir\LaravelAdjacencyList\Eloquent\Builder|Menus pathLike($value)
 * @method static \Staudenmeir\LaravelAdjacencyList\Eloquent\Builder|Menus pid($value)
 * @method static \Staudenmeir\LaravelAdjacencyList\Eloquent\Builder|Menus query()
 * @method static \Staudenmeir\LaravelAdjacencyList\Eloquent\Builder|BaseModel time($value)
 * @method static \Staudenmeir\LaravelAdjacencyList\Eloquent\Builder|Menus tree($maxDepth = null)
 * @method static \Staudenmeir\LaravelAdjacencyList\Eloquent\Builder|Menus treeOf(callable $constraint, $maxDepth = null)
 * @method static \Staudenmeir\LaravelAdjacencyList\Eloquent\Builder|Menus type($value)
 * @method static \Staudenmeir\LaravelAdjacencyList\Eloquent\Builder|Menus uniPath($value)
 * @method static \Staudenmeir\LaravelAdjacencyList\Eloquent\Builder|Menus uniqueAuth($value)
 * @method static \Staudenmeir\LaravelAdjacencyList\Eloquent\Builder|Menus uniqued($value)
 * @method static \Staudenmeir\LaravelAdjacencyList\Eloquent\Builder|Menus whereApi($value)
 * @method static \Staudenmeir\LaravelAdjacencyList\Eloquent\Builder|Menus whereComponent($value)
 * @method static \Staudenmeir\LaravelAdjacencyList\Eloquent\Builder|Menus whereCreatedAt($value)
 * @method static \Staudenmeir\LaravelAdjacencyList\Eloquent\Builder|Menus whereCrudAppId($value)
 * @method static \Staudenmeir\LaravelAdjacencyList\Eloquent\Builder|Menus whereCrudDashboardId($value)
 * @method static \Staudenmeir\LaravelAdjacencyList\Eloquent\Builder|Menus whereCrudId($value)
 * @method static \Staudenmeir\LaravelAdjacencyList\Eloquent\Builder|Menus whereDeletedAt($value)
 * @method static \Staudenmeir\LaravelAdjacencyList\Eloquent\Builder|Menus whereDepth($operator, $value = null)
 * @method static \Staudenmeir\LaravelAdjacencyList\Eloquent\Builder|Menus whereEntid($value)
 * @method static \Staudenmeir\LaravelAdjacencyList\Eloquent\Builder|Menus whereIcon($value)
 * @method static \Staudenmeir\LaravelAdjacencyList\Eloquent\Builder|Menus whereId($value)
 * @method static \Staudenmeir\LaravelAdjacencyList\Eloquent\Builder|Menus whereIsShow($value)
 * @method static \Staudenmeir\LaravelAdjacencyList\Eloquent\Builder|Menus whereLevel($value)
 * @method static \Staudenmeir\LaravelAdjacencyList\Eloquent\Builder|Menus whereMenuName($value)
 * @method static \Staudenmeir\LaravelAdjacencyList\Eloquent\Builder|Menus whereMenuPath($value)
 * @method static \Staudenmeir\LaravelAdjacencyList\Eloquent\Builder|Menus whereMenuType($value)
 * @method static \Staudenmeir\LaravelAdjacencyList\Eloquent\Builder|Menus whereMethods($value)
 * @method static \Staudenmeir\LaravelAdjacencyList\Eloquent\Builder|Menus whereOther($value)
 * @method static \Staudenmeir\LaravelAdjacencyList\Eloquent\Builder|Menus whereParentUniqued($value)
 * @method static \Staudenmeir\LaravelAdjacencyList\Eloquent\Builder|Menus wherePaths($value)
 * @method static \Staudenmeir\LaravelAdjacencyList\Eloquent\Builder|Menus wherePid($value)
 * @method static \Staudenmeir\LaravelAdjacencyList\Eloquent\Builder|Menus wherePosition($value)
 * @method static \Staudenmeir\LaravelAdjacencyList\Eloquent\Builder|Menus whereSort($value)
 * @method static \Staudenmeir\LaravelAdjacencyList\Eloquent\Builder|Menus whereStatus($value)
 * @method static \Staudenmeir\LaravelAdjacencyList\Eloquent\Builder|Menus whereType($value)
 * @method static \Staudenmeir\LaravelAdjacencyList\Eloquent\Builder|Menus whereUniImg($value)
 * @method static \Staudenmeir\LaravelAdjacencyList\Eloquent\Builder|Menus whereUniPath($value)
 * @method static \Staudenmeir\LaravelAdjacencyList\Eloquent\Builder|Menus whereUniqueAuth($value)
 * @method static \Staudenmeir\LaravelAdjacencyList\Eloquent\Builder|Menus whereUniqued($value)
 * @method static \Staudenmeir\LaravelAdjacencyList\Eloquent\Builder|Menus whereUpdatedAt($value)
 * @method static \Staudenmeir\LaravelAdjacencyList\Eloquent\Builder|Menus withGlobalScopes(array $scopes)
 * @method static \Staudenmeir\LaravelAdjacencyList\Eloquent\Builder|Menus withRelationshipExpression($direction, callable $constraint, $initialDepth, $from = null, $maxDepth = null)
 */
	class Menus extends \Eloquent {}
}

namespace App\Http\Model\System{
/**
 * 菜单.
 *
 * @property int $id 自增ID
 * @property string $name 标题名称
 * @property int $cid 分类id
 * @property string $pc_url PC端地址
 * @property string $uni_url 移动端地址
 * @property string $image 图标
 * @property int $sort 排序，数字越大越在前面
 * @property int $types 菜单类型 0:个人菜单 1:企业菜单
 * @property int $pc_show PC端显示 0:隐藏 1:显示
 * @property int $uni_show 移动端显示 0:隐藏 1:显示
 * @property int $status 状态 0:隐藏 1:显示
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Http\Model\System\Category|null $cate
 * @method static \Illuminate\Database\Eloquent\Builder|Quick cid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Quick nameLike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Quick newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Quick newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Quick notId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Quick query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Quick whereCid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Quick whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Quick whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Quick whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Quick whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Quick wherePcShow($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Quick wherePcUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Quick whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Quick whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Quick whereTypes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Quick whereUniShow($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Quick whereUniUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Quick whereUpdatedAt($value)
 */
	class Quick extends \Eloquent {}
}

namespace App\Http\Model\System{
/**
 * Class Role.
 *
 * @property int $id 权限自增id
 * @property string $role_name 身份管理名称
 * @property mixed $rules 身份管理权限(system_menus主键ID)
 * @property mixed $apis 身份管理接口权限(system_menus主键ID)
 * @property string $type 超级角色类型,空表示总后台
 * @property int $entid 0=总后台,非0为企业后台
 * @property int $level 等级
 * @property int $status 状态
 * @property string|null $uniqued 企业唯一值
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\Company\Assist> $admin
 * @property-read int|null $admin_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\Company\Assist> $user
 * @property-read int|null $user_count
 * @method static \Illuminate\Database\Eloquent\Builder|Role entid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role entidLike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Role notId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role type($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereApis($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereRoleName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereRules($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereUniqued($value)
 */
	class Role extends \Eloquent {}
}

namespace App\Http\Model\Todo{
/**
 * App\Http\Model\Todo\TodoItem
 *
 * @property int $id
 * @property int $user_id 待办归属用户ID
 * @property string $type 待办类型(对应TodoEnum)
 * @property int $source_id 来源表主键ID
 * @property string $title 待办标题
 * @property array|null $extra 类型特有扩展数据
 * @property \Illuminate\Support\Carbon|null $source_created_at 来源记录的created_at(排序依据)
 * @property int $status 1=待办中 2=已完成/已失效
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|TodoItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TodoItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TodoItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TodoItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TodoItem whereExtra($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TodoItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TodoItem whereSourceCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TodoItem whereSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TodoItem whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TodoItem whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TodoItem whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TodoItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TodoItem whereUserId($value)
 */
	class TodoItem extends \Eloquent {}
}

namespace App\Http\Model\User{
/**
 * 个人教育经历.
 *
 * @property int $id 自增id
 * @property string $uid 关联用户ID
 * @property int $resume_id 关联简历ID
 * @property string|null $start_time 开始时间
 * @property string|null $end_time 结束时间
 * @property string $school_name 学校名称
 * @property string $major 所学专业
 * @property string $education 学历
 * @property string $academic 学位
 * @property string $remark 备注
 * @method static \Illuminate\Database\Eloquent\Builder|EducationHistory id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EducationHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EducationHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EducationHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder|EducationHistory resumeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EducationHistory whereAcademic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EducationHistory whereEducation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EducationHistory whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EducationHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EducationHistory whereMajor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EducationHistory whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EducationHistory whereResumeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EducationHistory whereSchoolName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EducationHistory whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EducationHistory whereUid($value)
 */
	class EducationHistory extends \Eloquent {}
}

namespace App\Http\Model\User{
/**
 * 用户表.
 *
 * @deprecated
 * @property string $uid 用户uid
 * @property string $account 用户账号
 * @property string $password 用户密码
 * @property string $only_pwd 用户密码
 * @property string $avatar 用户头像
 * @property string $real_name 用户真实姓名
 * @property string $education 学历
 * @property string $nation 民族
 * @property string $birthplace 籍贯
 * @property string $card_id 身份证号码
 * @property string $province 现住所在省
 * @property string $city 现住所在城市
 * @property string $area 现住所在区
 * @property string $current_address 现住地
 * @property string $home_address 家庭住址
 * @property string $telephone 电话
 * @property string $phone 手机号
 * @property string $email 邮箱
 * @property string $standby_contacts 备用联系人名
 * @property string $standby_contacts_phone 备用联系人手机号
 * @property string $bank 开户行
 * @property string $bank_number 银行卡号
 * @property int $age 年龄
 * @property int $entid 当前登陆所在企业ID
 * @property string|null $last_ip 访问ip
 * @property int $uni_online 移动端登录状态
 * @property string $client_id 连接通道ID
 * @property string|null $scan_key 扫码登录参数
 * @property mixed|null $birthday 生日
 * @property int $login_count 登陆次数
 * @property int $marriage 是否结婚 0 = 未结婚,1=结婚
 * @property int $sex 性别 0=未知,1=男,2=女,3=其他
 * @property int $status 状态：0、锁定；1、正常；
 * @property int $is_init 是否为初始密码
 * @property string $language 语言
 * @property string $remark
 * @property \Illuminate\Support\Carbon|null $delete 是否删除
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|User name($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User notUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|User phone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User phoneLike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User sex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User status($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User uid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAccount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAge($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereArea($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereBank($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereBankNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereBirthday($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereBirthplace($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCurrentAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDelete($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEducation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereHomeAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIsInit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLanguage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLoginCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereMarriage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereNation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereOnlyPwd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRealName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereScanKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereSex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereStandbyContacts($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereStandbyContactsPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTelephone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUniOnline($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|User withoutTrashed()
 */
	class User extends \Eloquent implements \Tymon\JWTAuth\Contracts\JWTSubject, \crmeb\interfaces\TimeDataInterface {}
}

namespace App\Http\Model\User{
/**
 * Class UserCardPerfect.
 *
 * @property int $id 自增ID
 * @property int $creator 邀请人ID
 * @property int $user_id 被邀请人ID
 * @property int $entid 企业ID
 * @property string $uid 关联用户UID
 * @property int $card_id 关联企业用户名片ID
 * @property string|null $uniqued 唯一值
 * @property int $total 可操作量：-1、不限
 * @property int $used 已使用量
 * @property int $status 状态：0、待处理；1、已通过；2、已拒绝；
 * @property int $types 是否绑定用户信息
 * @property string|null $fail_time 失效时间
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Http\Model\Company\Company|null $enterprise
 * @method static \Illuminate\Database\Eloquent\Builder|UserCardPerfect newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserCardPerfect newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserCardPerfect query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserCardPerfect status($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCardPerfect time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCardPerfect total($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCardPerfect whereCardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCardPerfect whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCardPerfect whereCreator($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCardPerfect whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCardPerfect whereFailTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCardPerfect whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCardPerfect whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCardPerfect whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCardPerfect whereTypes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCardPerfect whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCardPerfect whereUniqued($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCardPerfect whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCardPerfect whereUsed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCardPerfect whereUserId($value)
 */
	class UserCardPerfect extends \Eloquent {}
}

namespace App\Http\Model\User{
/**
 * 用户日报
 * Class UserDaily.
 *
 * @property int $daily_id 自增id
 * @property int $entid
 * @property string $uid
 * @property int $user_id 副表(user_enterprise)ID
 * @property mixed|null $finish 工作总结
 * @property mixed|null $plan 工作计划
 * @property string $mark 备注信息
 * @property int $status 提交状态：0、未提交；1、已提交
 * @property int $types 报告类型：0、日报；1、周报；2、月报
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\Attach\SystemAttach> $attachs
 * @property-read int|null $attachs_count
 * @property-read \App\Http\Model\Admin\Admin|null $card
 * @property-read \App\Http\Model\Frame\Frame|null $frame
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\Admin\Admin> $members
 * @property-read int|null $members_count
 * @property-read \App\Http\Model\Company\UserDailyReply|null $reply
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\Company\UserDailyReply> $replys
 * @property-read int|null $replys_count
 * @property-read \App\Http\Model\Admin\Admin|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|UserDaily dailyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDaily day($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDaily entid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDaily finishLike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDaily newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserDaily newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserDaily query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserDaily status($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDaily time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDaily types($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDaily uid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDaily userId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDaily userIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDaily whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDaily whereDailyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDaily whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDaily whereFinish($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDaily whereMark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDaily wherePlan($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDaily whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDaily whereTypes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDaily whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDaily whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDaily whereUserId($value)
 */
	class UserDaily extends \Eloquent {}
}

namespace App\Http\Model\User{
/**
 * 个人教育经历
 * Class UserEducationHistory.
 *
 * @property int $id 自增id
 * @property string $uid 关联用户ID
 * @property int $resume_id 关联简历ID
 * @property string|null $start_time 开始时间
 * @property string|null $end_time 结束时间
 * @property string $school_name 学校名称
 * @property string $major 所学专业
 * @property string $education 学历
 * @property string $academic 学位
 * @property string $remark 备注
 * @method static \Illuminate\Database\Eloquent\Builder|UserEducationHistory id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEducationHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserEducationHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserEducationHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserEducationHistory resumeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEducationHistory whereAcademic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEducationHistory whereEducation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEducationHistory whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEducationHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEducationHistory whereMajor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEducationHistory whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEducationHistory whereResumeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEducationHistory whereSchoolName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEducationHistory whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEducationHistory whereUid($value)
 */
	class UserEducationHistory extends \Eloquent {}
}

namespace App\Http\Model\User{
/**
 * 企业邀请用户加入申请表
 * Class UserEnterpriseApply.
 *
 * @property int $id 自增id
 * @property int $entid 发送人或者企业ID
 * @property string $send_uid
 * @property string $uid 送达人id或者企业
 * @property int $frame_id
 * @property int $status -1=待处理,0=拒绝;1=同意
 * @property int $verify 审核状态：0、待审核；1、已通过；-1、拒绝；
 * @property string|null $perfect_key 邀请完善信息记录关联
 * @property \Illuminate\Support\Carbon|null $created_at 申请时间
 * @property-read \App\Http\Model\Company\Company|null $enterprise
 * @property-read \App\Http\Model\User\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|UserEnterpriseApply entid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEnterpriseApply entids($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEnterpriseApply id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEnterpriseApply newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserEnterpriseApply newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserEnterpriseApply query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserEnterpriseApply status($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEnterpriseApply statusApply($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEnterpriseApply verify($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEnterpriseApply whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEnterpriseApply whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEnterpriseApply whereFrameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEnterpriseApply whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEnterpriseApply wherePerfectKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEnterpriseApply whereSendUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEnterpriseApply whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEnterpriseApply whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEnterpriseApply whereVerify($value)
 */
	class UserEnterpriseApply extends \Eloquent {}
}

namespace App\Http\Model\User{
/**
 * Class UserEnterpriseInvite.
 *
 * @property int $id 自增id
 * @property int $entid 企业ID
 * @property string $send_uid 生成邀请码用户uuid
 * @property int $frame_id 组织架构ID
 * @property int $is_verify 是否需要企业审核：1、是；0、否；
 * @property string $uniqued 链接唯一值
 * @property string|null $perfect_key 邀请完善信息记录标识
 * @property mixed|null $fail_time 失效时间
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|UserEnterpriseInvite entid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEnterpriseInvite frameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEnterpriseInvite newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserEnterpriseInvite newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserEnterpriseInvite query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserEnterpriseInvite status($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEnterpriseInvite whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEnterpriseInvite whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEnterpriseInvite whereFailTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEnterpriseInvite whereFrameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEnterpriseInvite whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEnterpriseInvite whereIsVerify($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEnterpriseInvite wherePerfectKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEnterpriseInvite whereSendUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEnterpriseInvite whereUniqued($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEnterpriseInvite whereUpdatedAt($value)
 */
	class UserEnterpriseInvite extends \Eloquent {}
}

namespace App\Http\Model\User{
/**
 * 笔记
 * Class UserMemorial.
 *
 * @property int $id 自增id
 * @property string $uid 用户ID
 * @property string $title 标题名称
 * @property string $content 内容
 * @property int $pid 分类ID
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Http\Model\User\UserMemorialCategory|null $cate
 * @property-read \App\Http\Model\User\UserMemorialCategory|null $parent
 * @property-read \App\Http\Model\Admin\Admin|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|UserMemorial cateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserMemorial newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserMemorial newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserMemorial pid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserMemorial query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserMemorial time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserMemorial title($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserMemorial uid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserMemorial updatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserMemorial whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserMemorial whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserMemorial whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserMemorial wherePid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserMemorial whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserMemorial whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserMemorial whereUpdatedAt($value)
 */
	class UserMemorial extends \Eloquent {}
}

namespace App\Http\Model\User{
/**
 * 笔记分类
 * Class UserMemorialCategory.
 *
 * @property int $id 自增id
 * @property string $uid 用户ID
 * @property false|string[] $path 路径
 * @property string $name 分类名称
 * @property int $pid 上级ID
 * @property int $types 类型：0、默认；1、用户添加
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read UserMemorialCategory|null $cate
 * @property-read UserMemorialCategory|null $parent
 * @method static \Illuminate\Database\Eloquent\Builder|UserMemorialCategory id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserMemorialCategory name($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserMemorialCategory nameLike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserMemorialCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserMemorialCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserMemorialCategory notId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserMemorialCategory path($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserMemorialCategory pid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserMemorialCategory pidLike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserMemorialCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserMemorialCategory types($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserMemorialCategory uid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserMemorialCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserMemorialCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserMemorialCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserMemorialCategory wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserMemorialCategory wherePid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserMemorialCategory whereTypes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserMemorialCategory whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserMemorialCategory whereUpdatedAt($value)
 */
	class UserMemorialCategory extends \Eloquent {}
}

namespace App\Http\Model\User{
/**
 * 用户快捷入口.
 *
 * @property int $id 自增ID
 * @property int $entid 企业id
 * @property string $uuid 用户uid
 * @property string $pc_menu_id pc端菜单Id
 * @property string $app_menu_id app端菜单Id
 * @property string $statistics_type 统计类型
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|UserQuick newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserQuick newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserQuick query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserQuick whereAppMenuId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserQuick whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserQuick whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserQuick whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserQuick wherePcMenuId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserQuick whereStatisticsType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserQuick whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserQuick whereUuid($value)
 */
	class UserQuick extends \Eloquent {}
}

namespace App\Http\Model\User{
/**
 * 用户消息提醒日志
 * Class UserRemindLog.
 *
 * @property int $id 自增id
 * @property int $entid 企业ID
 * @property int $week 当年的第几周
 * @property int $month 当年的第几月
 * @property int $day 当月的第几天
 * @property int $year 那一年
 * @property int $quarter 第几季度
 * @property string $remind_type 提醒类型
 * @property int $user_id user_enterprise表ID
 * @property int $relation_id 关联id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|UserRemindLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserRemindLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserRemindLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserRemindLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserRemindLog whereDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserRemindLog whereEntid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserRemindLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserRemindLog whereMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserRemindLog whereQuarter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserRemindLog whereRelationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserRemindLog whereRemindType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserRemindLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserRemindLog whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserRemindLog whereWeek($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserRemindLog whereYear($value)
 */
	class UserRemindLog extends \Eloquent {}
}

namespace App\Http\Model\User{
/**
 * 用户简历.
 *
 * @property int $id 自增ID
 * @property string $uid
 * @property string $photo 照片
 * @property string $name 员工姓名
 * @property string $phone 手机号
 * @property string $position 期望职位
 * @property string $birthday 生日
 * @property string $nation 种族
 * @property string $politic 政治面貌
 * @property string $native 籍贯
 * @property string $address 居住地
 * @property int $sex 性别: 0、未知；1、男；2、女；
 * @property int $age 年龄
 * @property int $marriage 婚姻状况:0、未婚；1、已婚；
 * @property int $is_part 是否兼职:1、是；0、否；
 * @property int $work_years 工作年限
 * @property string $spare_name 紧急联系人
 * @property string $spare_tel 紧急联系电话
 * @property string $email 邮箱
 * @property string $work_time 入职时间
 * @property string $trial_time 试用时间
 * @property string $formal_time 转正时间
 * @property string $treaty_time 合同到期时间
 * @property string $social_num 社保账户
 * @property string $fund_num 公积金账户
 * @property string $bank_num 银行卡账户
 * @property string $bank_name 开户行
 * @property string $graduate_name 毕业院校
 * @property string $graduate_date 毕业时间
 * @property string $card_id 身份证号
 * @property string $card_front 身份证正面
 * @property string $card_both 身份证背面
 * @property string $education 学历
 * @property string $education_image 学历证书
 * @property string $acad 学位
 * @property string $acad_image 学位证书
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\User\UserEducationHistory> $educations
 * @property-read int|null $educations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\User\UserWorkHistory> $works
 * @property-read int|null $works_count
 * @method static \Illuminate\Database\Eloquent\Builder|UserResume id($val)
 * @method static \Illuminate\Database\Eloquent\Builder|UserResume ids($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserResume newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserResume newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserResume notId($val)
 * @method static \Illuminate\Database\Eloquent\Builder|UserResume query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserResume uid($val)
 * @method static \Illuminate\Database\Eloquent\Builder|UserResume uids($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserResume whereAcad($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserResume whereAcadImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserResume whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserResume whereAge($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserResume whereBankName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserResume whereBankNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserResume whereBirthday($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserResume whereCardBoth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserResume whereCardFront($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserResume whereCardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserResume whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserResume whereEducation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserResume whereEducationImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserResume whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserResume whereFormalTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserResume whereFundNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserResume whereGraduateDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserResume whereGraduateName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserResume whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserResume whereIsPart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserResume whereMarriage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserResume whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserResume whereNation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserResume whereNative($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserResume wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserResume wherePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserResume wherePolitic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserResume wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserResume whereSex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserResume whereSocialNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserResume whereSpareName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserResume whereSpareTel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserResume whereTreatyTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserResume whereTrialTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserResume whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserResume whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserResume whereWorkTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserResume whereWorkYears($value)
 */
	class UserResume extends \Eloquent {}
}

namespace App\Http\Model\User{
/**
 * 用户日程表
 * Class UserSchedule.
 *
 * @property int $id 自增id
 * @property string $uid 用户ID
 * @property int $schedultid 提醒ID
 * @property int $status 完成状态：1、是；0、否；
 * @property string|null $remind_day 提醒日期
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|UserScheduleRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserScheduleRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserScheduleRecord query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserScheduleRecord remindDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserScheduleRecord schedultid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserScheduleRecord time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserScheduleRecord uid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserScheduleRecord whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserScheduleRecord whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserScheduleRecord whereRemindDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserScheduleRecord whereSchedultid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserScheduleRecord whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserScheduleRecord whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserScheduleRecord whereUpdatedAt($value)
 */
	class UserScheduleRecord extends \Eloquent {}
}

namespace App\Http\Model\User{
/**
 * 用户Token.
 *
 * @property int $id 自增ID
 * @property string $uid 关联用户UID
 * @property string $client 登陆客户端名称
 * @property string $last_ip 登陆IP
 * @property string $mac 登陆MAC地址
 * @property string|null $last_token 上次过期token
 * @property string|null $remember_token 当前登陆token
 * @property string|null $refresh_token_hash 刷新TOKEN哈希
 * @property \Illuminate\Support\Carbon|null $refresh_expires_at 刷新TOKEN失效时间
 * @property \Illuminate\Support\Carbon|null $refresh_last_used_at 刷新TOKEN最后使用时间
 * @property \Illuminate\Support\Carbon|null $refresh_revoked_at 刷新TOKEN撤销时间
 * @property string|null $fail_time 失效时间
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|UserToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserToken query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserToken uid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserToken whereClient($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserToken whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserToken whereFailTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserToken whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserToken whereLastIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserToken whereLastToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserToken whereMac($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserToken whereRefreshExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserToken whereRefreshLastUsedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserToken whereRefreshRevokedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserToken whereRefreshTokenHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserToken whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserToken whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserToken whereUpdatedAt($value)
 */
	class UserToken extends \Eloquent {}
}

namespace App\Http\Model\User{
/**
 * 个人工作经历
 * Class UserWorkHistory.
 *
 * @property int $id 自增id
 * @property string $uid 关联用户ID
 * @property int $resume_id 关联简历ID
 * @property string|null $start_time 开始时间
 * @property string|null $end_time 结束时间
 * @property string $company 所在公司
 * @property string $position 职位
 * @property string $describe 工作描述
 * @property string $quit_reason 离职原因
 * @method static \Illuminate\Database\Eloquent\Builder|UserWorkHistory id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserWorkHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserWorkHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserWorkHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserWorkHistory resumeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserWorkHistory whereCompany($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserWorkHistory whereDescribe($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserWorkHistory whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserWorkHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserWorkHistory wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserWorkHistory whereQuitReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserWorkHistory whereResumeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserWorkHistory whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserWorkHistory whereUid($value)
 */
	class UserWorkHistory extends \Eloquent {}
}

namespace App\Http\Model\WorkExternalContact{
/**
 * 企微群发消息.
 *
 * @property int $id ID
 * @property int $uid 创建用户ID
 * @property int $types 类型:1、群聊消息,0、群发消息,2、朋友圈消息;
 * @property int $is_all 是否全部
 * @property array|null $send_uid 发送用户ID
 * @property array|null $send_group 发送群聊ID
 * @property array|null $send_customer 发送客户ID
 * @property array|null $search 搜索条件
 * @property int $is_modify 是否允许修改
 * @property int $temp_id 素材模板ID
 * @property int $is_timed 是否定时发送
 * @property mixed|null $send_time 发送时间
 * @property int $be_sent 待发送
 * @property int $is_send 已发送
 * @property int $is_sent 已送达
 * @property int $not_sent 未发送
 * @property array|null $sent_uid 已发送员工ID
 * @property array|null $not_sent_uid 未发送员工ID
 * @property string $msg_id 群发消息ID
 * @property array|null $fail_list 无效或无法发送的external_userid或chatid列表
 * @property int $status 状态:0、关闭,1、开启,2、执行中,3、完成;
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Http\Model\Admin\Admin|null $creator
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\Admin\Admin> $not_send_admin
 * @property-read int|null $not_send_admin_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\Admin\Admin> $send_admin
 * @property-read int|null $send_admin_count
 * @property-read \App\Http\Model\WorkExternalContact\WorkMassMessagingTemp|null $temp
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessaging contentLike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessaging newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessaging newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessaging onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessaging query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessaging sendMinute($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessaging status($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessaging time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessaging whereBeSent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessaging whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessaging whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessaging whereFailList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessaging whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessaging whereIsAll($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessaging whereIsModify($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessaging whereIsSend($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessaging whereIsSent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessaging whereIsTimed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessaging whereMsgId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessaging whereNotSent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessaging whereNotSentUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessaging whereSearch($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessaging whereSendCustomer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessaging whereSendGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessaging whereSendTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessaging whereSendUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessaging whereSentUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessaging whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessaging whereTempId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessaging whereTypes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessaging whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessaging whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessaging withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessaging withoutTrashed()
 */
	class WorkMassMessaging extends \Eloquent {}
}

namespace App\Http\Model\WorkExternalContact{
/**
 * 群发消息发送结果.
 *
 * @property int $id ID
 * @property int $task_id 群发任务ID
 * @property string|null $msgid 群发消息ID
 * @property int $uid 用户ID
 * @property string $chat_id 客户群ID
 * @property string $external_userid 客户ID
 * @property string $userid 员工ID
 * @property int $is_comment 是否评论
 * @property int $is_like 是否点赞
 * @property int $status 发送状态：0-未发送 1-已发送 2-因客户不是好友导致发送失败 3-因客户已经收到其他群发消息导致发送失败
 * @property mixed|null $send_time 发送时间
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Http\Model\Admin\Admin|null $admin
 * @property-read \App\Http\Model\Work\WorkGroupChat|null $chat_group
 * @property-read \App\Http\Model\Work\WorkClient|null $customer
 * @property-read \App\Http\Model\WorkExternalContact\WorkMassMessaging|null $messaging
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingResult externalUserid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingResult newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingResult newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingResult query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingResult taskId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingResult whereChatId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingResult whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingResult whereExternalUserid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingResult whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingResult whereIsComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingResult whereIsLike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingResult whereMsgid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingResult whereSendTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingResult whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingResult whereTaskId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingResult whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingResult whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingResult whereUserid($value)
 */
	class WorkMassMessagingResult extends \Eloquent {}
}

namespace App\Http\Model\WorkExternalContact{
/**
 * 群发消息发送任务.
 *
 * @property int $id ID
 * @property int $mass_id 群发任务ID
 * @property int $uid 用户ID
 * @property string $userid 员工ID
 * @property string $msgid 群发消息id
 * @property string $moment_id 朋友圈id
 * @property string $jobid 朋友圈任务id
 * @property int $status 发送状态：0-未发送 2-已发送
 * @property int $sum_count 发送人数
 * @property int $not_send_count 未发送人数
 * @property int $success_count 成功人数
 * @property int $fail_count 失败人数
 * @property int $types 类型:1、群聊消息,0、群发消息,2、朋友圈消息;
 * @property mixed|null $fail_list 无效或无法发送的external_userid或chatid列表
 * @property mixed|null $send_time 发送时间
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingTask newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingTask newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingTask onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingTask query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingTask time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingTask types($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingTask whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingTask whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingTask whereFailCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingTask whereFailList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingTask whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingTask whereJobid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingTask whereMassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingTask whereMomentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingTask whereMsgid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingTask whereNotSendCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingTask whereSendTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingTask whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingTask whereSuccessCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingTask whereSumCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingTask whereTypes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingTask whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingTask whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingTask whereUserid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingTask withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingTask withoutTrashed()
 */
	class WorkMassMessagingTask extends \Eloquent {}
}

namespace App\Http\Model\WorkExternalContact{
/**
 * 企微群发消息模板.
 *
 * @property int $id ID
 * @property int $uid 创建用户ID
 * @property int $group_id 分组ID
 * @property int $types 类型:0、素材,1、关联数据;
 * @property string $content 内容
 * @property int $sort 排序
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\WorkExternalContact\WorkMassMessagingTempAttach> $attach
 * @property-read int|null $attach_count
 * @property-read \App\Http\Model\Admin\Admin|null $creator
 * @property-read \App\Http\Model\WorkExternalContact\WorkMassMessagingTempGroup|null $group
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingTemp nameLike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingTemp newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingTemp newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingTemp onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingTemp query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingTemp time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingTemp whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingTemp whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingTemp whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingTemp whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingTemp whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingTemp whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingTemp whereTypes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingTemp whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingTemp whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingTemp withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingTemp withoutTrashed()
 */
	class WorkMassMessagingTemp extends \Eloquent {}
}

namespace App\Http\Model\WorkExternalContact{
/**
 * 群发素材模板附件.
 *
 * @property int $id ID
 * @property int $uid 创建用户ID
 * @property int $temp_id 素材ID
 * @property string $types 内容类型
 * @property string $title 标题
 * @property string $info 摘要
 * @property string $link 链接
 * @property string $app_id 小程序AppID
 * @property int $sort 排序
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Http\Model\WorkExternalContact\WorkMedia|null $file
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingTempAttach id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingTempAttach newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingTempAttach newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingTempAttach query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingTempAttach time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingTempAttach whereAppId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingTempAttach whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingTempAttach whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingTempAttach whereInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingTempAttach whereLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingTempAttach whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingTempAttach whereTempId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingTempAttach whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingTempAttach whereTypes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingTempAttach whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingTempAttach whereUpdatedAt($value)
 */
	class WorkMassMessagingTempAttach extends \Eloquent {}
}

namespace App\Http\Model\WorkExternalContact{
/**
 * 企微群发消息模板分组.
 *
 * @property int $id ID
 * @property int $pid 父级分组ID
 * @property int $uid 创建用户ID
 * @property string $name 分组名称
 * @property int $sort 排序
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingTempGroup nameLike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingTempGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingTempGroup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingTempGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingTempGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingTempGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingTempGroup whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingTempGroup wherePid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingTempGroup whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingTempGroup whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMassMessagingTempGroup whereUpdatedAt($value)
 */
	class WorkMassMessagingTempGroup extends \Eloquent {}
}

namespace App\Http\Model\WorkExternalContact{
/**
 * 素材.
 *
 * @property int $id ID
 * @property int $uid 业务员用户ID
 * @property string $real_name 原始名称
 * @property string $file_name 附件名称
 * @property string $file_url 附件地址
 * @property string $file_size 附件大小
 * @property string $file_type 附件类型
 * @property string $file_ext 扩展名
 * @property string $file_md5 文件md5值
 * @property int $up_type 上传方式：1、本地；2、七牛云；3、OSS；4、COS。
 * @property int $link_id 关联数据ID
 * @property string $link_type 关联数据类型
 * @property string|null $media_id 临时素材ID
 * @property string|null $attach_id 临时附件ID
 * @property mixed|null $attach_fail 临时附件过期时间
 * @property string|null $job_id 分片上传素材任务ID
 * @property string $media_type 素材类型: image、voice、video、file
 * @property mixed|null $fail_time 临时素材失效时间
 * @property string|null $media_msg 临时素材上传失败信息
 * @property string|null $attach_msg 临时附件上传失败信息
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMedia id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMedia jobId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMedia newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMedia newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMedia normal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMedia notId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMedia onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMedia query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMedia time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMedia whereAttachFail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMedia whereAttachId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMedia whereAttachMsg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMedia whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMedia whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMedia whereFailTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMedia whereFileExt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMedia whereFileMd5($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMedia whereFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMedia whereFileSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMedia whereFileType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMedia whereFileUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMedia whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMedia whereJobId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMedia whereLinkId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMedia whereLinkType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMedia whereMediaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMedia whereMediaMsg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMedia whereMediaType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMedia whereRealName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMedia whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMedia whereUpType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMedia whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMedia withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMedia withoutTrashed()
 */
	class WorkMedia extends \Eloquent {}
}

namespace App\Http\Model\WorkExternalContact{
/**
 * 快捷回复模板
 *
 * @property int $id ID
 * @property int $uid 创建用户ID
 * @property int $group_id 分租ID
 * @property string $types 内容类型
 * @property string $title 标题
 * @property string $info 摘要
 * @property string $link 链接
 * @property string $app_id 小程序AppID
 * @property string $content 文本内容
 * @property int $sort 排序
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property mixed|null $deleted_at
 * @property int $is_personal 是否个人库: 0=公共, 1=个人
 * @property-read \App\Http\Model\Admin\Admin|null $creator
 * @property-read \App\Http\Model\WorkExternalContact\WorkMedia|null $file
 * @property-read \App\Http\Model\WorkExternalContact\WorkReplyTempGroup|null $group
 * @method static \Illuminate\Database\Eloquent\Builder|WorkReplyTemp nameLike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkReplyTemp newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkReplyTemp newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkReplyTemp query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkReplyTemp time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkReplyTemp whereAppId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkReplyTemp whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkReplyTemp whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkReplyTemp whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkReplyTemp whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkReplyTemp whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkReplyTemp whereInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkReplyTemp whereIsPersonal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkReplyTemp whereLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkReplyTemp whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkReplyTemp whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkReplyTemp whereTypes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkReplyTemp whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkReplyTemp whereUpdatedAt($value)
 */
	class WorkReplyTemp extends \Eloquent {}
}

namespace App\Http\Model\WorkExternalContact{
/**
 * 快捷回复分组.
 *
 * @property int $id ID
 * @property int $pid 父级分组ID
 * @property int $uid 创建用户ID
 * @property string $name 分组名称
 * @property int $sort 排序
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|WorkReplyTempGroup nameLike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkReplyTempGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkReplyTempGroup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkReplyTempGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkReplyTempGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkReplyTempGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkReplyTempGroup whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkReplyTempGroup wherePid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkReplyTempGroup whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkReplyTempGroup whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkReplyTempGroup whereUpdatedAt($value)
 */
	class WorkReplyTempGroup extends \Eloquent {}
}

namespace App\Http\Model\Work{
/**
 * App\Http\Model\Work\WorkClient
 *
 * @property int $id
 * @property string $corp_id 企业微信id
 * @property string $external_userid 外部联系人的userid
 * @property int $uid 商城用户uid
 * @property string $userid 添加了此外部联系人的企业成员userid
 * @property string $name 外部联系人的名称
 * @property string $avatar 外部联系人头像
 * @property int $type 1表示该外部联系人是微信用户，2表示该外部联系人是企业微信用户
 * @property int $gender 性别 0-未知 1-男性 2-女性
 * @property string $unionid 开放平台的唯一身份标识
 * @property string $position 外部联系人的职位
 * @property string $phone 手机号码
 * @property string $corp_name 外部联系人所在企业的简称
 * @property string $corp_full_name 外部联系人所在企业的主体名称
 * @property string $external_profile 外部联系人的详情
 * @property string $remark 备注信息
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Http\Model\Work\WorkClientFollow|null $followOne
 * @method static \Illuminate\Database\Eloquent\Builder|WorkClient externalUserid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkClient newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkClient newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkClient query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkClient time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkClient userid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkClient whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkClient whereCorpFullName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkClient whereCorpId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkClient whereCorpName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkClient whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkClient whereExternalProfile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkClient whereExternalUserid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkClient whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkClient whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkClient whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkClient wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkClient wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkClient whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkClient whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkClient whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkClient whereUnionid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkClient whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkClient whereUserid($value)
 */
	class WorkClient extends \Eloquent {}
}

namespace App\Http\Model\Work{
/**
 * App\Http\Model\Work\WorkClientFollow
 *
 * @property int $id
 * @property int $client_id 客户id
 * @property string $userid 添加了此外部联系人的企业成员userid
 * @property string $remark 该成员对此外部联系人的备注
 * @property string $description 该成员对此外部联系人的描述
 * @property int $createtime 该成员添加此外部联系人的时间
 * @property string $remark_corp_name 该成员对此微信客户备注的企业名称
 * @property string $remark_mobiles 该成员对此客户备注的手机号码
 * @property int $add_way 该成员添加此客户的来源
 * @property string $oper_userid 发起添加的userid，如果成员主动添加，为成员的userid；如果是客户主动添加，则为客户的外部联系人userid；如果是内部成员共享/管理员分配，则为对应的成员/管理员userid
 * @property string $state 自定义字段返回数据
 * @property int $is_del_user 客户是否删除跟踪人:0=没有,1=删除
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Http\Model\Work\WorkClient|null $client
 * @property-read \App\Http\Model\Work\WorkMember|null $member
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Http\Model\Work\WorkClientFollowTags> $tags
 * @property-read int|null $tags_count
 * @method static \Illuminate\Database\Eloquent\Builder|WorkClientFollow createTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkClientFollow id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkClientFollow newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkClientFollow newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkClientFollow query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkClientFollow time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkClientFollow whereAddWay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkClientFollow whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkClientFollow whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkClientFollow whereCreatetime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkClientFollow whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkClientFollow whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkClientFollow whereIsDelUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkClientFollow whereOperUserid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkClientFollow whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkClientFollow whereRemarkCorpName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkClientFollow whereRemarkMobiles($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkClientFollow whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkClientFollow whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkClientFollow whereUserid($value)
 */
	class WorkClientFollow extends \Eloquent {}
}

namespace App\Http\Model\Work{
/**
 * App\Http\Model\Work\WorkClientFollowTags
 *
 * @property int $id
 * @property int $follow_id 跟踪人id
 * @property string $group_name 该成员添加此外部联系人所打标签的分组名称
 * @property string $tag_name 该成员添加此外部联系人所打标签名称
 * @property int $type 1-企业设置，2-用户自定义，3-规则组标签
 * @property string $tag_id 该成员添加此外部联系人所打企业标签的id，用户自定义类型标签（type=2）不返回
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Http\Model\Customer\Label|null $tag
 * @method static \Illuminate\Database\Eloquent\Builder|WorkClientFollowTags newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkClientFollowTags newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkClientFollowTags query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkClientFollowTags time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkClientFollowTags whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkClientFollowTags whereFollowId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkClientFollowTags whereGroupName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkClientFollowTags whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkClientFollowTags whereTagId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkClientFollowTags whereTagName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkClientFollowTags whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkClientFollowTags whereUpdatedAt($value)
 */
	class WorkClientFollowTags extends \Eloquent {}
}

namespace App\Http\Model\Work{
/**
 * 部门表.
 *
 * @property int $id
 * @property string $corp_id 企业id
 * @property int $department_id 部门id
 * @property string $name 部门名称
 * @property string $name_en 部门英文名称
 * @property string $department_leader leader
 * @property int $parentid 上级id
 * @property int $sort 排序
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|WorkDepartment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkDepartment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkDepartment query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkDepartment time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkDepartment whereCorpId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkDepartment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkDepartment whereDepartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkDepartment whereDepartmentLeader($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkDepartment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkDepartment whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkDepartment whereNameEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkDepartment whereParentid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkDepartment whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkDepartment whereUpdatedAt($value)
 */
	class WorkDepartment extends \Eloquent {}
}

namespace App\Http\Model\Work{
/**
 * App\Http\Model\Work\WorkGroupChat
 *
 * @property int $id
 * @property string $corp_id 企业ID
 * @property string $chat_id 客户群ID
 * @property string $name 群名
 * @property string $owner 群主ID
 * @property int $group_create_time 群的创建时间
 * @property string $notice 群公告
 * @property string $admin_list 群管理员userid
 * @property int $member_num 群人数
 * @property int $retreat_group_num 退群总数
 * @property int $status 客户群跟进状态。\r\n0 - 跟进人正常\r\n1 - 跟进人离职\r\n2 - 离职继承中\r\n3 - 离职继承完成
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Http\Model\Admin\Admin|null $admin
 * @method static \Illuminate\Database\Eloquent\Builder|WorkGroupChat adminId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkGroupChat adminList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkGroupChat nameLike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkGroupChat newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkGroupChat newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkGroupChat owner($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkGroupChat query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkGroupChat time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkGroupChat whereAdminList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkGroupChat whereChatId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkGroupChat whereCorpId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkGroupChat whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkGroupChat whereGroupCreateTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkGroupChat whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkGroupChat whereMemberNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkGroupChat whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkGroupChat whereNotice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkGroupChat whereOwner($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkGroupChat whereRetreatGroupNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkGroupChat whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkGroupChat whereUpdatedAt($value)
 */
	class WorkGroupChat extends \Eloquent {}
}

namespace App\Http\Model\Work{
/**
 * 群成员表.
 *
 * @property int $id
 * @property int $group_id 企业群ID
 * @property string $userid 群成员id
 * @property int $type 成员类型。1 - 企业成员2 - 外部联系人
 * @property string $unionid 微信开放平台的唯一身份标识（微信unionid）
 * @property int $join_time 入群时间
 * @property int $join_scene 入群方式。1 - 由群成员邀请入群（直接邀请入群）2 - 由群成员邀请入群（通过邀请链接入群）3 - 通过扫描群二维码入群
 * @property string $invitor_userid 邀请者。目前仅当是由本企业内部成员邀请入群时会返回该值
 * @property string $group_nickname 在群里的昵称
 * @property string $name 名字。仅当 need_name = 1 时返回
 * @property int $status 1=在群中,0=已退群
 * @property int $chat_sum 当前群人数
 * @property int $retreat_chat_num 当前退群人数
 * @property string $state
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|WorkGroupChatMember newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkGroupChatMember newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkGroupChatMember query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkGroupChatMember time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkGroupChatMember whereChatSum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkGroupChatMember whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkGroupChatMember whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkGroupChatMember whereGroupNickname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkGroupChatMember whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkGroupChatMember whereInvitorUserid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkGroupChatMember whereJoinScene($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkGroupChatMember whereJoinTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkGroupChatMember whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkGroupChatMember whereRetreatChatNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkGroupChatMember whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkGroupChatMember whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkGroupChatMember whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkGroupChatMember whereUnionid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkGroupChatMember whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkGroupChatMember whereUserid($value)
 */
	class WorkGroupChatMember extends \Eloquent {}
}

namespace App\Http\Model\Work{
/**
 * App\Http\Model\Work\WorkLabel
 *
 * @property int $id
 * @property string $corp_id 企业微信id
 * @property int $group_id 标签组id
 * @property string $group_name 标签组名称
 * @property string $name 标签名称
 * @property int $sort 排序
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|WorkLabel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkLabel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkLabel query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkLabel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkLabel whereCorpId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkLabel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkLabel whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkLabel whereGroupName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkLabel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkLabel whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkLabel whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkLabel whereUpdatedAt($value)
 */
	class WorkLabel extends \Eloquent {}
}

namespace App\Http\Model\Work{
/**
 * App\Http\Model\Work\WorkMember
 *
 * @property int $id
 * @property string $corp_id 企业微信id
 * @property string $userid 成员UserID
 * @property int $uid 用户id
 * @property string $name 成员名称
 * @property string $position 职务信息
 * @property string $mobile 手机号码
 * @property int $gender 性别。0表示未定义，1表示男性，2表示女性
 * @property string $email 邮箱
 * @property string $biz_mail 企业邮箱
 * @property string $direct_leader 直属上级UserID
 * @property string $avatar 头像url
 * @property string $thumb_avatar 头像缩略图url
 * @property string $telephone 座机
 * @property string $alias 别名
 * @property int $enable 启用/禁用成员。1表示启用成员，0表示禁用成员
 * @property int $is_leader 是否是领导
 * @property int $hide_mobile 是否隐藏手机号
 * @property string $address 地址
 * @property string $open_userid 全局唯一
 * @property int $main_department 主部门
 * @property int $status 激活状态: 1=已激活，2=已禁用，4=未激活，5=退出企业
 * @property string $qr_code 员工个人二维码
 * @property string $external_position 对外职务
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMember newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMember newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMember query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMember time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMember userid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMember whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMember whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMember whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMember whereBizMail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMember whereCorpId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMember whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMember whereDirectLeader($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMember whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMember whereEnable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMember whereExternalPosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMember whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMember whereHideMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMember whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMember whereIsLeader($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMember whereMainDepartment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMember whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMember whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMember whereOpenUserid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMember wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMember whereQrCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMember whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMember whereTelephone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMember whereThumbAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMember whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMember whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMember whereUserid($value)
 */
	class WorkMember extends \Eloquent {}
}

namespace App\Http\Model\Work{
/**
 * App\Http\Model\Work\WorkMemberOther
 *
 * @property int $id
 * @property int $member_id 企业成员id
 * @property string|null $extattr 扩展属性
 * @property string|null $external_profile 成员对外属性
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMemberOther memberId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMemberOther newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMemberOther newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMemberOther query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMemberOther time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMemberOther whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMemberOther whereExtattr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMemberOther whereExternalProfile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMemberOther whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMemberOther whereMemberId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMemberOther whereUpdatedAt($value)
 */
	class WorkMemberOther extends \Eloquent {}
}

namespace App\Http\Model\Work{
/**
 * App\Http\Model\Work\WorkMemberRelation
 *
 * @property int $id
 * @property int $member_id 员工ID
 * @property int $department 所属部门
 * @property int $srot 排序
 * @property int $is_leader_in_dept 是否为部门负责人
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMemberRelation memberId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMemberRelation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMemberRelation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMemberRelation query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMemberRelation time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMemberRelation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMemberRelation whereDepartment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMemberRelation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMemberRelation whereIsLeaderInDept($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMemberRelation whereMemberId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMemberRelation whereSrot($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMemberRelation whereUpdatedAt($value)
 */
	class WorkMemberRelation extends \Eloquent {}
}

namespace App\Http\Model\Work{
/**
 * App\Http\Model\Work\WorkMessage
 *
 * @property int $id
 * @property string $corp_id 企业ID
 * @property int $seq 消息的seq值，标识消息的序号
 * @property string $msg_id 消息唯一标识
 * @property string $action 消息动作，0.send(发送消息) 1.recall(撤回消息) 2.switch(切换企业日志)
 * @property string $from 消息发送方id。同一企业内容为userid，非相同企业/机器人为external_userid
 * @property mixed $tolist 消息接收方列表，可能是多个，同一个企业内容为userid，非相同企业为external_userid
 * @property mixed $tolist_id 接收方ID
 * @property int $tolist_type 接收方类型 0通讯录 1外部联系人 2群
 * @property string $msg_type 文本消息类型：text=文本，image=图片，revoke=撤回消息，agree=同意会话内容，voice=语音，video=视屏，card=名片，location=位置等等
 * @property mixed $content 文本内容：详细见wx文档
 * @property mixed|null $msg_time 消息发送时间戳，utc时间，ms单位
 * @property string $wx_room_id 微信群id。如果是单聊则为空
 * @property int $room_id 群id
 * @property int $status 关键词打标签查询状态（0：未查询，1：已查询）
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property mixed|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMessage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMessage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMessage query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMessage time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMessage whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMessage whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMessage whereCorpId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMessage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMessage whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMessage whereFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMessage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMessage whereMsgId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMessage whereMsgTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMessage whereMsgType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMessage whereRoomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMessage whereSeq($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMessage whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMessage whereTolist($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMessage whereTolistId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMessage whereTolistType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMessage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMessage whereWxRoomId($value)
 */
	class WorkMessage extends \Eloquent {}
}

namespace App\Http\Model\Work{
/**
 * App\Http\Model\Work\WorkMessageIndex
 *
 * @property int $id
 * @property string $corp_id 企业ID
 * @property int $index_id 对应type的 ID
 * @property int $index_type 0=员工，1=客户，2=群聊
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Http\Model\Work\WorkClient|null $client
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMessageIndex newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMessageIndex newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMessageIndex query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMessageIndex time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMessageIndex whereCorpId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMessageIndex whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMessageIndex whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMessageIndex whereIndexId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMessageIndex whereIndexType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMessageIndex whereUpdatedAt($value)
 */
	class WorkMessageIndex extends \Eloquent {}
}

namespace App\Http\Model\Work{
/**
 * App\Http\Model\Work\WorkMessageSeq
 *
 * @property int $id
 * @property string $corp_id 企业ID
 * @property int $seq
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMessageSeq newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMessageSeq newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMessageSeq query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel time($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMessageSeq whereCorpId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMessageSeq whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMessageSeq whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMessageSeq whereSeq($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkMessageSeq whereUpdatedAt($value)
 */
	class WorkMessageSeq extends \Eloquent {}
}

