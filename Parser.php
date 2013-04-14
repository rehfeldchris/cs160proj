<?php
/**
 * All parsers should be able to decide whether or 
 * not they think they successfully parsed their input.
 * 
 * They should also parse only when told to, not upon constructor initialization.
 * 
 * @author Chris Rehfeld
 */


interface Parser
{
    /**
     * @return boolean
     */
    public function isValid();
    
    /**
     * May throw a RuntimeException
     * @return void
     */
    public function parse();
}