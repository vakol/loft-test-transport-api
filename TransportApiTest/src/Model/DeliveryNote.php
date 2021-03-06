<?php

/**
 * Delivery note objects class. You can add other fields. They will be parsed
 * with appropriate names used in the input format (reflection is used).
 * 
 * PHP version 7
 */

namespace App\Model;

/**
 * A class that holds delivery note information and reflection information needed for parsing.
 * @author vako
 *
 */
class DeliveryNote
{
    
    /**
     * Mean of transportation from start to destination.
     * (The name of this field MUST correspond with a name of property used in the input format)
     * @var string
     */
    public $meanOfTransportation;
    
    /**
     * Departure point
     * (The name of this field MUST correspond with a name of property used in the input format)
     * @var string
     */
    public $from;
    
    /**
     * Destination point
     * (The name of this field MUST correspond with a name of property used in the input format)
     * @var string
     */
    public $to;
    
    /**
     * Name of a delivery service provider.
     * (The name of this field MUST correspond with a name of property used in the input format)
     * @var string
     */
    public $deliveryCompany;
    
    /**
     * The object reflection
     * @var \ReflectionObject
     */
    private $reflection;
    
    /**
     * Reflected properties (fields)
     * @var array of \ReflectionProperty
     */
    private $properties;
    
    /**
     * Reflected and compiled fields needed in the REGEX parser
     * @var string
     */
    private $fieldsInRegex;
    
    /**
     * Delivery note constructor
     */
    public function __construct() {
        
        // Prepare prerequisites for parser with reflection
        $this->reflection = new \ReflectionObject($this);
        $this->properties = $this->reflection->getProperties(\ReflectionProperty::IS_PUBLIC);
        
        // Get field names and compile REGEX part fro the parser. Field names are separated with "|"
        $fieldNames = array();
        foreach ($this->properties as $field)
            array_push($fieldNames, $field->getName());
        $this->fieldsInRegex = implode("|", $fieldNames);
    }
    
    /**
     * Parse input text that contains information about a note items in the input format
     * @param string $noteText
     */
    public function parseText($noteText) {
        
        // Parse input text into fields of the current object
        $preItems = explode(PHP_EOL, $noteText);
        foreach ($preItems as $itemText) {
            $this->loadField($itemText);
        }
    }
    
    /**
     * Detect, parse text and load appropriate field
     * @param string $fieldText
     */
    private function loadField($fieldText) {
        
        $matches = array();
        
        // Parse the input with REGEX. Use prerequisites built in constructor
        $regex = "/^\s*({$this->fieldsInRegex})\s*:\s*(.*?)\s*$/";
        if (preg_match($regex, $fieldText, $matches)
            && count($matches) == 3) {
                
            // Set appropriate object field
            $objectField = $this->reflection->getProperty($matches[1]);
            $objectField->setValue($this, $matches[2]);
        }
        else {
            throw new \Exception("Input error! Cannot recognize \"{$fieldText}\".");
        }
    }
    
    /**
     * Returns text of the current note that is compatible with input format.
     * @return string
     */
    public function getText() {
        
        $noteText = "-deliveryNote".PHP_EOL;
        
        // Traverse this object properties and append theirs text representations to
        // the output string.
        foreach ($this->properties as $property)
            $noteText .= "    {$property->getName()}: {$property->getValue($this)}".PHP_EOL;
        
        return $noteText;
    }
}