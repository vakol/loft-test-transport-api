<?php

/**
 * Destination with arrival and departure notes
 * 
 * PHP version 7
 */

namespace App\Model;

/**
 * A class that holds information about a destination
 * @author vako
 *
 */
class Destination
{
    /**
     * Name of the destination
     * @var string
     */
    public $name;
    
    /**
     * Reference to the delivery note that informs about arrival to this delivery point
     * @var model/ApiDeliveryNotes
     */
    public $arrivalNote;
    
    /**
     * Reference to the delivery note that informs about departure from this delivery point
     * @var model/ApiDeliveryNotes
     */
    public $departureNote;
    
    /**
     * Constructor of this destination object.
     * @param string $destinationName
     */
    public function __construct($destinationName) {
        
        $this->name = $destinationName;
    }
}