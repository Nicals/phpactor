<?php

namespace Phpactor\Extension\LanguageServerBridge\Converter;

use Phpactor\LanguageServerProtocol\Location as LspLocation;
use Phpactor\LanguageServerProtocol\Range;
use Phpactor\Extension\LanguageServerBridge\Converter\Exception\CouldNotLoadFileContents;
use Phpactor\TextDocument\Exception\TextDocumentNotFound;
use Phpactor\TextDocument\Location;
use Phpactor\TextDocument\Locations;
use Phpactor\TextDocument\TextDocumentLocator;

class LocationConverter
{
    public function __construct(private TextDocumentLocator $locator)
    {
    }

    public function toLspLocations(Locations $locations): array
    {
        $lspLocations = [];
        foreach ($locations as $location) {
            try {
                $lspLocations[] = $this->toLspLocation($location);
            } catch (TextDocumentNotFound) {
                continue;
            } catch (CouldNotLoadFileContents) {
                continue;
            }
        }

        return $lspLocations;
    }

    public function toLspLocation(Location $location): LspLocation
    {
        $textDocument = $this->locator->get($location->uri());
        $position = PositionConverter::byteOffsetToPosition(
            $location->offset(),
            $textDocument->__toString()
        );

        return new LspLocation($location->uri()->__toString(), new Range($position, $position));
    }
}
