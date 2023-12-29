<?php

namespace App\Model;

class FlashData
{
   
   public function __construct(
        private string $type,
        private string $message
   )
   {
    
   } 

    /**
     * Get the value of type
     */
    public function getType()
    {
            return $this->type;
    }

    /**
     * Set the value of type
     */
    public function setType($type): self
    {
            $this->type = $type;

            return $this;
    }

    /**
     * Get the value of message
     */
    public function getMessage()
    {
            return $this->message;
    }

    /**
     * Set the value of message
     */
    public function setMessage($message): self
    {
            $this->message = $message;

            return $this;
    }
}   