<?php

/**
 * Class Tx_SchedulerTimeline_Tests_Functional_Domain_Repository_LogRepositoryTest
 */
class Tx_SchedulerTimeline_Tests_Functional_Domain_Repository_LogRepositoryTest extends \TYPO3\CMS\Core\Tests\FunctionalTestCase {

	/**
	 * @var \Tx_SchedulerTimeline_Domain_Repository_LogRepository
	 */
	protected $logRepository;

	/**
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 */
	protected $objectManager;

	/**
	 * SetUp
	 */
	public function setUp() {
		$this->testExtensionsToLoad = array(
			'typo3conf/ext/scheduler_timeline',
			'typo3/sysext/scheduler'
		);
		parent::setUp();
		$this->importDataSet(__DIR__ . '/Fixtures/tx_schedulertimeline_domain_model_log.xml');
		$this->importDataSet(__DIR__ . '/Fixtures/tx_scheduler_task.xml');
		$this->objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
		$this->logRepository = $this->objectManager->get('Tx_SchedulerTimeline_Domain_Repository_LogRepository');
	}

	/**
	 * Tears down the fixture
	 *
	 * @return void
	 */
	public function tearDown() {
		parent::tearDown();
	}

	/**
	 * @test
	 */
	public function findByTimeReturnsQueryResultInterface() {

		// Order by starttime
		$expectedArray = array(5,1,2);

		$startTime = 1445191476; // 18.10.15 18:04
		$endTime = 1445191876; // 18.10.15 18:11
		$logs = $this->logRepository->findByTime($startTime, $endTime);

		/** @var \Tx_SchedulerTimeline_Domain_Model_Log $log */
		foreach ($logs as $log) {
			$actualArray[] = $log->getUid();
		}

		$this->assertSame(
			$expectedArray,
			$actualArray
		);
	}

	/**
	 * @test
	 */
	public function findGroupedByTaskReturnsMultidimensionArray() {

		$actualArray = array();
		$expectedArray = array(
			131 => array(
				'task' => 131,
				'logs' => array(1)
			),
			132 => array(
				'task' => 132,
				'logs' => array(2)
			),
			133 => array(
				'task' => 133,
				'logs' => array(3)
			),
			134 => array(
				'task' => 134,
				'logs' => array(4,5)
			),
		);

		$logsGroupedByTask = $this->logRepository->findGroupedByTask();

		foreach ($logsGroupedByTask as $taskUid => $taskObject) {
			$actualArray[$taskUid]['task'] = $taskUid;

			/** @var Tx_SchedulerTimeline_Domain_Model_Task $task */
			foreach ($taskObject as $task) {
				/** @var Tx_SchedulerTimeline_Domain_Model_Log @log */
				foreach ($task as $log) {
					$actualArray[$taskUid]['logs'][] = $log->getUid();
				}
			}
		}

		$this->assertSame(
			$expectedArray,
			$actualArray
		);
	}
}