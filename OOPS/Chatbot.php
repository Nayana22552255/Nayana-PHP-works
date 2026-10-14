<?php

class Chatbot
{
    private $botname="PHP Bot";

    public function getresponse($message)
    {
        $message=strtolower(trim($message));

        switch($message)
        {
                case "hello":
                    return "Helo! How can i help you?";
                    break;

                case "hi":
                    return "Hi! How can i help you?";
                    break;


                case "how are you":
                    return "I am fine! Thanks for asking.";
                    break;

                
                case "what is your name":
                    return "My name is ".$this->botname;
                    break;


                case "bye":
                        return "Goodbye! Have a nice day";
                        break;

                default:
                        return "sorry, Idon't Undetstand the message$message";
        }
    }
}

$bot=new Chatbot();

echo "Welcome to PHP chatbot\n";
echo "Tye 'bye' to exit.\n\n";

while(true)
    {
        $message=readline("You: ");

        $response=$bot->getresponse($message);

        echo "Bot:".$response. "\n";

        if(strtolower(trim($message)) === "bye")
            {
                break;
            }
    }

?>