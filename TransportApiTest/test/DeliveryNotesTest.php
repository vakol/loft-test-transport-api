<?php declare(strict_types=1);

/**
 * TEST class App\Api\DeliveryNotes.
 *
 * PHP version 7
 */

use PHPUnit\Framework\TestCase;

/**
 * Unit test object with an entry point method test()
 * @author user
 *
 */
final class DeliveryNotesTest extends TestCase
{
    
/**
 * CORECT OUTPUT STRING THAT SHOULD MATCH getSortedText() OUTPUT:
 *
 */
private $correctOutputText= <<<EOD
-deliveryNote
    meanOfTransportation: Truck
    from: Fazenda São Francisco Citros, Brazil
    to: São Paulo–Guarulhos International Airport, Brazil
    deliveryCompany: Correios
-deliveryNote
    meanOfTransportation: Flight
    from: São Paulo–Guarulhos International Airport, Brazil
    to: Porto International Airport, Portugal
    deliveryCompany: LATAM
-deliveryNote
    meanOfTransportation: Van
    from: Porto International Airport, Portugal
    to: Adolfo Suárez Madrid–Barajas Airport, Spain
    deliveryCompany: AnyVan
-deliveryNote
    meanOfTransportation: Flight
    from: Adolfo Suárez Madrid–Barajas Airport, Spain
    to: London Heathrow, UK
    deliveryCompany: DHL
-deliveryNote
    meanOfTransportation: Van
    from: London Heathrow, UK
    to: Loft Digital, London, UK
    deliveryCompany: City Sprint
EOD;
    
/**
 * TEST OUT OF ORDER DELIVERY NOTES
 */
public function testOutOfOrderNotes(): void
{
        
/**
 * INPUT: OUT OF ORDER DELIVERY NOTES.
 * 
 */
$deliveryNotesText= <<<EOD
        
-deliveryNote
    meanOfTransportation: Flight
    from: Adolfo Suárez Madrid–Barajas Airport, Spain
    to: London Heathrow, UK
    deliveryCompany: DHL
-deliveryNote
    meanOfTransportation: Truck
    from: Fazenda São Francisco Citros, Brazil
    to: São Paulo–Guarulhos International Airport, Brazil
    deliveryCompany: Correios
-deliveryNote
    meanOfTransportation: Van
    from: Porto International Airport, Portugal
    to: Adolfo Suárez Madrid–Barajas Airport, Spain
    deliveryCompany: AnyVan
-deliveryNote
    meanOfTransportation: Van
    from: London Heathrow, UK
    to: Loft Digital, London, UK
    deliveryCompany: City Sprint
-deliveryNote
    meanOfTransportation: Flight
    from: São Paulo–Guarulhos International Airport, Brazil
    to: Porto International Airport, Portugal
    deliveryCompany: LATAM
    
EOD;

    try {
        $parsedNotes = App\Api\DeliveryNotes::parse($deliveryNotesText);
        $sortedNotesText = $parsedNotes->getSortedText();
        
        // Check output
        $this->assertSame(trim($sortedNotesText), trim($this->correctOutputText));
        
        print($sortedNotesText);
    }
    catch (\Exception $e) {
        $this->assertSame(true, false);
    }
}
}