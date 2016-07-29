<?php

//register middleware here

return array(
    'verifyEchoStr'   => app\middleware\VerifyEchoStr::class,
    'messageHandler'  => app\middleware\MessageHandler::class,
);
