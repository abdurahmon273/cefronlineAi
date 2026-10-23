<?php

namespace App\Traits;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;


trait SendingSms
{

    /**
     * @throws GuzzleException
     */
    public static function getToken($login, $password)
    {



        $client2 = new Client();
        $options2 = [
            'multipart' => [
                [
                    'name' => 'email',
                    'contents' => $login
                ],
                [
                    'name' => 'password',
                    'contents' => $password
                ]
            ]];
        $request2 = new Request('POST', 'https://notify.eskiz.uz/api/auth/login');
        $res2 = $client2->send($request2, $options2);


        if($res2->getBody()){
            return json_decode( $res2->getBody())->data->token;
        }
        return  false;

    }

    private function sms($mobile_number, $message)
    {

        $nik='4546';

        $login='muzaffar0511@gmail.com';
        $password='p8W0N2y8mOf16FNTGlOKeDk6mKKvVLR0UeAhnmoN';


        $token=self::getToken($login,$password);
        if($token ){
            try {


                $client = new Client();
                $headers = [
                    "Authorization" => "Bearer " . $token];
                $options = [
                    'multipart' => [

                        [
                            'name' => 'mobile_phone',
                            'contents' => $mobile_number
                        ],
                        [
                            'name' => 'message',
                            'contents' => "$message"
                        ],
                        [
                            'name' => 'from',
                            'contents' => $nik
                        ],

                    ]];

                    $request = new Request('POST', 'https://notify.eskiz.uz/api/message/sms/send', $headers);

                    $res = $client->send($request, $options);




            }
            catch (\Exception $exception)
            {

            } catch (GuzzleException $e) {
            }

        }


    }

    public function send_sms($mobile_number, $message )
    {


            if (is_array($mobile_number)) {
                foreach ($mobile_number as $number) {
                    $this->sms($number, $message);
                }
            } else {
                $this->sms($mobile_number, $message);
            }
        }

}
