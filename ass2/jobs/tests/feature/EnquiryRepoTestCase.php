<?php

declare(strict_types=1);

require_once __DIR__ . '/TestBaseCase.php';
require_once __DIR__ . '/../../includes/jobcontroller.php';

final class EnquiryRepoTestCase extends TestBaseCase
{
    public function testSearchResultsReturnMatchingJobsForAnEnquiryQuery(): void
    {
        $pdo = $this->createInMemoryPdo();
        $this->createJobSchema($pdo);

        $jobController = new JobController($pdo);
        $jobController->createJob('Senior PHP Developer', 'Build secure backend APIs', 1, '70000', 1, 'Kathmandu');
        $jobController->createJob('UI Designer', 'Design polished user interfaces', 1, '55000', 1, 'Bhaktapur');

        $results = $jobController->searchJobs('API', 10);

        $this->assertCount(1, $results);
        $this->assertSame('Senior PHP Developer', $results[0]['title']);
        $this->assertStringContainsString('backend APIs', $results[0]['description']);
    }
}
