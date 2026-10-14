<?php
Class Calculator
{
        public $number1;
        public $number2;

        public function __construct($number1,$number2)
        {
            $this->number1=$number1;
            $this->number2=$number2;
        }

        public function Addition()
        {
            return $this->number1 + $this->number2;
        }

         public function Substraction()
        {
            return $this->number1 - $this->number2;
        }

         public function Multiplication()
        {
            return $this->number1 * $this->number2;
        }

         public function Division()
        {
            return $this->number1 / $this->number2;
        }


}
?>