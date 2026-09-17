<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Local-only workspace data for the employee dashboard.
 *
 * Every record is identified by a stable "Demo" title or reference number so
 * this seed can be run repeatedly from the Docker PHP container. It augments
 * the local database and deliberately never deletes a user's own records.
 */
class LocalWorkspaceDemoSeeder extends Seeder
{
    private const ENT_ID = 1;
    private const WAN_ID = 1;

    public function run(): void
    {
        $this->seedDemoTeamMembership();
        $this->seedJobsAndJobAnalysis();
        $this->seedSchedule();
        $this->seedReports();
        $this->seedApprovals();
        $this->seedAttendance();
        $this->seedPerformance();
        $this->seedLeadPool();
        $this->seedContractSigning();
        $this->seedOrderIncomeAndExpenses();
    }

    /** Make the local demo team eligible for staff-only dashboard views. */
    private function seedDemoTeamMembership(): void
    {
        $teamIds = [2, 3, 4, 5, 6];
        $teamUids = DB::table('admin')->whereIn('id', $teamIds)->pluck('uid')->all();

        DB::table('admin_info')->whereIn('uid', $teamUids)->update([
            'type' => 1,
            'updated_at' => now(),
        ]);
        DB::table('frame_assist')->whereIn('user_id', $teamIds)->update([
            'superior_uid' => self::WAN_ID,
            'updated_at' => now(),
        ]);
    }

    private function seedJobsAndJobAnalysis(): void
    {
        $jobs = [
            1 => ['Demo Managing Director', 'Lead strategy, client partnerships, and company delivery.'],
            2 => ['Demo Sales Manager', 'Build the sales pipeline and coach the customer-success team.'],
            3 => ['Demo Operations Manager', 'Coordinate delivery schedules, suppliers, and operational quality.'],
            4 => ['Demo People Operations Lead', 'Support employee experience, reporting, and people operations.'],
            5 => ['Demo Finance Executive', 'Maintain finance operations, reconciliations, and reporting.'],
            6 => ['Demo Client Success Executive', 'Maintain customer relationships and follow-up commitments.'],
            7 => ['Demo Operations Coordinator', 'Track delivery work and help the operations team stay on schedule.'],
        ];

        foreach ($jobs as $adminId => [$name, $duty]) {
            $job = DB::table('rank_job')->where(['entid' => self::ENT_ID, 'name' => $name])->first();
            $payload = [
                'user_id' => self::WAN_ID,
                'entid' => self::ENT_ID,
                'cate_id' => 0,
                'rank_id' => 0,
                'card_id' => 0,
                'job_count' => 1,
                'describe' => 'Local demo role for the workspace preview',
                'duty' => '<p>' . e($duty) . '</p><ul><li>Keep stakeholders informed</li><li>Review weekly priorities</li><li>Record meaningful outcomes</li></ul>',
                'status' => 1,
                'updated_at' => now(),
            ];
            if ($job) {
                DB::table('rank_job')->where('id', $job->id)->update($payload);
                $jobId = $job->id;
            } else {
                $payload['name'] = $name;
                $payload['created_at'] = now();
                $jobId = DB::table('rank_job')->insertGetId($payload);
            }
            DB::table('admin')->where('id', $adminId)->update(['job' => $jobId, 'updated_at' => now()]);
        }

        DB::table('enterprise_user_job_analysis')->updateOrInsert(
            ['entid' => self::ENT_ID, 'uid' => self::WAN_ID],
            [
                'data' => '<h2>Demo work analysis</h2><p>Focus this month: strengthen customer delivery, complete renewal reviews, and support the team’s weekly priorities.</p><p>Key measures: customer follow-ups completed, on-time delivery, and weekly report quality.</p>',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        );
    }

    private function seedSchedule(): void
    {
        $items = [
            ['Demo: Monday leadership planning', 'Set weekly priorities with the leadership team.', '2026-09-14 09:00:00', '2026-09-14 10:00:00', '#409EFF'],
            ['Demo: Customer pipeline review', 'Review active customer conversations and follow-up owners.', '2026-09-15 14:00:00', '2026-09-15 15:00:00', '#67C23A'],
            ['Demo: Operations stand-up', 'Check delivery risks and commitments for the week.', '2026-09-16 09:30:00', '2026-09-16 10:00:00', '#E6A23C'],
            ['Demo: Team report review', 'Read submitted reports and give concise feedback.', '2026-09-17 16:00:00', '2026-09-17 16:30:00', '#909399'],
            ['Demo: Friday performance check-in', 'Discuss progress and next steps with the team.', '2026-09-18 15:00:00', '2026-09-18 16:00:00', '#F56C6C'],
        ];
        foreach ($items as [$title, $content, $start, $end, $color]) {
            DB::table('schedule')->updateOrInsert(
                ['uid' => self::WAN_ID, 'title' => $title],
                [
                    'cid' => self::ENT_ID,
                    'color' => $color,
                    'content' => $content,
                    'all_day' => 0,
                    'start_time' => $start,
                    'end_time' => $end,
                    'period' => 0,
                    'rate' => 1,
                    'days' => json_encode([]),
                    'remind' => 1,
                    'fail_time' => $end,
                    'pid' => 0,
                    'link_id' => 0,
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            );
        }
    }

    private function seedReports(): void
    {
        $admins = DB::table('admin')->whereIn('id', [1, 2, 3, 4, 5])->get(['id', 'uid', 'name']);
        $dates = ['2026-09-11', '2026-09-12', '2026-09-14', '2026-09-15'];
        foreach ($admins as $admin) {
            foreach ($dates as $index => $date) {
                $mark = sprintf('Demo workspace report — %s — %s', $admin->name, $date);
                DB::table('enterprise_user_daily')->updateOrInsert(
                    ['mark' => $mark],
                    [
                        'entid' => self::ENT_ID,
                        'uid' => $admin->uid,
                        'user_id' => $admin->id,
                        'finish' => '<p>Completed priority follow-ups, updated the workspace, and shared progress with the team.</p>',
                        'plan' => '<p>Complete the next customer or team priority and capture any delivery risks early.</p>',
                        'status' => $index === 3 ? 0 : 1,
                        'types' => 0,
                        'created_at' => $date . ' 17:30:00',
                        'updated_at' => now(),
                    ],
                );
            }
        }
    }

    private function seedApprovals(): void
    {
        $approveId = (int) DB::table('approve')->where('id', 1)->value('id');
        if (! $approveId) {
            return;
        }

        $applications = [
            ['DEMO-WAN-APP-001', 1, 'Demo: Customer visit travel request', 0, 'demo-wan-review'],
            ['DEMO-WAN-APP-002', 1, 'Demo: Equipment purchase request', 1, 'demo-wan-complete'],
            ['DEMO-TEAM-APP-001', 2, 'Demo: Client workshop expense', 0, 'demo-team-review'],
            ['DEMO-TEAM-APP-002', 3, 'Demo: Operations software request', 0, 'demo-team-review-2'],
        ];
        foreach ($applications as [$number, $applicantId, $title, $status, $nodeId]) {
            DB::table('approve_apply')->updateOrInsert(
                ['number' => $number],
                [
                    'user_id' => $applicantId,
                    'entid' => self::ENT_ID,
                    'card_id' => 1,
                    'approve_id' => $approveId,
                    'node_id' => $nodeId,
                    'examine' => 1,
                    'status' => $status,
                    'info' => json_encode(['title' => $title, 'summary' => 'Local demo approval record'], JSON_UNESCAPED_UNICODE),
                    'crud_id' => 0,
                    'link_id' => 0,
                    'apply_id' => 0,
                    'is_recall' => 0,
                    'created_at' => now()->subDays($status ? 3 : 1),
                    'updated_at' => now(),
                ],
            );
            $applyId = (int) DB::table('approve_apply')->where('number', $number)->value('id');
            if ($applicantId !== self::WAN_ID) {
                DB::table('approve_user')->updateOrInsert(
                    ['user_id' => self::WAN_ID, 'apply_id' => $applyId, 'node_id' => $nodeId],
                    [
                        'card_id' => 1,
                        'approve_id' => $approveId,
                        'level' => 1,
                        'sort' => 1,
                        'verify' => 0,
                        'status' => $status ? 1 : 0,
                        'is_sign' => 0,
                        'is_transfer' => 0,
                        'parent' => 0,
                        'types' => 1,
                        'info' => '',
                        'process_info' => '',
                        'content' => $title,
                        'created_at' => now()->subDays(1),
                        'updated_at' => now(),
                    ],
                );
            }
        }
    }

    private function seedAttendance(): void
    {
        $adminIds = [1 => 2, 2 => 3, 3 => 4, 4 => 5, 5 => 6];
        foreach ($adminIds as $adminId => $frameId) {
            foreach (['2026-09-11', '2026-09-12', '2026-09-14', '2026-09-15'] as $date) {
                $clockIn = $date . ' 08:' . str_pad((string) (45 + $adminId), 2, '0', STR_PAD_LEFT) . ':00';
                $clockOut = $date . ' 17:' . str_pad((string) (10 + $adminId), 2, '0', STR_PAD_LEFT) . ':00';
                DB::table('attendance_clock_record')->updateOrInsert(
                    ['uid' => $adminId, 'clock_type' => 0, 'created_at' => $clockIn],
                    [
                        'frame_id' => $frameId,
                        'group_id' => 1,
                        'group' => 'Demo Kuala Lumpur HQ attendance',
                        'shift_id' => 1,
                        'shift_data' => 'Demo standard office hours',
                        'address' => 'Bukit Bintang, Kuala Lumpur, Malaysia',
                        'lat' => '3.1466',
                        'lng' => '101.7101',
                        'mac' => '',
                        'remark' => 'Demo check-in',
                        'image' => '',
                        'is_external' => 0,
                        'updated_at' => now(),
                        'deleted_at' => null,
                    ],
                );
                DB::table('attendance_clock_record')->updateOrInsert(
                    ['uid' => $adminId, 'clock_type' => 1, 'created_at' => $clockOut],
                    [
                        'frame_id' => $frameId,
                        'group_id' => 1,
                        'group' => 'Demo Kuala Lumpur HQ attendance',
                        'shift_id' => 1,
                        'shift_data' => 'Demo standard office hours',
                        'address' => 'Bukit Bintang, Kuala Lumpur, Malaysia',
                        'lat' => '3.1466',
                        'lng' => '101.7101',
                        'mac' => '',
                        'remark' => 'Demo check-out',
                        'image' => '',
                        'is_external' => 0,
                        'updated_at' => now(),
                        'deleted_at' => null,
                    ],
                );
                DB::table('attendance_statistics')->updateOrInsert(
                    ['uid' => $adminId, 'one_shift_time' => $clockIn],
                    [
                        'frame_id' => $frameId,
                        'group_id' => 1,
                        'group' => 'Demo Kuala Lumpur HQ attendance',
                        'shift_id' => 1,
                        'shift_data' => 'Demo standard office hours',
                        'one_shift_is_after' => 0,
                        'one_shift_status' => 1,
                        'one_shift_location_status' => 1,
                        'one_shift_record_id' => 0,
                        'two_shift_time' => $clockOut,
                        'two_shift_is_after' => 0,
                        'two_shift_status' => 1,
                        'two_shift_location_status' => 1,
                        'two_shift_record_id' => 0,
                        'three_shift_time' => null,
                        'three_shift_is_after' => 0,
                        'three_shift_status' => 0,
                        'three_shift_location_status' => 0,
                        'three_shift_record_id' => 0,
                        'four_shift_time' => null,
                        'four_shift_is_after' => 0,
                        'four_shift_status' => 0,
                        'four_shift_location_status' => 0,
                        'four_shift_record_id' => 0,
                        'required_work_hours' => 8,
                        'actual_work_hours' => 8.25,
                        'created_at' => $clockOut,
                        'updated_at' => now(),
                        'deleted_at' => null,
                    ],
                );
            }
        }
    }

    private function seedPerformance(): void
    {
        $rows = [
            ['Demo September performance review', 1, 2, 2, 86.00, 1, 2],
            ['Demo September sales review', 2, 1, 3, 78.00, 1, 2],
            ['Demo September operations review', 3, 1, 4, 82.00, 1, 2],
            ['Demo September people review', 4, 1, 5, 88.00, 1, 2],
            // The Performance review screen is the final-audit queue. Its
            // Pending tab intentionally filters for status 3, distinct from
            // the Department assessment manager-review queue (status 2).
            ['Demo September people audit', 4, 1, 5, 88.00, 1, 3],
            ['Demo September operations audit', 3, 1, 4, 82.00, 1, 3],
        ];
        foreach ($rows as [$name, $testUid, $checkUid, $frameId, $score, $grade, $status]) {
            DB::table('assess')->updateOrInsert(
                ['entid' => self::ENT_ID, 'name' => $name, 'test_uid' => $testUid],
                [
                    'period' => 3,
                    'planid' => 0,
                    'frame_id' => $frameId,
                    'number' => 1,
                    'check_uid' => $checkUid,
                    'start_time' => '2026-09-01 00:00:00',
                    'make_time' => '2026-09-05 00:00:00',
                    'make_status' => 1,
                    'end_time' => '2026-09-30 23:59:59',
                    'test_status' => 1,
                    'check_end' => $status === 3 ? now()->subDay() : null,
                    'check_status' => $status === 3 ? 1 : 0,
                    'verify_time' => $status === 3 ? now()->addDays(7) : null,
                    'verify_status' => 0,
                    'score' => $score,
                    'total' => 100,
                    'grade' => $grade,
                    'status' => $status,
                    'types' => 0,
                    'intact' => 1,
                    'is_show' => 1,
                    'self_reply' => 'Demo self-review: priorities are progressing as planned.',
                    'reply' => '',
                    'hide_reply' => '',
                    'delete' => null,
                    'created_at' => now()->subDays(7),
                    'updated_at' => now(),
                ],
            );
            $assessId = (int) DB::table('assess')->where([
                'entid' => self::ENT_ID,
                'name' => $name,
                'test_uid' => $testUid,
            ])->value('id');
            $this->seedPerformanceContent($assessId, $testUid);
        }
    }

    /** Supply the dimensions and indicators required by the assessment detail view. */
    private function seedPerformanceContent(int $assessId, int $testUid): void
    {
        $dimensions = [
            [
                'Delivery and outcomes', 60,
                [
                    ['Priority delivery', 50, 'Complete agreed priorities on time and flag risks early.', 'All current-week priorities are tracked and reviewed.', 80],
                    ['Customer and team impact', 50, 'Deliver measurable value for customers and internal partners.', 'Stakeholders received clear updates and next steps.', 86],
                ],
            ],
            [
                'Collaboration and growth', 40,
                [
                    ['Team collaboration', 50, 'Support shared delivery and communicate decisions clearly.', 'Worked constructively across the relevant departments.', 88],
                    ['Continuous improvement', 50, 'Identify one practical process improvement each review cycle.', 'Documented an improvement opportunity for the next cycle.', 82],
                ],
            ],
        ];

        foreach ($dimensions as $sort => [$name, $ratio, $targets]) {
            DB::table('assess_space')->updateOrInsert(
                ['assessid' => $assessId, 'name' => $name],
                [
                    'entid' => self::ENT_ID,
                    'targetid' => 0,
                    'ratio' => $ratio,
                    'sort' => $sort + 1,
                    'deleted_at' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            );
            $spaceId = (int) DB::table('assess_space')->where([
                'assessid' => $assessId,
                'name' => $name,
            ])->value('id');
            foreach ($targets as $targetSort => [$targetName, $targetRatio, $content, $finishInfo, $score]) {
                DB::table('assess_target')->updateOrInsert(
                    ['spaceid' => $spaceId, 'name' => $targetName],
                    [
                        'ratio' => $targetRatio,
                        'sort' => $targetSort + 1,
                        'content' => $content,
                        'info' => '',
                        'finish_info' => $finishInfo,
                        'finish_ratio' => 100,
                        'check_info' => 'Demo manager review recorded.',
                        'max' => 100,
                        'score' => $score,
                        'deleted_at' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                );
            }
        }
    }

    /** Populate the unassigned lead pool used by Customer > Lead management > Lead pool. */
    private function seedLeadPool(): void
    {
        // The label selector renders labels as children of a label group.
        // Keep this parent as a group and make the usable demo labels children
        // so the selector does not appear empty.
        DB::table('client_label')->updateOrInsert(
            ['entid' => self::ENT_ID, 'name' => 'Demo lead categories'],
            [
                'sort' => 1,
                'pid' => 0,
                'is_work' => 0,
                'work_group_id' => '',
                'work_tag_id' => '',
                'updated_at' => now(),
                'created_at' => now(),
            ],
        );
        $groupId = (int) DB::table('client_label')
            ->where(['entid' => self::ENT_ID, 'name' => 'Demo lead categories'])
            ->value('id');

        $labels = [
            'Demo: High priority',
            'Demo: Hospitality',
            'Demo: Technology',
        ];

        foreach ($labels as $sort => $name) {
            DB::table('client_label')->updateOrInsert(
                ['entid' => self::ENT_ID, 'name' => $name],
                [
                    'sort' => $sort + 1,
                    'pid' => $groupId,
                    'is_work' => 0,
                    'work_group_id' => '',
                    'work_tag_id' => '',
                    'updated_at' => now(),
                    'created_at' => now(),
                ],
            );
        }

        $labelIds = DB::table('client_label')
            ->where('entid', self::ENT_ID)
            ->whereIn('name', $labels)
            ->pluck('id', 'name');

        $leads = [
            ['Demo lead — Cedar & Co Catering', 'Website enquiry', '+60 12-410 8214', ['Demo: High priority', 'Demo: Hospitality'], '2026-09-16', 'Kuala Lumpur', 'Looking for catering for a 180-person corporate event.'],
            ['Demo lead — Brightline Analytics', 'Partner referral', '+60 11-2847 9120', ['Demo: High priority', 'Demo: Technology'], '2026-09-15', 'Petaling Jaya', 'Requested a discovery call for an operations analytics rollout.'],
            ['Demo lead — Lumen Retail Group', 'Industry event', '+60 17-650 4821', ['Demo: Hospitality'], '2026-09-14', 'Shah Alam', 'Interested in a customer-support workflow demonstration.'],
            ['Demo lead — Northbridge Learning', 'Campaign response', '+60 16-729 3045', ['Demo: Technology'], '2026-09-13', 'Subang Jaya', 'Asked for pricing and implementation timing for 80 staff.'],
            ['Demo lead — Atlas Wellness Studio', 'Website enquiry', '+60 12-976 1540', ['Demo: Hospitality'], '2026-09-12', 'Kuala Lumpur', 'Needs a follow-up after reviewing the introductory package.'],
            ['Demo lead — Riverstone Logistics', 'Referral', '+60 18-338 7602', ['Demo: High priority', 'Demo: Technology'], '2026-09-11', 'Klang', 'Looking to replace spreadsheet-based customer tracking.'],
        ];

        foreach ($leads as [$name, $source, $phone, $leadLabels, $date, $city, $remarks]) {
            $resolvedLabels = collect($leadLabels)
                ->map(fn (string $label) => (int) ($labelIds[$label] ?? 0))
                ->filter()
                ->values()
                ->all();

            DB::table('customer_clue')->updateOrInsert(
                ['name' => $name],
                [
                    'uid' => 0,
                    'before_uid' => 0,
                    'creator_uid' => self::WAN_ID,
                    'source' => $source,
                    'phone' => $phone,
                    'pool' => 'Local demo lead pool',
                    'customer_label' => json_encode($resolvedLabels),
                    'createtime' => $date,
                    'area_cascade' => json_encode([]),
                    'address' => $city . ', Selangor, Malaysia',
                    'status' => '1',
                    'followed' => '1',
                    'return_num' => 0,
                    'claim_time' => null,
                    'mark' => $remarks,
                    'external_userid' => '',
                    'userid' => '',
                    'deleted_at' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            );
        }
    }

    /** Populate Customer > Contract signing with complete local demo documents. */
    private function seedContractSigning(): void
    {
        $documents = [
            [
                'DEMO-SIGN-2026-001', 'Demo: Aster annual service agreement', 1, [1], 1, 3,
                '2026-09-01 00:00:00', '2027-08-31 23:59:59', '2026-09-01 10:30:00',
                'Signed local demo agreement for the annual service renewal.', 'Aster Retail Group', '+60 12-455 7210', 1,
            ],
            [
                'DEMO-SIGN-2026-002', 'Demo: Kinetic implementation agreement', 3, [3], 2, 2,
                '2026-09-18 00:00:00', '2027-09-17 23:59:59', null,
                'Electronic-signature demo awaiting the customer signatory.', 'Kinetic Health Systems', '+60 11-6902 8431', 0,
            ],
            [
                'DEMO-SIGN-2026-003', 'Demo: Sunway customer-support agreement', 5, [5], 1, 1,
                '2026-10-01 00:00:00', '2027-09-30 23:59:59', null,
                'Local demo agreement submitted for internal approval.', 'Sunway Horizon Foods', '+60 17-284 9506', 0,
            ],
        ];

        foreach ($documents as [
            $docNo, $docName, $customerId, $orderIds, $signType, $status,
            $startDate, $endDate, $signDate, $remarks, $customerName, $customerPhone, $externalStatus,
        ]) {
            DB::table('contract_doc')->updateOrInsert(
                ['doc_no' => $docNo],
                [
                    'uid' => self::WAN_ID,
                    'eid' => $customerId,
                    'cid' => json_encode($orderIds),
                    'oid' => null,
                    'link_type' => 2,
                    'doc_name' => $docName,
                    'status' => $status,
                    'sign_type' => $signType,
                    'term_type' => 1,
                    'date_count' => 364,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'sign_status' => $status === 3 ? 1 : 0,
                    'sign_date' => $signDate,
                    'signature_sn' => $signType === 2 ? 'DEMO-ESIGN-2026-002' : null,
                    'sign_file' => null,
                    'file_id' => null,
                    'app_url' => null,
                    'pc_url' => null,
                    'sign_url' => null,
                    'sign_result' => null,
                    'approve_id' => 0,
                    'is_verify' => $status === 1 ? 1 : 0,
                    'fail_time' => $endDate,
                    'mark' => $remarks,
                    'deleted_at' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            );

            $documentId = (int) DB::table('contract_doc')->where('doc_no', $docNo)->value('id');
            $isSigned = $status === 3 ? 1 : 0;

            DB::table('contract_signatory')->updateOrInsert(
                ['cid' => $documentId, 'types' => 0, 'phone' => '+60 12-000 0001'],
                [
                    'user_id' => self::WAN_ID,
                    'name' => 'Wan',
                    'company_name' => 'Meridian Peak Solutions',
                    'result' => '',
                    'sign_time' => $signDate,
                    'sign_status' => $isSigned,
                    'e_userid' => '',
                    'e_openid' => null,
                    'remark' => 'Local demo internal handler',
                    'deleted_at' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            );

            DB::table('contract_signatory')->updateOrInsert(
                ['cid' => $documentId, 'types' => 1, 'phone' => $customerPhone],
                [
                    'user_id' => 0,
                    'name' => $customerName . ' representative',
                    'company_name' => $customerName,
                    'result' => '',
                    'sign_time' => $signDate,
                    'sign_status' => $externalStatus,
                    'e_userid' => '',
                    'e_openid' => null,
                    'remark' => 'Local demo customer signatory',
                    'deleted_at' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            );
        }
    }

    /**
     * Populate Customer > Performance goals > Order income and expenses.
     *
     * These records deliberately use the September 2026 range selected by
     * default in the screen and span approved and pending income/expense
     * states, so the table and its summary figures are both demonstrable.
     */
    private function seedOrderIncomeAndExpenses(): void
    {
        $entries = [
            ['DEMO-BILL-2026-001', 1, 1, 0, 1, 18500.00, 'Bank transfer', '2026-09-03 10:15:00', 1, 'Initial payment received for the Aster Retail service package.'],
            ['DEMO-BILL-2026-002', 2, 2, 1, 1, 7200.00, 'Online payment', '2026-09-08 14:30:00', 1, 'Renewal payment received for the BluePalm hospitality programme.'],
            ['DEMO-BILL-2026-003', 3, 3, 2, 0, 1260.00, 'Company card', '2026-09-12 09:45:00', 1, 'Implementation workshop travel and materials expense.'],
            ['DEMO-BILL-2026-004', 4, 4, 0, 1, 9600.00, 'Bank transfer', '2026-09-16 11:20:00', 0, 'Pending approval for the Northbridge rollout deposit.'],
            ['DEMO-BILL-2026-005', 5, 5, 2, 0, 680.00, 'Company card', '2026-09-18 16:10:00', 0, 'Pending approval for Sunway project delivery expenses.'],
        ];

        foreach ($entries as [$billNo, $customerId, $contractId, $types, $billTypes, $amount, $payType, $date, $status, $mark]) {
            DB::table('client_bill')->updateOrInsert(
                ['bill_no' => $billNo],
                [
                    'entid' => self::ENT_ID,
                    'eid' => $customerId,
                    'cid' => $contractId,
                    'cate_id' => 0,
                    'bill_cate_id' => 0,
                    'bill_types' => $billTypes,
                    'uid' => (string) self::WAN_ID,
                    'invoice_id' => 0,
                    'num' => $amount,
                    'mark' => $mark,
                    'types' => $types,
                    'type_id' => 0,
                    'pay_type' => $payType,
                    'date' => $date,
                    'end_date' => null,
                    'apply_id' => 0,
                    'status' => $status,
                    'fail_msg' => '',
                    'created_at' => $date,
                    'updated_at' => now(),
                ],
            );
        }
    }
}
