<?php
/**
 * Copyright 2016 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */


namespace Google\Cloud\Samples\Translate;

use Symfony\Component\Console\Tester\CommandTester;

/**
 * Unit Tests for transcribe commands.
 */
class CommandTest extends \PHPUnit_Framework_TestCase
{
    private $application;

    public function setUp()
    {
        $this->application = require __DIR__ . '/../translate.php';
    }

    public function testTranslate()
    {
        $commandTester = new CommandTester($this->application->get('translate'));
        $commandTester->execute(
            [
                'text' => 'Hello.',
                '-t' => 'ja',
            ],
            ['interactive' => false]
        );
        $this->assertEquals(0, $commandTester->getStatusCode());
        $display = $this->getActualOutput();
        $this->assertContains('Source language: en', $display);
        $this->assertContains('Translation:', $display);
    }

    public function testTranslateBadLanguage()
    {
        $commandTester = new CommandTester($this->application->get('translate'));
        $this->setExpectedException('Google\Cloud\Core\Exception\BadRequestException');
        $commandTester->execute(
            [
                'text' => 'Hello.',
                '-t' => 'jp',
            ],
            ['interactive' => false]
        );
    }

    public function testTranslateWithModel()
    {
        $commandTester = new CommandTester($this->application->get('translate'));
        $commandTester->execute(
            [
                'text' => 'Hello.',
                '-t' => 'ja',
                '--model' => 'nmt',
            ],
            ['interactive' => false]
        );
        $this->assertEquals(0, $commandTester->getStatusCode());
        $display = $this->getActualOutput();
        $this->assertContains('Source language: en', $display);
        $this->assertContains('Translation:', $display);
        $this->assertContains('Model: nmt', $display);
    }

    public function testDetectLanguage()
    {
        $commandTester = new CommandTester($this->application->get('detect-language'));
        $commandTester->execute(
            [
                'text' => 'Hello.',
            ],
            ['interactive' => false]
        );
        $this->assertEquals(0, $commandTester->getStatusCode());
        $display = $this->getActualOutput();
        $this->assertContains('Language code: en', $display);
        $this->assertContains('Confidence:', $display);
    }

    public function testListCodes()
    {
        $commandTester = new CommandTester($this->application->get('list-codes'));
        $commandTester->execute([], ['interactive' => false]);
        $this->assertEquals(0, $commandTester->getStatusCode());
        $display = $this->getActualOutput();
        $this->assertContains("\nen\n", $display);
        $this->assertContains("\nja\n", $display);
    }

    public function testListLanguagesInEnglish()
    {
        $commandTester = new CommandTester($this->application->get('list-langs'));
        $commandTester->execute(
            ['-t' => 'en'],
            ['interactive' => false]
        );
        $this->assertEquals(0, $commandTester->getStatusCode());
        $display = $this->getActualOutput();
        $this->assertContains('ja: Japanese', $display);
    }

    public function testListLanguagesInJapanese()
    {
        $commandTester = new CommandTester($this->application->get('list-langs'));
        $commandTester->execute(
            ['-t' => 'ja'],
            ['interactive' => false]
        );
        $this->assertEquals(0, $commandTester->getStatusCode());
        $display = $this->getActualOutput();
        $this->assertContains('en: 英語', $display);
    }
}
