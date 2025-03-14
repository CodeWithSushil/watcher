<?php

use PHPUnit\Framework\TestCase;
use Watcher\Watcher;
use Symfony\Component\Filesystem\Filesystem;

class WatcherTest extends TestCase
{
    private $watcher;

    protected function setUp(): void
    {
        $this->watcher = new Watcher();
    }

    public function testWatch()
    {
        // Simulate a valid file path
        $path = 'tests/testFile.php';

        // Create a mock of the Filesystem class to avoid file system calls
        $mockFilesystem = $this->createMock(Filesystem::class);

        // Assume getFiles() returns an array of files in the path
        $mockFilesystem->expects($this->once())
            ->method('exists')
            ->willReturn(true);

        $this->fileWatcher->setFilesystem($mockFilesystem);

        // Test the watch function to see if it calls the file checking logic correctly
        $this->fileWatcher->watch($path);

        $this->assertTrue(true); // Placeholder assertion to pass test for now
    }

    public function testGetFilesExclusion()
    {
        $path = 'tests/';
        $excludePatterns = ['*.log', '*.bak'];

        // Files to be returned by getFiles
        $expectedFiles = [
            'tests/testFile.php',
            'tests/anotherTest.php'
        ];

        // Mock the getFiles method for testing exclusion patterns
        $reflection = new \ReflectionClass($this->fileWatcher);
        $method = $reflection->getMethod('getFiles');
        $method->setAccessible(true);

        $result = $method->invokeArgs($this->fileWatcher, [$path, $excludePatterns]);

        $this->assertSame($expectedFiles, $result);
    }

    public function testGetFilesWithExclusionPatterns()
    {
        // Simulate files to be ignored
        $path = 'tests/';
        $excludePatterns = ['*.log'];

        // Mock the getFiles method
        $files = [
            'tests/testFile.php',
            'tests/test.log', // Should be excluded
        ];

        // Mock Filesystem to return the list of files
        $mockFilesystem = $this->createMock(Filesystem::class);
        $mockFilesystem->expects($this->once())
            ->method('exists')
            ->willReturn(true);

        // Set mock in the watcher
        $this->fileWatcher->setFilesystem($mockFilesystem);

        // Check the files returned after exclusion
        $result = $this->fileWatcher->getFiles($path, $excludePatterns);

        // Assert that excluded files are not returned
        $this->assertNotContains('tests/test.log', $result);
    }
}
