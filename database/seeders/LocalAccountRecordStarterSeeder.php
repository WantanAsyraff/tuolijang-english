<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Constants\ApproveEnum;
use App\Http\Model\Approve\Approve;
use App\Http\Model\Approve\ApproveForm;
use App\Http\Service\Config\DictDataService;
use App\Http\Service\Config\DictTypeService;
use App\Http\Service\Finance\BillCategoryService;
use App\Http\Service\Finance\PaytypeService;
use Illuminate\Database\Seeder;

/**
 * Restores the minimum local starter configuration required by the three
 * Account records actions on an order: payment, renewal, and expense.
 *
 * This intentionally creates only missing records, so it is safe to run again
 * against the local Docker database.
 */
class LocalAccountRecordStarterSeeder extends Seeder
{
    public function run(): void
    {
        $this->ensureFinanceOptions();
        $this->ensureRenewalOptions();
        $this->ensureApprovalForms();
    }

    private function ensureFinanceOptions(): void
    {
        $categories = app(BillCategoryService::class);

        if (! $categories->exists(['name' => 'Demo operating expense', 'types' => 0, 'entid' => 1])) {
            $categories->resourceSave([
                'entid' => 1,
                'path' => [],
                'pid' => 0,
                'name' => 'Demo operating expense',
                'types' => 0,
                'sort' => 0,
                'contact_id' => 0,
            ]);
        }

        $paytypes = app(PaytypeService::class);
        if (! $paytypes->exists(['name' => 'Bank transfer', 'ident' => 'bank_transfer', 'entid' => 1])) {
            $paytypes->resourceSave([
                'entid' => 1,
                'type_id' => 0,
                'name' => 'Bank transfer',
                'ident' => 'bank_transfer',
                'info' => 'Local demo payment method',
                'status' => 1,
                'sort' => 0,
            ]);
        }
    }

    private function ensureRenewalOptions(): void
    {
        $types = app(DictTypeService::class);
        $type = $types->get(['ident' => 'client_renew']);

        if (! $type) {
            $type = $types->resourceSave([
                'name' => 'Renewal type',
                'ident' => 'client_renew',
                'link_type' => 'custom',
                'level' => 1,
                'status' => 1,
                'is_default' => 1,
                'mark' => 'System renewal category',
                'crud_id' => 0,
                'field_id' => 0,
            ]);
        }

        $data = app(DictDataService::class);
        if (! $data->exists(['type_name' => 'client_renew', 'value' => '2'])) {
            $data->resourceSave([
                'name' => 'Renewal',
                'value' => '2',
                'pid' => '',
                'type_id' => (int) $type->id,
                'color' => '#36BC1F',
                'status' => 1,
                'is_default' => 1,
                'sort' => 0,
                'mark' => 'System renewal option',
            ]);
        }
    }

    private function ensureApprovalForms(): void
    {
        foreach ($this->approvalDefinitions() as $definition) {
            if (Approve::query()->where([
                'types' => $definition['types'],
                'examine' => 0,
                'entid' => 1,
            ])->exists()) {
                continue;
            }

            // These are system starter templates, not user-created approval
            // flows. They deliberately bypass request-bound authentication so
            // the seeder remains usable from a Docker CLI rebuild.
            $approval = Approve::query()->create([
                'user_id' => 1,
                'card_id' => 1,
                'entid' => 1,
                'name' => $definition['name'],
                'icon' => 'iconjine',
                'color' => '#00C050',
                'info' => '',
                'types' => $definition['types'],
                'examine' => 0,
                'config' => '',
                'status' => 1,
                'sort' => 1,
            ]);

            ApproveForm::query()->create([
                'user_id' => 1,
                'card_id' => 1,
                'approve_id' => $approval->id,
                'title' => '',
                'info' => '',
                'value' => '',
                'required' => 0,
                'types' => $definition['form']['type'],
                'symbol' => '',
                'content' => $definition['form'],
                'props' => '',
                'options' => '',
                'config' => '',
                'uniqued' => $definition['form']['field'],
                'sort' => 0,
            ]);
        }
    }

    /** @return array<int, array{name: string, types: int, form: array<string, mixed>}> */
    private function approvalDefinitions(): array
    {
        return [
            [
                'name' => 'Add payment',
                'types' => ApproveEnum::CUSTOMER_CONTRACT_PAYMENT,
                'form' => $this->form('contractPayment', 'payment', [
                    $this->field('payment_contract', '订单', 'Order', 'contractList', 'select', true, ['disabled' => false, 'placeholder' => '请选择订单', 'placeholder_en' => 'Select an order']),
                    $this->field('payment_income_category', '财务收入科目', 'Income category', 'incomeCategories', 'cascader', true, ['filterable' => true, 'expandTrigger' => 'hover', 'options' => []]),
                    $this->field('payment_method', '支付方式', 'Payment method', 'payType', 'select', true, ['options' => []]),
                    $this->field('payment_amount', '回款金额', 'Amount received', 'collectionAmount', 'moneyFrom', true, ['type' => 'moneyFrom', 'min' => '0', 'precision' => 2]),
                    $this->field('payment_date', '付款时间', 'Payment date', 'payTime', 'datePicker', true, ['type' => 'datetime', 'placeholder' => '请选择时间', 'placeholder_en' => 'Select a time']),
                    $this->field('payment_voucher', '付款凭证', 'Payment voucher', 'paymentVoucher', 'uploadFrom', false, ['type' => 'uploadFrom']),
                    $this->field('payment_note', '备注', 'Notes', 'remark', 'input', false, ['type' => 'textarea', 'placeholder' => '请输入', 'placeholder_en' => 'Enter notes']),
                ]),
            ],
            [
                'name' => 'Add renewal',
                'types' => ApproveEnum::CUSTOMER_CONTRACT_RENEWAL,
                'form' => $this->form('contractRenewal', 'renewal', [
                    $this->field('renewal_contract', '订单', 'Order', 'contractList', 'select', true, ['disabled' => false, 'readonly' => true, 'placeholder' => '请选择订单', 'placeholder_en' => 'Select an order']),
                    $this->field('renewal_income_category', '财务收入科目', 'Income category', 'incomeCategories', 'cascader', true, ['filterable' => true, 'expandTrigger' => 'hover', 'options' => []]),
                    $this->field('renewal_type', '续费类型', 'Renewal type', 'renewalType', 'select', true, ['options' => []]),
                    $this->field('renewal_amount', '续费金额', 'Renewal amount', 'renewalAmount', 'moneyFrom', true, ['type' => 'moneyFrom', 'min' => '0', 'precision' => 2]),
                    $this->field('renewal_end_date', '续费结束日期', 'Renewal end date', 'renewalEndTime', 'datePicker', true, ['type' => 'datetime', 'placeholder' => '请选择日期', 'placeholder_en' => 'Select a date']),
                    $this->field('renewal_payment_method', '支付方式', 'Payment method', 'payType', 'select', true, ['options' => []]),
                    $this->field('renewal_payment_date', '付款时间', 'Payment date', 'payTime', 'datePicker', true, ['placeholder' => '请选择日期', 'placeholder_en' => 'Select a date']),
                    $this->field('renewal_voucher', '付款凭证', 'Payment voucher', 'paymentVoucher', 'uploadFrom', false, ['type' => 'uploadFrom']),
                    $this->field('renewal_note', '备注', 'Notes', 'remark', 'input', false, ['type' => 'textarea', 'placeholder' => '请输入', 'placeholder_en' => 'Enter notes']),
                ]),
            ],
            [
                'name' => 'Add expense',
                'types' => ApproveEnum::CUSTOMER_CONTRACT_EXPENSES,
                'form' => $this->form('contractExpenditure', 'expense', [
                    $this->field('expense_contract', '订单', 'Order', 'contractList', 'select', true, ['disabled' => false, 'readonly' => true, 'placeholder' => '请选择订单', 'placeholder_en' => 'Select an order']),
                    $this->field('expense_category', '财务支出科目', 'Expense category', 'expenditureCategories', 'cascader', true, ['filterable' => true, 'expandTrigger' => 'hover', 'options' => []]),
                    $this->field('expense_payment_method', '支付方式', 'Payment method', 'payType', 'select', true, ['options' => []]),
                    $this->field('expense_amount', '支出金额', 'Expense amount', 'expenditureAmount', 'moneyFrom', true, ['type' => 'moneyFrom', 'min' => '0', 'precision' => 2]),
                    $this->field('expense_date', '支出时间', 'Expense date', 'payTime', 'datePicker', true, ['type' => 'datetime', 'placeholder' => '请选择时间', 'placeholder_en' => 'Select a time']),
                    $this->field('expense_voucher', '付款凭证', 'Payment voucher', 'paymentVoucher', 'uploadFrom', false, ['type' => 'uploadFrom']),
                    $this->field('expense_note', '备注', 'Notes', 'remark', 'input', false, ['type' => 'textarea', 'placeholder' => '请输入', 'placeholder_en' => 'Enter notes']),
                ]),
            ],
        ];
    }

    /** @param array<int, array<string, mixed>> $children */
    private function form(string $type, string $field, array $children): array
    {
        return [
            'type' => $type,
            'field' => $field,
            'children' => $children,
            '_fc_drag_tag' => $type,
            'hidden' => false,
            'display' => true,
        ];
    }

    /** @param array<string, mixed> $props */
    private function field(string $field, string $title, string $titleEn, string $symbol, string $type, bool $required, array $props): array
    {
        return [
            'checkType' => 0,
            'display' => true,
            'effect' => ['fetch' => '', 'required' => $required],
            'field' => $field,
            'hidden' => false,
            'info' => '',
            'input' => false,
            'title' => $title,
            'title_en' => $titleEn,
            'symbol' => $symbol,
            'type' => $type,
            'props' => $props,
            '_fc_drag_tag' => $type,
        ];
    }
}
