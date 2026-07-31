<?php

declare(strict_types=1);


namespace App\Http\Contract\Company;

interface EmployeeTrainInterface
{
    public const COMPANY_PROFILE = 'company_profile';

    public const STRATEGIC_PLAN = 'strategic_plan';

    public const ORGANIZATION_CHART = 'organization_chart';

    public function getInfo(): mixed;

    public function updateTrain(string $content): mixed;
}
