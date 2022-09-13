<?php declare(strict_types=1);

/*
 * This file is part of Packagist.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *     Nils Adermann <naderman@naderman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FeedControllerTest extends WebTestCase
{
    /**
     * @dataProvider provideForFeed
     */
    public function testFeedAction(string $feed, string $format, ?string $vendor = null)
    {
        $client = self::createClient();

        $url = $client->getContainer()->get('router')->generate($feed, ['_format' => $format, 'vendor' => $vendor]);

        $crawler = $client->request('GET', $url);

        $this->assertEquals(200, $client->getResponse()->getStatusCode(), $client->getResponse()->getContent());
        $this->assertStringContainsString($format, $client->getResponse()->getContent());

        if ($vendor !== null) {
            $this->assertStringContainsString($vendor, $client->getResponse()->getContent());
        }
    }

    public function provideForFeed()
    {
        return [
            ['feed_packages', 'rss'],
            ['feed_packages', 'atom'],
            ['feed_releases', 'rss'],
            ['feed_releases', 'atom'],
            ['feed_vendor', 'rss', 'symfony'],
            ['feed_vendor', 'atom', 'symfony'],
        ];
    }
}
