<?php

namespace Netflie\GoogleStaticMaps;

use SparksCoding\StaticMaps\Components\Map;
use SparksCoding\StaticMaps\Components\Marker;
use SparksCoding\StaticMaps\StaticMap as StaticMapClient;

use Netflie\StaticMapSystem\Adapter\AbstractAdapter;
use Netflie\StaticMapSystem\Entity\Format as MapFormat;
use Netflie\StaticMapSystem\Entity\MapType;
use Netflie\StaticMapSystem\Exception\BadMappedFormatTypeException;
use Netflie\StaticMapSystem\Exception\BadMappedMapTypeException;

class GoogleStaticMapsAdapter extends AbstractAdapter
{
    private $client;

    public function __construct(StaticMapClient $client)
    {
        $this->client = $client;
    }

    public function getMappedFormats()
    {
        return [
            MapFormat::PNG => 'PNG',
            MapFormat::JPEG => 'JPEG',
        ];
    }

    public function getMappedTypes()
    {
        return [
            MapType::ROADMAP_TYPE => 'roadmap',
            MapType::SATELLITE_TYPE => 'satellite',
            MapType::HYBRID_TYPE => 'hybrid',
            MapType::TERRAIN_TYPE => 'terrain',
        ];
    }

    public function setCenter(string $center): bool
    {
        return (bool) $this->client->map->center($center);
    }

    public function setSize(int $width, int $height): bool
    {
        return (bool) $this->client->map->size($width, $height);
    }

    public function getWidth(): int
    {
        return (int) $this->getSizeFromString($this->client->map->size, 0);
    }

    public function getHeight(): int
    {
        return (int) $this->getSizeFromString($this->client->map->size, 1);
    }

    public function setZoom(int $zoom): bool
    {
        return (bool) $this->client->map->zoom($zoom);
    }

    public function setMapType(string $mapType): bool
    {
        try {
            $mappedType = $this->getMappedTypeValue($mapType);
        } catch (BadMappedMapTypeException $e) {
            return false;
        }

        return (bool) $this->client->map->type($mappedType);
    }

    public function setFormat(string $format): bool
    { 
        try {
            $mappedFormat = $this->getMappedFormatValue($format);
        } catch (BadMappedFormatTypeException $e) {
            return false;
        }

        return (bool) $this->client->map->format($mappedFormat);
    }

    public function addMarker(string $marker): bool
    {
        $marker = Marker::location($marker);

        return (bool) $this->client->addMarkers($marker);
    }

    public function getUri(): string
    {
        return $this->client->uri();
    }
    
    protected function getSizeFromString($stringSize, $sizePos)
    {
        $size = explode('x', $stringSize, 2);

        return $size[$sizePos];
    }
}