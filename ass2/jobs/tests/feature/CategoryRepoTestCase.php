<?php

declare(strict_types=1);

require_once __DIR__ . '/TestBaseCase.php';
require_once __DIR__ . '/../../includes/jobcontroller.php';

final class CategoryRepoTestCase extends TestBaseCase
{
    public function testJobsCanBeFilteredByCategory(): void
    {
        $pdo = $this->createInMemoryPdo();
        $this->createJobSchema($pdo);

        $jobController = new JobController($pdo);
        $jobController->createJob('Frontend Developer', 'Create UI', 1, '50000', 1, 'Pokhara');
        $jobController->createJob('Data Analyst', 'Analyze business data', 1, '45000', 1, 'Lalitpur');

        $jobs = $jobController->getJobsByCategory(1);

        $this->assertCount(2, $jobs);
        $this->assertSame(['Frontend Developer', 'Data Analyst'], array_column($jobs, 'title'));
    }
}
