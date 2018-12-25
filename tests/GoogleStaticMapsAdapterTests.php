<?php

namespace Netflie\GoogleStaticMaps\Test;

use Netflie\StaticMapSystem\Entity\Format as MapFormat;
use Netflie\GoogleStaticMaps\GoogleStaticMapsAdapter;
use Netflie\StaticMapSystem\Entity\MapType;

use SparksCoding\StaticMaps\Components\Map;
use SparksCoding\StaticMaps\Components\Marker;
use SparksCoding\StaticMaps\StaticMap as StaticMapClient;

use Prophecy\Prophecy\ObjectProphecy;
use PHPUnit\Framework\TestCase;

class GoogleStaticMapsAdapterTests extends TestCase
{
    /**
     * @var ObjectProphecy
     */
    protected $client;

    /**
     * @var ObjectProphecy
     */
    protected $map;

    /**
     * @var GoogleStaticMapsAdapter
     */
    protected $googleStaticMapsAdapter;

    /**
     * @before
     */
    public function setupClient()
    {
        $this->client = $this->prophesize(StaticMapClient::class);
        $this->map = $this->prophesize(Map::class);
        $this->client->map = $this->map->reveal();
        
        $this->googleStaticMapsAdapter = new GoogleStaticMapsAdapter($this->client->reveal());

    }

    public function testGetMappedFormats()
    {
        $expected_mapped_formats = [
            MapFormat::PNG => 'PNG',
            MapFormat::JPEG => 'JPEG',
        ];

        $this->assertEquals($expected_mapped_formats, $this->googleStaticMapsAdapter->getMappedFormats());
    }

    public function testGetMappedTypes()
    {
        $expected_mapped_types = [
            MapType::ROADMAP_TYPE => 'roadmap',
            MapType::SATELLITE_TYPE => 'satellite',
            MapType::HYBRID_TYPE => 'hybrid',
            MapType::TERRAIN_TYPE => 'terrain',
        ];

        $this->assertEquals($expected_mapped_types, $this->googleStaticMapsAdapter->getMappedTypes());
    }

    public function testSetCenter()
    {
        $center = 'Cala Millor, Spain';
        $this->map->center($center)->willReturn($this->map);

        $this->assertTrue($this->googleStaticMapsAdapter->setCenter($center));
    }

    public function testSize()
    {
        $width = 200;
        $height = 500;
        $this->map->size($width, $height)->willReturn($this->map);
        $this->map->size = "{$width}x{$height}";

        $this->assertTrue($this->googleStaticMapsAdapter->setSize($width, $height));
        $this->assertEquals($width, $this->googleStaticMapsAdapter->getWidth());
        $this->assertEquals($height, $this->googleStaticMapsAdapter->getHeight());
    }

    public function testSetZoom()
    {
        $zoom = 10;
        $this->map->zoom($zoom)->willReturn($this->map);

        $this->assertTrue($this->googleStaticMapsAdapter->setZoom($zoom));
    }

    public function testSetMapType()
    {
        $validMapType = MapType::TERRAIN_TYPE;
        $mappedValidMapType = $this->googleStaticMapsAdapter->getMappedTypeValue($validMapType);
        $invalidMapType = $mappedInvalidMapType = 'fake_map_type';

        $this->map->type($mappedValidMapType)->willReturn($this->map);
        $this->assertTrue($this->googleStaticMapsAdapter->setMapType($validMapType));

        $this->map->type($mappedInvalidMapType)->willReturn($this->map);
        $this->assertFalse($this->googleStaticMapsAdapter->setMapType($invalidMapType));
    }

    public function testSetFormat()
    {
        $validFormat = MapFormat::PNG;
        $mappedFormat = $this->googleStaticMapsAdapter->getMappedFormatValue(MapFormat::PNG);
        $invalidFormat = $mappedInvalidFormat = 'invalid_format';

        $this->map->format($mappedFormat)->willReturn($this->map);
        $this->assertTrue($this->googleStaticMapsAdapter->setFormat($validFormat));

        $this->map->format($mappedInvalidFormat)->willReturn($this->map);
        $this->assertFalse($this->googleStaticMapsAdapter->setFormat($invalidFormat));
    }

    public function testAddMarker()
    {
        $marker = 'New York, NY';

        $this->client->addMarkers(Marker::location($marker))->willReturn($this->client);

        $this->assertTrue($this->googleStaticMapsAdapter->addMarker($marker));
    }

    public function testGetUri()
    {
        $uri = "http://www.netflie.es/";

        $this->client->uri()->willReturn($uri);

        $this->assertEquals($uri, $this->googleStaticMapsAdapter->getUri());
    }
}