<?php

declare(strict_types=1);

require_once __DIR__ . '/TestBaseCase.php';
require_once __DIR__ . '/../../includes/jobcontroller.php';

final class ApplicationRepoTestCase extends TestBaseCase
{
    public function testJobRepositoryStyleWorkflowCreatesAndReadsJobs(): void
    {
        $pdo = $this->createInMemoryPdo();
        $this->createJobSchema($pdo);

        $jobController = new JobController($pdo);
        $jobId = $jobController->createJob('Backend Developer', 'Build APIs', 1, '60000', 1, 'Kathmandu');

        $this->assertGreaterThan(0, (int) $jobId);

        $job = $jobController->getJobById((int) $jobId);
        $this->assertSame('Backend Developer', $job['title']);
        $this->assertSame('Build APIs', $job['description']);
        $this->assertSame('ABC Tech', $job['companyName']);
    }
}
