---
name: oa-cron-jobs
description: 在陀螺匠 OA 项目中新增或维护 Swoole Timer 定时任务、Laravel Jobs、提醒任务、企业微信同步任务和异步任务时使用。
---

# OA 定时任务与异步任务

## 适用场景

新增定时提醒、周期统计、企业微信同步、合同/日程/绩效提醒、异步导出、耗时处理或修复任务重复执行问题时使用。

## 先看配置

- Swoole Timer 配置：`config/laravels.php`
- 定时任务目录：`app/Task`
- 队列任务目录：`app/Jobs`
- 任务投递模式：搜索 `Task::deliver(`、`dispatch(`、现有 Job 类。

## Timer 任务原则

- 任务必须幂等：同一时间重复执行不能重复发消息、重复写账、重复变更状态。
- 每次执行限制处理数量，避免阻塞 Worker。
- 长任务拆成批次或投递异步 Job。
- 捕获异常并记录日志，避免一个任务影响后续调度。
- 使用 Redis 锁或业务唯一键防止并发重复执行。

## Job 原则

- Job 参数必须可序列化：传 ID、数组、标量，不传 Request、闭包、连接对象、大 Model。
- Job 内重新查询最新数据，并再次校验状态。
- 外部接口调用要有超时、重试上限和失败日志。
- 发送通知、同步第三方、转换文件等要能重试。

## 新增定时任务流程

1. 搜索相似任务，例如提醒、同步、统计类。
2. 新建任务类，保持命名和命名空间一致。
3. 在 `config/laravels.php` 注册 Timer。
4. 加入锁、批量限制、日志和幂等判断。
5. 本地用小范围数据手动触发或临时执行验证。
6. 调整后重启 Swoole，而不只是 reload 普通代码。

## 风险检查

- 是否会跨企业处理错误数据。
- 是否会重复通知用户。
- 是否会扫描全表且无索引。
- 是否依赖当前登录用户。
- 是否把当天时间写死，导致时区或补偿任务失败。

## 验证

```bash
php -l app/Task/YourTask.php
php bin/laravels restart
php bin/laravels info
tail -f storage/logs/laravel.log
```
