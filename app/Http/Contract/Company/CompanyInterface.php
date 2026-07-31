<?php

declare(strict_types=1);


namespace App\Http\Contract\Company;

interface CompanyInterface
{
    public function companyList(array $where, array $field, $sort, array $with): array;

    public function createCompanyForm(): array;

    public function createCompanySave(array $data): array;

    public function updateCompanyForm(int $id): array;

    public function updateCompanySave($id, array $data): array;
}
