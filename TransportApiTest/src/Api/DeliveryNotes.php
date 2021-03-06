<?php

/**
 * Delivery notes API that parses formatted input text (the delivery notes)
 * and provides sorted result in the same compatible format.
 *
 * PHP version 7
 */

namespace App\Api;

use App\Model\DeliveryNote;
use App\Model\Destination;

/**
 * A class that holds delivery note information
 * @author vako
 *
 */
class DeliveryNotes
{
    /**
     * Associative array of destinations (destinationName => model/Destination object). This property is set with the parse(...) method.
     * @var array of model/Destination
     */
    public $destinations = array();
    
    /**
     * The one and only source destination. This property is set with the parse(...) method.
     * @var model/Destination
     */
    public $sourceDestination;
    
    /**
     * Overall number of parsed delivery notes.
     * @var integer
     */ 
    public $parsedNotesCount = 0;
    
    /**
     * Get destination if already exists. If it doesn't, create new destination object
     * and add it into the associative array.
     * @param string $destinationtName
     * @return model/Destination
     */
    private function getDestination($destinationName) {
        
        // Try to get existing destination from the associative array. If it does't exist, create and save new one.
        $destination = null;
        if (array_key_exists($destinationName, $this->destinations)) {
            
            // Get existing destination
            $destination = $this->destinations[$destinationName];
        }
        else {
            // Create and save a new destination
            $destination = new Destination($destinationName);
            $this->destinations[$destinationName] = $destination;
        }
        return $destination;
    }
    
    /**
     * Check the input note. The function throws an exception when the input note
     * doesn't exist.
     * @param model/DeliveryNote $note
     */
    private function checkNoteEmpty($note, $exceptionMessage) {
        
        if (!is_null($note))
            throw new \Exception($exceptionMessage);
    }
    
    /**
     * Put destinations found in a note into the associative array (destinationName -> destinationObject) and
     * form a simple path from destination to destination (here the path can still be cyclic or not continuous).
     * @param model/ApiDeliveryNotes $note
     */
    private function pushDestinationsPoints($note) {
        
        // Try to set arrival point (destination). Throw an exception if the destination already references an arrival note.
        $arrivalToDestination = $this->getDestination($note->to);
        $this->checkNoteEmpty($arrivalToDestination->arrivalNote, "The path specified is not direct. More ways lead to \"{$note->to}\"!");
        $arrivalToDestination->arrivalNote = $note;
        
        // Try to set departure point (destination). Throw an exception if the destination already references a departure note.
        $departureFromDestination =  $this->getDestination($note->from);
        $this->checkNoteEmpty($departureFromDestination->departureNote, "The path specified is not direct. More ways lead from \"{$note->from}\"!");
        $departureFromDestination->departureNote = $note;
    }
    
    /**
     * Parse single note. Tde text is in input format
     * @param string $noteText
     * @return model/DeliveryNote
     */
    private static function parseNote($noteText) {
        
        // Trim and check the input
        $noteText = trim($noteText);
        if ($noteText === "")
            return false;
            
            // Parse delivery note into an object of type model/DeliveryNote
            $note = new DeliveryNote();
            $note->parseText($noteText);
            return $note;
    }
    
    /**
     * Find all source nodes in the graph od destinations paths.
     * @return array
     */
    private function findAllSourceDestinations() {
        
        $sourceDestinations = array();
        
        // Traverse the list of all destinations, check theirs arrival notes and find
        // the ones that do not have arrival note set.
        foreach ($this->destinations as $destination) {
            if (is_null($destination->arrivalNote))
                $sourceDestinations[] = $destination;
        }
        return $sourceDestinations;
    }
    
    /**
     * Parses all delivery notes written in the input format
     * @param string $deliveryNotesText
     * @return array
     */
    public static function parse($deliveryNotesText) {
        
        // Create new API object for notes
        $apiNotes = new DeliveryNotes();
        
        // Parse input text with the delivery notes written in the input format
        $preList = explode('-deliveryNote', $deliveryNotesText);
        
        // Init the notes count
        $apiNotes->parsedNotesCount = 0;
        
        // Push arrival and departure destination points found in notes into an associative array
        foreach ($preList as $noteText) {
            $note = DeliveryNotes::parseNote($noteText);
            if ($note) {
                $apiNotes->pushDestinationsPoints($note);
                // Increment note count
                $apiNotes->parsedNotesCount++;
            }
        }
        
        // Find all destinations which are source nodes in the graph of transport paths
        $sourceDestinations = $apiNotes->findAllSourceDestinations();
        
        // Check and remeber the source node. It MUST exist one and only source node in the destinations path (graph)
        $sourceCount = count($sourceDestinations);
        if ($sourceCount == 1)
            $apiNotes->sourceDestination = $sourceDestinations[0];
        else
            throw new \Exception("Cannot find single destination where the transport starts! The number of starting points is $sourceCount. Some of delivery notes may be missed!");
        
        return $apiNotes;
    }
    
    /**
     * Get sorted notes in a list compatible with the input text format.
     * @param api/ApiDeliveryNotes $parsedNotes
     * @return string
     */
    public function getSortedText() {
        
        // Initialize
        $deliveryNotesText = '';
        $traversedDestionationsCount = 0;
        
        // Traverse destinations from source to sink destination and create continuous trasport path.
        $currentDestination = $this->sourceDestination;
        
        while (!is_null($currentDestination)) {
            
            // Append text of the current note to resulting text. The text is in the format compatible with input format.
            $note = $currentDestination->departureNote;
            if (is_null($note))
                break;
            
            // Append note text representation
            $deliveryNotesText .= $note->getText();
            
            // Increment the traversed destination counter
            $traversedDestionationsCount++;
            
            // Check if maximum steps has been reached
            if ($traversedDestionationsCount > $this->parsedNotesCount) {
                throw new \Exception("The input deliver notes form a cyclic path!");
            }
            
            // Move to the next destination
            $currentDestination = $this->destinations[$note->to];
        }
        
        // Check if all destinations have been visited by the previous algorithm
        if ($traversedDestionationsCount != $this->parsedNotesCount)
            throw new \Exception("Only $traversedDestionationsCount from {$this->parsedNotesCount} delivery notes lead to destination. The others has been rejected!");
        
        return $deliveryNotesText;
    }
}