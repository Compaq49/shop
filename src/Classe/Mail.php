<?php

namespace App\Classe;

use Mailjet\Client;
use Mailjet\Resources;


class Mail 
{
    private $api_key = '2fc28a198a12e5f38c071d880fc9ac8d';
    private  $api_key_secret = 'f7b50bcbe70eb76e20dfb1560d2f814b';
    
    public function send($to_email, $to_name, $subject, $content) 
    {
        $mj = new Client($this->api_key, $this->api_key_secret, true,['version' => 'v3.1']);
        
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "m_delahy@club-internet.fr",
                        'Name' => "La Boutique Française"
                    ],
                    'To' => [
                        [
                            'Email' => $to_email,
                            'Name' => $to_name
                        ]
                    ],
                    'TemplateID' => 1930831,
                    'TemplateLanguage' => true,
                    'Subject' => $subject,
                    'Variables' => [
                        'content' => $content,
                    ]
                ]
            ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        $response->success();// && dd($response->getData());
    }
}
