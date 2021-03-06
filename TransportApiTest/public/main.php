<?php

/**
 * MAIN ENTRY POINT. Run this program with "php.exe -f public/main.php"
 *
 * PHP version 7
 */

require_once __DIR__ . '/../vendor/autoload.php';


/**
 * INPUT: An unordered list of delivery notes which are in an input format.
 * Each delivery note starts with a line with "-deliveryNote" at the beginning.
 * Delivery note fields are in "fieldName: filedValue" format at each line. Where the
 * fieldName MUST correspond to model/DeliveryNote fields. Otherwise they will not be parsed.
 * (New public fields added into the model/DeliveryNote are parsed too)
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


/**
 * Folowing code parses input delivery notes written in input format, gets sorted result in the same format.
 * Finally prints the result on the output console. When an exception occurs, it displays appropriate error message.
 */
try {
    $parsedNotes = App\Api\DeliveryNotes::parse($deliveryNotesText);
    $sortedNotesText = $parsedNotes->getSortedText();
    
    echo "INPUT:".PHP_EOL;
    echo $deliveryNotesText.PHP_EOL;
    echo "OUTPUT:".PHP_EOL;
    echo $sortedNotesText;
}
catch (\Exception $e) {
    die($e->getMessage());
}
